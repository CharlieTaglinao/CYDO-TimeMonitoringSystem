<?php

require '../../../vendor/autoload.php';
require '../../../includes/database.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

date_default_timezone_set('Asia/Manila');
ob_start();

$type = isset($_GET['type']) ? $_GET['type'] : 'today';
$format = isset($_GET['format']) ? $_GET['format'] : 'pdf';
$customStartDate = isset($_POST['startDate']) ? $_POST['startDate'] : null;
$customEndDate = isset($_POST['endDate']) ? $_POST['endDate'] : null;

if ($type === 'month') {
    $startDate = date('Y-m-01');
    $endDate = date('Y-m-t');
    $title = "Time logs Report";
    $subtitle = date('F 1, Y', strtotime($startDate)) . " to " . date('F t, Y', strtotime($endDate));
    $filename = date('F-1-Y', strtotime($startDate)) . '-to-' . date('F-t-Y', strtotime($endDate)) . '-Visitors-Report';
} else if ($type === 'custom' && $customStartDate && $customEndDate) {
    $startDate = $customStartDate;
    $endDate = $customEndDate;
    $title = "Time logs Report";
    $subtitle = date('F j, Y', strtotime($startDate)) . " to " . date('F j, Y', strtotime($endDate));
    $filename = date('F-j-Y', strtotime($startDate)) . '-to-' . date('F-j-Y', strtotime($endDate)) . '-Visitors-Report';
} else {
    $startDate = $endDate = date('Y-m-d');
    $title = "Time logsReport";
    $subtitle = date('F j, Y', strtotime($startDate));
    $filename = date('F-j-Y', strtotime($startDate)) . '-Visitors-Report';
}

// Update the query to fetch all data without filtering by date
$query = "
    SELECT 
        CONCAT(visitors.first_name, ' ', visitors.middle_name, ' ', visitors.last_name) AS full_name,
        DATE(time_logs.time_in) AS log_date,
        DATE_FORMAT(time_logs.time_in, '%h:%i:%s %p') AS time_in,
        DATE_FORMAT(time_logs.time_out, '%h:%i:%s %p') AS time_out,
        TIME_FORMAT(TIMEDIFF(time_logs.time_out, time_logs.time_in), '%H:%i:%s') AS duration,
        office.office_name,
        purpose.purpose,
        barangays.barangay_name,
        time_logs.status
    FROM 
        time_logs
    INNER JOIN visitors ON time_logs.client_id = visitors.id
    INNER JOIN office ON visitors.office_id = office.id
    INNER JOIN purpose ON visitors.purpose_id = purpose.client_id
    INNER JOIN barangays ON visitors.barangay_id = barangays.id
    ";

$result = $conn->query($query);
if (!$result) {
    error_log('Query Error: ' . $conn->error);
    die('Error fetching records. Please contact the administrator.');
}

if ($result->num_rows === 0) {
    die('No records found.');
}

require '../../../vendor/tecnickcom/tcpdf/tcpdf.php';

class CustomPDF extends TCPDF
{
    public function Header()
    {
        global $title, $subtitle;
        if ($this->PageNo() == 1) {
            $this->Ln(5);
            $this->SetFont('helvetica', 'B', 20);
            $this->Cell(0, 15, $title, 0, 1, 'C', false, '', 0, false, 'T', 'M');
            $this->SetFont('helvetica', '', 10);
            $date = date('F j, Y, g:i A');
            $this->Cell(0, 0, 'Report Generated : ' . $date, 0, 1, 'C', false, '', 0, false, 'T', 'M');
            $this->Ln(8);
            $this->Image('../../../assets/images/CYDO-LOGO.png', 94, 10, 15);
            $this->Image('../../../assets/images/GENTRI-LOGO.jpeg', 191, 10, 15);
        }
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'C');
    }
}

$pdf = new CustomPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle($title);
$pdf->SetMargins(8, 20, 5); 
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 8);

$pdf->Ln(20); 

$headers = ['NAME', 'DATE', 'IN', 'OUT', 'OFFICE', 'PURPOSE', 'BARANGAY', 'DURATION', 'STATUS'];
$widths = [59, 22, 19, 19, 63, 34, 25, 20, 20]; 

// Header row
$pdf->SetFillColor(0, 0, 0);
$pdf->SetTextColor(255, 255, 255); 
$pdf->SetFont('helvetica', 'B');
foreach ($headers as $key => $header) {
    $pdf->Cell($widths[$key], 10, $header, 1, 0, 'C', true);
}
$pdf->Ln();

// Reset text color for data rows
$pdf->SetTextColor(0, 0, 0);

// Data rows
$pdf->SetFont('helvetica', '');
$pdf->SetFillColor(245, 245, 245);
$fill = false;

while ($row = $result->fetch_assoc()) {
    // Check if a new page is needed
    if ($pdf->GetY() > 180) {
        $pdf->AddPage();
    }

    $duration = isset($row['time_in'], $row['time_out'])
        ? (new DateTime($row['time_in']))->diff(new DateTime($row['time_out']))->format('%H:%I:%S')
        : '00:00:00';

    $data = [
        strtoupper($row['full_name']),
        isset($row['log_date']) ? $row['log_date'] : '-',
        isset($row['time_in']) ? $row['time_in'] : '-',
        isset($row['time_out']) ? $row['time_out'] : '-',
        $row['office_name'] ?? '-',
        $row['purpose'] ?? '-',
        $row['barangay_name'] ?? '-',
        $duration
    ];
    foreach ($data as $key => $value) {
        $alignment = $key == 0 ? 'L' : 'C';
        $pdf->MultiCell($widths[$key], 10, $value, 1, $alignment, $fill, 0, '', '', true);
    }
    // Apply red color to status if "Auto Logout" and green if "User Logout"
    if ($row['status'] == 'Auto Logout') {
        $pdf->SetTextColor(255, 0, 0); // Red
    } else {
        $pdf->SetTextColor(0, 128, 0); // Green
    }
    $pdf->MultiCell($widths[8], 10, wordwrap($row['status'] ?? 'User Logout', 15, "\n", true), 1, 'C', $fill, 0, '', '', true);
    $pdf->SetTextColor(0, 0, 0); // Reset text color
    $pdf->Ln();
    $fill = !$fill;
}

$filename = $filename . '.pdf';
$pdf->Output($filename, 'D');

?>