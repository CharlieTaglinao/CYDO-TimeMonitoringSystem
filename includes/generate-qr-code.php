<?php
session_start();
include 'phpqrcode/qrlib.php';

if (!isset($_SESSION['randomCode'])) {
    $_SESSION['message'] = "QR code generation failed.";
    $_SESSION['message_type'] = 'danger';
    header("Location: ../index.php");
    exit();
}

$text = $_SESSION['randomCode'];

header('Content-Type: image/png');
QRcode::png($text, null, QR_ECLEVEL_H, 8, 2);

?>
