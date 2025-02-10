
<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin.php");
    exit();
}

include '../includes/database.php';

// Fetch data for reports and analytics
$visitorsQuery = "SELECT * FROM visitors";
$visitorsResult = $conn->query($visitorsQuery);

$timeLogsQuery = "SELECT * FROM time_logs";
$timeLogsResult = $conn->query($timeLogsQuery);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>Admin Dashboard</h1>
    <h2>Visitors</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>First Name</th>
            <th>Middle Name</th>
            <th>Last Name</th>
            <th>Sex</th>
        </tr>
        <?php while ($row = $visitorsResult->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['first_name']; ?></td>
            <td><?php echo $row['middle_name']; ?></td>
            <td><?php echo $row['last_name']; ?></td>
            <td><?php echo $row['sex_id']; ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <h2>Time Logs</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Client ID</th>
            <th>Time In</th>
            <th>Time Out</th>
            <th>Code</th>
        </tr>
        <?php while ($row = $timeLogsResult->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['client_id']; ?></td>
            <td><?php echo $row['time_in']; ?></td>
            <td><?php echo $row['time_out']; ?></td>
            <td><?php echo $row['code']; ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>