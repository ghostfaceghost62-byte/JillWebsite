<?php
require __DIR__.'/../../includes/admin_auth.php';
require __DIR__ . '/../../includes/admin_auth.php';

$pdo = db();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $roomId = (int)($_POST['room_id'] ?? 0);
    $status = $_POST['status'] ?? '';
    $allowed = ['AVAILABLE','OCCUPIED','MAINTENANCE','INACTIVE'];
    $allowed = ['AVAILABLE', 'OCCUPIED', 'MAINTENANCE', 'INACTIVE'];

    if ($roomId > 0 && in_array($status, $allowed, true)) {
        $update = $pdo->prepare('UPDATE rooms SET status=? WHERE id=?');
        $update = $pdo->prepare('UPDATE rooms SET status = ? WHERE id = ?');
        $update->execute([$status, $roomId]);
        log_action((int)user()['id'], 'UPDATE_ROOM_STATUS', 'room', $roomId, 'Set room status to '.$status);
        flash('success', 'Room status updated.');
        log_action((int)user()['id'], 'UPDATE_ROOM_STATUS', 'room', $roomId, 'Set room status to ' . $status);
        flash('success', 'Room status updated successfully.');
    } else {
        flash('error', 'Invalid room update.');
        flash('error', 'Invalid room update request.');
    }
    header('Location: index.php');
    exit;
}
$rooms = $pdo->query("SELECT r.*, t.name AS type_name FROM rooms r JOIN room_types t ON t.id=r.room_type_id ORDER BY r.room_number")->fetchAll();
$pageTitle = 'Manage rooms';
require __DIR__.'/../../includes/header.php';

$rooms = $pdo->query("SELECT r.*, t.name AS type_name FROM rooms r JOIN room_types t ON t.id = r.room_type_id ORDER BY r.room_number")->fetchAll();
$pageTitle = 'Manage Rooms & Status';
require __DIR__ . '/../../includes/header.php';
?>
<div class="section-head"><div><p class="eyebrow">HOTEL MANAGEMENT</p><h1>Rooms</h1></div><a class="btn" href="<?=url('rooms/index.php')?>">View guest rooms</a></div>
<div class="panel table-scroll"><table><thead><tr><th>Room</th><th>Type</th><th>Capacity</th><th>Rate</th><th>Status</th><th>Update</th></tr></thead><tbody><?php foreach ($rooms as $room): ?><tr><td><strong><?=e($room['room_number'])?></strong><br><span class="muted"><?=e($room['title'])?></span></td><td><?=e($room['type_name'])?></td><td><?=e((string)$room['max_guests'])?> guests</td><td>₱<?=number_format((float)$room['price_per_night'], 2)?></td><td><span class="badge"><?=e($room['status'])?></span></td><td><form method="post" class="toolbar"><input type="hidden" name="csrf" value="<?=csrf()?>"><input type="hidden" name="room_id" value="<?=$room['id']?>"><select name="status"><?php foreach (['AVAILABLE','OCCUPIED','MAINTENANCE','INACTIVE'] as $status): ?><option value="<?=$status?>" <?=$room['status']===$status?'selected':''?>><?=$status?></option><?php endforeach; ?></select><button class="small">Save</button></form></td></tr><?php endforeach; ?></tbody></table></div>
<?php require __DIR__.'/../../includes/footer.php'; ?>

<div class="section-head" style="margin-bottom: 2rem;">
    <div>
        <span class="kicker">INVENTORY CONTROL</span>
        <h1 style="margin-bottom: 0.35rem;">Manage Rooms & Suites</h1>
        <p style="color: var(--text-secondary, #5C625D);">Update availability status for guest accommodations across all floors.</p>
    </div>
    <a class="btn small btn-gold" href="<?=url('rooms/index.php')?>">Preview Guest View</a>
</div>

<div class="panel table-scroll">
    <table>
        <thead>
            <tr>
                <th>Room #</th>
                <th>Title / Description</th>
                <th>Category</th>
                <th>Capacity</th>
                <th>Nightly Rate</th>
                <th>Current Status</th>
                <th>Update Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rooms as $room): ?>
                <tr>
                    <td>
                        <strong style="font-size: 1.1rem; font-family: 'Playfair Display', Georgia, serif;">
                            Room <?=e($room['room_number'])?>
                        </strong>
                        <br>
                        <small style="color: var(--text-secondary, #7A807B);">Floor <?=e($room['floor'] ?? '1')?></small>
                    </td>
                    <td>
                        <strong><?=e($room['title'])?></strong>
                        <br>
                        <span style="color: var(--text-secondary, #7A807B); font-size: 0.8rem;"><?=e($room['size'] ?? '35 sqm')?> &middot; <?=e($room['bed_type'] ?? 'King Bed')?></span>
                    </td>
                    <td><?=e($room['type_name'])?></td>
                    <td><?=e((string)$room['max_guests'])?> Guests</td>
                    <td><strong>₱<?=number_format((float)$room['price_per_night'], 2)?></strong></td>
                    <td><span class="badge" data-status="<?=e($room['status'])?>"><?=e($room['status'])?></span></td>
                    <td>
                        <form method="post" action="index.php" style="display: flex; gap: 0.5rem; align-items: center; width: auto;">
                            <input type="hidden" name="csrf" value="<?=csrf()?>">
                            <input type="hidden" name="room_id" value="<?=$room['id']?>">
                            <select name="status" style="padding: 0.4rem 0.65rem; font-size: 0.82rem; min-width: 140px;" aria-label="Change status for room <?=e($room['room_number'])?>">
                                <?php foreach (['AVAILABLE', 'OCCUPIED', 'MAINTENANCE', 'INACTIVE'] as $st): ?>
                                    <option value="<?=$st?>" <?=$room['status'] === $st ? 'selected' : ''?>><?=$st?></option>
                                <?php endforeach; ?>
                            </select>
                            <button class="btn small" type="submit">Save</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
