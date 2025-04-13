<?php include 'includes/header.php';
     include 'permission/permissionDownloadReport.php'; 
?>

<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-grow-1 p-4">
            <div class="container mt-4">
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
                    <h2 class="mb-5">Visitor Reports</h2>

                    <!-- XLSX FORMAT Section -->
                    <div class="card mb-5 shadow-lg">
                        <div class="card-header text-white py-4" style="background-color:rgb(46, 109, 76);">
                            <h4 class="mb-0">Visitors Report</h4>
                        </div>
                        <div class="card-body py-5">
                        <h4 class="mb-4">XLSX</h4>
                            <div class="row g-4">
                                <!-- Today's Report -->
                                <div class="col-md-3">
                                    <a class="btn btn-secondary btn-lg w-100 py-4" href="process/export/AllReport/report-logic.php?type=today&format=xlsx">Today's Report</a>
                                </div>
                                <!-- 1 Month Report -->
                                <div class="col-md-3">
                                    <a class="btn btn-secondary btn-lg w-100 py-4" href="process/export/AllReport/report-logic.php?type=month&format=xlsx">1 Month Report</a>
                                </div>
                                <!-- Custom Report -->
                                <div class="col-md-3">
                                    <button class="btn btn-secondary btn-lg w-100 py-4" id="customRangeExportXLSXBtn">Custom</button>
                                </div>
                                <!-- By Office Report -->
                                <div class="col-md-3">
                                    <button class="btn btn-secondary btn-lg w-100 py-4" id="customOfficeExportXLSXBtn">By Office</button>
                                </div>
                            </div>

                            <h4 class="mt-5 mb-4">PDF</h4>

                            <div class="row g-4">
                                <!-- Today's Report -->
                                <div class="col-md-3">
                                    <a class="btn btn-secondary btn-lg w-100 py-4" href="process/export/AllReport/report-logic.php?type=today&format=pdf">Today's Report</a>
                                </div>
                                <!-- 1 Month Report -->
                                <div class="col-md-3">
                                    <a class="btn btn-secondary btn-lg w-100 py-4" href="process/export/AllReport/report-logic.php?type=month&format=pdf">1 Month Report</a>
                                </div>
                                <!-- Custom Report -->
                                <div class="col-md-3">
                                    <button class="btn btn-secondary btn-lg w-100 py-4" id="customRangeExportPDFBtn">Custom</button>
                                </div>
                                <!-- By Office Report -->
                                <div class="col-md-3">
                                    <button class="btn btn-secondary btn-lg w-100 py-4" id="customOfficeExportPDFBtn">By Office</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
