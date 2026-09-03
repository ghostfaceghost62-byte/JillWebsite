<?php
require __DIR__.'/../includes/admin_auth.php';
$pdo=db();
$metrics=['Total rooms'=>'SELECT COUNT(*) FROM rooms','Available rooms'=>"SELECT COUNT(*) FROM rooms WHERE status='AVAILABLE'",'Occupied rooms'=>"SELECT COUNT(*) FROM rooms WHERE status='OCCUPIED'",'Maintenance rooms'=>"SELECT COUNT(*) FROM rooms WHERE status='MAINTENANCE'",'Today check-ins'=>"SELECT COUNT(*) FROM reservations WHERE check_in=CURDATE() AND status='CONFIRMED'",'Today check-outs'=>"SELECT COUNT(*) FROM reservations WHERE check_out=CURDATE() AND status='CHECKED_IN'",'Pending reservations'=>"SELECT COUNT(*) FROM reservations WHERE status='PENDING'",'Revenue'=>"SELECT COALESCE(SUM(amount),0) FROM payments WHERE payment_status='PAID'"];
$dashboardRooms=$pdo->query("SELECT r.*, t.name AS type_name FROM rooms r JOIN room_types t ON t.id=r.room_type_id ORDER BY r.room_number")->fetchAll();
$dashboardReservations=$pdo->query("SELECT r.*,rm.title,rm.room_number,u.first_name,u.last_name FROM reservations r JOIN rooms rm ON rm.id=r.room_id JOIN users u ON u.id=r.user_id ORDER BY r.created_at DESC")->fetchAll();
$dashboardLogs=$pdo->query("SELECT l.*,CONCAT(COALESCE(u.first_name,'System'),' ',COALESCE(u.last_name,'')) name FROM activity_logs l LEFT JOIN users u ON u.id=l.user_id ORDER BY l.created_at DESC LIMIT 100")->fetchAll();
$pageTitle='Admin dashboard';
require __DIR__.'/../includes/header.php';
require __DIR__ . '/../includes/admin_auth.php';

$pdo = db();
$metrics = [
    'Total Rooms' => 'SELECT COUNT(*) FROM rooms',
    'Available Rooms' => "SELECT COUNT(*) FROM rooms WHERE status='AVAILABLE'",
    'Occupied Rooms' => "SELECT COUNT(*) FROM rooms WHERE status='OCCUPIED'",
    'Maintenance' => "SELECT COUNT(*) FROM rooms WHERE status='MAINTENANCE'",
    'Today Check-ins' => "SELECT COUNT(*) FROM reservations WHERE check_in = CURDATE() AND status='CONFIRMED'",
    'Today Check-outs' => "SELECT COUNT(*) FROM reservations WHERE check_out = CURDATE() AND status='CHECKED_IN'",
    'Pending Stays' => "SELECT COUNT(*) FROM reservations WHERE status='PENDING'",
    'Total Revenue' => "SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payment_status='PAID'"
];

$dashboardRooms = $pdo->query("SELECT r.*, t.name AS type_name FROM rooms r JOIN room_types t ON t.id = r.room_type_id ORDER BY r.room_number")->fetchAll();
$dashboardReservations = $pdo->query("SELECT r.*, rm.title, rm.room_number, u.first_name, u.last_name FROM reservations r JOIN rooms rm ON rm.id = r.room_id JOIN users u ON u.id = r.user_id ORDER BY r.created_at DESC LIMIT 10")->fetchAll();
$dashboardLogs = $pdo->query("SELECT l.*, CONCAT(COALESCE(u.first_name,'System'),' ',COALESCE(u.last_name,'')) AS name FROM activity_logs l LEFT JOIN users u ON u.id = l.user_id ORDER BY l.created_at DESC LIMIT 25")->fetchAll();

