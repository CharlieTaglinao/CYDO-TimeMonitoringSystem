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
function has_permission($permission_id, $user_permissions)
{
    return in_array($permission_id, $user_permissions);
}

?>

<div id="sidebar" class="p-3" style="width: 330px; height: auto; min-height: 100vh;">
    <div class="btn mb-3 w-100">
        <a href="index">
            <img src="../assets/images/CH-LOGO.png" alt="" style="max-height: 100px; width: 280px;">
        </a>
    </div>
    <h2 class="fs-4">WELCOME <span style="color: #2e23a4"><?php echo strtoupper($user_login); ?></span></h2>
    <nav class="nav flex-column">

        <!-- Dashboard -->
        <a href="index" class="nav-link">Dashboard</a>

        <?php if (has_permission('c5pwoB1uPkzwwZgFokRZZ85fE', $user_permissions)) { ?>
            <a href="monitor-visitor" class="nav-link">Monitor</a>
        <?php } ?>

        <!-- Management -->
        <?php if (has_permission('6QmUceiC4tYAFPR8ablg55KFZ', $user_permissions) || has_permission('3DijIfOkS1uljCeXsJoyJAIbt', $user_permissions) || has_permission('M24yNFhXnUXIgzruLUVLrr1dQ', $user_permissions) || has_permission('T9rPHeL7ectsYwT6Ih2AswTeZ', $user_permissions) || has_permission('906IZi3K8od7FBS518t5I31jY', $user_permissions)
        ) { ?>
            <div class="nav-item">
                <a class="nav-link dropdown-toggle" data-bs-toggle="collapse" href="#managementSubmenu" role="button"
                    aria-expanded="false" aria-controls="managementSubmenu">
                    Management
                </a>
                <div class="collapse" id="managementSubmenu">
                    <nav class="nav flex-column ms-3">
                        <!-- Members -->
                        <div class="nav-item">
                            <a class="nav-link dropdown-toggle" data-bs-toggle="collapse" href="#membersSubmenu"
                                role="button" aria-expanded="false" aria-controls="membersSubmenu">
                                Members
                            </a>
                            <div class="collapse" id="membersSubmenu">
                                <nav class="nav flex-column ms-3">
                                    <?php if (has_permission('6QmUceiC4tYAFPR8ablg55KFZ', $user_permissions)) { ?>
                                        <a href="view-application" class="nav-link fw-normal">Application</a>
                                    <?php } ?>
                                    <?php if (has_permission('3DijIfOkS1uljCeXsJoyJAIbt', $user_permissions)) { ?>
                                        <a href="view-activation" class="nav-link fw-normal">Activate/Deactivate</a>
                                    <?php } ?>
                                    <?php if (has_permission('M24yNFhXnUXIgzruLUVLrr1dQ', $user_permissions)) { ?>
                                        <a href="view-member-codes.php" class="nav-link fw-normal">View Member Codes</a>
                                    <?php } ?>
                                </nav>
                            </div>
                        </div>

                        <!-- Accounts -->
                        <div class="nav-item">
                            <a class="nav-link dropdown-toggle" data-bs-toggle="collapse" href="#accountsSubmenu"
                                role="button" aria-expanded="false" aria-controls="accountsSubmenu">
                                Accounts
                            </a>
                            <div class="collapse" id="accountsSubmenu">
                                <nav class="nav flex-column ms-3">
                                    <?php if (has_permission('T9rPHeL7ectsYwT6Ih2AswTeZ', $user_permissions)) { ?>
                                        <a href="#" class="nav-link fw-normal" id="addAccountBtn">Add Account</a>
                                    <?php } ?>
                                    <?php if (has_permission('906IZi3K8od7FBS518t5I31jY', $user_permissions)) { ?>
                                        <a href="view-account" class="nav-link fw-normal">View Account</a>
                                    <?php } ?>
                                </nav>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
        <?php } ?>

        <!-- Monitoring -->
        <?php if (has_permission('GfsdZkrEFuNhmIUmxIm8e7fS8', $user_permissions) || has_permission('qD0mEzTMK6Toi4u8aR1Pdusag', $user_permissions)) { ?>
            <div class="nav-item">
                <a class="nav-link dropdown-toggle" data-bs-toggle="collapse" href="#monitoringSubmenu" role="button"
                    aria-expanded="false" aria-controls="monitoringSubmenu">
                    Monitoring
                </a>
                <div class="collapse" id="monitoringSubmenu">
                    <nav class="nav flex-column ms-3">
                        <?php if (has_permission('GfsdZkrEFuNhmIUmxIm8e7fS8', $user_permissions)) { ?>
                            <a href="report" class="nav-link fw-normal">Reports</a>
                        <?php } ?>
                        <?php if (has_permission('qD0mEzTMK6Toi4u8aR1Pdusag', $user_permissions)) { ?>
                            <a href="analytics" class="nav-link fw-normal">Analytics</a>
                        <?php } ?>
                    </nav>
                </div>
            </div>
        <?php } ?>

        <!-- Settings -->
        <div class="nav-item">
            <a class="nav-link dropdown-toggle" data-bs-toggle="collapse" href="#settingsSubmenu" role="button"
                aria-expanded="false" aria-controls="settingsSubmenu">
                Settings
            </a>
            <div class="collapse" id="settingsSubmenu">
                <nav class="nav flex-column ms-3">
                    <?php if (has_permission('nLpx3AoiT6FJB8BVTGy4D6VE7', $user_permissions) || has_permission('p1YjKW0Ny5bLE64YdGreQJ92w', $user_permissions)) { ?>
                        <a class="nav-link dropdown-toggle fw-normal" data-bs-toggle="collapse" href="#manageRoleSubmenu"
                            role="button" aria-expanded="false" aria-controls="manageRoleSubmenu">
                            Manage Account Types
                        </a>
                        <div class="collapse ms-3" id="manageRoleSubmenu">
                            <nav class="nav flex-column">
                                <?php if (has_permission('nLpx3AoiT6FJB8BVTGy4D6VE7', $user_permissions)) { ?>
                                    <a href="#" class="nav-link fw-normal" id="addAccountTypeBtn">Add Account Type</a>
                                <?php } ?>
                                <?php if (has_permission('p1YjKW0Ny5bLE64YdGreQJ92w', $user_permissions)) { ?>
                                    <a href="view-account-type" class="nav-link fw-normal">View Account Type</a>
                                <?php } ?>
                            </nav>
                        </div>
                    <?php } ?>            
                    
                    <?php if (has_permission('8sAygcnqpOXP8aAAG7IAWI4Cg', $user_permissions) || has_permission('ubmssiHKw9GEPDulEVpDtOudM', $user_permissions)) { ?>
                        <a class="nav-link dropdown-toggle fw-normal" data-bs-toggle="collapse"
                            href="#managePermissionSubmenu" role="button" aria-expanded="false"
                            aria-controls="managePermissionSubmenu">
                            Manage Permissions
                        </a>
                        <div class="collapse ms-3" id="managePermissionSubmenu">
                            <nav class="nav flex-column">
                                <?php if (has_permission('8sAygcnqpOXP8aAAG7IAWI4Cg', $user_permissions)) { ?>
                                    <a href="add-permission" class="nav-link fw-normal" id="addPermissionBtn">Add Permission</a>
                                <?php } ?>
                                <?php if (has_permission('ubmssiHKw9GEPDulEVpDtOudM', $user_permissions)) { ?>
                                    <a href="view-permission" class="nav-link fw-normal">View Permission</a>
                                <?php } ?>
                            </nav>
                        </div>
                    <?php } ?>



                    <a href="change-password" class="nav-link fw-normal">Change Password</a>
                </nav>
            </div>
        </div>

        <hr>
        <button id="dark-mode-toggle"><i class="fas fa-moon"></i></button>
        <!-- Logout -->
        <a href="process/logout.php" class="nav-link" id="logout-button">Logout</a>
    </nav>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Select all dropdown toggles
        const dropdownToggles = document.querySelectorAll('.nav-link.dropdown-toggle');

        dropdownToggles.forEach(toggle => {
            toggle.addEventListener('click', function (event) {
                event.preventDefault();

                // Get the submenu associated with the clicked toggle
                const submenu = document.querySelector(toggle.getAttribute('href'));

                // Close sibling dropdowns only
                const parentNav = toggle.closest('.nav');
                if (parentNav) {
                    const siblingToggles = parentNav.querySelectorAll('.nav-link.dropdown-toggle');
                    siblingToggles.forEach(siblingToggle => {
                        if (siblingToggle !== toggle) {
                            const siblingSubmenu = document.querySelector(siblingToggle.getAttribute('href'));
                            if (siblingSubmenu && siblingSubmenu.classList.contains('show')) {
                                siblingSubmenu.classList.remove('show');
                            }
                        }
                    });
                }

                // Toggle the current submenu
                if (submenu) {
                    submenu.classList.toggle('show');
                }
            });
        });
    });
</script>