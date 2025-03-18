<?php
include '../includes/database.php';

// Fetch permissions
$permissionsQuery = "SELECT permission_id, permission_name, category, created_at FROM permission";
$stmt = $conn->prepare($permissionsQuery);
$stmt->execute();
$permissionsResult = $stmt->get_result();

if (isset($_GET['search']) && !isset($_GET['pagination'])) {
    if ($permissionsResult->num_rows > 0) {
        while ($row = $permissionsResult->fetch_assoc()) {
            echo "<tr>
                <td>" . $row['permission_id'] . "</td>
                <td>" . strtoupper($row['permission_name']) . "</td>
                <td>" . $row['category'] . "</td>
                <td>" . date('Y-m-d H:i:s', strtotime($row['created_at'])) . "</td>
            </tr>";
        }
    } else {
        echo "<tr><td colspan='4'>No records found</td></tr>";
    }
    exit;
}
?>
