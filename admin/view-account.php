<?php
session_start();
include 'includes/header.php';
include 'fetch-accounts.php';
?>

<body class="bg-light">
    <div class="d-flex">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php';
        include 'edit-account-modal.php' ?>

        <!-- Main Content -->
        <div class="flex-grow-1 p-4">
            <div class="container mt-4">
                <div class="row text-center">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">ADMIN</h5>
                                <p class="card-text"><?php echo $totalAdmin; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">STAFF</h5>
                                <p class="card-text" id="current-visitors"><?php echo $totalStaff; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">TOTAL ACCOUNTS</h5>
                                <p class="card-text" id="current-visitors"><?php echo $totalAll; ?></p>
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
                    <h3>Account Records</h3>
                    <div class="d-flex justify-content-between mb-3">
                    <input type="text" id="search-input" class="form-control w-25" placeholder="Search by name"
                    value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    </div>
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Date Created</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="account-table">
                            <?php while ($row = $accountResult->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['username']; ?></td>
                                    <td><?php echo $row['role'] == 1 ? 'Admin' : ($row['role'] == 2 ? 'Staff' : 'Unknown'); ?></td>
                                    <td><?php echo $row['created_at']; ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary editModalBtn"
                                            data-id="<?php echo $row['id']; ?>"
                                            data-username="<?php echo $row['username']; ?>"
                                            data-role="<?php echo $row['role']; ?>" data-bs-toggle="modal"
                                            data-bs-target="#editModal">
                                            EDIT
                                        </button>

                                        <form action="process/delete-account-logic.php" method="POST"
                                            id="delete-button-on-form" class="d-inline delete-form">
                                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                            <button type="submit"
                                                class="btn btn-sm btn-danger delete-button">DELETE</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
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
