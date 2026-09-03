<?php
require __DIR__ . '/../includes/auth.php';

$id = (int)($_GET['id'] ?? 0);
$pdo = db();

$s = $pdo->prepare('
    SELECT r.*, rm.title, rm.price_per_night AS room_price, rm.max_guests 
    FROM reservations r
    JOIN rooms rm ON rm.id = r.room_id
    WHERE r.id = ? AND r.user_id = ?
');
$s->execute([$id, user()['id']]);
$reservation = $s->fetch();

if (!$reservation) {
    flash('error', 'Reservation not found.');
    header('Location: ' . url('customer/reservations.php'));
    exit;
}

if (!in_array($reservation['status'], ['PENDING', 'CONFIRMED']) || strtotime($reservation['check_in']) <= time()) {
    flash('error', 'This reservation cannot be modified.');
    header('Location: ' . url('customer/reservations.php'));
    exit;
}

$pageTitle = 'Modify Reservation';
require __DIR__ . '/../includes/header.php';
?>

<div class="section-head" style="margin-bottom: 2rem;">
    <div>
        <span class="kicker">UPDATE BOOKING</span>
        <h1 style="margin-bottom: 0.35rem;">Modify Reservation</h1>
        <p style="color: var(--text-secondary, #5C625D);">Make changes to your upcoming stay.</p>
    </div>
    <a class="btn small btn-outline" href="<?=url('customer/reservations.php')?>">Back to My Stays</a>
</div>

<div class="panel" style="max-width: 600px; margin: 0 auto;">
    <div style="margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border);">
        <h2 style="font-size: 1.25rem; margin-bottom: 0.25rem;"><?=e($reservation['title'])?></h2>
        <p style="color: var(--text-secondary); font-size: 0.9rem;">Reservation #: <strong><?=e($reservation['reservation_number'])?></strong></p>
    </div>

    <form method="post" action="<?=url('reservations/modify.php')?>">
        <input type="hidden" name="csrf" value="<?=csrf()?>">
        <input type="hidden" name="id" value="<?=$reservation['id']?>">

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
            <div>
                <label style="display:block; font-weight: 600; margin-bottom: 0.25rem; font-size: 0.85rem;" for="check_in">Check-in Date <span style="color: #c0392b;">*</span></label>
                <input type="date" id="check_in" name="check_in" required min="<?=date('Y-m-d')?>" value="<?=e($reservation['check_in'])?>" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px;">
            </div>
            <div>
                <label style="display:block; font-weight: 600; margin-bottom: 0.25rem; font-size: 0.85rem;" for="check_out">Check-out Date <span style="color: #c0392b;">*</span></label>
                <input type="date" id="check_out" name="check_out" required min="<?=date('Y-m-d', strtotime('+1 day'))?>" value="<?=e($reservation['check_out'])?>" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px;">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
            <div>
                <label style="display:block; font-weight: 600; margin-bottom: 0.25rem; font-size: 0.85rem;" for="adults">Adults <span style="color: #c0392b;">*</span></label>
                <input type="number" id="adults" name="adults" required min="1" max="<?=$reservation['max_guests']?>" value="<?=$reservation['adults']?>" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px;">
            </div>
            <div>
                <label style="display:block; font-weight: 600; margin-bottom: 0.25rem; font-size: 0.85rem;" for="children">Children</label>
                <input type="number" id="children" name="children" min="0" max="<?=max(0, $reservation['max_guests'] - 1)?>" value="<?=$reservation['children']?>" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px;">
            </div>
            <div style="grid-column: 1 / -1;">
                <small style="color: var(--text-secondary);">Maximum room capacity: <?=$reservation['max_guests']?> guests.</small>
            </div>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display:block; font-weight: 600; margin-bottom: 0.25rem; font-size: 0.85rem;" for="special_requests">Special Requests</label>
            <textarea id="special_requests" name="special_requests" rows="3" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px; resize: vertical;"><?=e($reservation['special_requests'])?></textarea>
        </div>

        <div style="background: rgba(28, 51, 40, 0.03); padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
            <p style="font-size: 0.85rem; color: var(--text-secondary); margin: 0;">
                <strong>Note:</strong> Modifying your stay dates or guest counts may result in a change to your total booking amount. Subject to availability.
            </p>
        </div>

        <div style="display: flex; gap: 1rem;">
            <button type="submit" class="btn btn-gold">Update Reservation</button>
            <a href="<?=url('customer/reservations.php')?>" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
