<?php

        require '../../../../vendor/autoload.php';
        require '../../../../includes/database.php';
        use PhpOffice\PhpSpreadsheet\Spreadsheet;
        use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
        use PhpOffice\PhpSpreadsheet\Style\Alignment;
        use PhpOffice\PhpSpreadsheet\Style\Fill;
        use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

        session_start();

        date_default_timezone_set('Asia/Manila');
        ob_start();

        $type = isset($_GET['type']) ? $_GET['type'] : 'today';
        $format = isset($_GET['format']) ? $_GET['format'] : 'pdf';
        $customStartDate = isset($_POST['startDate']) ? $_POST['startDate'] : null;
        $customEndDate = isset($_POST['endDate']) ? $_POST['endDate'] : null;

        if ($type === 'custom' && (!$customStartDate || !$customEndDate)) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                // If the request is an AJAX (fetch) request, return a JSON response
                http_response_code(400);
                echo json_encode(['error' => 'Please provide both start and end dates for custom reports.']);
                exit;
            } else {
                // Otherwise, redirect as usual
                $_SESSION['message'] = 'Please provide both start and end dates for custom reports.';
                $_SESSION['message_type'] = 'danger';
                header('Location: ../../../report.php');
                exit;
            }
        }

        if ($type === 'month') {
            $startDate = date('Y-m-01');
            $endDate = date('Y-m-t');
            $title = "TIMELOGS REPORT";
            $subtitle = date('F 1, Y', strtotime($startDate)) . " to " . date('F t, Y', strtotime($endDate));
            $filename = date('F-1-Y', strtotime($startDate)) . '-to-' . date('F-t-Y', strtotime($endDate)) . '-TimeLogs-Report';
        } else if ($type === 'custom' && $customStartDate && $customEndDate) {
            $startDate = $customStartDate;
            $endDate = $customEndDate;
            $title = "TIMELOGS REPORT";
            $subtitle = date('F j, Y', strtotime($startDate)) . " to " . date('F j, Y', strtotime($endDate));
            $filename = date('F-j-Y', strtotime($startDate)) . '-to-' . date('F-j-Y', strtotime($endDate)) . '-TimeLogs-Report';
        } else {
            $startDate = $endDate = date('Y-m-d');
            $title = "TIMELOGS REPORT";
            $subtitle = date('F j, Y', strtotime($startDate));
            $filename = date('F-j-Y', strtotime($startDate)) . '-TimeLogs-Report';
        }

        if ($type === 'today') {
            $query = "
                SELECT 
                    CONCAT(visitors.first_name, ' ', visitors.middle_name, ' ', visitors.last_name) AS full_name,
                    DATE(time_logs.time_in) AS log_date,
                    DATE_FORMAT(time_logs.time_in, '%h:%i:%s %p') AS time_in,
                    DATE_FORMAT(time_logs.time_out, '%h:%i:%s %p') AS time_out,
                    TIME_FORMAT(TIMEDIFF(time_logs.time_out, time_logs.time_in), '%H:%i:%s') AS duration,
                    (
                        SELECT p2.purpose FROM purpose p2
                        WHERE p2.client_id = visitors.id
                        AND p2.id = (
                            SELECT MAX(p3.id) FROM purpose p3
                            WHERE p3.client_id = visitors.id
                            AND p3.id <= time_logs.id
                        )
                    ) as purpose,
                    barangays.barangay_name,
                    visitor_school_name.school_name,
                    visitors.type,
                    time_logs.status
                FROM 
                    visitors
                LEFT JOIN time_logs ON time_logs.client_id = visitors.id AND DATE(time_logs.time_in) = '$startDate'
                LEFT JOIN barangays ON visitors.barangay_id = barangays.id
                LEFT JOIN visitor_school_name ON visitors.school_id = visitor_school_name.id
                WHERE DATE(time_logs.time_in) = '$startDate'
            ";
        } else if ($type === 'custom' && $customStartDate && $customEndDate) {
            $query = "
                SELECT 
                    CONCAT(visitors.first_name, ' ', visitors.middle_name, ' ', visitors.last_name) AS full_name,
                    DATE(time_logs.time_in) AS log_date,
                    DATE_FORMAT(time_logs.time_in, '%h:%i:%s %p') AS time_in,
                    DATE_FORMAT(time_logs.time_out, '%h:%i:%s %p') AS time_out,
                    TIME_FORMAT(TIMEDIFF(time_logs.time_out, time_logs.time_in), '%H:%i:%s') AS duration,
                    (
                        SELECT p2.purpose FROM purpose p2
                        WHERE p2.client_id = visitors.id
                        AND p2.id = (
                            SELECT MAX(p3.id) FROM purpose p3
                            WHERE p3.client_id = visitors.id
                            AND p3.id <= time_logs.id
                        )
                    ) as purpose,
                    barangays.barangay_name,
                    visitor_school_name.school_name,
                    visitors.type,
                    time_logs.status
                FROM 
                    visitors
                LEFT JOIN time_logs ON time_logs.client_id = visitors.id AND DATE(time_logs.time_in) BETWEEN '$startDate' AND '$endDate'
                LEFT JOIN barangays ON visitors.barangay_id = barangays.id
                LEFT JOIN visitor_school_name ON visitors.school_id = visitor_school_name.id
                WHERE DATE(time_logs.time_in) BETWEEN '$startDate' AND '$endDate'
            ";
        } else {
            $query = "
                SELECT 
                    CONCAT(visitors.first_name, ' ', visitors.middle_name, ' ', visitors.last_name) AS full_name,
                    DATE(time_logs.time_in) AS log_date,
                    DATE_FORMAT(time_logs.time_in, '%h:%i:%s %p') AS time_in,
                    DATE_FORMAT(time_logs.time_out, '%h:%i:%s %p') AS time_out,
                    TIME_FORMAT(TIMEDIFF(time_logs.time_out, time_logs.time_in), '%H:%i:%s') AS duration,
                    (
                        SELECT p2.purpose FROM purpose p2
                        WHERE p2.client_id = visitors.id
                        AND p2.id = (
                            SELECT MAX(p3.id) FROM purpose p3
                            WHERE p3.client_id = visitors.id
                            AND p3.id <= time_logs.id
                        )
                    ) as purpose,
                    barangays.barangay_name,
                    visitor_school_name.school_name,
                    visitors.type,
                    time_logs.status
                FROM 
                    visitors
                LEFT JOIN time_logs ON time_logs.client_id = visitors.id AND DATE(time_logs.time_in) BETWEEN '$startDate' AND '$endDate'
                LEFT JOIN barangays ON visitors.barangay_id = barangays.id
                LEFT JOIN visitor_school_name ON visitors.school_id = visitor_school_name.id
                WHERE (DATE(time_logs.time_in) BETWEEN '$startDate' AND '$endDate' OR time_logs.time_in IS NULL)
            ";
        }

        $result = $conn->query($query);
        if (!$result) {
            error_log('Query Error: ' . $conn->error);
            $_SESSION['message'] = 'Error fetching records. Please contact the administrator.';
            $_SESSION['message_type'] = 'danger';
            header('Location: ../../../report.php');
            exit;
        }

        if ($result->num_rows === 0) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                // If the request is an AJAX (fetch) request, return a JSON response
                http_response_code(404);
                echo json_encode(['error' => 'No records found for the selected period.']);
                exit;
            } else {
                // Otherwise, redirect as usual
                $_SESSION['message'] = 'No records found for the selected period.';
                $_SESSION['message_type'] = 'warning';
                header('Location: ../../../report.php');
                exit;
            }
        }

        if ($format === 'xlsx') {
            try {
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                $headers = ['NAME', 'DATE', 'IN', 'OUT', 'PURPOSE', 'BARANGAY', 'SCHOOL', 'TYPE', 'DURATION', 'STATUS'];
                $sheet->fromArray($headers, NULL, 'A1');

                // Style headers
                $sheet->getStyle('A1:J1')->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1c1c1c']],
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Adjust column widths
                $columns = ['A' => 55, 'B' => 22, 'C' => 22, 'D' => 22, 'E' => 40, 'F' => 32, 'G' => 32, 'H' => 18, 'I' => 20, 'J' => 22];
                foreach ($columns as $col => $width) {
                    $sheet->getColumnDimension($col)->setWidth($width);
                }

                // Set page layout to fit to 1 page wide and 1 page tall
                $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(1);

                $rowNumber = 2;
                while ($row = $result->fetch_assoc()) {
                    $sheet->fromArray([
                        wordwrap($row['full_name'] ?? '-', 15, "\n", true),
                        wordwrap($row['log_date'] ?? '-', 15, "\n", true),
                        wordwrap($row['time_in'] ?? '-', 15, "\n", true),
                        wordwrap($row['time_out'] ?? '-', 15, "\n", true),
                        wordwrap($row['purpose'] ?? '-', 15, "\n", true),
                        wordwrap($row['barangay_name'] ?? '-', 15, "\n", true),
                        wordwrap($row['school_name'] ?? '-', 15, "\n", true),
                        wordwrap($row['type'] ?? '-', 15, "\n", true),
                        wordwrap($row['duration'] ?? '-', 15, "\n", true),
                        wordwrap($row['status'] ?? 'User Logout', 15, "\n", true)
                    ], NULL, "A$rowNumber");

                    // Apply red color to status if "Auto Logout" and green if "User Logout"
                    if ($row['status'] == 'Auto Logout') {
                        $sheet->getStyle("J$rowNumber")->getFont()->getColor()->setRGB('FF0000');
                    } else {
                        $sheet->getStyle("J$rowNumber")->getFont()->getColor()->setRGB('008000');
                    }

                    $rowNumber++;
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
                ob_clean(); // Clear any output buffer to prevent corruption
                ob_end_clean(); // End the output buffer
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            } catch (Exception $e) {
                error_log('XLSX Generation Error: ' . $e->getMessage());
                die('Error generating Excel file.');
            }
        } else {
            require '../../../../vendor/tecnickcom/tcpdf/tcpdf.php';

            class CustomPDF extends TCPDF
            {
                public function Header()
                {
                    global $title, $subtitle;
                    if ($this->PageNo() == 1) {
                        $this->Ln(5);
                        $this->SetFont('helvetica', '', 10);

                        // Center the logo at the top, but move CH-LOGO.png slightly to the right
                        $pageWidth = $this->getPageWidth();
                        $logoWidth = 80; // width in mm
                        $logoX = ($pageWidth - $logoWidth) / 2 - 10; // Move 10mm to the right
                        @$this->Image('../../../../assets/images/CH-LOGO.png', $logoX, 10, $logoWidth, 20); // Adjusted logo position
                        $this->Image('../../../../assets/images/GENTRI-LOGO.jpeg', $logoX + 90, 10, $logoWidth - 60, 20); // Centered logo
                        $this->Ln(25); // Add space below the logo
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
            // Adjust margins to reduce left and right margins
            $pdf->SetMargins(8, 20, 5); 
            $pdf->AddPage();
            $pdf->SetFont('helvetica', 'B', 9);

            $pdf->Ln(20); // Add space to avoid overlap with header

            $headers = ['NAME', 'DATE', 'IN', 'OUT', 'PURPOSE', 'BARANGAY', 'SCHOOL', 'TYPE', 'DURATION', 'STATUS'];
            $widths = [50, 25, 25, 25, 35, 32, 32, 22, 20, 22]; 

            // Gather all data rows first
            $dataRows = [];
            while ($row = $result->fetch_assoc()) {
                $duration = isset($row['time_in'], $row['time_out'])
                    ? (new DateTime($row['time_in']))->diff(new DateTime($row['time_out']))->format('%H:%I:%S')
                    : '-';
                $dataRows[] = [
                    strtoupper($row['full_name']),
                    isset($row['log_date']) ? $row['log_date'] : '-',
                    isset($row['time_in']) ? $row['time_in'] : '-',
                    isset($row['time_out']) ? $row['time_out'] : '-',
                    $row['purpose'] ?? '-',
                    $row['barangay_name'] ?? '-',
                    $row['school_name'] ?? '-',
                    $row['type'] ?? '-',
                    $duration,
                    $row['status'] ?? 'User Logout'
                ];
            }

            // Use fixed column widths
            $colCount = count($headers);
            $colWidths = $widths;
            $padding = 4; // 2mm left, 2mm right

            // Calculate table width
            $tableWidth = array_sum($colWidths);
            // Center the table horizontally using public margin getters
            $margins = $pdf->getMargins();
            $pageWidth = $pdf->getPageWidth() - $margins['left'] - $margins['right'];
            $tableStartX = $margins['left'] + (($pageWidth - $tableWidth) / 2);
            $tableStartY = $pdf->GetY();

            // Header row
            $pdf->SetFillColor(0, 0, 0);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('helvetica', 'B'); // Bold and slightly larger font for header
            $x = $tableStartX;
            $y = $pdf->GetY();
            for ($i = 0; $i < $colCount; $i++) {
                $pdf->SetXY($x, $y);
                $pdf->MultiCell($colWidths[$i], 7, $headers[$i], 0, 'C', true, 0); // Centered and bold, slightly less height
                $x += $colWidths[$i];
            }
            // Guides inside header block
            $pdf->SetFont('helvetica', 'I', 7); // Italic, small
            $pdf->SetTextColor(200, 200, 200); // Lighter gray for inside header
            $x = $tableStartX;
            $y = $y + 7; // Move below header text
            $guides = [
                '(First Name, Middle Name, Surname)',
                '(YYYY-MM-DD)',
                '(HH:MM:SS)',
                '(HH:MM:SS)',
                '(Purpose of Visit)',
                '(Barangay Name)',
                '(School Name)',
                '(Member/Guest)',
                '(HH:MM:SS)',
                '(Status)'
            ];
            for ($i = 0; $i < $colCount; $i++) {
                $pdf->SetXY($x, $y);
                $pdf->MultiCell($colWidths[$i], 5, $guides[$i], 0, 'C', true, 0); // Still filled, so it's inside header
                $x += $colWidths[$i];
            }
            $pdf->Ln();
            // Draw header bottom line
            $pdf->SetDrawColor(0,0,0);
            $pdf->Line($tableStartX, $pdf->GetY(), $tableStartX + $tableWidth, $pdf->GetY());
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('helvetica', '', 9); // Reset font for data rows

            foreach ($dataRows as $rowIndex => $data) {
                $cellHeights = [];
                for ($i = 0; $i < $colCount; $i++) {
                    $cellHeights[$i] = $pdf->getStringHeight($colWidths[$i] - 2, $data[$i]) + 2;
                }
                $maxHeight = max(max($cellHeights), 12);
                $x = $tableStartX;
                $y = $pdf->GetY();

                // Check if next row will overflow the page, if so, add a new page (no header re-draw)
                if ($y + $maxHeight > ($pdf->getPageHeight() - $pdf->getMargins()['bottom'] - 15)) {
                    $pdf->AddPage();
                    $pdf->SetFont('helvetica', 'B', 9);
                    $pdf->SetTextColor(0, 0, 0);
                    $x = $tableStartX;
                    $y = $pdf->GetY();
                }

                if ($rowIndex % 2 == 0) {
                    $pdf->SetFillColor(255, 255, 255);
                } else {
                    $pdf->SetFillColor(240, 240, 240);
                }
                $pdf->setCellPaddings(3, 2, 3, 2);
                for ($i = 0; $i < $colCount; $i++) {
                    $pdf->SetXY($x, $y);
                    $pdf->SetFont('helvetica', 'B', 9); // Set bold font for all data cells
                    if ($i == 9) {
                        if ($data[9] == 'Auto Logout') {
                            $pdf->SetTextColor(255, 0, 0);
                        } else {
                            $pdf->SetTextColor(0, 128, 0);
                        }
                    } else {
                        $pdf->SetTextColor(0, 0, 0);
                    }
                    $align = $i == 0 ? 'L' : 'C';
                    $pdf->MultiCell($colWidths[$i], $maxHeight, $data[$i], 0, $align, true, 0);
                    $x += $colWidths[$i];
                }
                $pdf->SetTextColor(0, 0, 0);
                $pdf->SetFont('helvetica', '', 9); // Reset font after row
                $pdf->SetY($y + $maxHeight);
            }
            // Reset cell paddings to default after table
            $pdf->setCellPaddings(0, 0, 0, 0);
          
            ob_clean(); // Clear any output buffer to prevent corruption
            $filename = $filename . '.pdf';
            $pdf->Output($filename, 'D');
        }

        ?>