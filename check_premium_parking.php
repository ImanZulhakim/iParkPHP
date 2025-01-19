<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['spaceId'])) {
        $parkingSpaceID = $_GET['spaceId'];

        // Query to get active premium parking session
        $query = "SELECT 
            pp.*,
            u.username,
            TIMESTAMPDIFF(SECOND, NOW(), pp.end_time) as remaining_seconds
        FROM premium_parking pp
        JOIN user u ON pp.user_id = u.userID
        WHERE pp.parking_space_id = ? 
        AND pp.status = 'active'
        AND pp.end_time > NOW()
        ORDER BY pp.created_at DESC
        LIMIT 1";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $parkingSpaceID);
        
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                echo json_encode([
                    'status' => 'success',
                    'data' => [
                        'premium_id' => $row['premium_id'],
                        'parking_space_id' => $row['parking_space_id'],
                        'user_id' => $row['user_id'],
                        'username' => $row['username'],
                        'start_time' => $row['start_time'],
                        'end_time' => $row['end_time'],
                        'remaining_seconds' => max(0, $row['remaining_seconds']),
                        'status' => $row['status'],
                        'payment_status' => $row['payment_status']
                    ]
                ]);
            } else {
                // No active premium parking found
                echo json_encode([
                    'status' => 'success',
                    'data' => null
                ]);
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to check premium parking status'
            ]);
        }

        $stmt->close();
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Missing parking space ID'
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