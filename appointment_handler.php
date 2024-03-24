<?php
include('db_connection.php');

session_start();

if (!isset($_SESSION['user_id'])) {
    
    exit('Access Denied');
}

if (isset($_POST['action'])) {
    $action = $_POST['action'];

    switch ($action) {
        case 'schedule':
            scheduleAppointment($conn);
            break;
        case 'cancel':
            cancelAppointment($conn);
            break;
        case 'reschedule':
            rescheduleAppointment($conn);
            break;
        default:
            echo "Invalid action.";
    }
} else {
    echo "No action specified.";
}
function scheduleAppointment($conn) {
    // Extract and validate form data
    $patient_id = $_SESSION['user_id'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_type = $_POST['appointment_type'];

   
    $stmt = $conn->prepare("INSERT INTO appointments (patient_id, appointment_date ,app_id) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $patient_id, $appointment_date , $appointment_type);

    if ($stmt->execute()) {
        
        
        echo "<script type='text/javascript'>alert('Appointment scheduled successfully'); window.location.href='pwelcome.php';</script>";
    } else {
        echo "Error scheduling appointment.";
    }

    $stmt->close();
}
function cancelAppointment($conn) {
   
}

function rescheduleAppointment($conn) {
    
}
