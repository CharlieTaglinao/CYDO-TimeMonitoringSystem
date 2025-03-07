<?php
session_start();
if (isset($_SESSION['logged_in'])) {
    $_SESSION['message'] = "AUTO LOG OUT";
    $_SESSION['message_type'] = "warning";
    unset($_SESSION['logged_in']);
}

include '../includes/database.php';
$query = "UPDATE time_logs SET time_out = NOW(),code = null, status = 'Auto Logout' WHERE time_out IS NULL";
mysqli_query($conn, $query);

?>