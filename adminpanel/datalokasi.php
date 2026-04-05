<?php
require_once 'session_check.php';
include 'koneksi.php';

$db = new database();

// Handle AJAX requests - TANPA output buffer yang bermasalah
// Tangani hapus provinsi via AJAX
if (isset($_GET['action']) && $_GET['action'] == 'hapus_provinsi' && isset($_GET['id'])) {
    header('Content-Type: application/json');
    $id = $_GET['id'];
    $ajax_response = ['success' => false, 'message' => ''];

    if (is_numeric($id)) {
        if ($db->hapus_provinsi($id)) {
            $ajax_response['success'] = true;
            $ajax_response['message'] = 'Data provinsi berhasil dihapus';
        } else {
            $ajax_response['message'] = 'Gagal menghapus data provinsi';
        }
    } else {
        $ajax_response['message'] = 'ID tidak valid';
    }
    echo json_encode($ajax_response);
    exit;
}

// Tangani hapus kota via AJAX
if (isset($_GET['action']) && $_GET['action'] == 'hapus_kota' && isset($_GET['id'])) {
    header('Content-Type: application/json');
    $id = $_GET['id'];
    $ajax_response = ['success' => false, 'message' => ''];

    if (is_numeric($id)) {
        if ($db->hapus_kota($id)) {
            $ajax_response['success'] = true;
            $ajax_response['message'] = 'Data kota berhasil dihapus';
        } else {
            $ajax_response['message'] = 'Gagal menghapus data kota';
        }
    } else {
        $ajax_response['message'] = 'ID tidak valid';
    }
    echo json_encode($ajax_response);
    exit;
}

// Tangani edit provinsi via AJAX
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit_provinsi') {
    header('Content-Type: application/json');
    $id_provinsi = $_POST['id_provinsi'];
    $nama_provinsi = $_POST['nama_provinsi'];
    $ajax_response = ['success' => false, 'message' => ''];

    if (is_numeric($id_provinsi) && !empty($nama_provinsi)) {
        if ($db->update_provinsi($id_provinsi, $nama_provinsi)) {
            $ajax_response['success'] = true;
            $ajax_response['message'] = 'Data provinsi berhasil diperbarui';
        } else {
            $ajax_response['message'] = 'Gagal mengupdate data provinsi';
        }
    } else {
        $ajax_response['message'] = 'ID tidak valid atau nama provinsi kosong';
    }
    echo json_encode($ajax_response);
    exit;
}

// Tangani edit kota via AJAX
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit_kota') {
    header('Content-Type: application/json');
    $id_kota = $_POST['id_kota'];
    $id_provinsi = $_POST['id_provinsi'];
    $nama_kota = $_POST['nama_kota'];
    $ajax_response = ['success' => false, 'message' => ''];

    if (is_numeric($id_kota) && is_numeric($id_provinsi) && !empty($nama_kota)) {
        if ($db->update_kota($id_kota, $id_provinsi, $nama_kota)) {
            $ajax_response['success'] = true;
            $ajax_response['message'] = 'Data kota berhasil diperbarui';
        } else {
            $ajax_response['message'] = 'Gagal mengupdate data kota';
        }
    } else {
        $ajax_response['message'] = 'ID tidak valid atau data tidak lengkap';
    }
    echo json_encode($ajax_response);
    exit;
}

// Tangani tambah provinsi via AJAX
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'tambah_provinsi') {
    header('Content-Type: application/json');
    $nama_provinsi = $_POST['nama_provinsi'];
    $ajax_response = ['success' => false, 'message' => ''];

    if (!empty($nama_provinsi)) {
        if ($db->tambah_provinsi($nama_provinsi)) {
            $ajax_response['success'] = true;
            $ajax_response['message'] = 'Data provinsi berhasil ditambahkan';
        } else {
            $ajax_response['message'] = 'Gagal menambahkan data provinsi';
        }
    } else {
        $ajax_response['message'] = 'Nama provinsi tidak boleh kosong';
    }
    echo json_encode($ajax_response);
    exit;
}

// Tangani tambah kota via AJAX
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'tambah_kota') {
    header('Content-Type: application/json');
    $id_provinsi = $_POST['id_provinsi'];
    $nama_kota = $_POST['nama_kota'];
    $ajax_response = ['success' => false, 'message' => ''];

    if (is_numeric($id_provinsi) && !empty($nama_kota)) {
        if ($db->tambah_kota($id_provinsi, $nama_kota)) {
            $ajax_response['success'] = true;
            $ajax_response['message'] = 'Data kota berhasil ditambahkan';
        } else {
            $ajax_response['message'] = 'Gagal menambahkan data kota';
        }
    } else {
        $ajax_response['message'] = 'Data tidak lengkap atau tidak valid';
    }
    echo json_encode($ajax_response);
    exit;
}

// HAPUS semua ob_start(), ob_clean(), dan header yang bermasalah

// Ambil data provinsi
$data_provinsi = $db->tampil_data_provinsi();

// Ambil data kota dengan join provinsi
$data_kota = $db->tampil_data_kota();

// Tangani hapus provinsi (legacy - non-AJAX)
if (isset($_GET['action']) && $_GET['action'] == 'hapus_provinsi' && isset($_GET['id'])) {
    $id = $_GET['id'];
    if (is_numeric($id)) {
        if ($db->hapus_provinsi($id)) {
            $data_provinsi = $db->tampil_data_provinsi();
            $data_kota = $db->tampil_data_kota();
        }
    }
}

// Tangani hapus kota (legacy - non-AJAX)
if (isset($_GET['action']) && $_GET['action'] == 'hapus_kota' && isset($_GET['id'])) {
    $id = $_GET['id'];
    if (is_numeric($id)) {
        if ($db->hapus_kota($id)) {
            $data_kota = $db->tampil_data_kota();
        }
    }
}

// Tangani edit provinsi (legacy - non-AJAX)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit_provinsi') {
    $id_provinsi = $_POST['id_provinsi'];
    $nama_provinsi = $_POST['nama_provinsi'];

    if ($db->update_provinsi($id_provinsi, $nama_provinsi)) {
        $data_provinsi = $db->tampil_data_provinsi();
    }
}

// Tangani edit kota (legacy - non-AJAX)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit_kota') {
    $id_kota = $_POST['id_kota'];
    $id_provinsi = $_POST['id_provinsi'];
    $nama_kota = $_POST['nama_kota'];

    if ($db->update_kota($id_kota, $id_provinsi, $nama_kota)) {
        $data_kota = $db->tampil_data_kota();
    }
}

// Tangani tambah provinsi (legacy - non-AJAX)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'tambah_provinsi') {
    $nama_provinsi = $_POST['nama_provinsi'];

    if ($db->tambah_provinsi($nama_provinsi)) {
        $data_provinsi = $db->tampil_data_provinsi();
    }
}

// Tangani tambah kota (legacy - non-AJAX)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'tambah_kota') {
    $id_provinsi = $_POST['id_provinsi'];
    $nama_kota = $_POST['nama_kota'];

    if ($db->tambah_kota($id_provinsi, $nama_kota)) {
        $data_kota = $db->tampil_data_kota();
    }
}

