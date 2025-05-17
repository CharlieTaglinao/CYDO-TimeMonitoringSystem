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
                    <h3>Configure User's Permissions</h3>
                    
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
                                 
                                   
                                    
                                    $categories[$row['category']][] = $row;   
                                }
                            }
                                      // Toggle All switch
                                echo "<div class='card mb-3'>";
                                echo "<div class='card-header fw-bold bg-dark'>" . 'TOGGLE ALL'. "</div>";
                                echo "<div class='card-body'>";
                                echo "<div class='d-flex justify-content-between align-items-center mb-2'>";
                                echo "<span class='fw-normal'>Access all permission</span>";
                                echo "<div class='form-check form-switch'>";
                                echo "<input class='form-check-input' type='checkbox' id='toggleAllPermissions'>";
                                echo "<label class='form-check-label' for='toggleAllPermissions'>OFF / ON</label>";
                                echo "</div>";
                                echo "</div>";
                                echo "</div>"; // Close card-body
                                echo "</div>"; // Close card

                            foreach ($categories as $category => $permissions) {
                                echo "<div class='card mb-3'>";
                                echo "<div class='card-header fw-bold bg-dark'>" . strtoupper($category) . "</div>";
                                echo "<div class='card-body'>";

                      

                                foreach ($permissions as $row) {
                                    echo "<div class='d-flex justify-content-between align-items-center mb-2'>";
                                    echo "<span class='permission-name'>" . ucfirst($row['permission_name']) . "</span>";
                                    echo "<div class='form-check form-switch'>";
                                    echo "<input class='form-check-input permission-checkbox' type='checkbox' id='permission" . $row['permission_id'] . "' name='permissions[]' value='" . $row['permission_id'] . "'>";
                                    echo "<label class='form-check-label' for='permission" . $row['permission_id'] . "'>OFF / ON</label>";
                                    echo "</div>";
                                    echo "</div>";
                                }

                                echo "</div>"; // Close card-body
                                echo "</div>"; // Close card
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