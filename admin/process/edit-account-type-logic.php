<?php
include '../../includes/database.php';

if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $role = trim($_POST['role']);

    if (empty($role)) {
        $_SESSION['message'] = 'Account type name cannot be empty.';
        $_SESSION['message_type'] = 'danger';
    } else {
        $stmt = $conn->prepare("UPDATE role SET role = ? WHERE id = ?");
        $stmt->bind_param('si', $role, $id);
        if ($stmt->execute()) {
            $_SESSION['message'] = 'Account type updated successfully.';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Failed to update account type.';
            $_SESSION['message_type'] = 'danger';
        }
        $stmt->close();
    }
}

header('Location: ../view-account-type.php');
exit();