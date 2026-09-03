<?php
require __DIR__ . '/../../includes/admin_auth.php';

$pdo = db();
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    check_csrf();
    $id = (int)($_POST['id'] ?? 0);
    $actionMap = [
        'confirm'   => 'CONFIRMED',
        'reject'    => 'REJECTED',
        'check_in'  => 'CHECKED_IN',
        'check_out' => 'CHECKED_OUT',
        'cancel'    => 'CANCELLED',
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
            flash('success', 'Reservation ' . $r['reservation_number'] . ' updated to ' . $next . '.');
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

<div class="admin-page-heading">
    <div>
        <span class="kicker">FRONT DESK &amp; CONCIERGE</span>
        <h1>Manage Reservations</h1>
        <p>Review, confirm, check-in, or update guest reservation records.</p>
    </div>
</div>

<div class="admin-panel">
    <div class="table-scroll">
        <table class="admin-tbl">
            <thead>
                <tr>
                    <th>Reservation #</th>
                    <th>Guest</th>
                    <th>Room</th>
                    <th>Check-In</th>
                    <th>Check-Out</th>
                    <th>Rate</th>
                    <th>Status</th>
                    <th>Update</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $r): ?>
                    <tr>
                        <td>
                            <strong><?=e($r['reservation_number'])?></strong>
                            <br><small style="color:var(--text-secondary);font-size:0.74rem;">Booked: <?=date('M d, Y', strtotime($r['created_at']))?></small>
                        </td>
                        <td>
                            <strong><?=e($r['first_name'] . ' ' . $r['last_name'])?></strong>
                            <br><small style="color:var(--text-secondary);font-size:0.74rem;"><?=e($r['email'])?><?=!empty($r['phone']) ? ' &middot; ' . e($r['phone']) : ''?></small>
                        </td>
                        <td>
                            <strong><?=e($r['title'])?></strong>
                            <br><small style="color:var(--text-secondary);font-size:0.74rem;">Room <?=e($r['room_number'])?> &middot; <?=$r['guests']?> Guest<?=$r['guests'] > 1 ? 's' : ''?></small>
                        </td>
                        <td><?=date('M d, Y', strtotime($r['check_in']))?></td>
                        <td>
                            <?=date('M d, Y', strtotime($r['check_out']))?>
                            <br><small style="color:var(--text-secondary);font-size:0.74rem;"><?=$r['nights']?> Night<?=$r['nights'] > 1 ? 's' : ''?></small>
                        </td>
                        <td><strong>₱<?=number_format((float)$r['total_amount'], 2)?></strong></td>
                        <td>
                            <span class="s-badge <?=e(strtolower($r['status']))?>">
                                <?=e(ucfirst(strtolower(str_replace('_', ' ', $r['status']))))?>
                            </span>
                        </td>
                        <td>
                            <form method="post" action="index.php" style="display:flex;gap:0.4rem;align-items:center;">
                                <input type="hidden" name="csrf" value="<?=csrf()?>">
                                <input type="hidden" name="id" value="<?=$r['id']?>">
                                <select name="action" class="admin-select" aria-label="Action for <?=e($r['reservation_number'])?>">
                                    <option value="confirm"   <?=$r['status']==='CONFIRMED'  ?'selected':''?>>Confirm</option>
                                    <option value="check_in"  <?=$r['status']==='CHECKED_IN' ?'selected':''?>>Check In</option>
                                    <option value="check_out" <?=$r['status']==='CHECKED_OUT'?'selected':''?>>Check Out</option>
                                    <option value="reject"    <?=$r['status']==='REJECTED'   ?'selected':''?>>Reject</option>
                                    <option value="cancel"    <?=$r['status']==='CANCELLED'  ?'selected':''?>>Cancel</option>
                                </select>
                                <button class="btn-details" type="submit">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