$pageTitle = 'Management Dashboard';
require __DIR__ . '/../includes/header.php';
?>
<h1>Jill Hotel - Admin Dashboard</h1>
<div class="stats">
    <?php foreach($metrics as $name=>$q):?>
        <div class="stat"><b><?=str_contains($name,'Revenue')?'₱'.number_format((float)$pdo->query($q)->fetchColumn(),2):$pdo->query($q)->fetchColumn()?></b><span><?=e($name)?></span></div>
    <?php endforeach;?>

<div class="section-head" style="margin-bottom: 2rem;">
    <div>
        <span class="kicker">EXECUTIVE ADMINISTRATION</span>
        <h1 style="margin-bottom: 0.35rem;">Jill Hotel Management Dashboard</h1>
        <p style="color: var(--text-secondary, #5C625D);">Live overview of suites, guest reservations, revenue, and system operations.</p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <a class="btn small btn-gold" href="<?=url('admin/reservations/index.php')?>">Manage Reservations</a>
        <a class="btn small btn-outline" href="<?=url('admin/rooms/index.php')?>">Manage Rooms</a>
    </div>
</div>

<section class="admin-table-section">
    <div class="section-head"><h2>Rooms</h2><a class="btn small" href="<?=url('admin/rooms/index.php')?>">Manage rooms</a></div>
    <div class="panel table-scroll"><table>
        <thead><tr><th>Room</th><th>Type</th><th>Capacity</th><th>Rate</th><th>Status</th></tr></thead>
        <tbody><?php foreach($dashboardRooms as $room):?>
            <tr><td><strong><?=e($room['room_number'])?></strong><br><span class="muted"><?=e($room['title'])?></span></td><td><?=e($room['type_name'])?></td><td><?=e((string)$room['max_guests'])?> guests</td><td>₱<?=number_format((float)$room['price_per_night'],2)?></td><td><span class="badge"><?=e($room['status'])?></span></td></tr>
        <?php endforeach;?></tbody>
    </table></div>
<div class="stats" style="grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));">
    <?php foreach ($metrics as $name => $q):
        $val = (float)$pdo->query($q)->fetchColumn();
    ?>
        <div class="stat">
            <b><?=str_contains($name, 'Revenue') ? '₱' . number_format($val, 2) : number_format($val)?></b>
            <span><?=e($name)?></span>
        </div>
    <?php endforeach; ?>
</div>

<section class="admin-table-section" style="margin-top: 2.5rem;">
    <div class="section-head" style="margin-bottom: 1rem;">
        <div>
            <span class="kicker" style="font-size: 0.7rem; margin-bottom: 0.2rem;">ACCOMMODATIONS</span>
            <h2 style="font-size: 1.45rem; margin-bottom: 0;">Room Inventory & Rates</h2>
        </div>
        <a class="btn small btn-outline" href="<?=url('admin/rooms/index.php')?>">Edit Room Status</a>
    </div>
    <div class="panel table-scroll">
        <table>
            <thead>
                <tr>
                    <th>Room</th>
                    <th>Type</th>
                    <th>Capacity</th>
                    <th>Nightly Rate</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dashboardRooms as $room): ?>
                    <tr>
                        <td>
                            <strong>Room <?=e($room['room_number'])?></strong>
                            <br>
                            <span style="color: var(--text-secondary, #7A807B); font-size: 0.82rem;"><?=e($room['title'])?></span>
                        </td>
                        <td><?=e($room['type_name'])?></td>
                        <td><?=e((string)$room['max_guests'])?> Guests</td>
                        <td><strong>₱<?=number_format((float)$room['price_per_night'], 2)?></strong></td>
                        <td><span class="badge" data-status="<?=e($room['status'])?>"><?=e($room['status'])?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<section class="admin-table-section">
    <div class="section-head"><h2>Reservations</h2><a class="btn small" href="<?=url('admin/reservations/index.php')?>">Manage reservations</a></div>
    <div class="panel table-scroll"><table>
        <thead><tr><th>Reservation</th><th>Guest</th><th>Room</th><th>Stay</th><th>Status</th></tr></thead>
        <tbody><?php foreach($dashboardReservations as $reservation):?>
            <tr><td><?=e($reservation['reservation_number'])?></td><td><?=e($reservation['first_name'].' '.$reservation['last_name'])?></td><td><?=e($reservation['title'].' #'.$reservation['room_number'])?></td><td><?=e($reservation['check_in'].' → '.$reservation['check_out'])?></td><td><span class="badge"><?=e($reservation['status'])?></span></td></tr>
        <?php endforeach;?></tbody>
    </table></div>
