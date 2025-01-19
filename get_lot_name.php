<?php
header('Content-Type: application/json');

// Include the database connection file
include 'db_connect.php';

// Check if `lotID` is provided in the request
if (!isset($_GET['lotID'])) {
    echo json_encode(["status" => "error", "message" => "Lot ID is required"]);
    exit;
}

$lotID = $_GET['lotID'];

try {
    // Prepare and execute the query to fetch the lot name based on lotID
    $stmt = $conn->prepare("SELECT lot_name FROM parkinglot WHERE lotID = ?");
    $stmt->bind_param("s", $lotID); // "s" indicates the parameter type (string)
    $stmt->execute();
    
    // Fetch the result
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Check if a result was found
    if ($row && $row['lot_name']) {
        echo json_encode(["status" => "success", "data" => ["lot_name" => $row['lot_name']]]);
    } else {
        echo json_encode(["status" => "error", "message" => "Lot name not found"]);
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    // Return an error message if an exception occurs
    echo json_encode(["status" => "error", "message" => "Error: " . $e->getMessage()]);
}
?>