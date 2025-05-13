<?php
include 'permission/permissionViewUserPermission.php';

if (isset($_GET['visitor_code'])) {
    $_SESSION['randomCode'] = $_GET['visitor_code'];
}
include 'fetch-user-permission.php';
include 'includes/header.php';
?>

<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>
        <!-- Main Content -->
        <div class="flex-grow-1 p-4">
            <div class="container-fluid mt-4">

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
                                <th style="width: 50%">Username</th>
                                <th style="width: 50%;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="user-permissions-table">
                            <?php
                                while ($row = $permissionResult->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $row['username']; ?></td>
                                        <td>
                                            <button class="btn btn-outline-info view-permission-btn" data-username="<?php echo $row['username']; ?>" data-permissions="<?php echo htmlspecialchars($row['permissions']); ?>">VIEW PERMISSIONS</button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                        </tbody>
                    </table>

                    <!-- Modal -->
                    <div class="modal fade" id="permissionModal" tabindex="-1" aria-labelledby="permissionModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="permissionModalLabel">Permissions for <span id="modal-username"></span></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div id="permission-container" style="display: flex; flex-direction: column; gap: 10px;">
                                        <!-- Dynamic content will be inserted here -->
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const viewButtons = document.querySelectorAll('.view-permission-btn');

                            viewButtons.forEach(button => {
                                button.addEventListener('click', function () {
                                    const username = this.getAttribute('data-username');
                                    const permissions = this.getAttribute('data-permissions').split(',');

                                    const allActions = {
                                            'T9rPHeL7ectsYwT6Ih2AswTeZ': 'Add Account',
                                            'c5pwoB1uPkzwwZgFokRZZ85fE': 'Monitor Visitor',
                                            'GfsdZkrEFuNhmIUmxIm8e7fS8': 'Download Reports',
                                            'qD0mEzTMK6Toi4u8aR1Pdusag': 'View Analytics',
                                            '906IZi3K8od7FBS518t5I31jY': 'Edit/Delete Account',
                                            '8sAygcnqpOXP8aAAG7IAWI4Cg': 'Add Permission',
                                            'ubmssiHKw9GEPDulEVpDtOudM': 'View User Permission',
                                            '6QmUceiC4tYAFPR8ablg55KFZ': 'Accept/Decline Application',
                                            '3DijIfOkS1uljCeXsJoyJAIbt': 'Activate/Deactivate Member',
                                            'M24yNFhXnUXIgzruLUVLrr1dQ': 'View Member Codes',
                                            'nLpx3AoiT6FJB8BVTGy4D6VE7': 'Add Account Type',
                                            'p1YjKW0Ny5bLE64YdGreQJ92w': 'View Account Type',
                                    };

                                    const permissionContainer = document.getElementById('permission-container');
                                    permissionContainer.innerHTML = '';

                                    for (const [key, action] of Object.entries(allActions)) {
                                        const actionDiv = document.createElement('div');
                                        actionDiv.style.display = 'flex';
                                        actionDiv.style.justifyContent = 'space-between';

                                        const actionName = document.createElement('span');
                                        actionName.textContent = action;

                                        const actionStatus = document.createElement('span');
                                        if (permissions.includes(key)) {
                                            actionStatus.textContent = 'Permitted';
                                            actionStatus.style.color = 'green';
                                            actionStatus.style.fontWeight = 'bold';
                                        } else {
                                            actionStatus.textContent = 'Prohibited';
                                            actionStatus.style.color = 'red';
                                            actionStatus.style.fontWeight = 'bold';
                                        }

                                        actionDiv.appendChild(actionName);
                                        actionDiv.appendChild(actionStatus);
                                        permissionContainer.appendChild(actionDiv);
                                    }

                                    document.getElementById('modal-username').textContent = username;

                                    const modal = new bootstrap.Modal(document.getElementById('permissionModal'));
                                    modal.show();
                                });
                            });
                        });
                    </script>
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