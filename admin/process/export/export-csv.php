<?php

include '../../../includes/database.php';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=visitor_records.csv');

$output = fopen('php://output', 'w');

fputcsv($output, [
    'Name',
    'Date',
    'Time In',
    'Time Out',
    'Age',
    'Sex',
    'Duration',
    'Code'
]);

$GetDataQuery = "
    SELECT 
        CONCAT(UPPER(v.first_name), ' ', UPPER(v.middle_name), ' ', UPPER(v.last_name)) AS name,
        DATE_FORMAT(t.time_in, '%Y-%m-%d') AS date,
        TIME_FORMAT(t.time_in, '%H:%i:%s') AS time_in,
        TIME_FORMAT(t.time_out, '%H:%i:%s') AS time_out,
        v.age,
        s.sex_name AS sex,
        TIMESTAMPDIFF(SECOND, t.time_in, t.time_out) AS duration_seconds,
        t.code
    FROM visitors v
    INNER JOIN time_logs t ON v.id = t.client_id
    INNER JOIN sex s ON v.sex_id = s.id";

$result = $conn->query($GetDataQuery);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $duration = isset($row['duration_seconds']) ? gmdate('H:i:s', $row['duration_seconds']) : '-';
        

        fputcsv($output, [
            $row['name'] ? $row['name'] : '-',
            $row['date'] ? $row['date'] : '-',
            $row['time_in'] ? $row['time_in'] : '-',
            $row['time_out'] ? $row['time_out'] : '-',
            $row['age'] ? $row['age'] : '-',
            $row['sex'] ? $row['sex'] : '-',
            $duration ? $duration : '-',
            $row['code'] ? $row['code'] : '-'
        ]);
    }
}
fclose($output);
exit;
?>
