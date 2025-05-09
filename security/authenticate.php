<?php

/**
 * Ensure the user is logged in and has the required role.
 * Redirects to login page if no session exists or to a "no access" page if the role is insufficient.
 *
 * @param string $requiredRole 
 */

function checkUserRole($requiredRoles) {
    if (!isset($_SESSION['user_id'], $_SESSION['role'])) {
        session_unset();
        session_destroy();
        header("Location: ../index.php?showLoginModal=true");
        exit();
    }

    if (!in_array($_SESSION['role'], $requiredRoles)) {
        header("Location: ../security/no-access.html");
        exit();
    }
}

function isAuthenticated() {
    include '../includes/database.php';

    $query = "SELECT role FROM role";
    $result = $conn->query($query);

    $roles = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $roles[] = $row['role'];
        }
    }

    checkUserRole($roles);
}
