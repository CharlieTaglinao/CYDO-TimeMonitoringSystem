<?php
session_start();

// Regenerate session ID to prevent session fixation attacks
session_regenerate_id(true);

// Unset all session variables
$_SESSION = [];

// Destroy the session
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Clear all other cookies
foreach ($_COOKIE as $key => $value) {
    setcookie($key, '', time() - 42000, '/');
}

session_destroy();

// Redirect to the login page
header("Location: ../../login");
exit();
?>