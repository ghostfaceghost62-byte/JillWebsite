<?php
declare(strict_types=1);
require __DIR__ . '/../../includes/admin_auth.php';

$pdo = db();

// ── POST: update room status ────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $roomId  = (int)($_POST['room_id'] ?? 0);
    $status  = $_POST['status'] ?? '';
    $allowed = [
        'AVAILABLE','OCCUPIED','BOOKED','CHECKED_IN','CHECKED_OUT',
        'MAINTENANCE','NEEDS_CLEANING','INSPECTED','INACTIVE'
    ];
    if ($roomId > 0 && in_array($status, $allowed, true)) {
        $pdo->prepare('UPDATE rooms SET status = ? WHERE id = ?')->execute([$status, $roomId]);
        log_action((int)user()['id'], 'UPDATE_ROOM_STATUS', 'room', $roomId, 'Set room status to ' . $status);
        flash('success', 'Room status updated.');
    } else {
        flash('error', 'Invalid room update request.');
    }
    header('Location: index.php');
    exit;
}

// ── GET: list all rooms ─────────────────────────────────────────────────────────
$rooms = $pdo->query("
    SELECT r.*, t.name AS type_name,
           (SELECT image_path FROM room_images ri
            WHERE ri.room_id = r.id ORDER BY ri.is_primary DESC, ri.id LIMIT 1) AS thumb
    FROM rooms r
    JOIN room_types t ON t.id = r.room_type_id
    ORDER BY r.room_number
")->fetchAll();

// Summary counts
$totalRooms     = count($rooms);
$availableCount = count(array_filter($rooms, fn($r) => $r['status'] === 'AVAILABLE'));
$occupiedCount  = count(array_filter($rooms, fn($r) => in_array($r['status'], ['OCCUPIED','CHECKED_IN'])));
$maintenanceCount = count(array_filter($rooms, fn($r) => in_array($r['status'], ['MAINTENANCE','NEEDS_CLEANING','INSPECTED'])));

$pageTitle = 'Manage Rooms';
require __DIR__ . '/../../includes/header.php';
?>

<div class="admin-page-heading">
    <div>
        <span class="kicker">INVENTORY CONTROL</span>
        <h1>Rooms &amp; Suites</h1>
        <p>Manage availability, status, and inventory for all accommodations.</p>
    </div>
    <div style="display:flex;gap:0.75rem;">
        <a class="btn small" href="<?=url('admin/rooms/add.php')?>">+ Add New Room</a>
        <a class="btn small btn-gold" href="<?=url('rooms/index.php')?>">Preview Guest View</a>
    </div>
</div>

<!-- Stats strip -->
<div class="stats-strip" style="display:flex;gap:1rem;margin-bottom:1.75rem;flex-wrap:wrap;">
    <div class="stat-pill"><strong><?=$totalRooms?></strong><span>Total Rooms</span></div>
    <div class="stat-pill confirmed"><strong><?=$availableCount?></strong><span>Available</span></div>
    <div class="stat-pill checked_in"><strong><?=$occupiedCount?></strong><span>Occupied</span></div>
    <div class="stat-pill pending"><strong><?=$maintenanceCount?></strong><span>Maintenance</span></div>
</div>

<div class="admin-panel">
    <div class="table-scroll">
        <table class="admin-tbl">
            <thead>
                <tr>
                    <th>Photo</th>
                    <th>Room #</th>
                    <th>Title / Details</th>
                    <th>Category</th>
                    <th>Capacity</th>
                    <th>Rate</th>
                    <th>Status</th>
                    <th>Update Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rooms as $room): ?>
                    <?php
                    $sc = match(true) {
                        $room['status'] === 'AVAILABLE'                => 'confirmed',
                        in_array($room['status'], ['OCCUPIED','CHECKED_IN','BOOKED'], true) => 'checked_in',
                        in_array($room['status'], ['MAINTENANCE','NEEDS_CLEANING'], true)   => 'pending',
                        $room['status'] === 'INSPECTED'                => 'confirmed',
                        default                                        => 'cancelled',
                    };
                    $fallback = 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?auto=format&fit=crop&w=200&q=60';
                    $thumb = $room['thumb'] ? url($room['thumb']) : $fallback;
                    ?>
                    <tr>
                        <td>
                            <img src="<?=e($thumb)?>" alt="<?=e($room['title'])?>"
                                 style="width:60px;height:48px;object-fit:cover;border-radius:5px;"
                                 onerror="this.src='<?=$fallback?>'">
                        </td>
                        <td>
                            <strong style="font-family:'Playfair Display',Georgia,serif;font-size:1rem;">
                                <?=e($room['room_number'])?>
                            </strong>
                            <br><small style="color:var(--text-secondary);font-size:0.74rem;">Floor <?=e($room['floor'] ?? '1')?></small>
                        </td>
                        <td>
                            <strong><?=e($room['title'])?></strong>
                            <br><small style="color:var(--text-secondary);font-size:0.74rem;"><?=e($room['size'] ?? '—')?> · <?=e($room['bed_type'] ?? '—')?></small>
                        </td>
                        <td><?=e($room['type_name'])?></td>
                        <td><?=e((string)$room['max_guests'])?> Guests</td>
                        <td><strong>₱<?=number_format((float)$room['price_per_night'], 2)?></strong></td>
                        <td><span class="s-badge <?=$sc?>"><?=e($room['status'])?></span></td>
                        <td>
                            <form method="post" action="index.php" style="display:flex;gap:0.4rem;align-items:center;">
                                <input type="hidden" name="csrf" value="<?=csrf()?>">
                                <input type="hidden" name="room_id" value="<?=$room['id']?>">
                                <select name="status" class="admin-select" aria-label="Status for room <?=e($room['room_number'])?>">
                                    <?php foreach (['AVAILABLE','OCCUPIED','BOOKED','CHECKED_IN','CHECKED_OUT','MAINTENANCE','NEEDS_CLEANING','INSPECTED','INACTIVE'] as $st): ?>
                                        <option value="<?=$st?>" <?=$room['status']===$st?'selected':''?>><?=$st?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button class="btn-details" type="submit">Save</button>
                            </form>
                        </td>
                        <td>
                            <a class="btn-details" href="<?=url('rooms/details.php?id='.$room['id'])?>">View</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.stat-pill { display:flex;flex-direction:column;align-items:center;gap:0.2rem;background:var(--surface,#fff);border:1px solid var(--border);border-radius:10px;padding:0.75rem 1.5rem;min-width:110px; }
.stat-pill strong { font-size:1.6rem;font-family:'Playfair Display',Georgia,serif;line-height:1; }
.stat-pill span   { font-size:0.75rem;color:var(--text-secondary);font-weight:600;text-transform:uppercase;letter-spacing:.06em; }
.stat-pill.confirmed  { border-left:3px solid #27ae60; }
.stat-pill.checked_in { border-left:3px solid #2980b9; }
.stat-pill.pending    { border-left:3px solid #e67e22; }
</style>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
