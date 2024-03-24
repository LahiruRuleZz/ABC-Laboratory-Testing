<?php
include 'db_connection.php'; // Ensure you have the correct path

if (isset($_POST['report_id'], $_POST['report_name'], $_POST['report_date']) && isset($_FILES['report_file'])) {
    $reportId = $_POST['report_id'];
    $reportName = $_POST['report_name'];
    $reportDate = $_POST['report_date'];

    // Handle file upload
    $reportFilePath = 'uploads/reports/' . basename($_FILES['report_file']['name']);
    if (move_uploaded_file($_FILES['report_file']['tmp_name'], $reportFilePath)) {
        // File uploaded successfully
    } else {
        echo "Error uploading file.";
        exit;
    }

    // Update report details in database
    // Note: Ensure you use prepared statements to prevent SQL injection
    $sql = "UPDATE test_reports SET report_name=?, report_date=?, report_file_path=? WHERE report_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $reportName, $reportDate, $reportFilePath, $reportId);

    if ($stmt->execute()) {
        echo "Report updated successfully.";
    } else {
        echo "Error updating report.";
    }

    $stmt->close();
} else {
    echo "Required fields are missing.";
}

$conn->close();
?>
