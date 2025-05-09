<?php
session_start();
include '../../includes/database.php';

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $stmt = $conn->prepare('DELETE FROM role WHERE id = ?');
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        $_SESSION['message'] = 'Account type deleted successfully.';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Failed to delete account type.';
        $_SESSION['message_type'] = 'danger';
    }
    $stmt->close();
}

header('Location: ../view-account-type.php');
exit();