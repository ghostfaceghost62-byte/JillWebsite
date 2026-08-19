<?php http_response_code(404); require __DIR__.'/includes/header.php'; ?>
<div class="panel"><h1>404 — Page not found</h1><p>The page or room you requested could not be found.</p><a class="btn" href="<?=url()?>">Go home</a></div>
<?php require __DIR__.'/includes/footer.php'; ?>
