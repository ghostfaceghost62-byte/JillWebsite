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
    if (empty($_POST['agree_terms'])) {
        $errors[] = 'You must agree to the Terms & Conditions to create an account.';
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

            <div style="margin-bottom: 1.5rem;">
                <label style="display: flex; align-items: flex-start; gap: 0.65rem; cursor: pointer; font-size: 0.85rem; line-height: 1.5; color: var(--text-secondary, #5C625D);">
                    <input type="checkbox" name="agree_terms" value="1" id="agree-terms" required style="margin-top: 0.2rem; accent-color: var(--accent, #B89650); width: 18px; height: 18px; flex-shrink: 0; cursor: pointer;">
                    <span>I have read and agree to the <a href="<?=url('terms.php')?>" target="_blank" style="color: var(--accent, #B89650); font-weight: 600; text-decoration: underline;">Terms &amp; Conditions</a> of Lido De Paris Hotel &amp; Entertainment Center, Inc.</span>
                    <span>I have read and agree to the <a href="#" onclick="document.getElementById('terms-modal').style.display='flex'; return false;" style="color: var(--accent, #B89650); font-weight: 600; text-decoration: underline;">Terms &amp; Conditions</a> of Lido De Paris Hotel &amp; Entertainment Center, Inc.</span>
                </label>
            </div>

            <button class="btn btn-gold" type="submit" style="width: 100%; padding: 0.95rem;">Complete Registration</button>

            <div class="auth-links">
                <span>Already registered? <a href="<?=url('auth/login.php')?>"><strong>Sign in here</strong></a></span>
            </div>
        </form>
    </div>
</div>

<!-- Terms & Conditions Modal -->
<div id="terms-modal" style="display:none; position:fixed; inset:0; z-index:9999; align-items:center; justify-content:center; background:rgba(0,0,0,0.5); backdrop-filter:blur(2px); padding:1rem;">
    <div style="background:var(--surface, #FFFFFF); border-radius:10px; width:100%; max-width:640px; max-height:85vh; display:flex; flex-direction:column; box-shadow:0 25px 60px rgba(0,0,0,0.25); overflow:hidden;">
        <!-- Modal Header -->
        <div style="display:flex; justify-content:space-between; align-items:center; padding:1.5rem 2rem 1rem; border-bottom:1px solid var(--border-color, #E8E2D7); flex-shrink:0;">
            <div>
                <h2 style="font-family:'Playfair Display', Georgia, serif; font-size:1.5rem; margin:0; color:var(--text-primary, #1C211D);">Terms and Conditions</h2>
                <p style="font-size:0.78rem; color:var(--text-secondary, #7A807B); margin:0.25rem 0 0;">Last updated: September 19, 2026</p>
            </div>
            <button onclick="document.getElementById('terms-modal').style.display='none'" style="background:none; border:none; cursor:pointer; font-size:1.6rem; color:var(--text-secondary, #7A807B); padding:0.25rem; line-height:1;" aria-label="Close">&times;</button>
        </div>
        <!-- Modal Body (scrollable) -->
        <div style="overflow-y:auto; padding:1.5rem 2rem; font-size:0.88rem; line-height:1.75; color:var(--text-secondary, #5C625D); flex:1;">

            <h3 style="font-family:'Playfair Display', Georgia, serif; color:var(--text-primary, #1C211D); margin:0 0 0.6rem; font-size:1.05rem;">1. Acceptance of Terms</h3>
            <p>By creating an account, making a reservation, or using any services provided by Lido De Paris Hotel &amp; Entertainment Center, Inc. ("the Hotel"), you agree to be bound by these Terms and Conditions. If you do not agree, please refrain from using our services.</p>

            <h3 style="font-family:'Playfair Display', Georgia, serif; color:var(--text-primary, #1C211D); margin:1.25rem 0 0.6rem; font-size:1.05rem;">2. Reservation &amp; Booking Policy</h3>
            <ul style="padding-left:1.5rem; margin-bottom:0.75rem;">
                <li>All reservations are subject to room availability and confirmation by the Hotel.</li>
                <li>Guests must provide accurate personal information during registration and booking.</li>
                <li>Room rates are quoted in Philippine Pesos (₱) unless otherwise indicated.</li>
                <li>The Hotel reserves the right to cancel or modify bookings due to unforeseen circumstances, in which case a full refund or rebooking will be offered.</li>
            </ul>

            <h3 style="font-family:'Playfair Display', Georgia, serif; color:var(--text-primary, #1C211D); margin:1.25rem 0 0.6rem; font-size:1.05rem;">3. Check-In &amp; Check-Out</h3>
            <ul style="padding-left:1.5rem; margin-bottom:0.75rem;">
                <li><strong>Check-in time:</strong> 2:00 PM</li>
                <li><strong>Check-out time:</strong> 12:00 PM (noon)</li>
                <li>Early check-in and late check-out are subject to availability and may incur additional charges.</li>
                <li>Guests must present a valid government-issued ID upon check-in.</li>
            </ul>

            <h3 style="font-family:'Playfair Display', Georgia, serif; color:var(--text-primary, #1C211D); margin:1.25rem 0 0.6rem; font-size:1.05rem;">4. Payment Terms</h3>
            <ul style="padding-left:1.5rem; margin-bottom:0.75rem;">
                <li>Payment may be made via cash, bank transfer, GCash, or other approved methods.</li>
                <li>For online payments, guests must provide a valid reference code and upload a screenshot or proof of payment for verification.</li>
                <li>Payment proof is subject to review and approval by the Hotel's management before the reservation is confirmed.</li>
                <li>Fraudulent payment claims may result in immediate cancellation and account suspension.</li>
            </ul>

            <h3 style="font-family:'Playfair Display', Georgia, serif; color:var(--text-primary, #1C211D); margin:1.25rem 0 0.6rem; font-size:1.05rem;">5. Cancellation &amp; Refund Policy</h3>
            <ul style="padding-left:1.5rem; margin-bottom:0.75rem;">
                <li>Cancellations made <strong>48 hours or more</strong> before the check-in date may be eligible for a full refund.</li>
                <li>Cancellations made <strong>within 48 hours</strong> of the check-in date may incur a cancellation fee equivalent to one night's stay.</li>
                <li>No-shows will be charged the full booking amount.</li>
                <li>Refund processing may take 5–10 business days depending on the payment method.</li>
            </ul>

            <h3 style="font-family:'Playfair Display', Georgia, serif; color:var(--text-primary, #1C211D); margin:1.25rem 0 0.6rem; font-size:1.05rem;">6. Guest Conduct</h3>
            <ul style="padding-left:1.5rem; margin-bottom:0.75rem;">
                <li>Guests are expected to maintain respectful behavior and observe decorum within the Hotel premises.</li>
                <li>Smoking is only permitted in designated smoking areas.</li>
                <li>The Hotel is not responsible for the loss or damage of personal belongings left unattended.</li>
                <li>Any damage to Hotel property caused by a guest will be charged to the guest's account.</li>
                <li>The Hotel reserves the right to refuse or revoke accommodation to anyone who violates these terms or engages in disruptive behavior.</li>
            </ul>

            <h3 style="font-family:'Playfair Display', Georgia, serif; color:var(--text-primary, #1C211D); margin:1.25rem 0 0.6rem; font-size:1.05rem;">7. Privacy &amp; Data Protection</h3>
            <ul style="padding-left:1.5rem; margin-bottom:0.75rem;">
                <li>Personal information collected during registration and booking is used solely for providing Hotel services and improving guest experience.</li>
                <li>We comply with the <strong>Data Privacy Act of 2012</strong> (Republic Act No. 10173) of the Philippines.</li>
                <li>Your data will not be shared with third parties without your explicit consent, except as required by law.</li>
                <li>Guests have the right to request access to, correction of, or deletion of their personal data at any time by contacting the Hotel.</li>
            </ul>

            <h3 style="font-family:'Playfair Display', Georgia, serif; color:var(--text-primary, #1C211D); margin:1.25rem 0 0.6rem; font-size:1.05rem;">8. Account Security</h3>
            <ul style="padding-left:1.5rem; margin-bottom:0.75rem;">
                <li>Guests are responsible for maintaining the confidentiality of their account credentials.</li>
                <li>The Hotel is not liable for unauthorized access resulting from the guest's failure to protect their account information.</li>
                <li>Any suspicious activity should be reported immediately to the Hotel management.</li>
            </ul>

            <h3 style="font-family:'Playfair Display', Georgia, serif; color:var(--text-primary, #1C211D); margin:1.25rem 0 0.6rem; font-size:1.05rem;">9. Limitation of Liability</h3>
            <p>The Hotel shall not be held liable for any indirect, incidental, or consequential damages arising from the use of our services, website, or facilities, except where required by Philippine law.</p>

            <h3 style="font-family:'Playfair Display', Georgia, serif; color:var(--text-primary, #1C211D); margin:1.25rem 0 0.6rem; font-size:1.05rem;">10. Contact Information</h3>
            <div style="background:var(--surface-alt, #FAF8F5); border:1px solid var(--border-color, #E8E2D7); border-radius:6px; padding:1rem; margin-top:0.5rem;">
                <strong style="color:var(--text-primary, #1C211D);">Lido De Paris Hotel &amp; Entertainment Center, Inc.</strong><br>
                1036 Ongpin Street, Sta. Cruz, Manila, Philippines<br>
                Tel: +63 2 8708 8888 &nbsp;|&nbsp; Mobile: +63 917 894 6943<br>
                Email: inquiry@lidodeparishotel.com
            </div>
        </div>
        <!-- Modal Footer -->
        <div style="padding:1rem 2rem 1.5rem; border-top:1px solid var(--border-color, #E8E2D7); text-align:right; flex-shrink:0;">
            <button type="button" onclick="document.getElementById('agree-terms').checked=true; document.getElementById('terms-modal').style.display='none';" class="btn btn-gold" style="padding:0.75rem 2.5rem; font-size:0.9rem;">I Agree</button>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
