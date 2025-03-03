<?php
require '../../../vendor/autoload.php';
require '../../../includes/database.php';

date_default_timezone_set('Asia/Manila');

// Fetch Visitor Records
$query = "
    SELECT 
        CONCAT(visitors.first_name, ' ', visitors.middle_name, ' ', visitors.last_name) AS `Full Name`, 
        time_logs.time_in, 
        time_logs.time_out,
        visitors.age,
        visitors.sex_id,
        sex.sex_name,
        office.office_name,
        purpose.purpose,
        barangays.barangay_name
    FROM visitors
        INNER JOIN time_logs ON visitors.id = time_logs.client_id
        INNER JOIN sex ON visitors.sex_id = sex.id
        INNER JOIN office ON visitors.office_id = office.id
        INNER JOIN purpose ON visitors.purpose_id = purpose.client_id
        INNER JOIN barangays ON visitors.barangay_id = barangays.id";

$result = $conn->query($query);

if (!$result) {
    die("Error: " . $conn->error);
}

// Initialize TCPDF
class CustomPDF extends TCPDF {
    public function Header() {
        if ($this->PageNo() == 1) { 
            $this->SetFont('helvetica', 'B', 20);
            $this->Cell(0, 15, 'Visitor Records Report', 0, 1, 'C', false, '', 0, false, 'T', 'M');
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
$pdf->SetTitle('Visitor Report');
$pdf->SetSubject('Visitor Report');
$pdf->SetKeywords('TCPDF, PDF, visitor, report');

$pdf->AddPage();
$pdf->SetFont('helvetica', '', 8);

$pdf->Ln(20);

$pdf->SetFillColor(245, 245, 245);
$pdf->SetTextColor(0);
$pdf->SetFont('', 'B');

$colWidths = [50, 30, 30, 20, 20, 30, 40, 30, 40]; 
$headers = ['NAME', 'DATE', 'IN', 'OUT', 'AGE', 'SEX', 'OFFICE', 'PURPOSE', 'BARANGAY', 'DURATION'];
$data = [];
$maxWidths = array_fill(0, count($headers), 0);

while ($row = $result->fetch_assoc()) {
    $rowData = [
        strtoupper($row['Full Name']),
        isset($row['time_in']) ? date('Y-m-d', strtotime($row['time_in'])) : '-',
        isset($row['time_in']) ? date('H:i:s', strtotime($row['time_in'])) : '-',
        isset($row['time_out']) ? date('H:i:s', strtotime($row['time_out'])) : '-',
        isset($row['age']) ? $row['age'] : '-',
        isset($row['sex_name']) ? $row['sex_name'] : '-',
        isset($row['office_name']) ? $row['office_name'] : '-',
        isset($row['purpose']) ? $row['purpose'] : '-',
        isset($row['barangay_name']) ? $row['barangay_name'] : '-',
        isset($row['time_in'], $row['time_out']) 
            ? (new DateTime($row['time_in']))->diff(new DateTime($row['time_out']))->format('%h:%i:%s') : '-'
    ];
    $data[] = $rowData;

    foreach ($rowData as $i => $value) {
        $maxWidths[$i] = max($maxWidths[$i], $pdf->GetStringWidth($value) + 4);
    }
}

foreach ($headers as $i => $header) {
    $maxWidths[$i] = max($maxWidths[$i], $pdf->GetStringWidth($header) + 4);
}

$pdf->SetFillColor(0, 0, 0);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('', 'B',8);
foreach ($headers as $i => $header) {
    $pdf->Cell($maxWidths[$i], 10, $header, 1, 0, 'C', true);
}
$pdf->Ln();

$pdf->SetFillColor(245, 245, 245);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('', '');
$fill = false;
foreach ($data as $rowData) {
    foreach ($rowData as $i => $value) {
        $alignment = ($i === 0) ? 'L' : 'C';
        $pdf->Cell($maxWidths[$i], 10, $value, 1, 0, $alignment, $fill);
    }
    $pdf->Ln();
    $fill = !$fill;
}

$timestamp = date('Y-m-d_H-i-s'); 
$filename = "Visitor-Reports_{$timestamp}.pdf";

$pdf->Output($filename, 'D');
?>
