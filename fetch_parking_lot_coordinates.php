 <?php
header('Content-Type: application/json');

// Include the database connection file
include 'db_connect.php';

try {
    // Fetch all parking lot data in one query
    $stmt = $conn->prepare("SELECT lot_name, coordinates FROM parkinglot");
    $stmt->execute();
    
    $result = $stmt->get_result();
    $parkingLots = [];
    
    while ($row = $result->fetch_assoc()) {
        $parkingLots[$row['lot_name']] = [
            'coordinates' => $row['coordinates']
        ];
    }

    if (!empty($parkingLots)) {
        echo json_encode([
            "status" => "success", 
            "data" => $parkingLots
        ]);
    } else {
        echo json_encode([
            "status" => "error", 
            "message" => "No parking lots found"
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
