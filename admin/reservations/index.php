<?php
require __DIR__ . '/../../includes/admin_auth.php';

$pdo = db();
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    check_csrf();
    $id = (int)($_POST['id'] ?? 0);
    $postAction = $_POST['action'] ?? '';

    // Handle Payment Approval / Rejection
    if ($postAction === 'approve_payment' && $id > 0) {
        $s = $pdo->prepare('SELECT user_id, reservation_number FROM reservations WHERE id = ?');
        $s->execute([$id]);
        $r = $s->fetch();

        if ($r) {
            $pdo->prepare("UPDATE payments SET payment_status = 'PAID', admin_notes = 'Approved by Admin' WHERE reservation_id = ?")->execute([$id]);
            $pdo->prepare("UPDATE reservations SET status = 'CONFIRMED' WHERE id = ?")->execute([$id]);

            log_action(user()['id'], 'APPROVE_PAYMENT', 'reservation', $id, 'Approved payment proof for ' . $r['reservation_number']);
            notify($r['user_id'], 'Payment Approved!', 'Your payment for reservation ' . $r['reservation_number'] . ' was verified and approved. Your stay is CONFIRMED.', 'PAYMENT');
            flash('success', 'Payment proof for ' . $r['reservation_number'] . ' approved & reservation CONFIRMED!');
        }
        header('Location: index.php');
        exit;
    }

    if ($postAction === 'reject_payment' && $id > 0) {
        $s = $pdo->prepare('SELECT user_id, reservation_number FROM reservations WHERE id = ?');
        $s->execute([$id]);
        $r = $s->fetch();
        $notes = trim($_POST['reject_reason'] ?? 'Invalid payment reference code or screenshot.');

        if ($r) {
            $pdo->prepare("UPDATE payments SET payment_status = 'REJECTED', admin_notes = ? WHERE reservation_id = ?")->execute([$notes, $id]);

            log_action(user()['id'], 'REJECT_PAYMENT', 'reservation', $id, 'Rejected payment proof for ' . $r['reservation_number']);
            notify($r['user_id'], 'Payment Rejected', 'Your payment proof for reservation ' . $r['reservation_number'] . ' was rejected: ' . $notes, 'PAYMENT');
            flash('error', 'Payment proof for ' . $r['reservation_number'] . ' rejected.');
        }
        header('Location: index.php');
        exit;
    }

    $actionMap = [
        'confirm'   => 'CONFIRMED',
        'reject'    => 'REJECTED',
        'check_in'  => 'CHECKED_IN',
        'check_out' => 'CHECKED_OUT',
        'cancel'    => 'CANCELLED',
    ];
    $next = $actionMap[$postAction] ?? null;

    if ($next && $id > 0) {
        $s = $pdo->prepare('SELECT user_id, reservation_number FROM reservations WHERE id = ?');
        $s->execute([$id]);
        $r = $s->fetch();

        if ($r) {
            $pdo->prepare('UPDATE reservations SET status = ? WHERE id = ?')->execute([$next, $id]);
            notify($r['user_id'], 'Reservation Update', 'Your reservation ' . $r['reservation_number'] . ' is now ' . $next . '.', 'RESERVATION');
            log_action(user()['id'], 'RESERVATION_' . $next, 'reservation', $id, 'Updated status of ' . $r['reservation_number'] . ' to ' . $next);
            flash('success', 'Reservation ' . $r['reservation_number'] . ' updated to ' . $next . '.');
        }
    }
    header('Location: index.php');
    exit;
}

