<?php
include 'db_connect.php';

// Get the user ID from the request
$userID = $_GET['userID']; // Use userID instead of user_id

// Query to fetch user details
$sql = "SELECT * FROM user WHERE userID='$userID'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode([
        "status" => "success",
        "message" => "User details fetched successfully",
        "user" => $row
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "User not found"
    ]);
}

$conn->close();
?>