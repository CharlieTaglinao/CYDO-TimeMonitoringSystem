<?php
session_start();
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$pin = $data['pin'];

if (isset($_SESSION['verification_pin'], $_SESSION['verification_pin_expiration'])) {
    if (time() > $_SESSION['verification_pin_expiration']) {
        unset($_SESSION['verification_pin'], $_SESSION['verification_pin_expiration']); // Clear expired PIN
        echo json_encode(['success' => false, 'message' => 'PIN has expired.']);
    } elseif ($_SESSION['verification_pin'] == $pin) {
        unset($_SESSION['verification_pin'], $_SESSION['verification_pin_expiration']); // Clear PIN after successful verification
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid PIN.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No PIN found.']);
}
?>
