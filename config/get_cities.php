<?php
session_start();
include 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    exit;
}

$db = new database();
$province_id = isset($_GET['province_id']) ? intval($_GET['province_id']) : 0;

if ($province_id > 0) {
    $cities = $db->get_cities_by_province($province_id);
    header('Content-Type: application/json');
    echo json_encode($cities);
} else {
    echo json_encode([]);
}
?>