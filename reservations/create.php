<?php
require __DIR__ . '/../includes/bootstrap.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('rooms/index.php'));
    exit;
}
check_csrf();

$room = (int)($_POST['room_id'] ?? 0);
$in = $_POST['check_in'] ?? '';
$out = $_POST['check_out'] ?? '';
$adults = max(1, (int)($_POST['adults'] ?? 1));
$children = max(0, (int)($_POST['children'] ?? 0));
$guests = $adults + $children;
$selectedAddons = array_filter(array_map('intval', (array)($_POST['addons'] ?? [])));

if ($in < date('Y-m-d') || $out <= $in) {
    flash('error', 'Choose valid stay dates.');
    header('Location: ' . url('rooms/details.php?id=' . $room));
    exit;
}

$pdo = db();
try {
    $pdo->beginTransaction();

    $s = $pdo->prepare("SELECT * FROM rooms WHERE id=? AND status='AVAILABLE' FOR UPDATE");
    $s->execute([$room]);
    $r = $s->fetch();

    if (!$r || $guests > $r['max_guests']) {
        throw new RuntimeException('This room is not available for that guest count.');
    }

    $s = $pdo->prepare('SELECT COUNT(*) FROM reservations WHERE room_id=? AND ' . reservation_conflict_sql() . ' FOR UPDATE');
    $s->execute([$room, $out, $in]);
    if ($s->fetchColumn()) {
        throw new RuntimeException('This room was just reserved for overlapping dates. Please choose another room.');
    }

    $nights = (int)((strtotime($out) - strtotime($in)) / 86400);
    $subtotal = $nights * (float)$r['price_per_night'];

    // Add-ons calculation
    $addonsTotal = 0;
    $addonRecords = [];
    if (!empty($selectedAddons)) {
        $addonIds = implode(',', array_fill(0, count($selectedAddons), '?'));
        $addonStmt = $pdo->prepare("SELECT id, price FROM add_ons WHERE active = 1 AND id IN ($addonIds)");
        $addonStmt->execute($selectedAddons);
        $addonRecords = $addonStmt->fetchAll();
        foreach ($addonRecords as $addonRecord) {
            $addonsTotal += (float)$addonRecord['price'];
        }
    }

    $subtotal += $addonsTotal;
    $tax = round($subtotal * 0.12, 2);
    $total = $subtotal + $tax;

    $number = 'HTL-' . date('Y') . '-' . str_pad((string)((int)$pdo->query('SELECT COALESCE(MAX(id),0)+1 FROM reservations')->fetchColumn()), 6, '0', STR_PAD_LEFT);

    $pdo->prepare("
        INSERT INTO reservations (
            reservation_number, user_id, room_id, check_in, check_out, 
            guests, adults, children, nights, price_per_night, subtotal, 
            tax, total_amount, special_requests
        ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)
    ")->execute([
        $number, user()['id'], $room, $in, $out, $guests, $adults, $children, 
        $nights, $r['price_per_night'], $subtotal, $tax, $total, trim($_POST['special_requests'] ?? '')
    ]);
    
    $id = (int)$pdo->lastInsertId();

    // Insert addons to pivot table
    if (!empty($addonRecords)) {
        $pivotStmt = $pdo->prepare("INSERT INTO reservation_add_ons (reservation_id, add_on_id, qty, unit_price) VALUES (?, ?, 1, ?)");
        foreach ($addonRecords as $addonRecord) {
            $pivotStmt->execute([$id, $addonRecord['id'], $addonRecord['price']]);
        }
    }

    $pdo->prepare("INSERT INTO payments (reservation_id, payment_reference, amount, payment_method, payment_status) VALUES (?, ?, ?, 'CASH', 'PENDING')")->execute([$id, 'PAY-' . $number, $total]);
    
    log_action(user()['id'], 'CREATE_HOTEL_RESERVATION', 'reservation', $id, 'Created ' . $number);
    notify(user()['id'], 'Reservation received', 'Your stay reservation ' . $number . ' is pending confirmation.', 'RESERVATION');
    
    $pdo->commit();
    flash('success', 'Reservation successful! ' . $number);
    header('Location: ' . url('customer/reservations.php'));
} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    flash('error', $e instanceof RuntimeException ? $e->getMessage() : 'Unable to create reservation.');
    header('Location: ' . url('rooms/details.php?id=' . $room));
}
