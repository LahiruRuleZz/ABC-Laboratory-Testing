<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

include_once "db_connection.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_POST['patient_id'], $_POST['amount'], $_POST['payment_method'], $_POST['payment_status'])) {
    echo 'Missing data';
    exit;
}

try {
    
    $stmt = $conn->prepare("INSERT INTO payments (patient_id, amount, payment_date, payment_method, payment_status) VALUES (?, ?, NOW(), ?, ?)");

    if (false === $stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }


    $bind = $stmt->bind_param(
        'idss',
        $_POST['patient_id'],
        $_POST['amount'],
        $_POST['payment_method'],
        $_POST['payment_status']
    );


    if (false === $bind) {
        throw new Exception('Bind failed: ' . $stmt->error);
    }

    $exec = $stmt->execute();

    if (false === $exec) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }

    if ($stmt->affected_rows > 0) {
        echo "Payment information saved successfully.";
    } else {
        echo "No payment information was saved. Please check your input.";
    }

    $stmt->close();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}



$user_id = $_SESSION['user_id'] ?? 0;

$stmt = $conn->prepare("SELECT email, full_name FROM users WHERE user_id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$userInfo = $result->fetch_assoc();

if (!$userInfo) {
    echo "User not found.";
    exit;
}

$patientEmail = $userInfo['email'];
$patientName = $userInfo['full_name'];

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'path/to/PHPMailer/src/Exception.php';
require 'path/to/PHPMailer/src/PHPMailer.php';
require 'path/to/PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = ''; // Your full Gmail address
    $mail->Password = '1234567890'; // Your Gmail password or App Password if 2FA is enabled
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Encryption
    $mail->Port = 587; // TCP port to connect to

    // Recipients
    $mail->setFrom('your_gmail_address@gmail.com', 'test'); //set your Gmail address as the sender
    $mail->addAddress($patientEmail, $patientName);

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Payment Confirmation';
    $mail->Body    = 'Your payment has been processed successfully.';

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}


$conn->close();
