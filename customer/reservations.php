<?php
require __DIR__ . '/../includes/auth.php';

$pdo = db();
$s = $pdo->prepare('
    SELECT r.*, rm.title, rm.room_number, rm.bed_type
    FROM reservations r
    JOIN rooms rm ON rm.id = r.room_id
    WHERE r.user_id = ?
    ORDER BY r.created_at DESC
');
$s->execute([user()['id']]);
$rows = $s->fetchAll();

$pageTitle = 'My Stays & Bookings';
require __DIR__ . '/../includes/header.php';
?>

<div class="section-head" style="margin-bottom: 2rem;">
    <div>
        <span class="kicker">RESERVATION HISTORY</span>
        <h1 style="margin-bottom: 0.35rem;">My Stays</h1>
        <p style="color: var(--text-secondary, #5C625D);">View and manage your past and upcoming reservations with Jill Hotel.</p>
    </div>
    <a class="btn btn-gold" href="<?=url('rooms/index.php')?>">Book Another Stay</a>
</div>

<div class="panel">
    <?php if ($rows): ?>
        <div class="table-scroll">
            <table>
                <thead>
                    <tr>
                        <th>Reservation #</th>
                        <th>Suite</th>
                        <th>Stay Duration</th>
                        <th>Guests</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $r): ?>
                        <tr>
                            <td>
                                <strong style="font-family: monospace; font-size: 0.95rem;"><?=e($r['reservation_number'])?></strong>
                                <br>
                                <small style="color: var(--text-secondary, #7A807B); font-size: 0.72rem;">Booked: <?=date('M d, Y', strtotime($r['created_at']))?></small>
                            </td>
                            <td>
                                <strong><?=e($r['title'])?></strong>
                                <br>
                                <small style="color: var(--text-secondary, #7A807B);">Room <?=e($r['room_number'])?> &middot; <?=e($r['bed_type'] ?? 'Standard')?></small>
                            </td>
                            <td>
                                <?=date('M d, Y', strtotime($r['check_in']))?> &rarr; <?=date('M d, Y', strtotime($r['check_out']))?>
                                <br>
                                <small style="color: var(--text-secondary, #7A807B);"><?=$r['nights']?> Night<?=$r['nights'] > 1 ? 's' : ''?></small>
                            </td>
                            <td>
                                <?=$r['guests']?> Guest<?=$r['guests'] > 1 ? 's' : ''?>
                                <br>
                                <small style="color: var(--text-secondary, #7A807B);">(<?=$r['adults']?> Adult<?=$r['adults'] > 1 ? 's' : ''?><?=!empty($r['children']) ? ', ' . $r['children'] . ' Child' : ''?>)</small>
                            </td>
                            <td>
                                <strong style="color: var(--brand, #1C3328); font-size: 1.05rem;">₱<?=number_format((float)$r['total_amount'], 2)?></strong>
                                <br>
                                <small style="color: var(--text-secondary, #7A807B);">Includes 12% tax</small>
                            </td>
                            <td>
                                <span class="badge" data-status="<?=e($r['status'])?>"><?=e($r['status'])?></span>
                            </td>
                            <td>
                                <?php if (in_array($r['status'], ['PENDING', 'CONFIRMED']) && strtotime($r['check_in']) > time()): ?>
                                    <div style="display:flex; gap: 0.5rem; flex-wrap: wrap;">
                                        <a class="btn small" href="<?=url('customer/reservation_edit.php?id='.$r['id'])?>">Modify</a>
                                        <form method="post" action="<?=url('reservations/cancel.php')?>" onsubmit="return confirm('Are you sure you wish to cancel reservation <?=e($r['reservation_number'])?>?');">
                                            <input type="hidden" name="csrf" value="<?=csrf()?>">
                                            <input type="hidden" name="id" value="<?=$r['id']?>">
                                            <button class="btn danger small" type="submit">Cancel</button>
                                        </form>
                                    </div>
                                <?php elseif (in_array($r['status'], ['CHECKED_OUT', 'CANCELLED', 'REJECTED', 'EXPIRED'])): ?>
                                    <div style="display:flex; gap: 0.5rem; flex-wrap: wrap;">
                                        <a class="btn small btn-outline" href="<?=url('rooms/details.php?id='.$r['room_id'])?>">Book Again</a>
                                        <?php if ($r['status'] === 'CHECKED_OUT'): ?>
                                            <?php 
                                            // Check if already reviewed
                                            $revStmt = $pdo->prepare('SELECT id FROM reviews WHERE reservation_id = ?');
                                            $revStmt->execute([$r['id']]);
                                            $hasReviewed = $revStmt->fetchColumn();
                                            ?>
                                            <?php if (!$hasReviewed): ?>
                                                <a class="btn small" href="<?=url('customer/review.php?reservation_id='.$r['id'])?>">Leave Review</a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <span style="font-size: 0.8rem; color: var(--text-secondary, #7A807B);">&mdash;</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 4rem 1.5rem;">
            <h2 style="font-size: 1.5rem; margin-bottom: 0.75rem;">No Reservations Found</h2>
            <p style="color: var(--text-secondary, #5C625D); max-width: 460px; margin: 0 auto 2rem;">
                You haven't made any reservations yet. Discover our peaceful rooms and suites and reserve your next visit.
            </p>
            <a class="btn btn-gold" href="<?=url('rooms/index.php')?>">Browse Available Suites</a>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
