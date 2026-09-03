<?php
require __DIR__ . '/../includes/auth.php';

$pdo = db();
$s = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$s->execute([user()['id']]);
$u = $s->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $first = trim($_POST['first_name'] ?? '');
    $last = trim($_POST['last_name'] ?? '');

    if ($first && $last) {
        $pdo->prepare('UPDATE users SET first_name = ?, last_name = ?, phone = ?, address = ? WHERE id = ?')
            ->execute([$first, $last, trim($_POST['phone'] ?? ''), trim($_POST['address'] ?? ''), $u['id']]);
        $_SESSION['user']['name'] = $first . ' ' . $last;
        flash('success', 'Your personal details have been updated.');
        header('Location: profile.php');
        exit;
    }
    flash('error', 'First name and last name are required.');
}

$pageTitle = 'My Profile & Preferences';
require __DIR__ . '/../includes/header.php';
?>

<div style="margin-bottom: 2rem;">
    <span class="kicker">GUEST REGISTRY</span>
    <h1 style="margin-bottom: 0.35rem;">Guest Profile</h1>
    <p style="color: var(--text-secondary, #5C625D);">Manage your contact information and preferences for future stays.</p>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; align-items: flex-start;">
    <div class="panel">
        <h2 style="font-size: 1.45rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color, #E8E2D7); padding-bottom: 0.75rem;">
            Personal Information
        </h2>

        <form method="post" action="<?=url('customer/profile.php')?>">
            <input type="hidden" name="csrf" value="<?=csrf()?>">

            <div class="form-grid">
                <div>
                    <label for="prof-first">First Name *</label>
                    <input required id="prof-first" name="first_name" autocomplete="given-name" value="<?=e($u['first_name'])?>">
                </div>
                <div>
                    <label for="prof-last">Last Name *</label>
                    <input required id="prof-last" name="last_name" autocomplete="family-name" value="<?=e($u['last_name'])?>">
                </div>
            </div>

            <div class="form-grid">
                <div>
                    <label for="prof-phone">Contact Number</label>
                    <input id="prof-phone" name="phone" autocomplete="tel" value="<?=e($u['phone'])?>">
                </div>
                <div>
                    <label for="prof-email">Email Address (Read-Only)</label>
                    <input disabled id="prof-email" value="<?=e($u['email'])?>">
                </div>
            </div>

            <div style="margin-bottom: 1.75rem;">
                <label for="prof-address">Mailing / Billing Address</label>
                <textarea id="prof-address" name="address" autocomplete="street-address" style="min-height: 80px;"><?=e($u['address'])?></textarea>
            </div>

            <button class="btn btn-gold" type="submit">Save Profile Changes</button>
        </form>
    </div>

    <aside class="panel" style="background: var(--surface-muted, #F4EFE6); border: none;">
        <h3 style="font-size: 1.2rem; margin-bottom: 1.25rem;">Account Overview</h3>
        <div style="display: flex; flex-direction: column; gap: 1rem; font-size: 0.88rem;">
            <div>
                <span style="font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-secondary, #7A807B); display: block;">Membership Role</span>
                <strong><?=e($u['role'])?></strong>
            </div>
            <div>
                <span style="font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-secondary, #7A807B); display: block;">Account Status</span>
                <span class="badge success"><?=e($u['status'])?></span>
            </div>
            <div>
                <span style="font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-secondary, #7A807B); display: block;">Email Verification</span>
                <span class="badge success">Verified &check;</span>
            </div>
            <div>
                <span style="font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-secondary, #7A807B); display: block;">Registered Date</span>
                <span><?=date('F d, Y', strtotime($u['created_at']))?></span>
            </div>
        </div>
    </aside>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
