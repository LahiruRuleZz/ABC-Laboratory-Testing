<?php

include 'db_connection.php';

header('Content-Type: application/json');

if(isset($_GET['user_id']) && !empty($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']);

    $sql = "SELECT report_id, patient_id, report_name, report_file_path, report_date, created_at FROM test_reports WHERE patient_id = ?";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Statement preparation failed: ' . $conn->error]);
        exit;
    }

    $stmt->bind_param("i", $user_id);

    $stmt->execute();

    $stmt->bind_result($report_id, $patient_id, $report_name, $report_file_path, $report_date, $created_at);

    if ($stmt->fetch()) {

        $reportDetails = [
            'report_id' => $report_id,
            'patient_id' => $patient_id,
            'report_name' => $report_name,
            'report_file_path' => $report_file_path,
            'report_date' => $report_date,
            'created_at' => $created_at
        ];
   
        echo json_encode(['success' => true, 'data' => $reportDetails]);
    } else {

        echo json_encode(['success' => false, 'message' => 'No report found for the given user ID']);
    }


    $stmt->close();
} else {
 
    echo json_encode(['success' => false, 'message' => 'No user ID provided']);
}

// Close database connection
$conn->close();
?>
