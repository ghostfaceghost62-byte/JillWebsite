<?php http_response_code(404); require __DIR__.'/includes/header.php'; ?>
<div class="panel"><h1>404 — Page not found</h1><p>The page or room you requested could not be found.</p><a class="btn" href="<?=url()?>">Go home</a></div>
<?php require __DIR__.'/includes/footer.php'; ?>
<?php
http_response_code(404);
$pageTitle = '404 — Page Not Found';
require __DIR__ . '/includes/header.php';
?>
<div class="auth-wrapper">
    <div class="auth-card" style="text-align: center;">
        <span class="kicker">LOCATION NOT FOUND</span>
        <h1 style="font-size: 2.25rem; margin-bottom: 1rem;">404 &mdash; Page Not Found</h1>
        <p style="color: var(--text-secondary, #5C625D); margin-bottom: 2rem;">
            The page, suite, or destination you were looking for does not exist or may have been relocated.
        </p>
        <div style="display: flex; justify-content: center; gap: 1rem;">
            <a class="btn" href="<?=url()?>">Return to Home</a>
            <a class="btn btn-outline" href="<?=url('rooms/index.php')?>">Browse Suites</a>
        </div>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
