<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/database.php'; // Ensure the database connection is included

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $_SESSION['message'] = 'Please fill in all fields.';
        $_SESSION['message_type'] = 'danger';
        header('Location: ../index.php');
        exit();
    }

    try {
        $stmt = $conn->prepare(
            "SELECT account.id, account.username, account.password, role.role AS role_name 
             FROM account 
             JOIN role ON account.role = role.id 
             WHERE account.username = ?"
        );
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role_name'];

                // Redirect based on role
                if ($user['role_name'] === 'admin') {
                    header('Location: ../admin/index.php');
                } elseif ($user['role_name'] === 'staff') {
                    header('Location: ../staff/index.php');
                } else {
                    header('Location: ../index.php');
                }

                exit();
            } else {
                $_SESSION['message'] = 'Invalid password.';
                $_SESSION['message_type'] = 'danger';
                header('Location: ../index.php');
                exit();
            }
        } else {
            $_SESSION['message'] = 'User not found.';
            $_SESSION['message_type'] = 'danger';
            header('Location: ../index.php');
            exit();
        }
    } catch (Exception $e) {
        error_log("Exception in login script: " . $e->getMessage());
        $_SESSION['message'] = 'An error occurred: ' . htmlspecialchars($e->getMessage());
        $_SESSION['message_type'] = 'danger';
        header('Location: ../index.php');
        exit();
    }
} else {
    header('Location: ../index.php');
    exit();
}
?>
