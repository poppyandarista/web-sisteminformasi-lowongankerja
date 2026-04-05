<?php
require_once 'koneksi.php';

$db = new database();

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $admin = $db->get_admin_by_id($id);

    if ($admin) {
        echo json_encode(['success' => true, 'data' => $admin]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Admin tidak ditemukan']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ID tidak ditemukan']);
}
?>