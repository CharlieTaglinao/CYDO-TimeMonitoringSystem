<?php
include '../includes/database.php';

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$startDate = $_POST['startDate'] ?? $_GET['startDate'] ?? '';
$endDate = $_POST['endDate'] ?? $_GET['endDate'] ?? '';
$all = $_POST['all'] ?? '';

// Count today's visitors
$totalVisitorsTodayQuery = "SELECT COUNT(client_id) AS total_today FROM time_logs WHERE DATE(time_in) = CURDATE()";
$totalVisitorsTodayResult = $conn->query($totalVisitorsTodayQuery);
$totalVisitorsToday = $totalVisitorsTodayResult->fetch_assoc()['total_today'] ?? 0;

// Count current visitors
$currentVisitorsQuery = "SELECT (COUNT(client_id) - (SELECT COUNT(client_id) 
                        FROM time_logs WHERE DATE(time_in) = CURDATE() AND time_out IS NOT NULL)) 
                        AS current_visitors FROM time_logs 
                        WHERE DATE(time_in) = CURDATE();";
$currentVisitorsResult = $conn->query($currentVisitorsQuery);
$currentVisitors = $currentVisitorsResult->fetch_assoc()['current_visitors'] ?? 0;

// Count monthly visitors
$totalMonthlyVisitorsQuery = "SELECT COUNT(client_id) AS total_month FROM time_logs WHERE YEAR(time_in) = YEAR(CURDATE()) AND MONTH(time_in) = MONTH(CURDATE())";
$totalMonthlyVisitorsResult = $conn->query($totalMonthlyVisitorsQuery);
$totalMonthlyVisitor = $totalMonthlyVisitorsResult->fetch_assoc()['total_month'] ?? 0;

// Pagination setup
$limit = 7;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1);
$offset = ($page - 1) * $limit;

// Count total filtered rows
$totalRowsQuery = "
    SELECT COUNT(DISTINCT visitors.id) AS total 
    FROM visitors 
    INNER JOIN time_logs ON visitors.id = time_logs.client_id
    WHERE (
        CONCAT(visitors.first_name, ' ', visitors.middle_name, ' ', visitors.last_name) LIKE '$search%'
        OR CONCAT(visitors.first_name, ' ', visitors.last_name) LIKE '$search%'
        OR visitors.first_name LIKE '$search%'
        OR visitors.middle_name LIKE '$search%'
        OR visitors.last_name LIKE '$search%'
    )
    AND ('$startDate' = '' OR DATE(time_logs.time_in) >= '$startDate')
    AND ('$endDate' = '' OR DATE(time_logs.time_in) <= '$endDate')
";
$totalRowsResult = $conn->query($totalRowsQuery);
$totalRows = $totalRowsResult->fetch_assoc()['total'] ?? 0;
$totalPages = max(ceil($totalRows / $limit), 1);

// Fetch paginated visitors
$visitorsQuery = "
    SELECT DISTINCT
        visitors.first_name, 
        visitors.middle_name, 
        visitors.last_name,
        visitors.age, 
        visitors.sex_id,
        visitors.school_id, 
        sex.sex_name,
        visitor_school_name.school_name,
        time_logs.time_in, 
        time_logs.time_out, 
        time_logs.status,
        time_logs.code, 
        purpose.purpose
    FROM visitors
    INNER JOIN time_logs ON visitors.id = time_logs.client_id
    INNER JOIN sex ON visitors.sex_id = sex.id
    INNER JOIN visitor_school_name ON visitors.school_id = visitor_school_name.id
    INNER JOIN (SELECT client_id, MAX(id) as latest_purpose_id FROM purpose GROUP BY client_id) as latest_purposes ON visitors.id = latest_purposes.client_id
    INNER JOIN purpose ON latest_purposes.latest_purpose_id = purpose.id 
    WHERE CONCAT(visitors.first_name, ' ', visitors.middle_name, ' ', visitors.last_name) LIKE '$search%'
    OR CONCAT(visitors.first_name, ' ', visitors.last_name) LIKE '$search%'
    OR visitors.first_name LIKE '$search%'
    OR visitors.middle_name LIKE '$search%'
    OR visitors.last_name LIKE '$search%'
    AND ('$startDate' = '' OR DATE(time_logs.time_in) >= '$startDate')
    AND ('$endDate' = '' OR DATE(time_logs.time_in) <= '$endDate')
    ORDER BY time_logs.time_in DESC 
    LIMIT $limit OFFSET $offset";

