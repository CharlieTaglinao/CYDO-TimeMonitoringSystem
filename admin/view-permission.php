<?php
include 'permission/permissionViewUserPermission.php';

if (isset($_GET['visitor_code'])) {
    $_SESSION['randomCode'] = $_GET['visitor_code'];
}
include 'fetch-user-permission.php';
include 'includes/header.php';
?>

<body class="bg-light">
    <div class="d-flex">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>
        <!-- Main Content -->
        <div class="flex-grow-1 p-4">
            <div class="container mt-4">

                <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show mt-3" role="alert">
                    <?php echo $_SESSION['message'];
                    unset($_SESSION['message']);
                    unset($_SESSION['message_type']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>

                <div class="mt-4">
                    <h3>User's Permissions</h3>
                    <div class="d-flex justify-content-between mb-3">
                        <!-- Search Box -->
                        <div class="row">
                            <div class="col">
                                <input type="text" id="search-input-user-permission" class="form-control" placeholder="Search by username"
                                    value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                            </div>
                        </div>
                    </div>

                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Username</th>
                                <th>Add Account</th>
                                <th>Add Role</th>
                                <th>Download Reports</th>
                                <th>Edit/Delete Account</th>
                                <th>Monitor Dashboard</th>
                                <th>Monitor Visitor</th>
                                <th>View Analytics</th>
                                <th>View Permissions</th>
                            </tr>
                        </thead>
                        <tbody id="user-permission-table">
                            <?php
                                while ($row = $permissionResult->fetch_assoc()):
                                    $permissions = explode(',', $row['permissions']); // Convert permissions to array
                                    ?>
                                    <tr>
                                        <td>
                                            <?php echo $row['username']; ?>
                                        </td>
                                        <td><?php echo in_array('T9rPHeL7ectsYwT6Ih2AswTeZ', $permissions) ? '<i class="fas fa-check" style="color: green;"></i>' : '<i class="fas fa-times" style="color: red;"></i>'; ?></td>
                                        <td><?php echo in_array('8sAygcnqpOXP8aAAG7IAWI4Cg', $permissions) ? '<i class="fas fa-check" style="color: green;"></i>' : '<i class="fas fa-times" style="color: red;"></i>'; ?></td>
                                        <td><?php echo in_array('GfsdZkrEFuNhmIUmxIm8e7fS8', $permissions) ? '<i class="fas fa-check" style="color: green;"></i>' : '<i class="fas fa-times" style="color: red;"></i>'; ?></td>
                                        <td><?php echo in_array('906IZi3K8od7FBS518t5I31jY', $permissions) ? '<i class="fas fa-check" style="color: green;"></i>' : '<i class="fas fa-times" style="color: red;"></i>'; ?></td>
                                        <td><?php echo in_array('JyCQULxjmYOycJFVOyceWb8BA', $permissions) ? '<i class="fas fa-check" style="color: green;"></i>' : '<i class="fas fa-times" style="color: red;"></i>'; ?></td>
                                        <td><?php echo in_array('c5pwoB1uPkzwwZgFokRZZ85fE', $permissions) ? '<i class="fas fa-check" style="color: green;"></i>' : '<i class="fas fa-times" style="color: red;"></i>'; ?></td>
                                        <td><?php echo in_array('qD0mEzTMK6Toi4u8aR1Pdusag', $permissions) ? '<i class="fas fa-check" style="color: green;"></i>' : '<i class="fas fa-times" style="color: red;"></i>'; ?></td>
                                        <td><?php echo in_array('ubmssiHKw9GEPDulEVpDtOudM', $permissions) ? '<i class="fas fa-check" style="color: green;"></i>' : '<i class="fas fa-times" style="color: red;"></i>'; ?></td>
                                    </tr>
                                    <?php
                                endwhile;
                            ?>
                        </tbody>
                    </table>
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
</body>

</html>