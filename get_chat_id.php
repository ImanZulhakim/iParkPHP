<?php
header('Content-Type: application/json');

// Include the database connection file
include 'db_connect.php';

// Check if `userID` is provided in the request
if (!isset($_POST['userID'])) {
    echo json_encode(["status" => "error", "message" => "User ID is required"]);
    exit;
}

$userID = $_POST['userID'];

try {
    // Prepare and execute the query to fetch the chat ID based on user ID
    $stmt = $conn->prepare("SELECT chatID FROM user WHERE userID = ?");
    $stmt->bind_param("s", $userID); // "s" indicates the parameter type (string)
    $stmt->execute();
    
    // Fetch the result
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Check if a result was found
    if ($row && $row['chatID']) {
        echo json_encode(["status" => "success", "chatID" => $row['chatID']]);
    } else {
        echo json_encode(["status" => "error", "message" => "Chat ID not found"]);
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    // Return an error message if an exception occurs
    echo json_encode(["status" => "error", "message" => "Error: " . $e->getMessage()]);
}
?>
