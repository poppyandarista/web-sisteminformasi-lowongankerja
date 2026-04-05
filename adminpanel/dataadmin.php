<?php
require_once 'session_check.php';
include 'koneksi.php';

$db = new database();
$data_admin = $db->tampil_data_admin();

// Ambil dan hapus alert dari session
$session_alert = null;
if (isset($_SESSION['alert'])) {
  $session_alert = $_SESSION['alert'];
  unset($_SESSION['alert']);
}
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport"
    content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>
    Data Admin | LinkUp
  </title>
  <link rel="icon" type="image/png" href="src/images/logo/favicon.png">
  <link href="style.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.tailwindcss.css">
  <style>
    /* Overlay untuk seluruh halaman saat modal aktif */
    .page-overlay {
      position: fixed;
      inset: 0;
      background-color: rgba(75, 85, 99, 0.5) !important;
      /* Abu-abu medium transparan */
      z-index: 9998 !important;
      /* Turunkan sedikit */
      pointer-events: auto;
    }

    /* Turunkan z-index header dan sidebar */
    #main-wrapper header,
    #main-wrapper aside {
      z-index: 50 !important;
    }

    /* Modal harus di atas overlay */
    .modal-container {
      z-index: 10000 !important;
      /* Naikkan */
    }

    /* Pastikan modal konten di atas overlay */
    .modal-content {
      z-index: 10001 !important;
      /* Lebih tinggi lagi */
    }

    /* Force overlay di atas semua */
    .force-overlay {
      z-index: 9999 !important;
    }

    /* PERKECIL JARAK ANTAR BARIS TABEL */
    #adminTable tbody tr {
      height: 60px !important;
      /* Perkecil tinggi baris */
    }

    #adminTable td {
      padding-top: 0.75rem !important;
      /* py-3 (kurangi dari py-5) */
      padding-bottom: 0.75rem !important;
      padding-left: 1rem !important;
      /* px-4 (tetap) */
      padding-right: 1rem !important;
    }

    #adminTable th {
      padding-top: 0.75rem !important;
      /* py-3 (kurangi dari py-4) */
      padding-bottom: 0.75rem !important;
    }

    /* Perkecil tinggi gambar user */
    #adminTable .h-10.w-10 {
      height: 2rem !important;
      /* 8px */
      width: 2rem !important;
    }

    /* Perkecil padding tombol aksi */
    #adminTable .px-4.py-3 {
      padding-left: 0.75rem !important;
      /* px-3 */
      padding-right: 0.75rem !important;
      padding-top: 0.5rem !important;
      /* py-2 */
      padding-bottom: 0.5rem !important;
    }

    /* Perkecil font size tombol */
    #adminTable button.text-sm {
      font-size: 0.75rem !important;
      /* text-xs */
    }

    /* ALERT SYSTEM STYLES - Sesuai Template */
    .alert-container {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 99999;
      pointer-events: none;
    }

    .alert-item {
      pointer-events: auto;
      min-width: 320px;
      max-width: 400px;
      margin-bottom: 10px;
      transform: translateX(100%);
      opacity: 0;
      transition: all 0.3s ease-in-out;
      border-radius: 0.75rem;
      padding: 1rem;
      display: flex;
      align-items: flex-start;
      gap: 0.75rem;
    }

    .alert-item.show {
      transform: translateX(0);
      opacity: 1;
    }

    .alert-item.hide {
      transform: translateX(100%);
      opacity: 0;
    }

    /* Alert variants - Sesuai Template */
    .alert-success {
      border: 1px solid #10b981;
      background-color: #f0fdf4;
      color: #065f46;
    }

    .dark .alert-success {
      border-color: #10b981;
      background-color: rgba(16, 185, 129, 0.15);
      color: #34d399;
    }

    .alert-error {
      border: 1px solid #ef4444;
      background-color: #fef2f2;
      color: #991b1b;
    }

    .dark .alert-error {
      border-color: #ef4444;
      background-color: rgba(239, 68, 68, 0.15);
      color: #f87171;
    }

    .alert-warning {
      border: 1px solid #f59e0b;
      background-color: #fffbeb;
      color: #92400e;
    }

    .dark .alert-warning {
      border-color: #f59e0b;
      background-color: rgba(245, 158, 11, 0.15);
      color: #fbbf24;
    }

    .alert-info {
      border: 1px solid #3b82f6;
      background-color: #eff6ff;
      color: #1e40af;
    }

    .dark .alert-info {
      border-color: #3b82f6;
      background-color: rgba(59, 130, 246, 0.15);
      color: #60a5fa;
    }

    /* Perkecil gap antara item dalam baris */
    #adminTable .gap-3 {
      gap: 0.5rem !important;
      /* gap-2 */
    }

    /* Perkecil margin bottom pada h5 dan p di kolom user */
    #adminTable h5.font-medium {
      margin-bottom: 0.125rem !important;
    }

    #adminTable p.text-sm {
      font-size: 0.75rem !important;
      /* text-xs */
      line-height: 1rem !important;
    }

    /* Padatkan konten di dalam sel */
    #adminTable .flex.items-center {
      align-items: center !important;
      min-height: 2.5rem !important;
    }

    /* PERKECIL TOMBOL TAMBAH ADMIN */
    .btn-tambah-admin {
      padding: 0.5rem 0.75rem !important;
      /* py-2 px-3 */
      font-size: 0.75rem !important;
      /* text-xs */
    }

    .btn-tambah-admin svg {
      width: 16px !important;
      height: 16px !important;
    }

    /* PERKECIL ELEMEN DATATABLES */
    .dt-length,
    .dt-paging,
    .dt-info {
      font-size: 0.75rem !important;
      /* text-xs */
    }

    .dt-length select {
      padding: 0.25rem 0.5rem !important;
      /* py-1 px-2 */
      font-size: 0.75rem !important;
      height: 1.75rem !important;
    }

    .dt-search input {
      padding: 0.25rem 0.5rem !important;
      /* py-1 px-2 */
      font-size: 0.75rem !important;
      height: 1.75rem !important;
    }

    /* PERKECIL PAGINATION DENGAN JARAK ATAS */
    .dt-paging {
      margin-top: 0.75rem !important;
      /* mt-3 */
    }

    .dt-paging .dt-paging-button {
      padding: 0.125rem 0.375rem !important;
      /* py-0.5 px-1.5 (lebih kecil) */
      font-size: 0.6875rem !important;
      /* lebih kecil dari text-xs */
      height: 1.5rem !important;
      /* h-6 */
      min-width: 1.5rem !important;
      /* min-w-6 */
      margin: 0 0.125rem !important;
      /* mx-0.5 */
    }

    .dt-paging .dt-paging-button.current {
      background-color: rgb(59 130 246) !important;
      /* bg-blue-500 */
      color: white !important;
      border-color: rgb(59 130 246) !important;
    }

    .dt-paging .dt-paging-button:not(.current):hover {
      background-color: rgb(243 244 246) !important;
      /* bg-gray-100 */
      color: rgb(55 65 81) !important;
      /* text-gray-700 */
    }

    .dark .dt-paging .dt-paging-button:not(.current):hover {
      background-color: rgb(55 65 81) !important;
      /* dark:bg-gray-700 */
      color: rgb(229 231 235) !important;
      /* dark:text-gray-200 */
    }

    /* Kompensasi untuk layout yang lebih kecil */
    .dt-input {
      border-radius: 0.375rem !important;
      /* rounded-md */
    }

    /* Jarak atas untuk info dan pagination */
    .dt-info {
      margin-top: 0.75rem !important;
      /* mt-3 */
    }

    /* Container pagination lebih kompak */
    .dt-paging.paging_simple_numbers {
      display: flex !important;
      align-items: center !important;
      gap: 0.25rem !important;
      /* gap-1 */
    }

    /* Tambahkan ke style yang sudah ada */
    .z-9999 {
      z-index: 9999 !important;
    }

    /* Pastikan modal konfirmasi hapus memiliki z-index yang tinggi */
    #modalHapusAdmin.modal-container {
      z-index: 10010 !important;
    }

    #modalHapusAdmin .modal-content {
      z-index: 10011 !important;
    }

    /* Modal konfirmasi hapus yang lebih kecil */
    #modalHapusAdmin {
      display: none;
    }

    #modalHapusAdmin:not(.hidden) {
      display: flex;
    }

    /* Agar modal tambah admin juga lebih kompak */
    #modalTambahAdmin {
      display: none;
    }

    #modalTambahAdmin:not(.hidden) {
      display: flex;
    }

    /* Pastikan modal konten tidak terlalu tinggi */
    .modal-content {
      max-height: 90vh;
      overflow-y: auto;
    }

    /* Responsif untuk mobile */
    @media (max-width: 640px) {
      .modal-content {
        max-width: 95%;
        margin: 0 10px;
      }
    }

    /* Notifikasi styles */
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

    .notification-warning {
      background-color: #f59e0b;
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

    @keyframes slideOutRight {
      from {
        transform: translateX(0);
        opacity: 1;
      }

      to {
        transform: translateX(100%);
        opacity: 0;
      }
    }

    /* Error message untuk file input */
    .file-error {
      font-size: 0.75rem;
      margin-top: 0.25rem;
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

    /* Tambahkan di bagian <style> */
    /* File name truncation */
    .file-name-truncate {
      max-width: 150px;
      /* Sesuaikan dengan kebutuhan */
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
      display: inline-block;
      vertical-align: middle;
    }

    /* Tooltip styling */
    .file-name-tooltip {
      position: relative;
      cursor: help;
    }

    .file-name-tooltip:hover::after {
      content: attr(data-fullname);
      position: absolute;
      bottom: 100%;
      left: 50%;
      transform: translateX(-50%);
      background-color: rgba(0, 0, 0, 0.8);
      color: white;
      padding: 6px 10px;
      border-radius: 4px;
      font-size: 12px;
      white-space: nowrap;
      z-index: 100;
      margin-bottom: 5px;
      pointer-events: none;
    }

    .file-name-tooltip:hover::before {
      content: '';
      position: absolute;
      bottom: 100%;
      left: 50%;
      transform: translateX(-50%);
      border-width: 5px;
      border-style: solid;
      border-color: rgba(0, 0, 0, 0.8) transparent transparent transparent;
      margin-bottom: -5px;
      pointer-events: none;
    }

    /* Tambahkan di bagian CSS yang sudah ada */
    .min-w-0 {
      min-width: 0 !important;
    }

    .truncate {
      overflow: hidden !important;
      text-overflow: ellipsis !important;
      white-space: nowrap !important;
    }

    /* Tooltip styling improvement */
    .file-name-tooltip {
      position: relative;
      cursor: help;
    }

    .file-name-tooltip:hover::after {
      content: attr(data-fullname);
      position: absolute;
      bottom: 100%;
      left: 0;
      background-color: rgba(0, 0, 0, 0.9);
      color: white;
      padding: 8px 12px;
      border-radius: 4px;
      font-size: 12px;
      white-space: normal;
      max-width: 300px;
      word-break: break-word;
      z-index: 1000;
      margin-bottom: 8px;
      pointer-events: none;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .file-name-tooltip:hover::before {
      content: '';
      position: absolute;
      bottom: 100%;
      left: 10px;
      border-width: 5px;
      border-style: solid;
      border-color: rgba(0, 0, 0, 0.9) transparent transparent transparent;
      margin-bottom: -1px;
      pointer-events: none;
    }

    /* Tombol disabled state */
    .bg-gray-400 {
      background-color: #9ca3af !important;
    }

    .opacity-50 {
      opacity: 0.5 !important;
    }

    .cursor-not-allowed {
      cursor: not-allowed !important;
    }

    /* Animations */
    @keyframes spin {
      0% {
        transform: rotate(0deg);
      }

      100% {
        transform: rotate(360deg);
      }
    }

    .animate-spin {
      animation: spin 1s linear infinite;
    }

    /* ==================== RESPONSIVE TABEL ADMIN ==================== */
    /* Tampilan Desktop - Normal */
    #adminTable {
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
      #adminTable {
        min-width: 600px;
        font-size: 0.875rem;
      }

      #adminTable th,
      #adminTable td {
        padding: 0.5rem 0.75rem !important;
      }
    }

    /* Tampilan Mobile (<= 768px) */
    @media (max-width: 768px) {
      #adminTable {
        min-width: 500px;
        font-size: 0.75rem;
      }

      #adminTable th,
      #adminTable td {
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

      /* Adjust avatar size */
      .h-8.w-8.rounded-full {
        width: 32px !important;
        height: 32px !important;
      }
    }

    /* Tampilan Very Small Mobile (<= 480px) */
    .modal-content .grid {
      display: grid !important;
      grid-template-columns: repeat(2, 1fr) !important;
      gap: 1rem !important;
    }

    .modal-content .grid>div {
      width: 100% !important;
    }

    /* Fix untuk warna tombol export */
    .modal-content button[type="submit"][name="format"][value="csv"]:hover {
      background-color: #15803d !important;
    }

    .modal-content button[type="submit"][name="format"][value="excel"]:hover {
      background-color: #1d4ed8 !important;
    }

    .modal-content button[type="submit"][name="format"][value="pdf"]:hover {
      background-color: #b91c1c !important;
    }

    /* Fix untuk tombol export utama */
    button[onclick="toggleModal('modalExportAdmin')"]:hover {
      background-color: #15803d !important;
    }

    /* Fix untuk tombol print */
    button[onclick="printData()"]:hover {
      background-color: #6d28d9 !important;
    }

    button[onclick="printPreview()"]:hover {
      background-color: #0e7490 !important;
    }

    @media (max-width: 480px) {
      #adminTable {
        min-width: 400px;
      }

      .flex.gap-2 {
        flex-direction: column !important;
        gap: 0.25rem !important;
      }

      .h-8.w-8.rounded-full {
        width: 28px !important;
        height: 28px !important;
      }
    }
  </style>
