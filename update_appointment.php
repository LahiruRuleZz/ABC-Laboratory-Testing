<?php
include('db_connection.php');


$appointmentId = $_POST['appointmentId'];
$newDate = $_POST['appointmentDate'];
$newType = $_POST['appointmentType'];


$stmt = $conn->prepare("UPDATE appointments SET appointment_date = ?, appointment_type = ? WHERE appointment_id = ?");

$stmt->bind_param("ssi", $newDate, $newType, $appointmentId);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo "Appointment updated successfully.";
    } else {
        echo "No changes were made to the appointment."; 
    }
} else {
    echo "Error updating appointment: " . $stmt->error;
}

$stmt->close();

$conn->close();
