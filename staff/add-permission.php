<?php include 'includes/header.php'; 
    include 'fetch-permission.php'; 
    include 'permission/permissionAddPermission.php';
?>

<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>
        <!-- Main Content -->
        <div class="flex-grow-1 p-4">
            <div class="container-fluid mt-4">


                <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show mt-3" role="alert">
                    <?php echo $_SESSION['message'];
                    unset($_SESSION['message']);
                    unset($_SESSION['message_type']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                    
                <div class="mt-4">
                    <h3>Add Permissions</h3>
                    
                    <form method="POST" action="process/save-permission-logic.php">
                        <div class="mb-4">
                            <label for="userSelect" class="form-label">Select User</label>
                            <select class="form-select" id="userSelect" name="user_id" onchange="showPermissions()">
                                <option value="">Select a user</option>
                                <?php
                                 // Fetch users from the database
                                 $usersQuery = "SELECT id, username, role FROM account";
                                 $usersResult = $conn->query($usersQuery);
                                 
                                 if ($usersResult->num_rows > 0) {
                                     while ($user = $usersResult->fetch_assoc()) {
                    
                                         echo "<option value='" . $user['id'] . "' data-role='" . $user['role'] . "'>" . $user['username'] . "</option>";
                                     }
                                 }
                                ?>
                            </select>
                        </div>
                    
                        <div id="permissionsContainer" style="display: none;">
                            <?php
                            $categories = [];
                            if ($permissionsResult->num_rows > 0) {
                                while ($row = $permissionsResult->fetch_assoc()) {
                                    if ($currentUserRole == 2) {
                                        $restrictedPermissions = ['8sAygcnqpOXP8aAAG7IAWI4Cg', 'ubmssiHKw9GEPDulEVpDtOudM'];
                                        if (in_array($row['permission_id'], $restrictedPermissions)) {
                                            continue;
                                        }
                                    }
                                   
                                    
                                    $categories[$row['category']][] = $row;   
                                }
                            }

                            foreach ($categories as $category => $permissions) {
                                echo "<h4>" . ucfirst($category) . "</h4><div class='row'>";
                                $count = 0;
                                foreach ($permissions as $row) {
                                    $shortId = substr($row['permission_id'], 0, 6) . '****';
                                    if ($count % 4 == 0 && $count != 0) {
                                        echo "</div><div class='row'>";
                                    }
                                    echo "<div class='col-md-3 mb-3'>
                                        <div class='card' id='permission" .'_'. str_replace(' ', '_', $row['permission_name']) . "'>
                                            <div class='card-body text-dark'>
                                                <h6 class='card-title'>" . strtoupper($row['permission_name']) . "</h6>
                                                <p class='card-subtitle mb-2 text-sm fw-lighter'>ID: " . $shortId . "</p>
                                                <div class='form-check form-switch'>
                                                    <input class='form-check-input' type='checkbox' id='permission" . $row['permission_id'] . "' name='permissions[]' value='" . $row['permission_id'] . "'>
                                                    <label class='form-check-label' for='permission" . $row['permission_id'] . "'>OFF / ON</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>";
                                    $count++;
                                }
                                echo "</div>";
                            }
                            ?>
                        </div>
                        <button type="submit" class="btn btn-success w-100 mt-3">SAVE</button>
                    </form>
                </div>

            </div>
        </div>
    </div>
    <script src="assets/js/permission.js"></script>
    <script>
    
    </script>
</body>

</html>