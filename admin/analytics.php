<?php
include 'permission/permissionViewAnalytics.php';

if (isset($_GET['visitor_code'])) {
    $_SESSION['randomCode'] = $_GET['visitor_code'];
}

?>


<?php include 'includes/header.php'; 
    include 'fetch-analytics.php';?>

<body class="bg-light">
    <div class="d-flex">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>
        <!-- Main Content -->
        <div class="flex-grow-1 p-4">
            <div class="container mt-4">
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title fw-bold">VISITORS</h5>
                                <h4 class="card-text fw-normal">
                                    49
                                    <!-- <?php echo $totalVisitorsToday; ?> -->
                                </h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title fw-bold">USERS</h5>
                                <h4 class="card-text fw-normal" id="current-visitors">
                                    3
                                </h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title fw-bold">CYDO</h5>
                                <h4 class="card-text fw-normal" id="current-visitors">
                                    30
                                </h4>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title fw-bold">PDAO</h5>
                                <h4 class="card-text fw-normal" id="current-visitors">
                                    19
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>

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
                    <h3>Analytics</h3>
                    <div class="d-flex justify-content-between mb-3">
                        <!-- Search Box -->
                        <div class="row">
                            <div class="col">
                                <input type="text" id="search-input" class="form-control" placeholder="Search by name"
                                    value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                            </div>

                            <div class="col-3">
                                <form method="POST" action="index.php?page=1">
                                    <input type="hidden" name="all" id="all">
                                    <button class="form-control" id="allBtn">All</button>
                                </form>
                            </div>
                            <div class="col-4">
                                <form method="POST" action="index.php?page=1">
                                    <input type="hidden" name="startDate" id="startDate" value="<?php echo $startDate; ?>">
                                    <input type="hidden" name="endDate" id="endDate" value="<?php echo $endDate; ?>">
                                    <button class="form-control" id="customRangeBtn">Custom range</button>
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
    <script src="assets/js/script.js"></script>
</body>

</html>