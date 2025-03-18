<?php
include '../../../includes/database.php';
include '../../../includes/phpqrcode/qrlib.php'; 

$visitorCode = isset($_GET['visitor_code']) ? $_GET['visitor_code'] : '';

if ($visitorCode) {
    $stmt = $conn->prepare("SELECT
                                v.first_name,
                                v.middle_name,
                                v.last_name,
                                v.sex_id,
                                v.age,
                                t.time_in,
                                t.time_out,
                                t.code
                            FROM visitors v
                            INNER JOIN time_logs t
                            ON v.id = t.client_id
                            WHERE t.code = ?");
    $stmt->bind_param("s", $visitorCode);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $visitor = $result->fetch_assoc();
    } else {
        die("Visitor not found");
    }
} else {
    die("Visitor code not provided");
}


$tempDir = '../../../qrcodes/';
if (!is_dir($tempDir)) {
    mkdir($tempDir, 0755, true); 
}
$qrFilePath = $tempDir . $visitor['code'] . '.png';
QRcode::png($visitor['code'], $qrFilePath, QR_ECLEVEL_L, 4);

?>