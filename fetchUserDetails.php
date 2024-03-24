<?php
// fetchUserDetails.php

require_once 'UserManager.php';
require_once 'dbConnection.php'; // Assume this file returns a database connection $db

session_start();

// Example user ID from session
$userId = $_SESSION['user_id'] ?? 0; // Replace with actual session or request mechanism

$userManager = new UserManager($db);
$userDetails = $userManager->getUserDetails($userId);

if ($userDetails) {
    echo "<p>Username: " . htmlspecialchars($userDetails['username']) . "</p>";
    echo "<p>Full Name: " . htmlspecialchars($userDetails['full_name']) . "</p>";
    echo "<p>Email: " . htmlspecialchars($userDetails['email']) . "</p>";
    echo "<p>Phone Number: " . htmlspecialchars($userDetails['phone_number']) . "</p>";
    echo "<p>Date of Birth: " . htmlspecialchars($userDetails['date_of_birth']) . "</p>";
} else {
    echo "<p>User details not found.</p>";
}
?>
