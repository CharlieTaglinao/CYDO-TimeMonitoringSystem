<?php

include '../includes/database.php';

$searchInsite = isset($_GET['searchInsite']) ? $conn->real_escape_string($_GET['searchInsite']) : '';
$searchOutgoing = isset($_GET['searchOutgoing']) ? $conn->real_escape_string($_GET['searchOutgoing']) : '';


// Count today's insite visitors from the time_logs table
$totalInsiteVisitorQuery = "SELECT (COUNT(client_id) - (SELECT COUNT(client_id) 
                        FROM time_logs WHERE DATE(time_in) = CURDATE() AND time_out IS NOT NULL)) 
                        AS total_insite FROM time_logs 
                        WHERE DATE(time_in) = CURDATE();";
$totalInsiteResult = $conn->query($totalInsiteVisitorQuery);
$totalInsite = $totalInsiteResult->fetch_assoc()['total_insite'];

// Count today's already out visitors from the time_logs table
$totalAlreadyOutVisitorQuery = " SELECT COUNT(client_id) AS total_already_out FROM time_logs WHERE DATE(time_out) = CURDATE() AND DATE(time_in) = DATE(time_out)";

$totalAlreadyOutResult = $conn->query($totalAlreadyOutVisitorQuery);
$totalAlreadyOut = $totalAlreadyOutResult->fetch_assoc()['total_already_out'];

// Count todays total visitor even insite or already log out 
$totalVisitorsToday = $totalInsite + $totalAlreadyOut;


// QUERY FOR INSITE VISITOR FOR BOTH SEARCH AND FETCHING VISITOR
$insiteVisitorQuery = "SELECT DISTINCT
            visitors.first_name, 
            visitors.middle_name, 
            visitors.last_name,
            visitors.type,
            time_logs.time_in, 
            time_logs.time_out, 
            time_logs.code, 
            purpose.purpose
        FROM visitors
            INNER JOIN time_logs ON visitors.id = time_logs.client_id
            INNER JOIN (SELECT client_id, MAX(id) as latest_purpose_id FROM purpose GROUP BY client_id) as latest_purposes ON visitors.id = latest_purposes.client_id
            INNER JOIN purpose ON latest_purposes.latest_purpose_id = purpose.id
        WHERE time_logs.time_in IS NOT NULL 
            AND time_logs.time_out IS NULL
            AND DATE(time_logs.time_in) = CURDATE()
            AND CONCAT(visitors.first_name, ' ', visitors.middle_name, ' ', visitors.last_name) LIKE '%$searchInsite%'
        ORDER BY time_logs.time_in DESC ";

$insiteVisitorResult = $conn->query($insiteVisitorQuery);

