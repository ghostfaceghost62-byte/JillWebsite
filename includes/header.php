<?php require_once __DIR__.'/bootstrap.php'; $pageTitle = $pageTitle ?? 'Jill Hotel'; $initialTheme = ''; if (user()) { $themeQuery = db()->prepare('SELECT theme FROM user_preferences WHERE user_id=?'); $themeQuery->execute([user()['id']]); $initialTheme = (string) ($themeQuery->fetchColumn() ?: ''); } $isAdminShell = user() && user()['role'] === 'ADMIN'; ?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?=e($pageTitle)?> | Jill Hotel</title><script>(function(){try{var serverTheme=<?=json_encode($initialTheme)?>;var browserTheme=localStorage.getItem('hotelreserve-theme');var saved=serverTheme||browserTheme;var theme=(saved==='light'||saved==='dark')?saved:(matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light');document.documentElement.dataset.theme=theme;}catch(e){document.documentElement.dataset.theme='light';}})();</script><link rel="stylesheet" href="<?=url('assets/css/style.css')?>"><link rel="stylesheet" href="<?=url('assets/css/hotel.css')?>"><link rel="stylesheet" href="<?=url('assets/css/responsive.css')?>"><link rel="stylesheet" href="<?=url('assets/css/luxury.css')?>"><link rel="stylesheet" href="<?=url('assets/css/marketplace.css')?>"><link rel="stylesheet" href="<?=url('assets/css/lookbook.css')?>"><link rel="stylesheet" href="<?=url('assets/css/editorial-overrides.css')?>"><link rel="stylesheet" href="<?=url('assets/css/site-upgrade.css')?>"><link rel="stylesheet" href="<?=url('assets/css/design-system.css?v=2026090406')?>"><link rel="stylesheet" href="<?=url('assets/css/theme.css?v=2026090406')?>"><script defer src="<?=url('assets/js/hotel.js?v=2026090406')?>"></script></head><body class="<?=$isAdminShell?'admin-shell':''?>" data-favorites-url="<?=user()?url('user/favorites.php'):''?>" data-csrf="<?=user()?csrf():''?>">
<?php require __DIR__.'/navbar.php'; ?>
<main class="container"><?php show_flash(); ?>
<?php
require_once __DIR__ . '/bootstrap.php';
$pageTitle = $pageTitle ?? 'Jill Hotel';
$initialTheme = '';
if (user()) {
    $themeQuery = db()->prepare('SELECT theme FROM user_preferences WHERE user_id=?');
    $themeQuery->execute([user()['id']]);
    $initialTheme = (string) ($themeQuery->fetchColumn() ?: '');
}
$isAdminShell = user() && user()['role'] === 'ADMIN';
$assetVersion = '20260904v2';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?=e($pageTitle)?> | Jill Hotel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <script>
    (function(){
        try {
            var serverTheme = <?=json_encode($initialTheme)?>;
            var browserTheme = localStorage.getItem('hotelreserve-theme');
            var saved = serverTheme || browserTheme;
            var theme = (saved === 'light' || saved === 'dark') ? saved : (matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.dataset.theme = theme;
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
    <script defer src="<?=url('assets/js/hotel.js?v='.$assetVersion)?>"></script>
</head>
<body class="<?=$isAdminShell ? 'admin-shell' : ''?>" data-favorites-url="<?=user() ? url('user/favorites.php') : ''?>" data-csrf="<?=user() ? csrf() : ''?>">
<?php require __DIR__ . '/navbar.php'; ?>
<main class="container">
<?php show_flash(); ?>
