<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once '../includes/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $response = ['success' => false, 'errors' => []];

    if (empty($username)) {
        $response['errors']['username'] = 'Please provide a username.';
    }

    if (empty($password)) {
        $response['errors']['password'] = 'Please provide a password.';
    }

    if (!empty($response['errors'])) {
        echo json_encode($response);
        exit();
    }

    try {
        $stmt = $conn->prepare(
            "SELECT account.id, account.username, account.password, role.role AS role_name 
             FROM account 
             JOIN role ON account.role = role.id 
             WHERE BINARY account.username = ?"
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

                $response['success'] = true;
                $response['redirect'] = ($user['role_name'] === 'admin') ? 'admin/index' :
                                        (($user['role_name'] === 'staff') ? 'staff/index' : '../index');
                echo json_encode($response);
                exit();
            } else {
                $response['errors']['password'] = 'Invalid password.';
            }
        } else {
            $response['errors']['username'] = 'User not found.';
        }
    } catch (Exception $e) {
        $response['errors']['general'] = 'An error occurred: ' . htmlspecialchars($e->getMessage());
    }

    echo json_encode($response);
    exit();
} else {
    header('Location: ../index');
    exit();
}
?>