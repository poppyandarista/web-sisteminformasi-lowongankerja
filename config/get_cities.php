<?php
session_start();
require_once 'database.php';

$db = new database();

// Set header untuk JSON response
header('Content-Type: application/json');

// Check if province_id is set
if (isset($_GET['province_id']) && !empty($_GET['province_id'])) {
    $province_id = intval($_GET['province_id']);

    // Get cities by province
    $cities = $db->get_cities_by_province($province_id);

    // Return JSON response
    echo json_encode($cities);
} else {
    // Return empty array if no province_id
    echo json_encode([]);
}
?>