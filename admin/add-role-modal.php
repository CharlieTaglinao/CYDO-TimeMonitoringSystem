
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
                <form action="process/add-role-logic.php" method="POST">
                    <div class="mb-3">
                        <label for="account_type_name" class="form-label">Account type name<span class="text-danger fw-bold">*</span></label>
                        <input type="text" name="account_type_name" id="account_type_name" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-success w-100">ADD</button>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
