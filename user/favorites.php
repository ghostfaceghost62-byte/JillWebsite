<?php
declare(strict_types=1);
require __DIR__.'/../includes/auth.php';
header('Content-Type: application/json; charset=utf-8');
$pdo = db();
$userId = (int) user()['id'];
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $rows = $pdo->prepare('SELECT room_id FROM user_favorites WHERE user_id=?');
    $rows->execute([$userId]);
    echo json_encode(['favorites' => array_map('intval', array_column($rows->fetchAll(), 'room_id'))]);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['error' => 'Method not allowed']); exit; }
check_csrf();
$roomId = (int) ($_POST['room_id'] ?? 0);
$exists = $pdo->prepare('SELECT 1 FROM rooms WHERE id=?'); $exists->execute([$roomId]);
if (!$roomId || !$exists->fetchColumn()) { http_response_code(422); echo json_encode(['error' => 'Invalid room']); exit; }
$check = $pdo->prepare('SELECT 1 FROM user_favorites WHERE user_id=? AND room_id=?'); $check->execute([$userId, $roomId]);
if ($check->fetchColumn()) { $pdo->prepare('DELETE FROM user_favorites WHERE user_id=? AND room_id=?')->execute([$userId, $roomId]); $saved = false; }
else { $pdo->prepare('INSERT INTO user_favorites(user_id,room_id) VALUES(?,?)')->execute([$userId, $roomId]); $saved = true; }
echo json_encode(['room_id' => $roomId, 'saved' => $saved]);