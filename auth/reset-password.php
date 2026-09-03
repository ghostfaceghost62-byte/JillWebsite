<?php require __DIR__.'/../includes/bootstrap.php';$error='';$token=$_GET['token']??$_POST['token']??'';if($_SERVER['REQUEST_METHOD']==='POST'){check_csrf();$rows=db()->query('SELECT * FROM password_resets WHERE used_at IS NULL AND expires_at>NOW() ORDER BY id DESC')->fetchAll();$reset=null;foreach($rows as $r){if(password_verify($token,$r['token_hash'])){$reset=$r;break;}}if(!$reset)$error='This reset link is invalid or expired.';elseif(!valid_password($_POST['password']??''))$error='Use at least 10 characters with upper- and lower-case letters and a number.';elseif(($_POST['password']??'')!==($_POST['confirm_password']??''))$error='Passwords do not match.';else{db()->prepare('UPDATE users SET password=? WHERE id=?')->execute([password_hash($_POST['password'],PASSWORD_DEFAULT),$reset['user_id']]);db()->prepare('UPDATE password_resets SET used_at=NOW() WHERE id=?')->execute([$reset['id']]);unset($_SESSION['dev_reset_token']);flash('success','Password updated. Please sign in.');header('Location: login.php');exit;}}$pageTitle='Choose a new password';require __DIR__.'/../includes/header.php';?><div class="panel"><h1>Choose a new password</h1><?php if($error):?><div class="alert error"><?=e($error)?></div><?php endif;?><form method="post"><input type="hidden" name="csrf" value="<?=csrf()?>"><input type="hidden" name="token" value="<?=e($token)?>"><label>New password</label><input required type="password" name="password"><label>Confirm password</label><input required type="password" name="confirm_password"><p><button>Update password</button></p></form></div><?php require __DIR__.'/../includes/footer.php'; ?>
<?php
require __DIR__ . '/../includes/bootstrap.php';

$error = '';
$token = $_GET['token'] ?? $_POST['token'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $rows = db()->query('SELECT * FROM password_resets WHERE used_at IS NULL AND expires_at > NOW() ORDER BY id DESC')->fetchAll();
    $reset = null;

    foreach ($rows as $r) {
        if (password_verify($token, $r['token_hash'])) {
            $reset = $r;
            break;
        }
    }

    if (!$reset) {
        $error = 'This password reset link is invalid or has expired.';
    } elseif (!valid_password($_POST['password'] ?? '')) {
        $error = 'Use at least 10 characters with upper- and lower-case letters and a number.';
    } elseif (($_POST['password'] ?? '') !== ($_POST['confirm_password'] ?? '')) {
        $error = 'Passwords do not match.';
    } else {
        db()->prepare('UPDATE users SET password = ? WHERE id = ?')
            ->execute([password_hash($_POST['password'], PASSWORD_DEFAULT), $reset['user_id']]);
        db()->prepare('UPDATE password_resets SET used_at = NOW() WHERE id = ?')
            ->execute([$reset['id']]);
        unset($_SESSION['dev_reset_token']);
        flash('success', 'Your password has been securely updated. Please sign in.');
        header('Location: login.php');
        exit;
    }
}

$pageTitle = 'Choose a New Password';
require __DIR__ . '/../includes/header.php';
?>

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-header">
            <span class="kicker">SECURITY CREDENTIALS</span>
            <h1>New Password</h1>
            <p>Please enter and confirm your new account password.</p>
        </div>

        <?php if ($error): ?>
            <div class="alert error" role="alert"><?=e($error)?></div>
        <?php endif; ?>

        <form method="post" action="<?=url('auth/reset-password.php')?>">
            <input type="hidden" name="csrf" value="<?=csrf()?>">
            <input type="hidden" name="token" value="<?=e($token)?>">

            <div style="margin-bottom: 1.25rem;">
                <label for="reset-password">New Password</label>
                <div class="password-field">
                    <input required id="reset-password" type="password" name="password" autocomplete="new-password">
                    <button class="password-toggle" type="button" data-password-toggle="reset-password" aria-label="Show password" aria-pressed="false">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6Z"></path><circle cx="12" cy="12" r="2.75"></circle></svg>
                    </button>
                </div>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label for="reset-confirm">Confirm New Password</label>
                <div class="password-field">
                    <input required id="reset-confirm" type="password" name="confirm_password" autocomplete="new-password">
                    <button class="password-toggle" type="button" data-password-toggle="reset-confirm" aria-label="Show password" aria-pressed="false">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6Z"></path><circle cx="12" cy="12" r="2.75"></circle></svg>
                    </button>
                </div>
            </div>

            <p style="font-size: 0.78rem; color: var(--text-secondary, #7A807B); margin-bottom: 1.5rem;">
                Use at least 10 characters with upper- and lower-case letters and a number.
            </p>

            <button class="btn btn-gold" type="submit" style="width: 100%; padding: 0.95rem;">Update Password</button>

            <div class="auth-links">
                <a href="<?=url('auth/login.php')?>">&larr; Return to Sign In</a>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
