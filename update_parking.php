<?php
// Include the database connection file
include 'db_connect.php';

// Get parameters from the URL
$parkingSpaceID = $_GET['parkingSpaceID'];
$isAvailable = $_GET['isAvailable'];

// Ensure parkingSpaceID and isAvailable are properly sanitized
$parkingSpaceID = $conn->real_escape_string($parkingSpaceID);
$isAvailable = (int)$isAvailable; // Force to integer for security

// Update the database
$sql = "UPDATE parkingspace SET isAvailable = $isAvailable WHERE parkingSpaceID = '$parkingSpaceID'";
if ($conn->query($sql) === TRUE) {
    echo "Record updated successfully";
} else {
    echo "Error updating record: " . $conn->error;
}

// Close connection
$conn->close();
?>
