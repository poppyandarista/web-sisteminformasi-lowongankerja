<?php
require_once 'koneksi.php';

header('Content-Type: application/json');

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $db = new database();
    $perusahaan = $db->get_perusahaan_by_id($_GET['id']);

    if ($perusahaan) {
        echo json_encode([
            'success' => true,
            'data' => $perusahaan
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Data perusahaan tidak ditemukan'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'ID tidak valid'
    ]);
}
?>