</head>

<body
  x-data="{ page: 'basicTables', 'loaded': true, 'darkMode': false, 'stickyMenu': false, 'sidebarToggle': false, 'scrollTop': false, 'modalOpen': false }"
  x-init="
         darkMode = JSON.parse(localStorage.getItem('darkMode'));
         $watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)))"
  :class="{'dark bg-gray-900': darkMode === true}">
  <!-- ===== Preloader Start ===== -->
  <div x-show="loaded"
    x-init="window.addEventListener('DOMContentLoaded', () => {setTimeout(() => loaded = false, 500)})"
    class="fixed left-0 top-0 z-999999 flex h-screen w-screen items-center justify-center bg-white dark:bg-black">
    <div class="h-16 w-16 animate-spin rounded-full border-4 border-solid border-brand-500 border-t-transparent"></div>
  </div>

  <!-- ===== Preloader End ===== -->

  <!-- Alert Container -->
  <div id="alertContainer" class="alert-container"></div>

  <!-- Overlay untuk seluruh halaman -->
  <div id="pageOverlay" class="page-overlay force-overlay hidden" onclick="toggleModal('modalTambahAdmin')"></div>

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
              Data Admin
            </h2>
            <nav>
              <ol class="flex items-center gap-2">
                <li><a class="font-medium" href="index.php">Home ></a></li>
                <li class="font-medium text-primary">Data Admin</li>
              </ol>
            </nav>
          </div>

          <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
            <div
              class="border-b border-stroke px-4 py-4 dark:border-strokedark sm:px-6 xl:px-7.5 flex justify-between items-center">
              <h3 class="font-medium text-black dark:text-white">Tabel Data Admin</h3>
              <div class="flex items-center gap-2">
                <button onclick="toggleModal('modalExportAdmin')"
                  class="inline-flex items-center gap-2 px-3 py-2 text-xs font-medium text-white transition rounded-lg shadow-theme-xs"
                  style="background-color: #16a34a; hover:background-color: #15803d;">
                  <svg class="fill-current" width="16" height="16" viewBox="0 0 20 20" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                      fill="white" />
                  </svg>
                  Export
                </button>
                <button onclick="toggleModal('modalTambahAdmin')"
                  class="btn-tambah-admin inline-flex items-center gap-2 text-xs font-medium text-white transition rounded-lg bg-brand-500 shadow-theme-xs hover:bg-brand-600">
                  <svg class="fill-current" width="16" height="16" viewBox="0 0 20 20" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                      d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                      fill="white" />
                  </svg>
                  Tambah Admin
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
                <table id="adminTable" class="w-full table-auto border-collapse text-left">
                  <thead>
                    <tr class="bg-gray-2 dark:bg-meta-4">
                      <th class="px-4 py-3 font-medium text-black dark:text-white">ID Admin</th>
                      <th class="px-4 py-3 font-medium text-black dark:text-white">User</th>
                      <th class="px-4 py-3 font-medium text-black dark:text-white">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    if (!empty($data_admin)) {
                      foreach ($data_admin as $row) {
                        $format_id = "A" . sprintf("%04d", $row['id_admin']);
                        ?>
                        <tr class="border-b border-[#eee] dark:border-strokedark">
                          <td class="px-4 py-3">
                            <p class="text-black dark:text-white"><?php echo $format_id; ?></p>
                          </td>

                          <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                              <div class="h-8 w-8 rounded-full">
                                <img src="src/images/user/<?php echo $row['foto_admin'] ?: 'user-01.png'; ?>" alt="User"
                                  class="rounded-full h-8 w-8 object-cover" />
                              </div>
                              <div>
                                <h5 class="font-medium text-black dark:text-white text-sm"><?php echo $row['nama_admin']; ?>
                                </h5>
                                <p class="text-xs text-gray-500"><?php echo $row['email_admin']; ?></p>
                              </div>
                            </div>
                          </td>

                          <td class="px-4 py-3">
                            <div class="flex gap-2">
                              <!-- Ganti tombol edit yang lama dengan ini -->
                              <div class="flex gap-2">
                                <button onclick="editAdmin(<?php echo $row['id_admin']; ?>)"
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
                                  onclick="showDeleteConfirmation(<?php echo $row['id_admin']; ?>, '<?php echo addslashes($row['nama_admin']); ?>')"
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
                            </div>
                          </td>
                        </tr>
                        <?php
                      }
                    } else {
                      echo "<tr><td colspan='3' class='text-center py-4'>Data Kosong</td></tr>";
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

  <!-- Modal Tambah Admin -->
  <div id="modalTambahAdmin"
    class="modal-container fixed inset-0 hidden items-center justify-center transition-all duration-300 z-9999">
    <div class="absolute inset-0 bg-black/20" onclick="toggleModal('modalTambahAdmin')"></div>

    <div
      class="modal-content bg-white dark:bg-boxdark w-full max-w-md mx-4 rounded-xl shadow-xl relative flex flex-col overflow-hidden">
      <button onclick="toggleModal('modalTambahAdmin')"
        class="absolute top-4 right-4 text-gray-400 hover:text-red-500 transition-colors">
        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path d="M18 6L6 18M6 6l12 12" />
        </svg>
      </button>

      <div class="p-6">
        <div class="mb-6">
          <h5 class="text-xl font-bold text-black dark:text-white">Tambah Admin Baru</h5>
          <p class="text-sm text-gray-500">Isi data admin yang akan ditambahkan.</p>
        </div>

        <form id="formTambahAdmin" action="tambahadmin.php" method="POST" enctype="multipart/form-data"
          class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-1 dark:text-white">Nama Admin *</label>
            <input type="text" name="nama_admin" id="tambah_nama_admin" required
              class="w-full rounded-lg border border-stroke bg-transparent px-4 py-2 outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4"
              oninput="removeSpaces(this)" onkeydown="preventSpace(event)" placeholder="Tanpa spasi (contoh: JohnDoe)">
          </div>

          <div>
            <label class="block text-sm font-medium mb-1 dark:text-white">Email *</label>
            <input type="email" name="email_admin" id="tambah_email_admin" required
              class="w-full rounded-lg border border-stroke bg-transparent px-4 py-2 outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4">
          </div>

          <div>
            <label class="block text-sm font-medium mb-1 dark:text-white">Password *</label>
            <input type="password" name="password_admin" id="tambah_password_admin" required
              class="w-full rounded-lg border border-stroke bg-transparent px-4 py-2 outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4">
          </div>

          <div>
            <label class="block text-sm font-medium mb-1 dark:text-white">Foto Admin</label>
            <div class="flex items-center gap-3">
              <div
                class="w-16 h-16 rounded-full overflow-hidden border-2 border-gray-200 bg-gray-100 flex items-center justify-center">
                <img id="tambah_current_foto" src="src/images/user/user-01.png" alt="Foto Admin"
                  class="w-full h-full object-cover">
              </div>
              <div class="flex-1">
                <div class="mb-2">
                  <label for="tambah_fileInput"
                    class="cursor-pointer bg-gray-100 hover:bg-gray-200 dark:bg-meta-4 px-4 py-2 rounded-lg border border-dashed border-primary text-sm font-medium transition flex-1 text-center block">
                    Pilih File...
                  </label>
                  <input type="file" name="foto_admin" id="tambah_fileInput" class="hidden" accept="image/*"
                    onchange="validateTambahFileInput()">
                </div>

                <!-- Container untuk nama file -->
                <div id="tambah_file_name_container" class="hidden mb-2">
                  <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-800 rounded-lg p-2">
                    <div class="flex-1 min-w-0">
                      <span id="tambah_fileName"
                        class="file-name-tooltip block text-sm text-gray-700 dark:text-gray-300 truncate"
                        data-fullname=""></span>
                    </div>
                    <button type="button" onclick="clearTambahFileInput()"
                      class="ml-2 text-red-500 hover:text-red-700 text-sm font-medium">
                      ×
                    </button>
                  </div>
                </div>

                <!-- Error message -->
                <div id="tambah_fileError" class="file-error text-xs mt-1"></div>
              </div>
            </div>
            <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, GIF. Maks: 2MB</p>
          </div>

          <div class="flex justify-end gap-3 pt-4">
            <button type="button" onclick="toggleModal('modalTambahAdmin')"
              class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">Batal</button>
            <button type="submit" id="tambah_submit_btn"
              class="inline-flex items-center gap-2 px-4 py-3 text-sm font-medium text-white transition rounded-lg bg-brand-500 shadow-theme-xs hover:bg-brand-600">
              <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd"
                  d="M10 3a1 1 0 00-1 1v5H4a1 1 0 100 2h5v5a1 1 0 102 0v-5h5a1 1 0 100-2h-5V4a1 1 0 00-1-1z"
                  fill="white" />
              </svg>
              Simpan
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Edit Admin -->
  <div id="modalEditAdmin"
    class="modal-container fixed inset-0 hidden items-center justify-center transition-all duration-300 z-9999">
    <div class="absolute inset-0 bg-black/20" onclick="toggleModal('modalEditAdmin')"></div>

    <div
      class="modal-content bg-white dark:bg-boxdark w-full max-w-md mx-4 rounded-xl shadow-xl relative flex flex-col overflow-hidden">
      <button onclick="toggleModal('modalEditAdmin')"
        class="absolute top-4 right-4 text-gray-400 hover:text-red-500 transition-colors">
        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path d="M18 6L6 18M6 6l12 12" />
        </svg>
      </button>

      <div class="p-6">
        <div class="mb-6">
          <h5 class="text-xl font-bold text-black dark:text-white">Edit Admin</h5>
          <p class="text-sm text-gray-500">Edit data admin.</p>
        </div>

        <form id="formEditAdmin" method="POST" enctype="multipart/form-data" class="space-y-4">
          <input type="hidden" name="id_admin" id="edit_id_admin">

          <div>
            <label class="block text-sm font-medium mb-1 dark:text-white">Nama Admin *</label>
            <input type="text" name="nama_admin" id="edit_nama_admin" required
              class="w-full rounded-lg border border-stroke bg-transparent px-4 py-2 outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4"
              oninput="removeSpaces(this)" onkeydown="preventSpace(event)" placeholder="Tanpa spasi (contoh: JohnDoe)">
          </div>

          <div>
            <label class="block text-sm font-medium mb-1 dark:text-white">Email *</label>
            <input type="email" name="email_admin" id="edit_email_admin" required
              class="w-full rounded-lg border border-stroke bg-transparent px-4 py-2 outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4">
          </div>

          <div>
            <label class="block text-sm font-medium mb-1 dark:text-white">Password (Kosongkan jika tidak diubah)</label>
            <input type="password" name="password_admin" id="edit_password_admin"
              class="w-full rounded-lg border border-stroke bg-transparent px-4 py-2 outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4">
          </div>

          <div>
            <div>
              <label class="block text-sm font-medium mb-1 dark:text-white">Foto Admin</label>
              <div class="flex items-center gap-3">
                <div class="w-16 h-16 rounded-full overflow-hidden border-2 border-gray-200">
                  <img id="edit_current_foto" src="" alt="Foto Admin" class="w-full h-full object-cover">
                </div>
                <div class="flex-1">
                  <div class="mb-2">
                    <label for="edit_fileInput"
                      class="cursor-pointer bg-gray-100 hover:bg-gray-200 dark:bg-meta-4 px-4 py-2 rounded-lg border border-dashed border-primary text-sm font-medium transition flex-1 text-center block">
                      Pilih File...
                    </label>
                    <input type="file" name="foto_admin" id="edit_fileInput" class="hidden" accept="image/*"
                      onchange="validateFileInput()">
                  </div>

                  <!-- Container untuk nama file -->
                  <div id="file_name_container" class="hidden mb-2">
                    <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-800 rounded-lg p-2">
                      <div class="flex-1 min-w-0">
                        <span id="edit_fileName"
                          class="file-name-tooltip block text-sm text-gray-700 dark:text-gray-300 truncate"
                          data-fullname=""></span>
                      </div>
                      <button type="button" onclick="clearFileInput()"
                        class="ml-2 text-red-500 hover:text-red-700 text-sm font-medium">
                        ×
                      </button>
                    </div>
                  </div>

                  <!-- Error message -->
                  <div id="edit_fileError" class="file-error text-xs mt-1"></div>
                </div>
              </div>
              <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, GIF. Maks: 2MB</p>
            </div>
          </div>

          <div class="flex justify-end gap-3 pt-4">
            <button type="button" onclick="toggleModal('modalEditAdmin')"
              class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">Batal</button>
            <button type="submit" id="edit_submit_btn"
              class="inline-flex items-center gap-2 px-4 py-3 text-sm font-medium text-white transition rounded-lg bg-brand-500 shadow-theme-xs hover:bg-brand-600">
              <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd"
                  d="M16.7071 5.29289C17.0976 5.68342 17.0976 6.31658 16.7071 6.70711L8.70711 14.7071C8.51957 14.8946 8.26522 15 8 15H5C4.44772 15 4 14.5523 4 14V11C4 10.7348 4.10536 10.4804 4.29289 10.2929L12.2929 2.29289C12.6834 1.90237 13.3166 1.90237 13.7071 2.29289L16.7071 5.29289ZM6 11.4142V13H7.58579L14.5858 6L14 5.41421L7 12.4142L6 11.4142ZM16 5L15.2929 4.29289L13 6.58579L13.5858 7.17157L16 5Z"
                  fill="white" />
              </svg>
              Update
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Konfirmasi Hapus -->
  <!-- Modal Konfirmasi Hapus -->
  <div id="modalHapusAdmin" class="fixed inset-0 hidden items-center justify-center transition-all duration-300 z-9999">

    <!-- Background overlay -->
    <div class="absolute inset-0 bg-black/20" onclick="toggleModal('modalHapusAdmin')"></div>

    <!-- Modal Content - Diperkecil jadi kotak -->
    <div
      class="relative bg-white dark:bg-boxdark w-full max-w-xs mx-4 rounded-xl shadow-xl flex flex-col overflow-hidden">
      <button onclick="toggleModal('modalHapusAdmin')"
        class="absolute top-3 right-3 text-gray-400 hover:text-red-500 transition-colors z-10">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path d="M18 6L6 18M6 6l12 12" />
        </svg>
      </button>

      <div class="p-5">
        <!-- Icon Warning - Diperkecil -->
        <div class="mx-auto w-12 h-12 mb-3 rounded-full bg-red-100 flex items-center justify-center">
          <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.994-.833-2.764 0L4.406 16.5c-.77.833.192 2.5 1.732 2.5z" />
          </svg>
        </div>

        <div class="text-center mb-4">
          <h5 class="text-lg font-bold text-black dark:text-white mb-1">Konfirmasi Hapus</h5>
          <p class="text-sm text-gray-600 dark:text-gray-400" id="hapusMessage">
            Apakah Anda yakin ingin menghapus data ini?
          </p>
        </div>

        <div class="flex justify-center gap-2">
          <button type="button" onclick="toggleModal('modalHapusAdmin')"
            class="px-4 py-2 text-xs font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
            Batal
          </button>
          <button type="button" id="confirmDeleteBtn" style="background-color: #dc2626;"
            class="inline-flex items-center gap-2 px-4 py-3 text-sm font-medium text-white transition rounded-lg hover:bg-red-700 shadow-theme-xs">
            Ya, Hapus
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Export Data Admin -->
  <div id="modalExportAdmin"
    class="modal-container fixed inset-0 hidden items-center justify-center transition-all duration-300 z-9999">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="toggleModal('modalExportAdmin')"></div>
    <div
      class="modal-content bg-white dark:bg-boxdark w-full max-w-md mx-4 rounded-2xl shadow-2xl relative flex flex-col overflow-hidden max-h-[90vh]">
      <!-- Modal Header -->
      <div
        class="sticky top-0 z-10 bg-gradient-to-r from-blue-600 to-blue-700 dark:from-blue-800 dark:to-blue-900 px-6 py-5">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </div>
            <div>
              <h5 class="text-lg font-bold text-white">Export Data Admin</h5>
              <p class="text-xs text-blue-100">Pilih filter dan format export data</p>
            </div>
          </div>
          <button onclick="toggleModal('modalExportAdmin')"
            class="text-white/80 hover:text-white transition-colors p-2 hover:bg-white/10 rounded-lg">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M18 6L6 18M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Modal Body -->
      <div class="p-6 overflow-y-auto">
        <form id="formExportAdmin" action="export_admin.php" method="POST" class="space-y-6">
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

              <!-- Filter by Email -->
              <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Filter Email</label>
                <input type="text" name="filter_email"
                  class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                  placeholder="Cari email...">
              </div>
              <!-- Filter by Nama -->
              <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Filter Nama</label>
                <input type="text" name="filter_nama"
                  class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                  placeholder="Cari nama...">
              </div>

              <!-- Date Range -->
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

            <div class="space-y-2">
              <label
                class="flex items-center gap-3 p-2 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-blue-400 dark:hover:border-blue-500 transition-all cursor-pointer">
                <input type="checkbox" name="columns[]" value="id_admin" checked
                  class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <div>
                  <span class="text-sm font-medium text-gray-700 dark:text-gray-300">ID Admin</span>
                  <p class="text-xs text-gray-500 dark:text-gray-400">Nomor identifikasi</p>
                </div>
              </label>

              <label
                class="flex items-center gap-3 p-2 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-blue-400 dark:hover:border-blue-500 transition-all cursor-pointer">
                <input type="checkbox" name="columns[]" value="email_admin" checked
                  class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <div>
                  <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Email</span>
                  <p class="text-xs text-gray-500 dark:text-gray-400">Alamat email</p>
                </div>
              </label>

              <label
                class="flex items-center gap-3 p-2 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-blue-400 dark:hover:border-blue-500 transition-all cursor-pointer">
                <input type="checkbox" name="columns[]" value="nama_admin" checked
                  class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <div>
                  <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Nama Admin</span>
                  <p class="text-xs text-gray-500 dark:text-gray-400">Nama lengkap</p>
                </div>
              </label>

              <label
                class="flex items-center gap-3 p-2 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-blue-400 dark:hover:border-blue-500 transition-all cursor-pointer">
                <input type="checkbox" name="columns[]" value="foto_admin" checked
                  class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <div>
                  <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Foto</span>
                  <p class="text-xs text-gray-500 dark:text-gray-400">File foto</p>
                </div>
              </label>

              <label
                class="flex items-center gap-3 p-2 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-blue-400 dark:hover:border-blue-500 transition-all cursor-pointer">
                <input type="checkbox" name="columns[]" value="created_at" checked
                  class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <div>
                  <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Dibuat</span>
                  <p class="text-xs text-gray-500 dark:text-gray-400">Waktu pembuatan</p>
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
                <button type="button" onclick="printData()"
                  class="flex items-center gap-2 px-4 py-2 rounded-lg font-medium text-sm transition-colors shadow-sm"
                  style="background-color: #7c3aed; color: white; hover:background-color: #6d28d9;">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm-8 0h2v2H7v-2z" />
                  </svg>
                  Print Data
                </button>

                <button type="button" onclick="printPreview()"
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

  <script>
    // Validasi form export
    document.getElementById('formExportAdmin').addEventListener('submit', function (e) {
      const checkboxes = document.querySelectorAll('input[name="columns[]"]:checked');
      if (checkboxes.length === 0) {
        e.preventDefault();
        alert('Pilih minimal 1 kolom untuk di-export!');
        return false;
      }
    });

    // Fungsi untuk print data langsung
    function printData() {
      const form = document.getElementById('formExportAdmin');
      const formData = new FormData(form);

      // Validasi minimal 1 kolom dipilih
      const checkboxes = document.querySelectorAll('input[name="columns[]"]:checked');
      if (checkboxes.length === 0) {
        alert('Pilih minimal 1 kolom untuk print!');
        return;
      }

      // Buka window baru untuk print
      const printWindow = window.open('', '_blank');

      // Kirim data ke server untuk generate print view
      formData.append('format', 'print');

      fetch('export_admin.php', {
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

    // Fungsi untuk preview data
    function printPreview() {
      const form = document.getElementById('formExportAdmin');
      const formData = new FormData(form);

      // Validasi minimal 1 kolom dipilih
      const checkboxes = document.querySelectorAll('input[name="columns[]"]:checked');
      if (checkboxes.length === 0) {
        alert('Pilih minimal 1 kolom untuk preview!');
        return;
      }

      // Buka window baru untuk preview
      const previewWindow = window.open('', '_blank', 'width=800,height=600');

      // Kirim data ke server untuk generate preview
      formData.append('format', 'preview');

      fetch('export_admin.php', {
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
  </script>

  <!-- ===== Page Wrapper End ===== -->
  <script defer src="bundle.js"></script>

  <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
  <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
  <script src="https://cdn.datatables.net/2.0.8/js/dataTables.tailwindcss.js"></script>

  <script>
    $(document).ready(function () {
      $('#adminTable').DataTable({
        "dom": '<"flex flex-wrap items-center justify-between gap-3 mb-2"lf>rt<"flex flex-wrap items-center justify-between gap-3 mt-2"ip>',
        "language": {
          "search": "",
          "searchPlaceholder": "Cari admin...",
          "lengthMenu": "Tampilkan _MENU_ data",
          "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
          "paginate": {
            "previous": "‹",
            "next": "›"
          }
        },
        "pageLength": 5,
        "responsive": true,
        "initComplete": function () {
          // Pindahkan kontrol DataTables ke container kustom
          var $wrapper = $('#adminTable_wrapper');

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

    // Variabel global untuk status validasi
    let isFileValid = true;
    let currentEditAdminId = null;
    let adminToDelete = null;
    let adminNameToDelete = '';

    function toggleModal(modalId) {
      const modal = document.getElementById(modalId);
      const overlay = document.getElementById('pageOverlay');
      const wrapper = document.getElementById('main-wrapper');
      const header = document.querySelector('header');
      const sidebar = document.querySelector('aside');

      if (modal.style.display === 'none' || modal.classList.contains('hidden')) {
        // Tutup modal lain yang mungkin terbuka
        const openModals = document.querySelectorAll('.fixed.inset-0:not(.hidden)');
        openModals.forEach(openModal => {
          if (openModal.id !== modalId && openModal.id.includes('modal')) {
            openModal.style.display = 'none';
            openModal.classList.add('hidden');
            openModal.classList.remove('flex');
          }
        });

        // Buka modal
        modal.style.display = 'flex';
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        if (overlay) {
          overlay.classList.remove('hidden');
          overlay.classList.add('block');
        }

        document.body.style.overflow = 'hidden';

        // Turunkan z-index header dan sidebar
        if (header) header.style.zIndex = '50';
        if (sidebar) sidebar.style.zIndex = '50';

        // Nonaktifkan scrolling pada wrapper
        if (wrapper) {
          wrapper.style.overflow = 'hidden';
        }

        // Reset status validasi saat modal dibuka
        if (modalId === 'modalEditAdmin') {
          isFileValid = true;
          updateSubmitButtonState();
        }
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

        // Reset z-index header dan sidebar
        if (header) header.style.zIndex = '';
        if (sidebar) sidebar.style.zIndex = '';

        // Aktifkan kembali scrolling pada wrapper
        if (wrapper) {
          wrapper.style.overflow = '';
        }
      }
    }

    function hideAllModals() {
      const modals = ['modalTambahAdmin', 'modalEditAdmin', 'modalHapusAdmin', 'modalExportAdmin'];
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

    function showDeleteConfirmation(id, name) {
      adminToDelete = id;
      adminNameToDelete = name;

      const message = document.getElementById('hapusMessage');
      message.textContent = `Apakah Anda yakin ingin menghapus admin "${name}"? Tindakan ini tidak dapat dibatalkan.`;

      toggleModal('modalHapusAdmin');
    }

    function deleteAdmin() {
      if (adminToDelete) {
        window.location.href = 'tambahadmin.php?action=hapus&id=' + adminToDelete;
      }
    }

    // Variabel untuk modal tambah admin
    let isTambahFileValid = true;

    // Fungsi untuk mencegah input spasi
    function preventSpace(event) {
      if (event.key === ' ') {
        event.preventDefault();
        return false;
      }
    }

    // Fungsi untuk menghapus spasi dari input
    function removeSpaces(input) {
      // Hapus semua spasi dari nilai input
      input.value = input.value.replace(/\s/g, '');
    }

    // Fungsi untuk validasi file di modal tambah
    function validateTambahFileInput() {
      const input = document.getElementById('tambah_fileInput');
      const file = input.files[0];
      const errorElement = document.getElementById('tambah_fileError');
      const fileNameElement = document.getElementById('tambah_fileName');
      const fileNameContainer = document.getElementById('tambah_file_name_container');
      const submitBtn = document.getElementById('tambah_submit_btn');

      // Reset error message
      errorElement.innerHTML = '';
      isTambahFileValid = true;

      if (file) {
        // Validasi ukuran file (max 2MB)
        const maxSize = 2 * 1024 * 1024;
        if (file.size > maxSize) {
          errorElement.innerHTML = '<span class="text-red-500">❌ Ukuran file terlalu besar (maks 2MB)</span>';
          isTambahFileValid = false;
          input.value = '';
          fileNameContainer.classList.add('hidden');
        }
        // Validasi tipe file
        else {
          const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
          if (!allowedTypes.includes(file.type)) {
            errorElement.innerHTML = '<span class="text-red-500">❌ Format tidak didukung (hanya JPG, PNG, GIF)</span>';
            isTambahFileValid = false;
            input.value = '';
            fileNameContainer.classList.add('hidden');
          } else {
            // Validasi ekstensi file
            const fileName = file.name;
            const fileExt = fileName.split('.').pop().toLowerCase();
            const allowedExt = ['jpg', 'jpeg', 'png', 'gif'];

            if (!allowedExt.includes(fileExt)) {
              errorElement.innerHTML = '<span class="text-red-500">❌ Ekstensi file tidak valid</span>';
              isTambahFileValid = false;
              input.value = '';
              fileNameContainer.classList.add('hidden');
            } else {
              // File valid, tampilkan preview dan nama file
              const reader = new FileReader();
              reader.onload = function (e) {
                document.getElementById('tambah_current_foto').src = e.target.result;
              };
              reader.readAsDataURL(file);

              // Tampilkan nama file dengan truncation
              fileNameElement.textContent = truncateFileName(fileName, 30);
              fileNameElement.setAttribute('data-fullname', fileName);
              fileNameContainer.classList.remove('hidden');

              errorElement.innerHTML = '<span class="text-green-500">✓ File valid</span>';
            }
          }
        }
      } else {
        fileNameContainer.classList.add('hidden');
      }

      // Update status tombol submit
      updateTambahSubmitButtonState();
    }

    // Fungsi untuk menghapus file input di modal tambah
    function clearTambahFileInput() {
      const input = document.getElementById('tambah_fileInput');
      const fileNameContainer = document.getElementById('tambah_file_name_container');
      const errorElement = document.getElementById('tambah_fileError');

      input.value = '';
      fileNameContainer.classList.add('hidden');
      errorElement.innerHTML = '';
      isTambahFileValid = true;

      // Reset ke foto default
      document.getElementById('tambah_current_foto').src = 'src/images/user/user-01.png';

      updateTambahSubmitButtonState();
    }

    // Fungsi untuk mengubah state tombol submit di modal tambah
    function updateTambahSubmitButtonState() {
      const submitBtn = document.getElementById('tambah_submit_btn');

      if (!submitBtn) return;

      // Validasi form lainnya
      const nama = document.getElementById('tambah_nama_admin').value.trim();
      const email = document.getElementById('tambah_email_admin').value.trim();
      const password = document.getElementById('tambah_password_admin').value.trim();

      const isFormValid = nama !== '' && email !== '' && password !== '' && isTambahFileValid;

      if (isFormValid) {
        // Aktifkan tombol
        submitBtn.disabled = false;
        submitBtn.classList.remove('bg-gray-400', 'cursor-not-allowed', 'opacity-50');
        submitBtn.classList.add('bg-brand-500', 'hover:bg-brand-600', 'cursor-pointer');
      } else {
        // Nonaktifkan tombol
        submitBtn.disabled = true;
        submitBtn.classList.remove('bg-brand-500', 'hover:bg-brand-600', 'cursor-pointer');
        submitBtn.classList.add('bg-gray-400', 'cursor-not-allowed', 'opacity-50');
      }
    }

    // Reset modal tambah saat dibuka
    function resetTambahModal() {
      document.getElementById('tambah_nama_admin').value = '';
      document.getElementById('tambah_email_admin').value = '';
      document.getElementById('tambah_password_admin').value = '';
      document.getElementById('tambah_current_foto').src = 'src/images/user/user-01.png';

      // Reset file input
      document.getElementById('tambah_fileInput').value = '';
      document.getElementById('tambah_file_name_container').classList.add('hidden');
      document.getElementById('tambah_fileError').innerHTML = '';
      document.getElementById('tambah_fileName').textContent = '';
      document.getElementById('tambah_fileName').setAttribute('data-fullname', '');

      isTambahFileValid = true;
      updateTambahSubmitButtonState();
    }

    // Event listener untuk tombol konfirmasi hapus
    document.addEventListener('DOMContentLoaded', function () {
      const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
      if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', deleteAdmin);
      }
    });

    // Event listeners untuk modal tambah admin
    const tambahNamaInput = document.getElementById('tambah_nama_admin');
    const tambahEmailInput = document.getElementById('tambah_email_admin');
    const tambahPasswordInput = document.getElementById('tambah_password_admin');

    if (tambahNamaInput) {
      tambahNamaInput.addEventListener('input', updateTambahSubmitButtonState);
    }

    if (tambahEmailInput) {
      tambahEmailInput.addEventListener('input', updateTambahSubmitButtonState);
    }

    if (tambahPasswordInput) {
      tambahPasswordInput.addEventListener('input', updateTambahSubmitButtonState);
    }

    // Validasi file input untuk modal tambah
    const tambahFileInput = document.getElementById('tambah_fileInput');
    if (tambahFileInput) {
      tambahFileInput.addEventListener('change', validateTambahFileInput);
    }

    // Handle submit form tambah
    const formTambah = document.getElementById('formTambahAdmin');
    let isSubmitting = false; // Flag untuk prevent double submit

    if (formTambah) {
      formTambah.addEventListener('submit', function (e) {
        e.preventDefault();

        // Prevent double submit
        if (isSubmitting) {
          return;
        }
        isSubmitting = true;

        // Validasi akhir sebelum submit
        const fileInput = document.getElementById('tambah_fileInput');
        const file = fileInput.files[0];

        if (file) {
          // Validasi ulang
          const maxSize = 2 * 1024 * 1024;
          const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];

          if (file.size > maxSize) {
            showNotification('error', 'Ukuran file terlalu besar! Maksimal 2MB.', 'Error Validasi File');
            isSubmitting = false;
            return;
          }

          if (!allowedTypes.includes(file.type)) {
            showNotification('error', 'Format file tidak didukung! Hanya JPG, PNG, atau GIF.', 'Error Validasi File');
            isSubmitting = false;
            return;
          }
        }

        // Validasi form lainnya
        const nama = document.getElementById('tambah_nama_admin').value.trim();
        const email = document.getElementById('tambah_email_admin').value.trim();
        const password = document.getElementById('tambah_password_admin').value.trim();

        if (!nama || !email || !password) {
          showNotification('error', 'Nama, Email, dan Password harus diisi.', 'Form Tidak Lengkap');
          isSubmitting = false;
          return;
        }

        // Validasi nama tidak boleh mengandung spasi
        if (nama.includes(' ')) {
          showNotification('error', 'Nama admin tidak boleh mengandung spasi! Gunakan format tanpa spasi (contoh: JohnDoe).', 'Error Validasi Nama');
          isSubmitting = false;
          return;
        }

        // Validasi email format
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
          showNotification('error', 'Format email tidak valid.', 'Error Validasi Email');
          isSubmitting = false;
          return;
        }

        // Tampilkan loading
        const submitBtn = document.getElementById('tambah_submit_btn');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Menyimpan...';
        submitBtn.disabled = true;

        // Kirim form
        const formData = new FormData(this);

        fetch('tambahadmin.php', {
          method: 'POST',
          body: formData
        })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              showNotification('success', data.message, data.title);
              resetTambahModal();
              toggleModal('modalTambahAdmin');

              // Auto refresh setelah 2 detik untuk user bisa melihat alert
              setTimeout(() => {
                location.reload();
              }, 2000);
              isSubmitting = false;
            } else {
              showNotification('error', data.message, data.title);
              submitBtn.innerHTML = originalText;
              submitBtn.disabled = false;
              updateTambahSubmitButtonState();
              isSubmitting = false;
            }
          })
          .catch(error => {
            console.error('Error:', error);
            showNotification('error', 'Terjadi kesalahan saat menambah data', 'Error Server');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            updateTambahSubmitButtonState();
            isSubmitting = false;
          });
      });
    }

    function toggleModal(modalId) {
      const modal = document.getElementById(modalId);
      const overlay = document.getElementById('pageOverlay');
      const wrapper = document.getElementById('main-wrapper');
      const header = document.querySelector('header');
      const sidebar = document.querySelector('aside');

      if (modal.style.display === 'none' || modal.classList.contains('hidden')) {
        // Tutup modal lain yang mungkin terbuka
        const openModals = document.querySelectorAll('.fixed.inset-0:not(.hidden)');
        openModals.forEach(openModal => {
          if (openModal.id !== modalId && openModal.id.includes('modal')) {
            openModal.style.display = 'none';
            openModal.classList.add('hidden');
            openModal.classList.remove('flex');
          }
        });

        // Buka modal
        modal.style.display = 'flex';
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        if (overlay) {
          overlay.classList.remove('hidden');
          overlay.classList.add('block');
        }

        document.body.style.overflow = 'hidden';

        // Turunkan z-index header dan sidebar
        if (header) header.style.zIndex = '50';
        if (sidebar) sidebar.style.zIndex = '50';

        // Nonaktifkan scrolling pada wrapper
        if (wrapper) {
          wrapper.style.overflow = 'hidden';
        }

        // Reset status validasi saat modal dibuka
        if (modalId === 'modalEditAdmin') {
          isFileValid = true;
          updateSubmitButtonState();
        }

        // Reset modal tambah saat dibuka
        if (modalId === 'modalTambahAdmin') {
          resetTambahModal();
        }
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

        // Reset z-index header dan sidebar
        if (header) header.style.zIndex = '';
        if (sidebar) sidebar.style.zIndex = '';

        // Aktifkan kembali scrolling pada wrapper
        if (wrapper) {
          wrapper.style.overflow = '';
        }
      }
    }
    function editAdmin(id) {
      currentEditAdminId = id;

      fetch(`getadmin.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            document.getElementById('edit_id_admin').value = data.data.id_admin;
            document.getElementById('edit_nama_admin').value = data.data.nama_admin;
            document.getElementById('edit_email_admin').value = data.data.email_admin;

            const fotoUrl = `src/images/user/${data.data.foto_admin || 'user-01.png'}`;
            document.getElementById('edit_current_foto').src = fotoUrl;

            // Reset file container
            document.getElementById('file_name_container').classList.add('hidden');
            document.getElementById('edit_fileName').textContent = '';
            document.getElementById('edit_fileName').setAttribute('data-fullname', '');

            // Reset status validasi
            isFileValid = true;
            document.getElementById('edit_fileError').innerHTML = '';
            updateSubmitButtonState();

            toggleModal('modalEditAdmin');
          } else {
            showNotification('error', 'Gagal mengambil data admin', 'Error Database');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          showNotification('error', 'Terjadi kesalahan saat mengambil data', 'Error Server');
        });
    }

    // Fungsi untuk menghapus file input
    function clearFileInput() {
      const input = document.getElementById('edit_fileInput');
      const fileNameContainer = document.getElementById('file_name_container');
      const errorElement = document.getElementById('edit_fileError');

      input.value = '';
      fileNameContainer.classList.add('hidden');
      errorElement.innerHTML = '';
      isFileValid = true;

      // Reset ke foto sebelumnya
      if (currentEditAdminId) {
        fetch(`getadmin.php?id=${currentEditAdminId}`)
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              const fotoUrl = `src/images/user/${data.data.foto_admin || 'user-01.png'}`;
              document.getElementById('edit_current_foto').src = fotoUrl;
            }
          });
      }

      updateSubmitButtonState();
    }

    // Fungsi validasi file
    function validateFileInput() {
      const input = document.getElementById('edit_fileInput');
      const file = input.files[0];
      const errorElement = document.getElementById('edit_fileError');
      const fileNameElement = document.getElementById('edit_fileName');
      const fileNameContainer = document.getElementById('file_name_container');

      // Reset error message
      errorElement.innerHTML = '';
      isFileValid = true;

      if (file) {
        // Validasi ukuran file (max 2MB)
        const maxSize = 2 * 1024 * 1024;
        if (file.size > maxSize) {
          errorElement.innerHTML = '<span class="text-red-500">❌ Ukuran file terlalu besar (maks 2MB)</span>';
          isFileValid = false;
          input.value = '';
          fileNameContainer.classList.add('hidden');
        }
        // Validasi tipe file
        else {
          const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
          if (!allowedTypes.includes(file.type)) {
            errorElement.innerHTML = '<span class="text-red-500">❌ Format tidak didukung (hanya JPG, PNG, GIF)</span>';
            isFileValid = false;
            input.value = '';
            fileNameContainer.classList.add('hidden');
          } else {
            // Validasi ekstensi file
            const fileName = file.name;
            const fileExt = fileName.split('.').pop().toLowerCase();
            const allowedExt = ['jpg', 'jpeg', 'png', 'gif'];

            if (!allowedExt.includes(fileExt)) {
              errorElement.innerHTML = '<span class="text-red-500">❌ Ekstensi file tidak valid</span>';
              isFileValid = false;
              input.value = '';
              fileNameContainer.classList.add('hidden');
            } else {
              // File valid, tampilkan preview dan nama file
              const reader = new FileReader();
              reader.onload = function (e) {
                document.getElementById('edit_current_foto').src = e.target.result;
              };
              reader.readAsDataURL(file);

              // Tampilkan nama file dengan truncation
              fileNameElement.textContent = truncateFileName(fileName, 30);
              fileNameElement.setAttribute('data-fullname', fileName);
              fileNameContainer.classList.remove('hidden');

              errorElement.innerHTML = '<span class="text-green-500">✓ File valid</span>';
            }
          }
        }
      } else {
        fileNameContainer.classList.add('hidden');
      }

      // Update status tombol submit
      updateSubmitButtonState();
    }

    // Fungsi untuk memotong nama file
    function truncateFileName(fileName, maxLength) {
      if (fileName.length <= maxLength) {
        return fileName;
      }
      const extension = fileName.split('.').pop();
      const nameWithoutExt = fileName.substring(0, fileName.length - extension.length - 1);
      const truncatedName = nameWithoutExt.substring(0, maxLength - extension.length - 4) + '...';
      return truncatedName + '.' + extension;
    }

    // Fungsi untuk mengubah state tombol submit
    function updateSubmitButtonState() {
      const submitBtn = document.getElementById('edit_submit_btn');

      if (!submitBtn) return;

      // Validasi form lainnya
      const nama = document.getElementById('edit_nama_admin').value.trim();
      const email = document.getElementById('edit_email_admin').value.trim();

      const isFormValid = nama !== '' && email !== '' && isFileValid;

      if (isFormValid) {
        // Aktifkan tombol
        submitBtn.disabled = false;
        submitBtn.classList.remove('bg-gray-400', 'cursor-not-allowed', 'opacity-50');
        submitBtn.classList.add('bg-brand-500', 'hover:bg-brand-600', 'cursor-pointer');
      } else {
        // Nonaktifkan tombol
        submitBtn.disabled = true;
        submitBtn.classList.remove('bg-brand-500', 'hover:bg-brand-600', 'cursor-pointer');
        submitBtn.classList.add('bg-gray-400', 'cursor-not-allowed', 'opacity-50');
      }
    }

    // Event listeners untuk validasi real-time
    document.addEventListener('DOMContentLoaded', function () {
      // Validasi form input
      const namaInput = document.getElementById('edit_nama_admin');
      const emailInput = document.getElementById('edit_email_admin');

      if (namaInput) {
        namaInput.addEventListener('input', updateSubmitButtonState);
      }

      if (emailInput) {
        emailInput.addEventListener('input', updateSubmitButtonState);
      }

      // Handle submit form
      const form = document.getElementById('formEditAdmin');
      if (form) {
        form.addEventListener('submit', function (e) {
          e.preventDefault();

          // Validasi akhir sebelum submit
          const fileInput = document.getElementById('edit_fileInput');
          const file = fileInput.files[0];

          if (file) {
            // Validasi ulang
            const maxSize = 2 * 1024 * 1024;
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];

            if (file.size > maxSize) {
              showNotification('error', 'Ukuran file terlalu besar! Maksimal 2MB.', 'Error Validasi File');
              return;
            }

            if (!allowedTypes.includes(file.type)) {
              showNotification('error', 'Format file tidak didukung! Hanya JPG, PNG, atau GIF.', 'Error Validasi File');
              return;
            }
          }

          // Validasi form lainnya
          const nama = document.getElementById('edit_nama_admin').value.trim();
          const email = document.getElementById('edit_email_admin').value.trim();

          if (!nama || !email) {
            showNotification('error', 'Nama dan Email harus diisi.', 'Form Tidak Lengkap');
            return;
          }

          // Validasi nama tidak boleh mengandung spasi
          if (nama.includes(' ')) {
            showNotification('error', 'Nama admin tidak boleh mengandung spasi! Gunakan format tanpa spasi (contoh: JohnDoe).', 'Error Validasi Nama');
            return;
          }

          // Tampilkan loading
          const submitBtn = document.getElementById('edit_submit_btn');
          const originalText = submitBtn.innerHTML;
          submitBtn.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memproses...';
          submitBtn.disabled = true;

          // Kirim form
          const formData = new FormData(this);

          fetch('updateadmin.php', {
            method: 'POST',
            body: formData
          })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                showNotification('success', 'Data admin berhasil diupdate!', 'Update Berhasil');
                toggleModal('modalEditAdmin');

                setTimeout(() => {
                  location.reload();
                }, 1500);
              } else {
                showNotification('error', 'Gagal mengupdate data: ' + data.message, 'Error Update');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                updateSubmitButtonState();
              }
            })
            .catch(error => {
              console.error('Error:', error);
              showNotification('error', 'Terjadi kesalahan saat mengupdate data', 'Error Server');
              submitBtn.innerHTML = originalText;
              submitBtn.disabled = false;
              updateSubmitButtonState();
            });
        });
      }
    });

    // Fungsi untuk menampilkan notifikasi dengan template yang diberikan
    function showNotification(type, message, title = null, showRefreshButton = false) {
      const alertContainer = document.getElementById('alertContainer');

      // Buat alert element
      const alertElement = document.createElement('div');
      alertElement.className = `alert-item alert-${type} rounded-xl p-4`;

      // Tambahkan dark mode class jika diperlukan
      if (document.body.classList.contains('dark')) {
        alertElement.classList.add('dark');
      }

      // Icon sesuai type - Sesuai Template
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

      // Default title jika tidak ada
      const alertTitle = title || (type === 'success' ? 'Success' : type === 'error' ? 'Error' : type === 'warning' ? 'Warning' : 'Info');

      // Build alert HTML
      let alertHTML = `
        <div class="flex items-start gap-3">
          <div class="-mt-0.5 ${iconColor}">
            ${iconSvg}
          </div>
          <div class="flex-1">
            <h4 class="mb-1 text-sm font-semibold text-gray-800 dark:text-white/90">
              ${alertTitle}
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

      // Tambahkan tombol refresh jika diperlukan
      if (showRefreshButton) {
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

      // Auto hide after 8 seconds (lebih lama karena ada auto refresh)
      setTimeout(() => {
        closeAlert(alertElement.querySelector('button'));
      }, showRefreshButton ? 8000 : 5000);
    }

    // Fungsi untuk menutup alert
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

    // Tambahkan style untuk animasi dan tombol disabled
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slide-in {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes spin {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }
        
        .animate-slide-in {
            animation: slide-in 0.3s ease-out;
        }
        
        .animate-spin {
            animation: spin 1s linear infinite;
        }
        
        .text-xs {
            font-size: 0.75rem;
            line-height: 1rem;
        }
        
        /* Tombol disabled */
        button:disabled {
            cursor: not-allowed !important;
        }
        
        .bg-gray-400 {
            background-color: #9ca3af !important;
        }
        
        .opacity-50 {
            opacity: 0.5 !important;
        }
        
        .cursor-not-allowed {
            cursor: not-allowed !important;
        }
        
        .cursor-pointer {
            cursor: pointer !important;
        }
        
        /* Truncate text */
        .truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .min-w-0 {
            min-width: 0;
        }
        
        /* Tooltip styling */
        .file-name-tooltip {
            position: relative;
            display: inline-block;
        }
        
        .file-name-tooltip:hover::after {
            content: attr(data-fullname);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background-color: rgba(0, 0, 0, 0.9);
            color: white;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 12px;
            white-space: normal;
            max-width: 300px;
            word-break: break-word;
            z-index: 1000;
            margin-bottom: 5px;
            pointer-events: none;
        }
        
        .file-name-tooltip:hover::before {
            content: '';
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            border-width: 5px;
            border-style: solid;
            border-color: rgba(0, 0, 0, 0.9) transparent transparent transparent;
            margin-bottom: -1px;
            pointer-events: none;
        }
    `;
    document.head.appendChild(style);

    // Tampilkan alert dari session jika ada
    <?php if ($session_alert): ?>
      setTimeout(() => {
        showNotification('<?= $session_alert['type'] ?>', '<?= $session_alert['message'] ?>', '<?= $session_alert['title'] ?>');
      }, 500);
    <?php endif; ?>
  </script>

</body>

</html>