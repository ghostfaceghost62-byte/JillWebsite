<?php
require __DIR__ . '/../../includes/bootstrap.php';
require_admin();

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$start = $_GET['start'] ?? date('Y-m-01');
$end = $_GET['end'] ?? date('Y-m-t');

$pdo = db();
$stmt = $pdo->prepare('
    SELECT r.reservation_number, r.check_in, r.check_out, r.status, r.total_amount, 
           u.first_name, u.last_name, rm.title as room_name,
           r.guests, r.nights
    FROM reservations r
    JOIN users u ON u.id = r.user_id
    JOIN rooms rm ON rm.id = r.room_id
    WHERE r.check_in >= ? AND r.check_in <= ?
    ORDER BY r.check_in ASC
');
$stmt->execute([$start, $end]);
$reservations = $stmt->fetchAll();

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set Header
$headers = ['Ref Number', 'Guest Name', 'Room', 'Check In', 'Check Out', 'Guests', 'Nights', 'Status', 'Total (PHP)'];
foreach ($headers as $index => $header) {
    $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
    $sheet->setCellValue($col . '1', $header);
    $sheet->getStyle($col . '1')->getFont()->setBold(true);
}

// Set Data
$row = 2;
foreach ($reservations as $res) {
    $sheet->setCellValue('A' . $row, $res['reservation_number']);
    $sheet->setCellValue('B' . $row, $res['first_name'] . ' ' . $res['last_name']);
    $sheet->setCellValue('C' . $row, $res['room_name']);
    $sheet->setCellValue('D' . $row, $res['check_in']);
    $sheet->setCellValue('E' . $row, $res['check_out']);
    $sheet->setCellValue('F' . $row, $res['guests']);
    $sheet->setCellValue('G' . $row, $res['nights']);
    $sheet->setCellValue('H' . $row, $res['status']);
    
    // Explicitly set as number format
    $sheet->setCellValueExplicit('I' . $row, $res['total_amount'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
    $sheet->getStyle('I' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
    
    $row++;
}

// Auto-size columns
foreach (range('A', 'I') as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}

// Output
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="reservations_' . $start . '_to_' . $end . '.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;