$rows = $pdo->query("
    SELECT r.*, rm.title, rm.room_number, u.first_name, u.last_name, u.email, u.phone,
           p.id AS payment_id, p.payment_status, p.payment_method, p.online_ref_code, p.payment_proof_img, p.submitted_at, p.admin_notes
    FROM reservations r
    JOIN rooms rm ON rm.id = r.room_id
    JOIN users u ON u.id = r.user_id
    LEFT JOIN payments p ON p.reservation_id = r.id
    ORDER BY r.created_at DESC
")->fetchAll();

$pageTitle = 'Manage Reservations & Payments';
require __DIR__ . '/../../includes/header.php';
?>

<div class="admin-page-heading">
    <div>
        <span class="kicker">FRONT DESK &amp; CONCIERGE</span>
        <h1>Manage Reservations &amp; Online Payments</h1>
        <p>Review, verify online payment reference codes &amp; screenshots, confirm bookings, or update guest records.</p>
    </div>
</div>

<div class="admin-panel">
    <div class="table-scroll">
        <table class="admin-tbl">
            <thead>
                <tr>
                    <th>Reservation #</th>
                    <th>Guest</th>
                    <th>Room</th>
                    <th>Check-In / Out</th>
                    <th>Rate</th>
                    <th>Payment Review</th>
                    <th>Reservation Status</th>
                    <th>Update</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $r): 
                    $pStatus = $r['payment_status'] ?? 'PENDING';
                ?>
                    <tr>
                        <td>
                            <strong><?=e($r['reservation_number'])?></strong>
                            <br><small style="color:var(--text-secondary);font-size:0.74rem;">Booked: <?=date('M d, Y', strtotime($r['created_at']))?></small>
                        </td>
                        <td>
                            <strong><?=e($r['first_name'] . ' ' . $r['last_name'])?></strong>
                            <br><small style="color:var(--text-secondary);font-size:0.74rem;"><?=e($r['email'])?><?=!empty($r['phone']) ? ' &middot; ' . e($r['phone']) : ''?></small>
                        </td>
                        <td>
                            <strong><?=e($r['title'])?></strong>
                            <br><small style="color:var(--text-secondary);font-size:0.74rem;">Room <?=e($r['room_number'])?> &middot; <?=$r['guests']?> Guest<?=$r['guests'] > 1 ? 's' : ''?></small>
                        </td>
                        <td>
                            <?=date('M d, Y', strtotime($r['check_in']))?> &rarr; <?=date('M d, Y', strtotime($r['check_out']))?>
                            <br><small style="color:var(--text-secondary);font-size:0.74rem;"><?=$r['nights']?> Night<?=$r['nights'] > 1 ? 's' : ''?></small>
                        </td>
                        <td><strong>₱<?=number_format((float)$r['total_amount'], 2)?></strong></td>
                        <td>
                            <?php if ($pStatus === 'PENDING_APPROVAL'): ?>
                                <span class="badge" style="background:#FEF3C7;color:#92400E;font-weight:600;padding:0.25rem 0.5rem;border-radius:4px;display:inline-block;margin-bottom:0.3rem;">⚡ UNDER REVIEW</span>
                                <br>
                                <button class="btn small btn-gold" type="button" style="font-weight:700;margin-top:0.2rem;" onclick="openPaymentModal(<?=htmlentities(json_encode($r))?>)">📷 Inspect Proof</button>
                            <?php elseif ($pStatus === 'PAID'): ?>
                                <span class="badge" style="background:#ECFDF5;color:#065F46;font-weight:600;padding:0.2rem 0.45rem;border-radius:4px;display:inline-block;">✅ PAID</span>
                                <?php if (!empty($r['online_ref_code'])): ?>
                                    <br><small style="font-family:monospace;font-size:0.72rem;color:var(--text-secondary);">Ref: <?=e($r['online_ref_code'])?></small>
                                <?php endif; ?>
                                <br><button class="btn small btn-outline" type="button" style="margin-top:0.25rem;font-size:0.72rem;padding:0.2rem 0.45rem;border:1px solid #D4AF37;color:#6B1D2F;border-radius:4px;" onclick="openPaymentModal(<?=htmlentities(json_encode($r))?>)">📷 Review Proof</button>
                            <?php elseif ($pStatus === 'REJECTED'): ?>
                                <span class="badge" style="background:#FEF2F2;color:#991B1B;font-weight:600;padding:0.2rem 0.45rem;border-radius:4px;display:inline-block;">❌ REJECTED</span>
                                <br><button class="btn small btn-outline" type="button" style="margin-top:0.25rem;font-size:0.72rem;padding:0.2rem 0.45rem;border:1px solid #6B1D2F;color:#6B1D2F;border-radius:4px;" onclick="openPaymentModal(<?=htmlentities(json_encode($r))?>)">📷 Review Details</button>
                            <?php else: ?>
                                <span class="badge" style="background:#F3F4F6;color:#4B5563;font-weight:600;padding:0.2rem 0.45rem;border-radius:4px;display:inline-block;">UNPAID</span>
                                <br><button class="btn small btn-outline" type="button" style="margin-top:0.25rem;font-size:0.72rem;padding:0.2rem 0.45rem;border:1px solid #CCC;color:#555;border-radius:4px;" onclick="openPaymentModal(<?=htmlentities(json_encode($r))?>)">📷 Inspect Details</button>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="s-badge <?=e(strtolower($r['status']))?>">
                                <?=e(ucfirst(strtolower(str_replace('_', ' ', $r['status']))))?>
                            </span>
                        </td>
                        <td>
                            <form method="post" action="index.php" style="display:flex;gap:0.4rem;align-items:center;">
                                <input type="hidden" name="csrf" value="<?=csrf()?>">
                                <input type="hidden" name="id" value="<?=$r['id']?>">
                                <select name="action" class="admin-select" aria-label="Action for <?=e($r['reservation_number'])?>">
                                    <option value="confirm"   <?=$r['status']==='CONFIRMED'  ?'selected':''?>>Confirm</option>
                                    <option value="check_in"  <?=$r['status']==='CHECKED_IN' ?'selected':''?>>Check In</option>
                                    <option value="check_out" <?=$r['status']==='CHECKED_OUT'?'selected':''?>>Check Out</option>
                                    <option value="reject"    <?=$r['status']==='REJECTED'   ?'selected':''?>>Reject</option>
                                    <option value="cancel"    <?=$r['status']==='CANCELLED'  ?'selected':''?>>Cancel</option>
                                </select>
                                <button class="btn-details" type="submit">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal for Inspecting Payment Proof -->
