<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['userId'])) {
        $userID = $_GET['userId'];

        // Query to check for active premium parking sessions
        $query = "SELECT 
            pp.*,
            ps.parkingSpaceID,
            TIMESTAMPDIFF(SECOND, NOW(), pp.end_time) as remaining_seconds
        FROM premium_parking pp
        JOIN parkingspace ps ON pp.parking_space_id = ps.parkingSpaceID
        WHERE pp.user_id = ? 
        AND pp.status = 'active'
        AND pp.end_time > NOW()
        LIMIT 1";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $userID);
        
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                echo json_encode([
                    'status' => 'success',
                    'data' => [
                        'parking_space_id' => $row['parkingSpaceID'],
                        'end_time' => $row['end_time'],
                        'remaining_seconds' => max(0, $row['remaining_seconds'])
                    ]
                ]);
            } else {
                echo json_encode([
                    'status' => 'success',
                    'data' => null
                ]);
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to check user premium parking status'
            ]);
        }

        $stmt->close();
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Missing user ID'
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