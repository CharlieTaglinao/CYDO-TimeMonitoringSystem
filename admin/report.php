    <?php include 'includes/header.php'; ?>

    <body class="bg-light">
        <div class="d-flex">
            <!-- Sidebar -->
            <?php include 'includes/sidebar.php'; ?>

            <!-- Main Content -->
            <div class="flex-grow-1 p-4">
                <div class="container mt-4">

                    <div id="modalContainer"></div>

                    <div class="mt-4">
                        <h3>Visitor Reports</h3>
                        <div class="mb-3">
                            <div class="container bg-dark text-center py-3 rounded">
                                <h4 class="text-light">XLSX FORMAT</h4>
                                <div class="container bg-light p-4 rounded">
                                    <div class="row g-4">
                                        <!-- Today's Report -->
                                        <div class="col-md-4">
                                            <div class="download-card bg-white border shadow-sm d-flex flex-column align-items-center justify-content-center p-3 rounded">
                                            <a class="btn btn-success w-100" href="process/export/AllReport/report-logic.php?type=today&format=xlsx">Today's Report (XLSX)</a>

                                            </div>
                                        </div>

                                        <!-- 1 Month Report -->
                                        <div class="col-md-4">
                                            <div class="download-card bg-white border shadow-sm d-flex flex-column align-items-center justify-content-center p-3 rounded">
                                            <a class="btn btn-success w-100" href="process/export/AllReport/report-logic.php?type=month&format=xlsx">1 Month Report (XLSX)</a>
                                            </div>
                                        </div>

                                        <!-- Custom Report -->
                                        <div class="col-md-4">
                                            <div class="download-card bg-white border shadow-sm d-flex flex-column align-items-center justify-content-center p-3 rounded">
                                                <a class="btn btn-success w-100" id="export-custom"
                                                    href="#">
                                                    Custom
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
                                        <div class="col-md-4">
                                            <div class="download-card bg-white border shadow-sm d-flex flex-column align-items-center justify-content-center p-3 rounded">
                                            <a class="btn btn-success w-100" href="process/export/AllReport/report-logic.php?type=today&format=pdf">Today's Report (PDF)</a>
                                            </div>
                                        </div>

                                        <!-- 1 Month Report -->
                                        <div class="col-md-4">
                                            <div class="download-card bg-white border shadow-sm d-flex flex-column align-items-center justify-content-center p-3 rounded">
                                            <a class="btn btn-success w-100" href="process/export/AllReport/report-logic.php?type=month&format=pdf">1 Month Report (PDF)</a>
                                            </div>
                                        </div>

                                        <!-- Custom Report -->
                                        <div class="col-md-4">
                                            <div class="download-card bg-white border shadow-sm d-flex flex-column align-items-center justify-content-center p-3 rounded">
                                                <a class="btn btn-success w-100" id="export-custom"
                                                    href="#">
                                                    Custom
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>

        <script src="assets/js/script.js"></script>
    </body>
