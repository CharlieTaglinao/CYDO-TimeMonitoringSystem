<?php include 'includes/header.php'; ?>
<?php include 'fetch-monitor-visitor.php'; ?>

<body class="bg-light">
    <div class="d-flex">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>
        <!-- Main Content -->
        <div class="flex-grow-1 p-4">
            <div class="container">
                <h3 class="font-weight-bold">Monitoring Visitor</h3>
                <div class="row text-center mt-4">
                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm border-light rounded-lg">
                            <div class="card-body">
                                <h5 class="card-title">In-site</h5>
                                <h4 class="card-text text-secondary"><?php echo $totalInsite; ?></h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm border-light rounded-lg">
                            <div class="card-body">
                                <h5 class="card-title">Already Out</h5>
                                <h4 class="card-text text-secondary"><?php echo $totalAlreadyOut; ?></h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm border-light rounded-lg">
                            <div class="card-body">
                                <h5 class="card-title">Overall Visitors</h5>
                                <h4 class="card-text text-secondary"><?php echo $totalVisitorsToday; ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="modalContainer"></div>

                <!-- MODAL FOR CUSTOM RANGE -->
                <div class="modal fade" id="visitorDetailsModal" tabindex="-1"
                    aria-labelledby="visitorDetailsModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="visitorDetailsModalLabel"> <span id="modal-name"></span>
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p><strong>Age:</strong> <span id="modal-age"></span></p>
                                <p><strong>Sex:</strong> <span id="modal-sex"></span></p>
                                <p><strong>Code:</strong> <span id="modal-code"></span></p>
                                <p><strong>Purpose:</strong> <span id="modal-purpose"></span></p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>





                <!-- Visitor List -->
                <div class="row mt-1">
                    <!-- In-Site Visitors -->
                    <div class="col-md-6">
                        <div class="card shadow-lg border-light rounded-lg">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="card-title">In-site Visitors</h5>
                                    <input class="form-control w-50" type="text" id="searchInsite"
                                        placeholder="Search..."
                                        value="<?php echo isset($_GET['searchInsite']) ? htmlspecialchars($_GET['searchInsite']) : ''; ?>">
                                </div>
                                <div class="list-group monitor-container p-2">
                                    <div id="insite-table">
                                        <?php while ($row = $insiteVisitorResult->fetch_assoc()): ?>

                                            <div class="card mb-2">
                                                <div
                                                    class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong><?php echo $row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name']; ?></strong><br>
                                                        <small class="text-muted" style="font-weight: 600;">Date:
                                                            <?php echo (new DateTime($row['time_in']))->format('F j, Y'); ?></small><br>
                                                        <small class="text-muted" style="font-weight: 600;">Time in:
                                                            <?php echo (new DateTime($row['time_in']))->format('g:i A'); ?></small><br>
                                                        <small class="text-muted" style="font-weight: 600;">Purpose:
                                                            <?php echo $row['purpose']; ?></small>
                                                    </div>
                                                    <span class="badge bg-success">IN SITE</span>
                                                </div>
                                            </div>

                                        <?php endwhile; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Outgoing Visitors -->
                    <div class="col-md-6">
                        <div class="card shadow-lg border-light rounded-lg">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="card-title">Outgoing Visitors</h5>
                                    <input class="form-control w-50" type="text" id="searchOutgoing"
                                        placeholder="Search..."
                                        value="<?php echo isset($_GET['searchOutgoing']) ? htmlspecialchars($_GET['searchOutgoing']) : ''; ?>">
                                </div>

                                <div class="list-group monitor-container p-2">
                                    <div id="outgoing-table">
                                        <?php while ($row = $alreadyOutResult->fetch_assoc()): ?>
                                            <div class="card mb-2">
                                                <div
                                                    class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>

                                                        <strong><?php echo $row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name']; ?></strong><br>
                                                        <small class="text-muted" style="font-weight: 600;">Date:
                                                            <?php echo (new DateTime($row['time_in']))->format('F j, Y'); ?></small><br>
                                                        <small class="text-muted" style="font-weight: 600;">Time in:
                                                            <?php echo (new DateTime($row['time_in']))->format('g:i A'); ?></small><br>
                                                        <small class="text-muted" style="font-weight: 600;">Time out:
                                                            <?php echo (new DateTime($row['time_out']))->format('g:i A'); ?></small><br>
                                                        <small class="text-muted" style="font-weight: 600;">Purpose:
                                                            <?php echo $row['purpose']; ?></small><br>
                                                        <small class="text-muted" style="font-weight: 600;">Duration:
                                                            <?php
                                                            $timeIn = new DateTime($row['time_in']);
                                                            $timeOut = new DateTime($row['time_out']);
                                                            $duration = $timeIn->diff($timeOut);
                                                            echo $duration->format('%h hour %i minutes %s seconds');
                                                            ?> </small>
                                                    </div>
                                                    <span class="badge bg-danger">ALREADY OUT</span>
                                                </div>
                                            </div>

                                        <?php endwhile; ?>
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

</html>