<section class="admin-table-section" style="margin-top: 3rem;">
    <div class="section-head" style="margin-bottom: 1rem;">
        <div>
            <span class="kicker" style="font-size: 0.7rem; margin-bottom: 0.2rem;">GUEST STAYS</span>
            <h2 style="font-size: 1.45rem; margin-bottom: 0;">Latest Reservations</h2>
        </div>
        <a class="btn small btn-outline" href="<?=url('admin/reservations/index.php')?>">View All Reservations</a>
    </div>
    <div class="panel table-scroll">
        <table>
            <thead>
                <tr>
                    <th>Reservation #</th>
                    <th>Guest Name</th>
                    <th>Reserved Room</th>
                    <th>Stay Dates</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dashboardReservations as $res): ?>
                    <tr>
                        <td><strong style="font-family: monospace;"><?=e($res['reservation_number'])?></strong></td>
                        <td><?=e($res['first_name'] . ' ' . $res['last_name'])?></td>
                        <td><?=e($res['title'])?> <small class="muted">#<?=e($res['room_number'])?></small></td>
                        <td><?=e($res['check_in'])?> &rarr; <?=e($res['check_out'])?></td>
                        <td><strong>₱<?=number_format((float)$res['total_amount'], 2)?></strong></td>
                        <td><span class="badge" data-status="<?=e($res['status'])?>"><?=e($res['status'])?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<section class="admin-table-section">
    <div class="section-head"><h2>Activity logs</h2><a class="btn small" href="<?=url('admin/logs/index.php')?>">View all logs</a></div>
    <div class="panel table-scroll"><table>
        <thead><tr><th>User</th><th>Action</th><th>Description</th><th>Entity</th><th>Date</th></tr></thead>
        <tbody><?php foreach($dashboardLogs as $log):?>
            <tr><td><?=e($log['name'])?></td><td><span class="badge"><?=e($log['action'])?></span></td><td><?=e($log['description'])?></td><td><?=e($log['entity_type'].' #'.$log['entity_id'])?></td><td><?=e($log['created_at'])?></td></tr>
        <?php endforeach;?></tbody>
    </table></div>
<section class="admin-table-section" style="margin-top: 3rem;">
    <div class="section-head" style="margin-bottom: 1rem;">
        <div>
            <span class="kicker" style="font-size: 0.7rem; margin-bottom: 0.2rem;">AUDIT TRAIL</span>
            <h2 style="font-size: 1.45rem; margin-bottom: 0;">Recent Activity Log</h2>
        </div>
        <a class="btn small btn-outline" href="<?=url('admin/logs/index.php')?>">View Complete Logs</a>
    </div>
    <div class="panel table-scroll">
        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>Action</th>
                    <th>Description</th>
                    <th>Target Entity</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dashboardLogs as $log): ?>
                    <tr>
                        <td><strong><?=e($log['name'])?></strong></td>
                        <td><span class="badge"><?=e($log['action'])?></span></td>
                        <td><?=e($log['description'])?></td>
                        <td><?=e($log['entity_type'] . ' #' . $log['entity_id'])?></td>
                        <td><small style="color: var(--text-secondary, #7A807B);"><?=date('M d, Y &middot; g:i A', strtotime($log['created_at']))?></small></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php require __DIR__.'/../includes/footer.php'; ?>

<?php require __DIR__ . '/../includes/footer.php'; ?>
