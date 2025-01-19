<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db_connect.php';

if (isset($_GET['lotID'])) {
    $lotID = $_GET['lotID'];

    try {
        // Query to fetch locationType based on lotID
        $sql = "SELECT locationType FROM parkinglot WHERE lotID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $lotID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo json_encode([
                'status' => 'success',
                'data' => [
                    'locationType' => $row['locationType']
                ]
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Lot ID not found',
                'data' => [
                    'locationType' => 'indoor' // Default fallback
                ]
            ]);
        }

        $stmt->close();
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Database error: ' . $e->getMessage(),
            'data' => [
                'locationType' => 'indoor' // Default fallback
            ]
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'No lotID parameter provided',
        'data' => [
            'locationType' => 'indoor' // Default fallback
        ]
    ]);
}

$conn->close();
?>
