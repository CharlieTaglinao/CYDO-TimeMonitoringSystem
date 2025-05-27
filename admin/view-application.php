<?php
include 'includes/header.php';
include 'fetch-application.php';
include 'permission/permissionAcceptDeclineMemberApplication.php';
?>

<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>
        <?php
        include 'fetch-application.php';
        ?>

        <!-- Main Content -->
        <div class="flex-grow-1 p-4">
            <div class="container-fluid mt-4 fade-in-down">
                <div class="row text-center">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">TOTAL APPLICATION</h5>
                                <p class="card-text" id="current-visitors"><?php echo $totalApplicants; ?></p>
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
                    <h3>Applications</h3>
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
                                <th>ACTION</th>
                            </tr>
                        </thead>
                        <tbody id="application-table">
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
                                        <td class="d-flex justify-content-center gap-3">
                                            <form action="process/accept-decline-application-logic.php" method="POST">
                                                <input type="hidden" name="application_id" value="<?php echo $row['id']; ?>">
                                                <input type="hidden" name="action" value="accept">
                                                <button type="submit" class="btn btn-outline-info">ACCEPT</button>
                                            </form>
                                            <form action="process/accept-decline-application-logic.php" method="POST">
                                                <input type="hidden" name="application_id" value="<?php echo $row['id']; ?>">
                                                <input type="hidden" name="action" value="decline">
                                                <button type="submit" class="btn btn-outline-danger">DECLINE</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7">No records found</td>
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

    <!-- Membership Code Modal -->
    <div class="modal fade" id="membershipCodeModal" tabindex="-1" aria-labelledby="membershipCodeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="membershipCodeModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center" id="membershipCodeDetails">
                    <!-- Details will be injected here -->
                </div>
                <div class="">
                    <h6 class="fst-italic fw-normal">Note: Please take a capture of this details</h6>
                </div>
              
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Handle Accept/Decline button
            document.querySelectorAll('form[action="process/accept-decline-application-logic.php"]').forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    event.preventDefault(); // Prevent form submission

                    const action = form.querySelector('input[name="action"]').value;
                    const swalTitle = action === 'accept' ? 'Accept Application?' : 'Decline Application?';
                    const swalText = action === 'accept' ? 'Are you sure you want to accept this application?' : 'Are you sure you want to decline this application?';
                    const swalIcon = action === 'accept' ? 'info' : 'warning';

                    Swal.fire({
                        title: swalTitle,
                        text: swalText,
                        icon: swalIcon,
                        showCancelButton: true,
                        confirmButtonText: 'Yes',
                        cancelButtonText: 'No'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            if (action === 'accept') {
                                // AJAX for accept
                                const formData = new FormData(form);
                                // Get details from the row
                                const row = form.closest('tr');
                                const fullName = row.children[0].textContent.trim();  
                                const school = row.children[2].textContent.trim();
                                fetch('process/accept-decline-application-logic.php', {
                                    method: 'POST',
                                    body: formData
                                })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.status === 'success' && data.membership_code) {
                                            // Set modal header and body with details
                                            document.getElementById('membershipCodeModalLabel').textContent = fullName;
                                            document.getElementById('membershipCodeDetails').innerHTML = `
                                                <div class="mb-2"><strong>School:</strong> ${school}</div>
                                                <div class="mb-2 fw-bold fs-5"><strong>Membership Code:</strong> <span class="text-success">${data.membership_code}</span></div>
                                            `;
                                            var modal = new bootstrap.Modal(document.getElementById('membershipCodeModal'));
                                            modal.show();
                                            // Optionally reload after closing modal
                                            document.getElementById('membershipCodeModal').addEventListener('hidden.bs.modal', function () {
                                                window.location.reload();
                                            }, { once: true });
                                        } else {
                                            window.location.reload();
                                        }
                                    })
                                    .catch(() => window.location.reload());
                            } else {
                                // Decline: normal submit
                                form.submit();
                            }
                        }
                    });
                });
            });
        });
    </script>
</body>

</html>