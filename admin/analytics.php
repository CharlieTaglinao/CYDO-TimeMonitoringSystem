<?php
include 'permission/permissionViewAnalytics.php';

if (isset($_GET['visitor_code'])) {
    $_SESSION['randomCode'] = $_GET['visitor_code'];
}

?>

<?php include 'includes/header.php'; ?>

<head>
    <!-- ...existing code... -->
    <script src="assets/js/chart.js"></script>
    <?php include 'fetch-analytics-data.php'; ?>
</head>

<body>
    <div class="d-flex fade-in-down">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>
        <!-- Main Content -->
        <div class="flex-grow-1 p-4">
            <div class="d-flex justify-content-between align-items-center mt-4">
                <h3>Analytics</h3>
                <h3>
                    <?php 
                    if (isset($_POST['year'])) {
                        echo htmlspecialchars($_POST['year']);
                    }
                    ?>
                </h3>
            </div>

            <form action="analytics.php" method="post" id="yearForm">
                <select class="form-select col-6" name="year" id="yearSelect" onchange="document.getElementById('yearForm').submit();">
                    <option value="" selected disabled>Select a year</option>
                    <?php
                    $currentYear = date("Y");
                    for ($year = 2024; $year <= $currentYear; $year++) {
                        echo "<option value=\"$year\">$year</option>";
                    }
                    ?>
                </select>
            </form>

            <div class="row mt-4">
                <h4>Time Logs of Visitors</h4>
                <div class="col-md-6">
                    <canvas id="visitorChart"></canvas>
                </div>
                 <div class="col-md-6">
                    <canvas id="memberChart"></canvas>
                </div>                 
            </div>

            <hr>

            <div class="row mt-4">
                <h4>Users</h4>
                <div class="col-md-6">
                    <canvas id="userChart"></canvas>
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