<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['parkingSpaceID']) && isset($data['userID'])) {
        $parkingSpaceID = $data['parkingSpaceID'];
        $userID = $data['userID'];
        $startTime = date('Y-m-d H:i:s');
        $endTime = date('Y-m-d H:i:s', strtotime('+5 minutes'));

        // Begin transaction
        $conn->begin_transaction();

        try {
            // Insert into premium_parking table
            $query1 = "INSERT INTO premium_parking (parking_space_id, user_id, start_time, end_time) VALUES (?, ?, ?, ?)";
            $stmt1 = $conn->prepare($query1);
            $stmt1->bind_param("ssss", $parkingSpaceID, $userID, $startTime, $endTime);
            $stmt1->execute();

            // Update parking space availability
            $query2 = "UPDATE parkingspace SET isAvailable = 0 WHERE parkingSpaceID = ?";
            $stmt2 = $conn->prepare($query2);
            $stmt2->bind_param("s", $parkingSpaceID);
            $stmt2->execute();

            $conn->commit();
            echo json_encode([
                'status' => 'success',
                'message' => 'Premium parking created successfully'
            ]);
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode([
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }

        $stmt1->close();
        $stmt2->close();
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Missing required parameters'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
}

$conn->close();
?>