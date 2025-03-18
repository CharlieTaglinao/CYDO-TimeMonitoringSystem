<?php
include '../../includes/database.php';

$userId = $_GET['user_id'];
$response = ['permissions' => []];

if ($userId) {
    $query = "SELECT permission_id FROM user_permissions WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $response['permissions'][] = $row['permission_id'];
    }
}

echo json_encode($response);
?>
    