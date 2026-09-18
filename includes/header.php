<?php
require_once __DIR__ . '/bootstrap.php';
$pageTitle = $pageTitle ?? 'Lido De Paris Hotel';
$initialTheme = '';
if (user()) {
    $themeQuery = db()->prepare('SELECT theme FROM user_preferences WHERE user_id=?');
    $themeQuery->execute([user()['id']]);
    $initialTheme = (string) ($themeQuery->fetchColumn() ?: '');
}
$isAdminShell = user() && user()['role'] === 'ADMIN';
$assetVersion = '20260919v5';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?=e($pageTitle)?> | Lido De Paris Hotel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script>
    (function(){
        try {
            var serverTheme = <?=json_encode($initialTheme)?>;
            var browserTheme = localStorage.getItem('hotelreserve-theme');
            var saved = serverTheme || browserTheme;
            var theme = (saved === 'light' || saved === 'dark') ? saved : 'light';
            document.documentElement.dataset.theme = theme;

            if (localStorage.getItem('hotelreserve-admin-sidebar-collapsed') === 'true') {
                document.documentElement.classList.add('sidebar-collapsed');
            }
        } catch(e) {
            document.documentElement.dataset.theme = 'light';
        }
    })();
    </script>
    <link rel="stylesheet" href="<?=url('assets/css/base.css?v='.$assetVersion)?>">
    <link rel="stylesheet" href="<?=url('assets/css/luxury.css?v='.$assetVersion)?>">
    <link rel="stylesheet" href="<?=url('assets/css/search-results.css?v='.$assetVersion)?>">
    <link rel="stylesheet" href="<?=url('assets/css/responsive.css?v='.$assetVersion)?>">
    <link rel="stylesheet" href="<?=url('assets/css/theme.css?v='.$assetVersion)?>">
    <?php if ($isAdminShell): ?>
        <link rel="stylesheet" href="<?=url('assets/css/admin.css?v='.$assetVersion)?>">
    <?php endif; ?>
    <script defer src="<?=url('assets/js/hotel.js?v='.$assetVersion)?>"></script>
</head>
<body class="<?=$isAdminShell ? 'admin-shell' : ''?>" data-favorites-url="<?=user() ? url('user/favorites.php') : ''?>" data-csrf="<?=user() ? csrf() : ''?>">
<?php require __DIR__ . '/navbar.php'; ?>
<!-- Toast notification container -->
<div id="toast-container" aria-live="polite" aria-atomic="true"></div>
<?php
// Emit flash as a JS-readable data attribute instead of a plain alert div
if (!empty($_SESSION['flash'])) {
    [$flashType, $flashMsg] = $_SESSION['flash'];
    unset($_SESSION['flash']);
    echo '<script>window.__flashToast=' . json_encode(['type' => $flashType, 'msg' => $flashMsg]) . ';</script>';
}
?>
<main class="container">
