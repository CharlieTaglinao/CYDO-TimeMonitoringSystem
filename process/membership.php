<?php
session_start();
include '../includes/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize input data
    $first_name = strtoupper(trim($_POST['first_name']));
    $middle_name = strtoupper(trim($_POST['middle_name']));
    $last_name = strtoupper(trim($_POST['last_name']));
    $age = intval($_POST['age']);
    $email = trim($_POST['email']);
    $school_name = strtoupper(trim($_POST['school_name'])); 
    $barangay_id = intval($_POST['barangay']);
    $sex_id = intval($_POST['sex']);

    // Validate required fields
    if (empty($first_name) || empty($last_name) || empty($age) || empty($school_name) || empty($barangay_id) || empty($sex_id)) {
        $_SESSION['message'] = 'Please fill in all required fields.';
        $_SESSION['message_type'] = 'danger';
        header('Location: ../membership.php');
        exit();
    }

    // Check if the applicant is already a member in the visitors table
    $stmt = $conn->prepare(
        "SELECT COUNT(*) FROM visitors WHERE first_name = ? AND last_name = ? AND type = 'MEMBER'"
    );
    $stmt->bind_param('ss', $first_name, $last_name);
    $stmt->execute();
    $stmt->bind_result($member_count);
    $stmt->fetch();
    $stmt->close();

    if ($member_count > 0) {
        $_SESSION['message'] = 'You are already a registered member.';
        $_SESSION['message_type'] = 'warning';
        header('Location: ../membership.php');
        exit();
    }

    // Check if an application already exists for the given email
    $stmt = $conn->prepare(
        "SELECT COUNT(*) FROM member_applicants WHERE first_name = ? AND last_name = ?"
    );
    $stmt->bind_param('ss', $first_name, $last_name);
    $stmt->execute();
    $stmt->bind_result($fullName_count);
    $stmt->fetch();
    $stmt->close();

    if ($fullName_count > 0) {
        $_SESSION['message'] = 'Already submitted an application. Ask mentor or coordinator for activating your membership.';
        $_SESSION['message_type'] = 'warning';
        header('Location: ../membership.php');
        exit();
    }

    $conn->begin_transaction();

    try {
        // Insert email into member_email table
        $stmt = $conn->prepare("INSERT INTO member_email (email) VALUES (?)");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $email_id = $stmt->insert_id;
        $stmt->close();

        // Insert school name into member_school_name table
        $stmt = $conn->prepare("INSERT INTO member_school_name (school_name) VALUES (?)");
        $stmt->bind_param('s', $school_name);
        $stmt->execute();
        $school_id = $stmt->insert_id;
        $stmt->close();

        // Insert applicant data into member_applicants table
        $stmt = $conn->prepare(
            "INSERT INTO member_applicants (first_name, middle_name, last_name, age, email_id, school_id, barangay_id, sex_id, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, null)"
        );
        $stmt->bind_param('sssiiiii', $first_name, $middle_name, $last_name, $age, $email_id, $school_id, $barangay_id, $sex_id);
        $stmt->execute();
        $stmt->close();

        $conn->commit();

        $_SESSION['message'] = 'Membership application submitted successfully.';
        $_SESSION['message_type'] = 'success';
        header('Location: ../membership.php');
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['message'] = 'An error occurred while processing your application. Please try again.';
        $_SESSION['message_type'] = 'danger';
        header('Location: ../membership.php');
        exit();
    }
} else {
    $_SESSION['message'] = 'Invalid request method.';
    $_SESSION['message_type'] = 'danger';
    header('Location: ../membership.php');
    exit();
}
