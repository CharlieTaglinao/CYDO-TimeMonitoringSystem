<?php
session_start();
include '../includes/database.php';

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Count today's visitors from the time_logs table
$totalVisitorsTodayQuery = "SELECT COUNT(client_id) AS total_today FROM time_logs WHERE DATE(time_in) = CURDATE()";
$totalVisitorsTodayResult = $conn->query($totalVisitorsTodayQuery);
$totalVisitorsToday = $totalVisitorsTodayResult->fetch_assoc()['total_today'];

// Count current visitors
$currentVisitorsQuery = "SELECT COUNT(client_id) AS current_visitors FROM time_logs WHERE time_out IS NULL";
$currentVisitorsResult = $conn->query($currentVisitorsQuery);
$currentVisitors = $currentVisitorsResult->fetch_assoc()['current_visitors'];

// Count weekly visitors
$totalWeeklyVisitorsQuery = "SELECT COUNT(client_id) AS total_week FROM time_logs WHERE YEARWEEK(time_in, 1) = YEARWEEK(CURDATE(), 1)";
$totalWeeklyVisitorsResult = $conn->query($totalWeeklyVisitorsQuery);
$totalWeeklyVisitor = $totalWeeklyVisitorsResult->fetch_assoc()['total_week'];

// Set pagination variables
$limit = 10; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; 
$offset = ($page - 1) * $limit; 

// Count total rows with search filter
$totalRowsQuery = "
    SELECT COUNT(*) AS total 
    FROM visitors 
    INNER JOIN time_logs ON visitors.id = time_logs.client_id
    WHERE CONCAT(visitors.first_name, ' ', visitors.middle_name, ' ', visitors.last_name) LIKE '%$search%'
";
$totalRowsResult = $conn->query($totalRowsQuery);
$totalRows = $totalRowsResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit); 

// Fetch paginated records with search filter
    $visitorsQuery = "
        SELECT 
            visitors.first_name, visitors.middle_name, visitors.last_name,
            time_logs.time_in, time_logs.time_out, time_logs.code
        FROM visitors
        INNER JOIN time_logs ON visitors.id = time_logs.client_id
        WHERE CONCAT(visitors.first_name, ' ', visitors.middle_name, ' ', visitors.last_name) LIKE '%$search%'
        ORDER BY time_in DESC LIMIT $limit OFFSET $offset
    ";
    $visitorsResult = $conn->query($visitorsQuery);
    if (isset($_GET['search'])) {
    if ($visitorsResult->num_rows > 0) {
        while ($row = $visitorsResult->fetch_assoc()) {
            echo "<tr>
                <td>" . strtoupper($row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name']) . "</td>
                <td>" . (isset($row['time_in']) ? date('Y-m-d', strtotime($row['time_in'])) : '-') . "</td>
                <td>" . (isset($row['time_in']) ? date('H:i:s', strtotime($row['time_in'])) : '-') . "</td>
                <td>" . (isset($row['time_out']) ? date('H:i:s', strtotime($row['time_out'])) : '-') . "</td>
                <td>";
            if (isset($row['time_in'], $row['time_out'])) {
                $timeIn = new DateTime($row['time_in']);
                $timeOut = new DateTime($row['time_out']);
                $interval = $timeIn->diff($timeOut);
                echo $interval->format('%h hours %i minutes %s seconds');
            } else {
                echo '-';
            }
            echo "</td>
                <td>{$row['code']}</td>
            </tr>";
        }
    } else {
        echo "<tr><td colspan='6'>No records found</td></tr>";
    }
    exit; 
}


?>
