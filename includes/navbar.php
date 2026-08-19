<?php
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
$isActive = static function (string $path) use ($currentPath): string {
    return str_contains($currentPath, '/'.trim($path, '/')) ? ' active' : '';
};
?>
<header class="site-header">
    <a class="brand" href="<?=url()?>">JILL<span>HOTEL</span></a>
    <button class="menu-toggle" type="button" aria-controls="site-nav" aria-expanded="false">Menu</button>
    <nav id="site-nav" aria-label="Primary navigation">
        <?php if (user() && user()['role'] === 'ADMIN'): ?>
            <a class="<?=$isActive('admin/index.php')?>" href="<?=url('admin/index.php')?>">Admin Dashboard</a>
            <a class="<?=$isActive('admin/rooms')?>" href="<?=url('admin/rooms/index.php')?>">Manage Rooms</a>
            <a class="<?=$isActive('admin/reservations')?>" href="<?=url('admin/reservations/index.php')?>">Manage Reservations</a>
            <a class="<?=$isActive('admin/reports')?>" href="<?=url('admin/reports/index.php')?>">Reports</a>
            <a class="<?=$isActive('admin/logs')?>" href="<?=url('admin/logs/index.php')?>">Activity Logs</a>
            <form class="logout-form" method="post" action="<?=url('auth/logout.php')?>">
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
                <input type="hidden" name="csrf" value="<?=csrf()?>">
                <button class="link-button" type="submit">Logout</button>
            </form>
        <?php else: ?>
            <a class="<?=$isActive('index.php')?>" href="<?=url()?>">Home</a>
            <a class="<?=$isActive('auth/login.php')?>" href="<?=url('auth/login.php')?>">Sign in</a>
            <a class="<?=$isActive('auth/login.php')?>" href="<?=url('auth/login.php?mode=admin')?>">Admin sign in</a>
            <a class="btn small" href="<?=url('auth/register.php')?>">Register</a>
        <?php endif; ?>
        <button class="theme-toggle" type="button" data-theme-toggle data-theme-preference-url="<?=user()?url('user/preferences.php'):''?>" data-csrf="<?=user()?csrf():''?>"><span aria-hidden="true" data-theme-icon>🌙</span><span data-theme-label>Dark Mode</span></button>
    </nav>
</header>
