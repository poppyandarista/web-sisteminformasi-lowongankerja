<?php
require_once 'session_check.php';
include 'koneksi.php';

$db = new database();

$data_perusahaan = $db->tampil_data_perusahaan();

// Handle AJAX requests
// Log untuk debugging
error_log("AJAX Request: " . json_encode($_REQUEST) . " Files: " . json_encode($_FILES));

header('Content-Type: application/json');
$ajax_response = ['success' => false, 'message' => ''];

// Tangani hapus via AJAX
if (isset($_GET['action']) && $_GET['action'] == 'hapus' && isset($_GET['id'])) {
  $id = $_GET['id'];

  if (is_numeric($id)) {
    // Hapus dulu lowongan yang terkait dengan perusahaan ini
    $result = $db->hapus_perusahaan($id);
    if ($result) {
      $ajax_response['success'] = true;
      $ajax_response['message'] = 'Data perusahaan berhasil dihapus';
    } else {
      $ajax_response['message'] = 'Gagal menghapus data perusahaan. Mungkin masih ada lowongan terkait.';
    }
  } else {
    $ajax_response['message'] = 'ID tidak valid';
  }
  echo json_encode($ajax_response);
  exit;
}

// Tangani edit via AJAX
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit') {
  error_log("Processing edit request");
  $id_perusahaan = $_POST['id_perusahaan'];
  $nama_perusahaan = $_POST['nama_perusahaan'];
  $email_perusahaan = $_POST['email_perusahaan'];
  $password_perusahaan = $_POST['password_perusahaan'] ?: null;
  $id_provinsi = $_POST['id_provinsi'] ?? null;
  $id_kota = $_POST['id_kota'] ?? null;
  $alamat_perusahaan = $_POST['alamat_perusahaan'];
  $nohp_perusahaan = $_POST['nohp_perusahaan'];
  $deskripsi_perusahaan = $_POST['deskripsi_perusahaan'];

  // Handle upload logo jika ada
  $logo_perusahaan = null;
  if (isset($_FILES['logo_perusahaan']) && $_FILES['logo_perusahaan']['name'] != "") {
    error_log("Processing logo upload");
    $logo = $_FILES['logo_perusahaan'];
    $nama_logo = time() . "_" . basename($logo['name']);
    $target_dir = "src/images/company/";

    if (!is_dir($target_dir)) {
      mkdir($target_dir, 0777, true);
    }

    $target_file = $target_dir . $nama_logo;

    if ($logo['size'] > 2000000) {
      $ajax_response['message'] = "Ukuran logo terlalu besar! Maksimal 2MB.";
      echo json_encode($ajax_response);
      exit;
    } elseif (!in_array($logo['type'], ['image/jpeg', 'image/png', 'image/gif'])) {
      $ajax_response['message'] = "Format logo tidak didukung! Gunakan JPG, PNG, atau GIF.";
      echo json_encode($ajax_response);
      exit;
    } elseif (move_uploaded_file($logo['tmp_name'], $target_file)) {
      $logo_perusahaan = $nama_logo;
      error_log("Logo uploaded successfully: $nama_logo");
    } else {
      $logo_perusahaan = null;
      error_log("Logo upload failed");
    }
  } else {
    $logo_perusahaan = $_POST['logo_lama'] ?? null;
  }

  // Siapkan data untuk update
  $data = [
    'nama_perusahaan' => $nama_perusahaan,
    'email_perusahaan' => $email_perusahaan,
    'password_perusahaan' => $password_perusahaan,
    'id_provinsi' => $id_provinsi,
    'id_kota' => $id_kota,
    'alamat_perusahaan' => $alamat_perusahaan,
    'nohp_perusahaan' => $nohp_perusahaan,
    'deskripsi_perusahaan' => $deskripsi_perusahaan,
    'logo_perusahaan' => $logo_perusahaan
  ];

  error_log("Calling update_perusahaan with id=$id_perusahaan");

  // Panggil fungsi update
  if ($db->update_perusahaan($id_perusahaan, $data)) {
    $ajax_response['success'] = true;
    $ajax_response['message'] = 'Data perusahaan berhasil diperbarui';
    error_log("update_perusahaan success");
  } else {
    $ajax_response['message'] = 'Gagal memperbarui data perusahaan';
    error_log("update_perusahaan failed");
  }
  echo json_encode($ajax_response);
  exit;
}

// Tangani tambah via AJAX
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'tambah') {
  error_log("Processing tambah request");
  $nama_perusahaan = $_POST['nama_perusahaan'];
  $email_perusahaan = $_POST['email_perusahaan'];
  $password_perusahaan = $_POST['password_perusahaan'];
  $id_provinsi = $_POST['id_provinsi'] ?? null;
  $id_kota = $_POST['id_kota'] ?? null;
  $alamat_perusahaan = $_POST['alamat_perusahaan'] ?? null;
  $nohp_perusahaan = $_POST['nohp_perusahaan'] ?? null;
  $deskripsi_perusahaan = $_POST['deskripsi_perusahaan'] ?? null;

  // Handle upload logo jika ada
  $logo_data = null;
  if (isset($_FILES['logo_perusahaan']) && $_FILES['logo_perusahaan']['name'] != "") {
    $logo_data = $_FILES['logo_perusahaan'];
  }

  error_log("Calling tambah_perusahaan with params: email=$email_perusahaan, nama=$nama_perusahaan");

  // Panggil fungsi tambah dengan parameter yang benar
  if ($db->tambah_perusahaan($email_perusahaan, $password_perusahaan, $nama_perusahaan, $logo_data, $deskripsi_perusahaan, $id_provinsi, $id_kota, $alamat_perusahaan, $nohp_perusahaan)) {
    $ajax_response['success'] = true;
    $ajax_response['message'] = 'Data perusahaan berhasil ditambahkan';
    error_log("tambah_perusahaan success");
  } else {
    $ajax_response['message'] = 'Gagal menambahkan data perusahaan';
    error_log("tambah_perusahaan failed");
  }
  echo json_encode($ajax_response);
  exit;
}

// Reset content type untuk normal HTML
header('Content-Type: text/html; charset=UTF-8');

// Tangani hapus (legacy - non-AJAX)
if (isset($_GET['action']) && $_GET['action'] == 'hapus' && isset($_GET['id'])) {
  $id = $_GET['id'];

  if (is_numeric($id)) {
    // Hapus dulu lowongan yang terkait dengan perusahaan ini
    $result = $db->hapus_perusahaan($id);
    if ($result) {
      $success = "Data perusahaan berhasil dihapus";
      // Refresh data
      $data_perusahaan = $db->tampil_data_perusahaan();
    } else {
      $error = "Gagal menghapus data perusahaan. Mungkin masih ada lowongan terkait.";
    }
  }
}

// Tangani edit (legacy - non-AJAX)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit') {
  $id_perusahaan = $_POST['id_perusahaan'];
  $nama_perusahaan = $_POST['nama_perusahaan'];
  $email_perusahaan = $_POST['email_perusahaan'];
  $password_perusahaan = $_POST['password_perusahaan'] ?: null;
  $id_provinsi = $_POST['id_provinsi'] ?? null;
  $id_kota = $_POST['id_kota'] ?? null;
  $alamat_perusahaan = $_POST['alamat_perusahaan'];
  $nohp_perusahaan = $_POST['nohp_perusahaan'];
  $deskripsi_perusahaan = $_POST['deskripsi_perusahaan'];

  // Handle upload logo jika ada
  $logo_perusahaan = null;
  if (isset($_FILES['logo_perusahaan']) && $_FILES['logo_perusahaan']['name'] != "") {
    $logo = $_FILES['logo_perusahaan'];
    $nama_logo = time() . "_" . basename($logo['name']);
    $target_dir = "src/images/company/";

    if (!is_dir($target_dir)) {
      mkdir($target_dir, 0777, true);
    }

    $target_file = $target_dir . $nama_logo;

    if ($logo['size'] > 2000000) {
      $error = "Ukuran logo terlalu besar! Maksimal 2MB.";
    } elseif (!in_array($logo['type'], ['image/jpeg', 'image/png', 'image/gif'])) {
      $error = "Format logo tidak didukung! Gunakan JPG, PNG, atau GIF.";
    } elseif (move_uploaded_file($logo['tmp_name'], $target_file)) {
      $logo_perusahaan = $nama_logo;
    } else {
      $logo_perusahaan = null;
    }
  } else {
    $logo_perusahaan = $_POST['logo_lama'] ?? null;
  }

  // Siapkan data untuk update
  $data = [
    'nama_perusahaan' => $nama_perusahaan,
    'email_perusahaan' => $email_perusahaan,
    'password_perusahaan' => $password_perusahaan,
    'id_provinsi' => $id_provinsi,
    'id_kota' => $id_kota,
    'alamat_perusahaan' => $alamat_perusahaan,
    'nohp_perusahaan' => $nohp_perusahaan,
    'deskripsi_perusahaan' => $deskripsi_perusahaan,
    'logo_perusahaan' => $logo_perusahaan
  ];

  // Panggil fungsi update (perlu ditambahkan di class database)
  if ($db->update_perusahaan($id_perusahaan, $data)) {
    $success = "Data perusahaan berhasil diperbarui";
    $data_perusahaan = $db->tampil_data_perusahaan();
  } else {
    $error = "Gagal memperbarui data perusahaan";
  }
}

