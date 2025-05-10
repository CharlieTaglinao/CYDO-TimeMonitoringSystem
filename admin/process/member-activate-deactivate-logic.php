<?php
include '../../includes/database.php';
session_start(); // Ensure session is started

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $applicationId = $_POST['application_id'];
    $action = $_POST['action'];

    if ($action === 'accept') {
        // Activate the member
        $query = "UPDATE member_applicants SET status = 'ACTIVATED' WHERE id = ?";
    } elseif ($action === 'decline') {
        // Deactivate the member
        $query = "UPDATE member_applicants SET status = 'DEACTIVATED' WHERE id = ?";
    } else {
        // Invalid action
        $_SESSION['message'] = 'Invalid action specified.';
        $_SESSION['message_type'] = 'danger';
        header('Location: ../view-activation.php');
        exit;
    }

    // Prepare and execute the query
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $applicationId);

    if ($stmt->execute()) {
        $_SESSION['message'] = $action === 'accept' ? 'ACTIVATED SUCCESSFULLY.' : 'DEACTIVATED SUCCESSFULLY.';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'An error occurred while processing the request.';
        $_SESSION['message_type'] = 'danger';
    }

    $stmt->close();
    $conn->close();

    header('Location: ../view-activation.php');
    exit;
} else {
    // Redirect if accessed without POST method
    header('Location: ../view-activation.php');
    exit;
}