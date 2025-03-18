<?php
include 'includes/database.php'; 

if (isset($_GET['first_name'])) {
    $firstName = $_GET['first_name'];
    $query = "SELECT v.first_name, v.middle_name, v.last_name, e.email, v.age, v.sex_id 
              FROM visitors v 
              LEFT JOIN email e ON v.id = e.client_id
              INNER JOIN sex s ON v.sex_id = s.id 
              WHERE v.first_name = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $firstName);
    $stmt->execute();
    $result = $stmt->get_result();
    $visitor = $result->fetch_assoc();
    echo json_encode($visitor);
}
?>
