<?php
require __DIR__ . '/../includes/auth.php';

$pdo = db();
$userId = (int)user()['id'];

$s = $pdo->prepare('SELECT status, COUNT(*) AS n FROM reservations WHERE user_id = ? GROUP BY status');
$s->execute([$userId]);
$c = array_column($s->fetchAll(), 'n', 'status');

// Recent reservations
$recentStmt = $pdo->prepare('
    SELECT r.*, rm.title, rm.room_number, rm.price_per_night
    FROM reservations r
    JOIN rooms rm ON rm.id = r.room_id
    WHERE r.user_id = ?
    ORDER BY r.created_at DESC
    LIMIT 3
');
$recentStmt->execute([$userId]);
$recentStays = $recentStmt->fetchAll();

$pageTitle = 'Guest Dashboard';
require __DIR__ . '/../includes/header.php';
?>

<div style="margin-bottom: 2.5rem;">
    <span class="kicker">MEMBER CONCIERGE</span>
    <h1 style="margin-bottom: 0.5rem;">Welcome, <?=e(user()['name'])?></h1>
    <p style="font-size: 1.05rem; color: var(--text-secondary, #5C625D);">
        Manage your active itineraries, room reservations, and personal preferences.
    </p>
</div>

<div class="stats">
    <div class="stat">
        <b><?=($c['PENDING'] ?? 0) + ($c['CONFIRMED'] ?? 0)?></b>
        <span>Upcoming Stays</span>
    </div>
    <div class="stat">
        <b><?=$c['CHECKED_IN'] ?? 0?></b>
        <span>Active Stay</span>
    </div>
    <div class="stat">
        <b><?=$c['CHECKED_OUT'] ?? 0?></b>
        <span>Completed Visits</span>
    </div>
    <div class="stat">
        <b><?=$c['CANCELLED'] ?? 0?></b>
        <span>Cancelled</span>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; align-items: flex-start; margin-top: 1rem;">
    <section class="panel">
        <div class="section-head" style="margin-bottom: 1.25rem;">
            <div>
                <span class="kicker" style="font-size: 0.7rem; margin-bottom: 0.2rem;">ITINERARY</span>
                <h2 style="font-size: 1.35rem; margin-bottom: 0;">Recent Reservations</h2>
            </div>
            <a class="btn small btn-outline" href="<?=url('customer/reservations.php')?>">View All Stays</a>
        </div>

        <?php if ($recentStays): ?>
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Reservation #</th>
                            <th>Suite</th>
                            <th>Dates</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentStays as $stay): ?>
                            <tr>
                                <td><strong style="font-family: monospace;"><?=e($stay['reservation_number'])?></strong></td>
                                <td><?=e($stay['title'])?> <small class="muted">#<?=e($stay['room_number'])?></small></td>
                                <td><?=e($stay['check_in'])?> &rarr; <?=e($stay['check_out'])?></td>
                                <td><strong>₱<?=number_format((float)$stay['total_amount'], 2)?></strong></td>
                                <td><span class="badge" data-status="<?=e($stay['status'])?>"><?=e($stay['status'])?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 2.5rem 1rem;">
                <p style="color: var(--text-secondary, #5C625D); margin-bottom: 1.5rem;">You do not have any bookings registered yet.</p>
                <a class="btn btn-gold" href="<?=url('rooms/index.php')?>">Explore Suites & Book</a>
            </div>
        <?php endif; ?>
    </section>

    <aside class="panel" style="background: var(--surface-muted, #F4EFE6); border: none;">
        <h3 style="font-size: 1.2rem; margin-bottom: 1rem;">Quick Actions</h3>
        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            <a class="btn btn-gold" href="<?=url('rooms/index.php')?>" style="justify-content: flex-start;">
                ✦ Reserve a New Stay
            </a>
            <a class="btn" href="<?=url('customer/reservations.php')?>" style="justify-content: flex-start; background: var(--brand, #1C3328); color: #FAF8F5;">
                📋 All Reservation Details
            </a>
            <a class="btn" href="<?=url('customer/profile.php')?>" style="justify-content: flex-start; background: var(--brand, #1C3328); color: #FAF8F5;">
                👤 Edit Account Profile
            </a>
            <a class="btn" href="<?=url('customer/notifications.php')?>" style="justify-content: flex-start; background: var(--brand, #1C3328); color: #FAF8F5;">
                🔔 Guest Notifications
            </a>
        </div>
    </aside>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
