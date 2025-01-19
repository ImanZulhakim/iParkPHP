<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db_connect.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['parkingSpaceID']) && isset($data['isAvailable'])) {
        $parkingSpaceID = $data['parkingSpaceID'];
        $isAvailable = $data['isAvailable'];

        $query = "UPDATE parkingspace SET isAvailable = ? WHERE parkingSpaceID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("is", $isAvailable, $parkingSpaceID);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Parking space updated successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update parking space.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}

$conn->close();
?>
