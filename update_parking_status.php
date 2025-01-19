<?php
// Include the existing db_connect.php for database connection
include 'db_connect.php';

// Get parameters from the ESP8266 HTTP request
$parkingSpaceID = $_GET['parkingSpaceID'];
$isAvailable = $_GET['isAvailable'];

// Prepare the SQL query to update the isAvailable field
$sql = "UPDATE parkingspace SET isAvailable='$isAvailable' WHERE parkingSpaceID='$parkingSpaceID'";

// Execute the query and check for success
if ($conn->query($sql) === TRUE) {
  echo "Record updated successfully";
} else {
  echo "Error updating record: " . $conn->error;
}

// Close the database connection
$conn->close();
?>
