<?php
require __DIR__.'/../../includes/admin_auth.php';
$pdo = db();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $roomId = (int)($_POST['room_id'] ?? 0);
    $status = $_POST['status'] ?? '';
    $allowed = ['AVAILABLE','OCCUPIED','MAINTENANCE','INACTIVE'];
    if ($roomId > 0 && in_array($status, $allowed, true)) {
        $update = $pdo->prepare('UPDATE rooms SET status=? WHERE id=?');
        $update->execute([$status, $roomId]);
        log_action((int)user()['id'], 'UPDATE_ROOM_STATUS', 'room', $roomId, 'Set room status to '.$status);
        flash('success', 'Room status updated.');
    } else {
        flash('error', 'Invalid room update.');
    }
    header('Location: index.php');
    exit;
}
$rooms = $pdo->query("SELECT r.*, t.name AS type_name FROM rooms r JOIN room_types t ON t.id=r.room_type_id ORDER BY r.room_number")->fetchAll();
$pageTitle = 'Manage rooms';
require __DIR__.'/../../includes/header.php';
?>
<div class="section-head"><div><p class="eyebrow">HOTEL MANAGEMENT</p><h1>Rooms</h1></div><a class="btn" href="<?=url('rooms/index.php')?>">View guest rooms</a></div>
<div class="panel"><table><tr><th>Room</th><th>Type</th><th>Capacity</th><th>Rate</th><th>Status</th><th>Update</th></tr><?php foreach ($rooms as $room): ?><tr><td><strong><?=e($room['room_number'])?></strong><br><span class="muted"><?=e($room['title'])?></span></td><td><?=e($room['type_name'])?></td><td><?=e((string)$room['max_guests'])?> guests</td><td>₱<?=number_format((float)$room['price_per_night'], 2)?></td><td><span class="badge"><?=e($room['status'])?></span></td><td><form method="post" class="toolbar"><input type="hidden" name="csrf" value="<?=csrf()?>"><input type="hidden" name="room_id" value="<?=$room['id']?>"><select name="status"><?php foreach (['AVAILABLE','OCCUPIED','MAINTENANCE','INACTIVE'] as $status): ?><option value="<?=$status?>" <?=$room['status']===$status?'selected':''?>><?=$status?></option><?php endforeach; ?></select><button class="small">Save</button></form></td></tr><?php endforeach; ?></table></div>
<?php require __DIR__.'/../../includes/footer.php'; ?>
