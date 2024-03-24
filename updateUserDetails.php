<?php
include_once "db_connection.php";
if (isset($_POST['user_id']) && !empty($_POST['username'])) {
    $user_id = $conn->real_escape_string($_POST['user_id']);
    $username = $conn->real_escape_string($_POST['username']);
    $password = isset($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;
    $user_type = isset($_POST['user_type']) ? $conn->real_escape_string($_POST['user_type']) : '';
    $full_name = isset($_POST['full_name']) ? $conn->real_escape_string($_POST['full_name']) : '';
    $email = isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : '';
    $phone_number = isset($_POST['phone_number']) ? $conn->real_escape_string($_POST['phone_number']) : '';
    $date_of_birth = isset($_POST['date_of_birth']) ? $conn->real_escape_string($_POST['date_of_birth']) : null;
   
    $stmt = $conn->prepare("UPDATE users SET username = ?, password = ?, user_type = ?, full_name = ?, email = ?, phone_number = ?, date_of_birth = ? WHERE user_id = ?");

    $stmt->bind_param("sssssssi", $username, $password, $user_type, $full_name, $email, $phone_number, $date_of_birth, $user_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => 'User updated successfully.']);
    } else {
        echo json_encode(['error' => 'Failed to update user.']);
    }
} else {
    echo json_encode(['error' => 'Required fields are missing.']);
}
