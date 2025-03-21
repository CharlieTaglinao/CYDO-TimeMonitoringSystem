<?php
include '../../includes/database.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['visitor_code'])) {

    $visitorCode = $_POST['visitor_code'];


    session_start();
    $username = $_SESSION['username']; // Assuming the username is stored in the session

    $query = "UPDATE time_logs t 
              SET t.time_out = NOW(), t.status = CONCAT('Log out by ', ?), t.code = null
              WHERE t.code = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $username, $visitorCode);
    $stmt->execute();
    $stmt->close();

    header("Location: ../index.php");
    exit();


} else {
    // Redirect to the dashboard if accessed improperly
    header("Location: ../index.php");
    exit();
}

?>