<?php require __DIR__.'/../includes/bootstrap.php';$notice='';if($_SERVER['REQUEST_METHOD']==='POST'){check_csrf();$s=db()->prepare('SELECT id FROM users WHERE email=? LIMIT 1');$s->execute([trim($_POST['email'])]);if($id=$s->fetchColumn()){$token=bin2hex(random_bytes(32));db()->prepare('INSERT INTO password_resets(user_id,token_hash,expires_at) VALUES(?,?,DATE_ADD(NOW(),INTERVAL 30 MINUTE))')->execute([$id,password_hash($token,PASSWORD_DEFAULT)]);$_SESSION['dev_reset_token']=$token;} $notice='If that address is registered, password-reset instructions are available for this development session.';}$pageTitle='Forgot password';require __DIR__.'/../includes/header.php';?><div class="panel"><h1>Reset your password</h1><?php if($notice):?><div class="alert success"><?=e($notice)?> <?php if(!empty($_SESSION['dev_reset_token'])):?><a href="reset-password.php?token=<?=e($_SESSION['dev_reset_token'])?>">Reset password</a><?php endif;?></div><?php endif;?><form method="post"><input type="hidden" name="csrf" value="<?=csrf()?>"><label>Email</label><input type="email" required name="email"><p><button>Send reset instructions</button></p></form></div><?php require __DIR__.'/../includes/footer.php'; ?>
<?php
require __DIR__ . '/../includes/bootstrap.php';

$notice = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $s = db()->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $s->execute([trim($_POST['email'] ?? '')]);

    if ($id = $s->fetchColumn()) {
        $token = bin2hex(random_bytes(32));
        db()->prepare('INSERT INTO password_resets(user_id, token_hash, expires_at) VALUES(?,?,DATE_ADD(NOW(), INTERVAL 30 MINUTE))')
            ->execute([$id, password_hash($token, PASSWORD_DEFAULT)]);
        $_SESSION['dev_reset_token'] = $token;
    }
    $notice = 'If that email address is registered, password reset instructions have been generated for this session.';
}

$pageTitle = 'Forgot Password';
require __DIR__ . '/../includes/header.php';
?>

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-header">
            <span class="kicker">ACCOUNT RECOVERY</span>
            <h1>Reset Password</h1>
            <p>Enter your registered email address to receive password reset instructions.</p>
        </div>

        <?php if ($notice): ?>
            <div class="alert success" role="status">
                <div>
                    <p style="margin-bottom: 0.5rem;"><?=e($notice)?></p>
                    <?php if (!empty($_SESSION['dev_reset_token'])): ?>
                        <div style="margin-top: 0.75rem;">
                            <a class="btn small btn-gold" href="reset-password.php?token=<?=e($_SESSION['dev_reset_token'])?>">
                                Proceed to Reset Password &rarr;
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <form method="post" action="<?=url('auth/forgot-password.php')?>">
            <input type="hidden" name="csrf" value="<?=csrf()?>">

            <div style="margin-bottom: 1.5rem;">
                <label for="recovery-email">Email Address</label>
                <input required id="recovery-email" type="email" name="email" autocomplete="email" placeholder="your.name@example.com">
            </div>

            <button class="btn btn-gold" type="submit" style="width: 100%; padding: 0.95rem;">Send Reset Instructions</button>

            <div class="auth-links">
                <a href="<?=url('auth/login.php')?>">&larr; Return to Sign In</a>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
