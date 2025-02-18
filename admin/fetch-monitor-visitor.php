<?php 

include '../includes/database.php';


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
            AND DATE(time_logs.time_in) = CURDATE();";
            
$insiteVisitorResult = $conn->query($insiteVisitorQuery);

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
            AND DATE(time_logs.time_in) = CURDATE();";

$alreadyOutResult = $conn->query($alreadyOutQuery);
?>