<?php
include 'includes/header.php';
include 'fetch-monitor-visitor.php';
include 'permission/permissionMonitorVisitor.php'; ?>


<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>
        <!-- Main Content -->
        <div class="flex-grow-1 p-4">
            <div class="container-fluid fade-in-down">

                <div class="row text-center mt-4">
                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm border-light rounded-lg">
                            <div class="card-body">
                                <h5 class="card-title fw-bold">In-site</h5>
                                <h4 class="card-text fw-normal"><?php echo $totalInsite; ?></h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm border-light rounded-lg">
                            <div class="card-body">
                                <h5 class="card-title fw-bold">Already Out</h5>
                                <h4 class="card-text fw-normal"><?php echo $totalAlreadyOut; ?></h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm border-light rounded-lg">
                            <div class="card-body">
                                <h5 class="card-title fw-bold">Overall Visitors</h5>
                                <h4 class="card-text fw-normal"><?php echo $totalVisitorsToday; ?></h4>
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



                <h3 class="font-weight-bold">Monitoring Visitor</h3>

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
                                            <div class="card mb-2 border-0 shadow-lg">
                                                <div
                                                    class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-0">
                                                    <div>
                                                        <div class="d-flex align-items-center mb-1">
                                                            <h5 class="mb-0 fw-bold">
                                                                <?php echo htmlspecialchars($row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name']); ?>
                                                            </h5>
                                                        </div>
                                                        <div class="mb-1">
                                                            <small class="text-muted fw-semibold me-2">Date:</small>
                                                            <small><?php echo (new DateTime($row['time_in']))->format('F j, Y'); ?></small>
                                                        </div>
                                                        <div class="mb-1">
                                                            <small class="text-muted fw-semibold me-2">Time in:</small>
                                                            <small><?php echo (new DateTime($row['time_in']))->format('g:i A'); ?></small>
                                                        </div>
                                                        <div>
                                                            <small class="text-muted fw-semibold me-2">Purpose:</small>
                                                            <small><?php echo htmlspecialchars($row['purpose']); ?></small>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex flex-column align-items-end">
                                                        <?php if (!empty($row['type'])): ?>
                                                            <span
                                                                class="badge fs-6 mb-2 px-3 <?php echo (strtolower($row['type']) !== 'guest') ? '' : 'bg-secondary'; ?>"
                                                                style="<?php echo (strtolower($row['type']) !== 'guest') ? 'background-color: #2e2c73;' : ''; ?>">
                                                                <?php echo htmlspecialchars($row['type']); ?>
                                                            </span>
                                                        <?php endif; ?>
                                                        <span class="badge bg-success fs-6 px-3">IN SITE</span>
                                                    </div>
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
                        <div class="card shadow-lg border-light rounded-lg w-100">
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
                                            <div class="card mb-2 border-0 shadow-lg w-100">
                                                <div
                                                    class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-0">
                                                    <div>
                                                        <div class="d-flex align-items-center mb-1">
                                                            <h5 class="mb-0 fw-bold">
                                                                <?php echo htmlspecialchars($row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name']); ?>
                                                            </h5>
                                                        </div>
                                                        <div class="mb-1">
                                                            <small class="text-muted fw-semibold me-2">Date:</small>
                                                            <small><?php echo (new DateTime($row['time_in']))->format('F j, Y'); ?></small>
                                                        </div>
                                                        <div class="mb-1">
                                                            <small class="text-muted fw-semibold me-2">Time in:</small>
                                                            <small><?php echo (new DateTime($row['time_in']))->format('g:i A'); ?></small>
                                                        </div>
                                                        <div class="mb-1">
                                                            <small class="text-muted fw-semibold me-2">Time out:</small>
                                                            <small><?php echo (new DateTime($row['time_out']))->format('g:i A'); ?></small>
                                                        </div>
                                                        <div class="mb-1">
                                                            <small class="text-muted fw-semibold me-2">Purpose:</small>
                                                            <small><?php echo htmlspecialchars($row['purpose']); ?></small>
                                                        </div>
                                                        <div>
                                                            <small class="text-muted fw-semibold me-2">Duration:</small>
                                                            <small>
                                                                <?php
                                                                $timeIn = new DateTime($row['time_in']);
                                                                $timeOut = new DateTime($row['time_out']);
                                                                $duration = $timeIn->diff($timeOut);
                                                                echo $duration->format('%h hour %i minutes %s seconds');
                                                                ?>
                                                            </small>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex flex-column align-items-end">
                                                        <?php if (!empty($row['type'])): ?>
                                                            <span
                                                                class="badge fs-6 mb-2 px-3 <?php echo (strtolower($row['type']) !== 'guest') ? '' : 'bg-secondary'; ?>"
                                                                style="<?php echo (strtolower($row['type']) !== 'guest') ? 'background-color: #2e2c73;' : ''; ?>">
                                                                <?php echo htmlspecialchars($row['type']); ?>
                                                            </span>
                                                        <?php endif; ?>
                                                        <span class="badge bg-danger fs-6 px-3 py-2">ALREADY OUT</span>
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
</body>

</html>