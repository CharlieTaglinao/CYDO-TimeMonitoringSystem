<?php
include '../../includes/database.php';
session_start(); // Ensure session is started

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $applicationId = $_POST['application_id'];
    $action = $_POST['action'];

    if ($action === 'accept') {
        // Activate the member
        $query = "UPDATE member_applicants SET status = 'ACTIVATED' WHERE id = ?";
        $status = 'ACTIVATED';
    } elseif ($action === 'decline') {
        // Deactivate the member
        $query = "UPDATE member_applicants SET status = 'DEACTIVATED' WHERE id = ?";
        $status = 'DEACTIVATED';
    } else {
        // Invalid action
        $_SESSION['message'] = 'Invalid action specified.';
        $_SESSION['message_type'] = 'danger';
        header('Location: ../view-activation.php');
        exit;
    }

    // Prepare and execute the query for member_applicants
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $applicationId);

    if ($stmt->execute()) {
        // Find the visitor and membership_id for this applicant
        $visitorQuery = "SELECT id, membership_id FROM visitors WHERE first_name = (SELECT first_name FROM member_applicants WHERE id = ?) AND last_name = (SELECT last_name FROM member_applicants WHERE id = ?) AND type = 'MEMBER' LIMIT 1";
        $visitorStmt = $conn->prepare($visitorQuery);
        $visitorStmt->bind_param('ii', $applicationId, $applicationId);
        $visitorStmt->execute();
        $visitorResult = $visitorStmt->get_result();
        if ($visitorResult->num_rows > 0) {
            $visitor = $visitorResult->fetch_assoc();
            $membershipId = $visitor['membership_id'];
            if ($membershipId) {
                // Update member_code status
                $codeQuery = "UPDATE member_code SET status = ? WHERE id = ?";
                $codeStmt = $conn->prepare($codeQuery);
                $codeStmt->bind_param('si', $status, $membershipId);
                $codeStmt->execute();
                $codeStmt->close();
            }
        }
        $visitorStmt->close();

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