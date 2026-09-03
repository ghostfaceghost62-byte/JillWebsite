<?php require __DIR__.'/../../includes/admin_auth.php';$pdo=db();if($_SERVER['REQUEST_METHOD']==='POST'){check_csrf();$id=(int)$_POST['id'];$next=['confirm'=>'CONFIRMED','reject'=>'REJECTED','check_in'=>'CHECKED_IN','check_out'=>'CHECKED_OUT','cancel'=>'CANCELLED'][$_POST['action']??'']??null;if($next){$s=$pdo->prepare('SELECT user_id,reservation_number FROM reservations WHERE id=?');$s->execute([$id]);$r=$s->fetch();if($r){$pdo->prepare('UPDATE reservations SET status=? WHERE id=?')->execute([$next,$id]);notify($r['user_id'],'Reservation update','Your reservation '.$r['reservation_number'].' is now '.$next.'.','RESERVATION');log_action(user()['id'],'RESERVATION_'.$next,'reservation',$id,'Updated '.$r['reservation_number']);flash('success','Reservation updated.');}}header('Location:index.php');exit;}$rows=$pdo->query("SELECT r.*,rm.title,rm.room_number,u.first_name,u.last_name FROM reservations r JOIN rooms rm ON rm.id=r.room_id JOIN users u ON u.id=r.user_id ORDER BY r.created_at DESC")->fetchAll();$pageTitle='Manage reservations';require __DIR__.'/../../includes/header.php';?><h1>Reservations</h1><div class="panel table-scroll"><table><thead><tr><th>Reservation</th><th>Guest</th><th>Room</th><th>Stay</th><th>Status</th><th>Action</th></tr></thead><tbody><?php foreach($rows as $r):?><tr><td><?=e($r['reservation_number'])?></td><td><?=e($r['first_name'].' '.$r['last_name'])?></td><td><?=e($r['title'].' #'.$r['room_number'])?></td><td><?=e($r['check_in'].' → '.$r['check_out'])?></td><td><span class="badge"><?=e($r['status'])?></span></td><td><form method="post"><input type="hidden" name="csrf" value="<?=csrf()?>"><input type="hidden" name="id" value="<?=$r['id']?>"><select name="action"><option value="confirm">Confirm</option><option value="check_in">Check in</option><option value="check_out">Check out</option><option value="reject">Reject</option><option value="cancel">Cancel</option></select><button class="small">Update</button></form></td></tr><?php endforeach;?></tbody></table></div><?php require __DIR__.'/../../includes/footer.php'; ?>
<?php
require __DIR__ . '/../../includes/admin_auth.php';

$pdo = db();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $id = (int)($_POST['id'] ?? 0);
    $actionMap = [
        'confirm' => 'CONFIRMED',
        'reject' => 'REJECTED',
        'check_in' => 'CHECKED_IN',
        'check_out' => 'CHECKED_OUT',
        'cancel' => 'CANCELLED'
    ];
    $next = $actionMap[$_POST['action'] ?? ''] ?? null;

    if ($next && $id > 0) {
        $s = $pdo->prepare('SELECT user_id, reservation_number FROM reservations WHERE id = ?');
        $s->execute([$id]);
        $r = $s->fetch();

        if ($r) {
            $pdo->prepare('UPDATE reservations SET status = ? WHERE id = ?')->execute([$next, $id]);
            notify($r['user_id'], 'Reservation Update', 'Your reservation ' . $r['reservation_number'] . ' is now ' . $next . '.', 'RESERVATION');
            log_action(user()['id'], 'RESERVATION_' . $next, 'reservation', $id, 'Updated status of ' . $r['reservation_number'] . ' to ' . $next);
            flash('success', 'Reservation ' . $r['reservation_number'] . ' status updated to ' . $next . '.');
        }
    }
    header('Location: index.php');
    exit;
}

$rows = $pdo->query("
    SELECT r.*, rm.title, rm.room_number, u.first_name, u.last_name, u.email, u.phone
    FROM reservations r
    JOIN rooms rm ON rm.id = r.room_id
    JOIN users u ON u.id = r.user_id
    ORDER BY r.created_at DESC
")->fetchAll();

$pageTitle = 'Manage Reservations';
require __DIR__ . '/../../includes/header.php';
?>

<div class="section-head" style="margin-bottom: 2rem;">
    <div>
        <span class="kicker">FRONT DESK & CONCIERGE</span>
        <h1 style="margin-bottom: 0.35rem;">Manage Reservations</h1>
        <p style="color: var(--text-secondary, #5C625D);">Review, confirm, check-in, or update guest reservation records.</p>
    </div>
</div>

<div class="panel table-scroll">
    <table>
        <thead>
            <tr>
                <th>Reservation #</th>
                <th>Guest</th>
                <th>Suite & Room</th>
                <th>Stay Dates</th>
                <th>Total Rate</th>
                <th>Status</th>
                <th>Update Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $r): ?>
                <tr>
                    <td>
                        <strong style="font-family: monospace; font-size: 0.95rem;"><?=e($r['reservation_number'])?></strong>
                        <br>
                        <small style="color: var(--text-secondary, #7A807B);">Booked: <?=date('M d, Y', strtotime($r['created_at']))?></small>
                    </td>
                    <td>
                        <strong><?=e($r['first_name'] . ' ' . $r['last_name'])?></strong>
                        <br>
                        <small style="color: var(--text-secondary, #7A807B);"><?=e($r['email'])?><?=!empty($r['phone']) ? ' &middot; ' . e($r['phone']) : ''?></small>
                    </td>
                    <td>
                        <strong><?=e($r['title'])?></strong>
                        <br>
                        <small style="color: var(--text-secondary, #7A807B);">Room <?=e($r['room_number'])?> &middot; <?=$r['guests']?> Guest<?=$r['guests'] > 1 ? 's' : ''?></small>
                    </td>
                    <td>
                        <?=date('M d, Y', strtotime($r['check_in']))?> &rarr; <?=date('M d, Y', strtotime($r['check_out']))?>
                        <br>
                        <small style="color: var(--text-secondary, #7A807B);"><?=$r['nights']?> Night<?=$r['nights'] > 1 ? 's' : ''?></small>
                    </td>
                    <td>
                        <strong>₱<?=number_format((float)$r['total_amount'], 2)?></strong>
                    </td>
                    <td>
                        <span class="badge" data-status="<?=e($r['status'])?>"><?=e($r['status'])?></span>
                    </td>
                    <td>
                        <form method="post" action="index.php" style="display: flex; gap: 0.5rem; align-items: center; width: auto;">
                            <input type="hidden" name="csrf" value="<?=csrf()?>">
                            <input type="hidden" name="id" value="<?=$r['id']?>">
                            <select name="action" style="padding: 0.4rem 0.65rem; font-size: 0.82rem; min-width: 130px;" aria-label="Action for reservation <?=e($r['reservation_number'])?>">
                                <option value="confirm" <?=$r['status'] === 'CONFIRMED' ? 'selected' : ''?>>Confirm</option>
                                <option value="check_in" <?=$r['status'] === 'CHECKED_IN' ? 'selected' : ''?>>Check In</option>
                                <option value="check_out" <?=$r['status'] === 'CHECKED_OUT' ? 'selected' : ''?>>Check Out</option>
                                <option value="reject" <?=$r['status'] === 'REJECTED' ? 'selected' : ''?>>Reject</option>
                                <option value="cancel" <?=$r['status'] === 'CANCELLED' ? 'selected' : ''?>>Cancel</option>
                            </select>
                            <button class="btn small" type="submit">Update</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
