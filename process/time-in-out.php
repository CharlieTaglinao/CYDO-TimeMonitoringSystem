<?php
session_start();

date_default_timezone_set('Asia/Manila');

include '../includes/database.php';

$firstName = strtoupper(trim($_POST['firstName'])) ?? null;
$middleName = strtoupper(trim($_POST['middleName'])) ?? null;
$lastName = strtoupper(trim($_POST['lastName'])) ?? null;
$email = strtoupper(trim($_POST['email'])) ?? null;
$purpose = strtoupper(trim($_POST['purpose'])) ?? null;
$sex = strtoupper(trim($_POST['sex'])) ?? null;
$age = strtoupper(trim($_POST['age'])) ?? null;
$code = strtoupper(trim($_POST['code'])) ?? null;

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
    } elseif (!$purpose) {
        $_SESSION['message'] = "Purpose is required.";
        $_SESSION['message_type'] = 'danger';
        header("Location: ../index.php");
        exit();
    }elseif (!$age){
        $_SESSION['message'] = "Age is required.";
        $_SESSION['message_type'] = 'danger';
        header("Location: ../index.php");
        exit();
    }

    $logTime = date('Y-m-d H:i:s');

    // Check if the visitor exists in the database
    $checkFullNameQuery = "SELECT id FROM visitors WHERE first_name = ? AND middle_name = ? AND last_name = ?";
    $checkFullNameStmt = $conn->prepare($checkFullNameQuery);
    $checkFullNameStmt->bind_param("sss", $firstName, $middleName, $lastName);
    $checkFullNameStmt->execute();
    $checkFullNameStmt->store_result();

    if ($checkFullNameStmt->num_rows > 0) {
        $checkFullNameStmt->bind_result($clientId);
        $checkFullNameStmt->fetch();
    } else {
        $insertQuery = "INSERT INTO visitors (first_name, middle_name, last_name, sex_id, age) VALUES (?, ?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param("sssii", $firstName, $middleName, $lastName, $sex, $age);
        $insertStmt->execute();
        $clientId = $conn->insert_id;
    }

    // Check if the user has already timed in without timing out
    $checkTimeLogQuery = "SELECT id FROM time_logs WHERE client_id = ? AND time_out IS NULL";
    $checkTimeLogStmt = $conn->prepare($checkTimeLogQuery);
    $checkTimeLogStmt->bind_param("i", $clientId);
    $checkTimeLogStmt->execute();
    $checkTimeLogStmt->store_result();

    if ($checkTimeLogStmt->num_rows > 0) {
        $_SESSION['message'] = "You are already timed in. Please time out before recording a new time in.";
        $_SESSION['message_type'] = 'danger';
        header("Location: ../index.php");
        exit();
    }

    // Allow a new time-in
    $randomCode = generateRandomCode();

    // Insert purpose
    $insertPurposeQuery = "INSERT INTO purpose (client_id, purpose) VALUES (?, ?)";
    $insertPurposeStmt = $conn->prepare($insertPurposeQuery);
    $insertPurposeStmt->bind_param("is", $clientId, $purpose);
    $insertPurposeStmt->execute();

    // Insert new time log
    $insertLogQuery = "INSERT INTO time_logs (client_id, time_in, code) VALUES (?, ?, ?)";
    $insertLogStmt = $conn->prepare($insertLogQuery);
    $insertLogStmt->bind_param("iss", $clientId, $logTime, $randomCode);

    if ($insertLogStmt->execute()) {
        $_SESSION['message'] = "Successfully Time IN at $logTime. Your code is $randomCode";
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = "Failed to log in.";
        $_SESSION['message_type'] = 'danger';
    }

} elseif (isset($_POST['timeOut'])) {
    if (!$code) {
        $_SESSION['message'] = "Code is required.";
        $_SESSION['message_type'] = 'danger';
        header("Location: ../index.php");
        exit();
    }

    $validateCodeQuery = "SELECT id FROM time_logs WHERE code = ? AND time_out IS NULL";
    $validateCodeStmt = $conn->prepare($validateCodeQuery);
    $validateCodeStmt->bind_param("s", $code);
    $validateCodeStmt->execute();
    $validateCodeStmt->store_result();

    if ($validateCodeStmt->num_rows === 0) {
        $_SESSION['message'] = "The code is invalid or has already been used. Please check your code. If you have forgotten your code,kindly ask the admin or staff for assistance.";
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
        $_SESSION['message_type'] = 'success';

        // Clear the code after successful Time Out
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