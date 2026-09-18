<?php
require __DIR__ . '/../includes/auth.php';

$pdo = db();
$reservationId = (int)($_GET['id'] ?? $_POST['reservation_id'] ?? 0);

if (!$reservationId) {
    flash('error', 'Invalid reservation selected.');
    header('Location: ' . url('customer/reservations.php'));
    exit;
}

// Fetch reservation and payment record
$stmt = $pdo->prepare('
    SELECT r.*, rm.title AS room_title, rm.room_number, p.id AS payment_id, p.payment_status, p.payment_method, p.online_ref_code, p.payment_proof_img, p.admin_notes
    FROM reservations r
    JOIN rooms rm ON rm.id = r.room_id
    LEFT JOIN payments p ON p.reservation_id = r.id
    WHERE r.id = ? AND r.user_id = ?
');
$stmt->execute([$reservationId, user()['id']]);
$reservation = $stmt->fetch();

if (!$reservation) {
    flash('error', 'Reservation not found or access denied.');
    header('Location: ' . url('customer/reservations.php'));
    exit;
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf'] ?? '';
    if (!hash_equals(csrf(), $csrf)) {
        flash('error', 'Session expired. Please try again.');
        header('Location: ' . url('customer/pay.php?id=' . $reservationId));
        exit;
    }

    $paymentMethod = trim($_POST['payment_method'] ?? 'GCASH');
    $refCode = trim($_POST['online_ref_code'] ?? '');
    
    if (empty($refCode)) {
        flash('error', 'Please enter your payment Reference Code / Number.');
        header('Location: ' . url('customer/pay.php?id=' . $reservationId));
        exit;
    }

    $proofImagePath = $reservation['payment_proof_img'] ?? '';

    // Handle File Upload
    if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['payment_proof'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        if ($file['size'] > $maxSize) {
            flash('error', 'File size exceeds 5MB. Please upload a smaller image.');
            header('Location: ' . url('customer/pay.php?id=' . $reservationId));
            exit;
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedTypes)) {
            flash('error', 'Invalid file type. Please upload a JPG, PNG, or WEBP screenshot image.');
            header('Location: ' . url('customer/pay.php?id=' . $reservationId));
            exit;
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            $ext = 'jpg';
        }

        $uploadDir = __DIR__ . '/../assets/uploads/payments/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = 'proof_' . $reservationId . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            $proofImagePath = 'assets/uploads/payments/' . $fileName;
        } else {
            flash('error', 'Failed to save uploaded proof image. Please try again.');
            header('Location: ' . url('customer/pay.php?id=' . $reservationId));
            exit;
        }
    }

    if (empty($proofImagePath) && empty($reservation['payment_proof_img'])) {
        flash('error', 'Please upload a screenshot of your payment receipt.');
        header('Location: ' . url('customer/pay.php?id=' . $reservationId));
        exit;
    }

    // Update payment record in database
    if (!empty($reservation['payment_id'])) {
        $updateStmt = $pdo->prepare('
            UPDATE payments 
            SET payment_method = ?, online_ref_code = ?, payment_proof_img = ?, payment_status = "PENDING_APPROVAL", admin_notes = NULL, submitted_at = NOW()
            WHERE id = ?
        ');
        $updateStmt->execute([$paymentMethod, $refCode, $proofImagePath, $reservation['payment_id']]);
    } else {
        $insertStmt = $pdo->prepare('
            INSERT INTO payments (reservation_id, payment_reference, amount, payment_method, online_ref_code, payment_proof_img, payment_status, submitted_at, created_at)
            VALUES (?, ?, ?, ?, ?, ?, "PENDING_APPROVAL", NOW(), NOW())
        ');
        $insertStmt->execute([
            $reservationId, 'PAY-' . $reservation['reservation_number'], $reservation['total_amount'],
            $paymentMethod, $refCode, $proofImagePath
        ]);
    }

    log_action(user()['id'], 'SUBMIT_PAYMENT_PROOF', 'reservation', $reservationId, 'Submitted payment ref ' . $refCode);
    notify(user()['id'], 'Payment Proof Submitted', 'Your payment proof for ' . $reservation['reservation_number'] . ' was submitted and is undergoing admin review.', 'PAYMENT');

    flash('success', 'Payment proof submitted successfully! Your payment is under admin verification.');
    header('Location: ' . url('customer/reservations.php'));
    exit;
}

$pageTitle = 'Submit Payment Proof | Lido De Paris Hotel';
require __DIR__ . '/../includes/header.php';
?>

<div class="section-head" style="margin-bottom: 2rem;">
    <div>
        <span class="kicker">ONLINE PAYMENT VERIFICATION</span>
        <h1 style="margin-bottom: 0.35rem;">Submit Payment Proof</h1>
        <p style="color: var(--text-secondary, #5C625D);">Reservation #<strong><?=e($reservation['reservation_number'])?></strong></p>
    </div>
    <a class="btn btn-outline" href="<?=url('customer/reservations.php')?>">&larr; Back to My Stays</a>
</div>

<?php if (!empty($reservation['admin_notes']) && $reservation['payment_status'] === 'REJECTED'): ?>
    <div class="alert error" style="margin-bottom: 2rem;">
        <strong>⚠️ Previous Payment Rejected:</strong> <?=e($reservation['admin_notes'])?>. Please re-check your reference code and upload a clear screenshot.
    </div>
<?php endif; ?>

<div class="payment-grid" style="display: grid; grid-template-columns: 1fr 1.2fr; gap: 2.5rem; align-items: start;">
    
    <!-- Payment Instructions Panel -->
    <div class="panel" style="padding: 2rem;">
        <h3 style="font-size: 1.3rem; margin-bottom: 1rem; color: var(--brand, #6B1D2F);">Payment Channels</h3>
        <p style="font-size: 0.9rem; color: var(--text-secondary, #5C625D); line-height: 1.6; margin-bottom: 1.5rem;">
            Please send the exact amount of <strong style="color: var(--brand, #6B1D2F); font-size: 1.15rem;">₱<?=number_format((float)$reservation['total_amount'], 2)?></strong> to any of Lido De Paris Hotel's official payment accounts below:
        </p>

        <div class="channel-card" style="background: var(--surface-muted, #F5EFE6); border: 1px solid var(--border-color, #E8E0D5); border-radius: 8px; padding: 1.25rem; margin-bottom: 1rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                <strong style="color: #007DFF; font-size: 1.05rem;">💙 GCash</strong>
                <span class="badge" style="background: #E6F2FF; color: #0066CC;">Instant</span>
            </div>
            <div style="font-size: 0.9rem; line-height: 1.6;">
                Account Name: <strong>Lido De Paris Hotel Inc.</strong><br>
                GCash Number: <strong style="font-size: 1.1rem; letter-spacing: 0.5px;">0917-894-6943</strong>
            </div>
        </div>

        <div class="channel-card" style="background: var(--surface-muted, #F5EFE6); border: 1px solid var(--border-color, #E8E0D5); border-radius: 8px; padding: 1.25rem; margin-bottom: 1rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                <strong style="color: #00D632; font-size: 1.05rem;">💚 Maya / PayMaya</strong>
                <span class="badge" style="background: #E6FFE8; color: #008820;">Instant</span>
            </div>
            <div style="font-size: 0.9rem; line-height: 1.6;">
                Account Name: <strong>Lido De Paris Hotel</strong><br>
                Maya Number: <strong style="font-size: 1.1rem; letter-spacing: 0.5px;">0917-894-6943</strong>
            </div>
        </div>

        <div class="channel-card" style="background: var(--surface-muted, #F5EFE6); border: 1px solid var(--border-color, #E8E0D5); border-radius: 8px; padding: 1.25rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                <strong style="color: #1A365D; font-size: 1.05rem;">🏦 Bank Transfer (BDO)</strong>
                <span class="badge" style="background: #EDF2F7; color: #2B6CB0;">Online Banking</span>
            </div>
            <div style="font-size: 0.9rem; line-height: 1.6;">
                Account Name: <strong>Lido De Paris Hotel &amp; Entertainment Center</strong><br>
                Account Number: <strong style="font-size: 1.05rem;">0012-3456-7890</strong><br>
                Branch: Binondo Ongpin Branch
            </div>
        </div>
    </div>

    <!-- Payment Proof Form Panel -->
    <div class="panel" style="padding: 2rem;">
        <h3 style="font-size: 1.3rem; margin-bottom: 1.25rem; color: var(--text-heading);">Submit Your Receipt &amp; Ref Code</h3>

        <form method="post" enctype="multipart/form-data" action="<?=url('customer/pay.php?id=' . $reservationId)?>">
            <input type="hidden" name="csrf" value="<?=csrf()?>">
            <input type="hidden" name="reservation_id" value="<?=$reservationId?>">

            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label for="payment_method" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">Payment Method</label>
                <select id="payment_method" name="payment_method" required style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 6px; border: 1px solid var(--border-color); background: var(--surface);">
                    <option value="GCASH" <?=($reservation['payment_method'] ?? 'GCASH') === 'GCASH' ? 'selected' : ''?>>GCash</option>
                    <option value="MAYA" <?=($reservation['payment_method'] ?? '') === 'MAYA' ? 'selected' : ''?>>Maya / PayMaya</option>
                    <option value="BANK_TRANSFER" <?=($reservation['payment_method'] ?? '') === 'BANK_TRANSFER' ? 'selected' : ''?>>Bank Transfer (BDO / BPI)</option>
                    <option value="ONLINE" <?=($reservation['payment_method'] ?? '') === 'ONLINE' ? 'selected' : ''?>>Online Credit/Debit Card</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label for="online_ref_code" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">Payment Reference Number / Code <span style="color: red;">*</span></label>
                <input required id="online_ref_code" type="text" name="online_ref_code" value="<?=e($reservation['online_ref_code'] ?? '')?>" placeholder="e.g. 100294819420 or Ref # from receipt" style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 6px; border: 1px solid var(--border-color); font-family: monospace; font-size: 1rem;">
                <small style="color: var(--text-secondary, #7A807B); font-size: 0.78rem;">Enter the reference number printed on your GCash/Maya/Bank confirmation receipt.</small>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label for="payment_proof" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">Upload Payment Screenshot / Receipt <span style="color: red;">*</span></label>
                <input id="payment_proof" type="file" name="payment_proof" accept="image/jpeg,image/png,image/webp" style="width: 100%; padding: 0.5rem; border: 1px dashed var(--border-color); border-radius: 6px; background: var(--surface-muted); cursor: pointer;" onchange="previewProofImage(this)">
                <small style="color: var(--text-secondary, #7A807B); font-size: 0.78rem; display: block; margin-top: 0.3rem;">Accepted formats: JPG, PNG, WEBP (Max 5MB).</small>
            </div>

            <!-- Image Preview Container -->
            <div id="image_preview_box" style="margin-bottom: 1.5rem; <?=!empty($reservation['payment_proof_img']) ? '' : 'display: none;'?>">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.3rem; color: var(--text-secondary);">Receipt Preview:</label>
                <img id="preview_img" src="<?=!empty($reservation['payment_proof_img']) ? url($reservation['payment_proof_img']) : ''?>" alt="Payment Proof Preview" style="max-width: 100%; max-height: 250px; border-radius: 6px; border: 1px solid var(--border-color); object-fit: contain; background: #000;">
            </div>

            <button class="btn btn-gold" type="submit" style="width: 100%; padding: 0.85rem; font-size: 1.05rem;">Submit Payment for Admin Review</button>
        </form>
    </div>
</div>

<script>
function previewProofImage(input) {
    const previewBox = document.getElementById('image_preview_box');
    const previewImg = document.getElementById('preview_img');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewBox.style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php require __DIR__ . '/../includes/footer.php'; ?>
