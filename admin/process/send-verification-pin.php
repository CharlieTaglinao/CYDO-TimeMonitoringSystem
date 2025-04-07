<?php
// Include necessary files and start session
include '../../includes/database.php';
require '../../vendor/autoload.php'; // Ensure PHPMailer and Dotenv are installed via Composer
session_start();
header('Content-Type: application/json');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

// Decode and validate JSON input
$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['id']) || !is_numeric($data['id'])) {
    respondWithJson(false, 'Invalid account ID.');
    exit();
}

$id = intval($data['id']);

// Generate and store a random 6-digit PIN
$pin = random_int(100000, 999999);
$_SESSION['verification_pin'] = $pin;

// Fetch the user's email address
$email = fetchUserEmail($id, $conn);
if (!$email) {
    respondWithJson(false, 'Account not found.');
    exit();
}

// Validate SMTP configuration
$fromEmail = $_ENV['SMTP_FROM_EMAIL'] ?? '';
$fromName = $_ENV['SMTP_FROM_NAME'] ?? '';
if (empty($fromEmail)) {
    respondWithJson(false, 'SMTP_FROM_EMAIL is not set.');
    exit();
}

// Send the verification PIN via email
if (sendVerificationEmail($email, $pin, $fromEmail, $fromName)) {
    respondWithJson(true, 'Verification PIN sent successfully.');
} else {
    respondWithJson(false, 'Failed to send verification PIN.');
}

// Function to fetch the user's email address
function fetchUserEmail($id, $conn) {
    $query = "SELECT ae.email_address 
              FROM account a 
              INNER JOIN account_email ae ON a.email_id = ae.id 
              WHERE a.id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    return $user['email_address'] ?? null;
}

// Function to send the verification email
function sendVerificationEmail($email, $pin, $fromEmail, $fromName) {
    $mail = new PHPMailer(true);
    try {
        // Configure SMTP settings
        $mail->isSMTP();
        $mail->Host = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['SMTP_USERNAME'];
        $mail->Password = $_ENV['SMTP_PASSWORD'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $_ENV['SMTP_PORT'];

        // Set email details
        $mail->setFrom($fromEmail, $fromName);
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = "Verification PIN for editing details account";

        // Professional email body
        $mail->Body = "
            <div style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
                <h2 style='color: #4CAF50;'>Verification PIN</h2>
                <p>Hi,</p>
                <p>We received a request to verify your account. Please use the following PIN to complete the verification process:</p>
                <p style='font-size: 18px; font-weight: bold; color: #000;'>$pin</p>
                <p>If you did not request this, please ignore this email or contact support if you have concerns.</p>
                <hr style='border: none; border-top: 1px solid #ddd; margin: 20px 0;'>
                <p style='font-size: 12px; color: #555;'>This is an automated message. Please do not reply to this email.</p>
                <p style='font-size: 12px; color: #555;'>© " . date('Y') . " CYDO | All rights reserved.</p>
            </div>
        ";

        // Send the email
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Email error: ' . $mail->ErrorInfo);
        return false;
    }
}

// Function to respond with JSON
function respondWithJson($success, $message) {
    echo json_encode(['success' => $success, 'message' => $message]);
}
?>
