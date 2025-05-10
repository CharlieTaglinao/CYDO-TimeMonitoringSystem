<?php
include '../includes/database.php';
if(!isset($_SESSION)) {
    session_start();

}

if (!function_exists('generatePaginationLinks')) {
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
}

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$username = $_SESSION['username'];

// Count all members applications
$totalAllQuery = "SELECT COUNT(*) AS totalApplication FROM member_applicants WHERE status IS NULL";
$totalAllResult = $conn->query($totalAllQuery);
$totalApplicants = $totalAllResult->fetch_assoc()['totalApplication'];


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

// Updated query to fetch data from related tables
$accountQuery = "SELECT 
    ma.id, 
    ma.first_name, 
    ma.middle_name, 
    ma.last_name, 
    ma.age, 
    me.email AS email_address, 
    ms.school_name, 
    b.barangay_name, 
    s.sex_name, 
    ma.status
FROM member_applicants ma
INNER JOIN member_email me ON ma.email_id = me.id
INNER JOIN member_school_name ms ON ma.school_id = ms.id
INNER JOIN barangays b ON ma.barangay_id = b.id
INNER JOIN sex s ON ma.sex_id = s.id
WHERE (ma.first_name LIKE '%$search%' OR ma.last_name LIKE '%$search%') AND ma.status IS NULL
ORDER BY ma.submitted_at DESC
LIMIT $limit OFFSET $offset";

// Debugging: Log the query to check for issues
error_log("SQL Query: $accountQuery");

$accountResult = $conn->query($accountQuery);

// Check if the query execution was successful
if (!$accountResult) {
    error_log("Query Error: " . $conn->error);
}

if (isset($_GET['search']) && !isset($_GET['pagination'])) {
    if ($accountResult->num_rows > 0) {
        while ($row = $accountResult->fetch_assoc()) {
            echo "<tr>
                <td>" . $row['first_name'] . " " . $row['middle_name'] . " " . $row['last_name'] . "</td>
                <td>" . $row['email_address'] . "</td>
                <td>" . $row['school_name'] . "</td>
                <td>" . $row['age'] . "</td>
                <td>" . $row['barangay_name'] . "</td>
                <td>" . $row['sex_name'] . "</td>
                <td class='d-flex justify-content-center gap-3'>
                    <button class='btn btn-outline-info'>ACCEPT</button>
                    <button class='btn btn-outline-danger'>DECLINE</button>
                </td>
            </tr>";
        }
    } else {
        echo "<tr><td colspan='7'>No records found</td></tr>";
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
?>