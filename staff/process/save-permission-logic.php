<?php
include '../../includes/database.php';

$userId = $_POST['user_id'];
$permissions = isset($_POST['permissions']) ? $_POST['permissions'] : [];

$response = ['success' => false];

if ($userId && is_array($permissions)) {
    // Fetch existing permissions
    $existingPermissions = [];
    $query = "SELECT permission_id FROM user_permissions WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $existingPermissions[] = $row['permission_id'];
    }

    // Determine permissions to delete and insert
    $permissionsToDelete = array_diff($existingPermissions, $permissions);
    $permissionsToInsert = array_diff($permissions, $existingPermissions);

    // Delete permissions that are not checked
    if (!empty($permissionsToDelete)) {
        $deleteQuery = "DELETE FROM user_permissions WHERE user_id = ? AND permission_id = ?";
        $stmt = $conn->prepare($deleteQuery);
        if (!$stmt) {
            $response['error'] = $conn->error;
            echo json_encode($response);
            exit;
        }
        foreach ($permissionsToDelete as $permissionId) {
            $stmt->bind_param('is', $userId, $permissionId);
            $stmt->execute();
        }
    }

    // Insert new permissions that are checked
    if (!empty($permissionsToInsert)) {
        $insertQuery = "INSERT INTO user_permissions (user_id, permission_id) VALUES (?, ?)";
        $stmt = $conn->prepare($insertQuery);
        if (!$stmt) {
            $response['error'] = $conn->error;
            echo json_encode($response);
            exit;
        }
        foreach ($permissionsToInsert as $permissionId) {
            $stmt->bind_param('is', $userId, $permissionId);
            if (!$stmt->execute()) {
                $response['error'] = $stmt->error;
                echo json_encode($response);
                exit;
            }
        }
    }

    $response['success'] = true;
} else {
    $response['error'] = 'Invalid input data';
}

header('Location: ../add-permission.php');
?>
