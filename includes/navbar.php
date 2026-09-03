<?php
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
$isActive = static function (string $path) use ($currentPath): string {
    return str_contains($currentPath, '/'.trim($path, '/')) ? ' active' : '';
    $trimmed = trim($path, '/');
    if ($trimmed === '' || $trimmed === 'index.php') {
        return ($currentPath === '' || $currentPath === '/' || str_ends_with($currentPath, '/index.php') || str_ends_with($currentPath, '/hotelreservation') || str_ends_with($currentPath, '/hotelreservation/')) ? ' active' : '';
    }
    return str_contains($currentPath, '/' . $trimmed) ? ' active' : '';
};
?>
<header class="site-header">
    <a class="brand" href="<?=url()?>">JILL<span>HOTEL</span></a>
    <button class="menu-toggle" type="button" aria-controls="site-nav" aria-expanded="false">Menu</button>
    <a class="brand" href="<?=url()?>" aria-label="Jill Hotel Homepage">JILL<span>HOTEL</span></a>
    <button class="menu-toggle" type="button" aria-controls="site-nav" aria-expanded="false" aria-label="Toggle navigation menu">Menu</button>
    <nav id="site-nav" aria-label="Primary navigation">
        <?php if (user() && user()['role'] === 'ADMIN'): ?>
            <a class="<?=$isActive('admin/index.php')?>" href="<?=url('admin/index.php')?>">Admin Dashboard</a>
            <a class="<?=$isActive('admin/index.php')?>" href="<?=url('admin/index.php')?>">Dashboard</a>
            <a class="<?=$isActive('admin/rooms')?>" href="<?=url('admin/rooms/index.php')?>">Manage Rooms</a>
            <a class="<?=$isActive('admin/reservations')?>" href="<?=url('admin/reservations/index.php')?>">Manage Reservations</a>
            <a class="<?=$isActive('admin/reservations')?>" href="<?=url('admin/reservations/index.php')?>">Reservations</a>
            <a class="<?=$isActive('admin/reports')?>" href="<?=url('admin/reports/index.php')?>">Reports</a>
            <a class="<?=$isActive('admin/logs')?>" href="<?=url('admin/logs/index.php')?>">Activity Logs</a>
            <form class="logout-form" method="post" action="<?=url('auth/logout.php')?>">
            <a class="<?=$isActive('rooms')?>" href="<?=url('rooms/index.php')?>">View Guest Site</a>
            <form class="logout-form" method="post" action="<?=url('auth/logout.php')?>" style="display:inline;">
                <input type="hidden" name="csrf" value="<?=csrf()?>">
                <button class="link-button" type="submit">Logout</button>
            </form>
        <?php elseif (user()): ?>
            <a class="<?=$isActive('index.php')?>" href="<?=url()?>">Home</a>
            <a class="<?=$isActive('rooms')?>" href="<?=url('rooms/index.php')?>">Rooms</a>
            <a class="<?=$isActive('amenities.php')?>" href="<?=url('amenities.php')?>">Amenities</a>
            <a class="<?=$isActive('about.php')?>" href="<?=url('about.php')?>">About</a>
            <a class="btn small" href="<?=url('rooms/index.php')?>">Reserve a room</a>
            <a class="<?=$isActive('customer/reservations.php')?>" href="<?=url('customer/reservations.php')?>">My Bookings</a>
            <a class="<?=$isActive('customer/profile.php')?>" href="<?=url('customer/profile.php')?>"><?=e(user()['name'] ?? 'Profile')?></a>
            <form class="logout-form" method="post" action="<?=url('auth/logout.php')?>">
            <a class="btn small btn-gold" href="<?=url('rooms/index.php')?>">Reserve</a>
            <form class="logout-form" method="post" action="<?=url('auth/logout.php')?>" style="display:inline;">
                <input type="hidden" name="csrf" value="<?=csrf()?>">
                <button class="link-button" type="submit">Logout</button>
            </form>
        <?php else: ?>
            <a class="<?=$isActive('index.php')?>" href="<?=url()?>">Home</a>
            <a class="<?=$isActive('auth/login.php')?>" href="<?=url('auth/login.php')?>">Sign in</a>
            <a class="<?=$isActive('rooms')?>" href="<?=url('rooms/index.php')?>">Rooms</a>
            <a class="<?=$isActive('amenities.php')?>" href="<?=url('amenities.php')?>">Amenities</a>
            <a class="<?=$isActive('about.php')?>" href="<?=url('about.php')?>">About</a>
            <a class="<?=$isActive('contact.php')?>" href="<?=url('contact.php')?>">Contact</a>
            <a class="<?=$isActive('auth/login.php')?>" href="<?=url('auth/login.php')?>">Sign In</a>
            <a class="btn small" href="<?=url('auth/register.php')?>">Register</a>
        <?php endif; ?>
        <button class="theme-toggle" type="button" data-theme-toggle data-theme-preference-url="<?=user()?url('user/preferences.php'):''?>" data-csrf="<?=user()?csrf():''?>"><span aria-hidden="true" data-theme-icon>🌙</span><span data-theme-label>Dark Mode</span></button>
        <button class="theme-toggle" type="button" data-theme-toggle data-theme-preference-url="<?=user() ? url('user/preferences.php') : ''?>" data-csrf="<?=user() ? csrf() : ''?>" aria-label="Toggle visual theme">
            <span aria-hidden="true" data-theme-icon>🌙</span>
            <span data-theme-label>Dark</span>
        </button>
    </nav>
</header>
