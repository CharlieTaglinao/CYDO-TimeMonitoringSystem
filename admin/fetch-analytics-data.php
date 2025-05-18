<?php
include '../includes/database.php';
$selectedYear = isset($_POST['year']) ? $_POST['year'] : date('Y');

// Fetch data for visitors
$visitorQuery = "SELECT DATE_FORMAT(time_logs.time_in, '%Y-%m') as date, DATE_FORMAT(time_logs.time_in, '%b') as month, COUNT(DISTINCT time_logs.client_id) as count 
FROM time_logs 
INNER JOIN visitors ON time_logs.client_id = visitors.id 
WHERE YEAR(time_logs.time_in) = '$selectedYear' AND visitors.type = 'GUEST' 
GROUP BY DATE_FORMAT(time_logs.time_in, '%Y-%m')";

$visitorResult = $conn->query($visitorQuery);
$visitorData = ['labels' => [], 'values' => []];
while ($row = $visitorResult->fetch_assoc()) {
    $visitorData['labels'][] = $row['month'];
    $visitorData['values'][] = $row['count'];
}


// Fetch data for members
$memberQuery = "SELECT DATE_FORMAT(time_logs.time_in, '%Y-%m') as date, DATE_FORMAT(time_logs.time_in, '%b') as month, COUNT(DISTINCT time_logs.client_id) as count 
FROM time_logs 
INNER JOIN visitors ON time_logs.client_id = visitors.id
WHERE YEAR(time_logs.time_in) = '$selectedYear' 
  AND visitors.type = 'MEMBER' 
  AND time_logs.time_in IS NOT NULL 
  AND time_logs.time_in != '' 
  AND time_logs.time_in != '0000-00-00 00:00:00'
GROUP BY DATE_FORMAT(time_logs.time_in, '%Y-%m')";

$memberResult = $conn->query($memberQuery);
$memberData = ['labels' => [], 'values' => []];
while ($row = $memberResult->fetch_assoc()) {
    $memberData['labels'][] = $row['month'];
    $memberData['values'][] = $row['count'];
}


// Fetch data for users
$userQuery = "SELECT DATE_FORMAT(created_at, '%Y-%m') as date, DATE_FORMAT(created_at, '%b') as month, COUNT(*) as count FROM account WHERE YEAR(created_at) = '$selectedYear' GROUP BY DATE_FORMAT(created_at, '%Y-%m')";
$userResult = $conn->query($userQuery);
$userData = ['labels' => [], 'values' => []];
while ($row = $userResult->fetch_assoc()) {
    $userData['labels'][] = $row['month'];
    $userData['values'][] = $row['count'];
}

//fetch data for all users



// Output data as JavaScript variables
echo "<script>
    var visitorData = " . json_encode($visitorData) . ";
    var userData = " . json_encode($userData) . ";
    var memberData = " . json_encode($memberData) . ";
</script>";
?>
