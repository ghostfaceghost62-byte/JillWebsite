<?php
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
$isActive = static function (string $path) use ($currentPath): string {
    $trimmed = trim($path, '/');
    if ($trimmed === '' || $trimmed === 'index.php') {
        return ($currentPath === '' || $currentPath === '/' || str_ends_with($currentPath, '/index.php') || str_ends_with($currentPath, '/hotelreservation') || str_ends_with($currentPath, '/hotelreservation/')) ? ' active' : '';
    }
    return str_contains($currentPath, '/' . $trimmed) ? ' active' : '';
};
?>
<?php if (user() && user()['role'] === 'ADMIN'): ?>
<aside class="admin-sidebar">
    <a class="sidebar-brand" href="<?=url('admin/index.php')?>">JILL HOTEL</a>
    <span class="sidebar-label">JILL HOTEL ADMIN</span>
    <nav aria-label="Admin navigation">
        <a class="<?=$isActive('admin/index.php')?>" href="<?=url('admin/index.php')?>">
            <span class="nav-icon">
                <svg width="18" height="18" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            </span>
            Dashboard
        </a>
        <a class="<?=$isActive('admin/reservations')?>" href="<?=url('admin/reservations/index.php')?>">
            <span class="nav-icon">
                <svg width="18" height="18" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </span>
            Reservations
        </a>
        <a class="<?=$isActive('admin/rooms')?>" href="<?=url('admin/rooms/index.php')?>">
            <span class="nav-icon">
                <svg width="18" height="18" viewBox="0 0 24 24"><path d="M2 4v16"/><path d="M2 8h18a2 2 0 0 1 2 2v10"/><path d="M2 17h20"/><path d="M6 8v9"/></svg>
            </span>
            Rooms
        </a>
        <a class="<?=$isActive('admin/users')?>" href="<?=url('admin/index.php')?>">
            <span class="nav-icon">
                <svg width="18" height="18" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </span>
            Guest Management
        </a>
        <a class="<?=$isActive('admin/calendar.php')?>" href="<?=url('admin/calendar.php')?>">
            <span class="nav-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </span>
            Calendar Grid
        </a>
        <a class="<?=$isActive('admin/housekeeping.php')?>" href="<?=url('admin/housekeeping.php')?>">
            <span class="nav-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            </span>
            Housekeeping
        </a>
        <a class="<?=$isActive('admin/reports')?>" href="<?=url('admin/reports/index.php')?>">
            <span class="nav-icon">
                <svg width="18" height="18" viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
            </span>
            Reports
        </a>
        <a class="<?=$isActive('admin/logs')?>" href="<?=url('admin/logs/index.php')?>">
            <span class="nav-icon">
                <svg width="18" height="18" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </span>
            Activity Logs
        </a>
        <a class="<?=$isActive('customer/profile.php')?>" href="<?=url('customer/profile.php')?>">
            <span class="nav-icon">
                <svg width="18" height="18" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
            </span>
            Settings
        </a>
    </nav>
    <div class="sidebar-logout">
        <form method="post" action="<?=url('auth/logout.php')?>">
            <input type="hidden" name="csrf" value="<?=csrf()?>">
            <button type="submit">Sign out</button>
        </form>
    </div>
</aside>
<div class="admin-topbar">
    <form class="topbar-search" method="get" action="<?=url('admin/reservations/index.php')?>">
        <span class="search-icon" aria-hidden="true">
            <svg width="15" height="15" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        </span>
        <input type="search" name="q" placeholder="Search" aria-label="Search">
    </form>
    <div class="topbar-spacer"></div>
    <div class="topbar-roles" aria-label="View role">
        <a class="role-pill" href="<?=url()?>">VISITOR</a>
        <a class="role-pill" href="<?=url('customer/dashboard.php')?>">GUEST</a>
        <span class="role-pill is-active">ADMIN</span>
    </div>
    <div class="topbar-actions">
        <button class="topbar-icon-btn" type="button" aria-label="Notifications" title="Notifications">
            <svg width="18" height="18" viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
        </button>
        <button class="theme-toggle topbar-icon-btn" type="button" data-theme-toggle data-theme-preference-url="<?=url('user/preferences.php')?>" data-csrf="<?=csrf()?>" aria-label="Toggle visual theme" title="Toggle theme">
            <svg width="18" height="18" viewBox="0 0 24 24"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
        </button>
        <span class="topbar-avatar" title="<?=e(user()['name'] ?? 'Admin')?>">
            <?=e(strtoupper(substr(user()['first_name'] ?? user()['name'] ?? 'A', 0, 1)))?>
        </span>
    </div>
