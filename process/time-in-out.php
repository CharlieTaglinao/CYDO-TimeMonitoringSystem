<?php
session_start();
include '../includes/database.php'; // Assuming you have a database connection file

$idNumber = $_POST['idNumber'] ?? null;
$logType = $_POST['logType'] ?? null;

if ($idNumber && $logType) {
    $logTime = date('Y-m-d H:i:s');

    $insertQuery = "INSERT INTO time_logs (id_number, log_type, log_time) VALUES (?, ?, ?)";
    $insertStmt = $conn->prepare($insertQuery);
    $insertStmt->bind_param("sss", $idNumber, $logType, $logTime);
    $insertStmt->execute();

    $_SESSION['message'] = "Successfully logged $logType at $logTime";
    $_SESSION['message_type'] = ($logType == 'IN') ? 'success' : 'danger';
} else {
    $_SESSION['message'] = "ID Number and Log Type are required.";
    $_SESSION['message_type'] = 'warning';
}

header("Location: ../index.php");
exit();
?>
