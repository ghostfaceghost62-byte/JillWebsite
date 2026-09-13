<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../vendor/autoload.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    flash('error', 'No reservation ID provided.');
    header('Location: ' . url('customer/reservations.php'));
    exit;
}

$pdo = db();
$stmt = $pdo->prepare('
    SELECT r.*, rm.title, rm.room_number, rm.price_per_night, rm.bed_type,
           u.first_name, u.last_name, u.email
    FROM reservations r
    JOIN rooms rm ON rm.id = r.room_id
    JOIN users u ON u.id = r.user_id
    WHERE r.id = ? AND r.user_id = ?
');
$stmt->execute([$id, user()['id']]);
$res = $stmt->fetch();

if (!$res) {
    flash('error', 'Reservation not found or access denied.');
    header('Location: ' . url('customer/reservations.php'));
    exit;
}

// Calculated fields
$nights       = (int) $res['nights'];
$pricePerNight = (float) $res['price_per_night'];
$subtotal     = $pricePerNight * $nights;
$taxRate      = 0.12;
$taxAmount    = $subtotal * $taxRate;
$total        = $subtotal + $taxAmount;

$format = $_GET['format'] ?? 'html';

// ── PDF EXPORT ──────────────────────────────────────────────────────────────
if ($format === 'pdf') {
    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->SetCreator('Jill Hotel Reservation System');
    $pdf->SetAuthor('Jill Hotel');
    $pdf->SetTitle('Official Receipt - ' . $res['reservation_number']);
    $pdf->SetSubject('Hotel Reservation Receipt');
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetMargins(20, 20, 20);
    $pdf->SetAutoPageBreak(true, 20);
    $pdf->AddPage();

    $html = '
    <style>
        body { font-family: helvetica; color: #2A2D2A; }
        .header-band { background-color: #1C3328; color: #FAF8F5; padding: 18px 20px; }
        .header-band h1 { font-size: 26pt; color: #D8BA7B; margin: 0 0 2px 0; }
        .header-band p { font-size: 9pt; color: rgba(250,248,245,0.75); margin: 0; }
        .section-label { font-size: 7pt; font-weight: bold; letter-spacing: 2px; text-transform: uppercase; color: #8E7134; margin: 14px 0 6px 0; }
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { padding: 5px 0; font-size: 9.5pt; border-bottom: 1px solid #EDE7DD; }
        .info-table td:last-child { text-align: right; }
        .totals-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .totals-table td { padding: 5px 0; font-size: 9.5pt; }
        .totals-table td:last-child { text-align: right; }
        .total-row td { border-top: 2px solid #1C3328; padding-top: 10px; font-size: 12pt; font-weight: bold; color: #1C3328; }
        .status-badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 8pt; font-weight: bold; background: #E2F2E7; color: #166534; }
        .footer-note { margin-top: 20px; text-align: center; font-size: 8pt; color: #8E7134; border-top: 1px solid #E8E2D7; padding-top: 12px; }
    </style>

    <div class="header-band">
        <h1>JILL HOTEL</h1>
        <p>101 Seaside Avenue, Manila, Philippines &bull; +63 2 8123 4567 &bull; stay@jillhotel.com</p>
    </div>

    <div style="margin-top:14px; display: flex; justify-content: space-between;">
        <div>
            <p class="section-label">Official Receipt</p>
            <p style="font-size:9pt; color:#5C625D; margin:0;">Issued: ' . date('F d, Y') . '</p>
        </div>
        <div style="text-align:right;">
            <p style="font-size:16pt; font-weight:bold; color:#1C3328; margin:0 0 2px 0;">' . htmlspecialchars((string)$res['reservation_number']) . '</p>
            <span class="status-badge">' . htmlspecialchars((string)$res['status']) . '</span>
        </div>
    </div>

    <p class="section-label">Guest Information</p>
    <table class="info-table">
        <tr><td><strong>Guest Name</strong></td><td>' . htmlspecialchars($res['first_name'] . ' ' . $res['last_name']) . '</td></tr>
        <tr><td><strong>Email Address</strong></td><td>' . htmlspecialchars((string)$res['email']) . '</td></tr>
    </table>

    <p class="section-label">Stay Details</p>
    <table class="info-table">
        <tr><td><strong>Room / Suite</strong></td><td>' . htmlspecialchars((string)$res['title']) . ' &mdash; Room ' . htmlspecialchars((string)$res['room_number']) . '</td></tr>
        <tr><td><strong>Bed Type</strong></td><td>' . htmlspecialchars((string)($res['bed_type'] ?? 'Standard')) . '</td></tr>
        <tr><td><strong>Check-in</strong></td><td>' . date('F d, Y', strtotime((string)$res['check_in'])) . '</td></tr>
        <tr><td><strong>Check-out</strong></td><td>' . date('F d, Y', strtotime((string)$res['check_out'])) . '</td></tr>
        <tr><td><strong>Duration</strong></td><td>' . $nights . ' Night' . ($nights > 1 ? 's' : '') . '</td></tr>
        <tr><td><strong>Guests</strong></td><td>' . (int)$res['guests'] . ' (' . (int)$res['adults'] . ' Adult' . ((int)$res['adults'] > 1 ? 's' : '') . (!empty($res['children']) ? ', ' . $res['children'] . ' Child' : '') . ')</td></tr>
    </table>

    <p class="section-label">Billing Summary</p>
    <table class="totals-table">
        <tr><td>Room Rate</td><td>PHP ' . number_format($pricePerNight, 2) . ' / night</td></tr>
        <tr><td>Subtotal (' . $nights . ' night' . ($nights > 1 ? 's' : '') . ')</td><td>PHP ' . number_format($subtotal, 2) . '</td></tr>
        <tr><td>VAT (12%)</td><td>PHP ' . number_format($taxAmount, 2) . '</td></tr>
        <tr class="total-row"><td>TOTAL AMOUNT DUE</td><td>PHP ' . number_format($total, 2) . '</td></tr>
    </table>

    <div class="footer-note">
        <p>Thank you for choosing Jill Hotel. We look forward to welcoming you.<br>
        For questions, contact our concierge at +63 2 8123 4567 or stay@jillhotel.com</p>
    </div>
    ';

    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output('JillHotel_Receipt_' . $res['reservation_number'] . '.pdf', 'D');
    exit;
}

// ── EXCEL EXPORT ─────────────────────────────────────────────────────────────
if ($format === 'excel') {
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Receipt');

    // Header branding
    $sheet->setCellValue('A1', 'JILL HOTEL — Official Receipt');
    $sheet->mergeCells('A1:C1');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->getColor()->setRGB('1C3328');
    $sheet->getStyle('A1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('FAF8F5');

    $sheet->setCellValue('A2', 'Issued: ' . date('F d, Y'));
    $sheet->mergeCells('A2:C2');

    // Sections helper
    $rows = [
        4  => ['RESERVATION', '', ''],
        5  => ['Reservation #', $res['reservation_number'], ''],
        6  => ['Status',       $res['status'],            ''],
        8  => ['GUEST', '', ''],
        9  => ['Guest Name',   $res['first_name'] . ' ' . $res['last_name'], ''],
        10 => ['Email',        $res['email'],              ''],
        12 => ['STAY DETAILS', '', ''],
        13 => ['Room / Suite', $res['title'] . ' — Room ' . $res['room_number'], ''],
        14 => ['Bed Type',     $res['bed_type'] ?? 'Standard', ''],
        15 => ['Check-in',     $res['check_in'],           ''],
        16 => ['Check-out',    $res['check_out'],          ''],
        17 => ['Nights',       $nights,                    ''],
        18 => ['Guests',       $res['guests'],             ''],
        20 => ['BILLING', '', ''],
        21 => ['Rate / Night', 'PHP ' . number_format($pricePerNight, 2), ''],
        22 => ['Subtotal',     'PHP ' . number_format($subtotal, 2),      ''],
        23 => ['VAT (12%)',    'PHP ' . number_format($taxAmount, 2),     ''],
        24 => ['TOTAL DUE',    'PHP ' . number_format($total, 2),         ''],
    ];

    $sectionRows  = [4, 8, 12, 20];
    $totalRow     = 24;

    foreach ($rows as $rowNum => $cols) {
        $sheet->setCellValue('A' . $rowNum, $cols[0]);
        $sheet->setCellValue('B' . $rowNum, $cols[1]);

        if (in_array($rowNum, $sectionRows)) {
            $sheet->getStyle('A' . $rowNum)->getFont()->setBold(true)->getColor()->setRGB('8E7134');
        }

        if ($rowNum === $totalRow) {
            $sheet->getStyle('A' . $rowNum . ':B' . $rowNum)->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A' . $rowNum . ':B' . $rowNum)->getFill()
                  ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                  ->getStartColor()->setRGB('1C3328');
            $sheet->getStyle('A' . $rowNum . ':B' . $rowNum)->getFont()->getColor()->setRGB('FAF8F5');
        }
    }

    foreach (range('A', 'B') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="JillHotel_Receipt_' . $res['reservation_number'] . '.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('php://output');
    exit;
}

// ── HTML VIEW ────────────────────────────────────────────────────────────────
$pageTitle = 'Receipt · ' . $res['reservation_number'];
require __DIR__ . '/../includes/header.php';
?>

<div class="section-head" style="margin-bottom: 2rem;">
    <div>
        <span class="kicker">OFFICIAL RECEIPT</span>
        <h1 style="margin-bottom: 0.25rem;">Reservation <?=e($res['reservation_number'])?></h1>
        <p style="color: var(--text-secondary); margin: 0;">Issued <?=date('F d, Y')?></p>
    </div>
    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
        <a class="btn btn-gold" href="?id=<?=$res['id']?>&format=pdf" target="_blank">
            ↓ Download PDF
        </a>
        <a class="btn" href="?id=<?=$res['id']?>&format=excel" style="background:var(--brand,#1C3328);color:#FAF8F5;">
            ↓ Download Excel
        </a>
        <a class="btn btn-outline" href="<?=url('customer/reservations.php')?>">← My Stays</a>
    </div>
</div>

<!-- Receipt Document -->
<div class="receipt-doc" style="
    max-width: 660px;
    margin: 0 auto 3rem;
    background: var(--surface, #FFF);
    border: 1px solid var(--border-color, #E8E2D7);
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(28,51,40,0.10);
">

    <!-- Hotel Header Band -->
    <div style="
        background: #1C3328;
        color: #FAF8F5;
        padding: 2rem 2.25rem 1.5rem;
    ">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
            <div>
                <div style="font-family:'Plus Jakarta Sans',sans-serif; font-size: 0.7rem; letter-spacing: 0.22em; color: rgba(250,248,245,0.55); text-transform: uppercase; margin-bottom: 0.3rem;">Boutique Luxury</div>
                <h2 style="font-size: 2rem; color: #D8BA7B; margin: 0 0 0.35rem;">Jill Hotel</h2>
                <p style="font-size: 0.8rem; color: rgba(250,248,245,0.65); margin: 0; line-height: 1.5;">
                    101 Seaside Avenue, Manila, Philippines<br>
                    +63 2 8123 4567 &bull; stay@jillhotel.com
                </p>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 0.7rem; letter-spacing: 0.12em; color: rgba(250,248,245,0.5); text-transform: uppercase; margin-bottom: 0.35rem;">Official Receipt</div>
                <div style="font-size: 1.3rem; font-weight: 700; font-family: monospace; color: #FAF8F5;"><?=e($res['reservation_number'])?></div>
                <div style="margin-top: 0.5rem;">
                    <span style="
                        display: inline-block;
                        padding: 0.2rem 0.75rem;
                        border-radius: 999px;
                        font-size: 0.7rem;
                        font-weight: 700;
                        letter-spacing: 0.06em;
                        background: rgba(216,186,123,0.2);
                        color: #D8BA7B;
                        border: 1px solid rgba(216,186,123,0.35);
                        text-transform: uppercase;
                    "><?=e($res['status'])?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Body -->
    <div style="padding: 2rem 2.25rem;">

        <!-- Guest Info -->
        <div style="margin-bottom: 1.75rem;">
            <div style="font-size: 0.68rem; font-weight: 700; letter-spacing: 0.18em; color: var(--accent, #B89650); text-transform: uppercase; margin-bottom: 0.85rem;">Guest Information</div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem 1.5rem;">
                <div>
                    <div style="font-size: 0.72rem; color: var(--text-secondary); margin-bottom: 0.2rem;">Full Name</div>
                    <div style="font-weight: 600;"><?=e($res['first_name'] . ' ' . $res['last_name'])?></div>
                </div>
                <div>
                    <div style="font-size: 0.72rem; color: var(--text-secondary); margin-bottom: 0.2rem;">Email Address</div>
                    <div style="font-weight: 600;"><?=e($res['email'])?></div>
                </div>
            </div>
        </div>

        <hr style="border: none; border-top: 1px solid var(--border-color, #E8E2D7); margin: 0 0 1.75rem;">

        <!-- Stay Details -->
        <div style="margin-bottom: 1.75rem;">
            <div style="font-size: 0.68rem; font-weight: 700; letter-spacing: 0.18em; color: var(--accent, #B89650); text-transform: uppercase; margin-bottom: 0.85rem;">Stay Details</div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem 1.5rem;">
                <div>
                    <div style="font-size: 0.72rem; color: var(--text-secondary); margin-bottom: 0.2rem;">Room / Suite</div>
                    <div style="font-weight: 600;"><?=e($res['title'])?></div>
                    <div style="font-size: 0.8rem; color: var(--text-secondary);">Room <?=e($res['room_number'])?> &middot; <?=e($res['bed_type'] ?? 'Standard')?></div>
                </div>
                <div>
                    <div style="font-size: 0.72rem; color: var(--text-secondary); margin-bottom: 0.2rem;">Guests</div>
                    <div style="font-weight: 600;"><?=(int)$res['guests']?> Guest<?=$res['guests'] > 1 ? 's' : ''?></div>
                    <div style="font-size: 0.8rem; color: var(--text-secondary);"><?=(int)$res['adults']?> Adult<?=$res['adults'] > 1 ? 's' : ''?><?=!empty($res['children']) ? ', ' . $res['children'] . ' Child' : ''?></div>
                </div>
                <div>
                    <div style="font-size: 0.72rem; color: var(--text-secondary); margin-bottom: 0.2rem;">Check-in</div>
                    <div style="font-weight: 600;"><?=date('F d, Y', strtotime((string)$res['check_in']))?></div>
                </div>
                <div>
                    <div style="font-size: 0.72rem; color: var(--text-secondary); margin-bottom: 0.2rem;">Check-out</div>
                    <div style="font-weight: 600;"><?=date('F d, Y', strtotime((string)$res['check_out']))?></div>
                </div>
                <div>
                    <div style="font-size: 0.72rem; color: var(--text-secondary); margin-bottom: 0.2rem;">Duration</div>
                    <div style="font-weight: 600;"><?=$nights?> Night<?=$nights > 1 ? 's' : ''?></div>
                </div>
            </div>
        </div>

        <hr style="border: none; border-top: 1px solid var(--border-color, #E8E2D7); margin: 0 0 1.75rem;">

        <!-- Billing Summary -->
        <div style="margin-bottom: 1.75rem;">
            <div style="font-size: 0.68rem; font-weight: 700; letter-spacing: 0.18em; color: var(--accent, #B89650); text-transform: uppercase; margin-bottom: 0.85rem;">Billing Summary</div>

            <div style="display: flex; justify-content: space-between; padding: 0.55rem 0; border-bottom: 1px solid var(--border-light, #F0EBE2); font-size: 0.9rem;">
                <span>Room Rate</span>
                <span>&#8369;<?=number_format($pricePerNight, 2)?> / night</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 0.55rem 0; border-bottom: 1px solid var(--border-light, #F0EBE2); font-size: 0.9rem;">
                <span>Subtotal (<?=$nights?> night<?=$nights > 1 ? 's' : ''>)</span>
                <span>&#8369;<?=number_format($subtotal, 2)?></span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 0.55rem 0; border-bottom: 1px solid var(--border-light, #F0EBE2); font-size: 0.9rem; color: var(--text-secondary);">
                <span>VAT (12%)</span>
                <span>&#8369;<?=number_format($taxAmount, 2)?></span>
            </div>

            <!-- Total row -->
            <div style="
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-top: 1rem;
                padding: 1.1rem 1.25rem;
                background: #1C3328;
                border-radius: 6px;
            ">
                <span style="font-size: 0.85rem; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; color: rgba(250,248,245,0.75);">Total Amount Due</span>
                <span style="font-size: 1.5rem; font-weight: 700; color: #D8BA7B;">&#8369;<?=number_format($total, 2)?></span>
            </div>
        </div>

        <!-- Thank you note -->
        <div style="text-align: center; padding: 1.25rem; background: var(--surface-muted, #FAF8F5); border-radius: 6px; border: 1px solid var(--border-light, #F0EBE2);">
            <p style="font-size: 0.85rem; color: var(--accent, #B89650); font-weight: 600; margin: 0 0 0.35rem; letter-spacing: 0.06em; text-transform: uppercase;">Thank You</p>
            <p style="font-size: 0.88rem; color: var(--text-secondary); margin: 0;">We look forward to welcoming you to Jill Hotel.<br>For any inquiries, please contact our concierge team.</p>
        </div>

    </div><!-- /body -->
</div><!-- /receipt-doc -->

<!-- Print stylesheet -->
<style>
@media print {
    .site-header, footer, .section-head > div:last-child, .btn { display: none !important; }
    .receipt-doc { box-shadow: none !important; border: none !important; max-width: 100% !important; }
    body { background: white !important; }
}
</style>

<?php require __DIR__ . '/../includes/footer.php'; ?>
