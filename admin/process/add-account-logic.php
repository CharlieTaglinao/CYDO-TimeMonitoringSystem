<?php 
include '../../includes/database.php';

date_default_timezone_set('Asia/Manila');

$username = htmlspecialchars(trim($_POST['username']));

$password = $_POST['password'];
$role = $_POST['role'];

$hashedpassword = password_hash($password, PASSWORD_DEFAULT);


$AddAccountQuery = "INSERT INTO account( `username`, `password`, `role`,created_at) VALUES (?,?,?,NOW())";
$AddAccountStmt = $conn->prepare($AddAccountQuery);
$AddAccountStmt->bind_param("ssi",$username,$hashedpassword,$role);
$AddAccountStmt->execute();


header("Location: ../index.php");





?>