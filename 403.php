<?php http_response_code(403); require __DIR__.'/includes/header.php';?><div class="panel"><h1>403 — Access denied</h1><p>You do not have permission to view this page.</p></div><?php require __DIR__.'/includes/footer.php'; ?>
<?php
http_response_code(403);
$pageTitle = '403 — Access Denied';
require __DIR__ . '/includes/header.php';
?>
<div class="auth-wrapper">
    <div class="auth-card" style="text-align: center;">
        <span class="kicker">RESTRICTED ACCESS</span>
        <h1 style="font-size: 2.25rem; margin-bottom: 1rem;">403 &mdash; Access Denied</h1>
        <p style="color: var(--text-secondary, #5C625D); margin-bottom: 2rem;">
            You do not have the required permissions or authentication level to view this page.
        </p>
        <div style="display: flex; justify-content: center; gap: 1rem;">
            <a class="btn" href="<?=url()?>">Return to Home</a>
            <?php if (!user()): ?>
                <a class="btn btn-outline" href="<?=url('auth/login.php')?>">Sign In</a>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
