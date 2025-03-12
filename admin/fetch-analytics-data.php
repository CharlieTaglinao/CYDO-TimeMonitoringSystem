<?php
// Include necessary files and initialize database connection
include '../includes/database.php';

// Fetch data for visitors
$visitorQuery = "SELECT DATE_FORMAT(time_in, '%Y-%m') as date, DATE_FORMAT(time_in, '%b') as month, COUNT(*) as count FROM time_logs GROUP BY DATE_FORMAT(time_in, '%Y-%m')";
$visitorResult = $conn->query($visitorQuery);
$visitorData = ['labels' => [], 'values' => []];
while ($row = $visitorResult->fetch_assoc()) {
    $visitorData['labels'][] = $row['month'];
    $visitorData['values'][] = $row['count'];
}

// Fetch data for users
$userQuery = "SELECT DATE_FORMAT(created_at, '%Y-%m') as date, DATE_FORMAT(created_at, '%b') as month, COUNT(*) as count FROM account GROUP BY DATE_FORMAT(created_at, '%Y-%m')";
$userResult = $conn->query($userQuery);
$userData = ['labels' => [], 'values' => []];
while ($row = $userResult->fetch_assoc()) {
    $userData['labels'][] = $row['month'];
    $userData['values'][] = $row['count'];
}

// Fetch data for CYDO visitors
$cydoQuery = "SELECT DATE_FORMAT(created_at, '%Y-%m') as date, DATE_FORMAT(created_at, '%b') as month, COUNT(*) as count FROM visitors WHERE office_id = 1 GROUP BY DATE_FORMAT(created_at, '%Y-%m')";
$cydoResult = $conn->query($cydoQuery);
$cydoData = ['labels' => [], 'values' => []];
while ($row = $cydoResult->fetch_assoc()) {
    $cydoData['labels'][] = $row['month'];
    $cydoData['values'][] = $row['count'];
}

// Fetch data for PDAO visitors
$pdaoQuery = "SELECT DATE_FORMAT(created_at, '%Y-%m') as date, DATE_FORMAT(created_at, '%b') as month, COUNT(*) as count FROM visitors WHERE office_id = 2 GROUP BY DATE_FORMAT(created_at, '%Y-%m')";
$pdaoResult = $conn->query($pdaoQuery);
$pdaoData = ['labels' => [], 'values' => []];
while ($row = $pdaoResult->fetch_assoc()) {
    $pdaoData['labels'][] = $row['month'];
    $pdaoData['values'][] = $row['count'];
}

// Output data as JavaScript variables
echo "<script>
    var visitorData = " . json_encode($visitorData) . ";
    var userData = " . json_encode($userData) . ";
    var cydoData = " . json_encode($cydoData) . ";
    var pdaoData = " . json_encode($pdaoData) . ";
</script>";
?>
