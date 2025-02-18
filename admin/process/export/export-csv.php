<?php

date_default_timezone_set('Asia/Manila');

require '../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

include '../../../includes/database.php';

try {
    // Object for spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // headers
    $headers = ['Name', 'Date', 'Time In', 'Time Out', 'Age', 'Sex', 'Duration', 'Code'];
    $sheet->fromArray($headers, NULL, 'A1');

    $sheet->getStyle('A1:H1')->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->getStartColor()->setRGB('1c1c1c');

    $sheet->getStyle('A1:H1')->getFont()
        ->setBold(true) 
        ->setSize(12)   
        ->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF')) 
        ->setName('Arial');
    
    // Width of every column to be fit
    $sheet->getColumnDimension('A')->setWidth(40);    
    $sheet->getColumnDimension('B')->setWidth(15);
    $sheet->getColumnDimension('C')->setWidth(12);
    $sheet->getColumnDimension('D')->setWidth(12);
    $sheet->getColumnDimension('E')->setWidth(8);
    $sheet->getColumnDimension('F')->setWidth(8);
    $sheet->getColumnDimension('G')->setWidth(12);
    $sheet->getColumnDimension('H')->setWidth(12);

    // Fetch data from DB
    $GetDataQuery = "
        SELECT 
            CONCAT(UPPER(v.first_name), ' ', UPPER(v.middle_name), ' ', UPPER(v.last_name)) AS name,
            DATE_FORMAT(t.time_in, '%Y-%m-%d') AS date,
            TIME_FORMAT(t.time_in, '%H:%i:%s') AS time_in,
            TIME_FORMAT(t.time_out, '%H:%i:%s') AS time_out,
            v.age,
            s.sex_name AS sex,
            TIMESTAMPDIFF(SECOND, t.time_in, t.time_out) AS duration_seconds,
            t.code
        FROM visitors v
        INNER JOIN time_logs t ON v.id = t.client_id
        INNER JOIN sex s ON v.sex_id = s.id";
    $result = $conn->query($GetDataQuery);

    // Populate data
    $rowNumber = 2; 
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $duration = isset($row['duration_seconds']) && $row['duration_seconds'] > 0 
                ? gmdate('H:i:s', $row['duration_seconds']) 
                : '-';

            $sheet->fromArray([
                $row['name'] ?? '-',
                $row['date'] ?? '-',
                $row['time_in'] ?? '-',
                $row['time_out'] ?? '-',
                $row['age'] ?? '-',
                $row['sex'] ?? '-',
                $duration,
                $row['code'] ?? '-'
            ], NULL, "A$rowNumber");

            $rowNumber++;
        }
    }

    $sheet->getStyle("A1:H$rowNumber")
        ->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
        ->setVertical(Alignment::VERTICAL_CENTER);

    $currentDateTime = date('Y-m-d-H-i-s');
    $outputFile = "Visitor-Record-$currentDateTime.xlsx";
    
    $writer = new Xlsx($spreadsheet);
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $outputFile . '"');
    
    $writer->save('php://output');

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