// Get perusahaan by ID untuk edit
function get_perusahaan_by_id($db, $id)
{
  $query = "SELECT * FROM perusahaan WHERE id_perusahaan = ?";
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
  <title>Data Perusahaan | LinkUp</title>
  <link rel="icon" type="image/png" href="src/images/logo/favicon.png">
  <link href="style.css" rel="stylesheet">
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

    /* Style untuk tabel perusahaan */
    #perusahaanTable tbody tr {
      height: 60px !important;
    }

    #perusahaanTable td {
      padding-top: 0.75rem !important;
      padding-bottom: 0.75rem !important;
      padding-left: 1rem !important;
      padding-right: 1rem !important;
    }

    #perusahaanTable th {
      padding-top: 0.75rem !important;
      padding-bottom: 0.75rem !important;
    }

    /* Modal konfirmasi hapus */
    #modalHapusPerusahaan {
      display: none;
    }

    #modalHapusPerusahaan:not(.hidden) {
      display: flex;
    }

    #modalEditPerusahaan {
      display: none;
    }

    #modalEditPerusahaan:not(.hidden) {
      display: flex;
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
    }

    /* Deskripsi singkat */
    .deskripsi-singkat {
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
      text-overflow: ellipsis;
      max-width: 200px;
    }

    /* Logo perusahaan di tabel - KOTAK */
    .perusahaan-logo-container {
      width: 40px;
      height: 40px;
      border-radius: 8px;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
      background-color: #f3f4f6;
    }

    .dark .perusahaan-logo-container {
      background-color: #374151;
    }

    .perusahaan-logo {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .perusahaan-placeholder {
      font-size: 0.8rem;
      font-weight: 600;
      color: #6b7280;
    }

    .dark .perusahaan-placeholder {
      color: #9ca3af;
    }

    /* Tombol aksi */
    .action-btn {
      padding: 0.35rem 0.75rem;
      font-size: 0.75rem;
      border-radius: 5px;
    }

    /* Form styling */
    .form-group {
      margin-bottom: 0.75rem;
    }

    .form-label {
      display: block;
      margin-bottom: 0.25rem;
      font-size: 0.8rem;
      font-weight: 500;
      color: #374151;
    }

    .dark .form-label {
      color: #d1d5db;
    }

    .form-control {
      width: 100%;
      padding: 0.4rem 0.6rem;
      font-size: 0.8rem;
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
      min-height: 70px;
      resize: vertical;
      font-size: 0.8rem;
      padding: 0.4rem 0.6rem;
    }

    /* Logo preview di modal edit */
    .logo-preview-container {
      width: 100px;
      height: 100px;
      border-radius: 10px;
      overflow: hidden;
      margin-top: 5px;
      display: flex;
      align-items: center;
      justify-content: center;
      background-color: #f3f4f6;
      border: 1px solid #e5e7eb;
    }

    .dark .logo-preview-container {
      background-color: #374151;
      border-color: #4b5563;
    }

    .logo-preview {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    /* Modal edit yang lebih kecil */
    .compact-modal {
      max-width: 600px;
      width: 90%;
      max-height: 80vh;
    }

    /* Scrollbar untuk modal */
    .modal-scroll {
      max-height: calc(80vh - 100px);
      overflow-y: auto;
      padding-right: 8px;
    }

    .modal-scroll::-webkit-scrollbar {
      width: 6px;
    }

    .modal-scroll::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 3px;
    }

    .dark .modal-scroll::-webkit-scrollbar-track {
      background: #374151;
    }

    .modal-scroll::-webkit-scrollbar-thumb {
      background: #c1c1c1;
      border-radius: 3px;
    }

    .dark .modal-scroll::-webkit-scrollbar-thumb {
      background: #4b5563;
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

    /* ==================== TAMBAHAN UNTUK VALIDASI FILE ==================== */
    /* File error message */
    .file-error {
      font-size: 0.75rem;
      margin-top: 0.25rem;
      min-height: 1rem;
    }

    /* File name container */
    .file-name-container {
      margin-top: 0.5rem;
    }

    .file-name-box {
      display: flex;
      align-items: center;
      justify-content: space-between;
      background-color: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 0.375rem;
      padding: 0.5rem 0.75rem;
      margin-top: 0.25rem;
    }

    .dark .file-name-box {
      background-color: #1e293b;
      border-color: #334155;
    }

    .file-name-text {
      font-size: 0.75rem;
      color: #374151;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
      flex: 1;
    }

    .dark .file-name-text {
      color: #d1d5db;
    }

    .clear-file-btn {
      background: none;
      border: none;
      color: #ef4444;
      cursor: pointer;
      font-size: 1.25rem;
      padding: 0;
      margin-left: 0.5rem;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .clear-file-btn:hover {
      color: #dc2626;
    }

    /* Disabled button state */
    .btn-disabled {
      background-color: #9ca3af !important;
      cursor: not-allowed !important;
      opacity: 0.5 !important;
    }

    /* Success and error colors */
    .text-success {
      color: #10b981 !important;
    }

    .text-error {
      color: #ef4444 !important;
    }

    /* Notification styles */
    .notification {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 99999;
      max-width: 350px;
    }

    .notification-content {
      padding: 1rem 1.5rem;
      border-radius: 0.5rem;
      margin-bottom: 0.5rem;
      box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
      animation: slideInRight 0.3s ease-out;
    }

    .notification-success {
      background-color: #10b981;
      color: white;
    }

    .notification-error {
      background-color: #ef4444;
      color: white;
    }

    @keyframes slideInRight {
      from {
        transform: translateX(100%);
        opacity: 0;
      }

      to {
        transform: translateX(0);
        opacity: 1;
      }
    }

    /* Loading spinner */
    .spinner {
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      from {
        transform: rotate(0deg);
      }

      to {
        transform: rotate(360deg);
      }
    }

    /* Tombol disabled state untuk perusahaan */
    .btn-disabled {
      background-color: #9ca3af !important;
      cursor: not-allowed !important;
      opacity: 0.5 !important;
    }

    /* Success and error colors */
    .text-success {
      color: #10b981 !important;
    }

    .text-error {
      color: #ef4444 !important;
    }

    /* ==================== PERBAIKAN DATATABLES v2 ==================== */
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
    #perusahaanTable_wrapper .flex {
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

    /* ==================== TAMBAHAN UNTUK BUTTON TAMBAH ==================== */
    .btn-tambah-perusahaan {
      padding: 0.5rem 0.75rem !important;
      /* py-2 px-3 */
      font-size: 0.75rem !important;
      /* text-xs */
    }

    .btn-tambah-perusahaan svg {
      width: 16px !important;
      height: 16px !important;
    }

    /* ========== PERBAIKAN TOMBOL EXPORT PERUSAHAAN ========== */
    button[onclick*="modalExportPerusahaan"] {
      background-color: #16a34a !important;
      color: white !important;
      font-weight: 500 !important;
      border: none !important;
      display: inline-flex !important;
      align-items: center !important;
      gap: 0.5rem !important;
      padding: 0.5rem 1rem !important;
      border-radius: 0.375rem !important;
      transition: all 0.2s ease !important;
      box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
    }

    button[onclick*="modalExportPerusahaan"]:hover {
      background-color: #15803d !important;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
    }

    button[onclick*="modalExportPerusahaan"] svg {
      width: 1rem !important;
      height: 1rem !important;
    }

    // ========== PRINT & PREVIEW FUNCTIONS ==========
    function printPerusahaanData() {
      const form=document.getElementById('exportFormPerusahaan');
      const formData=new FormData(form);
      formData.append('format', 'print');

      fetch('export_perusahaan.php', {
        method: 'POST',
        body: formData

      }) .then(response=> response.text()) .then(html=> {
        const printWindow=window.open('', '_blank');
        printWindow.document.write(html);
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
        printWindow.close();

      }) .catch(error=> {
        console.error('Error:', error);
        alert('Gagal mencetak data');
      });
    }

    function printPerusahaanPreview() {
      const form=document.getElementById('exportFormPerusahaan');
      const formData=new FormData(form);
      formData.append('format', 'preview');

      fetch('export_perusahaan.php', {
        method: 'POST',
        body: formData

      }) .then(response=> response.text()) .then(html=> {
        const previewWindow=window.open('', '_blank', 'width=800,height=600');
        previewWindow.document.write(html);
        previewWindow.document.close();

      }) .catch(error=> {
        console.error('Error:', error);
        alert('Gagal menampilkan preview');
      });
    }

    // ========== EVENT LISTENER UNTUK MODAL ==================== */
    /* Tampilan Desktop - Normal */
    #perusahaanTable {
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
      #perusahaanTable {
        min-width: 768px;
        /* Minimum width agar tidak terlalu kecil */
        font-size: 0.875rem;
        /* text-sm */
      }

      #perusahaanTable th,
      #perusahaanTable td {
        padding: 0.5rem 0.75rem !important;
      }

      .deskripsi-singkat {
        max-width: 150px !important;
        -webkit-line-clamp: 1;
      }
    }

    /* Tampilan Mobile (<= 768px) */
    @media (max-width: 768px) {
      #perusahaanTable {
        min-width: 600px;
        font-size: 0.75rem;
        /* text-xs */
      }

      #perusahaanTable th,
      #perusahaanTable td {
        padding: 0.375rem 0.5rem !important;
      }

      .perusahaan-logo-container {
        width: 32px !important;
        height: 32px !important;
        border-radius: 6px !important;
      }

      .action-btn {
        padding: 0.25rem 0.5rem !important;
        font-size: 0.7rem !important;
      }

      .deskripsi-singkat {
        max-width: 120px !important;
        -webkit-line-clamp: 1;
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
    }

    /* Tampilan Very Small Mobile (<= 480px) */
    @media (max-width: 480px) {
      #perusahaanTable {
        min-width: 500px;
      }

      .flex.items-center.gap-2 {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.25rem !important;
      }

      .perusahaan-logo-container {
        width: 28px !important;
        height: 28px !important;
      }
    }
  </style>
