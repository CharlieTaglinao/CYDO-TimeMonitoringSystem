<?php
include '../includes/database.php';

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Set pagination variables
$limit = 13; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; 
$offset = ($page - 1) * $limit; 

// Count total rows with search filter
$totalRowsQuery = "
    SELECT COUNT(username) AS total 
    FROM account 
    WHERE username LIKE '%$search%'
";
$totalRowsResult = $conn->query($totalRowsQuery);
$totalRows = $totalRowsResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit); 

// Fetch paginated records with search filter
$permissionQuery = "
    SELECT account.*, GROUP_CONCAT(user_permissions.permission_id) AS permissions,
    role.role AS role_name
    FROM account
    LEFT JOIN user_permissions ON account.id = user_permissions.user_id
    INNER JOIN role ON account.role = role.id
    WHERE account.username LIKE '%$search%'
    GROUP BY account.id
    LIMIT $limit OFFSET $offset";

$permissionResult = $conn->query($permissionQuery);

if (isset($_GET['pagination'])) {
    echo generatePaginationLinks($totalPages, $page);
    exit;
}

// Return the search results as HTML
if (isset($_GET['search']) && !isset($_GET['pagination'])) {
    while ($row = $permissionResult->fetch_assoc()) {
        $permissions = explode(',', $row['permissions']); // Convert permissions to array
        echo '<tr>';
        echo '<td>' . $row['username'] . '</td>';
        echo '<td>' . (in_array('T9rPHeL7ectsYwT6Ih2AswTeZ', $permissions) ? '<i class="fas fa-check" style="color: green;"></i>' : '<i class="fas fa-times" style="color: red;"></i>') . '</td>';
        echo '<td>' . (in_array('8sAygcnqpOXP8aAAG7IAWI4Cg', $permissions) ? '<i class="fas fa-check" style="color: green;"></i>' : '<i class="fas fa-times" style="color: red;"></i>') . '</td>';
        echo '<td>' . (in_array('GfsdZkrEFuNhmIUmxIm8e7fS8', $permissions) ? '<i class="fas fa-check" style="color: green;"></i>' : '<i class="fas fa-times" style="color: red;"></i>') . '</td>';
        echo '<td>' . (in_array('906IZi3K8od7FBS518t5I31jY', $permissions) ? '<i class="fas fa-check" style="color: green;"></i>' : '<i class="fas fa-times" style="color: red;"></i>') . '</td>';
        echo '<td>' . (in_array('JyCQULxjmYOycJFVOyceWb8BA', $permissions) ? '<i class="fas fa-check" style="color: green;"></i>' : '<i class="fas fa-times" style="color: red;"></i>') . '</td>';
        echo '<td>' . (in_array('c5pwoB1uPkzwwZgFokRZZ85fE', $permissions) ? '<i class="fas fa-check" style="color: green;"></i>' : '<i class="fas fa-times" style="color: red;"></i>') . '</td>';
        echo '<td>' . (in_array('qD0mEzTMK6Toi4u8aR1Pdusag', $permissions) ? '<i class="fas fa-check" style="color: green;"></i>' : '<i class="fas fa-times" style="color: red;"></i>') . '</td>';
        echo '<td>' . (in_array('ubmssiHKw9GEPDulEVpDtOudM', $permissions) ? '<i class="fas fa-check" style="color: green;"></i>' : '<i class="fas fa-times" style="color: red;"></i>') . '</td>';
        echo '<td>' . $row['role_name'] . '</td>';
        echo '</tr>';
    }
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