// Pastikan tidak ada output sebelum HTML
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Data Lokasi | LinkUp</title>
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

        /* Style untuk tabel */
        #provinsiTable tbody tr {
            height: 60px !important;
        }

        #provinsiTable td {
            padding-top: 0.75rem !important;
            padding-bottom: 0.75rem !important;
            padding-left: 1rem !important;
            padding-right: 1rem !important;
        }

        #provinsiTable th {
            padding-top: 0.75rem !important;
            padding-bottom: 0.75rem !important;
        }

        .btn-tambah {
            padding: 0.5rem 0.75rem !important;
            font-size: 0.75rem !important;
        }

        .btn-tambah svg {
            width: 16px !important;
            height: 16px !important;
        }

        /* Modal */
        .modal {
            display: none;
        }

        .modal:not(.hidden) {
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

        /* Badge */
        .provinsi-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            background-color: #e0e7ff;
            color: #3730a3;
            border: 1px solid #c7d2fe;
        }

        .dark .provinsi-badge {
            background-color: #3730a3;
            color: #e0e7ff;
            border-color: #4f46e5;
        }

        /* Simple card */
        .location-card {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .location-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 14px;
        }

        .provinsi-icon {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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

        .select-control {
            width: 100%;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            background-color: white;
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.5rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
        }

        .dark .select-control {
            background-color: #374151;
            border-color: #4b5563;
            color: #f3f4f6;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%239ca3af' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
        }

        /* Modal Lihat Kota - Perbaikan background transparan */
        .modal-table-container {
            max-height: 400px;
            overflow-y: auto;
            border-radius: 0.375rem;
            border: 1px solid #e5e7eb;
        }

        .dark .modal-table-container {
            border-color: #374151;
        }

        .modal-table-container table {
            min-width: 100%;
            background-color: white;
        }

        .dark .modal-table-container table {
            background-color: #1f2937;
        }

        /* Header tabel di modal */
        .modal-table-container thead th {
            background-color: #f9fafb;
            position: sticky;
            top: 0;
            z-index: 20;
            border-bottom: 2px solid #e5e7eb;
        }

        .dark .modal-table-container thead th {
            background-color: #374151;
            border-bottom-color: #4b5563;
        }

        /* Sel tabel di modal */
        .modal-table-container tbody td {
            background-color: white;
            border-bottom: 1px solid #f3f4f6;
        }

        .dark .modal-table-container tbody td {
            background-color: #1f2937;
            border-bottom-color: #374151;
        }

        /* Hover effect */
        .modal-table-container tbody tr:hover td {
            background-color: #f9fafb;
        }

        .dark .modal-table-container tbody tr:hover td {
            background-color: #374151;
        }

        /* Modal content khusus untuk Lihat Kota */
        .modal-content-small {
            max-width: 600px !important;
            width: 90% !important;
            max-height: 70vh !important;
            background-color: white !important;
        }

        .dark .modal-content-small {
            background-color: #1f2937 !important;
        }

        /* Header modal */
        .modal-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .dark .modal-header {
            border-bottom-color: #374151;
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

        .modal-header {
            position: sticky;
            top: 0;
            z-index: 30;
            background-color: white;
            border-bottom: 1px solid #e5e7eb;
        }

        .dark .modal-header {
            background-color: #1f2937;
            border-bottom-color: #374151;
        }

        /* Efek hover untuk row tabel di modal */
        #kotaByProvinsiBody tr {
            transition: background-color 0.2s ease;
        }

        #kotaByProvinsiBody tr:hover {
            background-color: #f3f4f6 !important;
        }

        .dark #kotaByProvinsiBody tr:hover {
            background-color: #374151 !important;
        }

        /* Pastikan konten tidak transparan saat scroll */
        .modal-table-container::-webkit-scrollbar {
            width: 6px;
        }

        .modal-table-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .dark .modal-table-container::-webkit-scrollbar-track {
            background: #374151;
        }

        .modal-table-container::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        .dark .modal-table-container::-webkit-scrollbar-thumb {
            background: #6b7280;
        }

        .modal-table-container::-webkit-scrollbar-thumb:hover {
            background: #a1a1a1;
        }

        .dark .modal-table-container::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }

        /* Tombol aksi dalam satu baris */
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        /* Tambahkan di bagian <style> Anda */
        .btn-hapus {
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
        }

        .btn-hapus:hover {
            background-color: #dc2626 !important;
        }

        /* Untuk modal konfirmasi hapus */
        #confirmDeleteProvinsiLink,
        #confirmDeleteKotaLink {
            display: inline-flex !important;
            align-items: center !important;
            gap: 8px !important;
            padding: 0.5rem 1rem !important;
            font-size: 0.875rem !important;
            background-color: #ef4444 !important;
            color: white !important;
            border-radius: 0.375rem !important;
            text-decoration: none !important;
            border: none !important;
        }

        #confirmDeleteProvinsiLink:hover,
        #confirmDeleteKotaLink:hover {
            background-color: #dc2626 !important;
        }

        /* Pastikan tombol tetap terlihat di DataTables */
        .action-buttons .btn-tambah {
            visibility: visible !important;
            opacity: 1 !important;
        }

        /* Override DataTables styling */
        #provinsiTable td .action-buttons button {
            visibility: visible !important;
            display: inline-flex !important;
        }

        /* Perkecil ukuran font tabel */
        #provinsiTable {
            font-size: 0.75rem !important;
            /* 12px */
        }

        #provinsiTable th,
        #provinsiTable td {
            padding: 0.5rem 0.75rem !important;
            /* Perkecil padding */
            height: 40px !important;
            /* Perkecil tinggi row */
        }

        #provinsiTable thead th {
            font-size: 0.75rem !important;
            font-weight: 600;
        }

        /* Perkecil badge jumlah kota */
        .provinsi-badge {
            font-size: 0.6875rem !important;
            /* 11px */
            padding: 0.25rem 0.5rem !important;
        }

        /* Perkecil tombol aksi */
        .action-buttons button {
            font-size: 0.6875rem !important;
            /* 11px */
            padding: 0.375rem 0.625rem !important;
            gap: 0.25rem !important;
        }

        .action-buttons svg {
            width: 12px !important;
            height: 12px !important;
        }

        /* Perkecil card lokasi */
        .location-card {
            gap: 6px;
        }

        .location-icon {
            width: 24px !important;
            height: 24px !important;
            font-size: 12px !important;
            border-radius: 6px;
        }

        /* Perkecil header tabel */
        .border-b.border-stroke.px-4.py-4 {
            padding: 0.75rem 1rem !important;
        }

        .border-b.border-stroke.px-4.py-4 h3 {
            font-size: 0.875rem !important;
            /* 14px */
        }

        /* Perkecil konten area */
        .p-4.sm\:p-6.xl\:p-7\.5 {
            padding: 1rem !important;
        }

        @media (min-width: 640px) {
            .p-4.sm\:p-6.xl\:p-7\.5 {
                padding: 1.25rem !important;
            }
        }

        /* Perkecil tombol tambah di header */
        .btn-tambah {
            padding: 0.375rem 0.625rem !important;
            font-size: 0.6875rem !important;
        }

        .btn-tambah svg {
            width: 12px !important;
            height: 12px !important;
        }

        /* Perkecil input search DataTables */
        #provinsiTable_filter input {
            font-size: 0.75rem !important;
            height: 32px !important;
            padding: 0.25rem 0.5rem !important;
        }

        /* Perkecil pagination */
        .dataTables_paginate .paginate_button {
            min-width: 24px !important;
            height: 24px !important;
            font-size: 0.6875rem !important;
            margin: 0 0.125rem !important;
        }

        /* Perkecil dropdown "Show entries" */
        #provinsiTable_length select {
            font-size: 0.75rem !important;
            height: 32px !important;
            padding: 0.25rem 1.5rem 0.25rem 0.5rem !important;
        }

        /* Tambahkan margin atas untuk konten utama */
        main {
            margin-top: 1rem !important;
            margin-bottom: 1rem !important;
        }

        /* Atau tambahkan padding pada container utama */
        .mx-auto.max-w-screen-2xl.p-4.md\:p-6.2xl\:p-10 {
            padding-top: 1rem !important;
            padding-bottom: 1rem !important;
        }

        /* Untuk lebih banyak ruang, Anda bisa menambahkan: */
        main>div {
            margin-top: 0.5rem;
            margin-bottom: 0.5rem;
        }

        /* Class untuk margin atas/bawah */
        .my-compact {
            margin-top: 0.5rem !important;
            margin-bottom: 0.5rem !important;
        }

        /* Class untuk padding kompak */
        .p-compact {
            padding: 0.5rem !important;
        }

        /* Untuk header tabel yang lebih kompak */
        .table-header-compact {
            padding: 0.5rem 1rem !important;
            min-height: 40px !important;
        }

        .table-header-compact h3 {
            font-size: 0.875rem !important;
            margin: 0 !important;
        }

        /* ===== STYLING UMUM ===== */
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

        /* ===== STYLING TABEL (SAMA SEPERTI DATAADMIN) ===== */
        /* Wrapper untuk DataTables - SAMA PERSIS dengan dataadmin */
        #provinsiTable_wrapper {
            font-size: 0.75rem !important;
        }

        /* Kontrol DataTables (Show entries dan Search) */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            font-size: 0.75rem !important;
        }

        .dataTables_wrapper .dataTables_length select {
            padding: 0.25rem 0.5rem !important;
            font-size: 0.75rem !important;
            height: 1.75rem !important;
            border-radius: 0.375rem !important;
            border: 1px solid #d1d5db !important;
            background-color: transparent !important;
        }

        .dataTables_wrapper .dataTables_filter input {
            padding: 0.25rem 0.5rem !important;
            font-size: 0.75rem !important;
            height: 1.75rem !important;
            border-radius: 0.375rem !important;
            border: 1px solid #d1d5db !important;
            background-color: transparent !important;
        }

        .dark .dataTables_wrapper .dataTables_length select,
        .dark .dataTables_wrapper .dataTables_filter input {
            border-color: #4b5563 !important;
            background-color: #374151 !important;
            color: #f3f4f6 !important;
        }

        /* Pagination - SAMA dengan dataadmin */
        .dataTables_wrapper .dataTables_paginate {
            font-size: 0.75rem !important;
            margin-top: 0.75rem !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.125rem 0.375rem !important;
            font-size: 0.6875rem !important;
            height: 1.5rem !important;
            min-width: 1.5rem !important;
            margin: 0 0.125rem !important;
            border: 1px solid #d1d5db !important;
            border-radius: 0.375rem !important;
            background-color: transparent !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background-color: rgb(59 130 246) !important;
            color: white !important;
            border-color: rgb(59 130 246) !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:not(.current):hover {
            background-color: rgb(243 244 246) !important;
            color: rgb(55 65 81) !important;
        }

        .dark .dataTables_wrapper .dataTables_paginate .paginate_button:not(.current):hover {
            background-color: rgb(55 65 81) !important;
            color: rgb(229 231 235) !important;
        }

        /* Info text */
        .dataTables_wrapper .dataTables_info {
            font-size: 0.75rem !important;
            margin-top: 0.75rem !important;
        }

        /* ===== TAMPILAN TABEL UTAMA ===== */
        /* Header tabel */
        #provinsiTable thead th {
            padding: 0.75rem 1rem !important;
            font-size: 0.75rem !important;
            font-weight: 600;
            background-color: #f9fafb;
        }

        .dark #provinsiTable thead th {
            background-color: #374151;
            color: #f9fafb;
        }

        /* Baris tabel */
        #provinsiTable tbody tr {
            height: 60px !important;
            border-bottom: 1px solid #e5e7eb;
        }

        .dark #provinsiTable tbody tr {
            border-bottom-color: #374151;
        }

        /* Sel tabel */
        #provinsiTable td {
            padding: 0.75rem 1rem !important;
            vertical-align: middle !important;
        }

        /* Hover effect */
        #provinsiTable tbody tr:hover {
            background-color: #f9fafb !important;
        }

        .dark #provinsiTable tbody tr:hover {
            background-color: #374151 !important;
        }

        /* ===== STYLING KOMPONEN KHUSUS ===== */
        /* Badge jumlah kota */
        .provinsi-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.6875rem;
            font-weight: 500;
            background-color: #e0e7ff;
            color: #3730a3;
            border: 1px solid #c7d2fe;
        }

        .dark .provinsi-badge {
            background-color: #3730a3;
            color: #e0e7ff;
            border-color: #4f46e5;
        }

        /* Location card */
        .location-card {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .location-icon {
            width: 2rem;
            height: 2rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.75rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        /* Tombol aksi */
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .action-buttons button {
            padding: 0.375rem 0.625rem !important;
            font-size: 0.6875rem !important;
            border-radius: 0.375rem !important;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            transition: all 0.2s ease;
        }

        .action-buttons button svg {
            width: 12px;
            height: 12px;
        }

        /* Tombol tambah di header */
        .btn-tambah {
            padding: 0.5rem 0.75rem !important;
            font-size: 0.75rem !important;
        }

        .btn-tambah svg {
            width: 16px !important;
            height: 16px !important;
        }

        /* ===== MODAL STYLING ===== */
        .modal {
            display: none;
        }

        .modal:not(.hidden) {
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

        /* ===== MARGIN ATAS/BAWAH ===== */
        /* Kurangi margin di bagian atas halaman */
        main {
            margin-top: 0.5rem !important;
        }

        .mx-auto.max-w-screen-2xl.p-4.md\:p-6.2xl\:p-10 {
            padding-top: 0.5rem !important;
            padding-bottom: 1rem !important;
        }

        /* Kompensasi untuk breadcrumb */
        .mb-6.flex.flex-col.gap-3 {
            margin-bottom: 1rem !important;
        }

        /* Header tabel yang lebih kompak */
        .border-b.border-stroke {
            padding: 0.75rem 1rem !important;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 640px) {

            /* Search box lebih kecil di mobile */
            .dataTables_wrapper .dataTables_filter input {
                width: 120px !important;
            }

            /* Tombol aksi dalam kolom terakhir */
            .action-buttons {
                flex-direction: column;
                gap: 0.25rem;
            }

            .action-buttons button {
                width: 100%;
                justify-content: center;
            }

            /* Layout header untuk mobile */
            .border-b.border-stroke .flex {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .border-b.border-stroke .flex .flex {
                width: 100%;
                justify-content: flex-start;
            }
        }

        /* ===== DARK MODE FIXES ===== */
        .dark #provinsiTable {
            background-color: #1f2937;
            color: #f3f4f6;
        }

        .dark #provinsiTable thead th {
            background-color: #374151;
            color: #f9fafb;
        }

        .dark #provinsiTable tbody tr {
            background-color: #1f2937;
        }

        .dark #provinsiTable tbody tr:hover {
            background-color: #374151;
        }

        /* Input fields dalam dark mode */
        .dark .dataTables_wrapper input,
        .dark .dataTables_wrapper select {
            background-color: #374151 !important;
            border-color: #4b5563 !important;
            color: #f3f4f6 !important;
        }

        .dark .dataTables_wrapper input:focus,
        .dark .dataTables_wrapper select:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
        }

        /* KURANGI MARGIN ATAS HALAMAN */
        main>div {
            margin-top: 0 !important;
        }

        /* Kurangi padding container utama */
        .mx-auto.max-w-screen-2xl.p-4.md\:p-6.2xl\:p-10 {
            padding-top: 0.25rem !important;
            padding-bottom: 0.75rem !important;
        }

        /* Breadcrumb lebih kompak */
        .mb-6.flex.flex-col.gap-3 {
            margin-bottom: 0.75rem !important;
        }

        /* Title lebih kecil */
        .text-title-md2 {
            font-size: 1.25rem !important;
            line-height: 1.75rem !important;
        }

        /* PERBAIKAN UNTUK MODAL LIHAT KOTA - AGAR SEMUA BARIS TERLIHAT */

        /* 1. Perbaikan container modal */
        .modal-content-small {
            max-width: 700px !important;
            /* Lebarkan sedikit */
            width: 90% !important;
            max-height: 85vh !important;
            /* Tinggi lebih besar */
            background-color: white !important;
            display: flex;
            flex-direction: column;
        }

        .dark .modal-content-small {
            background-color: #1f2937 !important;
        }

        /* 2. Perbaikan area tabel agar lebih fleksibel */
        .modal-table-container {
            flex: 1;
            /* Ambil sisa ruang yang tersedia */
            overflow-y: auto;
            border-radius: 0.375rem;
            border: 1px solid #e5e7eb;
            max-height: calc(85vh - 180px) !important;
            /* Hitung otomatis berdasarkan tinggi modal */
        }

        /* 3. Pastikan header tabel tetap terlihat */
        .modal-table-container thead th {
            background-color: #f9fafb;
            position: sticky;
            top: 0;
            z-index: 20;
            border-bottom: 2px solid #e5e7eb;
            backdrop-filter: blur(8px);
        }

        .dark .modal-table-container thead th {
            background-color: #374151;
            border-bottom-color: #4b5563;
        }

        /* 4. Pastikan setiap baris terlihat jelas */
        .modal-table-container tbody tr {
            height: 48px !important;
            /* Tinggi tetap untuk setiap baris */
        }

        .modal-table-container tbody td {
            padding: 0.75rem 1rem !important;
            background-color: white !important;
            /* Pastikan tidak transparan */
            border-bottom: 1px solid #f3f4f6;
        }

        .dark .modal-table-container tbody td {
            background-color: #1f2937 !important;
            border-bottom-color: #374151;
        }

        /* 5. Hover effect yang jelas */
        #kotaByProvinsiBody tr:hover td {
            background-color: #f3f4f6 !important;
        }

        .dark #kotaByProvinsiBody tr:hover td {
            background-color: #374151 !important;
        }

        /* 6. Perbaikan untuk banyak baris */
        .modal-table-container table {
            min-width: 100%;
            table-layout: fixed;
            /* Layout tabel tetap */
        }

        .modal-table-container th,
        .modal-table-container td {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* 7. Kolom dengan leproporsi yang lebih baik */
        .modal-table-container th:nth-child(1) {
            /* ID Kota */
            width: 20%;
        }

        .modal-table-container th:nth-child(2) {
            /* Nama Kota */
            width: 45%;
        }

        .modal-table-container th:nth-child(3) {
            /* Aksi */
            width: 35%;
        }

        /* 8. Perbaikan scrollbar */
        .modal-table-container::-webkit-scrollbar {
            width: 8px;
        }

        .modal-table-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .dark .modal-table-container::-webkit-scrollbar-track {
            background: #374151;
        }

        .modal-table-container::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        .dark .modal-table-container::-webkit-scrollbar-thumb {
            background: #6b7280;
        }

        .modal-table-container::-webkit-scrollbar-thumb:hover {
            background: #a1a1a1;
        }

        .dark .modal-table-container::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }

        /* 9. Pastikan modal cukup tinggi untuk menampung semua data */
        @media (max-height: 800px) {
            .modal-content-small {
                max-height: 90vh !important;
                /* Lebih tinggi di layar kecil */
            }

            .modal-table-container {
                max-height: calc(90vh - 180px) !important;
            }
        }

        /* 10. Perbaikan untuk tombol aksi dalam modal */
        #kotaByProvinsiBody .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: nowrap;
        }

        #kotaByProvinsiBody .action-buttons button {
            padding: 0.375rem 0.75rem !important;
            font-size: 0.75rem !important;
            white-space: nowrap;
        }

        /* 11. Pastikan tidak ada baris yang terpotong */
        .modal-table-container tr:last-child td {
            border-bottom: none !important;
        }

        /* Pastikan baris terakhir terlihat */
        #kotaByProvinsiBody tr:last-child {
            border-bottom: 1px solid #e5e7eb !important;
        }

        .dark #kotaByProvinsiBody tr:last-child {
            border-bottom-color: #374151 !important;
        }

        /* Highlight untuk baris terakhir (opsional) */
        #kotaByProvinsiBody tr:last-child td {
            background-color: #fafafa !important;
        }

        .dark #kotaByProvinsiBody tr:last-child td {
            background-color: #1a202c !important;
        }
    </style>
