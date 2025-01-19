<?php
header('Content-Type: application/json');

// Include the database connection file
include 'db_connect.php';

// Check if `userID` and `last_used_lotID` are provided in the request
if (!isset($_POST['userID']) || !isset($_POST['last_used_lotID'])) {
    echo json_encode(["status" => "error", "message" => "User ID and last_used_lotID are required"]);
    exit;
}

$userID = $_POST['userID'];
$last_used_lotID = $_POST['last_used_lotID'];

try {
    // Prepare and execute the query to update the last_used_lotID for the user
    $stmt = $conn->prepare("UPDATE user SET last_used_lotID = ? WHERE userID = ?");
    $stmt->bind_param("ss", $last_used_lotID, $userID); // "ss" indicates two string parameters
    $stmt->execute();

    // Check if the update was successful
    if ($stmt->affected_rows > 0) {
        echo json_encode(["status" => "success", "message" => "Last used lotID updated successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "No rows updated"]);
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    // Return an error message if an exception occurs
    echo json_encode(["status" => "error", "message" => "Error: " . $e->getMessage()]);
}
?>