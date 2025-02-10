<?php include 'includes/header.php'; ?>
<?php session_start(); ?>

<body class="bg-dark d-flex align-items-center justify-content-center" style="height: 100vh;">
    <div class="container">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['message'];
                unset($_SESSION['message']);
                unset($_SESSION['message_type']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <script>
                setTimeout(function () {
                    var alert = document.querySelector('.alert');
                    if (alert) {
                        var bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }
                }, 5000);
            </script>
        <?php endif; ?>
        <div class="row mt-5">
            
            <div class="col-md-6 offset-md-3">
            <h1 class="card-title text-light text-center mb-5">City Youth Development Office</h1>
                <div class="card">
                    <div class="card-body">
            
                        <form action="process/time-in-out.php" method="POST">
                            <div class="mb-3">
                                <label for="firstName" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="firstName" name="firstName"
                                    placeholder="Enter first name">
                            </div>
                            <div class="mb-3">
                                <label for="lastName" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="lastName" name="lastName"
                                    placeholder="Enter last name">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="Enter email">
                            </div>

                            <div class="mb-3">
                                <label for="purpose" class="form-label>">Purpose</label>
                                <input type="text" class="form-control" id="purpose" name="purpose"
                                    placeholder="Enter purpose">
                            </div>

                            <div class="mb-3">
                                <label for="sex" class="form-label>">Sex</label>
                                <input type="text" class="form-control" id="sex" name="sex"
                                    placeholder="Male">
                            </div>

                            <input type="hidden" name="logType" id="logType">
                            <button type="submit" class="btn btn-secondary"
                                onclick="document.getElementById('logType').value='IN'">Time In</button>
                            <button type="submit" class="btn btn-danger"
                                onclick="document.getElementById('logType').value='OUT'">Time Out</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>