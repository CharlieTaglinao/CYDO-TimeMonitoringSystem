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
        <div class="header-container container-fluid w-100" style="position: relative; bottom: 80px;">
            <div class="cydo-logo">
                <div class="row">
                    <div class="col-md-12 d-flex align-items-center">
                        <img src="assets/images/CH-LOGO.png" class="LOGO-CYDO" alt="CYDO-LOGO">
                    </div>

                </div>          
            </div>
        </div>
        <div class="container text-center">
            <!-- Login button -->
            <div class="position-absolute top-0 end-0 p-3">
                <a href="login" class="btn btn-primary">
                    <i class="fa fa-user"></i>
                </a>
            </div>

            <!-- Centered title -->
            <div id="date-time-container" class="text-dark text-center">
                <h2 id="date" class="date"></h2>
                <h2 id="clock" class="clock"></h2>
            </div>

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
                    }, 4000);
                </script>
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


        <!-- Time In Modal -->
        <div class="modal fade" id="timeInModal" tabindex="-1" aria-labelledby="timeInModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="timeInModalLabel">Confirmation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="process/time-in-out.php" method="POST" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="isMember" class="form-label">Are you a member? <span class="asterisk-required-fields">*</span></label>
                                <select class="form-select" id="isMember" name="isMember" required>
                                    <option value="" disabled selected>Select</option>
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                                <div class="invalid-feedback">Please select membership status.</div>
                            </div>


                            <div id="membershipSection" style="display:none;">
                                <div class="mb-3">
                                    <label for="membership_code" class="form-label">Membership ID <span class="asterisk-required-fields">*</span></label>
                                    <input type="text" class="form-control" id="membership_code" name="membership_code" placeholder="CH-1234-ABC123">
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="purpose" class="form-label">Purpose of visit <span class="asterisk-required-fields">*</span></label>
                                    <input type="text" class="form-control" id="purpose" name="purpose" placeholder="Research">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>


                            <div id="personalDetailsSection" style="display:none;">
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
                                            <label class="form-label" for="school_name">School name <span class="asterisk-required-fields">*</span></label>
                                            <input type="text" name="schoolname" id="school_name" class="form-control" placeholder="Gov Ferrer Memorial Integrated National High School" required>
        
                                            <div class="invalid-feedback">Please provide a school name.</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="barangay">Barangay <span class="asterisk-required-fields">*</span></label>
                                            <select class="form-select" name="barangayname" id="barangay" required>
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
                        <form action="process/time-in-out.php" method="POST">
                            <div class="mb-3">
                                <label for="code" class="form-label">Code</label>
                                <input type="text" class="form-control" id="code" name="code" placeholder="Enter your code" required>
                            </div>
                            <button type="submit" class="btn btn-danger" name="timeOut" id="btnTimeOut">Time Out</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
 
        <!-- Temporary Code Modal -->
        <div class="modal fade" id="temporaryCodeModal" tabindex="-1" aria-labelledby="temporaryCodeModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="temporaryCodeModalLabel">Temporary Code</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                            <h4><em>Please don't forget to take a photo of your temporary code it will be used in the near future.</em></h4>
                        <h3 class="text-center" style="color: #2e2c73; letter-spacing: 3px;" id="temporaryCodeDisplay"></h3>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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

            // Auto log out scriptt
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

            document.addEventListener('DOMContentLoaded', () => {
                const isMemberSelect = document.getElementById('isMember');
                const personalSection = document.getElementById('personalDetailsSection');
                const membershipSection = document.getElementById('membershipSection');
                isMemberSelect.addEventListener('change', () => {
                    if (isMemberSelect.value === 'yes') {
                        membershipSection.style.display = 'block';
                        personalSection.style.display = 'none';
                        // Disable validation for personal details
                        personalSection.querySelectorAll('input, select').forEach(input => {
                            input.required = false;
                            input.disabled = true;
                        });
                        membershipSection.querySelectorAll('input').forEach(input => {
                            input.required = true;
                            input.disabled = false;
                        });
                    } else if (isMemberSelect.value === 'no') {
                        membershipSection.style.display = 'none';
                        personalSection.style.display = 'block';
                        // Enable validation for personal details
                        personalSection.querySelectorAll('input, select').forEach(input => {
                            input.required = true;
                            input.disabled = false;
                        });
                        membershipSection.querySelectorAll('input').forEach(input => {
                            input.required = false;
                            input.disabled = true;
                        });
                    }
                });

                <?php if (isset($_SESSION['temporary_code'])): ?>
                    const temporaryCode = "<?php echo $_SESSION['temporary_code']; ?>";
                    document.getElementById('temporaryCodeDisplay').textContent = temporaryCode;
                    const temporaryCodeModal = new bootstrap.Modal(document.getElementById('temporaryCodeModal'));
                    temporaryCodeModal.show();
                    <?php unset($_SESSION['temporary_code']); ?>
                <?php endif; ?>
            });
        </script>

        <footer>
           <div class="footer-text"><p class="text-dark LJF-text">Let's Join Forces <span class="text-success LJF-text2">For a More Progressive City of General Trias</span></p></div>
        </footer>

    </body>
</html>