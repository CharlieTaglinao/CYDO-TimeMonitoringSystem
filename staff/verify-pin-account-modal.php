<!-- Verification Pin -->
<div class="modal fade <?php if (isset($_SESSION['show_modal']) && $_SESSION['show_modal']) echo 'show'; ?>" 
     id="exampleModal" 
     tabindex="-1" 
     aria-labelledby="exampleModalLabel" 
     aria-hidden="true"
     style="<?php if (isset($_SESSION['show_modal']) && $_SESSION['show_modal']) echo 'display: block;'; ?>">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Verify Pin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Form to verify pin -->
                <form action="process/edit-account-logic.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo $_SESSION['user_id']; ?>">
                    <div class="mb-3">
                        <label for="verificationpin" class="form-label">Enter Pin</label>
                        <input type="text" id="verificationpin" name="verificationpin" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <p class="text-muted">The PIN will expire in 5 minutes.</p>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Verify and Update</button>
                </form>
            </div>
        </div>
    </div>
</div>
