<?php
include_once "db_connection.php";
if (isset($_POST['appointmenttype']) && !empty($_POST['appointmentprice'])) {
    $appointmentId = $conn->real_escape_string($_POST['appointmentId']);
    $appointmentprice = $conn->real_escape_string($_POST['appointmentprice']);
    $appointmenttype = $conn->real_escape_string($_POST['appointmenttype']);

   //`app_id`, `appoinment_type`, `price`
   $stmt = $conn->prepare("UPDATE appoinment_type SET appoinment_type = ?, price = ? WHERE app_id = ?");


    $stmt->bind_param("ssi", $appointmenttype , $appointmentprice  ,$appointmentId );

    if ($stmt->execute()) {
        echo json_encode(['success' => 'Appointment Type updated successfully.']);
    } else {
        echo json_encode(['error' => 'Failed to update Appointment.']);
    }
} else {
    echo json_encode(['error' => 'Required fields are missing.']);
}
