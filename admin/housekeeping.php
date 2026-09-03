<?php
$pageTitle = 'Housekeeping Management';
require __DIR__ . '/../includes/header.php';
require_login();

// Allow both main admins and users with the housekeeping flag
$u = user();
if (!$u['is_admin'] && empty($u['housekeeping'])) {
    flash('error', 'Access denied.');
    header('Location: ' . url('index.php'));
    exit;
}

$pdo = db();

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $roomId = (int)$_POST['room_id'];
    $status = $_POST['status'];
    $validStatuses = ['AVAILABLE', 'NEEDS_CLEANING', 'INSPECTED', 'MAINTENANCE'];
    
    if (in_array($status, $validStatuses)) {
        $pdo->prepare('UPDATE rooms SET status = ? WHERE id = ?')->execute([$status, $roomId]);
        log_action($u['id'], 'UPDATE_ROOM_STATUS', 'room', $roomId, "Status set to $status");
        flash('success', "Room status updated successfully.");
    }
    header('Location: housekeeping.php');
    exit;
}

// Fetch rooms
$rooms = $pdo->query('
    SELECT r.id, r.title, r.room_number, r.status, t.name as type_name
    FROM rooms r
    JOIN room_types t ON t.id = r.room_type_id
    ORDER BY r.status = "NEEDS_CLEANING" DESC, r.status = "INSPECTED" DESC, r.room_number ASC
')->fetchAll();

?>

<div class="admin-layout">
    <?php require __DIR__ . '/../includes/admin_sidebar.php'; ?>
    <main class="admin-content">
        <header class="admin-header">
            <h1>Housekeeping Dashboard</h1>
        </header>

        <div class="admin-panel">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Room Number</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rooms as $room): 
                        $statusColors = [
                            'AVAILABLE' => 'success',
                            'BOOKED' => 'info',
                            'CHECKED_IN' => 'warning',
                            'CHECKED_OUT' => 'error',
                            'MAINTENANCE' => 'error',
                            'NEEDS_CLEANING' => 'warning',
                            'INSPECTED' => 'success'
                        ];
                        $badge = $statusColors[$room['status']] ?? 'default';
                    ?>
                        <tr>
                            <td><strong><?=e($room['room_number'] ?? $room['title'])?></strong></td>
                            <td><?=e($room['type_name'])?></td>
                            <td><span class="badge <?=$badge?>"><?=e(str_replace('_', ' ', $room['status']))?></span></td>
                            <td>
                                <form method="post" style="display:flex; gap: 0.5rem; align-items:center;">
                                    <input type="hidden" name="csrf" value="<?=csrf()?>">
                                    <input type="hidden" name="room_id" value="<?=$room['id']?>">
                                    <select name="status" class="form-control" style="width: auto; padding: 0.25rem; font-size: 0.85rem;" onchange="this.form.submit()">
                                        <option value="" disabled selected>Update Status...</option>
                                        <option value="NEEDS_CLEANING">Needs Cleaning</option>
                                        <option value="INSPECTED">Inspected (Ready)</option>
                                        <option value="AVAILABLE">Available</option>
                                        <option value="MAINTENANCE">Maintenance</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
