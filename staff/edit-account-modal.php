<body>
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="process/edit-account-logic.php" method="POST" id="editAccountForm">
                        <input type="hidden" id="edit-id" name="id">
                        <div class="mb-3">
                            <label for="edit-role" class="form-label">Select a role</label>
                            <select name="role" id="edit-role" class="form-control">
                                <option value="1">ADMIN</option>
                                <option value="2">STAFF</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit-username" class="form-label">Username</label>
                            <input type="text" id="edit-username" name="username" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="edit-password" class="form-label">Password</label>
                            <input type="password" id="edit-password" name="password" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-success w-100">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
