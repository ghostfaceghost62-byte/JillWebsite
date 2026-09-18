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
            <span class="kicker">LIDO DE PARIS MEMBERSHIP</span>
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

            <div class="password-strength-container" style="margin-bottom: 1.5rem;">
                <div class="password-strength-bar" style="display:flex; height: 4px; gap: 4px; margin-bottom: 0.4rem; border-radius: 4px; overflow: hidden; background: var(--surface-muted, #EDE7DD);">
                    <div id="ps-1" style="flex:1; background: transparent; transition: background 0.3s;"></div>
                    <div id="ps-2" style="flex:1; background: transparent; transition: background 0.3s;"></div>
                    <div id="ps-3" style="flex:1; background: transparent; transition: background 0.3s;"></div>
                    <div id="ps-4" style="flex:1; background: transparent; transition: background 0.3s;"></div>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span id="ps-label" style="font-size: 0.75rem; font-weight: 600; color: var(--text-secondary, #7A807B); text-transform: uppercase; letter-spacing: 0.05em;">Strength</span>
                    <span style="font-size: 0.72rem; color: var(--text-secondary, #7A807B);">Min. 10 chars, uppercase, lowercase, number</span>
                </div>
            </div>

            <script>
            document.addEventListener('DOMContentLoaded', () => {
                const passInput = document.getElementById('signup-password');
                const bars = [
                    document.getElementById('ps-1'),
                    document.getElementById('ps-2'),
                    document.getElementById('ps-3'),
                    document.getElementById('ps-4')
                ];
                const label = document.getElementById('ps-label');

                passInput.addEventListener('input', () => {
                    const val = passInput.value;
                    let strength = 0;
                    
                    if (val.length > 0) strength++;
                    if (val.length >= 10) strength++;
                    if (/[A-Z]/.test(val) && /[a-z]/.test(val) && /[0-9]/.test(val)) strength++;
                    if (/[^A-Za-z0-9]/.test(val) && val.length >= 12) strength++;

                    bars.forEach((b, i) => {
                        b.style.background = 'transparent';
                        if (i < strength) {
                            if (strength === 1) b.style.background = '#EF4444'; // Red (Weak)
                            if (strength === 2) b.style.background = '#F59E0B'; // Orange (Fair)
                            if (strength === 3) b.style.background = '#10B981'; // Green (Good)
                            if (strength === 4) b.style.background = '#059669'; // Dark Green (Strong)
                        }
                    });

                    const labels = ['Strength', 'Weak', 'Fair', 'Good', 'Strong'];
                    label.textContent = labels[strength];
                    
                    if(strength === 0) label.style.color = 'var(--text-secondary, #7A807B)';
                    else if(strength === 1) label.style.color = '#EF4444';
                    else if(strength === 2) label.style.color = '#F59E0B';
                    else label.style.color = '#10B981';
                });
            });
            </script>

            <button class="btn btn-gold" type="submit" style="width: 100%; padding: 0.95rem;">Complete Registration</button>

            <div class="auth-links">
                <span>Already registered? <a href="<?=url('auth/login.php')?>"><strong>Sign in here</strong></a></span>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
