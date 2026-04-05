<?php

require_once 'session_check.php';
include 'koneksi.php';

$db = new database();

// Handle AJAX requests
header('Content-Type: application/json');
$ajax_response = ['success' => false, 'message' => ''];

// Tangani hapus via AJAX
if (isset($_GET['action']) && $_GET['action'] == 'hapus' && isset($_GET['id'])) {
    $id = $_GET['id'];

    if (is_numeric($id)) {
        if ($db->hapus_lamaran($id)) {
            $ajax_response['success'] = true;
            $ajax_response['message'] = 'Data lamaran berhasil dihapus';
        } else {
            $ajax_response['message'] = 'Gagal menghapus data lamaran';
        }
    } else {
        $ajax_response['message'] = 'ID tidak valid';
    }
    echo json_encode($ajax_response);
    exit;
}

// Tangani update status via AJAX
if (isset($_GET['action']) && $_GET['action'] == 'update_status' && isset($_GET['id']) && isset($_GET['status'])) {
    $id = $_GET['id'];
    $status = $_GET['status'];
    $catatan = $_GET['catatan'] ?? '';

    if (is_numeric($id)) {
        if ($db->update_status_lamaran($id, $status, $catatan)) {
            $ajax_response['success'] = true;
            $ajax_response['message'] = 'Status lamaran berhasil diperbarui';
        } else {
            $ajax_response['message'] = 'Gagal memperbarui status lamaran';
        }
    } else {
        $ajax_response['message'] = 'ID tidak valid';
    }
    echo json_encode($ajax_response);
    exit;
}

// Reset content type untuk normal HTML
header('Content-Type: text/html; charset=UTF-8');

$data_lamaran = $db->tampil_data_lamaran();

// Tangani hapus (legacy - non-AJAX)
if (isset($_GET['action']) && $_GET['action'] == 'hapus' && isset($_GET['id'])) {
    $id = $_GET['id'];

    if (is_numeric($id)) {
        if ($db->hapus_lamaran($id)) {
            $data_lamaran = $db->tampil_data_lamaran();
        }
    }
}

