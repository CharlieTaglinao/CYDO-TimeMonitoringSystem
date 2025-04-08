<?php


if (isset($_SESSION['username'])) {
    $user_login = $_SESSION['username'];
} else {
    $user_login = "Guest";
}

// Fetch user permissions from the database
$user_permissions = [];
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $query = "SELECT permission_id FROM user_permissions WHERE user_id = $user_id";
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $user_permissions[] = $row['permission_id'];
    }
}

// Function to check if user has a specific permission
function has_permission($permission_id, $user_permissions) {
    return in_array($permission_id, $user_permissions);
}

?>

<div id="sidebar" class="p-3" style="width: 250px; height: auto; min-height: 100vh;">
    <div class="btn mb-3 w-100">
        <a href="index">
            <img src="../assets/images/CYDO-LOGO.png" alt="" style="max-height: 200px;">
        </a>
    </div>
    <h2 class="fs-4"><?php echo "Welcome " . $user_login?></h2>
    <nav class="nav flex-column">

        <a href="index" class="nav-link">Dashboard</a>
        <?php if (has_permission('c5pwoB1uPkzwwZgFokRZZ85fE', $user_permissions)) { ?>
            <a href="monitor-visitor" class="nav-link">Monitor</a>
        <?php } ?>
        <?php if (has_permission('GfsdZkrEFuNhmIUmxIm8e7fS8', $user_permissions)) { ?>
            <a href="report" class="nav-link">Reports</a>
        <?php } ?>
        <?php if (has_permission('qD0mEzTMK6Toi4u8aR1Pdusag', $user_permissions)) { ?>
            <a href="analytics" class="nav-link">Analytics</a>
        <?php } ?>

        <!-- Account Menu with Submenu -->
        <?php if (has_permission('T9rPHeL7ectsYwT6Ih2AswTeZ', $user_permissions) || has_permission('906IZi3K8od7FBS518t5I31jY', $user_permissions)) { ?>
            <div class="nav-item">
                <a 
                    class="nav-link dropdown-toggle" 
                    data-bs-toggle="collapse" 
                    href="#accountSubmenu" 
                    role="button" 
                    aria-expanded="false" 
                    aria-controls="accountSubmenu">
                    Account
                </a>
                <div class="collapse" id="accountSubmenu">
                    <nav class="nav flex-column ms-3">
                        <?php if (has_permission('T9rPHeL7ectsYwT6Ih2AswTeZ', $user_permissions)) { ?>
                            <a href="#" class="nav-link" id="addAccountBtn">Add Account</a>
                        <?php } ?>
                        <?php if (has_permission('906IZi3K8od7FBS518t5I31jY', $user_permissions)) { ?>
                            <a href="view-account" class="nav-link">View Account</a>
                        <?php } ?>
                    </nav>
                </div>
            </div>
        <?php } ?>

        <!-- Manage role Menu with Submenu -->
        <?php if (has_permission('8sAygcnqpOXP8aAAG7IAWI4Cg', $user_permissions)) { ?>
            <div class="nav-item">
                <a 
                    class="nav-link dropdown-toggle" 
                    data-bs-toggle="collapse" 
                    href="#manageRoleSubmenu" 
                    role="button" 
                    aria-expanded="false" 
                    aria-controls="manageRoleSubmenu">
                    Others
                </a>
                <div class="collapse" id="manageRoleSubmenu">
                    <nav class="nav flex-column ms-3">
                        <a href="add-permission" class="nav-link">Add Permission</a>
                        <a href="view-permission" class="nav-link" id="viewPermissionsBtn">View Permissions</a>
                    </nav>
                </div>
            </div>
        <?php } ?>

        <!-- Change Password for Everyone -->
        <a href="change-password" class="nav-link">Change Password</a>

        <hr>

        <button id="dark-mode-toggle"><i class="fas fa-moon"></i> Dark Mode</button>

        <a href="process/logout.php" class="nav-link" id="logout-button">Logout</a>
    </nav>
    
</div>
