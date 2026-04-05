<?php
require_once 'session_check.php';
require_once 'koneksi.php';

header('Content-Type: application/json');

$db = new database();
$provinsi = $db->tampil_data_provinsi();

if ($provinsi) {
    echo json_encode([
        'success' => true,
        'data' => $provinsi
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Data provinsi tidak ditemukan'
    ]);
}
?>