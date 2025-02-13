<?php 
include '../../includes/database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $username = trim($_POST['username']);
    $role = intval($_POST['role']);
    
    // Validate input
    if (empty($username)) {
        $_SESSION['message'] = "Username is a required field.";
        $_SESSION['message_type'] = "danger";
        header("Location: ../view-account.php");
        exit();
    }

    // Retrieve the existing account details
    $selectQuery = "SELECT username, role FROM account WHERE id = ?";
    $selectStmt = $conn->prepare($selectQuery);
    $selectStmt->bind_param("i", $id);
    $selectStmt->execute();
    $result = $selectStmt->get_result();
    $existingData = $result->fetch_assoc();

    if (!$existingData) {
        $_SESSION['message'] = "Account not found.";
        $_SESSION['message_type'] = "danger";
        header("Location: ../view-account.php");
        exit();
    }

    // Check if there are any changes
    if ($existingData['username'] === $username && $existingData['role'] == $role) {
        $_SESSION['message'] = "Nothing changes.";
        $_SESSION['message_type'] = "info";
        header("Location: ../view-account.php");
        exit();
    }

    // Update the account details
    $updateQuery = "UPDATE account SET username = ?, role = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("sii", $username, $role, $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Account updated successfully.";
        $_SESSION['message_type'] = "success";
        header("Location: ../view-account.php");
        exit();
    } else {
        $_SESSION['message'] = "Failed to update account.";
        $_SESSION['message_type'] = "danger";
        header("Location: ../view-account.php");
        exit();
    }
} else {
    $_SESSION['message'] = "Invalid request.";
    $_SESSION['message_type'] = "danger";
    header("Location: ../view-account.php");
    exit();
}
?>
