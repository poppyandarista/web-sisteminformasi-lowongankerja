<?php
require_once 'koneksi.php';

header('Content-Type: application/json');

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $db = new database();
    $lowongan = $db->get_lowongan_by_id($_GET['id']);

    if ($lowongan) {
        echo json_encode([
            'success' => true,
            'data' => $lowongan
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Data tidak ditemukan'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'ID tidak valid'
    ]);
}
?>