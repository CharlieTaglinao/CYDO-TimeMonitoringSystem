<?php session_start(); ?>
<?php 
include 'includes/header.php';
// include 'includes/loader.php'; 
if (isset($_GET['visitor_code'])) {
    $_SESSION['randomCode'] = $_GET['visitor_code'];
}
?>

<body class="d-flex flex-column">
    <div class="w-100">
        <!-- Add the beep audio element -->
        <audio id="beep" src="assets/audio/beep.mp3"></audio>
        <div class="header-container container-fluid w-100 " style="position: relative; bottom: 120px;">
            <div class="cydo-logo">
                <div class="row">
                    <div class="col-md-3 d-flex align-items-center">
                        <img src="assets/images/CYDO-LOGO.png" class="LOGO-CYDO" alt="CYDO-LOGO">
                    </div>
                    <div class="col-md-9 d-flex flex-column justify-content-center">
                        <h1 class="card-title text-light TEXT-CYDO">CITY YOUTH DEVELOPMENT OFFICE</h1>
                        <div class="header-bar d-flex align-items-center">
                            <div class="right-decor">
                                <div class="slice"></div>
                                <div class="slice"></div>
                                <div class="slice"></div>
                                <div class="slice"></div>
                            </div>
                        </div>
                    </div>
                </div>          
            </div>
        </div>
        <div class="container text-center">
            <!-- Login button -->
            <div class="position-absolute top-0 end-0 p-3">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#loginModal">
                    <i class="fa fa-user"></i>
                </button>
            </div>

            <!-- Centered title -->
            <div id="date-time-container" class="text-dark text-center">
                <h2 id="date" class="date"></h2>
                <h2 id="clock" class="clock"></h2>
            </div>

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
                            <div class="time-in-card card text-center" id="time-in-button-card" data-bs-toggle="modal" data-bs-target="#timeInModal">
                                <div class="card-body">
                                    <h5 class="card-title">TIME IN</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="time-out-card card text-center" id="time-out-button-card" data-bs-toggle="modal" data-bs-target="#timeOutModal">
                                <div class="card-body">
                                    <h5 class="card-title">TIME OUT</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- QR Code Modal -->
        <div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" id="">
                    <div class="modal-header">
                        <h5 class="modal-title" id="qrModalLabel">
                            <?php echo $_SESSION['first_name'] . ' ' . $_SESSION['middle_name'] . ' ' . $_SESSION['last_name']; ?>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="includes/generate-qr-code.php" alt="QR Code">
                        <p class="mt-3">Please take a capture for your QR CODE <br> or use this code for time out.</p>
                        <h4><?php echo $_SESSION['randomCode'] ?></h4>
                    </div>
                    <div class="modal-footer">
                        <form action="admin/process/export/print-receipt.php" method="GET">
                            <input type="hidden" name="visitor_code" id="visitor_code" value="<?php echo $_SESSION['randomCode']; ?>">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Print</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Time In Modal -->
        <div class="modal fade" id="timeInModal" tabindex="-1" aria-labelledby="timeInModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="timeInModalLabel">Personal Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="process/time-in-out.php" method="POST" class="needs-validation" novalidate>
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="firstName" class="form-label">First Name <span class="asterisk-required-fields">*</span></label>
                                        <input type="text" class="form-control" id="firstName" name="firstName"
                                            placeholder="Ex. Juan" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="middleName" class="form-label">Middle Name</label>
                                        <input type="text" class="form-control" id="middleName" name="middleName"
                                            placeholder="Ex. Santos">
                                            <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="lastName" class="form-label">Last Name <span class="asterisk-required-fields">*</span></label>
                                        <input type="text" class="form-control" id="lastName" name="lastName"
                                            placeholder="Ex. Dela Cruz" required>
    
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            placeholder="Ex. abcdefg@gmail.com" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="office">Office <span class="asterisk-required-fields">*</span></label>
                                        <select class="form-select" name="office" id="office" required>
                                            <option value="" selected disabled>Select an Office</option>
                                            <option value="1">City Youth Development Office</option>
                                            <option value="2">Person with Disablity Affairs Office</option>
                                        </select>
    
                                        <div class="invalid-feedback">Please select an office.</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="barangay">Barangay <span class="asterisk-required-fields">*</span></label>
                                        <select class="form-select" name="barangay" id="barangay" required>
                                            <option value="" selected disabled>Select a Barangay</option>
                                            <option value="1">Alingaro</option>
                                            <option value="2">Arnaldo</option>
                                            <option value="3">Bacao I</option>
                                            <option value="4">Bacao II</option>
                                            <option value="5">Bagumbayan</option>
                                            <option value="6">Biclatan</option>
                                            <option value="7">Buenavista I</option>
                                            <option value="8">Buenavista II</option>
                                            <option value="9">Buenavista III</option>
                                            <option value="10">Corregidor</option>
                                            <option value="11">Dulong Bayan</option>
                                            <option value="12">Governor Ferrer</option>
                                            <option value="13">Javalera</option>
                                            <option value="14">Manggahan</option>
                                            <option value="15">Navarro</option>
                                            <option value="16">Panungyanan</option>
                                            <option value="17">Pasong Camachile I</option>
                                            <option value="18">Pasong Camachile II</option>
                                            <option value="19">Pasong Kawayan I</option>
                                            <option value="20">Pasong Kawayan II</option>
                                            <option value="21">Pinagtipunan</option>
                                            <option value="22">Prinza</option>
                                            <option value="23">Sampalucan</option>
                                            <option value="24">Santiago</option>
                                            <option value="25">San Francisco</option>
                                            <option value="26">San Gabriel</option>
                                            <option value="27">San Juan I</option>
                                            <option value="28">San Juan II</option>
                                            <option value="29">Santa Clara</option>
                                            <option value="30">Tapia</option>
                                            <option value="31">Tejero</option>
                                            <option value="32">Vibora</option>
                                            <option value="33">1896</option>
                                        </select>
    
                                        <div class="invalid-feedback">Please select a barangay.</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="purpose" class="form-label">Purpose <span class="asterisk-required-fields">*</span></label>
                                        <input type="text" class="form-control" id="purpose" name="purpose"
                                            placeholder="Ex. Personal Matters" required>
    
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="age" class="form-label">Age <span class="asterisk-required-fields">*</span></label>
                                        <input type="text" class="form-control" id="age" name="age" placeholder="Ex. 21" required>
    
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <div class="mb-3">
                                        <label for="sex" class="form-label">Sex <span class="asterisk-required-fields">*</span></label>
                                        <select name="sex" class="form-select" id="sex" required>
                                            <option value="" selected disabled>Select </option>
                                            <option value="1">MALE</option>
                                            <option value="2">FEMALE</option>
                                        </select>
    
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary" name="timeIn">TIME IN</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Time Out Modal -->
        <div class="modal fade" id="timeOutModal" tabindex="-1" aria-labelledby="timeOutModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="timeOutModalLabel">Time Out</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <h4>Place your QR Code on camera to time out</h4>
                        <video id="preview" width="100%"></video>
                        <form action="process/time-in-out.php" method="POST">
                            <div class="mb-3">
                                <label for="code" class="form-label">Code</label>
                                <input type="text" class="form-control" id="code" name="code" placeholder="Enter your code">
                            </div>
                            <button type="submit" class="btn btn-danger" name="timeOut" id="btnTimeOut">Time Out</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
 
        <!-- Login Modal -->
        <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="loginModalLabel"><i class="fa fa-user"></i> Login</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="process/login.php" method="POST" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                                <div class="invalid-feedback">Please provide a username.</div>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <div class="invalid-feedback">Please provide a password.</div>
                            </div>
                            <button type="submit" class="btn btn-primary">Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php include 'includes/footer.php'; ?>

        <script>
            <?php if (isset($_SESSION['showQRModal'])): ?>
                var qrModal = new bootstrap.Modal(document.getElementById('qrModal'));
                qrModal.show();
                <?php unset($_SESSION['showQRModal']); ?>
            <?php endif; ?>

            // Auto log out script
            function checkAutoLogout() {
                var now = new Date();
                if (now.getHours() === 23 && now.getMinutes() === 59) {
                    fetch('process/auto-log-out.php')
                        .then(response => response.text())
                        .then(data => {
                            console.log(data);
                            location.reload();
                        });
                }
            }

            setInterval(checkAutoLogout, 60000); // Check every minute

            document.getElementById('firstName').addEventListener('input', function() {
                var firstName = this.value;
                if (firstName.length > 2) {
                    fetch('fetch_visitor_data.php?first_name=' + firstName)
                        .then(response => response.json())
                        .then(data => {
                            if (data) {
                                document.getElementById('middleName').value = data.middle_name;
                                document.getElementById('lastName').value = data.last_name;
                                document.getElementById('email').value = data.email;
                                document.getElementById('age').value = data.age;
                                document.getElementById('sex').value = data.sex_id;
                            }
                        });
                }
            });
        </script>

        <footer>
           <div class="footer-text"><p class="text-dark LJF-text">Let's Join Forces <span class="text-success LJF-text2">For a More Progressive City of General Trias</span></p></div>  
        </footer>

    </body>
</html>