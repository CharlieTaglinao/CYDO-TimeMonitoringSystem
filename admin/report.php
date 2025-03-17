<?php include 'includes/header.php';
     include 'permission/permissionDownloadReport.php'; 
     
?>

    <body class="bg-light">
        <div class="d-flex">
            <!-- Sidebar -->
            <?php include 'includes/sidebar.php'; ?>

            <!-- Main Content -->
            <div class="flex-grow-1 p-4">
                <div class="container mt-4">


                <div id="modalContainer"></div>
                <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show mt-3" role="alert">
                    <?php echo $_SESSION['message'];
                    unset($_SESSION['message']);
                    unset($_SESSION['message_type']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
                    <div class="mt-4">
                        <h3>Visitor Reports</h3>
                        <div class="mb-3">
                            <div class="container bg-dark text-center py-3 rounded">
                                <h4 class="text-light">XLSX FORMAT</h4>
                                <div class="container bg-light p-4 rounded">
                                    <div class="row g-4">
                                        <!-- Today's Report -->
                                        <div class="col-md-3">
                                            <div class="download-card bg-white border shadow-sm d-flex flex-column align-items-center justify-content-center p-3 rounded">
                                            <a class="btn btn-success w-100" href="process/export/AllReport/report-logic.php?type=today&format=xlsx">Today's Report</a>

                                            </div>
                                        </div>

                                        <!-- 1 Month Report -->
                                        <div class="col-md-3">
                                            <div class="download-card bg-white border shadow-sm d-flex flex-column align-items-center justify-content-center p-3 rounded">
                                            <a class="btn btn-success w-100" href="process/export/AllReport/report-logic.php?type=month&format=xlsx">1 Month Report</a>
                                            </div>
                                        </div>

                                        <!-- Custom Report -->
                                        <div class="col-md-3">
                                            <div class="download-card bg-white border shadow-sm d-flex flex-column align-items-center justify-content-center p-3 rounded">
                                                <a class="btn btn-success w-100" id="customRangeExportXLSXBtn">
                                                    Custom    
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="download-card bg-white border shadow-sm d-flex flex-column align-items-center justify-content-center p-3 rounded">
                                                <a class="btn btn-success w-100" id="customOfficeExportXLSXBtn">
                                                    By Office    
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="container bg-dark text-center py-3 rounded">
                                <h4 class="text-light">PDF FORMAT</h4>
                                <div class="container bg-light p-4 rounded">
                                    <div class="row g-4">
                                        <!-- Today's Report -->
                                        <div class="col-md-3">
                                            <div class="download-card bg-white border shadow-sm d-flex flex-column align-items-center justify-content-center p-3 rounded">
                                            <a class="btn btn-success w-100" href="process/export/AllReport/report-logic.php?type=today&format=pdf">Today's Report (PDF)</a>
                                            </div>
                                        </div>

                                        <!-- 1 Month Report -->
                                        <div class="col-md-3">
                                            <div class="download-card bg-white border shadow-sm d-flex flex-column align-items-center justify-content-center p-3 rounded">
                                            <a class="btn btn-success w-100" href="process/export/AllReport/report-logic.php?type=month&format=pdf">1 Month Report (PDF)</a>
                                            </div>
                                        </div>

                                        <!-- Custom Report -->
                                        <div class="col-md-3">
                                            <div class="download-card bg-white border shadow-sm d-flex flex-column align-items-center justify-content-center p-3 rounded">
                                                <a class="btn btn-success w-100" id="customRangeExportPDFBtn">
                                                    Custom    
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="download-card bg-white border shadow-sm d-flex flex-column align-items-center justify-content-center p-3 rounded">
                                                <a class="btn btn-success w-100" id="customOfficeExportPDFBtn">
                                                    By Office    
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        

                    </div>

<!-- 
                    <div class="mt-4">
                        <h3>User Reports</h3>
                        <div class="mb-3">
                            <div class="container bg-dark text-center py-3 rounded">
                                <h4 class="text-light">PDF FORMAT</h4>
                                <div class="container bg-light p-4 rounded">
                                    <div class="row g-4">
                                        Today's Report
                                        <div class="col-md-6">
                                            <div class="download-card bg-white border shadow-sm d-flex flex-column align-items-center justify-content-center p-3 rounded">
                                            <a class="btn btn-success w-100" href="process/export/AllReport/report-logic.php?type=all&format=xlsx">Download All</a>

                                            </div>
                                        </div>

                                        1 Month Report
                                        <div class="col-md-6">
                                            <div class="download-card bg-white border shadow-sm d-flex flex-column align-items-center justify-content-center p-3 rounded">
                                            <a class="btn btn-success w-100" href="process/export/AllReport/report-logic.php?type=all&format=xlsx">By role</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>  
                    </div> -->

                </div>
            </div>
        </div>
    </body>
</html>
