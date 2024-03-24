<?php
include_once "db_connection.php";
header('Content-Type: application/json');

if (isset($_POST['user_id'])) {
    $user_id = $conn->real_escape_string($_POST['user_id']);
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => 'User deleted successfully.']);
    } else {
        echo json_encode(['error' => 'Failed to delete user.']);
    }
    $stmt->close();
} else {
    echo json_encode(['error' => 'User ID not provided.']);
}

$conn->close();
?>
