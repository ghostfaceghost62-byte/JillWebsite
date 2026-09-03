<?php
require __DIR__ . '/../../includes/bootstrap.php';
require_admin();

header('Content-Type: application/json');

$pdo = db();
$type = $_GET['type'] ?? 'events';

if ($type === 'resources') {
    // Return rooms
    $stmt = $pdo->query('SELECT id, title, status FROM rooms ORDER BY id');
    $resources = [];
    while ($row = $stmt->fetch()) {
        $resources[] = [
            'id' => $row['id'],
            'title' => $row['title'] . ' (' . $row['status'] . ')'
        ];
    }
    echo json_encode($resources);
    exit;
}

if ($type === 'events') {
    $start = $_GET['start'] ?? date('Y-m-d', strtotime('-1 month'));
    $end = $_GET['end'] ?? date('Y-m-d', strtotime('+2 months'));
    
    $stmt = $pdo->prepare("
        SELECT r.id, r.reservation_number, r.room_id, r.check_in, r.check_out, r.status,
               u.first_name, u.last_name 
        FROM reservations r
        JOIN users u ON u.id = r.user_id
        WHERE r.check_in < ? AND r.check_out > ?
    ");
    $stmt->execute([$end, $start]);
    $reservations = $stmt->fetchAll();
    
    $events = [];
    foreach ($reservations as $res) {
        $color = '#3788d8'; // default blue
        if ($res['status'] === 'CANCELLED') $color = '#e74c3c';
        if ($res['status'] === 'CHECKED_IN') $color = '#27ae60';
        if ($res['status'] === 'CHECKED_OUT') $color = '#95a5a6';
        if ($res['status'] === 'CONFIRMED') $color = '#f39c12';
        
        $events[] = [
            'id' => $res['id'],
            'resourceId' => $res['room_id'],
            'title' => $res['first_name'] . ' ' . substr($res['last_name'], 0, 1) . '. #' . substr($res['reservation_number'], -4),
            'start' => $res['check_in'],
            'end' => $res['check_out'],
            'color' => $color,
            'url' => url('admin/reservations/view.php?id=' . $res['id'])
        ];
    }
    
    echo json_encode($events);
    exit;
}

