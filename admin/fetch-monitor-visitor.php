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
$totalAlreadyOutVisitorQuery = "SELECT COUNT(client_id) AS total_already_out FROM time_logs WHERE DATE(time_out) = CURDATE()";
$totalAlreadyOutResult = $conn->query($totalAlreadyOutVisitorQuery);
$totalAlreadyOut = $totalAlreadyOutResult->fetch_assoc()['total_already_out'];

// Count todays total visitor even insite or already log out 
$totalVisitorsToday = $totalInsite + $totalAlreadyOut;


// QUERY FOR INSITE VISITOR FOR BOTH SEARCH AND FETCHING VISITOR
$insiteVisitorQuery = "SELECT DISTINCT
            visitors.first_name, 
            visitors.middle_name, 
            visitors.last_name,
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

            echo '<div class="card mb-2">
                       <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>' . $fullName . '</strong><br>
                                <small class="text-muted" style="font-weight: 600;">Date: ' . $date . '</small><br>
                                <small class="text-muted" style="font-weight: 600;">Time in: ' . $timeIn . '</small><br>
                                <small class="text-muted" style="font-weight: 600;">Purpose: ' . $purpose . '</small>
                            </div>
                            <span class="badge bg-success">IN SITE</span>
                        </div>
                  </div>';
        }
    } else {
        echo '<div class="card mb-2">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>' . 'No Records Found'.  '</strong><br>
                            </div>
                        </div>
                    </div>';
    }
}


// QUERY FOR ALREADY OUT VISITOR FOR BOTH SEARCH AND FETCHING VISITOR
$alreadyOutQuery = "SELECT DISTINCT
            visitors.first_name, 
            visitors.middle_name, 
            visitors.last_name,
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
            //calculate duration
            $timeInForDuration = new DateTime($row['time_in']);
            $timeOutForDuration = new DateTime($row['time_out']);
         

            $fullName = htmlspecialchars($row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name']);
            $date = (new DateTime($row['time_in']))->format('F j, Y');
            $timeIn = (new DateTime($row['time_in']))->format('g:i A');
            $timeOut = (new DateTime($row['time_out']))->format('g:i A');
            $purpose = htmlspecialchars($row['purpose']);
            $duration = $timeInForDuration->diff($timeOutForDuration);

            echo '<div class="card mb-2">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>' . $fullName . '</strong><br>
                                <small class="text-muted" style="font-weight: 600;">Date: ' . $date . '</small><br>
                                <small class="text-muted" style="font-weight: 600;">Time in: ' . $timeIn . '</small><br>
                                <small class="text-muted" style="font-weight: 600;">Time out: ' . $timeOut . '</small><br>
                                <small class="text-muted" style="font-weight: 600;">Purpose: ' . $purpose . '</small><br>
                                <small class="text-muted" style="font-weight: 600;">Duration: ' . $duration->format('%h hour %i minutes %s seconds') . '</small>
                                
                            </div>
                            <span class="badge bg-danger">ALREADY OUT</span>
                        </div>
                  </div>';
        }
    } else {
        echo '<div class="card mb-2">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>' . 'No Records Found'.  '</strong><br>
                            </div>
                        </div>
                    </div>';
    }
}

// FOR FILTERING VISITOR BASE ON THE DATE SELECTION 



?>  