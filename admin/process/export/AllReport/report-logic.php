<?php

require '../../../../vendor/autoload.php';
require '../../../../includes/database.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

date_default_timezone_set('Asia/Manila');
ob_start();

$type = isset($_GET['type']) ? $_GET['type'] : 'today'; 
$format = isset($_GET['format']) ? $_GET['format'] : 'pdf';

if ($type === 'month') {
    $startDate = date('Y-m-01');
    $endDate = date('Y-m-t');
    $title = "Visitor Records Report";
    $subtitle = date('F 1, Y', strtotime($startDate)) . " to " . date('F t, Y', strtotime($endDate));
    $filename = "Visitor_1_Month_Report_" . date('Y-m');
} else {
    $startDate = $endDate = date('Y-m-d');
    $title = "Visitor Records Report";
    $subtitle = date('F j, Y', strtotime($startDate));
    $filename = "Visitor_Today_Report_" . $startDate;
}

$query = "
SELECT 
    CONCAT(visitors.first_name, ' ', visitors.middle_name, ' ', visitors.last_name) AS full_name,
    DATE(time_logs.time_in) AS log_date,
    DATE_FORMAT(time_logs.time_in, '%h:%i:%s %p') AS time_in,
    DATE_FORMAT(time_logs.time_out, '%h:%i:%s %p') AS time_out,
    visitors.age,
    sex.sex_name, 
    TIME_FORMAT(TIMEDIFF(time_logs.time_out, time_logs.time_in), '%H:%i:%s') AS duration
FROM 
    time_logs
INNER JOIN 
    visitors ON time_logs.client_id = visitors.id
LEFT JOIN 
    sex ON visitors.sex_id = sex.id
WHERE 
    DATE(time_logs.time_in) BETWEEN '$startDate' AND '$endDate'
";


$result = $conn->query($query);
if (!$result) {
    error_log('Query Error: ' . $conn->error);
    die('Error fetching records. Please contact the administrator.');
}

if ($result->num_rows === 0) {
    die('No records found for the selected period.');
}

if ($format === 'xlsx') {
    try {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $headers = ['Name', 'Date', 'Time In', 'Time Out', 'Age', 'Sex', 'Duration'];
        $sheet->fromArray($headers, NULL, 'A1');
        
        // Style headers
        $sheet->getStyle('A1:G1')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1c1c1c']],
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        

        

        // Adjust column widths
        $columns = ['A' => 40, 'B' => 15, 'C' => 15, 'D' => 15, 'E' => 8, 'F' => 8, 'G' => 15];
        foreach ($columns as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);

        $rowNumber = 2;
        while ($row = $result->fetch_assoc()) {
            $sheet->fromArray([
                $row['full_name'] ?? '-',
                $row['log_date'] ?? '-',
                $row['time_in'] ?? '-',
                $row['time_out'] ?? '-',
                $row['age'] ?? '-',
                $row['sex_name'] ?? '-',
                $row['duration'] ?? '-',
            ], NULL, "A$rowNumber");
            $rowNumber++;
        }


        $lastColumn = $sheet->getHighestColumn();
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle("A2:{$lastColumn}{$lastRow}")->applyFromArray([
        'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
]);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename.xlsx\"");
        $writer = new Xlsx($spreadsheet);
        ob_end_clean(); 
        $writer->save('php://output');
    } catch (Exception $e) {
        error_log('XLSX Generation Error: ' . $e->getMessage());
        die('Error generating Excel file.');
    }
} else {
    require '../../../../vendor/tecnickcom/tcpdf/tcpdf.php';

    class CustomPDF extends TCPDF {
        public function Header() {
            global $title, $subtitle;
            $this->SetFont('times', 'B', 20);
            $this->Cell(0, 10, $title, 0, 1, 'C');
            $this->SetFont('times', '', 12);
            $this->Cell(0, 10, $subtitle, 0, 1, 'C');
            $this->Ln(4);
        }

        public function Footer() {
            $this->SetY(-15);
            $this->SetFont('helvetica', 'I', 8);
            $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'C');
        }
    }

    $pdf = new CustomPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetTitle($title);
    $pdf->SetMargins(10, 20, 10);
    $pdf->AddPage();

    $headers = ['Name', 'Date', 'Time In', 'Time Out', 'Age', 'Sex', 'Duration'];
    $colWidths = [80, 30, 30, 30, 20, 20, 70];

    $pdf->SetFont('times', '', 10);
    $pdf->SetFillColor(230, 230, 230);

    foreach ($headers as $i => $header) {
        $pdf->Cell($colWidths[$i], 10, $header, 1, 0, 'C', true);
    }
    $pdf->Ln();

    while ($row = $result->fetch_assoc()) {
        $pdf->Cell($colWidths[0], 10, strtoupper($row['full_name'] ?? '-'), 1);
        $pdf->Cell($colWidths[1], 10, $row['log_date'] ?? '-', 1);
        $pdf->Cell($colWidths[2], 10, $row['time_in'] ?? '-', 1);
        $pdf->Cell($colWidths[3], 10, $row['time_out'] ?? '-', 1);
        $pdf->Cell($colWidths[4], 10, $row['age'] ?? '-', 1);
        $pdf->Cell($colWidths[5], 10, $row['sex_name'] ?? '-', 1);
        $pdf->Cell($colWidths[6], 10, $row['duration'] ?? '-', 1, 1);
    }

    $pdf->Output("$filename.pdf", 'D');
}

?>
