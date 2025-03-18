<?php
include 'permission/permissionViewAnalytics.php';

if (isset($_GET['visitor_code'])) {
    $_SESSION['randomCode'] = $_GET['visitor_code'];
}

?>

<?php include 'includes/header.php'; ?>

<head>
    <!-- ...existing code... -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?php include 'fetch-analytics-data.php'; ?>
</head>

<body class="bg-light">
    <div class="d-flex">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>
        <!-- Main Content -->
        <div class="flex-grow-1 p-4">
            <div class="mt-4">
                <h3>Analytics</h3>
            </div>

            <form action="analytics.php" method="post" id="yearForm">
                <select class="form-select col-6" name="year" id="yearSelect" onchange="document.getElementById('yearForm').submit();">
                    <option value="" selected disabled>Select a year</option>
                    <?php
                    $currentYear = date("Y");
                    for ($year = 2020; $year <= $currentYear; $year++) {
                        echo "<option value=\"$year\">$year</option>";
                    }
                    ?>
                </select>
            </form>

            <div class="row mt-4">
                <div class="col-md-6">
                    <canvas id="visitorChart"></canvas>
                </div>
                <div class="col-md-6">
                    <canvas id="userChart"></canvas>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-6">
                    <canvas id="cydoChart"></canvas>
                </div>
                <div class="col-md-6">
                    <canvas id="pdaoChart"></canvas>
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
        </div>
    </div>
</body>

</html>