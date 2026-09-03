<?php require __DIR__.'/../includes/bootstrap.php'; if(user()){header('Location: '.url());exit;} $errors=[]; if($_SERVER['REQUEST_METHOD']==='POST'){ check_csrf(); $d=array_map('trim',$_POST); if(!$d['first_name']||!$d['last_name']||!filter_var($d['email'],FILTER_VALIDATE_EMAIL)||!$d['phone'])$errors[]='Complete the required fields using a valid email address.'; if(!valid_password($d['password']??''))$errors[]='Use at least 10 characters with upper- and lower-case letters and a number.'; if(($d['password']??'')!==($d['confirm_password']??''))$errors[]='Passwords do not match.'; if(!$errors){ try { $pdo=db(); $pdo->beginTransaction(); $pdo->prepare("INSERT INTO users(first_name,last_name,email,phone,address,password,role,status,email_verified) VALUES(?,?,?,?,?,?,'CUSTOMER','PENDING_VERIFICATION',0)")->execute([$d['first_name'],$d['last_name'],$d['email'],$d['phone'],$d['address']??'',password_hash($d['password'],PASSWORD_DEFAULT)]); $id=(int)$pdo->lastInsertId(); $code=verification_code(); $pdo->prepare('INSERT INTO email_verifications(user_id,verification_code_hash,expires_at) VALUES(?,?,DATE_ADD(NOW(),INTERVAL 15 MINUTE))')->execute([$id,password_hash($code,PASSWORD_DEFAULT)]); $pdo->commit(); $_SESSION['verify_user_id']=$id; $_SESSION['dev_verification_code']=$code; flash('success','Account created. Enter the 15-character verification code.'); header('Location: verify.php'); exit; } catch(Throwable $e) { if(isset($pdo)&&$pdo->inTransaction())$pdo->rollBack(); $errors[]='That email is already registered.'; } }} $pageTitle='Create account'; require __DIR__.'/../includes/header.php'; ?><div class="panel"><h1>Create your guest account</h1><?php foreach($errors as $x):?><div class="alert error"><?=e($x)?></div><?php endforeach;?><form method="post"><input type="hidden" name="csrf" value="<?=csrf()?>"><div class="form-grid"><div><label>First name</label><input required name="first_name"></div><div><label>Last name</label><input required name="last_name"></div><div><label>Email</label><input required type="email" name="email"></div><div><label>Phone</label><input required name="phone"></div></div><label>Address</label><textarea name="address"></textarea><div class="form-grid"><div><label for="signup-password">Password</label><div class="password-field"><input required id="signup-password" type="password" name="password" autocomplete="new-password"><button class="password-toggle" type="button" data-password-toggle="signup-password" aria-label="Show password" aria-pressed="false"><svg class="password-toggle-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path><circle cx="12" cy="12" r="2.75"></circle></svg></button></div></div><div><label for="confirm-password">Confirm password</label><div class="password-field"><input required id="confirm-password" type="password" name="confirm_password" autocomplete="new-password"><button class="password-toggle" type="button" data-password-toggle="confirm-password" aria-label="Show password" aria-pressed="false"><svg class="password-toggle-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path><circle cx="12" cy="12" r="2.75"></circle></svg></button></div></div></div><p><button type="submit">Create account</button></p></form></div><?php require __DIR__.'/../includes/footer.php'; ?>
<?php
require __DIR__ . '/../includes/bootstrap.php';

