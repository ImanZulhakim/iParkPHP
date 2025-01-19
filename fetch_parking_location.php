<?php
include 'db_connect.php';

// Query to fetch district, state, and parking lot details
$sql = "SELECT 
            ploc.district, 
            ploc.state, 
            pl.lot_name, 
            pl.lotID, 
            COALESCE(pl.coordinates, 'No Coordinates') AS coordinates, 
            pl.locationType, 
            pl.spaces
        FROM parkinglot pl
        INNER JOIN parkinglocation ploc ON pl.locationID = ploc.locationID";


$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $parkingData = [];

    while ($row = $result->fetch_assoc()) {
        $district = !empty($row['district']) ? $row['district'] : 'Unknown District';
        $state = !empty($row['state']) ? $row['state'] : 'Unknown State';

        // Ensure the state group exists
        if (!isset($parkingData[$state])) {
            $parkingData[$state] = []; // Initialize state group
        }

        // Ensure the district group exists within the state
        if (!isset($parkingData[$state][$district])) {
            $parkingData[$state][$district] = [
                'parking_lots' => []
            ];
        }

        // Add parking lot details under the respective district and state
        $parkingData[$state][$district]['parking_lots'][] = [
            'lot_name' => $row['lot_name'],
            'lotID' => $row['lotID'],
            'coordinates' => $row['coordinates'],
            'locationType' => $row['locationType'],
            'spaces' => $row['spaces']
        ];
    }

    // Convert associative array to a list grouped by state and district
    $formattedData = [];
    foreach ($parkingData as $state => $districts) {
        $formattedDistricts = [];
        foreach ($districts as $district => $details) {
            $formattedDistricts[] = [
                'district' => $district,
                'parking_lots' => $details['parking_lots']
            ];
        }
        $formattedData[] = [
            'state' => $state,
            'districts' => $formattedDistricts
        ];
    }

    echo json_encode([
        'status' => 'success',
        'data' => $formattedData,
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'No parking data found',
    ]);
}

$conn->close();
?>
