<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db_connect.php';

$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (isset($data['userID'], $data['brand'], $data['type'], $data['preferences'])) {
    $userID = $data['userID'];
    $brand = $data['brand'];
    $type = $data['type'];
    $category = $data['category'];
    $preferences = $data['preferences'];

    // Update vehicle data
    $sql_vehicle = "UPDATE Vehicle SET brand = ?, `type` = ?, `category` = ? WHERE userID = ?";
    $stmt = $conn->prepare($sql_vehicle);
    $stmt->bind_param("ssss", $brand, $type, $category, $userID);


    if ($stmt->execute()) {
        // Update parking preferences
        $isNearest = ($preferences['isNearest'] ? 1 : 0);
        $isCovered = ($preferences['isCovered'] ? 1 : 0);
        $requiresLargeSpace = ($preferences['requiresLargeSpace'] ? 1 : 0);
        $requiresWellLitArea = ($preferences['requiresWellLitArea'] ? 1 : 0);
        $requiresEVCharging = ($preferences['requiresEVCharging'] ? 1 : 0);
        $requiresWheelchairAccess = ($preferences['requiresWheelchairAccess'] ? 1 : 0);
        $requiresFamilyParkingArea = ($preferences['requiresFamilyParkingArea'] ? 1 : 0);
        $premiumParking = ($preferences['premiumParking'] ? 1 : 0);

        $sql_preferences = "UPDATE ParkingPreferences 
                            SET isNearest = ?, isCovered = ?, requiresLargeSpace = ?, requiresWellLitArea = ?, 
                                requiresEVCharging = ?, requiresWheelchairAccess = ?, requiresFamilyParkingArea = ?, premiumParking = ? 
                            WHERE userID = ?";
        $stmt = $conn->prepare($sql_preferences);
        $stmt->bind_param(
            "iiiiiiiss",
            $isNearest, $isCovered, $requiresLargeSpace, $requiresWellLitArea, 
            $requiresEVCharging, $requiresWheelchairAccess, $requiresFamilyParkingArea, $premiumParking, $userID
        );

        if ($stmt->execute()) {
            // Success
            echo json_encode([
                'status' => 'success',
                'message' => 'User data updated successfully'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Error updating parking preferences: ' . $stmt->error
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error updating vehicle data: ' . $stmt->error
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing required data.'
    ]);
}

$conn->close();
?>