// Tangani update status (legacy - non-AJAX)
if (isset($_GET['action']) && $_GET['action'] == 'update_status' && isset($_GET['id']) && isset($_GET['status'])) {
    $id = $_GET['id'];
    $status = $_GET['status'];
    $catatan = $_GET['catatan'] ?? '';

    if (is_numeric($id)) {
        if ($db->update_status_lamaran($id, $status, $catatan)) {
            $data_lamaran = $db->tampil_data_lamaran();
        }
    }
}

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Data Lamaran | LinkUp</title>
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

        /* Style untuk tabel lamaran */
        #lamaranTable tbody tr {
            height: 60px !important;
        }

        #lamaranTable td {
            padding-top: 0.75rem !important;
            padding-bottom: 0.75rem !important;
            padding-left: 1rem !important;
            padding-right: 1rem !important;
        }

        #lamaranTable th {
            padding-top: 0.75rem !important;
            padding-bottom: 0.75rem !important;
        }

        /* Modal */
        #modalHapusLamaran,
        #modalUpdateStatus {
            display: none;
        }

        #modalHapusLamaran:not(.hidden),
        #modalUpdateStatus:not(.hidden) {
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

        /* Status badges */
        .status-badge {
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-align: center;
            display: inline-block;
            min-width: 70px;
        }

        .status-diproses {
            background-color: #fef3c7;
            color: #92400e;
            border: 1px solid #fbbf24;
        }

        .dark .status-diproses {
            background-color: #92400e;
            color: #fef3c7;
            border-color: #d97706;
        }

        .status-diterima {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #10b981;
        }

        .dark .status-diterima {
            background-color: #065f46;
            color: #d1fae5;
            border-color: #059669;
        }

        .status-ditolak {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #ef4444;
        }

        .dark .status-ditolak {
            background-color: #991b1b;
            color: #fee2e2;
            border-color: #dc2626;
        }

        /* Catatan HRD */
        .catatan-box {
            background: #f0f9ff;
            border: 1px solid #e0f2fe;
            border-radius: 6px;
            padding: 8px 10px;
            font-size: 12px;
            color: #0369a1;
            margin-top: 5px;
        }

        .dark .catatan-box {
            background: #0c4a6e;
            border-color: #0369a1;
            color: #bae6fd;
        }

        /* Form styling */
        .form-group {
            margin-bottom: 12px;
        }

        .form-label {
            display: block;
            margin-bottom: 4px;
            font-size: 12px;
            font-weight: 600;
            color: #374151;
        }

        .dark .form-label {
            color: #d1d5db;
        }

        .form-control {
            width: 100%;
            padding: 6px 10px;
            font-size: 13px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            background-color: white;
        }

        .dark .form-control {
            background-color: #374151;
            border-color: #4b5563;
            color: #f3f4f6;
        }

        .form-textarea {
            min-height: 60px;
            resize: vertical;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .status-badge {
                min-width: 60px;
                font-size: 10px;
                padding: 2px 8px;
            }
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

        /* Avatar kecil */
        .avatar-small {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
        }

        .avatar-placeholder-small {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: #4f46e5;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 12px;
        }

        /* Tombol aksi */
        .action-btn {
            padding: 0.35rem 0.75rem;
            font-size: 0.75rem;
            border-radius: 5px;
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

        /* ==================== PERBAIKAN DATATABLES v2 UNTUK LAMARAN ==================== */
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
        #lamaranTable_wrapper .flex {
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

        /* ==================== PERBAIKAN TAMBAHAN UNTUK RESPONSIVENESS ==================== */
        @media (max-width: 768px) {

            #lamaranTable_wrapper .dt-length,
            #lamaranTable_wrapper .dt-search {
                margin-bottom: 0.5rem !important;
            }

            #lamaranTable_wrapper .dt-info,
            #lamaranTable_wrapper .dt-paging {
                margin-top: 0.5rem !important;
            }
        }

        /* ==================== RESPONSIVE TABEL LAMARAN ==================== */
        /* Tampilan Desktop - Normal */
        #lamaranTable {
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

        /* ========== PERBAIKAN TOMBOL EXPORT LAMARAN ========== */
        button[onclick*="modalExportLamaran"] {
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

        button[onclick*="modalExportLamaran"]:hover {
            background-color: #15803d !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
        }

        button[onclick*="modalExportLamaran"] svg {
            width: 1rem !important;
            height: 1rem !important;
        }

        /* ========== PERBAIKAN TOMBOL HAPUS LAMARAN ========== */
        button[onclick*="showDeleteConfirmation"] {
            background-color: #dc2626 !important;
            color: white !important;
            font-weight: 500 !important;
            border: none !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 0.375rem !important;
            padding: 0.375rem 0.625rem !important;
            font-size: 0.75rem !important;
            border-radius: 0.375rem !important;
            transition: all 0.3s ease !important;
        }

        button[onclick*="showDeleteConfirmation"]:hover {
            background-color: #b91c1c !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 6px -1px rgba(220, 38, 38, 0.1) !important;
        }

        button[onclick*="showDeleteConfirmation"] svg {
            width: 0.875rem !important;
            height: 0.875rem !important;
        }

        /* Tampilan Tablet (768px - 1024px) */
        @media (max-width: 1024px) {
            #lamaranTable {
                min-width: 900px;
                font-size: 0.875rem;
            }

            #lamaranTable th,
            #lamaranTable td {
                padding: 0.5rem 0.75rem !important;
            }
        }

        /* Tampilan Mobile (<= 768px) */
        @media (max-width: 768px) {
            #lamaranTable {
                min-width: 800px;
                font-size: 0.75rem;
            }

            #lamaranTable th,
            #lamaranTable td {
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

            /* Adjust avatar and logo sizes */
            .avatar-small,
            .avatar-placeholder-small {
                width: 32px !important;
                height: 32px !important;
            }

            .perusahaan-logo-container {
                width: 32px !important;
                height: 32px !important;
            }
        }

        /* Tampilan Very Small Mobile (<= 480px) */
        @media (max-width: 480px) {
            #lamaranTable {
                min-width: 700px;
            }

            .flex.gap-2 {
                flex-direction: column !important;
                gap: 0.25rem !important;
            }

            .avatar-small,
            .avatar-placeholder-small {
                width: 28px !important;
                height: 28px !important;
            }

            .perusahaan-logo-container {
                width: 28px !important;
                height: 28px !important;
            }
        }
    </style>
</head>

