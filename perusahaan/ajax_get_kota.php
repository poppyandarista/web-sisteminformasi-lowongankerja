<?php
// perusahaan/ajax_get_kota.php
session_start(); 
require_once 'koneksi_perusahaan.php';

if (isset($_GET['id_provinsi'])) {
    $provinsi_id = intval($_GET['id_provinsi']);
    $kota_list = $db->getKotaByProvinsi($provinsi_id);
    header('Content-Type: application/json');
    echo json_encode($kota_list);
} else {
    echo json_encode([]);
}
?>