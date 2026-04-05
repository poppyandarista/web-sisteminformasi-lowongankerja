<?php
require_once 'session_check.php';
include 'koneksi.php';

$db = new database();

$data_lowongan = $db->tampil_data_lowongan();
$data_perusahaan = $db->tampil_data_perusahaan();
$data_kategori = $db->tampil_data_kategori();
$data_jenis = $db->tampil_data_jenis();

// ========== HANDLER AJAX UNTUK OPERASI LOWONGAN ==========

// Tangani get lowongan by ID via AJAX (tambahkan handler ini)
if (isset($_GET['action']) && $_GET['action'] == 'get' && isset($_GET['id'])) {
  header('Content-Type: application/json');
  $id = $_GET['id'];
  $response = ['success' => false, 'data' => null, 'message' => ''];

  if (is_numeric($id)) {
    $query = "SELECT l.*, p.nama_perusahaan, k.nama_kategori, prov.nama_provinsi, kot.nama_kota, j.nama_jenis
                  FROM lowongan l
                  LEFT JOIN perusahaan p ON l.id_perusahaan = p.id_perusahaan
                  LEFT JOIN kategori k ON l.kategori_lowongan = k.id_kategori
                  LEFT JOIN provinsi prov ON l.id_provinsi = prov.id_provinsi
                  LEFT JOIN kota kot ON l.id_kota = kot.id_kota
                  LEFT JOIN jenis j ON l.id_jenis = j.id_jenis
                  WHERE l.id_lowongan = ?";
    $stmt = $db->koneksi->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      $response['success'] = true;
      $response['data'] = $result->fetch_assoc();
    } else {
      $response['message'] = 'Data tidak ditemukan';
    }
  } else {
    $response['message'] = 'ID tidak valid';
  }
  echo json_encode($response);
  exit;
}

// Tangani hapus lowongan via AJAX
if (isset($_GET['action']) && $_GET['action'] == 'hapus' && isset($_GET['id'])) {
  header('Content-Type: application/json');
  $id = $_GET['id'];
  $ajax_response = ['success' => false, 'message' => ''];

  if (is_numeric($id)) {
    if ($db->hapus_lowongan($id)) {
      $ajax_response['success'] = true;
      $ajax_response['message'] = 'Data lowongan berhasil dihapus';
    } else {
      $ajax_response['message'] = 'Gagal menghapus data lowongan';
    }
  } else {
    $ajax_response['message'] = 'ID tidak valid';
  }
  echo json_encode($ajax_response);
  exit;
}

// Tangani edit lowongan via AJAX
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit') {
  header('Content-Type: application/json');
  $id_lowongan = $_POST['id_lowongan'];
  $id_perusahaan = $_POST['id_perusahaan'];
  $judul_lowongan = $_POST['judul_lowongan'];
  $kategori_lowongan = (int) $_POST['kategori_lowongan'];
  $id_jenis = (int) $_POST['id_jenis']; // Update: dari waktukerja menjadi id_jenis
  $id_provinsi = $_POST['id_provinsi'] ?? null;
  $id_kota = $_POST['id_kota'] ?? null;
  $lokasi_lowongan = $_POST['lokasi_lowongan'];
  $gaji_lowongan = $_POST['gaji_lowongan'] ? floatval($_POST['gaji_lowongan']) : null;
  $kualifikasi = $_POST['kualifikasi'];
  $deskripsi_lowongan = $_POST['deskripsi_lowongan'];
  $pertanyaan = $_POST['pertanyaan'];
  $tanggal_tutup = $_POST['tanggal_tutup'];
  $status = $_POST['status'];
  $ajax_response = ['success' => false, 'message' => ''];

  // Validasi data wajib
  if (empty($id_perusahaan) || empty($id_provinsi) || empty($id_kota)) {
    $ajax_response['message'] = 'Perusahaan, Provinsi, dan Kota wajib diisi!';
    echo json_encode($ajax_response);
    exit;
  }

  // Handle upload gambar jika ada
  $gambar = null;
  if (isset($_FILES['gambar']) && $_FILES['gambar']['name'] != "") {
    $gambar = $_FILES['gambar'];
    $nama_gambar = time() . "_" . basename($gambar['name']);
    $target_dir = "src/images/jobs/";

    if (!is_dir($target_dir)) {
      mkdir($target_dir, 0777, true);
    }

    $target_file = $target_dir . $nama_gambar;

    if ($gambar['size'] > 2000000) {
      $ajax_response['message'] = 'Ukuran gambar terlalu besar! Maksimal 2MB.';
      echo json_encode($ajax_response);
      exit;
    } elseif (!in_array($gambar['type'], ['image/jpeg', 'image/png', 'image/gif'])) {
      $ajax_response['message'] = 'Format gambar tidak didukung! Gunakan JPG, PNG, atau GIF.';
      echo json_encode($ajax_response);
      exit;
    } elseif (move_uploaded_file($gambar['tmp_name'], $target_file)) {
      $gambar = $nama_gambar;
    } else {
      $gambar = null;
    }
  } else {
    $gambar = $_POST['gambar_lama'] ?? null;
  }

  // Siapkan data untuk update
  $data_update = [
    'id_perusahaan' => $id_perusahaan,
    'judul_lowongan' => $judul_lowongan,
    'kategori_lowongan' => $kategori_lowongan,
    'id_jenis' => $id_jenis, // Update: dari waktukerja menjadi id_jenis
    'id_provinsi' => $id_provinsi,
    'id_kota' => $id_kota,
    'lokasi_lowongan' => $lokasi_lowongan,
    'gaji_lowongan' => $gaji_lowongan,
    'kualifikasi' => $kualifikasi,
    'deskripsi_lowongan' => $deskripsi_lowongan,
    'pertanyaan' => $pertanyaan,
    'tanggal_tutup' => $tanggal_tutup,
    'status' => $status,
    'gambar' => $gambar
  ];

  if ($db->update_lowongan($id_lowongan, $data_update)) {
    $ajax_response['success'] = true;
    $ajax_response['message'] = 'Data lowongan berhasil diperbarui';
  } else {
    $ajax_response['message'] = 'Gagal mengupdate data lowongan';
  }
  echo json_encode($ajax_response);
  exit;
}

// Tangani tambah lowongan via AJAX (jika ada form tambah)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'tambah') {
  header('Content-Type: application/json');

  $id_perusahaan = $_POST['id_perusahaan'];
  $judul_lowongan = $_POST['judul_lowongan'];
  $kategori_lowongan = (int) $_POST['kategori_lowongan'];
  $id_jenis = (int) $_POST['id_jenis']; // Update: menggunakan id_jenis
  $id_provinsi = $_POST['id_provinsi'] ?? null;
  $id_kota = $_POST['id_kota'] ?? null;
  $lokasi_lowongan = $_POST['lokasi_lowongan'];
  $gaji_lowongan = $_POST['gaji_lowongan'] ? floatval($_POST['gaji_lowongan']) : null;
  $kualifikasi = $_POST['kualifikasi'];
  $deskripsi_lowongan = $_POST['deskripsi_lowongan'];
  $pertanyaan = $_POST['pertanyaan'];
  $tanggal_tutup = $_POST['tanggal_tutup'];
  $status = $_POST['status'];

  $ajax_response = ['success' => false, 'message' => ''];

  // Validasi data wajib
  if (empty($id_perusahaan) || empty($id_provinsi) || empty($id_kota)) {
    $ajax_response['message'] = 'Perusahaan, Provinsi, dan Kota wajib diisi!';
    echo json_encode($ajax_response);
    exit;
  }

  // Handle upload gambar jika ada
  $gambar = null;
  if (isset($_FILES['gambar']) && $_FILES['gambar']['name'] != "") {
    $gambar = $_FILES['gambar'];
    $nama_gambar = time() . "_" . basename($gambar['name']);
    $target_dir = "src/images/jobs/";

    if (!is_dir($target_dir)) {
      mkdir($target_dir, 0777, true);
    }

    $target_file = $target_dir . $nama_gambar;

    if ($gambar['size'] > 2000000) {
      $ajax_response['message'] = 'Ukuran gambar terlalu besar! Maksimal 2MB.';
      echo json_encode($ajax_response);
      exit;
    } elseif (!in_array($gambar['type'], ['image/jpeg', 'image/png', 'image/gif'])) {
      $ajax_response['message'] = 'Format gambar tidak didukung! Gunakan JPG, PNG, atau GIF.';
      echo json_encode($ajax_response);
      exit;
    } elseif (move_uploaded_file($gambar['tmp_name'], $target_file)) {
      $gambar = $nama_gambar;
    } else {
      $gambar = null;
    }
  }

  // Siapkan data untuk insert
  $data_insert = [
    'id_perusahaan' => $id_perusahaan,
    'judul_lowongan' => $judul_lowongan,
    'kategori_lowongan' => $kategori_lowongan,
    'id_jenis' => $id_jenis, // Update: menggunakan id_jenis
    'id_provinsi' => $id_provinsi,
    'id_kota' => $id_kota,
    'lokasi_lowongan' => $lokasi_lowongan,
    'gaji_lowongan' => $gaji_lowongan,
    'kualifikasi' => $kualifikasi,
    'deskripsi_lowongan' => $deskripsi_lowongan,
    'pertanyaan' => $pertanyaan,
    'tanggal_tutup' => $tanggal_tutup,
    'status' => $status,
    'gambar' => $gambar
  ];

  if ($db->tambah_lowongan($data_insert)) {
    $ajax_response['success'] = true;
    $ajax_response['message'] = 'Data lowongan berhasil ditambahkan';
  } else {
    $ajax_response['message'] = 'Gagal menambah data lowongan';
  }
  echo json_encode($ajax_response);
  exit;
}

