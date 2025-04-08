<?php
session_start();
include '../../includes/database.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['is_invalid']) && $_POST['is_invalid'] === 'true') {
        $_SESSION['message'] = 'Please correct the errors in the form before submitting.';
        $_SESSION['message_type'] = 'danger';
        header('Location: ../change-password.php');
        exit();
    }

    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // Validate if all fields are filled
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $_SESSION['message'] = 'All fields are required.';
        $_SESSION['message_type'] = 'danger';
        header('Location: ../change-password.php');
        exit();
    }

    // Check if new password matches confirm password
    if ($newPassword !== $confirmPassword) {
        $_SESSION['message'] = 'New password and confirm password do not match.';
        $_SESSION['message_type'] = 'danger';
        header('Location: ../change-password.php');
        exit();
    }

    // Fetch current user password from database
    $userId = $_SESSION['user_id']; // Assuming user ID is stored in session
    $query = $conn->prepare("SELECT password FROM account WHERE id = ?");
    $query->bind_param('i', $userId);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify current password
        if (!password_verify($currentPassword, $user['password'])) {
            $_SESSION['message'] = 'Current password is incorrect.';
            $_SESSION['message_type'] = 'danger';
            header('Location: ../change-password.php');
            exit();
        }

        // Hash the new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update password in the database
        $updateQuery = $conn->prepare("UPDATE account SET password = ? WHERE id = ?");
        $updateQuery->bind_param('si', $hashedPassword, $userId);

        if ($updateQuery->execute()) {
            $_SESSION['message'] = 'Password changed successfully.';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Failed to update password. Please try again.';
            $_SESSION['message_type'] = 'danger';
        }
    } else {
        $_SESSION['message'] = 'User not found.';
        $_SESSION['message_type'] = 'danger';
    }

    $query->close();
    $conn->close();
    header('Location: ../change-password.php');
    exit();
} else {
    header('Location: ../change-password.php');
    exit();
}
