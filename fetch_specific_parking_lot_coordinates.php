<?php
header('Content-Type: application/json');

// Include the database connection file
include 'db_connect.php';

try {
    // Check if lotID is provided
    if (!isset($_GET['lotID'])) {
        echo json_encode([
            "status" => "error", 
            "message" => "lotID parameter is required"
        ]);
        exit;
    }

    $lotID = $_GET['lotID'];

    // Fetch parking lot data for the specified lotID
    $stmt = $conn->prepare("SELECT coordinates FROM parkinglot WHERE lotID = ?");
    $stmt->bind_param("s", $lotID);
    $stmt->execute();
    
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo json_encode([
            "status" => "success", 
            "coordinates" => $row['coordinates']
        ]);
    } else {
        echo json_encode([
            "status" => "error", 
            "message" => "Parking lot not found"
        ]);
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    echo json_encode([
        "status" => "error", 
        "message" => "Error: " . $e->getMessage()
    ]);
}
?>