</head>

<body
    x-data="{ page: 'dataLokasi', 'loaded': true, 'darkMode': false, 'stickyMenu': false, 'sidebarToggle': false, 'scrollTop': false, 'modalOpen': false }"
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
                            Data Lokasi
                        </h2>
                        <nav>
                            <ol class="flex items-center gap-2">
                                <li><a class="font-medium" href="index.php">Home ></a></li>
                                <li class="font-medium text-primary">Data Lokasi</li>
                            </ol>
                        </nav>
                    </div>

                    <!-- Tabel Data Provinsi (TANPA TAB) -->
                    <div
                        class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                        <!-- HEADER TABEL YANG LEBIH KOMPAK -->
                        <div
                            class="border-b border-stroke px-4 py-3 dark:border-strokedark flex justify-between items-center">
                            <h3 class="font-medium text-black dark:text-white text-sm">Tabel Data Provinsi</h3>
                            <div class="flex gap-1">
                                <button onclick="toggleModal('modalTambahKota')" style="background-color: #0c87c1ff;"
                                    class="btn-tambah inline-flex items-center gap-1 text-xs font-medium text-white transition rounded-lg bg-brand-500 shadow-theme-xs hover:bg-brand-600">
                                    <svg class="fill-current" width="12" height="12" viewBox="0 0 20 20" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M10 3a1 1 0 00-1 1v5H4a1 1 0 100 2h5v5a1 1 0 102 0v-5h5a1 1 0 100-2h-5V4a1 1 0 00-1-1z"
                                            fill="" />
                                    </svg>
                                    Tambah Kota
                                </button>
                                <button onclick="toggleModal('modalTambahProvinsi')"
                                    class="btn-tambah inline-flex items-center gap-1 text-xs font-medium text-white transition rounded-lg bg-brand-500 shadow-theme-xs hover:bg-brand-600">
                                    <svg class="fill-current" width="12" height="12" viewBox="0 0 20 20" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M10 3a1 1 0 00-1 1v5H4a1 1 0 100 2h5v5a1 1 0 102 0v-5h5a1 1 0 100-2h-5V4a1 1 0 00-1-1z"
                                            fill="" />
                                    </svg>
                                    Tambah Provinsi
                                </button>
                            </div>
                        </div>

                        <!-- CONTENT TABEL -->
                        <div class="p-3">
                            <table id="provinsiTable" class="w-full table-auto border-collapse text-left text-sm">
                                <thead>
                                    <tr class="bg-gray-2 dark:bg-meta-4">
                                        <th class="px-4 py-3 font-medium text-black dark:text-white">ID Provinsi
                                        </th>
                                        <th class="px-4 py-3 font-medium text-black dark:text-white">Nama Provinsi
                                        </th>
                                        <th class="px-4 py-3 font-medium text-black dark:text-white">Jumlah Kota
                                        </th>
                                        <th class="px-4 py-3 font-medium text-black dark:text-white">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (!empty($data_provinsi)) {
                                        foreach ($data_provinsi as $row) {
                                            $format_id = "P" . sprintf("%04d", $row['id_provinsi']);
                                            $jumlah_kota = $db->get_jumlah_kota_by_provinsi($row['id_provinsi']);
                                            ?>
                                            <tr
                                                class="border-b border-[#eee] dark:border-strokedark hover:bg-gray-50 dark:hover:bg-meta-4">
                                                <td class="px-4 py-3">
                                                    <p class="text-black dark:text-white font-medium text-sm">
                                                        <?php echo $format_id; ?>
                                                    </p>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="location-card">
                                                        <div class="location-icon provinsi-icon">
                                                            <?php echo strtoupper(substr($row['nama_provinsi'], 0, 1)); ?>
                                                        </div>
                                                        <div>
                                                            <h5 class="font-medium text-black dark:text-white text-sm">
                                                                <?php echo htmlspecialchars($row['nama_provinsi']); ?>
                                                            </h5>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <span class="provinsi-badge">
                                                        <?php echo $jumlah_kota; ?> Kota
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="action-buttons">
                                                        <button
                                                            onclick="showEditProvinsi(<?php echo $row['id_provinsi']; ?>, '<?php echo addslashes($row['nama_provinsi']); ?>')"
                                                            style="background-color: #2563eb;"
                                                            class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium text-white transition rounded-lg hover:opacity-80">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5"
                                                                viewBox="0 0 20 20" fill="currentColor">
                                                                <path
                                                                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                            </svg>
                                                            Edit
                                                        </button>
                                                        <button
                                                            onclick="showLihatKota(<?php echo $row['id_provinsi']; ?>, '<?php echo addslashes($row['nama_provinsi']); ?>')"
                                                            style="background-color: #e79911ff;"
                                                            class="btn-tambah inline-flex items-center gap-2 text-xs font-medium text-white transition rounded-lg bg-brand-500 shadow-theme-xs hover:bg-brand-600">
                                                            <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                                <path fill-rule="evenodd"
                                                                    d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                                                    clip-rule="evenodd" />
                                                            </svg>
                                                            Lihat Kota
                                                        </button>
                                                        <button
                                                            onclick="showDeleteProvinsi(<?php echo $row['id_provinsi']; ?>, '<?php echo addslashes($row['nama_provinsi']); ?>')"
                                                            style="background-color: #dc2626;"
                                                            class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium text-white transition rounded-lg hover:opacity-80">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5"
                                                                viewBox="0 0 20 20" fill="currentColor">
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
                                        echo "<tr><td colspan='4' class='text-center py-4'>Tidak ada data provinsi</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
            <!-- ===== Main Content End ===== -->
        </div>
        <!-- ===== Content Area End ===== -->
    </div>

    <!-- Modal Tambah Provinsi -->
    <div id="modalTambahProvinsi"
        class="modal-container fixed inset-0 hidden items-center justify-center transition-all duration-300 z-9999">
        <div class="absolute inset-0 bg-black/20" onclick="hideAllModals()"></div>
        <div
            class="modal-content bg-white dark:bg-boxdark w-full max-w-md mx-4 rounded-xl shadow-xl relative flex flex-col overflow-hidden max-h-[85vh]">
            <div
                class="sticky top-0 z-10 bg-white dark:bg-boxdark border-b border-stroke dark:border-strokedark px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h5 class="text-lg font-bold text-black dark:text-white">Tambah Provinsi Baru</h5>
                        <p class="text-xs text-gray-500">Isi form untuk menambah data provinsi.</p>
                    </div>
                    <button onclick="hideAllModals()" class="text-gray-400 hover:text-red-500 transition-colors">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path d="M18 6L6 18M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="p-6 overflow-y-auto flex-1">
                <form id="formTambahProvinsi" method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="tambah_provinsi">
                    <div>
                        <label class="block text-sm font-medium mb-1 dark:text-white">Nama Provinsi *</label>
                        <input type="text" name="nama_provinsi" required class="form-control">
                    </div>
                    <div class="flex justify-end gap-2 pt-4">
                        <button type="button" onclick="hideAllModals()"
                            class="px-3 py-2 text-xs font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 dark:bg-meta-4 dark:text-gray-300">Batal</button>
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium text-white transition rounded-lg bg-brand-500 hover:bg-brand-600">
                            <svg class="fill-current" width="14" height="14" viewBox="0 0 20 20" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M16.7071 5.29289C17.0976 5.68342 17.0976 6.31658 16.7071 6.70711L8.70711 14.7071C8.31658 15.0976 7.68342 15.0976 7.29289 14.7071L3.29289 10.7071C2.90237 10.3166 2.90237 9.68342 3.29289 9.29289C3.68342 8.90237 4.31658 8.90237 4.70711 9.29289L8 12.5858L15.2929 5.29289C15.6834 4.90237 16.3166 4.90237 16.7071 5.29289Z"
                                    fill="white" />
                            </svg>
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Provinsi -->
    <div id="modalEditProvinsi"
        class="modal-container fixed inset-0 hidden items-center justify-center transition-all duration-300 z-9999">
        <div class="absolute inset-0 bg-black/20" onclick="hideAllModals()"></div>
        <div
            class="modal-content bg-white dark:bg-boxdark w-full max-w-md mx-4 rounded-xl shadow-xl relative flex flex-col overflow-hidden max-h-[85vh]">
            <div
                class="sticky top-0 z-10 bg-white dark:bg-boxdark border-b border-stroke dark:border-strokedark px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h5 class="text-lg font-bold text-black dark:text-white">Edit Provinsi</h5>
                        <p class="text-xs text-gray-500">Edit data provinsi.</p>
                    </div>
                    <button onclick="hideAllModals()" class="text-gray-400 hover:text-red-500 transition-colors">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path d="M18 6L6 18M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="p-6 overflow-y-auto flex-1">
                <form id="formEditProvinsi" method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="edit_provinsi">
                    <input type="hidden" name="id_provinsi" id="edit_id_provinsi">
                    <div>
                        <label class="block text-sm font-medium mb-1 dark:text-white">Nama Provinsi *</label>
                        <input type="text" name="nama_provinsi" id="edit_nama_provinsi" required class="form-control">
                    </div>
                    <div class="flex justify-end gap-2 pt-4">
                        <button type="button" onclick="hideAllModals()"
                            class="px-3 py-2 text-xs font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 dark:bg-meta-4 dark:text-gray-300">Batal</button>
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium text-white transition rounded-lg bg-brand-500 hover:bg-brand-600">
                            <svg class="fill-current" width="14" height="14" viewBox="0 0 20 20" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M16.7071 5.29289C17.0976 5.68342 17.0976 6.31658 16.7071 6.70711L8.70711 14.7071C8.31658 15.0976 7.68342 15.0976 7.29289 14.7071L3.29289 10.7071C2.90237 10.3166 2.90237 9.68342 3.29289 9.29289C3.68342 8.90237 4.31658 8.90237 4.70711 9.29289L8 12.5858L15.2929 5.29289C15.6834 4.90237 16.3166 4.90237 16.7071 5.29289Z"
                                    fill="white" />
                            </svg>
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Kota -->
    <div id="modalTambahKota"
        class="modal-container fixed inset-0 hidden items-center justify-center transition-all duration-300 z-9999">
        <div class="absolute inset-0 bg-black/20" onclick="hideAllModals()"></div>
        <div
            class="modal-content bg-white dark:bg-boxdark w-full max-w-md mx-4 rounded-xl shadow-xl relative flex flex-col overflow-hidden max-h-[85vh]">
            <div
                class="sticky top-0 z-10 bg-white dark:bg-boxdark border-b border-stroke dark:border-strokedark px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h5 class="text-lg font-bold text-black dark:text-white">Tambah Kota Baru</h5>
                        <p class="text-xs text-gray-500">Isi form untuk menambah data kota.</p>
                    </div>
                    <button onclick="hideAllModals()" class="text-gray-400 hover:text-red-500 transition-colors">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path d="M18 6L6 18M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="p-6 overflow-y-auto flex-1">
                <form id="formTambahKota" method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="tambah_kota">
                    <div>
                        <label class="block text-sm font-medium mb-1 dark:text-white">Provinsi *</label>
                        <select name="id_provinsi" required class="select-control">
                            <option value="">-- Pilih Provinsi --</option>
                            <?php foreach ($data_provinsi as $prov): ?>
                                <option value="<?php echo $prov['id_provinsi']; ?>"><?php echo $prov['nama_provinsi']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1 dark:text-white">Nama Kota *</label>
                        <input type="text" name="nama_kota" required class="form-control">
                    </div>
                    <div class="flex justify-end gap-2 pt-4">
                        <button type="button" onclick="hideAllModals()"
                            class="px-3 py-2 text-xs font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 dark:bg-meta-4 dark:text-gray-300">Batal</button>
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium text-white transition rounded-lg bg-brand-500 hover:bg-brand-600">
                            <svg class="fill-current" width="14" height="14" viewBox="0 0 20 20" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M16.7071 5.29289C17.0976 5.68342 17.0976 6.31658 16.7071 6.70711L8.70711 14.7071C8.31658 15.0976 7.68342 15.0976 7.29289 14.7071L3.29289 10.7071C2.90237 10.3166 2.90237 9.68342 3.29289 9.29289C3.68342 8.90237 4.31658 8.90237 4.70711 9.29289L8 12.5858L15.2929 5.29289C15.6834 4.90237 16.3166 4.90237 16.7071 5.29289Z"
                                    fill="white" />
                            </svg>
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Kota -->
    <div id="modalEditKota" class="fixed inset-0 hidden items-center justify-center transition-all duration-300 z-9999">
        <div class="absolute inset-0 bg-black/20" onclick="hideAllModals()"></div>
        <div
            class="relative bg-white dark:bg-boxdark w-full max-w-md mx-4 rounded-xl shadow-xl flex flex-col overflow-hidden max-h-[85vh]">
            <div
                class="sticky top-0 z-10 bg-white dark:bg-boxdark border-b border-stroke dark:border-strokedark px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h5 class="text-lg font-bold text-black dark:text-white">Edit Kota</h5>
                        <p class="text-xs text-gray-500">Edit data kota.</p>
                    </div>
                    <button onclick="hideAllModals()" class="text-gray-400 hover:text-red-500 transition-colors">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path d="M18 6L6 18M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="p-6 overflow-y-auto flex-1">
                <form id="formEditKota" method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="edit_kota">
                    <input type="hidden" name="id_kota" id="edit_id_kota">
                    <div>
                        <label class="block text-sm font-medium mb-1 dark:text-white">Provinsi *</label>
                        <select name="id_provinsi" id="edit_id_provinsi_kota" required class="select-control">
                            <option value="">-- Pilih Provinsi --</option>
                            <?php foreach ($data_provinsi as $prov): ?>
                                <option value="<?php echo $prov['id_provinsi']; ?>">
                                    <?php echo $prov['nama_provinsi']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1 dark:text-white">Nama Kota *</label>
                        <input type="text" name="nama_kota" id="edit_nama_kota" required class="form-control">
                    </div>
                    <div class="flex justify-end gap-2 pt-4">
                        <button type="button" onclick="hideAllModals()"
                            class="px-3 py-2 text-xs font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 dark:bg-meta-4 dark:text-gray-300">
                            Batal
                        </button>
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium text-white transition rounded-lg bg-brand-500 hover:bg-brand-600">
                            <svg class="fill-current" width="14" height="14" viewBox="0 0 20 20" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M16.7071 5.29289C17.0976 5.68342 17.0976 6.31658 16.7071 6.70711L8.70711 14.7071C8.31658 15.0976 7.68342 15.0976 7.29289 14.7071L3.29289 10.7071C2.90237 10.3166 2.90237 9.68342 3.29289 9.29289C3.68342 8.90237 4.31658 8.90237 4.70711 9.29289L8 12.5858L15.2929 5.29289C15.6834 4.90237 16.3166 4.90237 16.7071 5.29289Z"
                                    fill="white" />
                            </svg>
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Lihat Kota berdasarkan Provinsi -->
    <div id="modalLihatKota"
        class="modal-container fixed inset-0 hidden items-center justify-center transition-all duration-300 z-9999">
        <div class="absolute inset-0 bg-black/20" onclick="hideAllModals()"></div>
        <div
            class="modal-content bg-white dark:bg-boxdark w-full max-w-md mx-4 rounded-xl shadow-xl relative flex flex-col overflow-hidden modal-content-small">
            <!-- Header -->
            <div class="modal-header px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h5 class="text-lg font-bold text-black dark:text-white" id="modalLihatKotaTitle">Data Kota</h5>
                        <p class="text-xs text-gray-500 dark:text-gray-400" id="modalLihatKotaDesc">Daftar kota
                            berdasarkan provinsi</p>
                    </div>
                    <button onclick="hideAllModals()" class="text-gray-400 hover:text-red-500 transition-colors">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path d="M18 6L6 18M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Content dengan tabel -->
            <div class="flex-1 p-0 overflow-hidden">
                <div class="modal-table-container h-full">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 font-medium text-black dark:text-white text-sm text-left">ID Kota
                                </th>
                                <th class="px-4 py-3 font-medium text-black dark:text-white text-sm text-left">Nama Kota
                                </th>
                                <th class="px-4 py-3 font-medium text-black dark:text-white text-sm text-left">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="kotaByProvinsiBody" class="divide-y divide-gray-100 dark:divide-gray-700">
                            <!-- Data akan diisi via JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer dengan tombol tutup -->
            <div class="border-t border-stroke dark:border-strokedark px-6 py-4">
                <div class="flex justify-center">
                    <button onclick="hideAllModals()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition-colors">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus Provinsi -->
    <div id="modalHapusProvinsi"
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
                    <p class="text-sm text-gray-600 dark:text-gray-400" id="hapusMessageProvinsi">
                        Apakah Anda yakin ingin menghapus data ini?
                    </p>
                </div>
                <div class="flex justify-center gap-2">
                    <button type="button" onclick="hideAllModals()"
                        class="px-4 py-2 text-xs font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Batal
                    </button>
                    <a href="#" id="confirmDeleteProvinsiLink" style="background-color: #dc2626;"
                        class="inline-flex items-center gap-2 px-4 py-3 text-sm font-medium text-white transition rounded-lg hover:bg-red-700 shadow-theme-xs">
                        Ya, Hapus
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus Kota -->
    <div id="modalHapusKota"
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
                    <p class="text-sm text-gray-600 dark:text-gray-400" id="hapusMessageKota">
                        Apakah Anda yakin ingin menghapus data ini?
                    </p>
                </div>
                <div class="flex justify-center gap-2">
                    <button type="button" onclick="hideAllModals()"
                        class="px-4 py-2 text-xs font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Batal
                    </button>
                    <a href="#" id="confirmDeleteKotaLink" style="background-color: #dc2626;"
                        class="inline-flex items-center gap-2 px-4 py-3 text-sm font-medium text-white transition rounded-lg hover:bg-red-700 shadow-theme-xs">
                        Ya, Hapus
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== Page Wrapper End ===== -->
    <script defer src="bundle.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.tailwindcss.js"></script>

    <script>
        $(document).ready(function () {
            // Inisialisasi DataTable dengan konfigurasi SAMA seperti dataadmin
            $('#provinsiTable').DataTable({
                "dom": '<"flex flex-wrap items-center justify-between gap-3 mb-3"lf>rt<"flex flex-wrap items-center justify-between gap-3 mt-3"ip>',
                "language": {
                    "search": "",
                    "searchPlaceholder": "Cari provinsi...",
                    "lengthMenu": "Tampilkan _MENU_ data",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    "infoEmpty": "Tidak ada data",
                    "infoFiltered": "(disaring dari _MAX_ total data)",
                    "paginate": {
                        "previous": "‹",
                        "next": "›",
                        "first": "«",
                        "last": "»"
                    }
                },
                "pageLength": 5, // SAMA dengan dataadmin (5 entri)
                "lengthMenu": [[5, 10, 20, 50, -1], [5, 10, 20, 50, "Semua"]],
                "responsive": true,
                "order": [[0, "desc"]],
                "initComplete": function () {
                    // Terapkan styling setelah DataTables diinisialisasi
                    setTimeout(function () {
                        // Styling untuk search box dan dropdown
                        $('#provinsiTable_filter input').addClass('rounded-lg border border-stroke bg-transparent py-1 px-2 outline-none focus:border-primary dark:border-strokedark dark:bg-meta-4 text-xs h-7');
                        $('#provinsiTable_length select').addClass('rounded-lg border border-stroke bg-transparent py-1 px-2 outline-none dark:border-strokedark dark:bg-meta-4 text-xs h-7');

                        // Styling untuk pagination
                        $('#provinsiTable_paginate .paginate_button').addClass('rounded-md border border-stroke bg-transparent px-1.5 py-0.5 text-[0.6875rem] h-6 min-w-6 mx-0.5 dark:border-strokedark dark:bg-meta-4');
                        $('#provinsiTable_paginate .paginate_button.current').addClass('bg-blue-500 text-white border-blue-500');

                        // Hover effect untuk pagination
                        $('#provinsiTable_paginate .paginate_button:not(.current)').hover(
                            function () {
                                $(this).addClass('bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200');
                            },
                            function () {
                                $(this).removeClass('bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200');
                            }
                        );

                        // Styling untuk info text
                        $('#provinsiTable_info').addClass('text-xs mt-3');
                        $('#provinsiTable_length').addClass('text-xs');
                        $('#provinsiTable_paginate').addClass('text-xs mt-3');

                        // Layout untuk wrapper
                        $('#provinsiTable_wrapper .dt-length').addClass('flex items-center gap-2');
                        $('#provinsiTable_wrapper .dt-search').addClass('flex items-center gap-2');
                        $('#provinsiTable_wrapper .dt-paging').addClass('flex items-center gap-1');
                    }, 100);
                }
            });
        });

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
                hideAllModals();
            }
        }

        function hideAllModals() {
            const modals = [
                'modalTambahProvinsi', 'modalEditProvinsi', 'modalTambahKota',
                'modalEditKota', 'modalLihatKota', 'modalHapusProvinsi', 'modalHapusKota'
            ];
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

        function showEditProvinsi(id, nama) {
            document.getElementById('edit_id_provinsi').value = id;
            document.getElementById('edit_nama_provinsi').value = nama;
            toggleModal('modalEditProvinsi');
        }

        function showEditKota(id, nama, idProvinsi) {
            console.log('Editing Kota:', { id, nama, idProvinsi });

            document.getElementById('edit_id_kota').value = id;
            document.getElementById('edit_nama_kota').value = nama;

            // Gunakan ID yang baru
            const provinsiDropdown = document.getElementById('edit_id_provinsi_kota');
            if (provinsiDropdown) {
                console.log('Before setting:', provinsiDropdown.value);
                provinsiDropdown.value = idProvinsi;
                console.log('After setting:', provinsiDropdown.value);

                // Trigger change event untuk memastikan UI update
                const event = new Event('change', { bubbles: true });
                provinsiDropdown.dispatchEvent(event);
            }

            toggleModal('modalEditKota');
        }

        function showDeleteProvinsi(id, nama) {
            const message = document.getElementById('hapusMessageProvinsi');
            message.textContent = `Apakah Anda yakin ingin menghapus provinsi "${nama}"? Semua kota dalam provinsi ini juga akan dihapus.`;

            const deleteLink = document.getElementById('confirmDeleteProvinsiLink');
            deleteLink.onclick = function (e) {
                e.preventDefault();
                const originalText = deleteLink.innerHTML;
                deleteLink.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Menghapus...';
                deleteLink.style.pointerEvents = 'none';

                fetch(`datalokasi.php?action=hapus_provinsi&id=${id}`, {
                    method: 'GET'
                })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            showNotification('success', 'Data provinsi berhasil dihapus!', 'Hapus Berhasil');
                            toggleModal('modalHapusProvinsi');
                            // Auto refresh setelah 2 detik
                            setTimeout(() => {
                                location.reload();
                            }, 2000);
                        } else {
                            showNotification('error', result.message || 'Gagal menghapus data provinsi', 'Hapus Gagal');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('error', 'Terjadi kesalahan saat menghapus data', 'Error Server');
                    })
                    .finally(() => {
                        deleteLink.innerHTML = originalText;
                        deleteLink.style.pointerEvents = 'auto';
                    });
            };

            toggleModal('modalHapusProvinsi');
        }

        function showDeleteKota(id, nama) {
            const message = document.getElementById('hapusMessageKota');
            message.textContent = `Apakah Anda yakin ingin menghapus kota "${nama}"?`;

            const deleteLink = document.getElementById('confirmDeleteKotaLink');
            deleteLink.onclick = function (e) {
                e.preventDefault();
                const originalText = deleteLink.innerHTML;
                deleteLink.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Menghapus...';
                deleteLink.style.pointerEvents = 'none';

                fetch(`datalokasi.php?action=hapus_kota&id=${id}`, {
                    method: 'GET'
                })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            showNotification('success', 'Data kota berhasil dihapus!', 'Hapus Berhasil');
                            toggleModal('modalHapusKota');
                            // Auto refresh setelah 2 detik
                            setTimeout(() => {
                                location.reload();
                            }, 2000);
                        } else {
                            showNotification('error', result.message || 'Gagal menghapus data kota', 'Hapus Gagal');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('error', 'Terjadi kesalahan saat menghapus data', 'Error Server');
                    })
                    .finally(() => {
                        deleteLink.innerHTML = originalText;
                        deleteLink.style.pointerEvents = 'auto';
                    });
            };

            toggleModal('modalHapusKota');
        }

        function showLihatKota(idProvinsi, namaProvinsi) {
            // Set judul modal
            const modalTitle = document.getElementById('modalLihatKotaTitle');
            const modalDesc = document.getElementById('modalLihatKotaDesc');

            modalTitle.textContent = `Data Kota - ${namaProvinsi}`;
            modalDesc.textContent = `Daftar kota dalam provinsi ${namaProvinsi}`;

            // Reset konten tabel sementara
            const tableBody = document.getElementById('kotaByProvinsiBody');
            tableBody.innerHTML = `
        <tr>
            <td colspan="3" class="px-4 py-8 text-center">
                <div class="flex items-center justify-center">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    <span class="ml-2">Memuat data...</span>
                </div>
            </td>
        </tr>
    `;

            // Buka modal terlebih dahulu agar ukurannya terkalkulasi
            toggleModal('modalLihatKota');

            // Fetch data setelah modal terbuka
            setTimeout(() => {
                fetch(`get_kota_by_provinsi.php?id_provinsi=${idProvinsi}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.data.length > 0) {
                            let html = '';
                            data.data.forEach((kota, index) => {
                                const formatId = "C" + String(kota.id_kota).padStart(4, '0');
                                const isLastRow = index === data.data.length - 1;
                                html += `
                            <tr class="${isLastRow ? 'last-row' : ''}">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">
                                    ${formatId}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded bg-blue-100 dark:bg-blue-900 flex items-center justify-center text-blue-600 dark:text-blue-300 font-bold">
                                            ${kota.nama_kota.charAt(0).toUpperCase()}
                                        </div>
                                        <span>${kota.nama_kota}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <div class="flex gap-2">
                                        <button onclick="event.stopPropagation(); showEditKotaFromModal(${kota.id_kota}, '${kota.nama_kota.replace(/'/g, "\\'")}', ${kota.id_provinsi})" 
                                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-white transition rounded-lg bg-brand-500 hover:bg-brand-600">
                                            <svg class="fill-current h-3 w-3" width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M13.5858 3.58579C14.3668 2.80474 15.6332 2.80474 16.4142 3.58579C17.1953 4.36683 17.1953 5.63316 16.4142 6.41421L15.6213 7.20711L12.7929 4.37868L13.5858 3.58579Z" fill="white" />
                                                <path d="M11.3787 5.79289L3 14.1716V17H5.82842L14.2071 8.62132L11.3787 5.79289Z" fill="white" />
                                            </svg>
                                            Edit
                                        </button>
                                        <button onclick="event.stopPropagation(); showDeleteKotaFromModal(${kota.id_kota}, '${kota.nama_kota.replace(/'/g, "\\'")}')" 
                                        style="background-color: #dc2626;"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-white transition rounded-lg bg-red-500 hover:bg-red-600">
                                            <svg class="fill-current h-3 w-3" width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" fill="white"/>
                                            </svg>
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                            });
                            tableBody.innerHTML = html;

                            // Setelah data dimuat, scroll ke atas
                            setTimeout(() => {
                                const container = document.querySelector('.modal-table-container');
                                if (container) {
                                    container.scrollTop = 0;
                                }
                            }, 50);

                        } else {
                            tableBody.innerHTML = `
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p>Tidak ada data kota untuk provinsi ini</p>
                                </div>
                            </td>
                        </tr>
                    `;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        tableBody.innerHTML = `
                    <tr>
                        <td colspan="3" class="px-4 py-8 text-center text-red-500 dark:text-red-400">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 text-red-300 dark:text-red-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.994-.833-2.764 0L4.406 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                                <p>Terjadi kesalahan saat memuat data</p>
                            </div>
                        </td>
                    </tr>
                `;
                    });
            }, 100);
        }

        function showEditKotaFromModal(id, nama, idProvinsi) {
            console.log('Editing Kota from Modal:', { id, nama, idProvinsi });

            hideAllModals(); // Tutup modal lihat kota dulu

            // Gunakan setTimeout untuk memastikan modal sebelumnya benar-benar tertutup
            setTimeout(() => {
                document.getElementById('edit_id_kota').value = id;
                document.getElementById('edit_nama_kota').value = nama;

                // Gunakan ID yang baru
                const provinsiDropdown = document.getElementById('edit_id_provinsi_kota');
                if (provinsiDropdown) {
                    console.log('Modal - Before setting:', provinsiDropdown.value);
                    provinsiDropdown.value = idProvinsi;
                    console.log('Modal - After setting:', provinsiDropdown.value);

                    // Trigger change event untuk memastikan UI update
                    const event = new Event('change', { bubbles: true });
                    provinsiDropdown.dispatchEvent(event);
                }
                toggleModal('modalEditKota');
            }, 100);
        }

        function showDeleteKotaFromModal(id, nama) {
            hideAllModals();
            setTimeout(() => {
                showDeleteKota(id, nama);
            }, 100);
        }

        // ========== AJAX FORM HANDLERS ==========
        document.addEventListener('DOMContentLoaded', function () {
            // Handler untuk form tambah provinsi
            const formTambahProvinsi = document.getElementById('formTambahProvinsi');
            if (formTambahProvinsi) {
                formTambahProvinsi.addEventListener('submit', function (e) {
                    e.preventDefault();

                    const submitBtn = formTambahProvinsi.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;

                    // Tampilkan loading
                    submitBtn.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Menambahkan...';
                    submitBtn.disabled = true;

                    const formData = new FormData(formTambahProvinsi);

                    fetch('datalokasi.php', {
                        method: 'POST',
                        body: formData
                    })
                        .then(response => response.json())
                        .then(result => {
                            if (result.success) {
                                showNotification('success', 'Data provinsi berhasil ditambahkan!', 'Tambah Berhasil');
                                toggleModal('modalTambahProvinsi');
                                formTambahProvinsi.reset();
                                // Auto refresh setelah 2 detik
                                setTimeout(() => {
                                    location.reload();
                                }, 2000);
                            } else {
                                showNotification('error', result.message || 'Gagal menambahkan data provinsi', 'Tambah Gagal');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showNotification('error', 'Terjadi kesalahan saat menambahkan data', 'Error Server');
                        })
                        .finally(() => {
                            submitBtn.innerHTML = originalText;
                            submitBtn.disabled = false;
                        });
                });
            }

            // Handler untuk form edit provinsi
            const formEditProvinsi = document.getElementById('formEditProvinsi');
            if (formEditProvinsi) {
                formEditProvinsi.addEventListener('submit', function (e) {
                    e.preventDefault();

                    const submitBtn = formEditProvinsi.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;

                    // Tampilkan loading
                    submitBtn.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memperbarui...';
                    submitBtn.disabled = true;

                    const formData = new FormData(formEditProvinsi);

                    fetch('datalokasi.php', {
                        method: 'POST',
                        body: formData
                    })
                        .then(response => response.json())
                        .then(result => {
                            if (result.success) {
                                showNotification('success', 'Data provinsi berhasil diperbarui!', 'Edit Berhasil');
                                toggleModal('modalEditProvinsi');
                                // Auto refresh setelah 2 detik
                                setTimeout(() => {
                                    location.reload();
                                }, 2000);
                            } else {
                                showNotification('error', result.message || 'Gagal mengupdate data provinsi', 'Edit Gagal');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showNotification('error', 'Terjadi kesalahan saat mengupdate data', 'Error Server');
                        })
                        .finally(() => {
                            submitBtn.innerHTML = originalText;
                            submitBtn.disabled = false;
                        });
                });
            }

            // Handler untuk form tambah kota
            const formTambahKota = document.getElementById('formTambahKota');
            if (formTambahKota) {
                formTambahKota.addEventListener('submit', function (e) {
                    e.preventDefault();

                    const submitBtn = formTambahKota.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;

                    // Tampilkan loading
                    submitBtn.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Menambahkan...';
                    submitBtn.disabled = true;

                    const formData = new FormData(formTambahKota);

                    fetch('datalokasi.php', {
                        method: 'POST',
                        body: formData
                    })
                        .then(response => response.json())
                        .then(result => {
                            if (result.success) {
                                showNotification('success', 'Data kota berhasil ditambahkan!', 'Tambah Berhasil');
                                toggleModal('modalTambahKota');
                                formTambahKota.reset();
                                // Auto refresh setelah 2 detik
                                setTimeout(() => {
                                    location.reload();
                                }, 2000);
                            } else {
                                showNotification('error', result.message || 'Gagal menambahkan data kota', 'Tambah Gagal');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showNotification('error', 'Terjadi kesalahan saat menambahkan data', 'Error Server');
                        })
                        .finally(() => {
                            submitBtn.innerHTML = originalText;
                            submitBtn.disabled = false;
                        });
                });
            }

            // Handler untuk form edit kota
            const formEditKota = document.getElementById('formEditKota');
            if (formEditKota) {
                formEditKota.addEventListener('submit', function (e) {
                    e.preventDefault();

                    const submitBtn = formEditKota.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;

                    // Tampilkan loading
                    submitBtn.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memperbarui...';
                    submitBtn.disabled = true;

                    const formData = new FormData(formEditKota);

                    fetch('datalokasi.php', {
                        method: 'POST',
                        body: formData
                    })
                        .then(response => response.json())
                        .then(result => {
                            if (result.success) {
                                showNotification('success', 'Data kota berhasil diperbarui!', 'Edit Berhasil');
                                toggleModal('modalEditKota');
                                // Auto refresh setelah 2 detik
                                setTimeout(() => {
                                    location.reload();
                                }, 2000);
                            } else {
                                showNotification('error', result.message || 'Gagal mengupdate data kota', 'Edit Gagal');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showNotification('error', 'Terjadi kesalahan saat mengupdate data', 'Error Server');
                        })
                        .finally(() => {
                            submitBtn.innerHTML = originalText;
                            submitBtn.disabled = false;
                        });
                });
            }
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
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M20.3499 12.0004C20.3499 16.612 16.6115 20.3504 11.9999 20.3504C7.38832 20.3504 3.6499 16.612 3.6499 12.0004C3.6499 7.38881 7.38833 3.65039 11.9999 3.65039C16.6115 3.65039 20.3499 7.38881 20.3499 12.0004ZM11.9999 22.1504C17.6056 22.1504 22.1499 17.6061 22.1499 12.0004C22.1499 6.3947 17.6056 1.85039 11.9999 1.85039C6.39421 1.85039 1.8499 6.3947 1.8499 12.0004C1.8499 17.6061 6.39421 22.1504 11.9999 22.1504ZM13.0008 16.4753C13.0008 15.923 12.5531 15.4753 12.0008 15.4753L11.9998 15.4753C11.4475 15.4753 10.9998 15.923 10.9998 16.4753C10.9998 17.0276 11.4475 17.4753 11.9998 17.4753L12.0008 17.4753C12.5531 17.4753 13.0008 17.0276 13.0008 16.4753ZM11.9998 6.62898C12.414 6.62898 12.7498 6.96476 12.7498 7.37898L12.7498 13.0555C12.7498 13.4697 12.414 13.8055 11.9998 13.8055C11.5856 13.8055 11.2498 13.4697 11.2498 7.37898C11.2498 6.96476 11.5856 6.62898 11.9998 6.62898Z" fill="#F04438" />
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
    </script>

    <!-- Alert Container -->
    <div id="alertContainer" class="alert-container"></div>
</body>

</html>