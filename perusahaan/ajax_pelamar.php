<?php
// perusahaan/ajax_pelamar.php
session_start();
require_once 'koneksi_perusahaan.php';

header('Content-Type: application/json');

if (!isset($_SESSION['company_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$company_id = $_SESSION['company_id'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'get_detail':
        $id = intval($_GET['id'] ?? 0);
        $pelamar = $db->getDetailPelamar($id, $company_id);
        if ($pelamar) {
            echo json_encode(['success' => true, 'data' => $pelamar]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Pelamar tidak ditemukan']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>