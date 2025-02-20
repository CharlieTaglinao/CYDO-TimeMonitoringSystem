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
    class CustomPDF extends TCPDF {
        // Page header
        public function Header() {
            global $title;
            global $subtitle;
            
            if ($this->PageNo() == 1) { 
                 // Title
                $this->SetFont('times', 'B', 20);
                $this->Cell(0, 10, $title, 0, 1, 'C');
                $this->SetFont('times', '', 12);
                $this->Cell(0, 10, $subtitle, 0, 1, 'C');
                $this->Ln(4);
                $this->Image('../../../../assets/images/CYDO-LOGO.png', 92, 2, 15); 
                $this->Image('../../../../assets/images/GENTRI-LOGO.jpeg', 190, 2, 15); 
            }
        }
    
        // Page footer
        public function Footer() {
            $this->SetY(-15);
            $this->SetFont('helvetica', 'I', 8);
            $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'C');
        }
    }
    
    $pdf = new CustomPDF('L');
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetTitle($title);
    $pdf->SetMargins(10, 20, 10);
    $pdf->AddPage();
    $pdf->SetFont('times', '', 10);
    
    // Table Headers
    $headers = ['Name', 'Date', 'Time In', 'Time Out', 'Age', 'Sex', 'Duration'];
    $colWidths = [80, 30, 30, 30, 20, 20, 70];
    
    $pdf->SetFillColor(230, 230, 230);
    $pdf->SetTextColor(0);
    $pdf->SetDrawColor(50, 50, 50);
    $pdf->SetLineWidth(0.3);
    $pdf->SetFont('', 'B');
    
    // Header Row
    foreach ($headers as $i => $header) {
        $pdf->Cell($colWidths[$i], 10, $header, 1, 0, 'C', true);
    }
    $pdf->Ln();
    
    // Table Rows
    $pdf->SetFont('', '');
    $pdf->SetFillColor(245, 245, 245);
    $fill = false;
    while ($row = $result->fetch_assoc()) {
        $name = strtoupper($row['full_name'] ?? '-');
        $date = $row['log_date'] ?? '-';
        $timeIn = $row['time_in'] ?? '-';
        $timeOut = $row['time_out'] ?? '-';
        $age = $row['age'] ?? '-';
        $sex = $row['sex_name'] ?? '-';
        $duration = '-';
    
        if (isset($row['time_in'], $row['time_out'])) {
            $timeInObj = new DateTime($row['time_in']);
            $timeOutObj = new DateTime($row['time_out']);
            $interval = $timeInObj->diff($timeOutObj);
            $duration = $interval->format('%h hours %i minutes %s seconds');
        }
    
        $pdf->Cell($colWidths[0], 10, $name, 1, 0, 'L', $fill);
        $pdf->Cell($colWidths[1], 10, $date, 1, 0, 'C', $fill);
        $pdf->Cell($colWidths[2], 10, $timeIn, 1, 0, 'C', $fill);
        $pdf->Cell($colWidths[3], 10, $timeOut, 1, 0, 'C', $fill);
        $pdf->Cell($colWidths[4], 10, $age, 1, 0, 'C', $fill);
        $pdf->Cell($colWidths[5], 10, $sex, 1, 0, 'C', $fill);
        $pdf->Cell($colWidths[6], 10, $duration, 1, 1, 'C', $fill);
    
        $fill = !$fill;
    }
    
    
    // Output PDF
    $outputFile = "$filename.pdf";
    $pdf->Output($outputFile, 'D');
    }
    ?>
    