<?php
require __DIR__ . '/../includes/bootstrap.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('customer/reservations.php'));
    exit;
}
check_csrf();

$id = (int)($_POST['id'] ?? 0);
$in = $_POST['check_in'] ?? '';
$out = $_POST['check_out'] ?? '';
$adults = max(1, (int)($_POST['adults'] ?? 1));
$children = max(0, (int)($_POST['children'] ?? 0));
$guests = $adults + $children;
$special = trim($_POST['special_requests'] ?? '');

if ($in < date('Y-m-d') || $out <= $in) {
    flash('error', 'Please choose valid stay dates.');
    header('Location: ' . url('customer/reservation_edit.php?id=' . $id));
    exit;
}

$pdo = db();
try {
    $pdo->beginTransaction();

    // Lock the reservation
    $s = $pdo->prepare('SELECT r.*, rm.max_guests, rm.price_per_night FROM reservations r JOIN rooms rm ON rm.id = r.room_id WHERE r.id = ? AND r.user_id = ? FOR UPDATE');
    $s->execute([$id, user()['id']]);
    $reservation = $s->fetch();

    if (!$reservation) {
        throw new RuntimeException('Reservation not found.');
    }

    if (!in_array($reservation['status'], ['PENDING', 'CONFIRMED']) || strtotime($reservation['check_in']) <= time()) {
        throw new RuntimeException('This reservation cannot be modified.');
    }

    if ($guests > $reservation['max_guests']) {
        throw new RuntimeException('Exceeded maximum room capacity.');
    }

    // Check for conflicts, excluding THIS reservation
    $conflictSql = "status IN ('PENDING','CONFIRMED','CHECKED_IN') AND check_in < ? AND check_out > ? AND id != ?";
    $s = $pdo->prepare('SELECT COUNT(*) FROM reservations WHERE room_id = ? AND ' . $conflictSql . ' FOR UPDATE');
    $s->execute([$reservation['room_id'], $out, $in, $id]);
    if ($s->fetchColumn()) {
        throw new RuntimeException('The room is not available for the selected dates.');
    }

    // Recalculate
    $nights = (int)((strtotime($out) - strtotime($in)) / 86400);
    $pricePerNight = (float)$reservation['price_per_night'];
    
    // Check if there are add-ons associated to update total
    $s = $pdo->prepare('SELECT SUM(qty * unit_price) FROM reservation_add_ons WHERE reservation_id = ?');
    $s->execute([$id]);
    $addOnsTotal = (float)$s->fetchColumn();

    $subtotal = ($nights * $pricePerNight) + $addOnsTotal;
    $tax = round($subtotal * 0.12, 2);
    $total = $subtotal + $tax;

    // Update reservation
    $pdo->prepare('
        UPDATE reservations 
        SET check_in = ?, check_out = ?, guests = ?, adults = ?, children = ?, nights = ?, subtotal = ?, tax = ?, total_amount = ?, special_requests = ?
        WHERE id = ?
    ')->execute([$in, $out, $guests, $adults, $children, $nights, $subtotal, $tax, $total, $special, $id]);

    // Update payment record amount (assuming single payment record for simplicity here)
    $pdo->prepare('UPDATE payments SET amount = ? WHERE reservation_id = ? AND payment_status = \'PENDING\'')->execute([$total, $id]);

    log_action(user()['id'], 'MODIFY_RESERVATION', 'reservation', $id, 'Modified dates/guests for ' . $reservation['reservation_number']);

    $pdo->commit();
    flash('success', 'Reservation updated successfully.');
    header('Location: ' . url('customer/reservations.php'));
    
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    flash('error', $e instanceof RuntimeException ? $e->getMessage() : 'Unable to update reservation.');
    header('Location: ' . url('customer/reservation_edit.php?id=' . $id));
}
