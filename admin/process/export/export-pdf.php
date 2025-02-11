<?php
require '../../../vendor/autoload.php';
require '../../../includes/database.php';

// Fetch Visitor Records
$query = "
    SELECT 
        CONCAT(visitors.first_name, ' ', visitors.middle_name, ' ', visitors.last_name) AS `Full Name`, 
        time_logs.time_in, 
        time_logs.time_out 
    FROM 
        visitors
    LEFT JOIN 
        time_logs 
    ON 
        visitors.id = time_logs.client_id 
    ORDER BY 
        time_logs.time_in DESC";

$result = $conn->query($query);

if (!$result) {
    die("Error: " . $conn->error);
}

// Initialize TCPDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle('Visitor Report');
$pdf->SetSubject('Visitor Report');
$pdf->SetKeywords('TCPDF, PDF, visitor, report');


$pdf->AddPage();

$pdf->SetFont('helvetica', '', 10);

$pdf->Cell(0, 10, 'Visitor Records Report', 0, 1, 'C');
$pdf->Ln(5);

// Add table header
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(50, 10, 'Name', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Date', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Time In', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Time Out', 1, 0, 'C', true);
$pdf->Cell(50, 10, 'Duration', 1, 1, 'C', true);


while ($row = $result->fetch_assoc()) {
    $name = strtoupper($row['Full Name']);
    $date = isset($row['time_in']) ? date('Y-m-d', strtotime($row['time_in'])) : 'N/A';
    $timeIn = isset($row['time_in']) ? date('H:i:s', strtotime($row['time_in'])) : 'N/A';
    $timeOut = isset($row['time_out']) ? date('H:i:s', strtotime($row['time_out'])) : 'N/A';
    $duration = 'N/A';

    if (isset($row['time_in'], $row['time_out'])) {
        $timeInObj = new DateTime($row['time_in']);
        $timeOutObj = new DateTime($row['time_out']);
        $interval = $timeInObj->diff($timeOutObj);
        $duration = $interval->format('%h hours %i minutes %s seconds');
    }

    // Add row to PDF
    $pdf->Cell(50, 10, $name, 1);
    $pdf->Cell(30, 10, $date, 1);
    $pdf->Cell(30, 10, $timeIn, 1);
    $pdf->Cell(30, 10, $timeOut, 1);
    $pdf->Cell(50, 10, $duration, 1, 1);
}

// Output the PDF
$pdf->Output('visitor_report.pdf', 'D');
?>
