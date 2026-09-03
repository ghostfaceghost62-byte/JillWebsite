<?php http_response_code(500); require __DIR__.'/includes/header.php';?><div class="panel"><h1>Something went wrong</h1><p>Please try again later.</p><a class="btn" href="<?=url()?>">Go home</a></div><?php require __DIR__.'/includes/footer.php'; ?>
<?php
http_response_code(500);
$pageTitle = '500 — Server Error';
require __DIR__ . '/includes/header.php';
?>
<div class="auth-wrapper">
    <div class="auth-card" style="text-align: center;">
        <span class="kicker">SYSTEM NOTICE</span>
        <h1 style="font-size: 2.25rem; margin-bottom: 1rem;">500 &mdash; Temporary Disruption</h1>
        <p style="color: var(--text-secondary, #5C625D); margin-bottom: 2rem;">
            An unexpected error occurred while processing your request. Please try again or reach out to our concierge for assistance.
        </p>
        <div style="display: flex; justify-content: center; gap: 1rem;">
            <a class="btn" href="<?=url()?>">Return to Home</a>
            <a class="btn btn-outline" href="<?=url('contact.php')?>">Contact Concierge</a>
        </div>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
