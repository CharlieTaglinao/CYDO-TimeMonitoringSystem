<?php
session_start();

include '../includes/database.php';

// Count today's visitors from the time_logs table
$totalVisitorsTodayQuery = "SELECT COUNT(DISTINCT client_id) AS total_today FROM time_logs WHERE DATE(time_in) = CURDATE()";
$totalVisitorsTodayResult = $conn->query($totalVisitorsTodayQuery);
$totalVisitorsToday = $totalVisitorsTodayResult->fetch_assoc()['total_today'];

// Count current visitors
$currentVisitorsQuery = "SELECT COUNT(DISTINCT client_id) AS current_visitors 
                         FROM time_logs 
                         WHERE time_out IS NULL";
$currentVisitorsResult = $conn->query($currentVisitorsQuery);
$currentVisitors = $currentVisitorsResult->fetch_assoc()['current_visitors'];

// Fetch data for reports and analytics
$visitorsQuery = "SELECT * FROM visitors";
$visitorsResult = $conn->query($visitorsQuery);

$timeLogsQuery = "SELECT * FROM time_logs";
$timeLogsResult = $conn->query($timeLogsQuery);

?>