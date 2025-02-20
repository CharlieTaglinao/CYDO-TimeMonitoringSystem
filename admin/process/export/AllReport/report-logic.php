<?php
require '../../../../vendor/autoload.php';
require '../../../../includes/database.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

date_default_timezone_set('Asia/Manila');


// Determine report type and format
$type = isset($_GET['type']) ? $_GET['type'] : 'today';
$format = isset($_GET['format']) ? $_GET['format'] : 'pdf';

// Set the date range based on the type
if ($type === 'month') {
    $startDate = date('Y-m-01');
    $endDate = date('Y-m-t');
    $title = "Visitor Records Report - 1 Month";
    $filename = "Visitor_1_Month_Report_" . date('Y-m');
} else {
    $startDate = $endDate = date('Y-m-d');
    $title = "Visitor Records Report - Today";
    $filename = "Visitor_Today_Report_" . $startDate;
}

// Query with defined date range
$query = "
SELECT 
    CONCAT(visitors.first_name, ' ', visitors.middle_name, ' ', visitors.last_name) AS full_name,
    DATE(time_logs.time_in) AS log_date,
    DATE_FORMAT(time_logs.time_in, '%h:%i:%s %p') AS time_in,
    DATE_FORMAT(time_logs.time_out, '%h:%i:%s %p') AS time_out,
    visitors.age,
    sex.sex_name,  -- Join to get sex_name
    TIMEDIFF(time_logs.time_out, time_logs.time_in) AS duration
FROM 
    time_logs
INNER JOIN 
    visitors ON time_logs.client_id = visitors.id
LEFT JOIN 
    sex ON visitors.sex_id = sex.id
WHERE 
    DATE(time_logs.time_in) BETWEEN '$startDate' AND '$endDate'
";

// Fetch records
$result = $conn->query($query);
if (!$result) {
    die('Query Error: ' . $conn->error);
}

if ($format === 'xlsx') {
    // Generate XLSX
    try {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $headers = ['Name', 'Date', 'Time In', 'Time Out', 'Age', 'Sex', 'Duration'];
        $sheet->fromArray($headers, NULL, 'A1');

        // Style headers
        $sheet->getStyle('A1:G1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('1c1c1c');
        $sheet->getStyle('A1:G1')->getFont()
            ->setBold(true)
            ->setSize(12)
            ->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'))
            ->setName('Arial');

        // Adjust column widths
        $sheet->getColumnDimension('A')->setWidth(40);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(8);
        $sheet->getColumnDimension('F')->setWidth(8);
        $sheet->getColumnDimension('G')->setWidth(15);

        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        // Populate data
        $rowNumber = 2;
        while ($row = $result->fetch_assoc()) {
            $duration = isset($row['time_out'])
                ? gmdate('H:i:s', strtotime($row['time_out']) - strtotime($row['time_in']))
                : '-';
            $sheet->fromArray([
                $row['full_name'] ?? '-',
                $row['log_date'] ?? '-',
                $row['time_in'] ?? '-',
                $row['time_out'] ?? '-',
                $row['age'] ?? '-',
                $row['sex_name'] ?? '-',
                $duration,
            ], NULL, "A$rowNumber");
            $rowNumber++;
        }
        // Add a break row every 10 visitors
        if ($visitorCount % 10 === 0) {
            $sheet->mergeCells("A$rowNumber:G$rowNumber"); // Merge the break row
            $sheet->setCellValue("A$rowNumber", "");
            $sheet->getStyle("A$rowNumber")->getFont()->setBold(true);
            $sheet->getStyle("A$rowNumber")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $rowNumber++;
        }
        // Align cells
        $sheet->getStyle("A1:G$rowNumber")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        // Output file
        $outputFile = "$filename.xlsx";
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$outputFile\"");
        $writer->save('php://output');
    } catch (Exception $e) {
        echo 'Error generating XLSX: ' . $e->getMessage();
    }
} else {
    // Generate PDF
    class CustomPDF extends TCPDF
    {
        public function Header()
        {
            global $title;
            $this->SetFont('times', 'B', 20);
            $this->Cell(0, 15, $title, 0, 1, 'C');
            $this->Ln(12);
        }

        public function Footer()
        {
            $this->SetY(-15);
            $this->SetFont('helvetica', 'I', 8);
            $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'C');
        }
    }

    $pdf = new CustomPDF('L');
    $pdf->AddPage();
    $pdf->SetFont('times', '', 10);

    // Table header
    $pdf->SetFillColor(245, 245, 245);
    $pdf->SetTextColor(0);
    $pdf->SetFont('', 'B');
    $headers = ['Name', 'Date', 'Time In', 'Time Out', 'Age', 'Sex', 'Duration'];
    foreach ($headers as $header) {
        $pdf->Cell(40, 10, $header, 1, 0, 'C', true);
    }
    $pdf->Ln();

    // Table data
    $pdf->SetFont('', '');
    while ($row = $result->fetch_assoc()) {
        $duration = isset($row['time_out'])
            ? gmdate('H:i:s', strtotime($row['time_out']) - strtotime($row['time_in']))
            : '-';
        $pdf->Cell(40, 10, strtoupper($row['full_name'] ?? '-'));
        $pdf->Cell(40, 10, $row['log_date'] ?? '-');
        $pdf->Cell(40, 10, $row['time_in'] ?? '-');
        $pdf->Cell(40, 10, $row['time_out'] ?? '-');
        $pdf->Cell(40, 10, $row['age'] ?? '-');
        $pdf->Cell(40, 10, $row['sex_name'] ?? '-');
        $pdf->Cell(40, 10, $duration);
        $pdf->Ln();

        if ($visitorCount % 10 === 0) {
            $pdf->SetFont('', 'B');
            $pdf->Cell(280, 10, '', 0, 1, 'C');
            $pdf->SetFont('', '');
        }
    }

    $pdf->Output("$filename.pdf", 'D');
}
?>