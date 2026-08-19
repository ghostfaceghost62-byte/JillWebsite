<?php
declare(strict_types=1);
require __DIR__.'/../includes/auth.php';
header('Content-Type: application/json; charset=utf-8');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['error' => 'Method not allowed']); exit; }
check_csrf();
$theme = $_POST['theme'] ?? '';
if (!in_array($theme, ['light', 'dark'], true)) { http_response_code(422); echo json_encode(['error' => 'Invalid theme']); exit; }
db()->prepare('INSERT INTO user_preferences(user_id,theme) VALUES(?,?) ON DUPLICATE KEY UPDATE theme=VALUES(theme)')->execute([user()['id'], $theme]);
echo json_encode(['theme' => $theme]);