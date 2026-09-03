<?php
declare(strict_types=1);
require __DIR__ . '/../../includes/admin_auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('admin/rooms/add.php'));
    exit;
}
check_csrf();

$pdo = db();

// ── Validate required fields ──────────────────────────────────────────────────
$errors = [];

$roomNumber  = trim($_POST['room_number']   ?? '');
$typeId      = (int)($_POST['room_type_id'] ?? 0);
$title       = trim($_POST['title']         ?? '');
$description = trim($_POST['description']   ?? '');
$price       = (float)($_POST['price_per_night'] ?? 0);
$maxGuests   = (int)($_POST['max_guests']   ?? 1);
$bedType     = trim($_POST['bed_type']      ?? '');
$size        = trim($_POST['size']          ?? '');
$floor       = trim($_POST['floor']         ?? '');
$featured    = isset($_POST['featured']) ? 1 : 0;
$amenityIds  = array_values(array_filter(
    array_map('intval', (array)($_POST['amenities'] ?? [])),
    static fn(int $v): bool => $v > 0
));

if ($roomNumber === '') $errors[] = 'Room number is required.';
if ($typeId === 0)      $errors[] = 'Room category is required.';
if ($title === '')      $errors[] = 'Room title is required.';
if ($price <= 0)        $errors[] = 'Nightly rate must be greater than zero.';
if ($maxGuests < 1)     $errors[] = 'Max guests must be at least 1.';

// Check room number uniqueness
if ($roomNumber !== '') {
    $chk = $pdo->prepare('SELECT id FROM rooms WHERE room_number = ?');
    $chk->execute([$roomNumber]);
    if ($chk->fetchColumn()) {
        $errors[] = "Room number "{$roomNumber}" is already taken.";
    }
}

if ($errors) {
    flash('error', implode(' ', $errors));
    header('Location: ' . url('admin/rooms/add.php'));
    exit;
}

// ── Handle Image Uploads ──────────────────────────────────────────────────────
$uploadDir = __DIR__ . '/../../assets/room_images/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$uploadedPaths = [];
$files = $_FILES['room_images'] ?? [];

if (!empty($files['name'][0])) {
    $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
    $maxSize      = 5 * 1024 * 1024; // 5 MB

    foreach ($files['tmp_name'] as $idx => $tmp) {
        if ($files['error'][$idx] !== UPLOAD_ERR_OK) continue;
        if ($files['size'][$idx] > $maxSize)         continue;

        $mime = mime_content_type($tmp);
        if (!in_array($mime, $allowedMimes, true))   continue;

        $ext      = match($mime) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            default      => 'jpg',
        };
        $filename = 'room_' . uniqid('', true) . '.' . $ext;
        $dest     = $uploadDir . $filename;

        if (move_uploaded_file($tmp, $dest)) {
            $uploadedPaths[] = 'assets/room_images/' . $filename;
        }

        if (count($uploadedPaths) >= 8) break;
    }
}

// ── Insert Room ───────────────────────────────────────────────────────────────
try {
    $pdo->beginTransaction();

    $pdo->prepare('
        INSERT INTO rooms
            (room_number, room_type_id, title, description, price_per_night,
             max_guests, bed_type, size, floor, featured, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, \'AVAILABLE\')
    ')->execute([
        $roomNumber, $typeId, $title, $description, $price,
        $maxGuests, $bedType, $size, $floor, $featured,
    ]);

    $roomId = (int)$pdo->lastInsertId();

    // Insert amenities
    if ($amenityIds) {
        $amStmt = $pdo->prepare(
            'INSERT IGNORE INTO room_amenities (room_id, amenity_id) VALUES (?, ?)'
        );
        foreach ($amenityIds as $amId) {
            $amStmt->execute([$roomId, $amId]);
        }
    }

    // Insert images
    if ($uploadedPaths) {
        $imgStmt = $pdo->prepare(
            'INSERT INTO room_images (room_id, image_path, is_primary) VALUES (?, ?, ?)'
        );
        foreach ($uploadedPaths as $i => $path) {
            $imgStmt->execute([$roomId, $path, $i === 0 ? 1 : 0]);
        }
    }

    log_action(
        (int)user()['id'],
        'CREATE_ROOM',
        'room',
        $roomId,
        "Created room {$roomNumber} – "{$title}" at ₱" . number_format($price, 2) . '/night'
    );

    $pdo->commit();

    flash('success', "Room "{$title}" (#{$roomNumber}) created successfully.");
    header('Location: ' . url('admin/rooms/index.php'));
    exit;

} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();

    // Clean up any uploaded files on failure
    foreach ($uploadedPaths as $path) {
        $full = __DIR__ . '/../../' . $path;
        if (file_exists($full)) unlink($full);
    }

    flash('error', 'Could not create room: ' . $e->getMessage());
    header('Location: ' . url('admin/rooms/add.php'));
    exit;
}
