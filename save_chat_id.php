<?php
// Include the database connection file
include 'db_connect.php';

// Retrieve userID and chatID from the POST request
$userID = isset($_POST['userID']) ? $_POST['userID'] : '';
$chatID = isset($_POST['chatID']) ? $_POST['chatID'] : '';

if ($userID && $chatID) {
    // Prepare the SQL statement with placeholders
    $stmt = $conn->prepare("UPDATE user SET chatID = ? WHERE userID = ?");
    
    // Bind parameters to the statement
    $stmt->bind_param("ss", $chatID, $userID); // "ss" indicates two strings
    
    // Execute the statement and check if successful
    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Chat ID saved successfully.'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to save Chat ID.'
        ]);
    }
    
    // Close the statement
    $stmt->close();
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'User ID and Chat ID are required.'
    ]);
}

// Close the database connection
$conn->close();
?>