// Get lowongan by ID untuk edit
function get_lowongan_by_id($db, $id)
{
  $query = "SELECT * FROM lowongan WHERE id_lowongan = ?";
  $stmt = $db->koneksi->prepare($query);
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $result = $stmt->get_result();
  return $result->fetch_assoc();
}
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport"
    content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>Data Lowongan | LinkUp</title>
  <link rel="icon" type="image/png" href="src/images/logo/favicon.png">
  <link href="style.css" rel="stylesheet">
  <link href="button-styles.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.tailwindcss.css">
  <style>
    .page-overlay {
      position: fixed;
      inset: 0;
      background-color: rgba(75, 85, 99, 0.5) !important;
      z-index: 9998 !important;
      pointer-events: auto;
    }

    #main-wrapper header,
    #main-wrapper aside {
      z-index: 50 !important;
    }

    .modal-container {
      z-index: 10000 !important;
    }

    .modal-content {
      z-index: 10001 !important;
    }

    .force-overlay {
      z-index: 9999 !important;
    }

    /* Style untuk tabel lowongan */
    #lowonganTable tbody tr {
      height: 60px !important;
    }

    #lowonganTable td {
      padding-top: 0.75rem !important;
      padding-bottom: 0.75rem !important;
      padding-left: 1rem !important;
      padding-right: 1rem !important;
    }

    #lowonganTable th {
      padding-top: 0.75rem !important;
      padding-bottom: 0.75rem !important;
    }

    /* Modal konfirmasi hapus */
    #modalHapusLowongan {
      display: none;
    }

    #modalHapusLowongan:not(.hidden) {
      display: flex;
    }

    #modalEditLowongan {
      display: none;
    }

    #modalEditLowongan:not(.hidden) {
      display: flex;
    }

    #modalEditLowongan .modal-content {
      width: 85%;
      max-width: 768px;
      margin: 0 auto;
    }

    #modalTambahLowongan {
      display: none;
    }

    #modalTambahLowongan:not(.hidden) {
      display: flex;
    }

    #modalTambahLowongan .modal-content {
      width: 85%;
      max-width: 768px;
      margin: 0 auto;
    }

    .modal-content {
      max-height: 90vh;
      overflow-y: auto;
    }

    @media (max-width: 640px) {
      .modal-content {
        max-width: 95%;
        margin: 0 10px;
      }

      #modalEditLowongan .modal-content {
        width: 95%;
        max-width: none;
        margin: 0 10px;
      }

      #modalTambahLowongan .modal-content {
        width: 95%;
        max-width: none;
        margin: 0 10px;
      }
    }

    /* Status badge */
    .status-badge {
      padding: 2px 8px;
      border-radius: 12px;
      font-size: 11px;
      font-weight: 600;
    }

    .status-aktif {
      background-color: #dcfce7;
      color: #166534;
    }

    .dark .status-aktif {
      background-color: #14532d;
      color: #bbf7d0;
    }

    .status-nonaktif {
      background-color: #fee2e2;
      color: #991b1b;
    }

    .dark .status-nonaktif {
      background-color: #7f1d1d;
      color: #fecaca;
    }

    /* Gaji styling */
    .gaji-text {
      font-weight: 600;
      color: #059669;
    }

    .dark .gaji-text {
      color: #34d399;
    }

    /* Deskripsi singkat */
    .deskripsi-singkat {
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
      text-overflow: ellipsis;
      max-width: 250px;
    }

    /* Work type badge */
    .worktype-badge {
      padding: 2px 8px;
      border-radius: 12px;
      font-size: 11px;
      font-weight: 500;
      background-color: #e0e7ff;
      color: #3730a3;
    }

    .dark .worktype-badge {
      background-color: #3730a3;
      color: #e0e7ff;
    }

    /* Form styling */
    .form-group {
      margin-bottom: 1rem;
    }

    .form-label {
      display: block;
      margin-bottom: 0.25rem;
      font-size: 0.875rem;
      font-weight: 500;
      color: #374151;
    }

    .dark .form-label {
      color: #d1d5db;
    }

    .form-control {
      width: 100%;
      padding: 0.5rem 0.75rem;
      font-size: 0.875rem;
      border: 1px solid #d1d5db;
      border-radius: 0.375rem;
      background-color: white;
      transition: border-color 0.15s ease-in-out;
    }

    .dark .form-control {
      background-color: #374151;
      border-color: #4b5563;
      color: #f3f4f6;
    }

    .form-control:focus {
      outline: none;
      border-color: #3b82f6;
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .form-textarea {
      min-height: 80px;
      resize: vertical;
    }

    /* Tambahkan di bagian <style> dalam <head> */

    /* Fix untuk tombol hapus di tabel lowongan */
    #lowonganTable .action-buttons button {
      visibility: visible !important;
      opacity: 1 !important;
      display: inline-flex !important;
      align-items: center !important;
      justify-content: center !important;
    }

    /* Styling spesifik untuk tombol Edit dan Hapus di lowongan */
    .btn-edit-lowongan {
      padding: 0.5rem 0.75rem !important;
      font-size: 0.75rem !important;
      background-color: #3b82f6 !important;
      color: white !important;
      border-radius: 0.375rem !important;
      border: none !important;
      display: inline-flex !important;
      align-items: center !important;
      gap: 4px !important;
      cursor: pointer !important;
      transition: background-color 0.2s ease !important;
      white-space: nowrap !important;
    }

    .btn-edit-lowongan:hover {
      background-color: #2563eb !important;
    }

    .btn-hapus-lowongan {
      padding: 0.5rem 0.75rem !important;
      font-size: 0.75rem !important;
      background-color: #ef4444 !important;
      color: white !important;
      border-radius: 0.375rem !important;
      border: none !important;
      display: inline-flex !important;
      align-items: center !important;
      gap: 4px !important;
      cursor: pointer !important;
      transition: background-color 0.2s ease !important;
      white-space: nowrap !important;
    }

    .btn-hapus-lowongan:hover {
      background-color: #dc2626 !important;
    }

    /* Fix untuk kolom aksi di DataTables */
    #lowonganTable td:last-child {
      min-width: 140px !important;
    }

    /* Fix SVG visibility */
    #lowonganTable button svg {
      fill: white !important;
      stroke: white !important;
      display: inline-block !important;
      visibility: visible !important;
      opacity: 1 !important;
    }

    /* ==================== PERBAIKAN DATATABLES v2 UNTUK LOWONGAN ==================== */
    .dt-length,
    .dt-search,
    .dt-info,
    .dt-paging {
      font-size: 0.75rem !important;
      /* text-xs */
    }

    .dt-length {
      margin-bottom: 0.75rem !important;
      /* mb-3 */
    }

    .dt-search {
      margin-bottom: 0.75rem !important;
      /* mb-3 */
    }

    .dt-info {
      margin-top: 0.75rem !important;
      /* mt-3 */
    }

    .dt-paging {
      margin-top: 0.75rem !important;
      /* mt-3 */
    }

    /* Input dan Select - TAMBAHKAN line-height dan vertical alignment */
    .dt-search input,
    .dt-length select {
      height: 1.75rem !important;
      /* h-7 */
      min-height: 1.75rem !important;
      line-height: 1rem !important;
      padding-top: 0.25rem !important;
      /* py-1 */
      padding-bottom: 0.25rem !important;
      padding-left: 0.5rem !important;
      /* px-2 */
      padding-right: 0.5rem !important;
      font-size: 0.75rem !important;
      /* text-xs */
      vertical-align: middle !important;
    }

    /* Label harus flex dan align items center */
    .dt-length label,
    .dt-search label {
      display: flex !important;
      align-items: center !important;
      gap: 0.5rem !important;
      /* gap-2 */
      font-size: 0.75rem !important;
      /* text-xs */
    }

    /* Pagination buttons */
    .dt-paging .dt-paging-button {
      min-height: 1.5rem !important;
      /* h-6 */
      height: 1.5rem !important;
      display: inline-flex !important;
      align-items: center !important;
      justify-content: center !important;
      vertical-align: middle !important;
    }

    /* Pastikan container flex */
    #lowonganTable_wrapper .flex {
      display: flex !important;
      align-items: center !important;
    }

    /* FIX: Untuk select dropdown angka */
    .dt-length select option {
      font-size: 0.75rem !important;
      padding: 0.25rem !important;
    }

    /* PERBAIKAN KHUSUS: Untuk angka dalam dropdown */
    select.dt-input option {
      padding: 4px 8px !important;
      font-size: 12px !important;
    }

    @media (max-width: 768px) {

      #lowonganTable_wrapper .dt-length,
      #lowonganTable_wrapper .dt-search {
        margin-bottom: 0.5rem !important;
      }

      #lowonganTable_wrapper .dt-info,
      #lowonganTable_wrapper .dt-paging {
        margin-top: 0.5rem !important;
      }
    }

    /* ==================== RESPONSIVE TABEL LOWONGAN ==================== */
    /* Tampilan Desktop - Normal */
    #lowonganTable {
      width: 100%;
      table-layout: auto;
    }

    /* Container untuk horizontal scroll */
    .table-wrapper {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
      scrollbar-width: thin;
      scrollbar-color: #cbd5e1 #f1f5f9;
    }

    .dark .table-wrapper {
      scrollbar-color: #4b5563 #374151;
    }

    .table-wrapper::-webkit-scrollbar {
      height: 8px;
    }

    .table-wrapper::-webkit-scrollbar-track {
      background: #f1f5f9;
      border-radius: 4px;
    }

    .dark .table-wrapper::-webkit-scrollbar-track {
      background: #374151;
    }

    .table-wrapper::-webkit-scrollbar-thumb {
      background: #cbd5e1;
      border-radius: 4px;
    }

    .dark .table-wrapper::-webkit-scrollbar-thumb {
      background: #4b5563;
    }

    /* Fixed controls styling */
    #tableControls,
    #tableFooter {
      position: relative;
      z-index: 1;
      background: inherit;
    }

    /* Tampilan Tablet (768px - 1024px) */
    @media (max-width: 1024px) {
      #lowonganTable {
        min-width: 900px;
        font-size: 0.875rem;
      }

      #lowonganTable th,
      #lowonganTable td {
        padding: 0.5rem 0.75rem !important;
      }
    }

    /* Tampilan Mobile (<= 768px) */
    @media (max-width: 768px) {
      #lowonganTable {
        min-width: 800px;
        font-size: 0.75rem;
      }

      #lowonganTable th,
      #lowonganTable td {
        padding: 0.375rem 0.5rem !important;
      }

      /* DataTables controls responsif */
      .dt-length,
      .dt-search,
      .dt-info,
      .dt-paging {
        font-size: 0.7rem !important;
      }

      .dt-search input,
      .dt-length select {
        height: 1.5rem !important;
        font-size: 0.7rem !important;
      }

      /* Adjust job image size */
      .h-8.w-8.rounded-full {
        width: 32px !important;
        height: 32px !important;
      }
    }

    /* Tampilan Very Small Mobile (<= 480px) */
    @media (max-width: 480px) {
      #lowonganTable {
        min-width: 700px;
      }

      .flex.flex-col.gap-2.sm\:flex-row.sm\:gap-2 {
        flex-direction: column !important;
        gap: 0.25rem !important;
      }

      .h-8.w-8.rounded-full {
        width: 28px !important;
        height: 28px !important;
      }
    }

    /* ========== PERBAIKAN TOMBOL EXPORT LOWONGAN ========== */
    button[onclick*="modalExportLowongan"] {
      background-color: #16a34a !important;
      color: white !important;
      font-weight: 500 !important;
      border: none !important;
      display: inline-flex !important;
      align-items: center !important;
      gap: 0.5rem !important;
      padding: 0.5rem 0.75rem !important;
      font-size: 0.75rem !important;
      border-radius: 0.5rem !important;
      transition: all 0.3s ease !important;
      box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
    }

    button[onclick*="modalExportLowongan"]:hover {
      background-color: #15803d !important;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
    }

    button[onclick*="modalExportLowongan"] svg {
      width: 1rem !important;
      height: 1rem !important;
    }

    /* ==================== ALERT SYSTEM STYLES - Sesuai Template ==================== */
    .alert-container {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 9999;
      display: flex;
      flex-direction: column;
      gap: 10px;
      max-width: 400px;
    }

    .alert-item {
      border-radius: 0.75rem;
      padding: 1rem;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      border: 1px solid;
      display: flex;
      align-items: flex-start;
      gap: 0.75rem;
      transform: translateX(100%);
      opacity: 0;
      transition: all 0.3s ease-out;
    }

    .alert-item.show {
      transform: translateX(0);
      opacity: 1;
    }

    .alert-item.hide {
      transform: translateX(100%);
      opacity: 0;
    }

    .alert-item.success {
      border-color: #10b981;
      background-color: #f0fdf4;
      color: #065f46;
    }

    .alert-item.error {
      border-color: #ef4444;
      background-color: #fef2f2;
      color: #991b1b;
    }

    .alert-item.warning {
      border-color: #f59e0b;
      background-color: #fffbeb;
      color: #92400e;
    }

    .alert-item.info {
      border-color: #3b82f6;
      background-color: #eff6ff;
      color: #1e40af;
    }

    /* Dark mode support */
    .dark .alert-item {
      background: #1f2937;
      color: #f9fafb;
    }

    .dark .alert-item.success {
      background: rgba(16, 185, 129, 0.15);
      border-color: #10b981;
      color: #34d399;
    }

    .dark .alert-item.error {
      background: rgba(239, 68, 68, 0.15);
      border-color: #ef4444;
      color: #f87171;
    }

    .dark .alert-item.warning {
      background: rgba(245, 158, 11, 0.15);
      border-color: #f59e0b;
      color: #fbbf24;
    }

    .dark .alert-item.info {
      background: rgba(59, 130, 246, 0.15);
      border-color: #3b82f6;
      color: #60a5fa;
    }
  </style>
