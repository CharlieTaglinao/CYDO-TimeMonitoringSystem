<?php 
include '../../includes/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $username = trim($_POST['username']);
    $role = intval($_POST['role']);
    
    // Validate input
    if (empty($username)) {
        $_SESSION['error'] = "Username is required.";
        header("Location: ../view-account.php");
        exit;
    }

    // Update the account details
    $updateQuery = "UPDATE account SET username = ?, role = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("sii", $username, $role, $id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Account updated successfully.";
        header("Location: ../view-account.php");
        exit;
    } else {
        $_SESSION['error'] = "Failed to update account.";
        header("Location: ../view-account.php");
        exit;
    }
} else {
    $_SESSION['error'] = "Invalid request.";
    header("Location: ../view-account.php");
    exit;
}
?>