</div>
<?php else: ?>
<header class="site-header">
    <a class="brand" href="<?=url()?>" aria-label="Jill Hotel Homepage">JILL<span>HOTEL</span></a>
    <button class="menu-toggle" type="button" aria-controls="site-nav" aria-expanded="false" aria-label="Toggle navigation menu">Menu</button>
    <nav id="site-nav" aria-label="Primary navigation">
        <?php if (user()): ?>
            <a class="<?=$isActive('index.php')?>" href="<?=url()?>">Home</a>
            <a class="<?=$isActive('rooms')?>" href="<?=url('rooms/index.php')?>">Rooms</a>
            <a class="<?=$isActive('amenities.php')?>" href="<?=url('amenities.php')?>">Amenities</a>
            <a class="<?=$isActive('about.php')?>" href="<?=url('about.php')?>">About</a>
            <a class="<?=$isActive('customer/reservations.php')?>" href="<?=url('customer/reservations.php')?>">My Bookings</a>
            <a class="<?=$isActive('customer/profile.php')?>" href="<?=url('customer/profile.php')?>"><?=e(user()['name'] ?? 'Profile')?></a>
            <a class="btn small btn-gold" href="<?=url('rooms/index.php')?>">Reserve</a>
            <form class="logout-form" method="post" action="<?=url('auth/logout.php')?>" style="display:inline;">
                <input type="hidden" name="csrf" value="<?=csrf()?>">
                <button class="link-button" type="submit">Logout</button>
            </form>
        <?php else: ?>
            <a class="<?=$isActive('index.php')?>" href="<?=url()?>">Home</a>
            <a class="<?=$isActive('auth/login.php')?>" href="<?=url('auth/login.php')?>">Sign In</a>
            <a class="<?=$isActive('rooms')?>" href="<?=url('rooms/index.php')?>">Rooms</a>
            <a class="<?=$isActive('amenities.php')?>" href="<?=url('amenities.php')?>">Amenities</a>
            <a class="<?=$isActive('about.php')?>" href="<?=url('about.php')?>">About</a>
            <a class="<?=$isActive('contact.php')?>" href="<?=url('contact.php')?>">Contact</a>
            <a class="btn small" href="<?=url('auth/register.php')?>">Register</a>
        <?php endif; ?>
        <div class="nav-controls" style="display: flex; align-items: center; gap: 0.75rem; margin-left: auto;">
            <select id="currency_switcher" aria-label="Select Currency" style="background: transparent; color: inherit; border: 1px solid var(--border); border-radius: 4px; padding: 0.25rem 0.5rem; font-size: 0.85rem; cursor: pointer;">
                <option value="PHP">₱ PHP</option>
                <option value="USD">$ USD</option>
                <option value="EUR">€ EUR</option>
                <option value="JPY">¥ JPY</option>
                <option value="SGD">S$ SGD</option>
                <option value="AUD">A$ AUD</option>
                <option value="GBP">£ GBP</option>
            </select>
            <script>
            document.addEventListener("DOMContentLoaded", () => {
                const sel = document.getElementById('currency_switcher');
                const saved = localStorage.getItem('hotelreserve-currency') || 'PHP';
                sel.value = saved;
                sel.addEventListener('change', (e) => {
                    localStorage.setItem('hotelreserve-currency', e.target.value);
                    window.dispatchEvent(new CustomEvent('currencyChanged', { detail: e.target.value }));
                });
            });
            </script>

            <button class="theme-toggle" type="button" data-theme-toggle data-theme-preference-url="<?=user() ? url('user/preferences.php') : ''?>" data-csrf="<?=user() ? csrf() : ''?>" aria-label="Toggle visual theme">
                <span aria-hidden="true" data-theme-icon>☀️</span>
                <span data-theme-label>Light</span>
            </button>
        </div>
    </nav>
</header>
<?php endif; ?>
