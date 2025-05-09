<?php
include '../includes/database.php';

$recordsPerPage = 7;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $recordsPerPage;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $startDate = $_POST['startDate'] ?? '';
    $endDate = $_POST['endDate'] ?? '';
    $all = $_POST['all'] ?? '';

    if (!empty($startDate) && !empty($endDate)) {
        $stmt = $conn->prepare("
            SELECT DISTINCT
                visitors.first_name, 
                visitors.middle_name, 
                visitors.last_name,
                visitors.age, 
                visitors.sex_id, 
                sex.sex_name,
                visitor_school_name.school_name,
                time_logs.time_in, 
                time_logs.time_out, 
                time_logs.code, 
                purpose.purpose,
                time_logs.status
            FROM visitors
                INNER JOIN time_logs ON visitors.id = time_logs.client_id
                INNER JOIN sex ON visitors.sex_id = sex.id
                INNER JOIN visitor_school_name ON visitors.school_id = visitor_school_name.id
                INNER JOIN (
                    SELECT client_id, MAX(id) as latest_purpose_id 
                    FROM purpose 
                    GROUP BY client_id
                ) as latest_purposes ON visitors.id = latest_purposes.client_id
                INNER JOIN purpose ON latest_purposes.latest_purpose_id = purpose.id 
            WHERE DATE(time_logs.time_in) BETWEEN ? AND ?
            ORDER BY time_logs.time_in DESC
        ");
        $stmt->bind_param('ss', $startDate, $endDate);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
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
                    <td>" . htmlspecialchars($row['school_name']) . "</td>
                    <td>" . $row['status'] . "</td>
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
            echo "<tr><td colspan='8'>No records found</td></tr>";
        }
    } else {
        echo "<tr><td colspan='8'>Invalid date range</td></tr>";
    }
}

if (!empty($_GET['startDate']) && !empty($_GET['endDate'])) {
    $startDate = $_GET['startDate'];
    $endDate = $_GET['endDate'];
    $search = $_GET['search'] ?? '';

    // Fetch paginated records with search and date range
    $query = "
        SELECT DISTINCT
            visitors.first_name, 
            visitors.middle_name, 
            visitors.last_name,
            visitors.age, 
            visitors.sex_id, 
            sex.sex_name,
            visitor_school_name.school_name,
            time_logs.time_in, 
            time_logs.time_out, 
            time_logs.code, 
            purpose.purpose,
            time_logs.status
        FROM visitors
        INNER JOIN time_logs ON visitors.id = time_logs.client_id
        INNER JOIN sex ON visitors.sex_id = sex.id
        INNER JOIN visitor_school_name ON visitors.school_id = visitor_school_name.id
        INNER JOIN (
            SELECT client_id, MAX(id) as latest_purpose_id 
            FROM purpose 
            GROUP BY client_id
        ) as latest_purposes ON visitors.id = latest_purposes.client_id
        INNER JOIN purpose ON latest_purposes.latest_purpose_id = purpose.id 
        WHERE DATE(time_logs.time_in) BETWEEN '$startDate' AND '$endDate'
        AND (visitors.first_name LIKE '%$search%' OR visitors.middle_name LIKE '%$search%' OR visitors.last_name LIKE '%$search%')
        ORDER BY time_logs.time_in DESC
        LIMIT $recordsPerPage OFFSET $offset
    ";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . strtoupper($row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name']) . "</td>";
            echo "<td>" . (isset($row['time_in']) ? date('Y-m-d', strtotime($row['time_in'])) : '-') . "</td>";
            echo "<td>" . (isset($row['time_in']) ? date('H:i:s', strtotime($row['time_in'])) : '-') . "</td>";
            echo "<td>" . (isset($row['time_out']) ? date('H:i:s', strtotime($row['time_out'])) : '-') . "</td>";
            echo "<td>";
            if (isset($row['time_in'], $row['time_out'])) {
                $timeIn = new DateTime($row['time_in']);
                $timeOut = new DateTime($row['time_out']);
                $interval = $timeIn->diff($timeOut);
                echo $interval->format('%h hours %i minutes %s seconds');
            } else {
                echo '-';
            }
             echo "</td>
                <td>" . htmlspecialchars($row['school_name']) . "</td>
                <td>" . htmlspecialchars($row['status']) . "</td>
                <td>
                   <button class='btn btn-success view-details'
                        data-name='" . strtoupper($row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name']) . "' 
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
                        <button type='submit' class='btn btn-danger'>Time Out</button>
                      </form>";
            }

            echo "</td>
            </tr>";
        }
    } else {
        echo "<tr><td colspan='8'>No records found for the selected date range and search criteria.</td></tr>";
    }
}
?>