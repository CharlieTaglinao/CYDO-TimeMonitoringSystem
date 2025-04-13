<?php include 'includes/header.php';
    include 'permission/permissionDownloadReport.php'; 
?>
<body>
    <div class="d-flex">
       <!-- Sidebar -->
       <?php include 'includes/sidebar.php'; ?>

       <!-- Main Content -->
       <div class="flex-grow-1 p-4">
          <div class="container-fluid mt-4">
             <div id="modalContainer"></div>
             <?php if (isset($_SESSION['message'])): ?>
             <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index: 1050; width: 100%;" role="alert">
                <?php echo $_SESSION['message'];
                unset($_SESSION['message']);
                unset($_SESSION['message_type']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
             </div>
             <?php endif; ?>

             <div class="mt-4">
                <h3>Change Password</h3>
                <div class="container">
                    <div class="row justify-content-center">
                       <div class="col-md-6">
                          <form action="process/change-password-logic.php" method="POST" class="needs-validation" novalidate>
                             <div class="mb-3">
                                <input type="password" name="current_password" id="currentPassword" class="form-control" placeholder="Enter current password" required>
                                <div class="invalid-feedback" data-feedback="current_password"></div>
                             </div>
                             <div class="mb-3">
                                <input type="password" name="new_password" id="newPassword" class="form-control" placeholder="Enter new password" required>
                                <div class="invalid-feedback" data-feedback="new_password"></div>
                             </div>
                             <div class="mb-3">
                                <input type="password" name="confirm_password" id="confirmPassword" class="form-control" placeholder="Confirm new password" required>
                                <div class="invalid-feedback" data-feedback="confirm_password"></div>
                             </div>
                             <button type="submit" class="btn btn-success w-100">Change Password</button>
                          </form>
                       </div>
                    </div>
                </div>
             </div>

          </div>
       </div>
    </div>
    <script>
        // JavaScript for Bootstrap validation
        document.addEventListener("DOMContentLoaded", function () {
            const invalidFeedbackMessages = {
                current_password: "Please enter your current password.",
                new_password: "Please enter a new password.",
                confirm_password: "Please confirm your new password."
            };

            // Set invalid-feedback messages dynamically
            Object.keys(invalidFeedbackMessages).forEach(key => {
                const feedbackElement = document.querySelector(`[data-feedback="${key}"]`);
                if (feedbackElement) {
                    feedbackElement.textContent = invalidFeedbackMessages[key];
                }
            });

            const changePasswordForm = document.querySelector(".needs-validation");

            if (changePasswordForm) {
                const currentPassword = document.getElementById("currentPassword");
                const newPassword = document.getElementById("newPassword");
                const confirmPassword = document.getElementById("confirmPassword");

                const requirementsList = document.createElement("ul");
                requirementsList.classList.add("text-danger", "mt-2");
                newPassword.parentNode.appendChild(requirementsList);

                const requirements = [
                    { id: "length", text: "Password must be at least 8 characters long.", valid: false },
                    { id: "uppercase", text: "Password must include at least one uppercase letter.", valid: false },
                    { id: "lowercase", text: "Password must include at least one lowercase letter.", valid: false },
                    { id: "number", text: "Password must include at least one number.", valid: false },
                    { id: "special", text: "Password must include at least one special character (@$!%*?&).", valid: false }
                ];

                const updateRequirements = () => {
                    requirementsList.innerHTML = "";
                    requirements.forEach(req => {
                        if (!req.valid) {
                            const li = document.createElement("li");
                            li.textContent = req.text;
                            requirementsList.appendChild(li);
                        }
                    });
                };
                currentPassword.addEventListener("input", function () {
                    if (currentPassword.value.length > 0) {
                        currentPassword.classList.remove("is-invalid");
                        currentPassword.classList.add("is-valid");
                    } else {
                        currentPassword.classList.add("is-invalid");
                        currentPassword.classList.remove("is-valid");
                    }
                });
                
                // Validate new password step by step
                newPassword.addEventListener("input", function () {
                    if (newPassword.value === currentPassword.value) {
                        newPassword.classList.add("is-invalid");
                        newPassword.classList.remove("is-valid");
                        const feedback = newPassword.nextElementSibling;
                        if (feedback) {
                            feedback.textContent = "New password cannot be the same as the current password.";
                        }
                    } else {
                        const value = newPassword.value;

                        requirements.find(r => r.id === "length").valid = value.length >= 8;
                        requirements.find(r => r.id === "uppercase").valid = /[A-Z]/.test(value);
                        requirements.find(r => r.id === "lowercase").valid = /[a-z]/.test(value);
                        requirements.find(r => r.id === "number").valid = /\d/.test(value);
                        requirements.find(r => r.id === "special").valid = /[@$!%*?&]/.test(value);

                        updateRequirements();

                        if (requirements.every(req => req.valid)) {
                            newPassword.classList.remove("is-invalid");
                            newPassword.classList.add("is-valid");
                        } else {
                            newPassword.classList.add("is-invalid");
                            newPassword.classList.remove("is-valid");
                        }
                    }
                });

                // Validate confirm password on input
                confirmPassword.addEventListener("input", function () {
                    if (confirmPassword.value === newPassword.value) {
                        confirmPassword.classList.remove("is-invalid");
                        confirmPassword.classList.add("is-valid");
                    } else {
                        confirmPassword.classList.add("is-invalid");
                        confirmPassword.classList.remove("is-valid");
                    }
                });

                // Prevent form submission if validation fails
                changePasswordForm.addEventListener("submit", function (event) {
                    if (!changePasswordForm.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    changePasswordForm.classList.add("was-validated");
                });

                changePasswordForm.addEventListener("submit", function (event) {
                    event.preventDefault();

                    const invalidFields = document.querySelectorAll(".is-invalid");
                    const validFields = document.querySelectorAll(".is-valid");
                    const totalFields = document.querySelectorAll("input").length;

                    if (invalidFields.length > 0 || validFields.length !== totalFields) {
                        let messages = [];
                        invalidFields.forEach(field => {
                            const feedback = field.nextElementSibling;
                            if (feedback && feedback.classList.contains("invalid-feedback")) {
                                messages.push(feedback.textContent.trim());
                            }
                        });
                        Swal.fire({
                            icon: "error",
                            title: "Validation Errors",
                            text: "Please provide a valid credentials before submitting.",
                            html: messages.join("<br>"),
                        });
                    } else {
                        Swal.fire({
                            title: "Are you sure?",
                            text: "Do you want to proceed with changing your password?",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonText: "Yes",
                            cancelButtonText: "Cancel",
                        }).then((result) => {
                            if (result.isConfirmed) {
                                changePasswordForm.submit();
                            }
                        });
                    }
                });
            }
        });

        (function () {
            'use strict';
            const forms = document.querySelectorAll('.needs-validation');
            Array.from(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>
</body>
</html>
