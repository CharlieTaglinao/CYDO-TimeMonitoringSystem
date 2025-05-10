<?php
session_start();
require_once 'includes/header.php';
?>

<style>
    input::placeholder {
        color: grey !important;
    }

    .form-control:focus,.form-select:focus {
        box-shadow: none !important;
        border-color: #2e2c73 !important;
    }
select option {
  margin: 40px;
  background: rgba(0, 0, 0, 0.3);
  color: #fff;
  text-shadow: 0 1px 0 rgb(0, 0, 0);
}
    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-40px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .fade-in-down {
        animation: fadeInDown 0.5s ease-in-out;
    }
</style>

<body>
    <div class="container-fluid d-flex justify-content-center align-items-center vh-100"
        style="background: url('assets/images/background.jpg') no-repeat center center/cover;">
        <div class="overlay position-absolute w-100 h-100" style="background-color:rgba(0, 0, 0, 0.7); z-index: 1;">
        </div>
        <div class="row w-100 position-relative" style="z-index: 2;">
            <!-- Left Section -->
            <div
                class="col-md-5 d-none d-md-flex flex-column justify-content-center align-items-center text-white p-4 fade-in-down">
                <h1 class="display-4 fw-bold">Welcome Back!</h1>
                <p class="text-center fs-5 fst-italic">
                    Where Technology Meets Imagination.
                </p>
            </div>

            <!-- Right Section -->
            <div class="col-md-7 d-flex justify-content-center align-items-center">
                <div class="p-5 border-0 rounded-4 w-100 fade-in-down" style="max-width: 1000px;">
                    <div class="d-flex align-items-center justify-content-center gap-3 mb-4">
                        <a href="index">
                            <img src="assets/images/CH-logo.png" alt="Logo" style="max-height: 100px; width: auto;">
                        </a>
                    </div>
                    <div id="general-error" class="text-danger text-center mb-3" style="display: none;">Please provide
                        correct credentials.</div>

                    <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3 w-75" role="alert" data-bs-delay="5000">
                        <?php echo $_SESSION['message'];
                        unset($_SESSION['message']);
                        unset($_SESSION['message_type']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <script>
                        setTimeout(() => {
                            const alert = document.querySelector('.alert');
                            if (alert) {
                                alert.classList.remove('show');
                                alert.addEventListener('transitionend', () => alert.remove());
                            }
                        }, 6000);
                    </script>
                    <?php endif; ?>

                    <form action="process/membership.php" method="POST" class="needs-validation" novalidate>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="input-group mb-3">
                                    <span class="input-group-text bg-transparent border-0">
                                        <i class="fa fa-user text-white"></i>
                                    </span>
                                    <input type="text" id="first_name" name="first_name"
                                        class="form-control form-control-lg bg-transparent border-0 border-bottom border-light pb-2 text-white"
                                        placeholder="First Name" required>
                                    <div class="invalid-feedback fst-italic"></div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="input-group mb-3">
                                    <input type="text" id="last_name" name="last_name"
                                        class="form-control form-control-lg bg-transparent border-0 border-bottom border-light pb-2 text-white"
                                        placeholder="Last Name" required>
                                    <div class="invalid-feedback fst-italic"></div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="input-group mb-3">
                                    <input type="text" id="middle_name" name="middle_name"
                                        class="form-control form-control-lg bg-transparent border-0 border-bottom border-light pb-2 text-white"
                                        placeholder="Middle Name">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="input-group mb-3">
                                    <input type="text" id="age" name="age"
                                        class="form-control form-control-lg bg-transparent border-0 border-bottom border-light pb-2 text-white"
                                        placeholder="Age" required>
                                    <div class="invalid-feedback fst-italic"></div>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <span class="input-group-text bg-transparent border-0">
                                        <i class="fa fa-envelope text-white"></i>
                                    </span>
                                    <input type="email" id="email" name="email"
                                        class="form-control form-control-lg bg-transparent border-0 border-bottom border-light pb-2 text-white"
                                        placeholder="Email">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <span class="input-group-text bg-transparent border-0">
                                        <i class="fa fa-school text-white"></i>
                                    </span>
                                    <input type="text" id="school_name" name="school_name"
                                        class="form-control form-control-lg bg-transparent border-0 border-bottom border-light pb-2 text-white"
                                        placeholder="School Name" required>
                                    <div class="invalid-feedback fst-italic"></div>
                                </div>
                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <span class="input-group-text bg-transparent border-0">
                                        <i class="fa fa-city text-white"></i>
                                    </span>
                                    <select name="barangay" id="barangay"  class="form-select form-select-lg bg-transparent border-0 border-bottom border-light pb-2 text-white">
                                        <option value="" disabled selected>Select Barangay</option>
                                        <?php 
                                        include 'includes/database.php';
                                        $query = "SELECT * FROM barangays";
                                        $result = $conn->query($query);

                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<option value='" . $row['id'] . "'>" . $row['barangay_name'] . "</option>";
                                            }
                                        } else {
                                            echo "<option value=''>No barangays found</option>";
                                        }
                                        ?>
                                    </select>
                                    <div class="invalid-feedback fst-italic"></div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <span class="input-group-text bg-transparent border-0">
                                        <i class="fa fa-mars-and-venus text-white"></i>
                                    </span>
                                    <select id="sex" name="sex"
                                        class="form-select form-select-lg bg-transparent border-0 border-bottom border-light pb-2 text-white"
                                        required>
                                        <option value="" disabled selected>Select Sex</option>
                                        <option value="1">Male</option>
                                        <option value="2">Female</option>
                                    </select>
                                    <div class="invalid-feedback fst-italic"></div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-lg w-100" style="background-color: #2e2c73; color: white;">
                            Submit
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="background-overlay" id="loader-overlay" style="display: none;"></div>
    <div class="image-holder" id="loader-image" style="display: none;"></div>
    <div class="loader" id="loader-bar" style="display: none;"></div>

    <?php include 'includes/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('.needs-validation');
            const inputs = form.querySelectorAll('input, select');
            const invalidFeedbacks = Array.from(form.querySelectorAll('.invalid-feedback'));

            // Array to store validation messages
            const validationMessages = [
                'Please enter your first name.',
                'Please enter your last name.',
                '', // Middle name is optional
                'Please enter your age.',
                '', // Email is optional
                'Please enter your school name.',
                'Please enter your barangay.',
                'Please select your sex.'
            ];

            inputs.forEach((input, index) => {
                input.addEventListener('input', () => {
                    if (input.checkValidity()) {
                        input.classList.remove('is-invalid');
                        input.classList.add('is-valid');
                        invalidFeedbacks[index].textContent = '';
                    } else {
                        input.classList.remove('is-valid');
                        input.classList.add('is-invalid');
                        invalidFeedbacks[index].textContent = validationMessages[index];
                    }
                });
            });

            form.addEventListener('submit', (event) => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                    inputs.forEach((input, index) => {
                        if (!input.checkValidity()) {
                            input.classList.add('is-invalid');
                            invalidFeedbacks[index].textContent = validationMessages[index];
                        }
                    });
                }
            });
        });
    </script>
</body>

</html>