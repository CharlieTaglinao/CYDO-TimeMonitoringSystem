

<?php


include 'fetch-visitors.php'; ?>


<?php include 'includes/header.php'; ?>

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
                                <h5 class="card-title">Today's Visitors</h5>
                                <p class="card-text"><?php echo $totalVisitorsToday; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Current Visitors</h5>
                                <p class="card-text" id="current-visitors"><?php echo $currentVisitors; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Weekly Visitors</h5>
                                <p class="card-text" id="current-visitors"><?php echo $totalWeeklyVisitor; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div id="modalContainer"></div>

                <div class="mt-4">
                    <h3>Visitor Records</h3>
                    <div class="d-flex justify-content-between mb-3">
                        <!-- Search Box -->
                        <input type="text" id="search-input" class="form-control w-25" placeholder="Search by name"
                            value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">


                        <div class="flex-end">
                            <a class="btn btn-success" id="export-csv" href="process/export/export-csv.php">
                                CSV</a>
                            <a class="btn btn-primary" id="export-pdf" href="process/export/export-pdf.php">
                                PDF</a>
                        </div>


                    </div>
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Name</th>
                                <th>Date</th>
                                <th>Time In</th>
                                <th>Time Out</th>
                                <th>Age</th>
                                <th>Sex</th>
                                <th>Duration</th>
                                <th>Code</th>
                            </tr>
                        </thead>
                        <tbody id="visitor-table">
                            <?php while ($row = $visitorsResult->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo strtoupper($row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name']); ?>
                                    </td>
                                    <td><?php echo isset($row['time_in']) ? date('Y-m-d', strtotime($row['time_in'])) : '-'; ?>
                                    </td>
                                    <td><?php echo isset($row['time_in']) ? date('H:i:s', strtotime($row['time_in'])) : '-'; ?>
                                    </td>
                                    <td><?php echo isset($row['time_out']) ? date('H:i:s', strtotime($row['time_out'])) : '-'; ?>
                                    </td>
                                    <td><?php echo isset($row['age']) ? $row['age'] : '-'; ?></td>
                                    <td><?php echo $row['sex_name']?></td>
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
                                    <td><?php echo $row['code']; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <nav aria-label="Page navigation">
                        <ul class="pagination">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a>
                                </li>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    <p>Page <?php echo $page; ?> of <?php echo $totalPages; ?></p>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
</body>

</html>