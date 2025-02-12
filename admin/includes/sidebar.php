
<?php
if (isset($_SESSION['username'])) {
    $user_login = $_SESSION['username'];
} else {
    $user_login = "Guest";
}
?>
<div id="sidebar" class="bg-dark text-white p-3" style="width: 250px; height: 100vh;">
    <div class="btn mb-3 w-100">
        <a href="index.php">
            <img src="../assets/images/CYDO-LOGO.png" alt="" style="max-height: 200px;">
        </a>
    </div>
    <h2 class="fs-4"><?php echo "Welcome " . $user_login?></h2>
    <nav class="nav flex-column">
        <a href="index.php" class="nav-link text-white">Dashboard</a>

        <!-- Account Menu with Submenu -->
        <div class="nav-item">
            <a 
                class="nav-link text-white dropdown-toggle" 
                data-bs-toggle="collapse" 
                href="#accountSubmenu" 
                role="button" 
                aria-expanded="false" 
                aria-controls="accountSubmenu">
                Account
            </a>
            <div class="collapse" id="accountSubmenu">
                <nav class="nav flex-column ms-3">
                <a href="#" class="nav-link text-white" id="addAccountBtn">Add Account</a>
                    <a href="view-account.php" class="nav-link text-white">View Account</a>
                </nav>
            </div>
        </div>

        <a href="visitor-reports.php" class="nav-link text-white">Reports</a>
        <a href="#" class="nav-link text-white">Setting</a>
        <hr>
        
        <a href="process/logout.php" class="nav-link text-white" id="logout-button">Logout</a>
    </nav>
</div>