$visitorsResult = $conn->query($visitorsQuery);

// Handle AJAX requests for dynamic table updates
if (isset($_GET['ajax']) && $_GET['ajax'] === 'true') {
    // Fetch paginated visitors for AJAX requests
    if ($visitorsResult->num_rows > 0) {
        while ($row = $visitorsResult->fetch_assoc()) {
            $fullName = strtoupper(htmlspecialchars($row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name']));
            $date = isset($row['time_in']) ? date('Y-m-d', strtotime($row['time_in'])) : '-';
            $timeIn = isset($row['time_in']) ? date('H:i:s', strtotime($row['time_in'])) : '-';
            $timeOut = isset($row['time_out']) ? date('H:i:s', strtotime($row['time_out'])) : '-';

            echo "<tr>
                <td style='width: 20%;'>{$fullName}</td>
                <td style='width: 10%;'>{$date}</td>
                <td style='width: 5%;'>{$timeIn}</td>
                <td style='width: 5%;'>{$timeOut}</td>
                <td style='width: 20%;'>";

            if (isset($row['time_in'], $row['time_out'])) {
                $timeInObj = new DateTime($row['time_in']);
                $timeOutObj = new DateTime($row['time_out']);
                $interval = $timeInObj->diff($timeOutObj);
                echo $interval->format('%h hours %i minutes %s seconds');
            } else {
                echo '-';
            }

            echo "</td><td style='width: 10%;'>" . htmlspecialchars($row['school_name']) . "</td>
                <td style='width: 12%;'>" . htmlspecialchars($row['status']) . "</td>
                <td style='width: 20%;'>
                   <button class='btn btn-outline-success view-details'
                        data-name='{$fullName}'
                        data-age='" . htmlspecialchars($row['age']) . "'
                        data-sex='" . htmlspecialchars($row['sex_name']) . "'
                        data-code='" . htmlspecialchars($row['code']) . "'
                        data-purpose='" . htmlspecialchars($row['purpose']) . "'
                        data-bs-toggle='modal'
                        data-bs-target='#visitorDetailsModal'>
                        View Details
                    </button>";

            if ($row['time_out'] === null) {
                echo "<form action='process/force-time-out-visitor.php' method='POST' style='display:inline;'>
                        <input type='hidden' name='visitor_code' value='" . htmlspecialchars($row['code']) . "'>
                        <button type='submit' class='btn btn-outline-danger'>Time Out</button>
                      </form>";
            }

            echo "</td>
            </tr>";
        }
    } else {
        echo "<tr><td colspan='8'>No records found</td></tr>";
    }

    // Return updated pagination links
    echo '<script>
        document.getElementById("pagination").innerHTML = `' . generatePaginationLinks($totalPages, $page) . '`;
        document.getElementById("page-info").textContent = "Page ' . $page . ' of ' . $totalPages . '";
    </script>';
    exit;
}

// Handle search results
if (isset($_GET['search']) && !isset($_GET['pagination'])) {
    if ($visitorsResult->num_rows > 0) {
        while ($row = $visitorsResult->fetch_assoc()) {
            $fullName = strtoupper(htmlspecialchars($row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name']));
            $date = isset($row['time_in']) ? date('Y-m-d', strtotime($row['time_in'])) : '-';
            $timeIn = isset($row['time_in']) ? date('H:i:s', strtotime($row['time_in'])) : '-';
            $timeOut = isset($row['time_out']) ? date('H:i:s', strtotime($row['time_out'])) : '-';


            echo "<tr>
                <td>{$fullName}</td>
                <td>{$date}</td>
                <td>{$timeIn}</td>
                <td>{$timeOut}</td>
                <td>";

            if (isset($row['time_in'], $row['time_out'])) {
                $timeInObj = new DateTime($row['time_in']);
                $timeOutObj = new DateTime($row['time_out']);
                $interval = $timeInObj->diff($timeOutObj);
                echo $interval->format('%h hours %i minutes %s seconds');
            } else {
                echo '-';
            }

            echo "</td><td>" . htmlspecialchars($row['school_name']) . "</td>
                <td>" . htmlspecialchars($row['status']) . "</td>
                <td>
                   <button class='btn btn-outline-info view-details'
                        data-name='{$fullName}'
                        data-age='" . htmlspecialchars($row['age']) . "'
                        data-sex='" . htmlspecialchars($row['sex_name']) . "'
                        data-code='" . htmlspecialchars($row['code']) . "'
                        data-purpose='" . htmlspecialchars($row['purpose']) . "'
                        data-bs-toggle='modal'
                        data-bs-target='#visitorDetailsModal'>
                        View Details
                    </button>";

            if ($row['time_out'] === null) {
                echo "<form action='process/force-time-out-visitor.php' method='POST' style='display:inline;'>
                        <input type='hidden' name='visitor_code' value='" . htmlspecialchars($row['code']) . "'>
                        <button type='submit' class='btn btn-outline-danger'>Time Out</button>
                      </form>";
            }

            echo "</td>
            </tr>";
        }
    } else {
        echo "<tr><td colspan='8'>No records found</td></tr>";
    }
    
    echo "<input type='hidden' id='total-rows' value='$totalRows'>";
    ?>
    <?php
    exit;
}

// Handle pagination separately
if (isset($_GET['pagination'])) {
    echo generatePaginationLinks($totalPages, $page);
    exit;
}

// Generate pagination links
function generatePaginationLinks($totalPages, $currentPage) {
    $paginationLinks = '';
    $range = 2; // Number of pages to show before and after the current page
    $startPage = max(1, $currentPage - $range);
    $endPage = min($totalPages, $currentPage + $range);

    if ($currentPage > 1) {
        $paginationLinks .= '<li class="page-item"><a class="page-link" href="?page=' . ($currentPage - 1) . '">Previous</a></li>';
    }

    // Show ellipsis if there are pages before the start range
    if ($startPage > 1) {
        $paginationLinks .= '<li class="page-item"><a class="page-link" href="?page=1">1</a></li>';
        if ($startPage > 2) {
            $paginationLinks .= '<li class="page-item"><a class="page-link" href="?page=' . ($startPage - 1) . '">...</a></li>';
        }
    }

    for ($i = $startPage; $i <= $endPage; $i++) {
        $paginationLinks .= '<li class="page-item ' . ($i == $currentPage ? 'active' : '') . '">
                                <a class="page-link" href="?page=' . $i . '">' . $i . '</a>
                             </li>';
    }

    // Show ellipsis if there are pages after the end range
    if ($endPage < $totalPages) {
        if ($endPage < $totalPages - 1) {
            $paginationLinks .= '<li class="page-item"><a class="page-link" href="?page=' . ($endPage + 1) . '">...</a></li>';
        }
        $paginationLinks .= '<li class="page-item"><a class="page-link" href="?page=' . $totalPages . '">' . $totalPages . '</a></li>';
    }

    if ($currentPage < $totalPages) {
        $paginationLinks .= '<li class="page-item"><a class="page-link" href="?page=' . ($currentPage + 1) . '">Next</a></li>';
    }

    return $paginationLinks;
}
?>