<body
    x-data="{ page: 'dataLamaran', 'loaded': true, 'darkMode': false, 'stickyMenu': false, 'sidebarToggle': false, 'scrollTop': false, 'modalOpen': false }"
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
                            Data Lamaran
                        </h2>
                        <nav>
                            <ol class="flex items-center gap-2">
                                <li><a class="font-medium" href="index.php">Home ></a></li>
                                <li class="font-medium text-primary">Data Lamaran</li>
                            </ol>
                        </nav>
                    </div>

                    <div
                        class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                        <div
                            class="border-b border-stroke px-4 py-4 dark:border-strokedark sm:px-6 xl:px-7.5 flex justify-between items-center">
                            <h3 class="font-medium text-black dark:text-white">Tabel Data Lamaran</h3>
                            <div class="flex items-center gap-2">
                                <button onclick="toggleModal('modalExportLamaran')"
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
                                <table id="lamaranTable" class="w-full table-auto border-collapse text-left">
                                    <thead>
                                        <tr class="bg-gray-2 dark:bg-meta-4">
                                            <th class="px-4 py-3 font-medium text-black dark:text-white w-24">ID Lamaran
                                            </th>
                                            <th class="px-4 py-3 font-medium text-black dark:text-white w-48">Pelamar
                                            </th>
                                            <th class="px-4 py-3 font-medium text-black dark:text-white w-56">Perusahaan
                                                &
                                                Lowongan</th>
                                            <th class="px-4 py-3 font-medium text-black dark:text-white w-32">Tanggal
                                                Lamar</th>
                                            <th class="px-4 py-3 font-medium text-black dark:text-white w-32">Status
                                            </th>
                                            <th class="px-4 py-3 font-medium text-black dark:text-white w-40">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (!empty($data_lamaran)) {
                                            foreach ($data_lamaran as $row) {
                                                $format_id = "A" . sprintf("%04d", $row['id_lamaran']);
                                                $tanggal = date('d M Y', strtotime($row['tanggal_lamar']));

                                                // Get status class
                                                $status_class = '';
                                                switch ($row['status_lamaran']) {
                                                    case 'Diproses':
                                                        $status_class = 'status-diproses';
                                                        break;
                                                    case 'Diterima':
                                                        $status_class = 'status-diterima';
                                                        break;
                                                    case 'Ditolak':
                                                        $status_class = 'status-ditolak';
                                                        break;
                                                }
                                                ?>
                                                <tr class="border-b border-[#eee] dark:border-strokedark">
                                                    <td class="px-4 py-3 w-24">
                                                        <p class="text-black dark:text-white font-medium text-sm">
                                                            <?php echo $format_id; ?>
                                                        </p>
                                                    </td>

                                                    <td class="px-4 py-3 w-48">
                                                        <div class="flex items-center gap-2">
                                                            <div class="flex-shrink-0">
                                                                <?php if (!empty($row['foto_user'])): ?>
                                                                    <img src="src/images/user/<?php echo $row['foto_user']; ?>"
                                                                        alt="Foto" class="avatar-small">
                                                                <?php else: ?>
                                                                    <div class="avatar-placeholder-small">
                                                                        <?php echo !empty($row['nama_user']) ? substr($row['nama_user'], 0, 1) : 'P'; ?>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                            <div>
                                                                <h5 class="font-medium text-black dark:text-white text-sm">
                                                                    <?php echo htmlspecialchars($row['nama_user'] ?? 'Pelamar'); ?>
                                                                </h5>
                                                                <p class="text-xs text-gray-500">
                                                                    <?php echo $row['email_user'] ?? '-'; ?>
                                                                </p>
                                                                <?php if (!empty($row['catatan_hrd'])): ?>
                                                                    <div class="catatan-box mt-1"
                                                                        title="<?php echo htmlspecialchars($row['catatan_hrd']); ?>">
                                                                        <span class="font-medium">Catatan:</span>
                                                                        <?php echo substr(htmlspecialchars($row['catatan_hrd']), 0, 30) . (strlen($row['catatan_hrd']) > 30 ? '...' : ''); ?>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <td class="px-4 py-3 w-56">
                                                        <div class="flex items-center gap-2 mb-2">
                                                            <div class="perusahaan-logo-container">
                                                                <?php if (!empty($row['logo_perusahaan'])): ?>
                                                                    <img src="src/images/company/<?php echo $row['logo_perusahaan']; ?>"
                                                                        alt="Logo" class="perusahaan-logo" />
                                                                <?php else: ?>
                                                                    <span class="perusahaan-placeholder">
                                                                        <?php echo substr($row['nama_perusahaan'] ?? 'CO', 0, 2); ?>
                                                                    </span>
                                                                <?php endif; ?>
                                                            </div>
                                                            <div>
                                                                <p class="text-sm text-black dark:text-white font-medium">
                                                                    <?php echo htmlspecialchars($row['nama_perusahaan'] ?? '-'); ?>
                                                                </p>
                                                                <p class="text-xs text-gray-500">
                                                                    <?php echo htmlspecialchars($row['judul_lowongan'] ?? '-'); ?>
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <td class="px-4 py-3 w-32">
                                                        <p class="text-sm text-black dark:text-white">
                                                            <?php echo $tanggal; ?>
                                                        </p>
                                                    </td>

                                                    <td class="px-4 py-3 w-32">
                                                        <span class="status-badge <?php echo $status_class; ?>">
                                                            <?php echo $row['status_lamaran']; ?>
                                                        </span>
                                                    </td>

                                                    <td class="px-4 py-3">
                                                        <div class="flex gap-2">
                                                            <button onclick="showUpdateStatus(
                                                                <?php echo $row['id_lamaran']; ?>,
                                                                '<?php echo addslashes($row['nama_user'] ?? 'Pelamar'); ?>',
                                                                '<?php echo addslashes($row['judul_lowongan'] ?? '-'); ?>',
                                                                '<?php echo addslashes($row['status_lamaran']); ?>',
                                                                '<?php echo addslashes($row['catatan_hrd'] ?? ''); ?>'
                                                            )"
                                                                class="inline-flex items-center gap-1 px-2 py-1.5 text-xs font-medium text-white transition rounded bg-brand-500 hover:bg-brand-600 action-btn">
                                                                <svg class="fill-current h-3 w-3" width="20" height="20"
                                                                    viewBox="0 0 20 20" fill="none"
                                                                    xmlns="http://www.w3.org/2000/svg">
                                                                    <path
                                                                        d="M13.5858 3.58579C14.3668 2.80474 15.6332 2.80474 16.4142 3.58579C17.1953 4.36683 17.1953 5.63316 16.4142 6.41421L15.6213 7.20711L12.7929 4.37868L13.5858 3.58579Z"
                                                                        fill="white" />
                                                                    <path
                                                                        d="M11.3787 5.79289L3 14.1716V17H5.82842L14.2071 8.62132L11.3787 5.79289Z"
                                                                        fill="white" />
                                                                </svg>
                                                                Edit
                                                            </button>

                                                            <button
                                                                onclick="showDeleteConfirmation(<?php echo $row['id_lamaran']; ?>, '<?php echo addslashes($row['nama_user'] ?? $row['email_user']); ?>')"
                                                                style="background-color: #dc2626;"
                                                                class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium text-white transition rounded-lg hover:opacity-80">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5"
                                                                    viewBox="0 0 20 20" fill="currentColor">
                                                                    <path fill-rule="evenodd"
                                                                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0111 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
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
                                            echo "<tr><td colspan='6' class='text-center py-4'>Tidak ada data lamaran</td></tr>";
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
        </div>
        </main>
        <!-- ===== Main Content End ===== -->
    </div>
    <!-- ===== Content Area End ===== -->
    </div>

    <!-- Modal Update Status -->
    <div id="modalUpdateStatus"
        class="modal-container fixed inset-0 hidden items-center justify-center transition-all duration-300 z-9999">
        <div class="absolute inset-0 bg-black/20" onclick="hideAllModals()"></div>
        <div
            class="modal-content bg-white dark:bg-boxdark w-full max-w-md mx-4 rounded-xl shadow-xl relative flex flex-col overflow-hidden">
            <div
                class="sticky top-0 z-10 bg-white dark:bg-boxdark border-b border-stroke dark:border-strokedark px-4 py-3">
                <div class="flex items-center justify-between">
                    <div>
                        <h5 class="text-base font-bold text-black dark:text-white">Update Status Lamaran</h5>
                        <p class="text-xs text-gray-500">Ubah status lamaran pelamar</p>
                    </div>
                    <button onclick="hideAllModals()" class="text-gray-400 hover:text-red-500 transition-colors p-1">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path d="M18 6L6 18M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="p-4">
                <form id="formUpdateStatus" class="space-y-3">
                    <input type="hidden" id="updateLamaranId" name="id">

                    <div class="grid grid-cols-2 gap-3">
                        <div class="form-group">
                            <label class="form-label">Pelamar</label>
                            <input type="text" id="updatePelamarNama" class="form-control" readonly>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Lowongan</label>
                            <input type="text" id="updateLowonganJudul" class="form-control" readonly>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="form-group">
                            <label class="form-label">Status Saat Ini</label>
                            <input type="text" id="updateStatusSaatIni" class="form-control" readonly>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Status Baru *</label>
                            <select name="status" id="updateStatusBaru" required class="form-control">
                                <option value="">Pilih Status</option>
                                <option value="Diproses">Diproses</option>
                                <option value="Diterima">Diterima</option>
                                <option value="Ditolak">Ditolak</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Catatan HRD (Opsional)</label>
                        <textarea name="catatan" id="updateCatatan" rows="3" class="form-control form-textarea"
                            placeholder="Tambahkan catatan untuk pelamar..."></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-3">
                        <button type="button" onclick="hideAllModals()"
                            class="px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 rounded hover:bg-gray-200 dark:bg-meta-4 dark:text-gray-300">Batal</button>
                        <button type="button" onclick="updateStatusLamaran()"
                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-white transition rounded bg-brand-500 hover:bg-brand-600">
                            Update Status
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div id="modalHapusLamaran"
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
                    <p class="text-sm text-gray-600 dark:text-gray-400" id="hapusMessageLamaran">
                        Apakah Anda yakin ingin menghapus data ini?
                    </p>
                </div>
                <div class="flex justify-center gap-2">
                    <button type="button" onclick="hideAllModals()"
                        class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 rounded hover:bg-gray-200 transition-colors">
                        Batal
                    </button>
                    <button type="button" id="confirmDeleteLink" style="background-color: #dc2626;"
                        class="inline-flex items-center gap-2 px-4 py-3 text-sm font-medium text-white transition rounded-lg hover:bg-red-700 shadow-theme-xs">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Export Data Lamaran -->
    <div id="modalExportLamaran"
        class="modal-container fixed inset-0 hidden items-center justify-center transition-all duration-300 z-9999">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="toggleModal('modalExportLamaran')"></div>
        <div
            class="modal-content bg-white dark:bg-boxdark w-full max-w-md mx-4 rounded-2xl shadow-2xl relative flex flex-col overflow-hidden max-h-[90vh]">
            <!-- Modal Header -->
            <div
                class="sticky top-0 z-10 bg-gradient-to-r from-blue-600 to-blue-700 dark:from-blue-800 dark:to-blue-900 px-6 py-5">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-lg font-bold text-white">Export Data Lamaran</h4>
                        <p class="text-xs text-blue-100 mt-1">Pilih filter dan format export data</p>
                    </div>
                    <button onclick="toggleModal('modalExportLamaran')"
                        class="text-white/80 hover:text-white transition-colors p-2 hover:bg-white/10 rounded-lg">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path d="M18 6L6 18M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="p-6 overflow-y-auto">
                <form id="formExportLamaran" action="export_lamaran.php" method="POST" class="space-y-6">
                    <!-- Filter Section -->
                    <div
                        class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-5 border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-2 mb-4">
                            <div
                                class="w-5 h-5 bg-blue-100 dark:bg-blue-900/30 rounded flex items-center justify-center">
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
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">ID
                                    Min</label>
                                <input type="number" name="id_min"
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                    placeholder="ID minimum" min="0" oninput="this.value = Math.abs(this.value)">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">ID
                                    Max</label>
                                <input type="number" name="id_max"
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                    placeholder="ID maksimum" min="0" oninput="this.value = Math.abs(this.value)">
                            </div>

                            <!-- Filter by Nama Pelamar -->
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Nama
                                    Pelamar</label>
                                <input type="text" name="filter_nama_pelamar"
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                    placeholder="Cari nama pelamar...">
                            </div>

                            <!-- Filter by Perusahaan -->
                            <div>
                                <label
                                    class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Perusahaan</label>
                                <input type="text" name="filter_perusahaan"
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                    placeholder="Cari nama perusahaan...">
                            </div>

                            <!-- Filter by Status -->
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Status
                                    Lamaran</label>
                                <select name="filter_status"
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                    <option value="">Semua Status</option>
                                    <option value="Diproses">Diproses</option>
                                    <option value="Diterima">Diterima</option>
                                    <option value="Ditolak">Ditolak</option>
                                </select>
                            </div>

                            <!-- Filter by Lowongan -->
                            <div>
                                <label
                                    class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Lowongan</label>
                                <input type="text" name="filter_lowongan"
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                    placeholder="Cari judul lowongan...">
                            </div>

                            <!-- Filter by Tanggal -->
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Tanggal
                                    Dari</label>
                                <input type="date" name="tanggal_dari"
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Tanggal
                                    Sampai</label>
                                <input type="date" name="tanggal_sampai"
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                            </div>
                        </div>
                    </div>

                    <!-- Column Selection Section -->
                    <div
                        class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-5 border border-blue-200 dark:border-blue-800">
                        <div class="flex items-center gap-2 mb-4">
                            <div
                                class="w-5 h-5 bg-blue-100 dark:bg-blue-900/30 rounded flex items-center justify-center">
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
                                <input type="checkbox" name="columns[]" value="id_lamaran" checked
                                    class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                                <div class="flex-1">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">ID Lamaran</span>
                                    <p class="text-xs text-gray-500">Nomor identifikasi lamaran</p>
                                </div>
                            </label>

                            <label
                                class="flex items-center gap-3 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <input type="checkbox" name="columns[]" value="nama_user" checked
                                    class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                                <div class="flex-1">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Nama
                                        Pelamar</span>
                                    <p class="text-xs text-gray-500">Nama lengkap pelamar</p>
                                </div>
                            </label>

                            <label
                                class="flex items-center gap-3 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <input type="checkbox" name="columns[]" value="email_user" checked
                                    class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                                <div class="flex-1">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Email
                                        Pelamar</span>
                                    <p class="text-xs text-gray-500">Email pelamar</p>
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
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Lowongan</span>
                                    <p class="text-xs text-gray-500">Judul lowongan</p>
                                </div>
                            </label>

                            <label
                                class="flex items-center gap-3 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <input type="checkbox" name="columns[]" value="tanggal_lamar" checked
                                    class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                                <div class="flex-1">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal
                                        Lamar</span>
                                    <p class="text-xs text-gray-500">Tanggal pengajuan lamaran</p>
                                </div>
                            </label>

                            <label
                                class="flex items-center gap-3 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <input type="checkbox" name="columns[]" value="status_lamaran" checked
                                    class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                                <div class="flex-1">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Status
                                        Lamaran</span>
                                    <p class="text-xs text-gray-500">Status lamaran</p>
                                </div>
                            </label>

                            <label
                                class="flex items-center gap-3 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <input type="checkbox" name="columns[]" value="catatan_hrd"
                                    class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                                <div class="flex-1">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Catatan
                                        HRD</span>
                                    <p class="text-xs text-gray-500">Catatan dari HRD</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Export Format Section -->
                    <div
                        class="bg-green-50 dark:bg-green-900/20 rounded-xl p-5 border border-green-200 dark:border-green-800">
                        <div class="flex items-center gap-2 mb-4">
                            <div
                                class="w-5 h-5 bg-green-100 dark:bg-green-900/30 rounded flex items-center justify-center">
                                <svg class="w-3 h-3 text-green-600 dark:text-green-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
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
                                        d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0112.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                PDF
                            </button>
                        </div>

                        <!-- Print Section -->
                        <div class="mt-4">
                            <div class="flex gap-3 justify-center py-3">
                                <button type="button" onclick="printLamaranData()"
                                    class="flex items-center gap-2 px-4 py-2 rounded-lg font-medium text-sm transition-colors shadow-sm"
                                    style="background-color: #7c3aed; color: white; hover:background-color: #6d28d9;">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm-8 0h2v2H7v-2z" />
                                    </svg>
                                    Print Data
                                </button>

                                <button type="button" onclick="printLamaranPreview()"
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

    <!-- ===== Page Wrapper End ===== -->
    <script defer src="bundle.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.tailwindcss.js"></script>

    <script>
        $(document).ready(function () {
            $('#lamaranTable').DataTable({
                "dom": '<"flex flex-wrap items-center justify-between gap-3 mb-2"lf>rt<"flex flex-wrap items-center justify-between gap-3 mt-2"ip>',
                "language": {
                    "search": "",
                    "searchPlaceholder": "Cari lamaran...",
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
                    var $wrapper = $('#lamaranTable_wrapper');

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
            const modals = ['modalUpdateStatus', 'modalHapusLamaran', 'modalExportLamaran'];
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

        // Fungsi untuk menampilkan modal update status (TANPA AJAX)
        function showUpdateStatus(id, nama_pelamar, judul_lowongan, status_saat_ini, catatan) {
            // Isi form dengan data yang sudah ada di halaman
            document.getElementById('updateLamaranId').value = id;
            document.getElementById('updatePelamarNama').value = nama_pelamar || 'Pelamar';
            document.getElementById('updateLowonganJudul').value = judul_lowongan || '-';
            document.getElementById('updateStatusSaatIni').value = status_saat_ini || 'Diproses';
            document.getElementById('updateStatusBaru').value = status_saat_ini || 'Diproses';
            document.getElementById('updateCatatan').value = catatan || '';

            toggleModal('modalUpdateStatus');
        }

        function updateStatusLamaran() {
            const id = document.getElementById('updateLamaranId').value;
            const status = document.getElementById('updateStatusBaru').value;
            const catatan = document.getElementById('updateCatatan').value;

            if (!status) {
                alert('Silakan pilih status baru!');
                return;
            }

            const updateBtn = document.querySelector('#modalUpdateStatus button[onclick]');
            const originalText = updateBtn.innerHTML;
            
            // Tampilkan loading
            updateBtn.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memperbarui...';
            updateBtn.disabled = true;
            
            // Encode catatan untuk URL
            const catatanEncoded = encodeURIComponent(catatan);
            
            // AJAX request untuk update
            fetch(`datalamaran.php?action=update_status&id=${id}&status=${status}&catatan=${catatanEncoded}`, {
                method: 'GET'
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    showNotification('success', 'Status lamaran berhasil diperbarui!', 'Update Berhasil');
                    toggleModal('modalUpdateStatus', false);
                    // Auto refresh setelah 2 detik
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    showNotification('error', result.message || 'Gagal memperbarui status lamaran', 'Update Gagal');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('error', 'Terjadi kesalahan saat memperbarui data', 'Error Server');
            })
            .finally(() => {
                updateBtn.innerHTML = originalText;
                updateBtn.disabled = false;
            });
        }

        function showDeleteConfirmation(id, judul) {
            const message = document.getElementById('hapusMessageLamaran');
            message.textContent = `Apakah Anda yakin ingin menghapus lamaran untuk "${judul}"?`;

            const deleteLink = document.getElementById('confirmDeleteLink');
            deleteLink.onclick = async function () {
                const originalText = deleteLink.innerHTML;
                deleteLink.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Menghapus...';
                deleteLink.disabled = true;
                
                try {
                    const response = await fetch(`datalamaran.php?action=hapus&id=${id}`, {
                        method: 'GET'
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        showNotification('success', 'Data lamaran berhasil dihapus!', 'Hapus Berhasil');
                        toggleModal('modalHapusLamaran', false);
                        // Auto refresh setelah 2 detik
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    } else {
                        showNotification('error', result.message || 'Gagal menghapus data lamaran', 'Hapus Gagal');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showNotification('error', 'Terjadi kesalahan saat menghapus data', 'Error Server');
                } finally {
                    deleteLink.innerHTML = originalText;
                    deleteLink.disabled = false;
                }
            };

            toggleModal('modalHapusLamaran');
        }

        // Validasi form export lamaran
        document.getElementById('formExportLamaran').addEventListener('submit', function (e) {
            const checkboxes = document.querySelectorAll('#formExportLamaran input[name="columns[]"]:checked');
            if (checkboxes.length === 0) {
                e.preventDefault();
                alert('Pilih minimal 1 kolom untuk export!');
                return false;
            }
        });

        // Fungsi untuk print data lamaran langsung
        function printLamaranData() {
            const form = document.getElementById('formExportLamaran');
            const formData = new FormData(form);

            // Validasi minimal 1 kolom dipilih
            const checkboxes = document.querySelectorAll('#formExportLamaran input[name="columns[]"]:checked');
            if (checkboxes.length === 0) {
                alert('Pilih minimal 1 kolom untuk print!');
                return;
            }

            // Buka window baru untuk print
            const printWindow = window.open('', '_blank');

            // Kirim data ke server untuk generate print view
            formData.append('format', 'print');

            fetch('export_lamaran.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.text())
                .then(html => {
                    printWindow.document.write(html);
                    printWindow.document.close();

                    // Tunggu sedikit lalu trigger print
                    setTimeout(() => {
                        printWindow.print();
                    }, 500);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal memuat data untuk print');
                });
        }

        // Fungsi untuk preview data lamaran
        function printLamaranPreview() {
            const form = document.getElementById('formExportLamaran');
            const formData = new FormData(form);

            // Validasi minimal 1 kolom dipilih
            const checkboxes = document.querySelectorAll('#formExportLamaran input[name="columns[]"]:checked');
            if (checkboxes.length === 0) {
                alert('Pilih minimal 1 kolom untuk preview!');
                return;
            }

            // Buka window baru untuk preview
            const previewWindow = window.open('', '_blank', 'width=800,height=600');

            // Kirim data ke server untuk generate preview
            formData.append('format', 'preview');

            fetch('export_lamaran.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.text())
                .then(html => {
                    previewWindow.document.write(html);
                    previewWindow.document.close();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal memuat data untuk preview');
                });
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
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M20.3499 12.0004C20.3499 16.612 16.6115 20.3504 11.9999 20.3504C7.38832 20.3504 3.6499 16.612 3.6499 12.0004C3.6499 7.38881 7.38833 3.65039 11.9999 3.65039C16.6115 3.65039 20.3499 7.38881 20.3499 12.0004ZM11.9999 22.1504C17.6056 22.1504 22.1499 17.6061 22.1499 12.0004C22.1499 6.3947 17.6056 1.85039 11.9999 1.85039C6.39421 1.85039 1.8499 6.3947 1.8499 12.0004C1.8499 17.6061 6.39421 22.1504 11.9999 22.1504ZM13.0008 16.4753C13.0008 15.923 12.5531 15.4753 12.0008 15.4753L11.9998 15.4753C11.4475 15.4753 10.9998 15.923 10.9998 16.4753C10.9998 17.0276 11.4475 17.4753 11.9998 17.4753L12.0008 17.4753C12.5531 17.4753 13.0008 17.0276 13.0008 16.4753ZM11.9998 6.62898C12.414 6.62898 12.7498 6.96476 12.7498 7.37898L12.7498 13.0555C12.7498 13.4697 12.414 13.8055 11.9998 13.8055C11.5856 13.8055 11.2498 13.4697 11.2498 7.37898C11.2498 6.96476 11.5856 6.62898 11.9998 6.62898Z" fill="#F04438" />
                      </svg>`;
                    iconColor = 'text-error-500';
                    break;
                case 'warning':
                    iconSvg = `<svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M3.6501 12.0004C3.6501 7.38803 7.38852 3.64961 12.0001 3.64961C16.6117 3.64961 20.3501 7.38803 20.3501 12.0001C20.3501 16.6117 16.6117 20.3496 12.0001 20.3496C7.38852 20.3496 3.6501 16.6112 3.6501 12.0001ZM12.0001 1.84961C6.39441 1.84961 1.8501 6.39392 1.8501 11.9996C1.8501 17.6053 6.39441 22.1496 12.0001 22.1496C17.6058 22.1496 22.1501 17.6053 22.1496 12.0001C22.1501 6.39392 17.6058 1.84961 12.0001 1.84961ZM10.9992 7.52468C10.9992 8.07697 11.4469 8.52468 11.9992 8.52468H12.0002C12.5525 8.52468 13.0002 8.07697 13.0002 7.52468C13.0002 6.9724 12.5525 6.52468 12.0002 6.52468H11.9992C11.4469 6.52468 10.9992 6.9724 10.9992 7.52468ZM12.0002 17.371C11.586 17.371 11.2502 17.0352 11.2502 16.621V10.9445C11.2502 10.5303 11.586 10.1945 12.0002 10.1945C12.4144 10.1945 12.7502 10.5303 12.7502 10.9445V16.621C12.7502 17.0352 12.4144 17.371 12.0002 17.371Z" fill="" />
                      </svg>`;
                    iconColor = 'text-warning-500 dark:text-orange-400';
                    break;
                case 'info':
                    iconSvg = `<svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M3.6501 11.9996C3.6501 7.38803 7.38852 3.64961 12.0001 3.64961C16.6117 3.64961 20.3501 7.38803 20.3501 11.9996C20.3501 16.6112 16.6117 20.3496 12.0001 20.3496C7.38852 20.3496 3.6501 16.6112 3.6501 11.9996ZM12.0001 1.84961C6.39441 1.84961 1.8501 6.39392 1.8501 11.9996C1.8501 17.6053 6.39441 22.1496 12.0001 22.1496C17.6058 22.1496 22.1501 17.6053 22.1496 12.0001C22.1501 6.39392 17.6058 1.84961 12.0001 1.84961ZM10.9992 7.52468C10.9992 8.07697 11.4469 8.52468 11.9992 8.52468H12.0002C12.5525 8.52468 13.0002 8.07697 13.0002 7.52468C13.0002 6.9724 12.5525 6.52468 12.0002 6.52468H11.9992C11.4469 6.52468 10.9992 6.9724 10.9992 7.52468ZM12.0002 17.371C11.586 17.371 11.2502 17.0352 11.2502 16.621V10.9445C11.2502 10.5303 11.586 10.1945 12.0002 10.1945C12.4144 10.1945 12.7502 10.5303 12.7502 10.9445V16.621C12.7502 17.0352 12.4144 17.371 12.0002 17.371Z" fill="" />
                      </svg>`;
                    iconColor = 'text-blue-light-500';
                    break;
                default:
                    iconSvg = `<svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M3.6501 11.9996C3.6501 7.38803 7.38852 3.64961 12.0001 3.64961C16.6117 3.64961 20.3501 7.38803 20.3501 11.9996C20.3501 16.6112 16.6117 20.3496 12.0001 20.3496C7.38852 20.3496 3.6501 16.6112 3.6501 11.9996ZM12.0001 1.84961C6.39441 1.84961 1.8501 6.39392 1.8501 11.9996C1.8501 17.6053 6.39441 22.1496 12.0001 22.1496C17.6058 22.1496 22.1501 17.6053 22.1496 12.0001C22.1501 6.39392 17.6058 1.84961 12.0001 1.84961ZM10.9992 7.52468C10.9992 8.07697 11.4469 8.52468 11.9992 8.52468H12.0002C12.5525 8.52468 13.0002 8.07697 13.0002 7.52468C13.0002 6.9724 12.5525 6.52468 12.0002 6.52468H11.9992C11.4469 6.52468 10.9992 6.9724 10.9992 7.52468ZM12.0002 17.371C11.586 17.371 11.2502 17.0352 11.2502 16.621V10.9445C11.2502 10.5303 11.586 10.1945 12.0002 10.1945C12.4144 10.1945 12.7502 10.5303 12.7502 10.9445V16.621C12.7502 17.0352 12.4144 17.371 12.0002 17.371Z" fill="" />
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
    </script>

    <!-- Alert Container -->
    <div id="alertContainer" class="alert-container"></div>
</body>

</html>