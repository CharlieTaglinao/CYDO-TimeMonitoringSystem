<?php 
include '../../includes/database.php';

date_default_timezone_set('Asia/Manila');
session_start(); 

$username = htmlspecialchars(trim($_POST['username']));
$password = trim($_POST['password']);
$role = $_POST['role'];

if (empty($username) || empty($password) || empty($role) || ctype_space($username) || ctype_space($password)) {
    $_SESSION['message'] = "All fields are required and cannot contain only spaces.";
    $_SESSION['message_type'] = "danger";
    header("Location: ../view-account.php");
    exit();
} else {

    $query = "SELECT id FROM account WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['message'] = "The username is already taken. Please choose a different one.";
        $_SESSION['message_type'] = "danger";
        header("Location: ../view-account.php");
        exit();
    }
}

$hashedpassword = password_hash($password, PASSWORD_DEFAULT);

$AddAccountQuery = "INSERT INTO account( `username`, `password`, `role`, created_at) VALUES (?,?,?,NOW())";
$AddAccountStmt = $conn->prepare($AddAccountQuery);
$AddAccountStmt->bind_param("ssi", $username, $hashedpassword, $role);

if ($AddAccountStmt->execute()) {
    $_SESSION['message'] = "Account successfully added!";
    $_SESSION['message_type'] = "success";
} else {
    $_SESSION['message'] = "An error occurred while adding the account. Please try again.";
    $_SESSION['message_type'] = "danger";
}

header("Location: ../view-account.php");


$AddAccountStmt->close();
$conn->close();
?>
