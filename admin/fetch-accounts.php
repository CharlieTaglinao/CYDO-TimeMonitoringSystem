<?php 
include '../includes/database.php';

// Set pagination variables
$limit = 10; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; 
$offset = ($page - 1) * $limit; 

// Count total rows with search filter
$totalRowsQuery = "
    SELECT COUNT(*) AS total 
    FROM account 
";
$totalRowsResult = $conn->query($totalRowsQuery);
$totalRows = $totalRowsResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit); 


// Count all admin
$totalAdminQuery = "SELECT COUNT(id) AS total_admin FROM account WHERE role = 1";
$totalAdminResult = $conn->query($totalAdminQuery);
$totalAdmin = $totalAdminResult->fetch_assoc()['total_admin'];

// Count all staff
$totalStaffQuery = "SELECT COUNT(id) AS total_staff FROM account WHERE role = 2";
$totalStaffResult = $conn->query($totalStaffQuery);
$totalStaff = $totalStaffResult->fetch_assoc()['total_staff'];

// Count all users
$totalAllQuery = "SELECT COUNT(*) AS total_all FROM account ";
$totalAllResult = $conn->query($totalAllQuery);
$totalAll = $totalAllResult->fetch_assoc()['total_all'];

// Fetch accounts
$accountQuery = "SELECT * FROM account LIMIT $limit OFFSET $offset";
$accountResult = $conn->query($accountQuery);



?>