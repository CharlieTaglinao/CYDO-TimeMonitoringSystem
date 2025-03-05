<?php
// Fetch Visitor Records
require '../../../vendor/autoload.php';
require '../../../includes/database.php';

date_default_timezone_set('Asia/Manila');

$query = "
    SELECT 
        CONCAT(visitors.first_name, ' ', visitors.middle_name, ' ', visitors.last_name) AS `Full Name`, 
        time_logs.time_in, 
        time_logs.time_out,
        visitors.sex_id,
        office.office_name,
        purpose.purpose,
        barangays.barangay_name
    FROM visitors
        INNER JOIN time_logs ON visitors.id = time_logs.client_id
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
            $this->Cell(0, 15, 'Visitor Records Report', 0, 1, 'C');
            $this->SetFont('helvetica', '', 10);
            $date = date('F j, Y, g:i A');
            $this->Cell(0, 10, 'Report Generated : ' . $date, 0, 1, 'C');
            $this->Ln(8);
            $this->Image('../../../assets/images/CYDO-LOGO.png', 92, 5, 15);
            $this->Image('../../../assets/images/GENTRI-LOGO.jpeg', 191, 5, 15);
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
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 8);

$pdf->Ln(20); 

// Column headers
$headers = ['NAME', 'DATE', 'IN', 'OUT', 'OFFICE', 'PURPOSE', 'BARANGAY', 'DURATION'];
$widths = [62, 25, 20, 20, 65, 40, 25, 20]; 

// Header row
$pdf->SetFillColor(0, 0, 0); 
$pdf->SetTextColor(255, 255, 255); 
$pdf->SetFont('', 'B');
foreach ($headers as $key => $header) {
    $pdf->Cell($widths[$key], 10, $header, 1, 0, 'C', true);
}
$pdf->Ln();

// Reset text color for data rows
$pdf->SetTextColor(0, 0, 0);

// Data rows
$pdf->SetFont('', '');
$pdf->SetFillColor(245, 245, 245);
$fill = false;

while ($row = $result->fetch_assoc()) {
    $data = [
        strtoupper($row['Full Name']),
        isset($row['time_in']) ? date('Y-m-d', strtotime($row['time_in'])) : '-',
        isset($row['time_in']) ? date('H:i:s', strtotime($row['time_in'])) : '-',
        isset($row['time_out']) ? date('H:i:s', strtotime($row['time_out'])) : '-',
        $row['office_name'] ?? '-',
        $row['purpose'] ?? '-',
        $row['barangay_name'] ?? '-',
        isset($row['time_in'], $row['time_out'])
            ? (new DateTime($row['time_in']))->diff(new DateTime($row['time_out']))->format('%h:%i:%s')
            : '-'
    ];
    foreach ($data as $key => $value) {
        $alignment = $key == 0 ? 'L' : 'C';
        $pdf->Cell($widths[$key], 10, $value, 1, 0, $alignment, $fill);
    }
    $pdf->Ln();
    $fill = !$fill;
}

$filename = 'Visitor-Reports_' . date('Y-m-d_H-i-s') . '.pdf';
$pdf->Output($filename, 'D');
?>