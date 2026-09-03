<?php
require __DIR__ . '/../includes/auth.php';

$pdo = db();
$userId = (int)user()['id'];

$s = $pdo->prepare('SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 100');
$s->execute([$userId]);
$rows = $s->fetchAll();

// Mark as read
$pdo->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = ?')->execute([$userId]);

$pageTitle = 'Guest Notifications';
require __DIR__ . '/../includes/header.php';
?>

<div class="section-head" style="margin-bottom: 2rem;">
    <div>
        <span class="kicker">COMMUNICATIONS</span>
        <h1 style="margin-bottom: 0.35rem;">Guest Notifications</h1>
        <p style="color: var(--text-secondary, #5C625D);">Updates and alerts regarding your room bookings and account.</p>
    </div>
</div>

<div class="panel">
    <?php if ($rows): ?>
        <div style="display: flex; flex-direction: column; gap: 1.25rem;">
            <?php foreach ($rows as $r): ?>
                <article style="padding: 1.25rem 1.5rem; background: var(--surface-muted, #F4EFE6); border-radius: 6px; border-left: 3px solid var(--accent, #B89650);">
                    <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 0.45rem;">
                        <h3 style="font-size: 1.15rem; margin-bottom: 0; color: var(--text-heading, #15221B);"><?=e($r['title'])?></h3>
                        <span class="badge"><?=e($r['type'] ?? 'SYSTEM')?></span>
                    </div>
                    <p style="font-size: 0.92rem; color: var(--text-primary, #2A2D2A); margin-bottom: 0.65rem; line-height: 1.6;">
                        <?=nl2br(e($r['message']))?>
                    </p>
                    <small style="color: var(--text-secondary, #7A807B); font-size: 0.75rem;">
                        <?=date('M d, Y &middot; g:i A', strtotime($r['created_at']))?>
                    </small>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 3.5rem 1rem;">
            <p style="color: var(--text-secondary, #5C625D); margin-bottom: 1.5rem;">You do not have any new notifications.</p>
            <a class="btn btn-outline" href="<?=url('customer/dashboard.php')?>">Return to Dashboard</a>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
