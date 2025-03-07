<?php
include '../includes/database.php';

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$startDate = $_POST['startDate'] ?? '';
$endDate = $_POST['endDate'] ?? '';
$all = $_POST['all'] ?? '';

// Count today's visitors from the time_logs table
$totalVisitorsTodayQuery = "SELECT COUNT(client_id) AS total_today FROM time_logs WHERE DATE(time_in) = CURDATE()";
$totalVisitorsTodayResult = $conn->query($totalVisitorsTodayQuery);
$totalVisitorsToday = $totalVisitorsTodayResult->fetch_assoc()['total_today'];

// Count current visitors
$currentVisitorsQuery = "SELECT (COUNT(client_id) - (SELECT COUNT(client_id) 
                        FROM time_logs WHERE DATE(time_in) = CURDATE() AND time_out IS NOT NULL)) 
                        AS current_visitors FROM time_logs 
                        WHERE DATE(time_in) = CURDATE();";
$currentVisitorsResult = $conn->query($currentVisitorsQuery);
$currentVisitors = $currentVisitorsResult->fetch_assoc()['current_visitors'];

// Count monthly visitors
$totalMonthlyVisitorsQuery = "SELECT COUNT(client_id) AS total_month FROM time_logs WHERE YEAR(time_in) = YEAR(CURDATE()) AND MONTH(time_in) = MONTH(CURDATE())";
$totalMonthlyVisitorsResult = $conn->query($totalMonthlyVisitorsQuery);
$totalMonthlyVisitor = $totalMonthlyVisitorsResult->fetch_assoc()['total_month'];

// Set pagination variables
$limit = 7; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; 
$offset = ($page - 1) * $limit; 

// Count total rows with search filter
$totalRowsQuery = "
    SELECT COUNT(*) AS total 
    FROM visitors 
    INNER JOIN time_logs ON visitors.id = time_logs.client_id
    WHERE CONCAT(visitors.first_name, ' ', visitors.middle_name, ' ', visitors.last_name) LIKE '%$search%'
";
$totalRowsResult = $conn->query($totalRowsQuery);
$totalRows = $totalRowsResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit); 

// Fetch paginated records with search filter
$visitorsQuery = "
    SELECT DISTINCT
        visitors.first_name, 
        visitors.middle_name, 
        visitors.last_name,
        visitors.age, 
        visitors.sex_id, 
        sex.sex_name,
        time_logs.time_in, 
        time_logs.time_out, 
        time_logs.status,
        time_logs.code, 
        purpose.purpose
    FROM visitors
    INNER JOIN time_logs ON visitors.id = time_logs.client_id
    INNER JOIN sex ON visitors.sex_id = sex.id
    INNER JOIN (SELECT client_id, MAX(id) as latest_purpose_id FROM purpose GROUP BY client_id) as latest_purposes ON visitors.id = latest_purposes.client_id
    INNER JOIN purpose ON latest_purposes.latest_purpose_id = purpose.id 
    WHERE CONCAT(visitors.first_name, ' ', visitors.middle_name, ' ', visitors.last_name) LIKE '%$search%'
    ORDER BY time_logs.time_in DESC 
    LIMIT $limit OFFSET $offset";

$visitorsResult = $conn->query($visitorsQuery);
if (isset($_GET['search']) && !isset($_GET['pagination'])) {
    if ($visitorsResult->num_rows > 0) {
        while ($row = $visitorsResult->fetch_assoc()) {
            echo "<tr>
                <td>" . strtoupper($row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name']) . "</td>
                <td>" . (isset($row['time_in']) ? date('Y-m-d', strtotime($row['time_in'])) : '-') . "</td>
                <td>" . (isset($row['time_in']) ? date('H:i:s', strtotime($row['time_in'])) : '-') . "</td>
                <td>" . (isset($row['time_out']) ? date('H:i:s', strtotime($row['time_out'])) : '-') . "</td>
                <td>";
            if (isset($row['time_in'], $row['time_out'])) {
                $timeIn = new DateTime($row['time_in']);
                $timeOut = new DateTime($row['time_out']);
                $interval = $timeIn->diff($timeOut);
                echo $interval->format('%h hours %i minutes %s seconds');
            } else {
                echo '-';
            }

            echo "</td>
                
                <td>
                    <button class='btn btn-success view-details'
                        data-name='" . strtoupper($row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name']) . "'
                        data-age='{$row['age']}'
                        data-sex='{$row['sex_name']}'
                        data-code='{$row['code']}'
                        data-purpose='{$row['purpose']}'
                        data-bs-toggle='modal'
                        data-bs-target='#visitorDetailsModal'>
                        View Details
                    </button>
                </td>
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