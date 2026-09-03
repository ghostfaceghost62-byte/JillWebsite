<?php
require __DIR__ . '/../includes/bootstrap.php';

$error = '';
$id = (int)($_SESSION['verify_user_id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $code = strtoupper(trim($_POST['code'] ?? ''));
    $s = db()->prepare('SELECT * FROM email_verifications WHERE user_id = ? AND verified_at IS NULL ORDER BY id DESC LIMIT 1');
    $s->execute([$id]);
    $v = $s->fetch();

    if (!$v || $v['attempts'] >= 5 || strtotime($v['expires_at']) < time()) {
        $error = 'This verification code has expired. Please request a new code below.';
    } elseif (strlen($code) !== 15 || !password_verify($code, $v['verification_code_hash'])) {
        db()->prepare('UPDATE email_verifications SET attempts = attempts + 1 WHERE id = ?')->execute([$v['id']]);
        $error = 'Invalid verification code. Please check and try again.';
    } else {
        db()->prepare('UPDATE email_verifications SET verified_at = NOW() WHERE id = ?')->execute([$v['id']]);
        db()->prepare("UPDATE users SET email_verified = 1, status = 'ACTIVE', verified_at = NOW() WHERE id = ?")->execute([$id]);
        unset($_SESSION['dev_verification_code']);
        flash('success', 'Your account has been successfully verified! Please sign in to your account.');
        header('Location: login.php');
        exit;
    }
}

$pageTitle = 'Verify Guest Account';
require __DIR__ . '/../includes/header.php';
?>

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-header">
            <span class="kicker">SECURITY VERIFICATION</span>
            <h1>Account Verification</h1>
            <p>Please enter the 15-character verification code sent to your registered email address.</p>
        </div>

        <?php if ($error): ?>
            <div class="alert error" role="alert"><?=e($error)?></div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['dev_verification_code'])): ?>
            <div class="alert warning" style="border-left-color: var(--accent, #B89650); background-color: var(--surface-muted, #F4EFE6); color: var(--text-primary, #2A2D2A);">
                <div>
                    <strong style="display: block; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--accent, #B89650); margin-bottom: 0.25rem;">Development Code</strong>
                    <code style="font-size: 1.15rem; font-weight: 700; letter-spacing: 0.18em; font-family: monospace;"><?=e($_SESSION['dev_verification_code'])?></code>
                </div>
            </div>
        <?php endif; ?>

        <form method="post" action="<?=url('auth/verify.php')?>" style="margin-bottom: 1.5rem;">
            <input type="hidden" name="csrf" value="<?=csrf()?>">

            <div style="margin-bottom: 1.5rem;">
                <label for="verify-code">15-Character Verification Code</label>
                <input required id="verify-code" maxlength="15" pattern="[A-Za-z0-9]{15}" name="code" autocomplete="one-time-code" placeholder="e.g. 7X9K2M4P6Q8R1T3" style="font-family: monospace; font-size: 1.1rem; letter-spacing: 0.15em; text-transform: uppercase; text-align: center;">
            </div>

            <button class="btn btn-gold" type="submit" style="width: 100%; padding: 0.95rem;">Verify & Activate Account</button>
        </form>

        <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 1.25rem; border-top: 1px solid var(--border-color, #EDE7DD);">
            <form method="post" action="<?=url('auth/resend-code.php')?>" style="width: auto;">
                <input type="hidden" name="csrf" value="<?=csrf()?>">
                <button type="submit" class="link-button" style="font-size: 0.82rem; color: var(--accent, #B89650); font-weight: 600;">Request New Code</button>
            </form>
            <a href="<?=url('auth/login.php')?>" style="font-size: 0.82rem;">Back to Sign In</a>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
