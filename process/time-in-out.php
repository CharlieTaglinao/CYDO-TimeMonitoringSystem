<?php
session_start();
date_default_timezone_set('Asia/Manila');

include '../includes/database.php';

$firstName = strtoupper($_POST['firstName'] ?? '');
$middleName = strtoupper($_POST['middleName'] ?? '');
$lastName = strtoupper($_POST['lastName'] ?? '');
$email = $_POST['email'] ?? '';
$officename = strtoupper(trim($_POST['office']));
$barangayname = strtoupper(trim($_POST['barangay']));
$purpose = strtoupper(trim($_POST['purpose'] ?? ''));
$sex = strtoupper(trim($_POST['sex'] ?? ''));
$age = (int) ($_POST['age'] ?? 0);
$code = strtoupper(trim($_POST['code'] ?? ''));
$checkBoxNotAvailEmail = isset($_POST['noEmail']) ? $_POST['noEmail'] : false;


$_SESSION['first_name'] = $firstName;
$_SESSION['middle_name'] = $middleName;
$_SESSION['last_name'] = $lastName;

function generateRandomCode($length = 6)
{
    return substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $length);
}

if (isset($_POST['timeIn'])) {
    // Validation
    if (!$firstName || !$middleName || !$lastName || !$purpose || !$age) {
        $_SESSION['message'] = "All required fields must be filled out.";
        $_SESSION['message_type'] = 'danger';
        header("Location: ../index.php");
        exit();
    }

    $logTime = date('Y-m-d H:i:s');

    // Check if client is already timed in
    $clientId = null;

    $checkFullNameQuery = "SELECT id FROM visitors WHERE first_name = ? AND middle_name= ? AND last_name = ?";
    $checkFullNameStmt = $conn->prepare($checkFullNameQuery);
    $checkFullNameStmt->bind_param("sss", $firstName, $middleName, $lastName);
    $checkFullNameStmt->execute();
    $checkFullNameStmt->store_result();

    if ($checkFullNameStmt->num_rows > 0) {
        $checkFullNameStmt->bind_result($clientId);
        $checkFullNameStmt->fetch();
    } else {
        // Insert new visitor
    $insertQuery = "INSERT INTO visitors (first_name, middle_name, last_name, sex_id, purpose_id, office_id, barangay_id, age) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $insertStmt = $conn->prepare($insertQuery);
    $insertStmt->bind_param("sssiiiii", $firstName, $middleName, $lastName, $sex, $purpose, $officename, $barangayname, $age);
    $insertStmt->execute();
    $clientId = $conn->insert_id;

        // Insert email
        $emailValue = $checkBoxNotAvailEmail ? 'This visitor has no email' : $email;
        $insertEmailQuery = "INSERT INTO email (client_id, email) VALUES (?, ?)";
        $insertEmailStmt = $conn->prepare($insertEmailQuery);
        $insertEmailStmt->bind_param("is", $clientId, $emailValue);
        $insertEmailStmt->execute();
    }

    // Check if the client is already timed in
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

    // Update Purpose_id
    $updatePurposeIdQuery = "UPDATE visitors SET visitors.purpose_id = ? WHERE visitors.id = ?";
    $updatePurposeIdStmt = $conn->prepare($updatePurposeIdQuery);
    $updatePurposeIdStmt->bind_param("ii",$clientId,$clientId);
    $updatePurposeIdStmt->execute();

    // Insert new time log
    $insertLogQuery = "INSERT INTO time_logs (client_id, time_in, code,status) VALUES (?, ?, ?, 'On Site')";
    $insertLogStmt = $conn->prepare($insertLogQuery);
    $insertLogStmt->bind_param("iss", $clientId, $logTime, $randomCode);

    if ($insertLogStmt->execute()) {
        $_SESSION['showQRModal'] = true;
        $_SESSION['randomCode'] = $randomCode;
        $_SESSION['message'] = "Successfully Time IN at $logTime. Your code is $randomCode";
        $_SESSION['message_type'] = 'success';
        include '../includes/generate-qr-code.php';
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
        $_SESSION['message'] = "Invalid or used code. Ask admin/staff for assistance.";
        $_SESSION['message_type'] = 'danger';
        header("Location: ../index.php");
        exit();
    }

    $logTime = date('Y-m-d H:i:s');

    $updateLogQuery = "UPDATE time_logs SET time_out = ?, code = null, status = 'User Logout' WHERE code = ? AND time_out IS NULL";
    $updateLogStmt = $conn->prepare($updateLogQuery);
    $updateLogStmt->bind_param("ss", $logTime, $code);

    if ($updateLogStmt->execute()) {
        // Delete the used code file in the qrcodes folder
        $qrCodeFile = "../qrcodes/$code.png";
        if (file_exists($qrCodeFile)) {
            unlink($qrCodeFile);
        }

        $_SESSION['message'] = "Successfully Time OUT at $logTime";
        $_SESSION['message_type'] = 'success';
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
