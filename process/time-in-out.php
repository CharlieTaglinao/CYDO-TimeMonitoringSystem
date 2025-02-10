<?php
session_start();
include '../includes/database.php';

$firstName = $_POST['firstName'] ?? null;
$middleName = $_POST['middleName'] ?? null;
$lastName = $_POST['lastName'] ?? null;
$email = $_POST['email'] ?? null;
$purpose = $_POST['purpose'] ?? null;
$sex = $_POST['sex'] ?? null;
$code = $_POST['code'] ?? null;

function generateRandomCode($length = 6)
{
    return substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $length);
}

if (isset($_POST['timeIn'])) {
    if (!$firstName) {
        $_SESSION['message'] = "First Name is required.";
        $_SESSION['message_type'] = 'danger';
        header("Location: ../index.php");
        exit();
    } elseif (!$middleName) {
        $_SESSION['message'] = "Middle Name is required.";
        $_SESSION['message_type'] = 'danger';
        header("Location: ../index.php");
        exit();
    } elseif (!$lastName) {
        $_SESSION['message'] = "Last Name is required.";
        $_SESSION['message_type'] = 'danger';
        header("Location: ../index.php");
        exit();
    } elseif (!$email) {
        $_SESSION['message'] = "Email is required.";
        $_SESSION['message_type'] = 'danger';
        header("Location: ../index.php");
        exit();
    } elseif (!$purpose) {
        $_SESSION['message'] = "Purpose is required.";
        $_SESSION['message_type'] = 'danger';
        header("Location: ../index.php");
        exit();
    }

    $logTime = date('Y-m-d H:i:s');

    $checkEmailQuery = "SELECT client_id FROM email WHERE email = ?";
    $checkEmailStmt = $conn->prepare($checkEmailQuery);
    $checkEmailStmt->bind_param("s", $email);
    $checkEmailStmt->execute();
    $checkEmailStmt->store_result();

    if ($checkEmailStmt->num_rows > 0) {
        $checkEmailStmt->bind_result($clientId);
        $checkEmailStmt->fetch();
    } else {
        $insertQuery = "INSERT INTO visitors (first_name, middle_name, last_name, sex_id) VALUES (?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param("sssi", $firstName, $middleName, $lastName, $sex);
        $insertStmt->execute();
        $clientId = $conn->insert_id;

        $insertEmailQuery = "INSERT INTO email (client_id, email) VALUES (?, ?)";
        $insertEmailStmt = $conn->prepare($insertEmailQuery);
        $insertEmailStmt->bind_param("is", $clientId, $email);
        $insertEmailStmt->execute();
    }

   
    $checkTimeInQuery = "SELECT time_in FROM time_logs WHERE client_id = ? AND time_out IS NULL";
    $checkTimeInStmt = $conn->prepare($checkTimeInQuery);
    $checkTimeInStmt->bind_param("i", $clientId);
    $checkTimeInStmt->execute();
    $checkTimeInStmt->store_result();

    if ($checkTimeInStmt->num_rows > 0) {
        $_SESSION['message'] = "You are already timed in.";
        $_SESSION['message_type'] = 'warning';
        header("Location: ../index.php");
        exit();
    }

    $randomCode = generateRandomCode();
    $insertPurposeQuery = "INSERT INTO purpose (client_id, purpose) VALUES (?, ?)";
    $insertPurposeStmt = $conn->prepare($insertPurposeQuery);
    $insertPurposeStmt->bind_param("is", $clientId, $purpose);
    $insertPurposeStmt->execute();

    $insertLogQuery = "INSERT INTO time_logs (client_id, time_in, code) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE time_in = VALUES(time_in), code = VALUES(code)";
    $insertLogStmt = $conn->prepare($insertLogQuery);
    $insertLogStmt->bind_param("iss", $clientId, $logTime, $randomCode);
    $_SESSION['message'] = "Successfully Time IN at $logTime. Your code is $randomCode";
    $_SESSION['message_type'] = 'success';

    if (isset($insertLogStmt)) {
        $insertLogStmt->execute();
    }
} elseif (isset($_POST['timeOut'])) {
    if (!$code) {
        $_SESSION['message'] = "Code is required.";
        $_SESSION['message_type'] = 'danger';
        header("Location: ../index.php");
        exit();
    }

    $logTime = date('Y-m-d H:i:s');

    $updateLogQuery = "UPDATE time_logs SET time_out = ? WHERE code = ? AND time_out IS NULL";
    $updateLogStmt = $conn->prepare($updateLogQuery);
    $updateLogStmt->bind_param("ss", $logTime, $code);

    if ($updateLogStmt->execute()) {
        $_SESSION['message'] = "Successfully Time OUT at $logTime";
        $_SESSION['message_type'] = 'danger';

        $deleteCodeQuery = "UPDATE time_logs SET code = NULL WHERE code = ? AND time_out IS NOT NULL";
        $deleteCodeStmt = $conn->prepare($deleteCodeQuery);
        $deleteCodeStmt->bind_param("s", $code);
        $deleteCodeStmt->execute();
    } else {
        $_SESSION['message'] = "Failed to log out.";
        $_SESSION['message_type'] = 'warning';
    }
} else {
    $_SESSION['message'] = "All fields are required.";
    $_SESSION['message_type'] = 'danger';
}

header("Location: ../index.php");
exit();
?>