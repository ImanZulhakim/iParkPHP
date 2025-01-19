<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db_connect.php';

// Function to generate the next userID
function generateUserID($conn) {
    $result = $conn->query("SELECT userID FROM User ORDER BY userID DESC LIMIT 1");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastID = $row['userID'];
        $number = (int) substr($lastID, 1);
        $newID = 'U' . str_pad($number + 1, 2, '0', STR_PAD_LEFT);
        return $newID;
    } else {
        return 'U01';
    }
}

// Function to generate the next vehicleID
function generateVehicleID($conn) {
    $result = $conn->query("SELECT vehicleID FROM Vehicle ORDER BY vehicleID DESC LIMIT 1");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastID = $row['vehicleID'];
        $number = (int) substr($lastID, 1);
        $newID = 'V' . str_pad($number + 1, 2, '0', STR_PAD_LEFT);
        return $newID;
    } else {
        return 'V01';
    }
}

// Function to generate the next preferencesID
function generatePreferencesID($conn) {
    $result = $conn->query("SELECT preferencesID FROM ParkingPreferences ORDER BY preferencesID DESC LIMIT 1");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastID = $row['preferencesID'];
        $number = (int) substr($lastID, 1); // Assuming 'P01' format for preferencesID
        $newID = 'P' . str_pad($number + 1, 2, '0', STR_PAD_LEFT);
        return $newID;
    } else {
        return 'P01';
    }
}

$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_BCRYPT);
$phoneNo = $_POST['phoneNo'];
$username = $_POST['username'];
$gender = $_POST['gender'];
$hasDisability = $_POST['hasDisability'];
$brand = $_POST['brand'];
$type = $_POST['type'];
$category = $_POST['category'];
$preferences = json_decode($_POST['preferences'], true);

// Check if json_decode was successful
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(["status" => "error", "message" => "Invalid JSON in preferences"]);
    exit();
}

// ** Check if email already exists **
$sql_check_email = "SELECT * FROM User WHERE email = ?";
$stmt = $conn->prepare($sql_check_email);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Email already exists"]);
    exit();
}

// Generate a new userID
$userID = generateUserID($conn);

// Insert user data
$sql_user = "INSERT INTO User (userID, email, password, phoneNo, username, userType, gender, hasDisability) VALUES ('$userID', '$email', '$password', '$phoneNo', '$username', 'Driver', '$gender', '$hasDisability')";

if ($conn->query($sql_user) === TRUE) {
    // Generate a new vehicleID
    $vehicleID = generateVehicleID($conn);

    // Insert vehicle data
    $sql_vehicle = "INSERT INTO Vehicle (vehicleID, brand, type, category, userID) VALUES ('$vehicleID', '$brand', '$type', '$category', '$userID')";
    if (!$conn->query($sql_vehicle)) {
        echo json_encode(["status" => "error", "message" => "Error: " . $conn->error]);
        exit();
    }

    // ** Check if preferences already exist for the user **
    $sql_check_preferences = "SELECT * FROM ParkingPreferences WHERE userID = ?";
    $stmt = $conn->prepare($sql_check_preferences);
    $stmt->bind_param("s", $userID);
    $stmt->execute();
    $result = $stmt->get_result();

    // Assign preferences to variables to pass them to bind_param()
    $isNearest = ($preferences['isNearest'] ? 1 : 0);
    $isCovered = ($preferences['isCovered'] ? 1 : 0);
    $requiresLargeSpace = ($preferences['requiresLargeSpace'] ? 1 : 0);
    $requiresWellLitArea = ($preferences['requiresWellLitArea'] ? 1 : 0);
    $requiresEVCharging = ($preferences['requiresEVCharging'] ? 1 : 0);
    $requiresWheelchairAccess = ($preferences['requiresWheelchairAccess'] ? 1 : 0);
    $requiresFamilyParkingArea = ($preferences['requiresFamilyParkingArea'] ? 1 : 0);
    $premiumParking = ($preferences['premiumParking'] ? 1 : 0);

    if ($result->num_rows > 0) {
        // Update existing preferences for the user
        $sql_update_preferences = "UPDATE ParkingPreferences 
                                   SET isNearest = ?, isCovered = ?, requiresLargeSpace = ?, requiresWellLitArea = ?, requiresEVCharging = ?, requiresWheelchairAccess = ?, requiresFamilyParkingArea = ?, premiumParking = ? 
                                   WHERE userID = ?";
        $stmt = $conn->prepare($sql_update_preferences);
        $stmt->bind_param(
            "iiiiiiiss",
            $isNearest,
            $isCovered,
            $requiresLargeSpace,
            $requiresWellLitArea,
            $requiresEVCharging,
            $requiresWheelchairAccess,
            $requiresFamilyParkingArea,
            $premiumParking,
            $userID
        );

        if (!$stmt->execute()) {
            echo json_encode(["status" => "error", "message" => "Error updating preferences: " . $stmt->error]);
            exit();
        }
    } else {
        // If no existing preferences, proceed with the insertion
        $preferencesID = generatePreferencesID($conn);  // Generate new preferencesID
        $sql_preferences = "INSERT INTO ParkingPreferences (preferencesID, isNearest, isCovered, requiresLargeSpace, requiresWellLitArea, requiresEVCharging, requiresWheelchairAccess, requiresFamilyParkingArea, premiumParking, userID) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql_preferences);
        $stmt->bind_param(
            "siiiiiiiss",
            $preferencesID,
            $isNearest,
            $isCovered,
            $requiresLargeSpace,
            $requiresWellLitArea,
            $requiresEVCharging,
            $requiresWheelchairAccess,
            $requiresFamilyParkingArea,
            $premiumParking,
            $userID
        );

        if (!$stmt->execute()) {
            echo json_encode(["status" => "error", "message" => "Error inserting preferences: " . $stmt->error]);
            exit();
        }
    }

    // Return success message with user, vehicle details, and preferences
    echo json_encode([
        "status" => "success",
        "message" => "User registered successfully",
        "user" => [
            "userID" => $userID,
            "email" => $email,
            "username" => $username,
            "brand" => $brand,
            "type" => $type,
            "category" => $category,
            "preferences" => $preferences
        ]
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Error: " . $conn->error]);
}

$conn->close();
