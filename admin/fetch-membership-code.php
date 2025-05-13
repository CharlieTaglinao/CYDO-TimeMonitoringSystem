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
    $totalRowsQuery .= " AND (visitors.first_name LIKE '%$search%' OR visitors.last_name LIKE '%$search%')";
}
$totalRowsResult = $conn->query($totalRowsQuery);
$totalRows = $totalRowsResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

// Updated query to fetch data from related tables
$accountQuery = "SELECT 
    visitors.first_name,
    visitors.middle_name,
    visitors.last_name,
    visitor_school_name.school_name,
    visitors.age,
    barangays.barangay_name,
    sex.sex_name,
    member_code.membership_code
FROM visitors
INNER JOIN visitor_school_name ON visitors.school_id = visitor_school_name.id
INNER JOIN barangays ON visitors.barangay_id = barangays.id
INNER JOIN member_code ON visitors.membership_id = member_code.id
INNER JOIN sex ON visitors.sex_id = sex.id
WHERE (visitors.first_name LIKE '%$search%' OR visitors.last_name LIKE '%$search%')
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
                <td>" . $row['school_name'] . "</td>
                <td>" . $row['age'] . "</td>
                <td>" . $row['barangay_name'] . "</td>
                <td>" . $row['sex_name'] . "</td>
                <td>" . $row['membership_code'] . "</td>
            </tr>";
        }
    } else {
        echo "<tr><td colspan='6'>No records found</td></tr>";
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