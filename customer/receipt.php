<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../vendor/autoload.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    die("No reservation ID provided.");
}

$pdo = db();
$stmt = $pdo->prepare('
    SELECT r.*, rm.title, rm.room_number, rm.price_per_night, u.first_name, u.last_name, u.email
    FROM reservations r
    JOIN rooms rm ON rm.id = r.room_id
    JOIN users u ON u.id = r.user_id
    WHERE r.id = ? AND r.user_id = ?
');
$stmt->execute([$id, user()['id']]);
$res = $stmt->fetch();

if (!$res) {
    die("Reservation not found or access denied.");
}

$format = $_GET['format'] ?? 'html';

if ($format === 'pdf') {
    // Generate PDF using TCPDF
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Jill Hotel');
    $pdf->SetTitle('Receipt - ' . $res['reservation_number']);
    $pdf->SetMargins(15, 15, 15);
    $pdf->AddPage();
    
    $html = '
    <h1 style="text-align:center;">Jill Hotel Receipt</h1>
    <hr>
    <p><strong>Reservation Number:</strong> ' . htmlspecialchars((string) $res['reservation_number']) . '</p>
    <p><strong>Guest Name:</strong> ' . htmlspecialchars($res['first_name'] . ' ' . $res['last_name']) . '</p>
    <p><strong>Room:</strong> ' . htmlspecialchars((string) $res['title']) . ' (Room ' . htmlspecialchars((string) $res['room_number']) . ')</p>
    <p><strong>Check-in:</strong> ' . htmlspecialchars((string) $res['check_in']) . '</p>
    <p><strong>Check-out:</strong> ' . htmlspecialchars((string) $res['check_out']) . '</p>
    <p><strong>Nights:</strong> ' . htmlspecialchars((string) $res['nights']) . '</p>
    <p><strong>Total Amount:</strong> PHP ' . number_format((float)$res['total_amount'], 2) . '</p>
    <br>
    <p>Thank you for staying with us!</p>
    ';
    
    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output('receipt_' . $res['reservation_number'] . '.pdf', 'D');
    exit;
} elseif ($format === 'excel') {
    // Generate Excel using PhpSpreadsheet
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    $sheet->setCellValue('A1', 'Jill Hotel Receipt');
    $sheet->mergeCells('A1:B1');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
    
    $sheet->setCellValue('A3', 'Reservation Number:');
    $sheet->setCellValue('B3', $res['reservation_number']);
    
    $sheet->setCellValue('A4', 'Guest Name:');
    $sheet->setCellValue('B4', $res['first_name'] . ' ' . $res['last_name']);
    
    $sheet->setCellValue('A5', 'Room:');
    $sheet->setCellValue('B5', $res['title'] . ' (Room ' . $res['room_number'] . ')');
    
    $sheet->setCellValue('A6', 'Check-in:');
    $sheet->setCellValue('B6', $res['check_in']);
    
    $sheet->setCellValue('A7', 'Check-out:');
    $sheet->setCellValue('B7', $res['check_out']);
    
    $sheet->setCellValue('A8', 'Nights:');
    $sheet->setCellValue('B8', $res['nights']);
    
    $sheet->setCellValue('A9', 'Total Amount:');
    $sheet->setCellValue('B9', 'PHP ' . number_format((float)$res['total_amount'], 2));
    
    foreach(range('A','B') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="receipt_' . $res['reservation_number'] . '.xlsx"');
    header('Cache-Control: max-age=0');
    
    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('php://output');
    exit;
}

// Default HTML view
$pageTitle = 'Receipt - ' . $res['reservation_number'];
require __DIR__ . '/../includes/header.php';
?>

<div class="section-head" style="margin-bottom: 2rem;">
    <div>
        <span class="kicker">RESERVATION RECEIPT</span>
        <h1 style="margin-bottom: 0.35rem;">Receipt for #<?=e($res['reservation_number'])?></h1>
    </div>
    <div style="display: flex; gap: 1rem;">
        <a class="btn btn-gold" href="?id=<?=$res['id']?>&format=pdf" target="_blank">Download PDF</a>
        <a class="btn btn-outline" href="?id=<?=$res['id']?>&format=excel">Download Excel</a>
    </div>
</div>

<div class="panel" style="max-width: 600px; margin: 0 auto;">
    <h2 style="text-align:center; margin-bottom: 1.5rem;">Jill Hotel</h2>
    <table style="width: 100%; margin-bottom: 2rem;">
        <tr>
            <td style="padding: 0.5rem 0;"><strong>Guest Name:</strong></td>
            <td style="text-align: right; padding: 0.5rem 0;"><?=e($res['first_name'] . ' ' . $res['last_name'])?></td>
        </tr>
        <tr>
            <td style="padding: 0.5rem 0;"><strong>Room:</strong></td>
            <td style="text-align: right; padding: 0.5rem 0;"><?=e($res['title'])?> (<?=e($res['room_number'])?>)</td>
        </tr>
        <tr>
            <td style="padding: 0.5rem 0;"><strong>Check-in:</strong></td>
            <td style="text-align: right; padding: 0.5rem 0;"><?=e($res['check_in'])?></td>
        </tr>
        <tr>
            <td style="padding: 0.5rem 0;"><strong>Check-out:</strong></td>
            <td style="text-align: right; padding: 0.5rem 0;"><?=e($res['check_out'])?></td>
        </tr>
        <tr>
            <td style="padding: 0.5rem 0;"><strong>Nights:</strong></td>
            <td style="text-align: right; padding: 0.5rem 0;"><?=e((string)$res['nights'])?></td>
        </tr>
        <tr style="border-top: 1px solid var(--border-color, #EDE7DD);">
            <td style="padding: 1rem 0;"><strong>Total Amount:</strong></td>
            <td style="text-align: right; padding: 1rem 0; font-size: 1.25rem;"><strong>&#8369;<?=number_format((float)$res['total_amount'], 2)?></strong></td>
        </tr>
    </table>
    <p style="text-align: center; color: var(--text-secondary, #5C625D);">Thank you for choosing Jill Hotel.</p>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>