<div id="paymentModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.75); z-index:9999; align-items:center; justify-content:center; padding:1.5rem;">
    <div style="background:var(--surface, #FFF); border-radius:10px; max-width:620px; width:100%; max-height:90vh; overflow-y:auto; padding:2rem; position:relative; box-shadow:0 20px 40px rgba(0,0,0,0.4); border:2px solid #D4AF37;">
        <button type="button" onclick="closePaymentModal()" style="position:absolute; top:1rem; right:1rem; background:transparent; border:none; font-size:1.5rem; cursor:pointer; color:var(--text-primary);">&times;</button>
        
        <h3 style="margin-top:0; margin-bottom:1rem; font-size:1.35rem; color:#6B1D2F;">📷 Lido Payment Verification &amp; Review</h3>
        
        <div id="modalContent">
            <!-- Populated via JavaScript -->
        </div>
    </div>
</div>

<script>
function openPaymentModal(data) {
    const modal = document.getElementById('paymentModal');
    const content = document.getElementById('modalContent');
    
    const csrfToken = <?=json_encode(csrf())?>;
    const proofUrl = data.payment_proof_img ? '<?=url('')?>' + data.payment_proof_img : '';
    
    let html = `
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1.25rem; background:var(--surface-muted, #F5EFE6); padding:1rem; border-radius:6px; font-size:0.9rem;">
            <div>
                <strong>Reservation #:</strong> <span style="font-family:monospace;">${data.reservation_number}</span><br>
                <strong>Guest Name:</strong> ${data.first_name} ${data.last_name}<br>
                <strong>Total Amount:</strong> <span style="color:#6B1D2F; font-weight:bold;">₱${parseFloat(data.total_amount).toLocaleString(undefined, {minimumFractionDigits:2})}</span><br>
                <strong>Payment Status:</strong> <span style="font-weight:bold;">${data.payment_status || 'UNPAID'}</span>
            </div>
            <div>
                <strong>Payment Method:</strong> ${data.payment_method || 'N/A'}<br>
                <strong>Ref Code / Number:</strong> <span style="font-family:monospace; background:#FFF; padding:2px 6px; border-radius:4px; font-weight:bold; border:1px solid #D4AF37;">${data.online_ref_code || 'None'}</span><br>
                <strong>Submitted At:</strong> ${data.submitted_at || 'N/A'}
                ${data.admin_notes ? `<br><strong>Admin Notes:</strong> ${data.admin_notes}` : ''}
            </div>
        </div>
    `;

    if (proofUrl) {
        html += `
            <div style="margin-bottom:1.5rem; text-align:center;">
                <label style="display:block; font-weight:bold; margin-bottom:0.5rem; text-align:left; color:#6B1D2F;">Uploaded Screenshot Receipt:</label>
                <a href="${proofUrl}" target="_blank" title="Click to view full resolution">
                    <img src="${proofUrl}" alt="Proof of Payment" style="max-width:100%; max-height:300px; border-radius:6px; border:2px solid #D4AF37; object-fit:contain; background:#000;">
                </a>
                <small style="display:block; color:var(--text-secondary); margin-top:0.3rem;">(Click image to open full size in new tab)</small>
            </div>
        `;
    } else {
        html += `<div style="background:#FFF3CD; color:#856404; padding:0.75rem; border-radius:6px; margin-bottom:1rem; font-size:0.9rem;">ℹ️ No screenshot proof uploaded for this booking.</div>`;
    }

    if (data.payment_status === 'PENDING_APPROVAL') {
        html += `
            <div style="display:flex; gap:1rem; margin-top:1.5rem; border-top:1px solid var(--border-color, #EAE4DA); padding-top:1.25rem;">
                <form method="post" action="index.php" style="flex:1;">
                    <input type="hidden" name="csrf" value="${csrfToken}">
                    <input type="hidden" name="id" value="${data.id}">
                    <input type="hidden" name="action" value="approve_payment">
                    <button class="btn btn-gold" type="submit" style="width:100%; padding:0.75rem; font-weight:bold; background:linear-gradient(135deg, #D4AF37, #C5A059); color:#1A0D00; border:none; border-radius:6px; cursor:pointer;" onclick="return confirm('Approve payment of ₱${parseFloat(data.total_amount).toLocaleString()} and confirm reservation?')">
                        ✅ Approve Payment &amp; Confirm Booking
                    </button>
                </form>

                <button class="btn danger" type="button" style="flex:1; padding:0.75rem; font-weight:bold; background:#6B1D2F; color:#FFF; border:none; border-radius:6px; cursor:pointer;" onclick="toggleRejectBox()">
                    ❌ Reject Payment
                </button>
            </div>

            <form id="rejectForm" method="post" action="index.php" style="display:none; margin-top:1rem; background:#FEF2F2; padding:1rem; border-radius:6px; border:1px solid #FCA5A5;">
                <input type="hidden" name="csrf" value="${csrfToken}">
                <input type="hidden" name="id" value="${data.id}">
                <input type="hidden" name="action" value="reject_payment">
                <label style="display:block; font-weight:bold; margin-bottom:0.4rem; color:#991B1B;">Reason for Rejection:</label>
                <input required type="text" name="reject_reason" placeholder="e.g. Invalid reference code or blurry receipt image" style="width:100%; padding:0.5rem; border-radius:4px; border:1px solid #FCA5A5; margin-bottom:0.75rem;">
                <button class="btn danger small" type="submit" style="background:#6B1D2F; color:#FFF; border:none; padding:0.4rem 0.8rem; border-radius:4px; cursor:pointer;">Confirm Rejection</button>
            </form>
        `;
    }

    content.innerHTML = html;
    modal.style.display = 'flex';
}

function toggleRejectBox() {
    const box = document.getElementById('rejectForm');
    if (box) box.style.display = box.style.display === 'none' ? 'block' : 'none';
}

function closePaymentModal() {
    document.getElementById('paymentModal').style.display = 'none';
}
</script>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
