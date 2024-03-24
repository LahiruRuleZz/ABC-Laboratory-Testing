<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include_once "db_connection.php"; 

   
    $patient_id = $_POST['patient_id'];
    $report_name = $_POST['report_name'];
    $report_date = $_POST['report_date'];
    $report_file = $_FILES['report_file'];

   
    $uploadDir = "uploads/reports/";
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = time() . basename($report_file["name"]);
    $uploadFilePath = $uploadDir . $fileName;

  
    if (move_uploaded_file($report_file["tmp_name"], $uploadFilePath)) {
        // Insert report details into the database
        $query = "INSERT INTO test_reports (patient_id, report_name, report_file_path, report_date) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("isss", $patient_id, $report_name, $uploadFilePath, $report_date);
        
        if ($stmt->execute()) {
            echo "The report has been uploaded successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "There was an error uploading the file.";
    }
}
?>
