<?php
include 'includes/header.php';
include 'fetch-account-type.php';
?>

<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php';
        include 'edit-account-type-modal.php' ?>

        <!-- Main Content -->
        <div class="flex-grow-1 p-4">
            <div class="container-fluid mt-4">
                <div class="row text-center">
                    <div class="col-md">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">TOTAL ACCOUNT TYPE</h5>
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
                                <th>ID</th>
                                <th>ACCOUNT TYPE NAME</th>
                                <th>DATE CREATED</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="account-table">
                            <?php if ($accountResult->num_rows > 0): ?>
                                <?php while ($row = $accountResult->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td><?php echo $row['role']?></td>
                                        </td>
                                        <td><?php echo $row['created_at']; ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary editModalBtn"
                                                data-id="<?php echo $row['id']; ?>"
                                                data-role="<?php echo $row['role']; ?>" 
                                                onclick="confirmEdit(this)">
                                                EDIT
                                            </button>

                                            <form action="process/delete-account-type-logic.php" method="POST" id="delete-button-on-form" class="d-inline delete-form">
                                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                <button type="submit"
                                                    class="btn btn-sm btn-danger delete-button">DELETE</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan='5'>No records found</td></tr>
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
    <script>
        function confirmEdit(button) {
            const id = button.getAttribute('data-id');
            const role = button.getAttribute('data-role');
            const editModal = new bootstrap.Modal(document.getElementById('editModal'));
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-role').value = role;
            editModal.show();
        }
    </script>
</body>