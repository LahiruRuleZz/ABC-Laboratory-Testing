<?php

include_once "db_connection.php";


$app_id = $_GET['app_id'];

$sql = "SELECT * FROM appoinment_type WHERE app_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $app_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {

    echo json_encode([
        'appointment_type' => $row['appoinment_type'],
        'price' => $row['price'],
        'app_id' => $row['app_id']
    ]);
} else {
    echo json_encode(['error' => 'User not found.']);
}


// echo json_encode(['appointment_type' => 'Test Type', 'price' => '123']);
// exit;