</head>

<body
  x-data="{ page: 'dataLowongan', 'loaded': true, 'darkMode': false, 'stickyMenu': false, 'sidebarToggle': false, 'scrollTop': false, 'modalOpen': false }"
  x-init="
         darkMode = JSON.parse(localStorage.getItem('darkMode'));
         $watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)))"
  :class="{'dark bg-gray-900': darkMode === true}">
  <!-- ===== Preloader Start ===== -->
  <div x-show="loaded"
    x-init="window.addEventListener('DOMContentLoaded', () => {setTimeout(() => loaded = false, 500)})"
    class="fixed left-0 top-0 z-999999 flex h-screen w-screen items-center justify-center bg-white dark:bg-black">
    <div class="h-16 w-16 animate-spin rounded-full border-4 border-solid border-brand-500 border-t-transparent">
    </div>
  </div>
  <!-- ===== Preloader End ===== -->

  <!-- Overlay untuk seluruh halaman -->
  <div id="pageOverlay" class="page-overlay force-overlay hidden" onclick="hideAllModals()"></div>

  <!-- ===== Page Wrapper Start ===== -->
  <div id="main-wrapper" class="flex h-screen overflow-hidden transition-all duration-300">
    <!-- ===== Sidebar Start ===== -->
    <?php include 'sidebar.php'; ?>
    <!-- ===== Sidebar End ===== -->

    <!-- ===== Content Area Start ===== -->
    <div class="relative flex flex-col flex-1 overflow-x-hidden overflow-y-auto">
      <!-- Small Device Overlay Start -->
      <div @click="sidebarToggle = false" :class="sidebarToggle ? 'block lg:hidden' : 'hidden'"
        class="fixed w-full h-screen z-9 bg-gray-900/50"></div>
      <!-- Small Device Overlay End -->

      <!-- ===== Header Start ===== -->
      <?php include("header.php") ?>
      <!-- ===== Header End ===== -->

      <!-- ===== Main Content Start ===== -->
      <main>
        <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
          <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-title-md2 font-bold text-black dark:text-white">
              Data Lowongan
            </h2>
            <nav>
              <ol class="flex items-center gap-2">
                <li><a class="font-medium" href="index.php">Home ></a></li>
                <li class="font-medium text-primary">Data Lowongan</li>
              </ol>
            </nav>
          </div>


          <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
            <div
              class="border-b border-stroke px-4 py-4 dark:border-strokedark sm:px-6 xl:px-7.5 flex justify-between items-center">
              <h3 class="font-medium text-black dark:text-white">Tabel Data Lowongan</h3>
              <div class="flex items-center gap-2">
                <button onclick="toggleModal('modalTambahLowongan')"
                  class="inline-flex items-center justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors duration-200 gap-2 shadow-sm hover:shadow-md">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                  </svg>
                  Tambah Lowongan
                </button>
                <button onclick="toggleModal('modalExportLowongan')"
                  class="inline-flex items-center justify-center rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 transition-colors duration-200 gap-2 shadow-sm hover:shadow-md">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                  Export
                </button>
              </div>
            </div>
            <div class="p-4 sm:p-6 xl:p-7.5">
              <!-- DataTables Controls Container - Fixed -->
              <div id="tableControls" class="mb-2">
                <!-- Length Menu dan Search akan di-inject oleh DataTables di sini -->
              </div>

              <!-- Table Container dengan Horizontal Scroll -->
              <div class="table-wrapper overflow-x-auto">
                <table id="lowonganTable" class="w-full table-auto border-collapse text-left">
                  <thead>
                    <tr class="bg-gray-2 dark:bg-meta-4">
                      <th class="px-4 py-3 font-medium text-black dark:text-white">ID Lowongan</th>
                      <th class="px-4 py-3 font-medium text-black dark:text-white">Judul Lowongan</th>
                      <th class="px-4 py-3 font-medium text-black dark:text-white">Perusahaan & Kategori</th>
                      <th class="px-4 py-3 font-medium text-black dark:text-white">Lokasi & Kualifikasi</th>
                      <th class="px-4 py-3 font-medium text-black dark:text-white">Gaji & Status</th>
                      <th class="px-4 py-3 font-medium text-black dark:text-white">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    if (!empty($data_lowongan)) {
                      foreach ($data_lowongan as $row) {
                        $format_id = "L" . sprintf("%04d", $row['id_lowongan']);
                        $gaji = $row['gaji_lowongan'] ? "Rp " . number_format($row['gaji_lowongan'], 0, ',', '.') : "Negosiasi";
                        $lokasi = trim($row['nama_kota'] . ", " . $row['nama_provinsi'], ", ");
                        $kualifikasi_singkat = strlen($row['kualifikasi']) > 100
                          ? substr($row['kualifikasi'], 0, 100) . "..."
                          : $row['kualifikasi'];
                        ?>
                        <tr class="border-b border-[#eee] dark:border-strokedark">
                          <td class="px-4 py-3">
                            <p class="text-black dark:text-white font-medium text-sm"><?php echo $format_id; ?></p>
                            <p class="text-xs text-gray-500">
                              <?php echo date('d M Y', strtotime($row['tanggal_posting'])); ?>
                            </p>
                          </td>

                          <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                              <div
                                class="h-8 w-12 rounded flex items-center justify-center bg-gray-100 dark:bg-gray-800 overflow-hidden">
                                <?php if (!empty($row['gambar'])): ?>
                                  <img src="src/images/jobs/<?php echo $row['gambar']; ?>" alt="Lowongan"
                                    class="h-8 w-12 object-cover" />
                                <?php else: ?>
                                  <span class="text-xs font-semibold text-gray-600 dark:text-gray-400">
                                    <?php echo substr($row['judul_lowongan'], 0, 2); ?>
                                  </span>
                                <?php endif; ?>
                              </div>
                              <div>
                                <h5 class="font-medium text-black dark:text-white text-sm">
                                  <?php echo htmlspecialchars($row['judul_lowongan']); ?>
                                </h5>
                                <div class="flex items-center gap-1 mt-1">
                                  <span class="worktype-badge">
                                    <?php echo $row['nama_jenis'] ?? '-'; ?>
                                  </span>
                                </div>
                              </div>
                            </div>
                          </td>

                          <td class="px-4 py-3">
                            <p class="text-sm text-black dark:text-white">
                              <?php echo htmlspecialchars($row['nama_perusahaan'] ?? '-'); ?>
                            </p>
                            <p class="text-xs text-gray-500">
                              <?php
                              // Ambil nama kategori dari join yang sudah dilakukan di query tampil_data_lowongan
                              echo htmlspecialchars($row['nama_kategori'] ?? '-');
                              ?>
                            </p>
                          </td>

                          <td class="px-4 py-3">
                            <p class="text-sm text-black dark:text-white"><?php echo htmlspecialchars($lokasi); ?></p>
                            <p class="text-xs text-gray-500 deskripsi-singkat"
                              title="<?php echo htmlspecialchars($row['kualifikasi']); ?>">
                              <?php echo htmlspecialchars($kualifikasi_singkat); ?>
                            </p>
                          </td>

                          <td class="px-4 py-3">
                            <p class="text-sm gaji-text"><?php echo $gaji; ?></p>
                            <span
                              class="status-badge <?php echo $row['status'] == 'Aktif' ? 'status-aktif' : 'status-nonaktif'; ?>">
                              <?php echo $row['status']; ?>
                            </span>
                          </td>

                          <td class="px-4 py-3">
                            <div class="flex flex-col gap-2 sm:flex-row sm:gap-2">
                              <button onclick="showEditForm(<?php echo $row['id_lowongan']; ?>)"
                                class="btn-edit-lowongan inline-flex items-center gap-2 px-3 py-2 text-xs font-medium text-white transition rounded-lg">
                                <svg width="14" height="14" viewBox="0 0 20 20" fill="none"
                                  xmlns="http://www.w3.org/2000/svg">
                                  <path
                                    d="M13.5858 3.58579C14.3668 2.80474 15.6332 2.80474 16.4142 3.58579C17.1953 4.36683 17.1953 5.63316 16.4142 6.41421L15.6213 7.20711L12.7929 4.37868L13.5858 3.58579Z"
                                    fill="white" />
                                  <path d="M11.3787 5.79289L3 14.1716V17H5.82842L14.2071 8.62132L11.3787 5.79289Z"
                                    fill="white" />
                                </svg>
                                <span>Edit</span>
                              </button>

                              <button
                                onclick="showDeleteConfirmation(<?php echo $row['id_lowongan']; ?>, '<?php echo addslashes($row['judul_lowongan']); ?>')"
                                style="background-color: #dc2626;"
                                class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium text-white transition rounded-lg hover:opacity-80">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20"
                                  fill="currentColor">
                                  <path fill-rule="evenodd"
                                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                                </svg>
                                Hapus
                              </button>
                            </div>
                          </td>
                        </tr>
                        <?php
                      }
                    } else {
                      echo "<tr><td colspan='6' class='text-center py-4'>Tidak ada data lowongan</td></tr>";
                    }
                    ?>
                  </tbody>
                </table>
              </div>

              <!-- DataTables Info and Pagination Container - Fixed -->
              <div id="tableFooter" class="mt-2">
                <!-- Info dan Pagination akan di-inject oleh DataTables di sini -->
              </div>
            </div>
          </div>
        </div>
      </main>
      <!-- ===== Main Content End ===== -->
    </div>
    <!-- ===== Content Area End ===== -->
  </div>

  <!-- Modal Edit Lowongan -->
  <div id="modalEditLowongan"
    class="modal-container fixed inset-0 hidden items-center justify-center transition-all duration-300 z-9999">
    <div class="absolute inset-0 bg-black/20" onclick="hideAllModals()"></div>
    <div
      class="modal-content bg-white dark:bg-boxdark w-11/12 max-w-3xl mx-4 rounded-xl shadow-xl relative flex flex-col overflow-hidden max-h-[90vh]">
      <div class="sticky top-0 z-10 bg-white dark:bg-boxdark border-b border-stroke dark:border-strokedark px-6 py-4">
        <div class="flex items-center justify-between">
          <div>
            <h5 class="text-lg font-bold text-black dark:text-white">Edit Lowongan</h5>
            <p class="text-xs text-gray-500">Edit data lowongan pekerjaan.</p>
          </div>
          <button onclick="hideAllModals()" class="text-gray-400 hover:text-red-500 transition-colors">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M18 6L6 18M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
      <div class="p-6 overflow-y-auto flex-1">
        <form id="editFormLowongan" method="POST" enctype="multipart/form-data" class="space-y-4">
          <input type="hidden" name="action" value="edit">
          <input type="hidden" name="id_lowongan" id="edit_id_lowongan">
          <input type="hidden" name="gambar_lama" id="edit_gambar_lama">

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-medium mb-1 dark:text-white">Judul Lowongan *</label>
              <input type="text" name="judul_lowongan" id="edit_judul_lowongan" required
                class="w-full rounded border border-stroke bg-transparent px-3 py-2 text-sm outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4">
            </div>

            <div>
              <label class="block text-xs font-medium mb-1 dark:text-white">Perusahaan *</label>
              <select name="id_perusahaan" id="edit_id_perusahaan" required
                class="w-full rounded border border-stroke bg-transparent px-3 py-2 text-sm outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4">
                <option value="">Pilih Perusahaan</option>
                <?php foreach ($data_perusahaan as $perusahaan): ?>
                  <option value="<?php echo $perusahaan['id_perusahaan']; ?>">
                    <?php echo htmlspecialchars($perusahaan['nama_perusahaan']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-medium mb-1 dark:text-white">Kategori *</label>
              <select name="kategori_lowongan" id="edit_kategori_lowongan" required
                class="w-full rounded border border-stroke bg-transparent px-3 py-2 text-sm outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4">
                <option value="">Pilih Kategori</option>
                <?php foreach ($data_kategori as $kategori): ?>
                  <option value="<?php echo $kategori['id_kategori']; ?>">
                    <?php echo htmlspecialchars($kategori['nama_kategori']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div>
              <label class="block text-xs font-medium mb-1 dark:text-white">Jenis Pekerjaan *</label>
              <select name="id_jenis" id="edit_id_jenis" required
                class="w-full rounded border border-stroke bg-transparent px-3 py-2 text-sm outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4">
                <option value="">Pilih Jenis</option>
                <?php foreach ($data_jenis as $jenis): ?>
                  <option value="<?php echo $jenis['id_jenis']; ?>">
                    <?php echo htmlspecialchars($jenis['nama_jenis']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-medium mb-1 dark:text-white">Provinsi</label>
              <select name="id_provinsi" id="edit_id_provinsi"
                class="w-full rounded border border-stroke bg-transparent px-3 py-2 text-sm outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4"
                onchange="loadKotaEditLowongan(this.value)">
                <option value="">Pilih Provinsi</option>
                <?php
                $data_provinsi = $db->tampil_data_provinsi();
                foreach ($data_provinsi as $provinsi) {
                  echo "<option value='" . $provinsi['id_provinsi'] . "'>" . $provinsi['nama_provinsi'] . "</option>";
                }
                ?>
              </select>
            </div>

            <div>
              <label class="block text-xs font-medium mb-1 dark:text-white">Kota</label>
              <select name="id_kota" id="edit_id_kota"
                class="w-full rounded border border-stroke bg-transparent px-3 py-2 text-sm outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4">
                <option value="">Pilih Kota</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block text-xs font-medium mb-1 dark:text-white">Alamat Lengkap</label>
            <textarea name="lokasi_lowongan" id="edit_lokasi_lowongan" rows="2"
              class="w-full rounded border border-stroke bg-transparent px-3 py-2 text-sm outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4"></textarea>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-medium mb-1 dark:text-white">Gaji</label>
              <input type="number" name="gaji_lowongan" id="edit_gaji_lowongan"
                class="w-full rounded border border-stroke bg-transparent px-3 py-2 text-sm outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4">
              <p class="text-xs text-gray-500 mt-1">Kosongkan jika "Negosiasi"</p>
            </div>

            <div>
              <label class="block text-xs font-medium mb-1 dark:text-white">Status *</label>
              <select name="status" id="edit_status" required
                class="w-full rounded border border-stroke bg-transparent px-3 py-2 text-sm outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4">
                <option value="Aktif">Aktif</option>
                <option value="Nonaktif">Nonaktif</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block text-xs font-medium mb-1 dark:text-white">Tanggal Tutup</label>
            <input type="date" name="tanggal_tutup" id="edit_tanggal_tutup"
              class="w-full rounded border border-stroke bg-transparent px-3 py-2 text-sm outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4">
          </div>

          <div>
            <label class="block text-xs font-medium mb-2 dark:text-white">Gambar Lowongan</label>
            <div class="space-y-3">
              <!-- Current Image Preview -->
              <div id="currentImage"
                class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600">
                <div class="text-center">
                  <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Gambar saat ini:</p>
                  <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                    <span class="text-xs text-gray-500">No image</span>
                  </div>
                </div>
              </div>

              <!-- File Upload -->
              <div class="flex items-center gap-3">
                <label for="fileInputEditLowongan"
                  class="cursor-pointer bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/20 dark:hover:bg-blue-900/30 px-4 py-2 rounded-lg border border-blue-200 dark:border-blue-700 text-xs font-medium text-blue-600 dark:text-blue-400 transition flex-1 text-center flex items-center justify-center gap-2">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                  </svg>
                  <span id="fileNameEditLowongan">Pilih Gambar...</span>
                </label>
                <input type="file" name="gambar" id="fileInputEditLowongan" class="hidden" accept="image/*"
                  onchange="updateFileName('fileInputEditLowongan', 'fileNameEditLowongan')">
              </div>
              <p class="text-xs text-gray-500 dark:text-gray-400">Format: JPG, PNG. Maks: 2MB</p>
            </div>
          </div>

          <div>
            <label class="block text-xs font-medium mb-1 dark:text-white">Deskripsi Lowongan</label>
            <textarea name="deskripsi_lowongan" id="edit_deskripsi_lowongan" rows="3"
              class="w-full rounded border border-stroke bg-transparent px-3 py-2 text-sm outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4"></textarea>
          </div>

          <div>
            <label class="block text-xs font-medium mb-1 dark:text-white">Kualifikasi</label>
            <textarea name="kualifikasi" id="edit_kualifikasi" rows="3"
              class="w-full rounded border border-stroke bg-transparent px-3 py-2 text-sm outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4"></textarea>
          </div>

          <div>
            <label class="block text-xs font-medium mb-1 dark:text-white">Pertanyaan (Opsional)</label>
            <textarea name="pertanyaan" id="edit_pertanyaan" rows="2"
              class="w-full rounded border border-stroke bg-transparent px-3 py-2 text-sm outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4"></textarea>
          </div>

          <div class="flex justify-end gap-2 pt-4">
            <button type="button" onclick="hideAllModals()"
              class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded hover:bg-gray-200 dark:bg-meta-4 dark:text-gray-300">Batal</button>
            <button type="submit"
              class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white transition rounded-lg bg-brand-500 hover:bg-brand-600">
              <svg class="fill-current" width="14" height="14" viewBox="0 0 20 20" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd"
                  d="M16.7071 5.29289C17.0976 5.68342 17.0976 6.31658 16.7071 6.70711L8.70711 14.7071C8.31658 15.0976 7.68342 15.0976 7.29289 14.7071L3.29289 10.7071C2.90237 10.3166 2.90237 9.68342 3.29289 9.29289C3.68342 8.90237 4.31658 8.90237 4.70711 9.29289L8 12.5858L15.2929 5.29289C15.6834 4.90237 16.3166 4.90237 16.7071 5.29289Z"
                  fill="white" />
              </svg>
              Update Lowongan
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Tambah Lowongan -->
  <div id="modalTambahLowongan"
    class="modal-container fixed inset-0 hidden items-center justify-center transition-all duration-300 z-9999">
    <div class="absolute inset-0 bg-black/20" onclick="hideAllModals()"></div>
    <div
      class="modal-content bg-white dark:bg-boxdark w-11/12 max-w-3xl mx-4 rounded-xl shadow-xl relative flex flex-col overflow-hidden max-h-[90vh]">
      <div class="sticky top-0 z-10 bg-white dark:bg-boxdark border-b border-stroke dark:border-strokedark px-6 py-4">
        <div class="flex items-center justify-between">
          <div>
            <h5 class="text-lg font-bold text-black dark:text-white">Tambah Lowongan</h5>
            <p class="text-xs text-gray-500">Tambah data lowongan pekerjaan baru.</p>
          </div>
          <button onclick="hideAllModals()" class="text-gray-400 hover:text-red-500 transition-colors">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M18 6L6 18M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
      <div class="p-6 overflow-y-auto flex-1">
        <form id="tambahFormLowongan" method="POST" enctype="multipart/form-data" class="space-y-4">
          <input type="hidden" name="action" value="tambah">

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-medium mb-1 dark:text-white">Judul Lowongan *</label>
              <input type="text" name="judul_lowongan" required
                class="w-full rounded border border-stroke bg-transparent px-3 py-2 text-sm outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4">
            </div>

            <div>
              <label class="block text-xs font-medium mb-1 dark:text-white">Perusahaan *</label>
              <select name="id_perusahaan" required
                class="w-full rounded border border-stroke bg-transparent px-3 py-2 text-sm outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4">
                <option value="">Pilih Perusahaan</option>
                <?php foreach ($data_perusahaan as $perusahaan): ?>
                  <option value="<?php echo $perusahaan['id_perusahaan']; ?>">
                    <?php echo htmlspecialchars($perusahaan['nama_perusahaan']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-medium mb-1 dark:text-white">Kategori *</label>
              <select name="kategori_lowongan" required
                class="w-full rounded border border-stroke bg-transparent px-3 py-2 text-sm outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4">
                <option value="">Pilih Kategori</option>
                <?php foreach ($data_kategori as $kategori): ?>
                  <option value="<?php echo $kategori['id_kategori']; ?>">
                    <?php echo htmlspecialchars($kategori['nama_kategori']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div>
              <label class="block text-xs font-medium mb-1 dark:text-white">Jenis Pekerjaan *</label>
              <select name="id_jenis" required
                class="w-full rounded border border-stroke bg-transparent px-3 py-2 text-sm outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4">
                <option value="">Pilih Jenis</option>
                <?php foreach ($data_jenis as $jenis): ?>
                  <option value="<?php echo $jenis['id_jenis']; ?>">
                    <?php echo htmlspecialchars($jenis['nama_jenis']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-medium mb-1 dark:text-white">Provinsi</label>
              <select name="id_provinsi"
                class="w-full rounded border border-stroke bg-transparent px-3 py-2 text-sm outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4"
                onchange="loadKotaTambahLowongan(this.value)">
                <option value="">Pilih Provinsi</option>
                <?php
                $data_provinsi = $db->tampil_data_provinsi();
                foreach ($data_provinsi as $provinsi) {
                  echo "<option value='" . $provinsi['id_provinsi'] . "'>" . $provinsi['nama_provinsi'] . "</option>";
                }
                ?>
              </select>
            </div>

            <div>
              <label class="block text-xs font-medium mb-1 dark:text-white">Kota</label>
              <select name="id_kota" id="tambah_id_kota"
                class="w-full rounded border border-stroke bg-transparent px-3 py-2 text-sm outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4">
                <option value="">Pilih Kota</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block text-xs font-medium mb-1 dark:text-white">Alamat Lengkap</label>
            <textarea name="lokasi_lowongan" rows="2"
              class="w-full rounded border border-stroke bg-transparent px-3 py-2 text-sm outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4"></textarea>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-medium mb-1 dark:text-white">Gaji</label>
              <input type="number" name="gaji_lowongan"
                class="w-full rounded border border-stroke bg-transparent px-3 py-2 text-sm outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4">
              <p class="text-xs text-gray-500 mt-1">Kosongkan jika "Negosiasi"</p>
            </div>

            <div>
              <label class="block text-xs font-medium mb-1 dark:text-white">Status *</label>
              <select name="status" required
                class="w-full rounded border border-stroke bg-transparent px-3 py-2 text-sm outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4">
                <option value="Aktif">Aktif</option>
                <option value="Nonaktif">Nonaktif</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block text-xs font-medium mb-1 dark:text-white">Tanggal Tutup</label>
            <input type="date" name="tanggal_tutup"
              class="w-full rounded border border-stroke bg-transparent px-3 py-2 text-sm outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4">
          </div>

          <div>
            <label class="block text-xs font-medium mb-2 dark:text-white">Gambar Lowongan</label>
            <div class="space-y-3">
              <div class="flex items-center gap-3">
                <label for="fileInputTambahLowongan"
                  class="cursor-pointer bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/20 dark:hover:bg-blue-900/30 px-4 py-2 rounded-lg border border-blue-200 dark:border-blue-700 text-xs font-medium text-blue-600 dark:text-blue-400 transition flex-1 text-center flex items-center justify-center gap-2">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                  </svg>
                  <span id="fileNameTambahLowongan">Pilih Gambar...</span>
                </label>
                <input type="file" name="gambar" id="fileInputTambahLowongan" class="hidden" accept="image/*"
                  onchange="updateFileName('fileInputTambahLowongan', 'fileNameTambahLowongan')">
              </div>
              <p class="text-xs text-gray-500 dark:text-gray-400">Format: JPG, PNG. Maks: 2MB</p>
            </div>
          </div>

          <div>
            <label class="block text-xs font-medium mb-1 dark:text-white">Deskripsi Lowongan</label>
            <textarea name="deskripsi_lowongan" rows="3"
              class="w-full rounded border border-stroke bg-transparent px-3 py-2 text-sm outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4"></textarea>
          </div>

          <div>
            <label class="block text-xs font-medium mb-1 dark:text-white">Kualifikasi</label>
            <textarea name="kualifikasi" rows="3"
              class="w-full rounded border border-stroke bg-transparent px-3 py-2 text-sm outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4"></textarea>
          </div>

          <div>
            <label class="block text-xs font-medium mb-1 dark:text-white">Pertanyaan (Opsional)</label>
            <textarea name="pertanyaan" rows="2"
              class="w-full rounded border border-stroke bg-transparent px-3 py-2 text-sm outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4"></textarea>
          </div>

          <div class="flex justify-end gap-2 pt-4">
            <button type="button" onclick="hideAllModals()"
              class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded hover:bg-gray-200 dark:bg-meta-4 dark:text-gray-300">Batal</button>
            <button type="submit"
              class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white transition rounded-lg bg-brand-500 hover:bg-brand-600">
              <svg class="fill-current" width="14" height="14" viewBox="0 0 20 20" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd"
                  d="M16.7071 5.29289C17.0976 5.68342 17.0976 6.31658 16.7071 6.70711L8.70711 14.7071C8.31658 15.0976 7.68342 15.0976 7.29289 14.7071L3.29289 10.7071C2.90237 10.3166 2.90237 9.68342 3.29289 9.29289C3.68342 8.90237 4.31658 8.90237 4.70711 9.29289L8 12.5858L15.2929 5.29289C15.6834 4.90237 16.3166 4.90237 16.7071 5.29289Z"
                  fill="white" />
              </svg>
              Tambah Lowongan
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Konfirmasi Hapus -->
  <div id="modalHapusLowongan"
    class="fixed inset-0 hidden items-center justify-center transition-all duration-300 z-9999">
    <div class="absolute inset-0 bg-black/20" onclick="hideAllModals()"></div>
    <div
      class="relative bg-white dark:bg-boxdark w-full max-w-xs mx-4 rounded-xl shadow-xl flex flex-col overflow-hidden">
      <button onclick="hideAllModals()"
        class="absolute top-3 right-3 text-gray-400 hover:text-red-500 transition-colors z-10">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path d="M18 6L6 18M6 6l12 12" />
        </svg>
      </button>
      <div class="p-5">
        <div class="mx-auto w-12 h-12 mb-3 rounded-full bg-red-100 flex items-center justify-center">
          <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.994-.833-2.764 0L4.406 16.5c-.77.833.192 2.5 1.732 2.5z" />
          </svg>
        </div>
        <div class="text-center mb-4">
          <h5 class="text-lg font-bold text-black dark:text-white mb-1">Konfirmasi Hapus</h5>
          <p class="text-sm text-gray-600 dark:text-gray-400" id="hapusMessageLowongan">
            Apakah Anda yakin ingin menghapus data ini?
          </p>
        </div>
        <div class="flex justify-center gap-2">
          <button type="button" onclick="hideAllModals()"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
            Batal
          </button>
          <a href="#" id="confirmDeleteLink"
            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white transition rounded-lg bg-red-600 hover:bg-red-700">
            Ya, Hapus
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Export Data Lowongan -->
  <div id="modalExportLowongan"
    class="modal-container fixed inset-0 hidden items-center justify-center transition-all duration-300 z-9999">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="toggleModal('modalExportLowongan')"></div>
    <div
      class="modal-content bg-white dark:bg-boxdark w-full max-w-md mx-4 rounded-2xl shadow-2xl relative flex flex-col overflow-hidden max-h-[90vh]">
      <!-- Modal Header -->
      <div
        class="sticky top-0 z-10 bg-gradient-to-r from-blue-600 to-blue-700 dark:from-blue-800 dark:to-blue-900 px-6 py-5">
        <div class="flex items-center justify-between">
          <div>
            <h4 class="text-lg font-bold text-white">Export Data Lowongan</h4>
            <p class="text-xs text-blue-100 mt-1">Pilih filter dan format export data</p>
          </div>
          <button onclick="toggleModal('modalExportLowongan')"
            class="text-white/80 hover:text-white transition-colors p-2 hover:bg-white/10 rounded-lg">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M18 6L6 18M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Modal Body -->
      <div class="p-6 overflow-y-auto">
        <form id="formExportLowongan" action="export_lowongan.php" method="POST" class="space-y-6">
          <!-- Filter Section -->
          <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-5 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-2 mb-4">
              <div class="w-5 h-5 bg-blue-100 dark:bg-blue-900/30 rounded flex items-center justify-center">
                <svg class="w-3 h-3 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                  viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
              </div>
              <h6 class="text-sm font-semibold text-gray-800 dark:text-gray-200">Filter Data</h6>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <!-- Filter by ID Range -->
              <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">ID Min</label>
                <input type="number" name="id_min"
                  class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                  placeholder="ID minimum" min="0" oninput="this.value = Math.abs(this.value)">
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">ID Max</label>
                <input type="number" name="id_max"
                  class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                  placeholder="ID maksimum" min="0" oninput="this.value = Math.abs(this.value)">
              </div>

              <!-- Filter by Judul Lowongan -->
              <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Judul Lowongan</label>
                <input type="text" name="filter_judul"
                  class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                  placeholder="Cari judul lowongan...">
              </div>

              <!-- Filter by Perusahaan -->
              <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Perusahaan</label>
                <input type="text" name="filter_perusahaan"
                  class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                  placeholder="Cari nama perusahaan...">
              </div>

              <!-- Filter by Kategori -->
              <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Kategori</label>
                <select name="filter_kategori"
                  class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                  <option value="">Semua Kategori</option>
                  <?php foreach ($data_kategori as $kategori): ?>
                    <option value="<?php echo htmlspecialchars($kategori['nama_kategori']); ?>">
                      <?php echo htmlspecialchars($kategori['nama_kategori']); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <!-- Filter by Status -->
              <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                <select name="filter_status"
                  class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                  <option value="">Semua Status</option>
                  <option value="Aktif">Aktif</option>
                  <option value="Nonaktif">Nonaktif</option>
                </select>
              </div>

              <!-- Filter by Tanggal -->
              <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Tanggal Dari</label>
                <input type="date" name="tanggal_dari"
                  class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Tanggal Sampai</label>
                <input type="date" name="tanggal_sampai"
                  class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
              </div>
            </div>
          </div>

          <!-- Column Selection Section -->
          <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-5 border border-blue-200 dark:border-blue-800">
            <div class="flex items-center gap-2 mb-4">
              <div class="w-5 h-5 bg-blue-100 dark:bg-blue-900/30 rounded flex items-center justify-center">
                <svg class="w-3 h-3 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                  viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
              </div>
              <h6 class="text-sm font-semibold text-gray-800 dark:text-gray-200">Pilih Kolom</h6>
            </div>

            <div class="space-y-3">
              <label
                class="flex items-center gap-3 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <input type="checkbox" name="columns[]" value="id_lowongan" checked
                  class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                <div class="flex-1">
                  <span class="text-sm font-medium text-gray-700 dark:text-gray-300">ID Lowongan</span>
                  <p class="text-xs text-gray-500">Nomor identifikasi lowongan</p>
                </div>
              </label>

              <label
                class="flex items-center gap-3 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <input type="checkbox" name="columns[]" value="nama_perusahaan" checked
                  class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                <div class="flex-1">
                  <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Perusahaan</span>
                  <p class="text-xs text-gray-500">Nama perusahaan</p>
                </div>
              </label>

              <label
                class="flex items-center gap-3 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <input type="checkbox" name="columns[]" value="judul_lowongan" checked
                  class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                <div class="flex-1">
                  <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Judul Lowongan</span>
                  <p class="text-xs text-gray-500">Judul posisi lowongan</p>
                </div>
              </label>

              <label
                class="flex items-center gap-3 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <input type="checkbox" name="columns[]" value="kategori_lowongan" checked
                  class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                <div class="flex-1">
                  <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Kategori</span>
                  <p class="text-xs text-gray-500">Jenis pekerjaan</p>
                </div>
              </label>

              <label
                class="flex items-center gap-3 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <input type="checkbox" name="columns[]" value="waktukerja" checked
                  class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                <div class="flex-1">
                  <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Waktu Kerja</span>
                  <p class="text-xs text-gray-500">Jam kerja</p>
                </div>
              </label>

              <label
                class="flex items-center gap-3 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <input type="checkbox" name="columns[]" value="lokasi_lowongan" checked
                  class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                <div class="flex-1">
                  <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Lokasi</span>
                  <p class="text-xs text-gray-500">Lokasi pekerjaan</p>
                </div>
              </label>

              <label
                class="flex items-center gap-3 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <input type="checkbox" name="columns[]" value="gaji_lowongan" checked
                  class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                <div class="flex-1">
                  <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Gaji</span>
                  <p class="text-xs text-gray-500">Range gaji</p>
                </div>
              </label>

              <label
                class="flex items-center gap-3 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <input type="checkbox" name="columns[]" value="tanggal_tutup" checked
                  class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                <div class="flex-1">
                  <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Tutup</span>
                  <p class="text-xs text-gray-500">Batas pendaftaran</p>
                </div>
              </label>

              <label
                class="flex items-center gap-3 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <input type="checkbox" name="columns[]" value="status" checked
                  class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                <div class="flex-1">
                  <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Status</span>
                  <p class="text-xs text-gray-500">Status lowongan</p>
                </div>
              </label>
            </div>
          </div>

          <!-- Export Format Section -->
          <div class="bg-green-50 dark:bg-green-900/20 rounded-xl p-5 border border-green-200 dark:border-green-800">
            <div class="flex items-center gap-2 mb-4">
              <div class="w-5 h-5 bg-green-100 dark:bg-green-900/30 rounded flex items-center justify-center">
                <svg class="w-3 h-3 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                  viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                </svg>
              </div>
              <h6 class="text-sm font-semibold text-gray-800 dark:text-gray-200">Format Export</h6>
            </div>

            <div class="flex gap-3 justify-center py-3">
              <button type="submit" name="format" value="csv"
                class="flex items-center gap-2 px-4 py-2 rounded-lg font-medium text-sm transition-colors shadow-sm"
                style="background-color: #16a34a; color: white;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                CSV
              </button>

              <button type="submit" name="format" value="excel"
                class="flex items-center gap-2 px-4 py-2 rounded-lg font-medium text-sm transition-colors shadow-sm"
                style="background-color: #2563eb; color: white;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 17v1a1 1 0 001 1h4a1 1 0 001-1v-1m-6 0h6m-6 0V9a2 2 0 012-2h2a2 2 0 012 2v8m-6 0h6" />
                </svg>
                Excel
              </button>

              <button type="submit" name="format" value="pdf"
                class="flex items-center gap-2 px-4 py-2 rounded-lg font-medium text-sm transition-colors shadow-sm"
                style="background-color: #dc2626; color: white;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0112.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                PDF
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Alert Container -->
  <div id="alertContainer" class="alert-container"></div>

  <!-- ===== Page Wrapper End ===== -->
  <script defer src="bundle.js"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
  <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
  <script src="https://cdn.datatables.net/2.0.8/js/dataTables.tailwindcss.js"></script>

  <script>
    $(document).ready(function () {
      $('#lowonganTable').DataTable({
        "dom": '<"flex flex-wrap items-center justify-between gap-3 mb-2"lf>rt<"flex flex-wrap items-center justify-between gap-3 mt-2"ip>',
        "language": {
          "search": "",
          "searchPlaceholder": "Cari lowongan...",
          "lengthMenu": "Tampilkan _MENU_ data",
          "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
          "paginate": {
            "previous": "‹",
            "next": "›"
          }
        },
        "pageLength": 5,
        "responsive": true,
        "order": [[0, "desc"]],
        "columnDefs": [
          {
            "targets": 5, // Kolom aksi (index ke-5)
            "orderable": false,
            "searchable": false,
            "className": "action-column"
          }
        ],
        "initComplete": function () {
          // Pindahkan kontrol DataTables ke container kustom
          var $wrapper = $('#lowonganTable_wrapper');

          // Pindahkan Length Menu dan Search ke container atas
          $wrapper.find('.dt-length, .dt-search').appendTo('#tableControls');
          $('#tableControls').addClass('flex flex-wrap items-center justify-between gap-3');

          // Pindahkan Info dan Pagination ke container bawah
          $wrapper.find('.dt-info, .dt-paging').appendTo('#tableFooter');
          $('#tableFooter').addClass('flex flex-wrap items-center justify-between gap-3');

          // Styling untuk DataTables
          $('.dt-length').addClass('text-xs');
          $('.dt-info').addClass('text-xs');
          $('.dt-paging').addClass('text-xs');

          // PERBAIKAN: Tambahkan h-7 untuk tinggi yang cukup
          $('.dt-search input').addClass('rounded-lg border border-stroke bg-transparent py-1 px-2 outline-none focus:border-primary dark:border-strokedark dark:bg-meta-4 text-xs h-7');
          $('.dt-length select').addClass('rounded-lg border border-stroke bg-transparent py-1 px-2 outline-none dark:border-strokedark dark:bg-meta-4 text-xs h-7');

          $('.dt-paging .dt-paging-button').addClass('rounded-md border border-stroke bg-transparent px-1.5 py-0.5 text-[0.6875rem] h-6 min-w-6 mx-0.5 dark:border-strokedark dark:bg-meta-4');
          $('.dt-paging .dt-paging-button.current').addClass('bg-blue-500 text-white border-blue-500');

          $('.dt-paging .dt-paging-button:not(.current)').hover(
            function () {
              $(this).addClass('bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200');
            },
            function () {
              $(this).removeClass('bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200');
            }
          );

          $('.dt-paging.paging_simple_numbers').addClass('flex items-center gap-1');
          $('.dt-info').css('margin-right', '1rem');

          // Hapus wrapper asli untuk mencegah duplikasi
          $wrapper.find('.dt-length, .dt-search, .dt-info, .dt-paging').remove();
        }
      });
    });

    // ========== ALERT SYSTEM FUNCTIONS - Sesuai Template ==========
    function showNotification(type, message, title) {
      const alertContainer = document.getElementById('alertContainer');

      // Create alert element
      const alertElement = document.createElement('div');
      alertElement.className = `alert-item ${type}`;

      // Get icon based on type - Sesuai Template
      let iconSvg = '';
      let iconColor = '';

      switch (type) {
        case 'success':
          iconSvg = `<svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M3.70186 12.0001C3.70186 7.41711 7.41711 3.70186 12.0001 3.70186C16.5831 3.70186 20.2984 7.41711 20.2984 12.0001C20.2984 16.5831 16.5831 20.2984 12.0001 20.2984C7.41711 20.2984 3.70186 16.5831 3.70186 12.0001ZM12.0001 1.90186C6.423 1.90186 1.90186 6.423 1.90186 12.0001C1.90186 17.5772 6.423 22.0984 12.0001 22.0984C17.5772 22.0984 22.0984 17.5772 22.0984 12.0001C22.0984 6.423 17.5772 1.90186 12.0001 1.90186ZM15.6197 10.7395C15.9712 10.388 15.9712 9.81819 15.6197 9.46672C15.2683 9.11525 14.6984 9.11525 14.347 9.46672L11.1894 12.6243L9.6533 11.0883C9.30183 10.7368 8.73198 10.7368 8.38051 11.0883C8.02904 11.4397 8.02904 12.0096 8.38051 12.3611L10.553 14.5335C10.7217 14.7023 10.9507 14.7971 11.1894 14.7971C11.428 14.7971 11.657 14.7023 11.8257 14.5335L15.6197 10.7395Z" fill="" />
                      </svg>`;
          iconColor = 'text-success-500';
          break;
        case 'error':
          iconSvg = `<svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M20.3499 12.0004C20.3499 16.612 16.6115 20.3504 11.9999 20.3504C7.38832 20.3504 3.6499 16.612 3.6499 12.0004C3.6499 7.38881 7.38833 3.65039 11.9999 3.65039C16.6115 3.65039 20.3499 7.38881 20.3499 12.0004ZM11.9999 22.1504C17.6056 22.1504 22.1499 17.6061 22.1499 12.0004C22.1499 6.3947 17.6056 1.85039 11.9999 1.85039C6.39421 1.85039 1.8499 6.3947 1.8499 12.0004C1.8499 17.6061 6.39421 22.1504 11.9999 22.1504ZM13.0008 16.4753C13.0008 15.923 12.5531 15.4753 12.0008 15.4753L11.9998 15.4753C11.4475 15.4753 10.9998 15.923 10.9998 16.4753C10.9998 17.0276 11.4475 17.4753 11.9998 17.4753L12.0008 17.4753C12.5531 17.4753 13.0008 17.0276 13.0008 16.4753ZM11.9998 6.62898C12.414 6.62898 12.7498 6.96476 12.7498 7.37898L12.7498 13.0555C12.7498 13.4697 12.414 13.8055 11.9998 13.8055C11.5856 13.8055 11.2498 13.4697 11.2498 13.0555L11.2498 7.37898C11.2498 6.96476 11.5856 6.62898 11.9998 6.62898Z" fill="#F04438" />
                      </svg>`;
          iconColor = 'text-error-500';
          break;
        case 'warning':
          iconSvg = `<svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M3.6501 12.0004C3.6501 7.38852 7.38852 3.6501 12.0001 3.6501C16.6117 3.6501 20.3501 7.38852 20.3501 12.0001C20.3501 16.6117 16.6117 20.3501 12.0001 20.3501C7.38852 20.3501 3.6501 16.6117 3.6501 12.0001ZM12.0001 1.8501C6.39441 1.8501 1.8501 6.39441 1.8501 12.0001C1.8501 17.6058 6.39441 22.1501 12.0001 22.1501C17.6058 22.1501 22.1501 17.6058 22.1501 12.0001C22.1501 6.39441 17.6058 1.8501 12.0001 1.8501ZM10.9992 7.52517C10.9992 8.07746 11.4469 8.52517 11.9992 8.52517H12.0002C12.5525 8.52517 13.0002 8.07746 13.0002 7.52517C13.0002 6.97289 12.5525 6.52517 12.0002 6.52517H11.9992C11.4469 6.52517 10.9992 6.97289 10.9992 7.52517ZM12.0002 17.3715C11.586 17.3715 11.2502 17.0357 11.2502 16.6215V10.945C11.2502 10.5308 11.586 10.195 12.0002 10.195C12.4144 10.195 12.7502 10.5308 12.7502 10.945V16.6215C12.7502 17.0357 12.4144 17.3715 12.0002 17.3715Z" fill="" />
                      </svg>`;
          iconColor = 'text-warning-500 dark:text-orange-400';
          break;
        case 'info':
          iconSvg = `<svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M3.6501 11.9996C3.6501 7.38803 7.38852 3.64961 12.0001 3.64961C16.6117 3.64961 20.3501 7.38803 20.3501 11.9996C20.3501 16.6112 16.6117 20.3496 12.0001 20.3496C7.38852 20.3496 3.6501 16.6112 3.6501 11.9996ZM12.0001 1.84961C6.39441 1.84961 1.8501 6.39392 1.8501 11.9996C1.8501 17.6053 6.39441 22.1496 12.0001 22.1496C17.6058 22.1496 22.1501 17.6053 22.1501 11.9996C22.1501 6.39392 17.6058 1.84961 12.0001 1.84961ZM10.9992 7.52468C10.9992 8.07697 11.4469 8.52468 11.9992 8.52468H12.0002C12.5525 8.52468 13.0002 8.07697 13.0002 7.52468C13.0002 6.9724 12.5525 6.52468 12.0002 6.52468H11.9992C11.4469 6.52468 10.9992 6.9724 10.9992 7.52468ZM12.0002 17.371C11.586 17.371 11.2502 17.0352 11.2502 16.621V10.9445C11.2502 10.5303 11.586 10.1945 12.0002 10.1945C12.4144 10.1945 12.7502 10.5303 12.7502 10.9445V16.621C12.7502 17.0352 12.4144 17.371 12.0002 17.371Z" fill="" />
                      </svg>`;
          iconColor = 'text-blue-light-500';
          break;
        default:
          iconSvg = `<svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M3.6501 11.9996C3.6501 7.38803 7.38852 3.64961 12.0001 3.64961C16.6117 3.64961 20.3501 7.38803 20.3501 11.9996C20.3501 16.6112 16.6117 20.3496 12.0001 20.3496C7.38852 20.3496 3.6501 16.6112 3.6501 11.9996ZM12.0001 1.84961C6.39441 1.84961 1.8501 6.39392 1.8501 11.9996C1.8501 17.6053 6.39441 22.1496 12.0001 22.1496C17.6058 22.1496 22.1501 17.6053 22.1501 11.9996C22.1501 6.39392 17.6058 1.84961 12.0001 1.84961ZM10.9992 7.52468C10.9992 8.07697 11.4469 8.52468 11.9992 8.52468H12.0002C12.5525 8.52468 13.0002 8.07697 13.0002 7.52468C13.0002 6.9724 12.5525 6.52468 12.0002 6.52468H11.9992C11.4469 6.52468 10.9992 6.9724 10.9992 7.52468ZM12.0002 17.371C11.586 17.371 11.2502 17.0352 11.2502 16.621V10.9445C11.2502 10.5303 11.586 10.1945 12.0002 10.1945C12.4144 10.1945 12.7502 10.5303 12.7502 10.9445V16.621C12.7502 17.0352 12.4144 17.371 12.0002 17.371Z" fill="" />
                      </svg>`;
          iconColor = 'text-blue-light-500';
      }

      // Build alert HTML - Sesuai Template
      let alertHTML = `
                <div class="flex items-start gap-3">
                    <div class="-mt-0.5 ${iconColor}">
                        ${iconSvg}
                    </div>
                    <div class="flex-1">
                        <h4 class="mb-1 text-sm font-semibold text-gray-800 dark:text-white/90">
                            ${title}
                        </h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            ${message}
                        </p>
                    </div>
                    <button onclick="closeAlert(this)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            `;

      alertElement.innerHTML = alertHTML;

      // Add to container
      alertContainer.appendChild(alertElement);

      // Show animation
      setTimeout(() => {
        alertElement.classList.add('show');
      }, 10);

      // Auto hide after 5 seconds
      setTimeout(() => {
        closeAlert(alertElement.querySelector('button'));
      }, 5000);
    }

    function closeAlert(button) {
      const alertItem = button.closest('.alert-item');
      if (alertItem) {
        alertItem.classList.add('hide');
        setTimeout(() => {
          if (alertItem.parentNode) {
            alertItem.parentNode.removeChild(alertItem);
          }
        }, 300);
      }
    }

    function toggleModal(modalId) {
      const modal = document.getElementById(modalId);
      const overlay = document.getElementById('pageOverlay');

      if (modal.style.display === 'none' || modal.classList.contains('hidden')) {
        // Buka modal
        modal.style.display = 'flex';
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        if (overlay) {
          overlay.classList.remove('hidden');
          overlay.classList.add('block');
        }

        document.body.style.overflow = 'hidden';
      } else {
        // Tutup modal
        modal.style.display = 'none';
        modal.classList.add('hidden');
        modal.classList.remove('flex');

        if (overlay) {
          overlay.classList.add('hidden');
          overlay.classList.remove('block');
        }

        document.body.style.overflow = 'auto';
      }
    }

    function hideAllModals() {
      const modals = ['modalEditLowongan', 'modalHapusLowongan', 'modalExportLowongan', 'modalTambahLowongan'];
      modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal) {
          modal.style.display = 'none';
          modal.classList.add('hidden');
          modal.classList.remove('flex');
        }
      });

      const overlay = document.getElementById('pageOverlay');
      if (overlay) {
        overlay.classList.add('hidden');
        overlay.classList.remove('block');
      }

      document.body.style.overflow = 'auto';
    }

    function updateFileName(inputId, labelId) {
      const input = document.getElementById(inputId);
      const label = document.getElementById(labelId);
      if (input.files.length > 0) {
        label.innerText = input.files[0].name;
      } else {
        label.innerText = "Pilih Gambar...";
      }
    }

    async function showEditForm(id) {
      try {
        // Fetch data lowongan via AJAX menggunakan handler yang sudah ditambahkan di atas
        const response = await fetch(`datalowongan.php?action=get&id=${id}`);
        const data = await response.json();

        if (data.success) {
          const lowongan = data.data;

          // Isi form dengan data
          document.getElementById('edit_id_lowongan').value = lowongan.id_lowongan;
          document.getElementById('edit_judul_lowongan').value = lowongan.judul_lowongan;
          document.getElementById('edit_id_perusahaan').value = lowongan.id_perusahaan;
          document.getElementById('edit_kategori_lowongan').value = lowongan.kategori_lowongan;
          document.getElementById('edit_id_jenis').value = lowongan.id_jenis; // Update: dari waktukerja menjadi id_jenis
          document.getElementById('edit_id_provinsi').value = lowongan.id_provinsi || '';

          // Load kota berdasarkan provinsi
          if (lowongan.id_provinsi) {
            await loadKotaEditLowongan(lowongan.id_provinsi);
            // Setelah load kota, set nilai kota
            setTimeout(() => {
              document.getElementById('edit_id_kota').value = lowongan.id_kota || '';
            }, 500);
          }

          document.getElementById('edit_lokasi_lowongan').value = lowongan.lokasi_lowongan || '';
          document.getElementById('edit_gaji_lowongan').value = lowongan.gaji_lowongan;
          document.getElementById('edit_kualifikasi').value = lowongan.kualifikasi || '';
          document.getElementById('edit_deskripsi_lowongan').value = lowongan.deskripsi_lowongan || '';
          document.getElementById('edit_pertanyaan').value = lowongan.pertanyaan || '';
          document.getElementById('edit_tanggal_tutup').value = lowongan.tanggal_tutup;
          document.getElementById('edit_status').value = lowongan.status;

          // Handle gambar
          if (lowongan.gambar) {
            document.getElementById('edit_gambar_lama').value = lowongan.gambar;
            const currentImage = document.getElementById('currentImage');
            currentImage.innerHTML = `
                            <div class="text-center">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Gambar saat ini:</p>
                                <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-lg overflow-hidden flex items-center justify-center">
                                    <img src="src/images/jobs/${lowongan.gambar}" alt="Current" class="w-full h-full object-cover">
                                </div>
                            </div>
                        `;
          } else {
            document.getElementById('edit_gambar_lama').value = '';
            const currentImage = document.getElementById('currentImage');
            currentImage.innerHTML = `
                            <div class="text-center">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Gambar saat ini:</p>
                                <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                    <span class="text-xs text-gray-500">No image</span>
                                </div>
                            </div>
                        `;
          }

          toggleModal('modalEditLowongan');
        } else {
          showNotification('error', data.message || 'Gagal memuat data lowongan', 'Error');
        }
      } catch (error) {
        console.error('Error:', error);
        showNotification('error', 'Terjadi kesalahan saat memuat data', 'Error Server');
      }
    }

    // Handler untuk form edit lowongan
    document.addEventListener('DOMContentLoaded', function () {
      const formEditLowongan = document.getElementById('editFormLowongan');
      if (formEditLowongan) {
        formEditLowongan.addEventListener('submit', function (e) {
          e.preventDefault();

          const submitBtn = formEditLowongan.querySelector('button[type="submit"]');
          const originalText = submitBtn.innerHTML;

          // Tampilkan loading
          submitBtn.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memperbarui...';
          submitBtn.disabled = true;

          const formData = new FormData(formEditLowongan);

          fetch('datalowongan.php', {
            method: 'POST',
            body: formData
          })
            .then(response => response.json())
            .then(result => {
              if (result.success) {
                showNotification('success', 'Data lowongan berhasil diperbarui!', 'Edit Berhasil');
                toggleModal('modalEditLowongan');
                // Auto refresh setelah 2 detik
                setTimeout(() => {
                  location.reload();
                }, 2000);
              } else {
                showNotification('error', result.message || 'Gagal mengupdate data lowongan', 'Edit Gagal');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
              }
            })
            .catch(error => {
              console.error('Error:', error);
              showNotification('error', 'Terjadi kesalahan saat mengupdate data', 'Error Server');
              submitBtn.innerHTML = originalText;
              submitBtn.disabled = false;
            });
        });
      }
    });

    // Handler untuk form tambah lowongan
    document.addEventListener('DOMContentLoaded', function () {
      const formTambahLowongan = document.getElementById('tambahFormLowongan');
      if (formTambahLowongan) {
        formTambahLowongan.addEventListener('submit', function (e) {
          e.preventDefault();

          const submitBtn = formTambahLowongan.querySelector('button[type="submit"]');
          const originalText = submitBtn.innerHTML;

          // Tampilkan loading
          submitBtn.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Menambahkan...';
          submitBtn.disabled = true;

          const formData = new FormData(formTambahLowongan);

          fetch('datalowongan.php', {
            method: 'POST',
            body: formData
          })
            .then(response => response.json())
            .then(result => {
              if (result.success) {
                showNotification('success', 'Data lowongan berhasil ditambahkan!', 'Tambah Berhasil');
                toggleModal('modalTambahLowongan');
                // Auto refresh setelah 2 detik
                setTimeout(() => {
                  location.reload();
                }, 2000);
              } else {
                showNotification('error', result.message || 'Gagal menambah data lowongan', 'Tambah Gagal');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
              }
            })
            .catch(error => {
              console.error('Error:', error);
              showNotification('error', 'Terjadi kesalahan saat menambah data', 'Error Server');
              submitBtn.innerHTML = originalText;
              submitBtn.disabled = false;
            });
        });
      }
    });

    function showDeleteConfirmation(id, judul) {
      const message = document.getElementById('hapusMessageLowongan');
      message.textContent = `Apakah Anda yakin ingin menghapus lowongan "${judul}"?`;

      const deleteLink = document.getElementById('confirmDeleteLink');

      // Hapus onclick lama dan set yang baru
      deleteLink.onclick = function (e) {
        e.preventDefault();
        const originalText = deleteLink.innerHTML;
        deleteLink.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Menghapus...';
        deleteLink.style.pointerEvents = 'none';
        deleteLink.classList.add('opacity-50', 'cursor-not-allowed');

        fetch(`datalowongan.php?action=hapus&id=${id}`, {
          method: 'GET'
        })
          .then(response => response.json())
          .then(result => {
            if (result.success) {
              showNotification('success', 'Data lowongan berhasil dihapus!', 'Hapus Berhasil');
              toggleModal('modalHapusLowongan');
              // Auto refresh setelah 2 detik
              setTimeout(() => {
                location.reload();
              }, 2000);
            } else {
              showNotification('error', result.message || 'Gagal menghapus data lowongan', 'Hapus Gagal');
              deleteLink.innerHTML = originalText;
              deleteLink.style.pointerEvents = 'auto';
              deleteLink.classList.remove('opacity-50', 'cursor-not-allowed');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            showNotification('error', 'Terjadi kesalahan saat menghapus data', 'Error Server');
            deleteLink.innerHTML = originalText;
            deleteLink.style.pointerEvents = 'auto';
            deleteLink.classList.remove('opacity-50', 'cursor-not-allowed');
          });
      };

      toggleModal('modalHapusLowongan');
    }

    // Validasi form export lowongan
    document.addEventListener('DOMContentLoaded', function () {
      const formExport = document.getElementById('formExportLowongan');
      if (formExport) {
        formExport.addEventListener('submit', function (e) {
          const checkboxes = document.querySelectorAll('#formExportLowongan input[name="columns[]"]:checked');
          if (checkboxes.length === 0) {
            e.preventDefault();
            showNotification('error', 'Pilih minimal 1 kolom untuk export!', 'Export Gagal');
            return false;
          }
        });
      }
    });

    // Function untuk load kota berdasarkan provinsi (form edit)
    async function loadKotaEditLowongan(id_provinsi) {
      const kotaSelect = document.getElementById('edit_id_kota');
      kotaSelect.innerHTML = '<option value="">Pilih Kota</option>';

      if (id_provinsi) {
        try {
          const response = await fetch(`get_kota.php?id_provinsi=${id_provinsi}`);
          const data = await response.json();

          if (data.success) {
            data.data.forEach(kota => {
              kotaSelect.innerHTML += `<option value="${kota.id_kota}">${kota.nama_kota}</option>`;
            });
          }
        } catch (error) {
          console.error('Error loading kota:', error);
        }
      }
    }

    // Function untuk load kota berdasarkan provinsi (form tambah)
    async function loadKotaTambahLowongan(id_provinsi) {
      const kotaSelect = document.getElementById('tambah_id_kota');
      kotaSelect.innerHTML = '<option value="">Pilih Kota</option>';

      if (id_provinsi) {
        try {
          const response = await fetch(`get_kota.php?id_provinsi=${id_provinsi}`);
          const data = await response.json();

          if (data.success) {
            data.data.forEach(kota => {
              kotaSelect.innerHTML += `<option value="${kota.id_kota}">${kota.nama_kota}</option>`;
            });
          }
        } catch (error) {
          console.error('Error loading kota:', error);
        }
      }
    }
  </script>
</body>

</html>