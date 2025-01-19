<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db_connect.php';

// Existing function to fetch user, vehicle, and parking preferences data based on user ID
function fetchUserData($conn, $userID) {
    $sql = "
        SELECT 
            u.userID, u.email, u.username, u.userType, u.gender, u.hasDisability,
            v.vehicleID, v.brand, v.type, v.category,
            p.isNearest, p.isCovered, p.requiresLargeSpace, p.requiresWellLitArea, 
            p.requiresEVCharging, p.requiresWheelchairAccess, p.requiresFamilyParkingArea, p.premiumParking
        FROM User u
        LEFT JOIN Vehicle v ON u.userID = v.userID
        LEFT JOIN ParkingPreferences p ON u.userID = p.userID
        WHERE u.userID = ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $userID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return null;
    }
}

// Check if userID is provided in POST request
if (isset($_POST['userID'])) {
    $userID = $_POST['userID'];  // Change from $_GET to $_POST
    $userData = fetchUserData($conn, $userID);

    if ($userData) {
        echo json_encode([
            'status' => 'success',
            'data' => $userData
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'User data not found.'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'No userID provided.'
    ]);
}

$conn->close();
?>
