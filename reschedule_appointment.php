<?php
include('db_connection.php');

if (isset($_POST['appointment_id']) && isset($_POST['new_appointment_date']) && isset($_POST['remark'])) {
    $appointmentId = $_POST['appointment_id'];
    $newDate = $_POST['new_appointment_date'];
    $remark = $_POST['remark'];

  
    $query = "UPDATE appointments SET appointment_date = ?, remark = ? WHERE appointment_id = ?";
    $stmt = $conn->prepare($query);
   
    $stmt->bind_param("ssi", $newDate, $remark, $appointmentId);

    if ($stmt->execute()) {
        echo "Appointment rescheduled successfully.";
    } else {
        echo "Error rescheduling appointment.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Missing information.";
}
?>
