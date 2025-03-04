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
$customStartDate = isset($_POST['startDate']) ? $_POST['startDate'] : null;
$customEndDate = isset($_POST['endDate']) ? $_POST['endDate'] : null;

if ($type === 'month') {
    $startDate = date('Y-m-01');
    $endDate = date('Y-m-t');
    $title = "Visitor Records Report";
    $subtitle = date('F 1, Y', strtotime($startDate)) . " to " . date('F t, Y', strtotime($endDate));
    $filename = "Visitor_1_Month_Report_" . date('Y-m');
} else if ($type === 'custom' && $customStartDate && $customEndDate) {
    $startDate = $customStartDate;
    $endDate = $customEndDate;
    $title = "Visitor Records Report";
    $subtitle = date('F j, Y', strtotime($startDate)) . " to " . date('F j, Y', strtotime($endDate));
    $filename = "Visitor_Custom_Range_Report_" . date('Ymd', strtotime($startDate)) . "_to_" . date('Ymd', strtotime($endDate));
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
        TIME_FORMAT(TIMEDIFF(time_logs.time_out, time_logs.time_in), '%H:%i:%s') AS duration,
        office.office_name,
        purpose.purpose,
        barangays.barangay_name
    FROM 
        time_logs
    INNER JOIN visitors ON time_logs.client_id = visitors.id
    INNER JOIN office ON visitors.office_id = office.id
    INNER JOIN purpose ON visitors.purpose_id = purpose.client_id
    INNER JOIN barangays ON visitors.barangay_id = barangays.id
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
        $headers = ['NAME', 'DATE', 'IN', 'OUT', 'OFFICE', 'PURPOSE', 'BARANGAY', 'DURATION'];
        $sheet->fromArray($headers, NULL, 'A1');

        // Style headers
        $sheet->getStyle('A1:H1')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1c1c1c']],
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Adjust column widths
        $columns = ['A' => 40, 'B' => 15, 'C' => 15, 'D' => 15, 'E' => 35, 'F' => 20, 'G' => 20, 'H' => 15];
        foreach ($columns as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        // Set page layout to fit to 1 page wide and 1 page tall
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(1);

        $rowNumber = 2;
        $dataCount = 0;
        while ($row = $result->fetch_assoc()) {
            $sheet->fromArray([
                $row['full_name'] ?? '-',
                $row['log_date'] ?? '-',
                $row['time_in'] ?? '-',
                $row['time_out'] ?? '-',
                $row['office_name'] ?? '-',
                $row['purpose'] ?? '-',
                $row['barangay_name'] ?? '-',
                $row['duration'] ?? '-',
            ], NULL, "A$rowNumber");
            $rowNumber++;
            $dataCount++;
            
            if ($dataCount % 10 === 0) {
                $sheet->mergeCells("A$rowNumber:H$rowNumber");
                $sheet->setCellValue("A$rowNumber", '<' . str_repeat('-', 120) . ' BREAK ' . str_repeat('-', 120) . '>');
                $sheet->getStyle("A$rowNumber")->getFont()->setBold(true);
                $rowNumber++;
            }
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
// START FOR EXPORTING IN PDF FILE FOR BOTH TODAY,1MONTH REPORT
} else {
    require '../../../../vendor/tecnickcom/tcpdf/tcpdf.php';

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
                $this->Image('../../../../assets/images/CYDO-LOGO.png', 92, 5, 15);
                $this->Image('../../../../assets/images/GENTRI-LOGO.jpeg', 190, 5, 15);
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
    $pdf->SetMargins(10, 20, 10);
    $pdf->AddPage();

    $headers = ['NAME', 'DATE', 'IN', 'OUT', 'OFFICE', 'PURPOSE', 'BARANGAY', 'DURATION'];
    $data = [];
    $maxWidths = array_fill(0, count($headers), 0);

    $fixedWidths = [
        'NAME' => 70,
        'OFFICE' => 70
    ];

    while ($row = $result->fetch_assoc()) {
        $rowData = [
            strtoupper($row['full_name']),
            $row['log_date'] ?? '-',
            $row['time_in'] ?? '-',
            $row['time_out'] ?? '-',
            $row['office_name'] ?? '-',
            $row['purpose'] ?? '-',
            $row['barangay_name'] ?? '-',
            $row['duration'] ?? '-'
        ];
        $data[] = $rowData;

        foreach ($rowData as $i => $value) {
            if (!isset($fixedWidths[$headers[$i]])) {
                $maxWidths[$i] = max($maxWidths[$i], $pdf->GetStringWidth($value) + 6);
            }
        }
    }

    foreach ($headers as $i => $header) {
        if (!isset($fixedWidths[$header])) {
            $maxWidths[$i] = max($maxWidths[$i], $pdf->GetStringWidth($header) + 6);
        }
    }

    $pdf->SetFillColor(0, 0, 0);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('', 'B', 9);
    foreach ($headers as $i => $header) {
        $width = isset($fixedWidths[$header]) ? $fixedWidths[$header] : $maxWidths[$i];
        $pdf->MultiCell($width, 9, $header, 1, 'C', true, 0);
    }
    $pdf->Ln();

    $pdf->SetFillColor(245, 245, 245);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('', '');
    $fill = false;
    foreach ($data as $rowData) {
        foreach ($rowData as $i => $value) {
            $alignment = ($i === 0) ? 'L' : 'C';
            $width = isset($fixedWidths[$headers[$i]]) ? $fixedWidths[$headers[$i]] : $maxWidths[$i];
            $pdf->MultiCell($width, 10, $value, 1, $alignment, $fill, 0);
        }
        $pdf->Ln();
        $fill = !$fill;
    }

    $pdf->Output("$filename.pdf", 'D');
}

?>