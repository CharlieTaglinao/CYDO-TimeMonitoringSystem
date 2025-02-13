<?php

/**
 * Ensure the user is logged in and has the required role.
 * Redirects to login page if no session exists or to a "no access" page if the role is insufficient.
 *
 * @param string $requiredRole 
 */

function checkUserRole($requiredRole) {
    if (!isset($_SESSION['user_id'], $_SESSION['role'])) {
        session_unset();
        session_destroy();
        header("Location: ../index.php?showLoginModal=true");
        
    }

    if ($_SESSION['role'] !== $requiredRole) {
        header("Location: ../security/no-access.html");
        exit();
    }
}

function RoleAsAdmin() {
    checkUserRole('admin');
}
function RoleAsStaff(){
    checkUserRole('staff');
}
