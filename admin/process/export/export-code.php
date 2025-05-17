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
                        $this->Ln(5);
                        $this->SetFont('helvetica', '', 10);

                        // Center the logo at the top, but move CH-LOGO.png slightly to the right
                        $pageWidth = $this->getPageWidth();
                        $logoWidth = 80; // width in mm
                        $logoX = ($pageWidth - $logoWidth) / 2 - 10; // Move 10mm to the right
                        @$this->Image('../../../assets/images/CH-LOGO.png', $logoX, 10, $logoWidth, 20); // Adjusted logo position
                        $this->Image('../../../assets/images/GENTRI-LOGO.jpeg', $logoX + 90, 10, $logoWidth - 60, 20); // Centered logo
                        $this->Ln(25); // Add space below the logo
                        
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
$pdf->SetTitle('VISITOR CODES');
$pdf->SetSubject('VISITOR CODES');
$pdf->SetKeywords('TCPDF, PDF, visitor, report');

$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);

$pdf->Ln(30); // Add space to avoid overlap with header

// Define headers and guides
$headers = ['NAME', 'SEX', 'CODE', 'LAST USED'];
$guides = [
    '(First Name, Middle Name, Surname)',
    '(Male/Female)',
    '(Code)',
    '(YYYY-MM-DD HH:MM:SS)'
];
$colWidths = [108, 60, 60, 60]; // Total is 288

// Calculate table width and center
$tableWidth = array_sum($colWidths);
$margins = $pdf->getMargins();
$pageWidth = $pdf->getPageWidth() - $margins['left'] - $margins['right'];
$tableStartX = $margins['left'] + (($pageWidth - $tableWidth) / 2);

// Header row
$pdf->SetFillColor(0, 0, 0);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('helvetica', 'B', 12);
$x = $tableStartX;
$y = $pdf->GetY();
for ($i = 0; $i < count($headers); $i++) {
    $pdf->SetXY($x, $y);
    $pdf->MultiCell($colWidths[$i], 10, $headers[$i], 0, 'C', true, 0);
    $x += $colWidths[$i];
}
// Guides row
$pdf->SetFont('helvetica', 'I', 8);
$pdf->SetTextColor(200, 200, 200);
$x = $tableStartX;
$y = $y + 10;
for ($i = 0; $i < count($guides); $i++) {
    $pdf->SetXY($x, $y);
    $pdf->MultiCell($colWidths[$i], 6, $guides[$i], 0, 'C', true, 0);
    $x += $colWidths[$i];
}
$pdf->Ln();
// Draw header bottom line
$pdf->SetDrawColor(0,0,0);
$pdf->Line($tableStartX, $pdf->GetY(), $tableStartX + $tableWidth, $pdf->GetY());
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('helvetica', '', 11);

// Prepare data
$data = [];
while ($row = $result->fetch_assoc()) {
    $lastUsed = isset($row['time_out']) && $row['time_out'] !== null && $row['time_out'] !== '' ? strtoupper($row['time_out']) : '-';
    $codeValue = ($lastUsed !== '-') ? 'CODE USED' : (isset($row['code']) ? strtoupper($row['code']) : 'CODE USED');
    $rowData = [
        strtoupper($row['Full Name']),
        strtoupper($row['sex_name']),
        $codeValue,
        $lastUsed
    ];
    $data[] = $rowData;
}

// Render data rows
$fillColors = [[255,255,255],[240,240,240]];
$fill = 0;
foreach ($data as $rowData) {
    $cellHeights = [];
    for ($i = 0; $i < count($rowData); $i++) {
        $cellHeights[$i] = $pdf->getStringHeight($colWidths[$i] - 2, $rowData[$i]) + 2;
    }
    $maxHeight = max(max($cellHeights), 12);
    $x = $tableStartX;
    $y = $pdf->GetY();
    // Check for page overflow
    if ($y + $maxHeight > ($pdf->getPageHeight() - $pdf->getMargins()['bottom'] - 15)) {
        $pdf->AddPage();
        $x = $tableStartX;
        $y = $pdf->GetY();
    }
    $pdf->SetFillColor($fillColors[$fill][0], $fillColors[$fill][1], $fillColors[$fill][2]);
    $pdf->setCellPaddings(3, 2, 3, 2);
    for ($i = 0; $i < count($rowData); $i++) {
        $pdf->SetXY($x, $y);
        $pdf->SetFont('helvetica', 'B', 11);
        if ($i == 2 && $rowData[2] === 'CODE USED') {
            $pdf->SetTextColor(255, 0, 0);
        } else {
            $pdf->SetTextColor(0, 0, 0);
        }
        $align = ($i == 0) ? 'L' : 'C';
        $pdf->MultiCell($colWidths[$i], $maxHeight, $rowData[$i], 0, $align, true, 0);
        $x += $colWidths[$i];
    }
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 11);
    $pdf->SetY($y + $maxHeight);
    $fill = 1 - $fill;
}
$pdf->setCellPaddings(0, 0, 0, 0);

$timestamp = date('Y-m-d_H-i-s'); 
$filename = "All_Codes_$timestamp.pdf";

$pdf->Output($filename, 'D');
?>