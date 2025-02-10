<?php include 'includes/header.php'; ?>
<?php session_start(); ?>

<body class="bg-dark d-flex justify-content-center" style="height: 100vh;">
    <div class="container text-center">
        <div class="mb-4 mt-5">
            <img src="assets/images/CYDO-LOGO.png" alt="CYDO LOGO" style="max-width: 200px;">
        </div>
        <h1 style="font-size: 4rem;" class="card-title text-light mb-5">CITY YOUTH DEVELOPMENT OFFICE</h1>
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show mt-3" role="alert">
                <?php echo $_SESSION['message'];
                unset($_SESSION['message']);
                unset($_SESSION['message_type']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <div class="row mt-7">
            <div class="col-md-6 offset-md-3">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card text-center" onclick="showForm('timeInForm')">
                            <div class="card-body">
                                <h5 class="card-title">TIME IN</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card text-center" onclick="showForm('timeOutForm')">
                            <div class="card-body">
                                <h5 class="card-title">TIME OUT</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="timeInForm" class="card mt-3" style="display: none;">
                    <div class="card-body">
                        <button type="button" class="btn-close float-end" aria-label="Close" onclick="hideForm('timeInForm')"></button>
                        <h5 class="card-title text-center">Time In</h5>
                        <form action="process/time-in-out.php" method="POST">
                            <div class="mb-3">
                                <label for="firstName" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="firstName" name="firstName" placeholder="Ex. Juan">
                            </div>
                            <div class="mb-3">
                                <label for="middleName" class="form-label">Middle Name</label>
                                <input type="text" class="form-control" id="middleName" name="middleName" placeholder="Ex. Santos">
                            </div>
                            <div class="mb-3">
                                <label for="lastName" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="lastName" name="lastName" placeholder="Ex. Dela Cruz">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Ex. abcdefg@gmail.com">
                            </div>
                            <div class="mb-3">
                                <label for="purpose" class="form-label">Purpose</label>
                                <input type="text" class="form-control" id="purpose" name="purpose" placeholder="Ex. Personal Matters">
                            </div>
                            <div class="mb-3">
                                <label for="sex" class="form-label">Sex</label>
                                <select name="sex" class="form-control" id="sex">
                                    <option value="1">MALE</option>
                                    <option value="2">FEMALE</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-secondary" name="timeIn">Time In</button>
                        </form>
                    </div>
                </div>
                <div id="timeOutForm" class="card mt-3" style="display: none;">
                    <div class="card-body">
                        <button type="button" class="btn-close float-end" aria-label="Close" onclick="hideForm('timeOutForm')"></button>
                        <h5 class="card-title text-center">Time Out</h5>
                        <form action="process/time-in-out.php" method="POST">
                            <div class="mb-3">
                                <label for="code" class="form-label">Code</label>
                                <input type="text" class="form-control" id="code" name="code" placeholder="Enter your code">
                            </div>
                            <button type="submit" class="btn btn-danger" name="timeOut">Time Out</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include 'includes/footer.php'; ?>
</body>
</html>