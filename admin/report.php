<?php include 'includes/header.php';
     include 'permission/permissionDownloadReport.php'; 
?>

<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-grow-1 p-4">
            <div class="container-fluid mt-4 fade-in-down">
                <div id="modalContainer"></div>
                <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show mt-3 position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index: 1050; width: 100%;" role="alert">
                    <?php echo $_SESSION['message'];
                    unset($_SESSION['message']);
                    unset($_SESSION['message_type']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>

                <div class="mt-4">
                    <h2 class="mb-5">REPORTS</h2>

                    <!-- XLSX FORMAT Section -->
                    <div class="card mb-5 shadow-lg">
                        <div class="card-header text-white py-4" style="background-color:#2e2c73;">
                            <h4 class="mb-0">TIME LOGS</h4>
                        </div>
                        <div class="card-body py-5">
                        <h4 class="mb-4">XLSX</h4>
                            <div class="row g-4">
                                <!-- Today's Report -->
                                <div class="col-md-4">
                                    <a class="btn btn-secondary btn-lg w-100 py-4" id="export-today-button-xlsx" data-type="today" data-format="xlsx">Today's Report</a>
                                </div>
                                <!-- 1 Month Report -->
                                <div class="col-md-4">
                                    <a class="btn btn-secondary btn-lg w-100 py-4" id="export-todays-month-button-xlsx" data-type="month" data-format="xlsx">1 Month Report</a>
                                </div>
                                <!-- Custom Report -->
                                <div class="col-md-4">
                                    <button class="btn btn-secondary btn-lg w-100 py-4" id="customRangeExportXLSXBtn">Custom</button>
                                </div>
                            </div>

                            <h4 class="mt-5 mb-4">PDF</h4>

                            <div class="row g-4">
                                <!-- Today's Report -->
                                <div class="col-md-4">
                                    <a class="btn btn-secondary btn-lg w-100 py-4" id="export-today-button-pdf" data-type="today" data-format="pdf">Today's Report</a>
                                </div>
                                <!-- 1 Month Report -->
                                <div class="col-md-4">
                                    <a class="btn btn-secondary btn-lg w-100 py-4" id="export-todays-month-button-pdf" data-type="month" data-format="pdf">1 Month Report</a>
                                </div>
                                <!-- Custom Report -->
                                <div class="col-md-4">
                                    <button class="btn btn-secondary btn-lg w-100 py-4" id="customRangeExportPDFBtn">Custom</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/export-report.js"></script>
</body>
</html>
