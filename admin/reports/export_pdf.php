<?php
require __DIR__ . '/../../includes/bootstrap.php';
require_admin();

if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../vendor/autoload.php';
}

if (!class_exists('TCPDF')) {
    die('PDF export unavailable: TCPDF is not installed on the server. Please run composer install.');
}

$start = $_GET['start'] ?? date('Y-m-01');
$end = $_GET['end'] ?? date('Y-m-t');

$pdo = db();
$stmt = $pdo->prepare('
    SELECT r.reservation_number, r.check_in, r.check_out, r.status, r.total_amount, 
           u.first_name, u.last_name, rm.title as room_name
    FROM reservations r
    JOIN users u ON u.id = r.user_id
    JOIN rooms rm ON rm.id = r.room_id
    WHERE r.check_in >= ? AND r.check_in <= ?
    ORDER BY r.check_in ASC
');
$stmt->execute([$start, $end]);
$reservations = $stmt->fetchAll();

// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('Jill Hotel');
$pdf->SetAuthor('Admin');
$pdf->SetTitle('Reservations Report');

// Set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// Set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Add a page
$pdf->AddPage();

$html = '<h2>Reservations Report</h2>';
$html .= '<p>Period: ' . htmlspecialchars($start) . ' to ' . htmlspecialchars($end) . '</p>';

$html .= '<table border="1" cellpadding="4">
    <thead>
        <tr style="background-color:#f0f0f0;">
            <th><strong>Ref Number</strong></th>
            <th><strong>Guest Name</strong></th>
            <th><strong>Room</strong></th>
            <th><strong>Check In</strong></th>
            <th><strong>Check Out</strong></th>
            <th><strong>Status</strong></th>
            <th><strong>Total (PHP)</strong></th>
        </tr>
    </thead>
    <tbody>';

$totalRev = 0;
foreach ($reservations as $res) {
    $html .= '<tr>
        <td>' . htmlspecialchars($res['reservation_number']) . '</td>
        <td>' . htmlspecialchars($res['first_name'] . ' ' . $res['last_name']) . '</td>
        <td>' . htmlspecialchars($res['room_name']) . '</td>
        <td>' . htmlspecialchars($res['check_in']) . '</td>
        <td>' . htmlspecialchars($res['check_out']) . '</td>
        <td>' . htmlspecialchars($res['status']) . '</td>
        <td align="right">' . number_format((float)$res['total_amount'], 2) . '</td>
    </tr>';
    if ($res['status'] !== 'CANCELLED') {
        $totalRev += (float)$res['total_amount'];
    }
}

$html .= '<tr>
    <td colspan="6" align="right"><strong>Total Non-Cancelled Revenue:</strong></td>
    <td align="right"><strong>' . number_format($totalRev, 2) . '</strong></td>
</tr>';

$html .= '</tbody></table>';

// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// Close and output PDF document
$pdf->Output('report_' . $start . '_to_' . $end . '.pdf', 'I');

