<?php
session_start();
include('db_connection.php');

if (!isset($_POST['appointmentIds'])) {
    echo 'No appointments provided';
    exit; 
}

$appointmentIds = $_POST['appointmentIds'];

$conn->begin_transaction();

try {
 
    $stmt = $conn->prepare("UPDATE appointments SET status = 'paid' WHERE appointment_id = ?");

    foreach ($appointmentIds as $appointmentId) {
        $stmt->bind_param('i', $appointmentId);
        $stmt->execute();
    }

    $conn->commit();
    echo "Appointments updated successfully.";
} catch (Exception $e) {
    $conn->rollback();
    echo "Error: " . $e->getMessage();
}

$conn->close();
