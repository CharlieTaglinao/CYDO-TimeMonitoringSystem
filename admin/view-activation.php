<?php
include 'includes/header.php';
include 'fetch-activation.php';
include 'permission/permissionDeactivateActivateMember.php';
?>

<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>
        <?php
        include 'fetch-activation.php';
        ?>

        <!-- Main Content -->
        <div class="flex-grow-1 p-4">
            <div class="container-fluid mt-4 fade-in-down">
                <div class="row text-center">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">FOR ACTIVATION</h5>
                                <p class="card-text" id="current-visitors"><?php echo $totalAllForActivation; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">ACTIVATED</h5>
                                <p class="card-text" id="current-visitors"><?php echo $totalAllActivated; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">DEACTIVATED</h5>
                                <p class="card-text" id="current-visitors"><?php echo $totalAllDeactivated; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Validation message for adding an account -->
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert-container">
                        <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show"
                            role="alert">
                            <?php
                            echo $_SESSION['message'];
                            unset($_SESSION['message']);
                            unset($_SESSION['message_type']);
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="mt-4">
                    <h3>Activation</h3>
                    <div class="d-flex justify-content-between mb-3">
                        <input type="text" id="search-input" class="form-control w-25" placeholder="Search by name"
                            value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    </div>
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>FULL NAME</th>
                                <th>E-MAIL</th>
                                <th>SCHOOL NAME</th>
                                <th>AGE</th>
                                <th>BARANGAY</th>
                                <th>SEX</th>
                                <th>STATUS</th>
                                <th>ACTION</th>
                            </tr>
                        </thead>
                        <tbody id="activation-table">
                            <?php if ($accountResult->num_rows > 0): ?>
                                <?php while ($row = $accountResult->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name']; ?>
                                        </td>
                                        <td><?php echo $row['email_address']; ?></td>
                                        <td><?php echo $row['school_name']; ?></td>
                                        <td><?php echo $row['age']; ?></td>
                                        <td><?php echo $row['barangay_name']; ?></td>
                                        <td><?php echo $row['sex_name']; ?></td>
                                        <td><?php echo $row['status']; ?></td>
                                        <td class="d-flex justify-content-center gap-3">
                                            <?php if ($row['status'] !== 'ACTIVATED'): ?>
                                                <form action="process/member-activate-deactivate-logic.php" method="POST">
                                                    <input type="hidden" name="application_id" value="<?php echo $row['id']; ?>">
                                                    <input type="hidden" name="action" value="accept">
                                                    <button type="submit" class="btn btn-outline-info">ACTIVATE</button>
                                                </form>
                                            <?php else: ?>
                                                <form action="process/member-activate-deactivate-logic.php" method="POST">
                                                    <input type="hidden" name="application_id" value="<?php echo $row['id']; ?>">
                                                    <input type="hidden" name="action" value="decline">
                                                    <button type="submit" class="btn btn-outline-danger">DEACTIVATE</button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8">No records found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <nav aria-label="Page navigation">
                        <ul class="pagination" id="pagination">
                            <?php
                            $pagesToShow = 3;
                            $startPage = max(1, $page - (($page - 1) % $pagesToShow));
                            $endPage = min($totalPages, $startPage + $pagesToShow - 1);

                            // Display "Previous" button
                            if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a>
                                </li>
                            <?php endif; ?>

                            <!-- Display the range of pages dynamically -->
                            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>

                            <!-- Show ellipsis if there are more pages -->
                            <?php if ($endPage < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $endPage + 1; ?>">...</a>
                                </li>
                            <?php endif; ?>

                            <!-- Display "Next" button -->
                            <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    <p id="page-info">Page <?php echo $page; ?> of <?php echo $totalPages; ?></p>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>