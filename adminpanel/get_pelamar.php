<?php
require_once 'session_check.php';
require_once 'koneksi.php';

// Aktifkan error reporting untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $db = new database();
    $pelamar = $db->get_pelamar_by_id($_GET['id']);

    if ($pelamar) {
        echo json_encode([
            'success' => true,
            'data' => $pelamar
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Data tidak ditemukan di database'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'ID tidak valid'
    ]);
}
?>