if (isset($_GET['searchInsite'])) {
    if ($insiteVisitorResult->num_rows > 0) {
        while ($row = $insiteVisitorResult->fetch_assoc()) {
            $fullName = htmlspecialchars($row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name']);
            $date = (new DateTime($row['time_in']))->format('F j, Y');
            $timeIn = (new DateTime($row['time_in']))->format('g:i A');
            $purpose = htmlspecialchars($row['purpose']);
            $type = isset($row['type']) ? htmlspecialchars($row['type']) : '';
            $typeBadgeClass = (strtolower($type) !== 'guest') ? '' : 'bg-secondary';
            $typeBadgeStyle = (strtolower($type) !== 'guest') ? 'background-color: #2e2c73;' : '';

            echo '<div class="card mb-2 border-0 shadow-lg">'
                .'<div class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-0">'
                    .'<div>'
                        .'<div class="d-flex align-items-center mb-1">'
                            .'<h5 class="mb-0 fw-bold">' . $fullName . '</h5>'
                        .'</div>'
                        .'<div class="mb-1">'
                            .'<small class="text-muted fw-semibold me-2">Date:</small>'
                            .'<small>' . $date . '</small>'
                        .'</div>'
                        .'<div class="mb-1">'
                            .'<small class="text-muted fw-semibold me-2">Time in:</small>'
                            .'<small>' . $timeIn . '</small>'
                        .'</div>'
                        .'<div>'
                            .'<small class="text-muted fw-semibold me-2">Purpose:</small>'
                            .'<small>' . $purpose . '</small>'
                        .'</div>'
                    .'</div>'
                    .'<div class="d-flex flex-column align-items-end">';
            if (!empty($type)) {
                echo '<span class="badge fs-6 mb-2 ' . $typeBadgeClass . '" style="' . $typeBadgeStyle . '">' . $type . '</span>';
            }
            echo '<span class="badge bg-success fs-6 px-3 py-2">IN SITE</span>';
            echo '</div></div></div>';
        }
    } else {
        echo '<div class="card mb-2 border-0 shadow-lg">'
            .'<div class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-0">'
                .'<div>'
                    .'<strong>No Records Found</strong><br>'
                .'</div>'
            .'</div>'
        .'</div>';
    }
}


// QUERY FOR ALREADY OUT VISITOR FOR BOTH SEARCH AND FETCHING VISITOR
$alreadyOutQuery = "SELECT DISTINCT
            visitors.first_name, 
            visitors.middle_name, 
            visitors.last_name,
            visitors.type,
            time_logs.time_in, 
            time_logs.time_out, 
            time_logs.code, 
            purpose.purpose
        FROM visitors
            INNER JOIN time_logs ON visitors.id = time_logs.client_id
            INNER JOIN (SELECT client_id, MAX(id) as latest_purpose_id FROM purpose GROUP BY client_id) as latest_purposes ON visitors.id = latest_purposes.client_id
            INNER JOIN purpose ON latest_purposes.latest_purpose_id = purpose.id
            WHERE time_logs.time_in IS NOT NULL 
            AND time_logs.time_out IS NOT NULL
            AND DATE(time_logs.time_in) = CURDATE()
            AND CONCAT(visitors.first_name, ' ', visitors.middle_name, ' ', visitors.last_name) LIKE '%$searchOutgoing%';";

$alreadyOutResult = $conn->query($alreadyOutQuery);

if (isset($_GET['searchOutgoing'])) {
    if ($alreadyOutResult->num_rows > 0) {
        while ($row = $alreadyOutResult->fetch_assoc()) {
            $timeInForDuration = new DateTime($row['time_in']);
            $timeOutForDuration = new DateTime($row['time_out']);
            $fullName = htmlspecialchars($row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name']);
            $date = (new DateTime($row['time_in']))->format('F j, Y');
            $timeIn = (new DateTime($row['time_in']))->format('g:i A');
            $timeOut = (new DateTime($row['time_out']))->format('g:i A');
            $purpose = htmlspecialchars($row['purpose']);
            $duration = $timeInForDuration->diff($timeOutForDuration);
            $type = isset($row['type']) ? htmlspecialchars($row['type']) : '';
            $typeBadgeClass = (strtolower($type) !== 'guest') ? '' : 'bg-secondary';
            $typeBadgeStyle = (strtolower($type) !== 'guest') ? 'background-color: #2e2c73;' : '';

            echo '<div class="card mb-2 border-0 shadow-lg">'
                .'<div class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-0">'
                    .'<div>'
                        .'<div class="d-flex align-items-center mb-1">'
                            .'<h5 class="mb-0 fw-bold">' . $fullName . '</h5>'
                        .'</div>'
                        .'<div class="mb-1">'
                            .'<small class="text-muted fw-semibold me-2">Date:</small>'
                            .'<small>' . $date . '</small>'
                        .'</div>'
                        .'<div class="mb-1">'
                            .'<small class="text-muted fw-semibold me-2">Time in:</small>'
                            .'<small>' . $timeIn . '</small>'
                        .'</div>'
                        .'<div class="mb-1">'
                            .'<small class="text-muted fw-semibold me-2">Time out:</small>'
                            .'<small>' . $timeOut . '</small>'
                        .'</div>'
                        .'<div class="mb-1">'
                            .'<small class="text-muted fw-semibold me-2">Purpose:</small>'
                            .'<small>' . $purpose . '</small>'
                        .'</div>'
                        .'<div>'
                            .'<small class="text-muted fw-semibold me-2">Duration:</small>'
                            .'<small>' . $duration->format('%h hour %i minutes %s seconds') . '</small>'
                        .'</div>'
                    .'</div>'
                    .'<div class="d-flex flex-column align-items-end">'
                        .'<span class="badge bg-danger fs-6 px-3 py-2">ALREADY OUT</span>'
                    .'</div>'
                .'</div>'
            .'</div>';
        }
    } else {
        echo '<div class="card mb-2 border-0 shadow-lg">'
            .'<div class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-0">'
                .'<div>'
                    .'<strong>No Records Found</strong><br>'
                .'</div>'
            .'</div>'
        .'</div>';
    }
}

// FOR FILTERING VISITOR BASE ON THE DATE SELECTION 



?>