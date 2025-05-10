<?php
session_start();
require_once 'includes/header.php';
?>

<style>
    input::placeholder {
        color: grey !important;
    }
    .form-control:focus {
        box-shadow: none !important;
        border-color: #2e2c73 !important;
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
    <div 
        class="container-fluid d-flex justify-content-center align-items-center vh-100"
        style="background: url('assets/images/background.jpg') no-repeat center center/cover;"
    >
        <div 
            class="overlay position-absolute w-100 h-100"
            style="background-color:rgba(0, 0, 0, 0.7); z-index: 1;"
        ></div>
        <div class="row w-100 position-relative" style="z-index: 2;">
            <!-- Left Section -->
            <div class="col-md-5 d-none d-md-flex flex-column justify-content-center align-items-center text-white p-4 fade-in-down">
                <h1 class="display-4 fw-bold">Welcome Back!</h1>
                <p class="text-center fs-5 fst-italic">
                    Where Technology Meets Imagination.
                </p>
            </div>

            <!-- Right Section -->
            <div class="col-md-7 d-flex justify-content-center align-items-center">
                <div 
                    class="p-5 border-0 rounded-4 w-100 fade-in-down" 
                    style="max-width: 600px;"
                >
                    <div class="d-flex align-items-center justify-content-center gap-3 mb-4">
                        <a href="index">
                            <img src="assets/images/CH-logo.png" alt="Logo" style="max-height: 100px; width: auto;">  
                        </a>
                    </div>
                    <div id="general-error" class="text-danger text-center mb-3" style="display: none;">Please provide correct credentials.</div>
                    <form action="process/login.php" method="POST" class="needs-validation" novalidate>
                        <div class="input-group mb-4">
                            <span class="input-group-text bg-transparent border-0">
                                <i class="fa fa-user text-white"></i>
                            </span>
                            <input 
                                type="text" 
                                id="username" 
                                name="username" 
                                class="form-control form-control-lg bg-transparent border-0 border-bottom border-light pb-2 text-white" 
                                placeholder="Username" 
                                required
                            >
                            <div class="invalid-feedback fst-italic">Please enter a valid username.</div>
                        </div>
                        <div class="input-group mb-4">
                            <span class="input-group-text bg-transparent border-0">
                                <i class="fa fa-lock text-white"></i>
                            </span>
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                class="form-control form-control-lg bg-transparent border-0 border-bottom border-light pb-2 text-white" 
                                placeholder="Password" 
                                required
                            >
                            <div class="invalid-feedback fst-italic">Please enter your password.</div>
                        </div>
                        <button 
                            type="submit" 
                            class="btn btn-lg w-100"style="background-color: #2e2c73; color: white;"
                        >
                            Login
                        </button>
                        <p class="mt-4 text-center">
                            <span style="color: rgb(182, 178, 178);">Not Member? </span>
                            <a href="membership" class="text-decoration-none fw-bold" style="color:rgb(211, 211, 211);">Join our community today!</a>
                        </p>
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
        document.addEventListener('DOMContentLoaded', function () {
            const usernameInput = document.getElementById('username');
            const passwordInput = document.getElementById('password');
            const generalError = document.getElementById('general-error');
            const loaderOverlay = document.getElementById('loader-overlay');
            const loaderImage = document.getElementById('loader-image');
            const loaderBar = document.getElementById('loader-bar');

            usernameInput.addEventListener('input', function () {
                usernameInput.classList.remove('is-invalid');
                generalError.style.display = 'none';
            });

            passwordInput.addEventListener('input', function () {
                passwordInput.classList.remove('is-invalid');
                generalError.style.display = 'none';
            });

            document.querySelector('form').addEventListener('submit', async function (e) {
                e.preventDefault();

                loaderOverlay.style.display = 'block';
                loaderImage.style.display = 'block';
                loaderBar.style.display = 'block';

                setTimeout(async () => {
                    const form = e.target;
                    const formData = new FormData(form);

                    const response = await fetch(form.action, {
                        method: form.method,
                        body: formData
                    });

                    const result = await response.json();

                    loaderOverlay.style.display = 'none';
                    loaderImage.style.display = 'none';
                    loaderBar.style.display = 'none';

                    if (result.success) {
                        window.location.href = result.redirect;
                    } else {
                        generalError.style.display = 'block';
                        usernameInput.classList.add('is-invalid');
                        passwordInput.classList.add('is-invalid');
                    }
                }, 300); // Timeout of 2 seconds
            });
        });
    </script>
</body>
</html>