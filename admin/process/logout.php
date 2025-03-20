<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['visitor_code'])) {
    // Perform any additional cleanup or logging if necessary
    $visitorCode = $_POST['visitor_code'];

    // Destroy the session
    session_unset();
    session_destroy();

    // Redirect to the login page
    header("Location: ../login.php");
    exit();
} else {
    // Redirect to the dashboard if accessed improperly
    header("Location: ../index.php");
    exit();
}
?>