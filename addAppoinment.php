<?php
include_once "db_connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $appointment_type = $_POST["appointment_type"];
    $price = $_POST["price"];

    //`app_id`, `appoinment_type`, `price`
    $sql = "INSERT INTO appoinment_type (appoinment_type, price) 
            VALUES ('$appointment_type', '$price')";

    if (mysqli_query($conn, $sql)) {
        $response["success"] = true;
        $response["message"] = "appoinment type added successfully!";
    } else {
        $response["success"] = false;
        $response["message"] = "Failed to add appoinment type. Please try again.";
    }

    mysqli_close($conn);
    header("Content-Type: application/json");
    echo json_encode($response);
} else {
    header("Location: test.php");
    exit();
}
?>
