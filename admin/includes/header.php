
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../security/authenticate.php';
RoleAsAdmin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <script src="assets/js/popper.js"></script>
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/sweetalert.js"></script>
    <script src="assets/js/jquery.js"></script>
    <link rel="stylesheet" href="assets/css/loader.css">
</head>

<div id="modalContainer"></div>