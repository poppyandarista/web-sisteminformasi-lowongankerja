<?php
require_once 'session_check.php';
require_once 'koneksi.php';

header('Content-Type: application/json');

if (isset($_GET['id_provinsi']) && is_numeric($_GET['id_provinsi'])) {
    $db = new database();
    $kota = $db->get_kota_by_provinsi($_GET['id_provinsi']);

    if ($kota) {
        echo json_encode([
            'success' => true,
            'data' => $kota
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'data' => []
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'ID provinsi tidak valid'
    ]);
}
?>