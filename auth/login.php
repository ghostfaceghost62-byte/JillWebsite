<?php
require __DIR__ . '/../includes/bootstrap.php';

if (user()) {
    header('Location: ' . url());
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $email = trim($_POST['email'] ?? '');
    $pdo = db();
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';

    $blocked = $pdo->prepare('SELECT COUNT(*) FROM login_attempts WHERE ip_address = ? AND attempted_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)');
    $blocked->execute([$ip]);

    if ($blocked->fetchColumn() >= 5) {
        $error = 'Too many sign-in attempts. Please try again in 15 minutes.';
    } else {
        $s = $pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $s->execute([$email]);
        $u = $s->fetch();
        $role = strtoupper((string)($u['role'] ?? ''));
        $status = strtoupper((string)($u['status'] ?? ''));

        if ($u && password_verify($_POST['password'] ?? '', $u['password']) && $status === 'ACTIVE' && (int)$u['email_verified'] === 1) {
            session_regenerate_id(true);
            $_SESSION['authenticated'] = true;
            $_SESSION['user'] = [
                'id' => (int)$u['id'],
                'name' => $u['first_name'] . ' ' . $u['last_name'],
                'role' => $role
            ];
            $pdo->prepare('UPDATE users SET last_login = NOW() WHERE id = ?')->execute([$u['id']]);
            $pdo->prepare('DELETE FROM login_attempts WHERE email = ? OR ip_address = ?')->execute([$email, $ip]);
            log_action((int)$u['id'], 'LOGIN', 'user', (int)$u['id'], 'User logged in');
            header('Location: ' . url($role === 'ADMIN' ? 'admin/index.php' : 'customer/dashboard.php'));
            exit;
        }

        $pdo->prepare('INSERT INTO login_attempts(email, ip_address) VALUES(?,?)')->execute([$email, $ip]);
        $error = ($u && $status === 'PENDING_VERIFICATION')
            ? 'Your account has not been verified yet. Use the verification link below.'
            : 'Invalid email address or password.';
    }
}

$pageTitle = 'Sign In to Your Account';
require __DIR__ . '/../includes/header.php';
?>

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-header">
            <span class="kicker">LIDO DE PARIS GUEST ACCESS</span>
            <h1>Welcome Back</h1>
            <p>Sign in to manage your reservations and preferences.</p>
        </div>

        <?php if ($error): ?>
            <div class="alert error" role="alert"><?=e($error)?></div>
        <?php endif; ?>

        <form method="post" action="<?=url('auth/login.php')?>">
            <input type="hidden" name="csrf" value="<?=csrf()?>">

            <div style="margin-bottom: 1.25rem;">
                <label for="login-email">Email Address</label>
                <input required id="login-email" type="email" name="email" autocomplete="username" placeholder="your.name@example.com" value="<?=e($_POST['email'] ?? '')?>">
            </div>

            <div style="margin-bottom: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: baseline;">
                    <label for="login-password">Password</label>
                    <a href="<?=url('auth/forgot-password.php')?>" style="font-size: 0.75rem; color: var(--accent, #B89650); font-weight: 600;">Forgot?</a>
                </div>
                <div class="password-field">
                    <input required id="login-password" type="password" name="password" autocomplete="current-password" placeholder="Enter your password">
                    <button class="password-toggle" type="button" data-password-toggle="login-password" aria-label="Show password" aria-pressed="false">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path><circle cx="12" cy="12" r="2.75"></circle></svg>
                    </button>
                </div>
            </div>

            <button class="btn btn-gold" type="submit" style="width: 100%; padding: 0.95rem;">Sign In to Account</button>

            <div class="auth-links">
                <span>New guest? <a href="<?=url('auth/register.php')?>"><strong>Create an account</strong></a></span>
                &middot;
                <a href="<?=url('auth/verify.php')?>">Verify account</a>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
