<?php include 'fetch/fetch-visitors.php'; ?>

<!DOCTYPE html>
<html lang="en">
<?php include 'includes/header.php'; ?>
<body class="bg-light">
    <div class="d-flex">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-grow-1 p-4">
            <div class="container mt-4">
                <!-- Dashboard Overview -->
                <div class="row text-center">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Total Visitors Today</h5>
                                <p class="card-text" id="total-visitors"><?php echo $totalVisitorsToday ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">test test test</h5>
                                <p class="card-text" id="avg-time">0 mins</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Current Visitors</h5>
                                <p class="card-text" id="current-visitors"><?php echo $currentVisitors ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Visitor Records -->
                <div class="mt-4">
                    <h3>Visitor Records</h3>
                    <div class="d-flex justify-content-between mb-3">
                        <!-- Search Box -->
                        <input type="text" id="search-input" class="form-control w-25" placeholder="Search by name">
                    </div>
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Name</th>
                                <th>Date</th>
                                <th>Time In</th>
                                <th>Time Out</th>
                                <th>Duration</th>
                                <th>Codes</th>
                            </tr>
                        </thead>
                        <tbody id="visitor-table">
                            <?php while ($row = $visitorsResult->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo strtoupper($row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name']); ?></td>
                                    <?php
                                    $visitorId = $row['id'];
                                    $timeLogsQuery = "SELECT * FROM time_logs WHERE client_id = $visitorId ORDER BY time_in DESC LIMIT 1";
                                    $timeLogResult = $conn->query($timeLogsQuery);
                                    $timeLog = $timeLogResult->fetch_assoc();
                                    ?>
                                    <td><?php echo isset($timeLog['time_in']) ? date('Y-m-d', strtotime($timeLog['time_in'])) : 'N/A'; ?></td>
                                    <td><?php echo isset($timeLog['time_in']) ? date('H:i:s', strtotime($timeLog['time_in'])) : 'N/A'; ?></td>
                                    <td><?php echo isset($timeLog['time_out']) ? date('H:i:s', strtotime($timeLog['time_out'])) : 'N/A'; ?></td>
                                    <td>
                                        <?php
                                        if (isset($timeLog['time_in'], $timeLog['time_out'])) {
                                            $timeIn = new DateTime($timeLog['time_in']);
                                            $timeOut = new DateTime($timeLog['time_out']);
                                            $interval = $timeIn->diff($timeOut);
                                            echo $interval->format('%h hours %i minutes %s seconds');
                                        } else {
                                            echo 'N/A';
                                        }
                                        ?>
                                    </td>
                                        <td><?php echo $timeLog['code'] ?></td>
                    
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Reports -->
                <div class="mt-4">
                    <h3>Generate Reports</h3>
                    <a class="btn btn-success" id="export-csv" href="">Export CSV</a>
                    <a class="btn btn-primary" id="export-pdf" href="process/export/export-pdf.php">Export PDF</a>

                </div>
            </div>
        </div>
    </div>

</body>
</html>
