<?php
header('Content-Type: application/json');
include 'db_connect.php';

try {
    $query = "SELECT lotID, point_order, latitude, longitude 
              FROM parking_lot_boundaries 
              ORDER BY lotID, point_order";
    $result = $conn->query($query);
    
    $response = ['status' => 'success', 'data' => []];
    
    while ($row = $result->fetch_assoc()) {
        if (!isset($response['data'][$row['lotID']])) {
            $response['data'][$row['lotID']] = ['coordinates' => []];
        }
        
        $response['data'][$row['lotID']]['coordinates'][] = [
            'lat' => floatval($row['latitude']),
            'lng' => floatval($row['longitude'])
        ];
    }
    
    echo json_encode($response);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>