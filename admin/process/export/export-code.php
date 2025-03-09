<?php
require '../../../vendor/autoload.php';
require '../../../includes/database.php';

date_default_timezone_set('Asia/Manila');

// Fetch Visitor Records
$query = "
    SELECT 
        CONCAT(visitors.first_name, ' ', visitors.middle_name, ' ', visitors.last_name) AS `Full Name`, 
        sex.sex_name,
        time_logs.code,
        time_logs.time_out
    FROM visitors
        INNER JOIN time_logs ON visitors.id = time_logs.client_id
        INNER JOIN sex ON visitors.sex_id = sex.id";

$result = $conn->query($query);

if (!$result) {
    die("Error: " . $conn->error);
}

// Initialize TCPDF
class CustomPDF extends TCPDF {
    public function Header() {
        if ($this->PageNo() == 1) { 
            $this->SetFont('helvetica', 'B', 20);
            $this->Cell(0, 15, 'Visitor Codes Report', 0, 1, 'C', false, '', 0, false, 'T', 'M');
            $this->SetFont('helvetica', '', 10); 
            $date = date('F j, Y, g:i A'); 
            $this->Cell(0, 0, 'Report Generated : ' . $date, 0, 1, 'C', false, '', 0, false, 'T', 'M');
            $this->Ln(8); 
            $this->Image('../../../assets/images/CYDO-LOGO.png', 92, 2, 15); 
            $this->Image('../../../assets/images/GENTRI-LOGO.jpeg', 190, 2, 15); 
        }
    }

    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'C');
    }
}

$pdf = new CustomPDF('L'); 
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle('Visitor Codes Report');
$pdf->SetSubject('Visitor Report');
$pdf->SetKeywords('TCPDF, PDF, visitor, report');

$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);

$pdf->Ln(20);

$pdf->SetFillColor(245, 245, 245);
$pdf->SetTextColor(0);
$pdf->SetFont('', 'B');

// Calculate column widths dynamically
$pageWidth = $pdf->GetPageWidth() - $pdf->GetX() - $pdf->getMargins()['right'];
$fixedWidths = [50, 50, 50]; // Adjusted to match the number of headers
$nameColWidth = $pageWidth - array_sum($fixedWidths);
$colWidths = [$nameColWidth, $fixedWidths[0], $fixedWidths[1], $fixedWidths[2]]; // Ensure this matches the number of headers

$headers = ['NAME', 'SEX', 'CODE', 'LAST USED'];
$data = [];

while ($row = $result->fetch_assoc()) {
    $rowData = [
        strtoupper($row['Full Name']),
        strtoupper($row['sex_name']),
        strtoupper($row['code'] ?? 'CODE USED'),
        strtoupper($row['time_out'] ?? 'NOT USED')
    ];
    $data[] = $rowData;
}

// Render headers
$pdf->SetFillColor(0, 0, 0);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('', 'B', 12);
foreach ($headers as $i => $header) {
    $pdf->Cell($colWidths[$i] ?? 0, 10, $header, 1, 0, 'C', true);
}
$pdf->Ln();

// Render data
$pdf->SetFillColor(245, 245, 245);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('', '');
$fill = false;
foreach ($data as $rowData) {
    foreach ($rowData as $i => $value) {
        $alignment = ($i === 0) ? 'L' : 'C';

        if ($value === 'CODE USED') {
            $pdf->SetTextColor(255, 0, 0);
        } else {
            $pdf->SetTextColor(0, 0, 0);
        }
        $pdf->Cell($colWidths[$i] ?? 0, 10, $value ?? '', 1, 0, $alignment, $fill);
    }
    $pdf->Ln();
    $fill = !$fill;
}

$timestamp = date('Y-m-d_H-i-s'); 
$filename = "All_Codes_$timestamp.pdf";

$pdf->Output($filename, 'D');
?>