<?php
declare(strict_types=1);
require_once __DIR__.'/../config/database.php';
ini_set('display_errors', '0');
session_name('hotelreserve_session');
session_set_cookie_params(['httponly'=>true, 'samesite'=>'Lax', 'secure'=>(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')]);
session_start();

const SESSION_TIMEOUT = 1800;
if (!empty($_SESSION['last_activity']) && time() - (int) $_SESSION['last_activity'] > SESSION_TIMEOUT) {
    $_SESSION = [];
    session_destroy();
    session_start();
    $_SESSION['flash'] = ['warning', 'Your session expired. Please sign in again.'];
}
$_SESSION['last_activity'] = time();

function e(?string $value): string { return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function app_root(): string {
    static $root;
    if ($root !== null) {
        return $root;
    }

    $documentRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '');
    $applicationRoot = realpath(__DIR__.'/..');
    if ($documentRoot && $applicationRoot) {
        $documentRoot = str_replace('\\', '/', $documentRoot);
        $applicationRoot = str_replace('\\', '/', $applicationRoot);
        if (str_starts_with(strtolower($applicationRoot), strtolower(rtrim($documentRoot, '/').'/'))) {
            return $root = rtrim(substr($applicationRoot, strlen(rtrim($documentRoot, '/'))), '/');
        }
    }

    return $root = rtrim(APP_ROOT, '/');
}
function url(string $path=''): string { return app_root() . '/' . ltrim($path, '/'); }
function csrf(): string { $_SESSION['csrf'] ??= bin2hex(random_bytes(32)); return $_SESSION['csrf']; }
function check_csrf(): void { if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) { http_response_code(419); exit('Invalid request token. Please go back and try again.'); } }
function flash(string $type, string $message): void { $_SESSION['flash'] = [$type, $message]; }
function show_flash(): void { if (!empty($_SESSION['flash'])) { [$t,$m]=$_SESSION['flash']; unset($_SESSION['flash']); echo '<div class="alert '.$t.'">'.e($m).'</div>'; } }
function user(): ?array { return $_SESSION['user'] ?? null; }
function require_login(): void { if (!user() || empty($_SESSION['authenticated'])) { flash('error','Please sign in to continue.'); header('Location: '.url('auth/login.php')); exit; } }
function require_admin(): void { require_login(); if (user()['role'] !== 'ADMIN') { http_response_code(403); require __DIR__.'/../403.php'; exit; } }
function log_action(?int $userId, string $action, string $entity, ?int $entityId, string $description): void { $s=db()->prepare('INSERT INTO activity_logs (user_id,action,entity_type,entity_id,description,ip_address) VALUES (?,?,?,?,?,?)'); $s->execute([$userId,$action,$entity,$entityId,$description,$_SERVER['REMOTE_ADDR'] ?? '']); }
function notify(int $userId, string $title, string $message, string $type='SYSTEM'): void { db()->prepare('INSERT INTO notifications (user_id,title,message,type) VALUES (?,?,?,?)')->execute([$userId,$title,$message,$type]); }
function active_statuses(): string { return "'PENDING','CONFIRMED','CHECKED_IN'"; }
function reservation_conflict_sql(): string { return "status IN ('PENDING','CONFIRMED','CHECKED_IN') AND check_in < ? AND check_out > ?"; }
function verification_code(): string { $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; $code = ''; for ($i = 0; $i < 15; $i++) { $code .= $alphabet[random_int(0, strlen($alphabet) - 1)]; } return $code; }
function valid_password(string $password): bool { return strlen($password) >= 10 && preg_match('/[A-Z]/', $password) && preg_match('/[a-z]/', $password) && preg_match('/\d/', $password); }

$protectedPaths = ['/rooms/index.php', '/rooms/details.php', '/amenities.php', '/about.php'];
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
if (in_array($requestPath, array_map(static fn(string $path): string => app_root().$path, $protectedPaths), true)) {
    require_login();
}
