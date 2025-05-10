<?php
include '../includes/database.php';
if (!isset($_SESSION)) {
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

// Count all activated members
$totalAllQuery = "SELECT COUNT(*) AS TOTAL_ACTIVATED FROM member_applicants WHERE status = 'ACTIVATED'";
$totalAllResult = $conn->query($totalAllQuery);
$totalAllActivated = $totalAllResult->fetch_assoc()['TOTAL_ACTIVATED'];

// Count all deactivated members
$totalDeactivatedQuery = "SELECT COUNT(*) AS TOTAL_DEACTIVATED FROM member_applicants WHERE status = 'DEACTIVATED'";
$totalDeactivatedResult = $conn->query($totalDeactivatedQuery);
$totalAllDeactivated = $totalDeactivatedResult->fetch_assoc()['TOTAL_DEACTIVATED'];

// Count all for activation
$totalForActivationQuery = "SELECT COUNT(*) AS TOTAL_FOR_ACTIVATION FROM member_applicants WHERE status = 'FOR ACTIVATION'";
$totalForActivationResult = $conn->query($totalForActivationQuery);
$totalAllForActivation = $totalForActivationResult->fetch_assoc()['TOTAL_FOR_ACTIVATION'];

// Set pagination variables
$limit = 8;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Count total rows with search filter
$totalRowsQuery = "SELECT COUNT(*) AS total FROM account WHERE username != ?";
if (!empty($search)) {
    $totalRowsQuery .= " AND username LIKE ?";
}
$stmt = $conn->prepare($totalRowsQuery);
if (!empty($search)) {
    $searchParam = "$search%";
    $stmt->bind_param('ss', $username, $searchParam);
} else {
    $stmt->bind_param('s', $username);
}
$stmt->execute();
$totalRowsResult = $stmt->get_result();
$totalRows = $totalRowsResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);
$stmt->close();

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
WHERE (ma.first_name LIKE ? OR ma.last_name LIKE ?) AND ma.status IS NOT NULL
ORDER BY ma.submitted_at DESC
LIMIT ? OFFSET ?";
$stmt = $conn->prepare($accountQuery);
$searchParam = "%$search%";
$stmt->bind_param('ssii', $searchParam, $searchParam, $limit, $offset);
$stmt->execute();
$accountResult = $stmt->get_result();
$stmt->close();

if (isset($_GET['search']) && !isset($_GET['pagination'])) {
    if ($accountResult->num_rows > 0) {
        while ($row = $accountResult->fetch_assoc()) {
            echo "<tr>
                <td>" . htmlspecialchars($row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name']) . "</td>
                <td>" . htmlspecialchars($row['email_address']) . "</td>
                <td>" . htmlspecialchars($row['school_name']) . "</td>
                <td>" . htmlspecialchars($row['age']) . "</td>
                <td>" . htmlspecialchars($row['barangay_name']) . "</td>
                <td>" . htmlspecialchars($row['sex_name']) . "</td>
                <td>" . htmlspecialchars($row['status']) . "</td>
                <td class='d-flex justify-content-center gap-3'>";
            if ($row['status'] !== 'ACTIVATED') {
                echo "<form action='process/member-activate-deactivate-logic.php' method='POST'>
                        <input type='hidden' name='application_id' value='" . htmlspecialchars($row['id']) . "'>
                        <input type='hidden' name='action' value='accept'>
                        <button type='submit' class='btn btn-outline-info'>ACTIVATE</button>
                    </form>";
            } else {
                echo "<form action='process/member-activate-deactivate-logic.php' method='POST'>
                        <input type='hidden' name='application_id' value='" . htmlspecialchars($row['id']) . "'>
                        <input type='hidden' name='action' value='decline'>
                        <button type='submit' class='btn btn-outline-danger'>DEACTIVATE</button>
                    </form>";
            }
            echo "</td>
            </tr>";
        }
    } else {
        echo "<tr><td colspan='8'>No records found</td></tr>";
    }
    echo "<input type='hidden' id='total-rows' value='" . htmlspecialchars($totalRows) . "'>";
    echo "<script>
        document.getElementById('pagination').innerHTML = '" . generatePaginationLinks($totalPages, $page) . "';
        document.getElementById('page-info').textContent = 'Page $page of $totalPages';
    </script>";
    exit;
}

if (isset($_GET['pagination'])) {
    echo generatePaginationLinks($totalPages, $page);
    exit;
}
?>