if (user()) {
    header('Location: ' . url());
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $d = array_map('trim', $_POST);

    if (!$d['first_name'] || !$d['last_name'] || !filter_var($d['email'], FILTER_VALIDATE_EMAIL) || !$d['phone']) {
        $errors[] = 'Please complete all required fields with a valid email address.';
    }
    if (!valid_password($d['password'] ?? '')) {
        $errors[] = 'Password must be at least 10 characters and include uppercase, lowercase, and numeric characters.';
    }
    if (($d['password'] ?? '') !== ($d['confirm_password'] ?? '')) {
        $errors[] = 'Passwords do not match.';
    }

    if (!$errors) {
        try {
            $pdo = db();
            $pdo->beginTransaction();
            $pdo->prepare("INSERT INTO users(first_name, last_name, email, phone, address, password, role, status, email_verified) VALUES(?,?,?,?,?,?,'CUSTOMER','PENDING_VERIFICATION',0)")
                ->execute([
                    $d['first_name'],
                    $d['last_name'],
                    $d['email'],
                    $d['phone'],
                    $d['address'] ?? '',
                    password_hash($d['password'], PASSWORD_DEFAULT)
                ]);

            $id = (int)$pdo->lastInsertId();
            $code = verification_code();

            $pdo->prepare('INSERT INTO email_verifications(user_id, verification_code_hash, expires_at) VALUES(?,?,DATE_ADD(NOW(), INTERVAL 15 MINUTE))')
                ->execute([$id, password_hash($code, PASSWORD_DEFAULT)]);

            $pdo->commit();

            $_SESSION['verify_user_id'] = $id;
            $_SESSION['dev_verification_code'] = $code;
            flash('success', 'Account created successfully! Please enter your 15-character verification code.');
            header('Location: verify.php');
            exit;
        } catch (Throwable $e) {
            if (isset($pdo) && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $errors[] = 'An account with that email address already exists.';
        }
    }
}

$pageTitle = 'Create Guest Account';
require __DIR__ . '/../includes/header.php';
?>

<div class="auth-wrapper">
    <div class="auth-card" style="max-width: 620px;">
        <div class="auth-header">
            <span class="kicker">JILL HOTEL MEMBERSHIP</span>
            <h1>Create Guest Account</h1>
            <p>Join our private guest registry for seamless bookings and exclusive privileges.</p>
        </div>

        <?php foreach ($errors as $x): ?>
            <div class="alert error" role="alert"><?=e($x)?></div>
        <?php endforeach; ?>

        <form method="post" action="<?=url('auth/register.php')?>">
            <input type="hidden" name="csrf" value="<?=csrf()?>">

            <div class="form-grid">
                <div>
                    <label for="reg-first">First Name *</label>
                    <input required id="reg-first" name="first_name" autocomplete="given-name" value="<?=e($_POST['first_name'] ?? '')?>">
                </div>
                <div>
                    <label for="reg-last">Last Name *</label>
                    <input required id="reg-last" name="last_name" autocomplete="family-name" value="<?=e($_POST['last_name'] ?? '')?>">
                </div>
            </div>

            <div class="form-grid">
                <div>
                    <label for="reg-email">Email Address *</label>
                    <input required id="reg-email" type="email" name="email" autocomplete="email" value="<?=e($_POST['email'] ?? '')?>">
                </div>
                <div>
                    <label for="reg-phone">Contact Number *</label>
                    <input required id="reg-phone" name="phone" autocomplete="tel" placeholder="+63 900 000 0000" value="<?=e($_POST['phone'] ?? '')?>">
                </div>
            </div>

            <div style="margin-bottom: 1.25rem;">
                <label for="reg-address">Mailing / Billing Address</label>
                <textarea id="reg-address" name="address" autocomplete="street-address" placeholder="City, province, postal code" style="min-height: 75px;"><?=e($_POST['address'] ?? '')?></textarea>
            </div>

            <div class="form-grid">
                <div>
                    <label for="signup-password">Password *</label>
                    <div class="password-field">
                        <input required id="signup-password" type="password" name="password" autocomplete="new-password">
                        <button class="password-toggle" type="button" data-password-toggle="signup-password" aria-label="Show password" aria-pressed="false">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6Z"></path><circle cx="12" cy="12" r="2.75"></circle></svg>
                        </button>
                    </div>
                </div>
                <div>
                    <label for="confirm-password">Confirm Password *</label>
                    <div class="password-field">
                        <input required id="confirm-password" type="password" name="confirm_password" autocomplete="new-password">
                        <button class="password-toggle" type="button" data-password-toggle="confirm-password" aria-label="Show password" aria-pressed="false">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6Z"></path><circle cx="12" cy="12" r="2.75"></circle></svg>
                        </button>
                    </div>
                </div>
            </div>

            <p style="font-size: 0.78rem; color: var(--text-secondary, #7A807B); margin-bottom: 1.5rem;">
                Minimum 10 characters with upper and lower case letters, and at least one digit.
            </p>

            <button class="btn btn-gold" type="submit" style="width: 100%; padding: 0.95rem;">Complete Registration</button>

            <div class="auth-links">
                <span>Already registered? <a href="<?=url('auth/login.php')?>"><strong>Sign in here</strong></a></span>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
