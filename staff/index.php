<?php

include 'permission/permissionMonitorDashboard.php';

if (isset($_GET['visitor_code'])) {
    $_SESSION['randomCode'] = $_GET['visitor_code'];
}

// Include fetch-visitors.php before the header
include 'fetch-visitors.php';

include 'includes/header.php';
?>

<body class="bg-light">
    <div class="d-flex">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>
        <!-- Main Content -->
        <div class="flex-grow-1 p-4">
            <div class="container mt-4">
                <div class="row text-center">

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title fw-bold">Today's Visitors</h5>
                                <h4 class="card-text fw-normal"><?php echo $totalVisitorsToday; ?></h4>
                            </div>
                        </div>
                    </div>


                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title fw-bold">Current Visitors</h5>
                                <h4 class="card-text fw-normal" id="current-visitors">
                                    <?php echo $currentVisitors; ?>
                                </h4>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title fw-bold">Monthly Visitors</h5>
                                <h4 class="card-text fw-normal" id="current-visitors">
                                    <?php echo $totalMonthlyVisitor; ?>
                                </h4>
                            </div>
                        </div>
                    </div>

                </div>

                <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show mt-3" role="alert">
                    <?php echo $_SESSION['message'];
                    unset($_SESSION['message']);
                    unset($_SESSION['message_type']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>

                <div class="mt-4">
                    <h3>Visitor Records</h3>
                    <div class="d-flex justify-content-between mb-3">
                        <!-- Search Box -->
                        <div class="row">
                            <div class="col">
                                <input type="text" id="search-input" class="form-control" placeholder="Search by name"
                                    value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                            </div>

                            <div class="col-3">
                                <form method="POST" action="index.php">
                                    <input type="hidden" name="all" id="all">
                                    <button class="form-control" id="allBtn">All</button>
                                </form>
                            </div>
                            <div class="col-4">
                                <form method="GET" action="index.php">
                                    <input type="hidden" name="startDate" id="startDate" value="<?php echo $startDate; ?>">
                                    <input type="hidden" name="endDate" id="endDate" value="<?php echo $endDate; ?>">
                                    <input type="hidden" name="page" value="1">
                                    <button class="form-control" id="customRangeBtn">Custom range</button>
                                </form>
                            </div>
                        </div>

                        <div class="btn">
                            <a class="btn btn-secondary" id="export-code" href="process/export/export-code.php">GET CODES</a>
                        </div>
                    </div>

                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Name</th>
                                <th>Date</th>
                                <th>Time In</th>
                                <th>Time Out</th>
                                <th>Duration</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="visitor-table">
                            <?php
                            if (!empty($startDate) && !empty($endDate)) {
                                include 'fetch-range-logic.php';
                            } else {
                                if ($visitorsResult->num_rows > 0) {
                                    while ($row = $visitorsResult->fetch_assoc()):
                                        ?>
                                        <tr>
                                            <td>
                                                <?php echo strtoupper($row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name']); ?>
                                            </td>
                                            <td><?php echo isset($row['time_in']) ? date('Y-m-d', strtotime($row['time_in'])) : '-'; ?></td>
                                            <td><?php echo isset($row['time_in']) ? date('H:i:s', strtotime($row['time_in'])) : '-'; ?></td>
                                            <td><?php echo isset($row['time_out']) ? date('H:i:s', strtotime($row['time_out'])) : '-'; ?></td>
                                            <td>
                                                <?php
                                                if (isset($row['time_in'], $row['time_out'])) {
                                                    $timeIn = new DateTime($row['time_in']);
                                                    $timeOut = new DateTime($row['time_out']);
                                                    $interval = $timeIn->diff($timeOut);
                                                    echo $interval->format('%h hours %i minutes %s seconds');
                                                } else {
                                                    echo '-';
                                                }
                                                ?>
                                            </td>
                                            <td><?php echo isset($row['status']) ? $row['status'] : '-'; ?></td>
                                            <td>
                                                <button class="btn btn-success view-details"
                                                    data-name="<?php echo strtoupper($row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name']); ?>"
                                                    data-age="<?php echo $row['age']; ?>" data-sex="<?php echo $row['sex_name']; ?>"
                                                    data-code="<?php echo $row['code']; ?>"
                                                    data-purpose="<?php echo $row['purpose']; ?>" data-bs-toggle="modal"
                                                    data-bs-target="#visitorDetailsModal">
                                                    View Details
                                                </button>

                                                <form action="process/force-time-out-visitor.php" method="POST" style="display:inline;">
                                                    <input type="hidden" name="visitor_code" value="<?php echo $row['code']; ?>">
                                                    <button type="submit" class="btn btn-danger">Time Out</button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php
                                    endwhile;
                                } else {
                                    ?>
                                    <tr>
                                        <td colspan="7" class="text-center">End of the list reached.</td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- MODAL FOR VIEWING VISITOR DETAIL -->
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
                                <form action="process/export/print-receipt.php" method="GET">
                                    <input type="hidden" name="visitor_code" id="visitor_code">
                                    <button type="submit" class="btn btn-primary" id="print-button">Print</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <nav aria-label="Page navigation">
                        <ul class="pagination" id="pagination">
                            <?php
                            $pagesToShow = 3;
                            $startPage = max(1, $page - (($page - 1) % $pagesToShow));
                            $endPage = min($totalPages, $startPage + $pagesToShow - 1);

                            // Preserve custom range parameters
                            $queryParams = http_build_query([
                                'startDate' => $startDate ?? '',
                                'endDate' => $endDate ?? ''
                            ]);

                            // Display "Previous" button
                            if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page - 1; ?>&<?php echo $queryParams; ?>">Previous</a>
                                </li>
                            <?php endif; ?>

                            <!-- Display the range of pages dynamically -->
                            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>&<?php echo $queryParams; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>

                            <!-- Show ellipsis if there are more pages -->
                            <?php if ($endPage < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $endPage + 1; ?>&<?php echo $queryParams; ?>">...</a>
                                </li>
                            <?php endif; ?>

                            <!-- Display "Next" button -->
                            <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page + 1; ?>&<?php echo $queryParams; ?>">Next</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>

                    <p id="page-info">Page <?php echo $page; ?> of <?php echo $totalPages; ?></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>