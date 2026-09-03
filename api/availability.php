<?php
require __DIR__ . '/../includes/bootstrap.php';
header('Content-Type: application/json');

$roomId = (int)($_GET['room_id'] ?? 0);
if ($roomId <= 0) {
    echo json_encode([]);
    exit;
}

$pdo = db();
$stmt = $pdo->prepare("
    SELECT check_in, check_out 
    FROM reservations 
    WHERE room_id = ? 
      AND status IN ('PENDING','CONFIRMED','CHECKED_IN')
      AND check_out > CURDATE()
");
$stmt->execute([$roomId]);
$bookings = $stmt->fetchAll();

$blockedDates = [];
foreach ($bookings as $b) {
    $current = strtotime($b['check_in']);
    $end = strtotime($b['check_out']);
    
    // We block all days from check_in up to (but not including) check_out, 
    // because checkout day is available for checkin.
    while ($current < $end) {
        $blockedDates[] = date('Y-m-d', $current);
        $current = strtotime('+1 day', $current);
    }
}

// Return unique blocked dates
echo json_encode(array_values(array_unique($blockedDates)));
