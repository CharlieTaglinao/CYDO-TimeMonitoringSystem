<?php 

include '../includes/database.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$userId = $_SESSION['user_id']; 
$userPermissions = [];
$query = "SELECT permission_id FROM user_permissions WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $userPermissions[] = $row['permission_id'];
}
$_SESSION['user_permissions'] = $userPermissions;


$permissionQuery = "SELECT permission_id FROM permission WHERE permission_name = 'Accept/Decline Application'";
$permissionResult = $conn->query($permissionQuery);
$accessDashboardPermissionId = $permissionResult->fetch_assoc()['permission_id'];

if (!in_array($accessDashboardPermissionId, $_SESSION['user_permissions'])) {
    session_unset();
    session_destroy();
    header('Location: ../security/no-access.html'); 
    exit;
}


?>