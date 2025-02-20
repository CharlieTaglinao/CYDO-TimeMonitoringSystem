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
        sex.sex_name
    FROM visitors
        INNER JOIN time_logs ON visitors.id = time_logs.client_id
        INNER JOIN sex ON visitors.sex_id = sex.id";
    

$result = $conn->query($query);

if (!$result) {
    die("Error: " . $conn->error);
}

// Initialize TCPDF
class CustomPDF extends TCPDF {
    // Page header
    public function Header() {
        if ($this->PageNo() == 1) { 
            $this->SetFont('times', 'B', 20);
            $this->Cell(0, 15, 'Visitor Records Report', 0, 1, 'C', false, '', 0, false, 'T', 'M');
            $this->SetFont('times', '', 10); 
            $date = date('F j, Y, g:i A'); 
            $this->Cell(0, 0, 'Report Generated : ' . $date, 0, 1, 'C', false, '', 0, false, 'T', 'M');
            $this->Ln(8); 
            $this->Image('../../../assets/images/CYDO-LOGO.png', 92, 2, 15); 
            $this->Image('../../../assets/images/GENTRI-LOGO.jpeg', 190, 2, 15); 

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
$pdf->SetTitle('Visitor Report');
$pdf->SetSubject('Visitor Report');
$pdf->SetKeywords('TCPDF, PDF, visitor, report');

$pdf->AddPage();
$pdf->SetFont('times', '', 10);

$pdf->Ln(20);

$pdf->SetFillColor(245, 245, 245);
$pdf->SetTextColor(0);
$pdf->SetFont('', 'B');


$colWidths = [80, 30, 30, 20, 20, 20, 80]; 
$headers = ['Name', 'Date', 'Time In', 'Time Out', 'Age', 'Sex', 'Duration'];

$fill = false;
// Add headers
foreach ($headers as $i => $header) {
    $pdf->Cell($colWidths[$i], 10, $header, 1, 0, 'C', true);
}
$pdf->Ln();

// Table rows
while ($row = $result->fetch_assoc()) {
    $name = strtoupper($row['Full Name']);
    $date = isset($row['time_in']) ? date('Y-m-d', strtotime($row['time_in'])) : '-';
    $timeIn = isset($row['time_in']) ? date('H:i:s', strtotime($row['time_in'])) : '-';
    $timeOut = isset($row['time_out']) ? date('H:i:s', strtotime($row['time_out'])) : '-';
    $age = isset($row['age']) ? $row['age'] : '-';
    $sex = isset($row['sex_name']) ? $row['sex_name'] : '-';
    $duration = '-';

    if (isset($row['time_in'], $row['time_out'])) {
        $timeInObj = new DateTime($row['time_in']);
        $timeOutObj = new DateTime($row['time_out']);
        $interval = $timeInObj->diff($timeOutObj);
        $duration = $interval->format('%h hours %i minutes %s seconds');
    }

    // Add row
    $pdf->Cell($colWidths[0], 10, $name, 1, 0, 'L', $fill);
    $pdf->Cell($colWidths[1], 10, $date, 1, 0, 'C', $fill);
    $pdf->Cell($colWidths[2], 10, $timeIn, 1, 0, 'C', $fill);
    $pdf->Cell($colWidths[3], 10, $timeOut, 1, 0, 'C', $fill);
    $pdf->Cell($colWidths[4], 10, $age, 1, 0, 'C', $fill);
    $pdf->Cell($colWidths[5], 10, $sex, 1, 0, 'C', $fill);
    $pdf->Cell($colWidths[6], 10, $duration, 1, 1, 'C', $fill);

    $fill = !$fill;
}


$timestamp = date('Y-m-d_H-i-s'); 
$filename = "Visitor-Reports_{$timestamp}.pdf";

$pdf->Output($filename, 'D');
?>
