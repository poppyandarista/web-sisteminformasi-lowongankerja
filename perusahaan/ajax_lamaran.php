<?php
// perusahaan/ajax_lamaran.php
session_start(); 
require_once 'koneksi_perusahaan.php';

header('Content-Type: application/json');

if (!isset($_SESSION['company_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$company_id = $_SESSION['company_id'];
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'update_status':
        $id = intval($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? 'Diproses';
        $catatan = $_POST['catatan'] ?? '';
        
        $result = $db->updateLamaranStatus($id, $status, $catatan, $company_id);
        echo json_encode(['success' => $result, 'message' => $result ? 'Status berhasil diupdate' : 'Gagal update status']);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>