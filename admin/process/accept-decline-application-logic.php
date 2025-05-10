<?php
include '../../includes/database.php';  
session_start();

if (isset($_POST['application_id']) && isset($_POST['action'])) {
    $applicationId = $_POST['application_id'];
    $action = $_POST['action'];

    // Determine the status based on the action
    $status = $action === 'accept' ? 'FOR ACTIVATION' : 'DECLINED';

    // Update the status of the application
    $updateQuery = "UPDATE member_applicants SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param('si', $status, $applicationId);

    if ($stmt->execute()) {
        // Fetch member applicant details
        $applicantQuery = "SELECT * FROM member_applicants WHERE id = ?";
        $applicantStmt = $conn->prepare($applicantQuery);
        $applicantStmt->bind_param('i', $applicationId);
        $applicantStmt->execute();
        $applicantResult = $applicantStmt->get_result();

        if ($applicantResult->num_rows > 0) {
            $applicant = $applicantResult->fetch_assoc();

            // Ensure school_id exists in visitor_school_name table
            $schoolValidationQuery = "SELECT id FROM visitor_school_name WHERE id = ?";
            $schoolValidationStmt = $conn->prepare($schoolValidationQuery);
            $schoolValidationStmt->bind_param('i', $applicant['school_id']);
            $schoolValidationStmt->execute();
            $schoolValidationResult = $schoolValidationStmt->get_result();

            if ($schoolValidationResult->num_rows === 0) {
                // Insert school_id into visitor_school_name table
                $insertSchoolQuery = "INSERT INTO visitor_school_name (id, school_name) SELECT id, school_name FROM member_school_name WHERE id = ?";
                $insertSchoolStmt = $conn->prepare($insertSchoolQuery);
                $insertSchoolStmt->bind_param('i', $applicant['school_id']);
                $insertSchoolStmt->execute();
                $insertSchoolStmt->close();
            }

            $schoolValidationStmt->close();

            // Insert into visitors table
            $visitorInsertQuery = "INSERT INTO visitors (first_name, middle_name, last_name, sex_id, school_id, barangay_id, age, type, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, 'MEMBER', NOW())";
            $visitorStmt = $conn->prepare($visitorInsertQuery);
            $visitorStmt->bind_param('sssiiii', $applicant['first_name'], $applicant['middle_name'], $applicant['last_name'], $applicant['sex_id'], $applicant['school_id'], $applicant['barangay_id'], $applicant['age']);
            $visitorStmt->execute();

            // Generate unique membership code
            $membershipCode = 'CH-' . date('Y') . '-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));

            // Insert into member_code table
            $memberCodeInsertQuery = "INSERT INTO member_code (membership_code, created_at) VALUES (?, NOW())";
            $memberCodeStmt = $conn->prepare($memberCodeInsertQuery);
            $memberCodeStmt->bind_param('s', $membershipCode);
            $memberCodeStmt->execute();

            // Update visitor record with membership ID
            $membershipId = $conn->insert_id;
            $updateVisitorQuery = "UPDATE visitors SET membership_id = ? WHERE id = ?";
            $updateVisitorStmt = $conn->prepare($updateVisitorQuery);
            $visitorId = $conn->insert_id;
            $updateVisitorStmt->bind_param('ii', $membershipId, $visitorId);
            $updateVisitorStmt->execute();

            $memberCodeStmt->close();
            $visitorStmt->close();
            $updateVisitorStmt->close();
        }

        $applicantStmt->close();

        $_SESSION['message'] = $action === 'accept' ? 'Application accepted successfully.' : 'Application declined successfully.';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Failed to process the application. Please try again.';
        $_SESSION['message_type'] = 'danger';
    }
    

    $stmt->close();
    header('Location: ../view-activation.php');
    exit;
} else {
    $_SESSION['message'] = 'Invalid request.';
    $_SESSION['message_type'] = 'danger';
    header('Location: ../view-application.php');
    exit;
}
?>