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
                        <label for="edit-role" class="form-label">Select a role <span class="text-danger fw-bold">*</span></label>
                        <select name="role" id="edit-role" class="form-control">
                            <option value="1">ADMIN</option>
                            <option value="2">STAFF</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit-username" class="form-label">Username <span class="text-danger fw-bold">*</span></label>
                        <input type="text" id="edit-username" name="username" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="edit-password" class="form-label">Password <span class="text-danger fw-bold">*</span></label>
                        <input type="password" id="edit-password" name="password" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-success w-100">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="pinVerificationModal" tabindex="-1" aria-labelledby="pinVerificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pinVerificationModalLabel">Enter Verification PIN <span class="text-danger fw-bold">*</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="pinVerificationForm">
                    <input type="hidden" id="pin-account-id">
                    <input type="hidden" id="pin-username">
                    <input type="hidden" id="pin-role">
                    <div class="mb-3">
                        <label for="verification-pin" class="form-label">PIN</label>
                        <input type="text" id="verification-pin" class="form-control" maxlength="6" required>
                    </div>
                    <button type="button" class="btn btn-primary w-100" onclick="verifyPin()">Verify</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    function verifyPin() {
        const pin = document.getElementById('verification-pin').value;

        fetch('process/verify-pin.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ pin: pin })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const editModal = new bootstrap.Modal(document.getElementById('editModal'));
                const accountId = document.getElementById('pin-account-id').value;
                document.getElementById('edit-id').value = accountId;
                document.getElementById('edit-username').value = document.getElementById('pin-username').value;
                document.getElementById('edit-role').value = document.getElementById('pin-role').value;
                editModal.show();
                const pinModal = bootstrap.Modal.getInstance(document.getElementById('pinVerificationModal'));
                pinModal.hide();
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        });
    }
</script>
