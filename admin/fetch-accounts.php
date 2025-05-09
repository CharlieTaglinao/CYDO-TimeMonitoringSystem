<?php
include '../includes/database.php';
if(!isset($_SESSION)) {
    session_start();

}

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$username = $_SESSION['username'];

// Count all users
$totalAllQuery = "SELECT COUNT(*) AS total_all FROM account";
$totalAllResult = $conn->query($totalAllQuery);
$totalAll = $totalAllResult->fetch_assoc()['total_all'];

// Set pagination variables
$limit = 8;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Count total rows with search filter
$totalRowsQuery = "SELECT COUNT(*) AS total FROM account WHERE username != '$username'";
if (!empty($search)) {
    $totalRowsQuery .= " AND username LIKE '$search%'";
}
$totalRowsResult = $conn->query($totalRowsQuery);
$totalRows = $totalRowsResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

// Fetch accounts with optional search
$accountQuery = "SELECT account.id, account.username, account.role, role.role AS role_name, account.created_at, account_email.email_address 
                FROM account
                INNER JOIN account_email ON account.email_id = account_email.id 
                INNER JOIN role ON account.role = role.id 
                WHERE username LIKE '$search%' AND username != '$username' ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$accountResult = $conn->query($accountQuery);

if (isset($_GET['search']) && !isset($_GET['pagination'])) {
    if ($accountResult->num_rows > 0) {
        while ($row = $accountResult->fetch_assoc()) {
            $roleName = $row['role_name'];
            echo "<tr>
                <td>" . $row['username']  . "</td>
                <td>" . $row['email_address'] . "</td>
                <td>" . $roleName . "</td>
                <td>" . $row['created_at'] . "</td>

                <td>
                    <button class='btn btn-sm btn-outline-info editModalBtn'
                            data-id='" . $row['id'] . "'
                            data-username='" . $row['username'] . "'
                            data-role='" . $row['role'] . "' 
                            data-bs-toggle='modal'
                            data-bs-target='#editModal'>
                            EDIT
                    </button>
                
                    <form action='process/delete-account-logic.php' method='POST' id='delete-button-on-form' class='d-inline delete-form'>
                        <input type='hidden' name='id' value='" . $row['id'] . "'>
                        <button type='submit' class='btn btn-sm btn-outline-danger delete-button'>DELETE</button>
                    </form>
                </td>
            </tr>";
        }
    } else {
        echo "<tr><td colspan='5'>No records found</td></tr>";
    }
    echo "<input type='hidden' id='total-rows' value='$totalRows'>";
    echo "<script>
        document.getElementById('pagination').innerHTML = '".generatePaginationLinks($totalPages, $page)."';
        document.getElementById('page-info').textContent = 'Page $page of $totalPages';
    </script>";
    exit;
}

if (isset($_GET['pagination'])) {
    echo generatePaginationLinks($totalPages, $page);
    exit;
}

function generatePaginationLinks($totalPages, $currentPage) {
    $pagesToShow = 3;
    $startPage = max(1, $currentPage - (($currentPage - 1) % $pagesToShow));
    $endPage = min($totalPages, $startPage + $pagesToShow - 1);
    $paginationLinks = '';

    if ($currentPage > 1) {
        $paginationLinks .= '<li class="page-item"><a class="page-link" href="?page=' . ($currentPage - 1) . '">Previous</a></li>';
    }

    for ($i = $startPage; $i <= $endPage; $i++) {
        $paginationLinks .= '<li class="page-item ' . ($i == $currentPage ? 'active' : '') . '"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
    }

    if ($endPage < $totalPages) {
        $paginationLinks .= '<li class="page-item"><a class="page-link" href="?page=' . ($endPage + 1) . '">...</a></li>';
    }

    if ($currentPage < $totalPages) {
        $paginationLinks .= '<li class="page-item"><a class="page-link" href="?page=' . ($currentPage + 1) . '">Next</a></li>';
    }

    return $paginationLinks;
}
?>