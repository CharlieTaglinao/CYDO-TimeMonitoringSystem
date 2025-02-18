<?php include 'includes/header.php'; ?>
<?php include 'fetch-monitor-visitor.php'; ?>

<body class="bg-light">
    <div class="d-flex">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>
        <!-- Main Content -->
        <div class="flex-grow-1 p-4">
            <div class="container mt-4">
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
                                <h5 class="card-title">Already out</h5>
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

                <!-- Visitor List -->
                <div class="flex-grow-1">
                    <div class="container mt-3">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card shadow-lg border-light rounded-lg">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <h5 class="card-title">In-site Visitors</h5>
                                            <input class="form-control w-50" type="text" name="searchOutgoing"
                                                id="searchOutgoing">
                                        </div>
                                        <div class="list-group monitor-container p-2">
                                            <?php while ($row = $insiteVisitorResult->fetch_assoc()): ?>
                                                <div class="card mb-2">

                                                    <div
                                                        class="list-group-item d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <strong><?php echo $row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name'] ?></strong><br>
                                                            <small class="text-muted" style="font-weight: 600;">Date:
                                                                <?php echo (new DateTime($row['time_in']))->format('F j, Y'); ?></small><br>
                                                            <small class="text-muted" style="font-weight: 600;">Time in:
                                                                <?php echo (new DateTime($row['time_in']))->format('g:i A'); ?></small><br>


                                                            <small class="text-muted" style="font-weight: 600;">Purpose:
                                                                <?php echo $row['purpose'] ?></small>
                                                        </div>
                                                        <span class="badge bg-success">IN SITE</span>
                                                    </div>
                                                </div>

                                            <?php endwhile; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card shadow-lg border-light rounded-lg">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <h5 class="card-title">Outgoing Visitors</h5>
                                            <input class="form-control w-50" type="text" name="searchOutgoing"
                                                id="searchOutgoing">
                                        </div>

                                        <div class="list-group monitor-container p-2">
                                            <!-- Already out visitors -->
                                            <?php while ($row = $alreadyOutResult->fetch_assoc()): ?>
                                                <div class="card mb-2">
                                                    <div
                                                        class="list-group-item d-flex justify-content-between align-items-center">
                                                        <div class="col-md-6">
                                                            <div>
                                                                <strong><?php echo $row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name'] ?></strong><br>
                                                                <small class="text-muted" style="font-weight: 600;">Date:
                                                                    <?php echo (new DateTime($row['time_in']))->format('F j, Y'); ?></small><br>
                                                                <small class="text-muted" style="font-weight: 600;">Time in:
                                                                    <?php echo (new DateTime($row['time_in']))->format('g:i A'); ?></small><br>
                                                                <small class="text-muted" style="font-weight: 600;">Time
                                                                    out:
                                                                    <?php echo (new DateTime($row['time_out']))->format('g:i A'); ?></small><br>
                                                                <small class="text-muted" style="font-weight: 600;">Purpose:
                                                                    <?php echo $row['purpose']; ?></small>

                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 flex-grow-1">
                                                            <small class="text-muted" style="font-weight: 600;"> <?php
                                                            if (isset($row['time_in'], $row['time_out'])) {
                                                                $timeIn = new DateTime($row['time_in']);
                                                                $timeOut = new DateTime($row['time_out']);
                                                                $interval = $timeIn->diff($timeOut);
                                                                echo $interval->format('%h hours %i minutes %s seconds');
                                                            } else {
                                                                echo '-';
                                                            }
                                                            ?></small>
                                                            <div class="d-flex justify-content-end p-3">
                                                                <div class="badge bg-danger">
                                                                    ALREADY OUT
                                                                </div>
                                                            </div>
                                                        </div>
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
    </div>
    </div>

    <script src="assets/js/script.js"></script>
</body>

</html>