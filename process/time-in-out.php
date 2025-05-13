<?php
session_start();
date_default_timezone_set('Asia/Manila');

include '../includes/database.php';

$firstName = strtoupper($_POST['firstName'] ?? '');
$middleName = strtoupper($_POST['middleName'] ?? '');
$lastName = strtoupper($_POST['lastName'] ?? '');
$email = $_POST['email'] ?? '';
$schoolName = strtoupper(trim($_POST['schoolname']));
$barangayname = strtoupper(trim($_POST['barangayname']));
$purpose = strtoupper(trim($_POST['purpose'] ?? ''));
$sex = strtoupper(trim($_POST['sex'] ?? ''));
$age = (int) ($_POST['age'] ?? 0);
$code = trim($_POST['membership_code'] ?? '');  
$isMember = $_POST['isMember'] ?? '';

$_SESSION['first_name'] = $firstName;
$_SESSION['middle_name'] = $middleName;
$_SESSION['last_name'] = $lastName;

function generateRandomCode($length = 6)
{
    return substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $length);
}

if (isset($_POST['timeIn']) || isset($_POST['timeOut'])) {
    if ($isMember === 'yes') {
        // Check if the membership code is valid and fetch the status
        $checkCodeQuery = "SELECT id, status FROM member_code WHERE membership_code = ?";
        $checkCodeStmt = $conn->prepare($checkCodeQuery);
        $checkCodeStmt->bind_param("s", $code);
        $checkCodeStmt->execute();
        $checkCodeStmt->store_result();

        if ($checkCodeStmt->num_rows === 0) {
            $_SESSION['message'] = "Invalid membership code.";
            $_SESSION['message_type'] = 'danger';
            header("Location: ../index.php");
            exit();
        }

        // Fetch the membership ID and status
        $checkCodeStmt->bind_result($membershipId, $membershipStatus);
        $checkCodeStmt->fetch();

        // Check if the membership is deactivated
        if (strtoupper($membershipStatus) === 'DEACTIVATED') {
            $_SESSION['message'] = "Your membership is deactivated. Please contact an administrator.";
            $_SESSION['message_type'] = 'danger';   
            header("Location: ../index.php");
            exit();
        } else if ($membershipStatus === null) {
            $_SESSION['message'] = "Your membership is for activation, Please ask the administrator to activate your membership.";
            $_SESSION['message_type'] = 'info';
            header("Location: ../index.php");
            exit();
        }

        
    }

    if (isset($_POST['timeIn'])) {

        $logTime = date('Y-m-d H:i:s');

        if ($isMember === 'yes') {
            // Ensure the visitor is associated with the membership ID
            $updateMembershipQuery = "UPDATE visitors SET membership_id = ? WHERE first_name = ? AND last_name = ? AND membership_id IS NULL";
            $updateMembershipStmt = $conn->prepare($updateMembershipQuery);
            $updateMembershipStmt->bind_param("iss", $membershipId, $firstName, $lastName);
            $updateMembershipStmt->execute();

            // Get the client ID associated with the membership code
            $getClientIdQuery = "SELECT id FROM visitors WHERE membership_id = ?";
            $getClientIdStmt = $conn->prepare($getClientIdQuery);
            $getClientIdStmt->bind_param("i", $membershipId);
            $getClientIdStmt->execute();
            $getClientIdStmt->store_result();

            if ($getClientIdStmt->num_rows === 0) {
                $_SESSION['message'] = "No client found with this membership code.";
                $_SESSION['message_type'] = 'danger';
                header("Location: ../index.php");
                exit();
            }

            // Fetch the client ID
            $getClientIdStmt->bind_result($clientId);
            $getClientIdStmt->fetch();
        } else {
            // Guest logic: no membership code generation
            $clientId = null;

            // Handle visitor_school_name table
            $schoolName = strtoupper(trim($_POST['schoolname'] ?? ''));
            $checkSchoolQuery = "SELECT id FROM visitor_school_name WHERE school_name = ?";
            $checkSchoolStmt = $conn->prepare($checkSchoolQuery);
            $checkSchoolStmt->bind_param("s", $schoolName);
            $checkSchoolStmt->execute();
            $checkSchoolStmt->store_result();
            if ($checkSchoolStmt->num_rows > 0) {
                $checkSchoolStmt->bind_result($schoolId);
                $checkSchoolStmt->fetch();
            } else {
                $insertSchoolQuery = "INSERT INTO visitor_school_name (school_name) VALUES (?)";
                $insertSchoolStmt = $conn->prepare($insertSchoolQuery);
                $insertSchoolStmt->bind_param("s", $schoolName);
                $insertSchoolStmt->execute();
                $schoolId = $conn->insert_id;
            }

            // Insert new visitor without membership_id
            $insertQuery = "INSERT INTO visitors (first_name, middle_name, last_name, sex_id, purpose_id, school_id, barangay_id,membership_id, age, type, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, null, ?, 'GUEST', NOW())";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("sssiiiii", $firstName, $middleName, $lastName, $sex, $purpose, $schoolId, $barangayname, $age);
            $insertStmt->execute();
            $clientId = $conn->insert_id;

            $insertEmailQuery = "INSERT INTO email (client_id, email) VALUES (?, ?)";
            $insertEmailStmt = $conn->prepare($insertEmailQuery);
            $insertEmailStmt->bind_param("is", $clientId, $email);
            $insertEmailStmt->execute();

            // Insert purpose
            $insertPurposeQuery = "INSERT INTO purpose (client_id, purpose) VALUES (?, ?)";
            $insertPurposeStmt = $conn->prepare($insertPurposeQuery);
            $insertPurposeStmt->bind_param("is", $clientId, $purpose);
            $insertPurposeStmt->execute();

            // Update Purpose_id
            $updatePurposeIdQuery = "UPDATE visitors SET visitors.purpose_id = ? WHERE visitors.id = ?";
            $updatePurposeIdStmt = $conn->prepare($updatePurposeIdQuery);
            $updatePurposeIdStmt->bind_param("ii", $clientId, $clientId);
            $updatePurposeIdStmt->execute();

            // Generate a temporary code for the guest
            $temporaryCode = generateRandomCode();

            // Insert the temporary code into the time_logs table
            $insertLogQuery = "INSERT INTO time_logs (client_id, time_in, status, code) VALUES (?, ?, 'On Site', ?)";
            $insertLogStmt = $conn->prepare($insertLogQuery);
            $insertLogStmt->bind_param("iss", $clientId, $logTime, $temporaryCode);

            if ($insertLogStmt->execute()) {
                $_SESSION['temporary_code'] = $temporaryCode; // Set the temporary code in the session
                $_SESSION['message'] = "Successfully Time IN at $logTime. Your temporary code is $temporaryCode.";
                $_SESSION['message_type'] = 'success';
                header("Location: ../index.php");
                exit();
            } else {
                $_SESSION['message'] = "Failed to log in.";
                $_SESSION['message_type'] = 'danger';
                header("Location: ../index.php");
                exit();
            }
        }

        // Ensure clientId is set before checking time-in status
        if ($clientId) {
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
        }

        // Allow a new time-in with membership code
        $insertLogQuery = "INSERT INTO time_logs (client_id, time_in, status, code) VALUES (?, ?, 'On Site', ?)";
        $insertLogStmt = $conn->prepare($insertLogQuery);
        $insertLogStmt->bind_param("iss", $clientId, $logTime, $code);

        if ($insertLogStmt->execute()) {
            $_SESSION['message'] = "Successfully Time IN at $logTime.";
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = "Failed to log in.";
            $_SESSION['message_type'] = 'danger';
        }
    } elseif (isset($_POST['timeOut'])) {
        $code = trim($_POST['code'] ?? ''); // Ensure code is retrieved from the form

        if (empty($code)) {
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
            $_SESSION['message'] = "Invalid or expired code. Please contact an administrator for assistance.";
            $_SESSION['message_type'] = 'danger';
            header("Location: ../index.php");
            exit();
        }

        $logTime = date('Y-m-d H:i:s');

        $updateLogQuery = "UPDATE time_logs SET time_out = ?, status = 'User Logout' WHERE code = ? AND time_out IS NULL";
        $updateLogStmt = $conn->prepare($updateLogQuery);
        $updateLogStmt->bind_param("ss", $logTime, $code);

        if ($updateLogStmt->execute()) {
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
}

header("Location: ../index.php");
exit();