</head>

<body
  x-data="{ page: 'dataPerusahaan', 'loaded': true, 'darkMode': false, 'stickyMenu': false, 'sidebarToggle': false, 'scrollTop': false, 'modalOpen': false }"
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
              Data Perusahaan
            </h2>
            <nav>
              <ol class="flex items-center gap-2">
                <li><a class="font-medium" href="index.php">Home ></a></li>
                <li class="font-medium text-primary">Data Perusahaan</li>
              </ol>
            </nav>
          </div>

          <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
            <div
              class="border-b border-stroke px-4 py-4 dark:border-strokedark sm:px-6 xl:px-7.5 flex justify-between items-center">
              <h3 class="font-medium text-black dark:text-white">Tabel Data Perusahaan</h3>
              <div class="flex items-center gap-2">
                <button onclick="toggleModal('modalExportPerusahaan')"
                  class="inline-flex items-center justify-center rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 transition-colors duration-200 gap-2 shadow-sm hover:shadow-md">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                  Export
                </button>
                <button onclick="toggleModal('modalTambahPerusahaan')"
                  class="btn-tambah-perusahaan inline-flex items-center gap-2 px-4 py-2 bg-brand-500 text-white rounded-lg hover:bg-brand-600 transition-colors">
                  <svg class="fill-current" width="16" height="16" viewBox="0 0 20 20" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                      d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                      fill="" />
                  </svg>
                  Tambah Perusahaan
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
                <table id="perusahaanTable" class="w-full table-auto border-collapse text-left">
                  <thead>
                    <tr class="bg-gray-2 dark:bg-meta-4">
                      <th class="px-4 py-3 font-medium text-black dark:text-white">ID Perusahaan</th>
                      <th class="px-4 py-3 font-medium text-black dark:text-white">Perusahaan</th>
                      <th class="px-4 py-3 font-medium text-black dark:text-white">Email</th>
                      <th class="px-4 py-3 font-medium text-black dark:text-white">Lokasi</th>
                      <th class="px-4 py-3 font-medium text-black dark:text-white">Kontak</th>
                      <th class="px-4 py-3 font-medium text black dark:text-white">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    if (!empty($data_perusahaan)) {
                      foreach ($data_perusahaan as $row) {
                        $format_id = "C" . sprintf("%04d", $row['id_perusahaan']);
                        $lokasi = trim(($row['nama_kota'] ?? '') . ", " . ($row['nama_provinsi'] ?? ''), ", ");

                        // ========== PERBAIKAN UTAMA ==========
                        // Handle deskripsi yang mungkin NULL
                        $deskripsi = $row['deskripsi_perusahaan'] ?? '';
                        if (is_null($deskripsi) || $deskripsi === '') {
                          $deskripsi_singkat = '-';
                          $deskripsi_full = 'Tidak ada deskripsi';
                        } else {
                          $deskripsi_singkat = strlen($deskripsi) > 100
                            ? substr($deskripsi, 0, 100) . "..."
                            : $deskripsi;
                          $deskripsi_full = $deskripsi;
                        }
                        // ========== END PERBAIKAN ==========
                        ?>
                        <tr class="border-b border-[#eee] dark:border-strokedark">
                          <td class="px-4 py-3">
                            <p class="text-black dark:text-white font-medium"><?php echo $format_id; ?></p>
                          </td>

                          <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                              <div class="perusahaan-logo-container">
                                <?php if (!empty($row['logo_perusahaan'])): ?>
                                  <img src="src/images/company/<?php echo $row['logo_perusahaan']; ?>" alt="Logo"
                                    class="perusahaan-logo" />
                                <?php else: ?>
                                  <span class="perusahaan-placeholder">
                                    <?php echo substr(htmlspecialchars($row['nama_perusahaan'] ?? '??'), 0, 2); ?>
                                  </span>
                                <?php endif; ?>
                              </div>
                              <div>
                                <h5 class="font-medium text-black dark:text-white text-sm">
                                  <?php echo htmlspecialchars($row['nama_perusahaan'] ?? '-'); ?>
                                </h5>
                                <p class="text-xs text-gray-500 deskripsi-singkat"
                                  title="<?php echo htmlspecialchars($deskripsi_full); ?>">
                                  <?php echo htmlspecialchars($deskripsi_singkat); ?>
                                </p>
                              </div>
                            </div>
                          </td>

                          <td class="px-4 py-3">
                            <p class="text-sm text-black dark:text-white">
                              <?php echo htmlspecialchars($row['email_perusahaan'] ?? '-'); ?>
                            </p>
                          </td>

                          <td class="px-4 py-3">
                            <p class="text-sm text-black dark:text-white">
                              <?php echo htmlspecialchars($lokasi ?: '-'); ?>
                            </p>
                            <p class="text-xs text-gray-500">
                              <?php echo htmlspecialchars($row['alamat_perusahaan'] ?? '-'); ?>
                            </p>
                          </td>

                          <td class="px-4 py-3">
                            <p class="text-sm text-black dark:text-white">
                              <?php echo htmlspecialchars($row['nohp_perusahaan'] ?? '-'); ?>
                            </p>
                          </td>

                          <td class="px-4 py-3">
                            <div class="flex gap-2">
                              <button onclick="showEditForm(<?php echo $row['id_perusahaan']; ?>)"
                                style="background-color: #2563eb;"
                                class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium text-white transition rounded-lg hover:opacity-80">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20"
                                  fill="currentColor">
                                  <path
                                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                </svg>
                                Edit
                              </button>

                              <button
                                onclick="showDeleteConfirmation(<?php echo $row['id_perusahaan']; ?>, '<?php echo addslashes($row['nama_perusahaan'] ?? ''); ?>')"
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
                      echo "<tr><td colspan='6' class='text-center py-4'>Tidak ada data perusahaan</td></tr>";
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

  <!-- Modal Edit Perusahaan -->
  <div id="modalEditPerusahaan"
    class="modal-container fixed inset-0 hidden items-center justify-center transition-all duration-300 z-9999">
    <div class="absolute inset-0 bg-black/20" onclick="hideAllModals()"></div>
    <div
      class="modal-content bg-white dark:bg-boxdark compact-modal mx-4 rounded-xl shadow-xl relative flex flex-col overflow-hidden">
      <div class="sticky top-0 z-10 bg-white dark:bg-boxdark border-b border-stroke dark:border-strokedark px-4 py-3">
        <div class="flex items-center justify-between">
          <div>
            <h5 class="text-base font-bold text-black dark:text-white">Edit Perusahaan</h5>
            <p class="text-xs text-gray-500">Edit data perusahaan</p>
          </div>
          <button onclick="hideAllModals()" class="text-gray-400 hover:text-red-500 transition-colors p-1">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M18 6L6 18M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
      <div class="p-4 modal-scroll">
        <form action="" method="POST" enctype="multipart/form-data" class="space-y-3" id="editFormPerusahaan">
          <input type="hidden" name="action" value="edit">
          <input type="hidden" name="id_perusahaan" id="edit_id_perusahaan">
          <input type="hidden" name="logo_lama" id="edit_logo_lama">

          <div class="grid grid-cols-2 gap-3">
            <div class="form-group">
              <label class="form-label">Nama Perusahaan *</label>
              <input type="text" name="nama_perusahaan" id="edit_nama_perusahaan" required class="form-control">
            </div>

            <div class="form-group">
              <label class="form-label">Email *</label>
              <input type="email" name="email_perusahaan" id="edit_email_perusahaan" required class="form-control">
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Password (Kosongkan jika tidak diubah)</label>
            <input type="password" name="password_perusahaan" id="edit_password_perusahaan" class="form-control">
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div class="form-group">
              <label class="form-label">Provinsi</label>
              <select name="id_provinsi" id="edit_id_provinsi" class="form-control"
                onchange="loadKotaEditPerusahaan(this.value)">
                <option value="">Pilih Provinsi</option>
                <?php
                $data_provinsi = $db->tampil_data_provinsi();
                foreach ($data_provinsi as $provinsi) {
                  echo "<option value='" . $provinsi['id_provinsi'] . "'>" . $provinsi['nama_provinsi'] . "</option>";
                }
                ?>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label">Kota</label>
              <select name="id_kota" id="edit_id_kota" class="form-control">
                <option value="">Pilih Kota</option>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Alamat</label>
            <textarea name="alamat_perusahaan" id="edit_alamat_perusahaan" rows="2"
              class="form-control form-textarea"></textarea>
          </div>

          <div class="form-group">
            <label class="form-label">No. HP</label>
            <input type="text" name="nohp_perusahaan" id="edit_nohp_perusahaan" class="form-control">
          </div>

          <div class="form-group">
            <label class="form-label">Logo Perusahaan</label>
            <div id="currentLogo" class="mb-1">
              <!-- Preview logo akan ditampilkan di sini -->
            </div>

            <div id="filePreviewContainer" class="hidden mb-2">
              <div class="file-name-box">
                <span id="fileNameText" class="file-name-text"></span>
                <button type="button" onclick="clearFileInputPerusahaan()" class="clear-file-btn">×</button>
              </div>
            </div>

            <div class="flex items-center gap-2">
              <label for="fileInputEditPerusahaan"
                class="cursor-pointer bg-gray-100 hover:bg-gray-200 dark:bg-meta-4 px-3 py-2 rounded border border-dashed border-primary text-xs font-medium transition flex-1 text-center">
                <span>Ganti Logo...</span>
              </label>
              <input type="file" name="logo_perusahaan" id="fileInputEditPerusahaan" class="hidden" accept="image/*">
            </div>

            <!-- Error message untuk file -->
            <div id="fileErrorPerusahaan" class="file-error"></div>

            <p class="text-xs text-gray-500">Format: JPG, PNG. Maks: 2MB</p>
          </div>

          <div class="form-group">
            <label class="form-label">Deskripsi Perusahaan</label>
            <textarea name="deskripsi_perusahaan" id="edit_deskripsi_perusahaan" rows="3"
              class="form-control form-textarea"></textarea>
          </div>

          <div class="flex justify-end gap-2 pt-3">
            <button type="button" onclick="hideAllModals()"
              class="px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 rounded hover:bg-gray-200 dark:bg-meta-4 dark:text-gray-300">Batal</button>
            <button type="submit" id="submitEditPerusahaanBtn"
              class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-white transition rounded bg-brand-500 hover:bg-brand-600">
              <svg class="fill-current" width="12" height="12" viewBox="0 0 20 20" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd"
                  d="M16.7071 5.29289C17.0976 5.68342 17.0976 6.31658 16.7071 6.70711L8.70711 14.7071C8.31658 15.0976 7.68342 15.0976 7.29289 14.7071L3.29289 10.7071C2.90237 10.3166 2.90237 9.68342 3.29289 9.29289C3.68342 8.90237 4.31658 8.90237 4.70711 9.29289L8 12.5858L15.2929 5.29289C15.6834 4.90237 16.3166 4.90237 16.7071 5.29289Z"
                  fill="white" />
              </svg>
              Update Perusahaan
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Konfirmasi Hapus -->
  <div id="modalHapusPerusahaan"
    class="fixed inset-0 hidden items-center justify-center transition-all duration-300 z-9999">
    <div class="absolute inset-0 bg-black/20" onclick="hideAllModals()"></div>
    <div
      class="relative bg-white dark:bg-boxdark w-full max-w-xs mx-4 rounded-xl shadow-xl flex flex-col overflow-hidden">
      <button onclick="hideAllModals()"
        class="absolute top-3 right-3 text-gray-400 hover:text-red-500 transition-colors z-10 p-1">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path d="M18 6L6 18M6 6l12 12" />
        </svg>
      </button>
      <div class="p-4">
        <div class="mx-auto w-10 h-10 mb-2 rounded-full bg-red-100 flex items-center justify-center">
          <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.994-.833-2.764 0L4.406 16.5c-.77.833.192 2.5 1.732 2.5z" />
          </svg>
        </div>
        <div class="text-center mb-3">
          <h5 class="text-base font-bold text-black dark:text-white mb-1">Konfirmasi Hapus</h5>
          <p class="text-sm text-gray-600 dark:text-gray-400" id="hapusMessagePerusahaan">
            Apakah Anda yakin ingin menghapus data ini?
          </p>
        </div>
        <div class="flex justify-center gap-2">
          <button type="button" onclick="hideAllModals()"
            class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 rounded hover:bg-gray-200 transition-colors">
            Batal
          </button>
          <button type="button" id="confirmDeleteBtnPerusahaan" style="background-color: #dc2626;"
            class="inline-flex items-center gap-2 px-4 py-3 text-sm font-medium text-white transition rounded-lg hover:bg-red-700 shadow-theme-xs">
            Ya, Hapus
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Tambah Perusahaan -->
  <div id="modalTambahPerusahaan"
    class="modal-container fixed inset-0 hidden items-center justify-center transition-all duration-300 z-9999">
    <div class="absolute inset-0 bg-black/20" onclick="toggleModal('modalTambahPerusahaan')"></div>
    <div
      class="modal-content bg-white dark:bg-boxdark w-full max-w-md mx-4 rounded-xl shadow-xl relative flex flex-col overflow-hidden max-h-[85vh] overflow-y-auto">
      <div class="sticky top-0 z-10 bg-white dark:bg-boxdark border-b border-stroke dark:border-strokedark px-6 py-4">
        <div class="flex items-center justify-between">
          <div>
            <h5 class="text-base font-bold text-black dark:text-white">Tambah Perusahaan Baru</h5>
            <p class="text-xs text-gray-500">Masukkan data perusahaan baru</p>
          </div>
          <button onclick="toggleModal('modalTambahPerusahaan')"
            class="text-gray-400 hover:text-red-500 transition-colors p-1">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M18 6L6 18M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
      <div class="p-6">
        <form id="formTambahPerusahaan" method="POST" enctype="multipart/form-data" class="space-y-3">
          <input type="hidden" name="action" value="tambah">
          <div class="grid grid-cols-2 gap-3">
            <div class="form-group">
              <label class="form-label">Nama Perusahaan *</label>
              <input type="text" name="nama_perusahaan" required class="form-control"
                placeholder="Masukkan nama perusahaan">
            </div>

            <div class="form-group">
              <label class="form-label">Email Perusahaan *</label>
              <input type="email" name="email_perusahaan" required class="form-control"
                placeholder="email@perusahaan.com">
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Password *</label>
            <input type="password" name="password_perusahaan" required class="form-control"
              placeholder="Masukkan password">
          </div>

          <div class="form-group">
            <label class="form-label">Deskripsi Perusahaan</label>
            <textarea name="deskripsi_perusahaan" rows="3" class="form-control form-textarea"
              placeholder="Deskripsi singkat perusahaan..."></textarea>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div class="form-group">
              <label class="form-label">Provinsi</label>
              <select name="id_provinsi" id="id_provinsi" class="form-control"
                onchange="loadKotaPerusahaan(this.value)">
                <option value="">Pilih Provinsi</option>
                <?php
                $data_provinsi = $db->tampil_data_provinsi();
                foreach ($data_provinsi as $provinsi) {
                  echo "<option value='" . $provinsi['id_provinsi'] . "'>" . $provinsi['nama_provinsi'] . "</option>";
                }
                ?>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label">Kota</label>
              <select name="id_kota" id="id_kota" class="form-control">
                <option value="">Pilih Kota</option>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Alamat Lengkap</label>
            <textarea name="alamat_perusahaan" rows="2" class="form-control form-textarea"
              placeholder="Alamat lengkap perusahaan..."></textarea>
          </div>

          <div class="form-group">
            <label class="form-label">No. HP/WA</label>
            <input type="text" name="nohp_perusahaan" class="form-control"
              placeholder="0812-3456-7890 atau +62-812-3456-7890">
            <p class="text-xs text-gray-500 mt-1">Format: 0812-3456-7890 atau +62-812-3456-7890 (Maks: 50 karakter)</p>
          </div>

          <div class="form-group">
            <label class="form-label">Logo Perusahaan</label>
            <div class="flex items-center gap-2">
              <label for="logoInput"
                class="cursor-pointer bg-gray-100 hover:bg-gray-200 dark:bg-meta-4 px-3 py-2 rounded border border-dashed border-primary text-xs font-medium transition flex-1 text-center">
                <span>Pilih Logo...</span>
              </label>
              <input type="file" name="logo_perusahaan" id="logoInput" class="hidden" accept="image/*"
                onchange="previewLogo(this)">
            </div>
            <div id="logoPreview" class="mt-2 hidden">
              <img id="logoPreviewImg" src="" alt="Preview" class="h-16 w-16 object-cover rounded">
              <p class="text-xs text-gray-500 mt-1">Preview logo</p>
            </div>
            <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, GIF (Maks: 2MB)</p>
          </div>

          <div class="flex justify-end gap-2 pt-3">
            <button type="button" onclick="toggleModal('modalTambahPerusahaan')"
              class="px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 rounded hover:bg-gray-200 dark:bg-meta-4 dark:text-gray-300">
              Batal
            </button>
            <button type="submit"
              class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-white transition rounded bg-brand-500 hover:bg-brand-600">
              <svg class="fill-current h-3 w-3" width="20" height="20" viewBox="0 0 20 20" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M13.5858 3.58579C14.3668 2.80474 15.6332 2.80474 16.4142 3.58579C17.1953 4.36683 17.1953 5.63316 16.4142 6.41421L15.6213 7.20711L12.7929 4.37868L13.5858 3.58579Z"
                  fill="white" />
                <path d="M11.3787 5.79289L3 14.1716V17H5.82842L14.2071 8.62132L11.3787 5.79289Z" fill="white" />
              </svg>
              Simpan Perusahaan
            </button>
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
      $('#perusahaanTable').DataTable({
        "dom": '<"flex flex-wrap items-center justify-between gap-3 mb-2"lf>rt<"flex flex-wrap items-center justify-between gap-3 mt-2"ip>',
        "language": {
          "search": "",
          "searchPlaceholder": "Cari perusahaan...",
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
        "initComplete": function () {
          // Pindahkan kontrol DataTables ke container kustom
          var $wrapper = $('#perusahaanTable_wrapper');

          // Pindahkan Length Menu dan Search ke container atas
          $wrapper.find('.dt-length, .dt-search').appendTo('#tableControls');
          $('#tableControls').addClass('flex flex-wrap items-center justify-between gap-3');

          // Pindahkan Info dan Pagination ke container bawah
          $wrapper.find('.dt-info, .dt-paging').appendTo('#tableFooter');
          $('#tableFooter').addClass('flex flex-wrap items-center justify-between gap-3');

          // Styling untuk kontrol
          $('.dt-length').addClass('text-xs');
          $('.dt-info').addClass('text-xs');
          $('.dt-paging').addClass('text-xs');

          // PASTIKAN INI SAMA DENGAN dataadmin.php
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

    // Variabel global untuk validasi
    let isFileValid = true;
    let currentPerusahaanId = null;

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
      const modals = ['modalEditPerusahaan', 'modalHapusPerusahaan', 'modalTambahPerusahaan'];
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

    // Preview logo untuk form tambah
    function previewLogo(input) {
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
          document.getElementById('logoPreviewImg').src = e.target.result;
          document.getElementById('logoPreview').classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
      }
    }

    // Fungsi untuk validasi file input
    function validateFileInputPerusahaan() {
      const input = document.getElementById('fileInputEditPerusahaan');
      const file = input.files[0];
      const errorElement = document.getElementById('fileErrorPerusahaan');
      const fileNameElement = document.getElementById('fileNameText');
      const filePreviewContainer = document.getElementById('filePreviewContainer');
      const submitBtn = document.getElementById('submitEditPerusahaanBtn');

      // Reset error message
      errorElement.innerHTML = '';
      isFileValid = true;

      if (file) {
        // Validasi ukuran file (max 2MB)
        const maxSize = 2 * 1024 * 1024; // 2MB in bytes
        if (file.size > maxSize) {
          errorElement.innerHTML = '<span class="text-error">❌ Ukuran file terlalu besar (maks 2MB)</span>';
          isFileValid = false;
          input.value = '';
          filePreviewContainer.classList.add('hidden');
          updateSubmitButtonStatePerusahaan();
          return;
        }

        // Validasi tipe file
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        const allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        // Validasi MIME type
        if (!allowedTypes.includes(file.type)) {
          errorElement.innerHTML = '<span class="text-error">❌ Format tidak didukung (hanya JPG, PNG, GIF)</span>';
          isFileValid = false;
          input.value = '';
          filePreviewContainer.classList.add('hidden');
          updateSubmitButtonStatePerusahaan();
          return;
        }

        // Validasi ekstensi file
        const fileName = file.name;
        const fileExt = fileName.split('.').pop().toLowerCase();

        if (!allowedExtensions.includes(fileExt)) {
          errorElement.innerHTML = '<span class="text-error">❌ Ekstensi file tidak valid</span>';
          isFileValid = false;
          input.value = '';
          filePreviewContainer.classList.add('hidden');
          updateSubmitButtonStatePerusahaan();
          return;
        }

        // File valid, tampilkan preview dan nama file
        const reader = new FileReader();
        reader.onload = function (e) {
          // Update preview di currentLogo
          const currentLogo = document.getElementById('currentLogo');
          currentLogo.innerHTML = `
                <div class="logo-preview-container">
                    <img src="${e.target.result}" alt="Logo Preview" class="logo-preview">
                </div>
            `;
        };
        reader.readAsDataURL(file);

        // Tampilkan nama file
        fileNameElement.textContent = fileName;
        filePreviewContainer.classList.remove('hidden');

        errorElement.innerHTML = '<span class="text-success">✓ File valid</span>';
      } else {
        filePreviewContainer.classList.add('hidden');
      }

      // Update status tombol submit
      updateSubmitButtonStatePerusahaan();
    }

    // Fungsi untuk mengubah state tombol submit di perusahaan
    function updateSubmitButtonStatePerusahaan() {
      const submitBtn = document.getElementById('submitEditPerusahaanBtn');

      if (!submitBtn) return;

      // Validasi form lainnya
      const nama = document.getElementById('edit_nama_perusahaan').value.trim();
      const email = document.getElementById('edit_email_perusahaan').value.trim();

      const isFormValid = nama !== '' && email !== '' && isFileValid;

      if (isFormValid) {
        // Aktifkan tombol
        submitBtn.disabled = false;
        submitBtn.classList.remove('btn-disabled');
        submitBtn.classList.add('bg-brand-500', 'hover:bg-brand-600', 'cursor-pointer');
      } else {
        // Nonaktifkan tombol
        submitBtn.disabled = true;
        submitBtn.classList.remove('bg-brand-500', 'hover:bg-brand-600', 'cursor-pointer');
        submitBtn.classList.add('btn-disabled');
      }
    }

    // Fungsi untuk menghapus file input di perusahaan
    function clearFileInputPerusahaan() {
      const input = document.getElementById('fileInputEditPerusahaan');
      const filePreviewContainer = document.getElementById('filePreviewContainer');
      const errorElement = document.getElementById('fileErrorPerusahaan');
      const fileNameText = document.getElementById('fileNameText');

      input.value = '';
      filePreviewContainer.classList.add('hidden');
      errorElement.innerHTML = '';
      fileNameText.textContent = '';
      isFileValid = true;

      // Reset ke logo sebelumnya
      const logoLama = document.getElementById('edit_logo_lama').value;
      const currentLogo = document.getElementById('currentLogo');
      if (logoLama) {
        currentLogo.innerHTML = `
            <div class="logo-preview-container">
                <img src="src/images/company/${logoLama}" alt="Logo" class="logo-preview">
            </div>
        `;
      } else {
        currentLogo.innerHTML = `
            <div class="logo-preview-container flex items-center justify-center">
                <span class="text-xs text-gray-500">Tidak ada logo</span>
            </div>
        `;
      }

      updateSubmitButtonStatePerusahaan();
    }

    // Fungsi untuk validasi file
    function validateFileInput() {
      const input = document.getElementById('fileInputEditPerusahaan');
      const file = input.files[0];
      const errorElement = document.getElementById('fileErrorPerusahaan');
      const fileNameElement = document.getElementById('fileNameText');
      const filePreviewContainer = document.getElementById('filePreviewContainer');

      // Reset error message
      errorElement.innerHTML = '';
      isFileValid = true;

      if (file) {
        // Validasi ukuran file (max 2MB)
        const maxSize = 2 * 1024 * 1024;
        if (file.size > maxSize) {
          errorElement.innerHTML = '<span class="text-error">❌ Ukuran file terlalu besar (maks 2MB)</span>';
          isFileValid = false;
          input.value = '';
          filePreviewContainer.classList.add('hidden');
          updateSubmitButtonState();
          return;
        }
        // Validasi tipe file
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
          errorElement.innerHTML = '<span class="text-error">❌ Format tidak didukung (hanya JPG, PNG, GIF)</span>';
          isFileValid = false;
          input.value = '';
          filePreviewContainer.classList.add('hidden');
          updateSubmitButtonState();
          return;
        }
        // Validasi ekstensi file
        const fileName = file.name;
        const fileExt = fileName.split('.').pop().toLowerCase();
        const allowedExt = ['jpg', 'jpeg', 'png', 'gif'];

        if (!allowedExt.includes(fileExt)) {
          errorElement.innerHTML = '<span class="text-error">❌ Ekstensi file tidak valid</span>';
          isFileValid = false;
          input.value = '';
          filePreviewContainer.classList.add('hidden');
          updateSubmitButtonState();
          return;
        }

        // File valid, tampilkan preview dan nama file
        const reader = new FileReader();
        reader.onload = function (e) {
          // Update preview di currentLogo
          const currentLogo = document.getElementById('currentLogo');
          currentLogo.innerHTML = `
            <div class="logo-preview-container">
              <img src="${e.target.result}" alt="Logo Preview" class="logo-preview">
            </div>
          `;
        };
        reader.readAsDataURL(file);

        // Tampilkan nama file
        fileNameElement.textContent = fileName;
        filePreviewContainer.classList.remove('hidden');

        errorElement.innerHTML = '<span class="text-success">✓ File valid</span>';
      } else {
        filePreviewContainer.classList.add('hidden');
      }

      // Update status tombol submit
      updateSubmitButtonState();
    }

    // Fungsi untuk menghapus file input
    function clearFileInput() {
      const input = document.getElementById('fileInputEditPerusahaan');
      const filePreviewContainer = document.getElementById('filePreviewContainer');
      const errorElement = document.getElementById('fileErrorPerusahaan');

      input.value = '';
      filePreviewContainer.classList.add('hidden');
      errorElement.innerHTML = '';
      isFileValid = true;

      // Reset ke logo sebelumnya dengan fetch yang benar
      if (currentPerusahaanId) {
        // Reset ke logo lama yang sudah ada di hidden input
        const logoLama = document.getElementById('edit_logo_lama').value;
        const currentLogo = document.getElementById('currentLogo');
        if (logoLama) {
          currentLogo.innerHTML = `
            <div class="logo-preview-container">
              <img src="src/images/company/${logoLama}" alt="Logo" class="logo-preview">
            </div>
          `;
        } else {
          currentLogo.innerHTML = `
            <div class="logo-preview-container flex items-center justify-center">
              <span class="text-xs text-gray-500">Tidak ada logo</span>
            </div>
          `;
        }
      }

      updateSubmitButtonState();
    }

    // Fungsi untuk mengubah state tombol submit
    function updateSubmitButtonState() {
      const submitBtn = document.getElementById('submitEditPerusahaanBtn');

      if (!submitBtn) return;

      // Validasi form lainnya
      const nama = document.getElementById('edit_nama_perusahaan').value.trim();
      const email = document.getElementById('edit_email_perusahaan').value.trim();

      const isFormValid = nama !== '' && email !== '' && isFileValid;

      if (isFormValid) {
        // Aktifkan tombol
        submitBtn.disabled = false;
        submitBtn.classList.remove('btn-disabled');
        submitBtn.classList.add('bg-brand-500', 'hover:bg-brand-600', 'cursor-pointer');
      } else {
        // Nonaktifkan tombol
        submitBtn.disabled = true;
        submitBtn.classList.remove('bg-brand-500', 'hover:bg-brand-600', 'cursor-pointer');
        submitBtn.classList.add('btn-disabled');
      }
    }

    // ========== ALERT SYSTEM FUNCTIONS - Sesuai Template ==========
    function showNotification(type, message, title, showRefreshButton = false) {
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

      if (showRefreshButton && type === 'success') {
        alertHTML += `
          <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
            <button onclick="location.reload()" class="w-full bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium py-2 px-4 rounded-lg transition-colors duration-200">
              <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
              </svg>
              Refresh Data
            </button>
          </div>
        `;
      }

      alertElement.innerHTML = alertHTML;

      // Add to container
      alertContainer.appendChild(alertElement);

      // Show animation
      setTimeout(() => {
        alertElement.classList.add('show');
      }, 10);

      // Auto hide after 5 seconds (kecuali ada tombol refresh)
      setTimeout(() => {
        closeAlert(alertElement.querySelector('button'));
      }, showRefreshButton ? 8000 : 5000);
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

    async function showEditForm(id) {
      try {
        currentPerusahaanId = id;

        // Fetch data perusahaan via AJAX
        const response = await fetch(`get_perusahaan.php?id=${id}`);
        const data = await response.json();

        if (data.success) {
          const perusahaan = data.data;

          // Isi form dengan data
          document.getElementById('edit_id_perusahaan').value = perusahaan.id_perusahaan;
          document.getElementById('edit_nama_perusahaan').value = perusahaan.nama_perusahaan;
          document.getElementById('edit_email_perusahaan').value = perusahaan.email_perusahaan;
          document.getElementById('edit_id_provinsi').value = perusahaan.id_provinsi || '';
          document.getElementById('edit_id_kota').value = perusahaan.id_kota || '';
          document.getElementById('edit_alamat_perusahaan').value = perusahaan.alamat_perusahaan || '';
          document.getElementById('edit_nohp_perusahaan').value = perusahaan.nohp_perusahaan || '';
          document.getElementById('edit_deskripsi_perusahaan').value = perusahaan.deskripsi_perusahaan || '';

          // Load kota berdasarkan provinsi yang dipilih
          if (perusahaan.id_provinsi) {
            await loadKotaEditPerusahaan(perusahaan.id_provinsi);
            // Set selected kota setelah load
            setTimeout(() => {
              document.getElementById('edit_id_kota').value = perusahaan.id_kota || '';
            }, 500);
          }

          // Handle logo
          const currentLogo = document.getElementById('currentLogo');
          if (perusahaan.logo_perusahaan) {
            document.getElementById('edit_logo_lama').value = perusahaan.logo_perusahaan;
            currentLogo.innerHTML = `
              <div class="logo-preview-container">
                <img src="src/images/company/${perusahaan.logo_perusahaan}" alt="Logo" class="logo-preview">
              </div>
            `;
          } else {
            document.getElementById('edit_logo_lama').value = '';
            currentLogo.innerHTML = `
              <div class="logo-preview-container flex items-center justify-center">
                <span class="text-xs text-gray-500">Tidak ada logo</span>
              </div>
            `;
          }

          // Reset file input dan validasi
          const fileInput = document.getElementById('fileInputEditPerusahaan');
          fileInput.value = '';
          document.getElementById('filePreviewContainer').classList.add('hidden');
          document.getElementById('fileErrorPerusahaan').innerHTML = '';
          isFileValid = true;
          updateSubmitButtonState();

          toggleModal('modalEditPerusahaan');
        } else {
          showNotification('error', 'Gagal memuat data perusahaan', 'Error');
        }
      } catch (error) {
        console.error('Error:', error);
        showNotification('error', 'Terjadi kesalahan saat memuat data', 'Error Server');
      }
    }

    // ========== AJAX FORM SUBMIT HANDLERS ==========
    // Handle form tambah perusahaan dengan AJAX
    document.getElementById('formTambahPerusahaan').addEventListener('submit', async function (e) {
      e.preventDefault();

      const formData = new FormData(this);
      const submitBtn = this.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;

      // Tampilkan loading
      submitBtn.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Menyimpan...';
      submitBtn.disabled = true;

      try {
        const response = await fetch('dataperusahaan.php', {
          method: 'POST',
          body: formData
        });

        const result = await response.json();

        if (result.success) {
          showNotification('success', 'Data perusahaan berhasil ditambahkan!', 'Tambah Berhasil');
          toggleModal('modalTambahPerusahaan', false);
          this.reset();
          // Auto refresh setelah 2 detik
          setTimeout(() => {
            location.reload();
          }, 2000);
        } else {
          showNotification('error', result.message || 'Gagal menambahkan data perusahaan', 'Tambah Gagal');
        }
      } catch (error) {
        console.error('Error:', error);
        showNotification('error', 'Terjadi kesalahan saat menambahkan data', 'Error Server');
      } finally {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
      }
    });

    // Handle form edit perusahaan dengan AJAX  
    document.getElementById('editFormPerusahaan').addEventListener('submit', async function (e) {
      e.preventDefault();

      const formData = new FormData(this);
      const submitBtn = document.getElementById('submitEditPerusahaanBtn');
      const originalText = submitBtn.innerHTML;

      // Tampilkan loading
      submitBtn.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memperbarui...';
      submitBtn.disabled = true;

      try {
        const response = await fetch('dataperusahaan.php', {
          method: 'POST',
          body: formData
        });

        const result = await response.json();

        if (result.success) {
          showNotification('success', 'Data perusahaan berhasil diperbarui!', 'Edit Berhasil');
          toggleModal('modalEditPerusahaan', false);
          // Auto refresh setelah 2 detik
          setTimeout(() => {
            location.reload();
          }, 2000);
        } else {
          showNotification('error', result.message || 'Gagal mengupdate data', 'Edit Gagal');
        }
      } catch (error) {
        console.error('Error:', error);
        showNotification('error', 'Terjadi kesalahan saat mengupdate data', 'Error Server');
      } finally {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
      }
    });

    function showDeleteConfirmation(id, nama) {
      const message = document.getElementById('hapusMessagePerusahaan');
      message.textContent = `Apakah Anda yakin ingin menghapus perusahaan "${nama}"? (Semua lowongan terkait juga akan dihapus)`;

      // Set event listener untuk tombol hapus
      const deleteBtn = document.getElementById('confirmDeleteBtnPerusahaan');
      deleteBtn.onclick = async function () {
        const originalText = deleteBtn.innerHTML;
        deleteBtn.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Menghapus...';
        deleteBtn.disabled = true;

        try {
          const response = await fetch(`dataperusahaan.php?action=hapus&id=${id}`, {
            method: 'GET'
          });

          const result = await response.json();

          if (result.success) {
            showNotification('success', 'Data perusahaan berhasil dihapus!', 'Hapus Berhasil');
            toggleModal('modalHapusPerusahaan', false);
            // Auto refresh setelah 2 detik
            setTimeout(() => {
              location.reload();
            }, 2000);
          } else {
            showNotification('error', result.message || 'Gagal menghapus data perusahaan', 'Hapus Gagal');
          }
        } catch (error) {
          console.error('Error:', error);
          showNotification('error', 'Terjadi kesalahan saat menghapus data', 'Error Server');
        } finally {
          deleteBtn.innerHTML = originalText;
          deleteBtn.disabled = false;
        }
      };

      toggleModal('modalHapusPerusahaan');
    }

    // Event listeners untuk validasi real-time
    document.addEventListener('DOMContentLoaded', function () {
      // Setup event listeners untuk perusahaan
      const fileInput = document.getElementById('fileInputEditPerusahaan');
      if (fileInput) {
        fileInput.addEventListener('change', validateFileInputPerusahaan);
      }

      // Event listener untuk tombol clear file perusahaan
      const clearFileBtn = document.getElementById('clearFileBtn');
      if (clearFileBtn) {
        clearFileBtn.addEventListener('click', clearFileInputPerusahaan);
      }

      // Event listeners untuk validasi real-time form perusahaan
      const namaInput = document.getElementById('edit_nama_perusahaan');
      const emailInput = document.getElementById('edit_email_perusahaan');

      if (namaInput) {
        namaInput.addEventListener('input', updateSubmitButtonStatePerusahaan);
      }

      if (emailInput) {
        emailInput.addEventListener('input', updateSubmitButtonStatePerusahaan);
      }

      // Handle submit form perusahaan - DISABLED karena sudah menggunakan AJAX di atas
      // const form = document.getElementById('editFormPerusahaan');
      // if (form) {
      //   form.addEventListener('submit', function (e) {
      //     // Form ini sudah dihandle oleh AJAX handler di atas
      //   });
      // }

      // Inisialisasi status tombol submit perusahaan
      updateSubmitButtonStatePerusahaan();
    });

    // Function untuk load kota berdasarkan provinsi (form tambah)
    async function loadKotaPerusahaan(id_provinsi) {
      const kotaSelect = document.getElementById('id_kota');
      kotaSelect.innerHTML = '<option value="">Pilih Kota</option>';

      console.log('loadKotaPerusahaan called with id_provinsi:', id_provinsi);

      if (id_provinsi) {
        try {
          const url = `get_kota.php?id_provinsi=${id_provinsi}`;
          console.log('Fetching URL:', url);

          const response = await fetch(url);
          console.log('Response status:', response.status);

          const data = await response.json();
          console.log('Response data:', data);

          if (data.success) {
            console.log('Kota count:', data.data.length);
            data.data.forEach(kota => {
              kotaSelect.innerHTML += `<option value="${kota.id_kota}">${kota.nama_kota}</option>`;
            });
            console.log('Kota options loaded successfully');
          } else {
            console.error('API returned error:', data.message);
          }
        } catch (error) {
          console.error('Error loading kota:', error);
        }
      }
    }

    // Function untuk load kota berdasarkan provinsi (form edit)
    async function loadKotaEditPerusahaan(id_provinsi) {
      const kotaSelect = document.getElementById('edit_id_kota');
      kotaSelect.innerHTML = '<option value="">Pilih Kota</option>';

      console.log('loadKotaEditPerusahaan called with id_provinsi:', id_provinsi);

      if (id_provinsi) {
        try {
          const url = `get_kota.php?id_provinsi=${id_provinsi}`;
          console.log('Fetching URL (edit):', url);

          const response = await fetch(url);
          console.log('Response status (edit):', response.status);

          const data = await response.json();
          console.log('Response data (edit):', data);

          if (data.success) {
            console.log('Kota count (edit):', data.data.length);
            data.data.forEach(kota => {
              kotaSelect.innerHTML += `<option value="${kota.id_kota}">${kota.nama_kota}</option>`;
            });
            console.log('Kota options loaded successfully (edit)');
          } else {
            console.error('API returned error (edit):', data.message);
          }
        } catch (error) {
          console.error('Error loading kota (edit):', error);
        }
      }
    }

  </script>
  <!-- Modal Export Perusahaan -->
  <div id="modalExportPerusahaan"
    class="modal-container fixed inset-0 hidden items-center justify-center transition-all duration-300 z-9999">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="toggleModal('modalExportPerusahaan')"></div>
    <div
      class="modal-content bg-white dark:bg-boxdark w-full max-w-md mx-4 rounded-2xl shadow-2xl relative flex flex-col overflow-hidden max-h-[90vh]">
      <!-- Modal Header -->
      <div
        class="sticky top-0 z-10 bg-gradient-to-r from-blue-600 to-blue-700 dark:from-blue-800 dark:to-blue-900 px-6 py-5">
        <div class="flex items-center justify-between">
          <div>
            <h4 class="text-lg font-bold text-white">Export Data Perusahaan</h4>
            <p class="text-xs text-blue-100 mt-1">Pilih filter dan format export data</p>
          </div>
          <button onclick="toggleModal('modalExportPerusahaan')"
            class="text-white/80 hover:text-white transition-colors p-2 hover:bg-white/10 rounded-lg">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M18 6L6 18M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Modal Body -->
      <div class="p-6 overflow-y-auto">
        <form id="exportFormPerusahaan" action="export_perusahaan.php" method="POST" class="space-y-6">
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

            <div class="grid grid-cols-2 gap-4"
              style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
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

              <!-- Filter by Nama Perusahaan -->
              <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Nama Perusahaan</label>
                <input type="text" name="filter_nama"
                  class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                  placeholder="Cari nama perusahaan...">
              </div>

              <!-- Filter by Email -->
              <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                <input type="text" name="filter_email"
                  class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                  placeholder="Cari email...">
              </div>

              <!-- Filter by Lokasi -->
              <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Lokasi</label>
                <input type="text" name="filter_lokasi"
                  class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                  placeholder="Cari lokasi...">
              </div>

              <!-- Filter by No. HP -->
              <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">No. HP</label>
                <input type="text" name="filter_nohp"
                  class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                  placeholder="Cari no. HP...">
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
                <input type="checkbox" name="columns[]" value="id_perusahaan" checked
                  class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                <div class="flex-1">
                  <span class="text-sm font-medium text-gray-700 dark:text-gray-300">ID Perusahaan</span>
                  <p class="text-xs text-gray-500">Nomor identifikasi perusahaan</p>
                </div>
              </label>

              <label
                class="flex items-center gap-3 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <input type="checkbox" name="columns[]" value="nama_perusahaan" checked
                  class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                <div class="flex-1">
                  <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Nama Perusahaan</span>
                  <p class="text-xs text-gray-500">Nama lengkap perusahaan</p>
                </div>
              </label>

              <label
                class="flex items-center gap-3 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <input type="checkbox" name="columns[]" value="email_perusahaan" checked
                  class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                <div class="flex-1">
                  <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Email</span>
                  <p class="text-xs text-gray-500">Email perusahaan</p>
                </div>
              </label>

              <label
                class="flex items-center gap-3 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <input type="checkbox" name="columns[]" value="nohp_perusahaan" checked
                  class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                <div class="flex-1">
                  <span class="text-sm font-medium text-gray-700 dark:text-gray-300">No. HP</span>
                  <p class="text-xs text-gray-500">Nomor telepon perusahaan</p>
                </div>
              </label>

              <label
                class="flex items-center gap-3 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <input type="checkbox" name="columns[]" value="lokasi" checked
                  class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                <div class="flex-1">
                  <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Lokasi</span>
                  <p class="text-xs text-gray-500">Kota, Provinsi</p>
                </div>
              </label>

              <label
                class="flex items-center gap-3 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <input type="checkbox" name="columns[]" value="alamat_perusahaan"
                  class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                <div class="flex-1">
                  <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Alamat</span>
                  <p class="text-xs text-gray-500">Alamat lengkap perusahaan</p>
                </div>
              </label>

              <label
                class="flex items-center gap-3 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <input type="checkbox" name="columns[]" value="tanggal_daftar"
                  class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                <div class="flex-1">
                  <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Daftar</span>
                  <p class="text-xs text-gray-500">Tanggal registrasi perusahaan</p>
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
                style="background-color: #16a34a; color: white; hover:background-color: #15803d;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                CSV
              </button>

              <button type="submit" name="format" value="excel"
                class="flex items-center gap-2 px-4 py-2 rounded-lg font-medium text-sm transition-colors shadow-sm"
                style="background-color: #2563eb; color: white; hover:background-color: #1d4ed8;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 17v1a1 1 0 001 1h4a1 1 0 001-1v-1m-6 0h6m-6 0V9a2 2 0 012-2h2a2 2 0 012 2v8m-6 0h6" />
                </svg>
                Excel
              </button>

              <button type="submit" name="format" value="pdf"
                class="flex items-center gap-2 px-4 py-2 rounded-lg font-medium text-sm transition-colors shadow-sm"
                style="background-color: #dc2626; color: white; hover:background-color: #b91c1c;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                PDF
              </button>
            </div>

            <!-- Print Section -->
            <div class="mt-4">
              <div class="flex gap-3 justify-center py-3">
                <button type="button" onclick="printPerusahaanData()"
                  class="flex items-center gap-2 px-4 py-2 rounded-lg font-medium text-sm transition-colors shadow-sm"
                  style="background-color: #7c3aed; color: white; hover:background-color: #6d28d9;">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm-8 0h2v2H7v-2z" />
                  </svg>
                  Print Data
                </button>

                <button type="button" onclick="printPerusahaanPreview()"
                  class="flex items-center gap-2 px-4 py-2 rounded-lg font-medium text-sm transition-colors shadow-sm"
                  style="background-color: #0891b2; color: white; hover:background-color: #0e7490;">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                  Preview
                </button>
              </div>
            </div>
          </div>

          <!-- Hidden field untuk data -->
          <input type="hidden" name="action" value="export">
        </form>
      </div>
    </div>
  </div>

</body>

</html>