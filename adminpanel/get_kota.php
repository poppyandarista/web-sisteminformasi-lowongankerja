<?php
require_once 'session_check.php';
include 'koneksi.php';

$db = new database();

if (isset($_GET['id_provinsi']) && is_numeric($_GET['id_provinsi'])) {
    $id_provinsi = $_GET['id_provinsi'];
    $kota_data = $db->get_kota_by_provinsi($id_provinsi);

    echo json_encode([
        'success' => true,
        'data' => $kota_data
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'ID Provinsi tidak valid'
    ]);
}
?>