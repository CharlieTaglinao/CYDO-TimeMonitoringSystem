<?php
include '../../includes/database.php';  
session_start();


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check and insert
if (!empty($_POST['account_type_name'])) {
    $role = $conn->real_escape_string(trim($_POST['account_type_name']));
    $stmt = $conn->prepare("INSERT INTO role (`role`) VALUES (?)");
    $stmt->bind_param("s", $role);
    if ($stmt->execute()) {
        $_SESSION['show_modal'] = false;
        header("Location: ../view-account-type.php?success=1");
    } else {
        $_SESSION['show_modal'] = true;
        header("Location: ../add-role-modal.php?error=" . urlencode($stmt->error));
    }
    $stmt->close();
} else {
    $_SESSION['show_modal'] = true;
    header("Location: ../add-role-modal.php?error=Name%20cannot%20be%20empty");
}

$conn->close();
exit;