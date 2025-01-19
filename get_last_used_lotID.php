<?php
header('Content-Type: application/json');

// Include the database connection file
include 'db_connect.php';

// Check if `userID` is provided in the request
if (!isset($_GET['userID'])) {
    echo json_encode(["status" => "error", "message" => "User ID is required"]);
    exit;
}

$userID = $_GET['userID'];

try {
    // Prepare and execute the query to fetch the last_used_lotID based on user ID
    $stmt = $conn->prepare("SELECT last_used_lotID FROM user WHERE userID = ?");
    $stmt->bind_param("s", $userID); // "s" indicates the parameter type (string)
    $stmt->execute();
    
    // Fetch the result
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Check if a result was found
    if ($row && $row['last_used_lotID']) {
        echo json_encode(["status" => "success", "data" => ["last_used_lotID" => $row['last_used_lotID']]]);
    } else {
        echo json_encode(["status" => "error", "message" => "Last used lotID not found"]);
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    // Return an error message if an exception occurs
    echo json_encode(["status" => "error", "message" => "Error: " . $e->getMessage()]);
}
?>