<?php
include('db_connection.php'); 

if (isset($_POST['appointment_id'])) {
    $appointmentId = $_POST['appointment_id'];

    
    $query = "UPDATE appointments SET status = 'Confirm' WHERE appointment_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $appointmentId);

    if ($stmt->execute()) {
        echo "Appointment Confirm successfully.";
    } else {
        echo "Error Confirm appointment.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "No appointment ID provided.";
}
?>