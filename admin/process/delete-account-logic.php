<?php
include '../../includes/database.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {

    
    $id = $_POST['id']; 

    $checkQuery = "SELECT * FROM account WHERE id = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    
    if ($result->num_rows > 0) {
        $deleteQuery = "DELETE FROM account WHERE id = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            header("Location: ../view-account.php?message=Account deleted successfully");
            exit;
        } else {
            echo "Failed to delete account.";
        }
    } else {
        echo "Account not found.";
    }
} else {
    echo "Invalid request.";
}
?>
