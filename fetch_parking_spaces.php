<?php
header('Content-Type: application/json');

// Include the database connection file
include 'db_connect.php';

try {
    // Check if lotID is provided in the request
    if (isset($_GET['lotID'])) {
        $lotID = $_GET['lotID'];

        // Fetch parking spaces data for the given lotID
        $stmt = $conn->prepare("
            SELECT 
                parkingSpaceID,
                parkingType,
                isNearest,
                isCovered,
                isWheelchairAccessible,
                hasLargeSpace,
                isWellLitArea,
                hasEVCharging,
                isFamilyParkingArea,
                isPremium,
                isAvailable,
                lotID,
                coordinates
            FROM parkingspace
            WHERE lotID = ?
        ");
        $stmt->bind_param("s", $lotID);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $parkingSpaces = [];
        
        while ($row = $result->fetch_assoc()) {
            $parkingSpaces[] = [
                'parkingSpaceID' => $row['parkingSpaceID'],
                'parkingType' => $row['parkingType'],
                'isNearest' => (bool)$row['isNearest'],
                'isCovered' => (bool)$row['isCovered'],
                'isWheelchairAccessible' => (bool)$row['isWheelchairAccessible'],
                'hasLargeSpace' => (bool)$row['hasLargeSpace'],
                'isWellLitArea' => (bool)$row['isWellLitArea'],
                'hasEVCharging' => (bool)$row['hasEVCharging'],
                'isFamilyParkingArea' => (bool)$row['isFamilyParkingArea'],
                'isPremium' => (bool)$row['isPremium'],
                'isAvailable' => (bool)$row['isAvailable'],
                'lotID' => $row['lotID'],
                'coordinates' => $row['coordinates'],
            ];
        }

        if (!empty($parkingSpaces)) {
            echo json_encode([
                "status" => "success",
                "data" => $parkingSpaces
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "No parking spaces found for the provided lotID"
            ]);
        }

        $stmt->close();
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "No lotID parameter provided"
        ]);
    }

    $conn->close();

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Error: " . $e->getMessage()
    ]);
}
