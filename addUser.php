<?php
include_once "db_connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $userType = $_POST["user_type"];
    $fullName = $_POST["full_name"];
    $email = $_POST["email"]; 
    $phoneNumber = $_POST["phone_number"]; 
    $dateOfBirth = $_POST["date_of_birth"];

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (username, password, user_type, full_name, email, phone_number, date_of_birth) 
            VALUES ('$username', '$hashed_password', '$userType', '$fullName', '$email', '$phoneNumber', '$dateOfBirth')";

    if (mysqli_query($conn, $sql)) {
        $response["success"] = true;
        $response["message"] = "User added successfully!";
    } else {
        $response["success"] = false;
        $response["message"] = "Failed to add user. Please try again.";
    }

    mysqli_close($conn);
    header("Content-Type: application/json");
    echo json_encode($response);
} else {
    header("Location: usermanage.php");
    exit();
}
?>
