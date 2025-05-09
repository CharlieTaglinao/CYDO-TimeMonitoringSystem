

<?php 
include '../includes/database.php';  
?>
<body>
<div class="modal fade <?php if (isset($_SESSION['show_modal']) && $_SESSION['show_modal']) echo 'show'; ?>" 
     id="exampleModal" 
     tabindex="-1" 
     aria-labelledby="exampleModalLabel" 
     aria-hidden="true"
     style="<?php if (isset($_SESSION['show_modal']) && $_SESSION['show_modal']) echo 'display: block;'; ?>">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <!-- Form -->
                <form action="process/add-account-logic.php" method="POST">
                    <div class="mb-3">
                        <label for="role" class="form-label">Select a role <span class="text-danger fw-bold">*</span></label>
                        <?php
                        // Fetch roles from the database 
                        $query = "SELECT id, role FROM role";
                        $result = $conn->query($query);
                        ?>
                        <select name="role" id="role" class="form-control">
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars(strtoupper($row['role']))?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username <span class="text-danger fw-bold">*</span></label>
                        <input type="text" id="username" name="username" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger fw-bold">*</span></label>
                        <input type="text" id="email" name="email" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password <span class="text-danger fw-bold">*</span></label>
                        <input type="password" id="password" name="password" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-success w-100">ADD</button>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
