<?php

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Schedule Appointment</title>
    
</head>
<body>
    <h1>Schedule Your Appointment</h1>
    <form action="appointment_handler.php" method="post">
        <label for="appointment_date">Appointment Date:</label>
        <input type="datetime-local" id="appointment_date" name="appointment_date" required>
        
        <label for="appointment_type">Appointment Type:</label>
        <select id="appointment_type" name="appointment_type" required>
            <option value="blood_test">Blood Test</option>
            <option value="x_ray">X-Ray</option>
            <option value="MRI">MRI</option>
          
        </select>
        
        <button type="submit" name="schedule_appointment">Schedule Appointment</button>
    </form>
</body>
</html>
