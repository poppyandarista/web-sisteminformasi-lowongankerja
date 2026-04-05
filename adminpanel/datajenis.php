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
    if ($db->hapus_jenis($id)) {
      $ajax_response['success'] = true;
      $ajax_response['message'] = 'Data jenis berhasil dihapus';
    } else {
      $ajax_response['message'] = 'Gagal menghapus data jenis';
    }
  } else {
    $ajax_response['message'] = 'ID tidak valid';
  }
  echo json_encode($ajax_response);
  exit;
}

// Tangani edit via AJAX
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit') {
  $id_jenis = $_POST['id_jenis'];
  $nama_jenis = $_POST['nama_jenis'];

  if ($db->update_jenis($id_jenis, $nama_jenis)) {
    $ajax_response['success'] = true;
    $ajax_response['message'] = 'Data jenis berhasil diperbarui';
  } else {
    $ajax_response['message'] = 'Gagal mengupdate data';
  }
  echo json_encode($ajax_response);
  exit;
}

// Tangani tambah via AJAX
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'tambah') {
  $nama_jenis = $_POST['nama_jenis'];

  if ($db->tambah_jenis($nama_jenis)) {
    $ajax_response['success'] = true;
    $ajax_response['message'] = 'Data jenis berhasil ditambahkan';
  } else {
    $ajax_response['message'] = 'Gagal menambahkan data jenis';
  }
  echo json_encode($ajax_response);
  exit;
}

// Reset content type untuk normal HTML
header('Content-Type: text/html; charset=UTF-8');

// Ambil data jenis
$data_jenis = $db->tampil_data_jenis();

// Tangani hapus (legacy - non-AJAX)
if (isset($_GET['action']) && $_GET['action'] == 'hapus' && isset($_GET['id'])) {
  $id = $_GET['id'];

  if (is_numeric($id)) {
    if ($db->hapus_jenis($id)) {
      $data_jenis = $db->tampil_data_jenis();
    }
  }
}

// Tangani edit (legacy - non-AJAX)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit') {
  $id_jenis = $_POST['id_jenis'];
  $nama_jenis = $_POST['nama_jenis'];

  if ($db->update_jenis($id_jenis, $nama_jenis)) {
    $data_jenis = $db->tampil_data_jenis();
  }
}

// Tangani tambah (legacy - non-AJAX)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'tambah') {
  $nama_jenis = $_POST['nama_jenis'];

  if ($db->tambah_jenis($nama_jenis)) {
    $data_jenis = $db->tampil_data_jenis();
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
  <title>Data Jenis | LinkUp</title>
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

    /* Style untuk tabel jenis */
    #jenisTable tbody tr {
      height: 60px !important;
    }

    #jenisTable td {
      padding-top: 0.75rem !important;
      padding-bottom: 0.75rem !important;
      padding-left: 1rem !important;
      padding-right: 1rem !important;
    }

    #jenisTable th {
      padding-top: 0.75rem !important;
      padding-bottom: 0.75rem !important;
    }

    .btn-tambah-jenis {
      padding: 0.5rem 0.75rem !important;
      font-size: 0.75rem !important;
    }

    .btn-tambah-jenis svg {
      width: 16px !important;
      height: 16px !important;
    }

    /* Modal konfirmasi hapus */
    #modalHapusJenis {
      display: none;
    }

    #modalHapusJenis:not(.hidden) {
      display: flex;
    }

    #modalTambahJenis,
    #modalEditJenis {
      display: none;
    }

    #modalTambahJenis:not(.hidden),
    #modalEditJenis:not(.hidden) {
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

    /* GANTI CSS DataTables di datajenis.php dengan ini: */

    /* PERBAIKAN UNTUK CONTROLS DATATABLES v2 */
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
    #jenisTable_wrapper .flex {
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

    /* ==================== RESPONSIVE TABEL JENIS ==================== */
    /* Tampilan Desktop - Normal */
    #jenisTable {
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
      #jenisTable {
        min-width: 400px;
        font-size: 0.875rem;
      }

      #jenisTable th,
      #jenisTable td {
        padding: 0.5rem 0.75rem !important;
      }
    }

    /* Tampilan Mobile (<= 768px) */
    @media (max-width: 768px) {
      #jenisTable {
        min-width: 350px;
        font-size: 0.75rem;
      }

      #jenisTable th,
      #jenisTable td {
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
    }

    /* Tampilan Very Small Mobile (<= 480px) */
    @media (max-width: 480px) {
      #jenisTable {
        min-width: 300px;
      }

      .flex.items-center.gap-2 {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.25rem !important;
      }
    }
  </style>
</head>

<body
  x-data="{ page: 'dataJenis', 'loaded': true, 'darkMode': false, 'stickyMenu': false, 'sidebarToggle': false, 'scrollTop': false, 'modalOpen': false }"
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
              Data Jenis Pekerjaan
            </h2>
            <nav>
              <ol class="flex items-center gap-2">
                <li><a class="font-medium" href="index.php">Home ></a></li>
                <li class="font-medium text-primary">Data Jenis</li>
              </ol>
            </nav>
          </div>

          <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
            <div
              class="border-b border-stroke px-4 py-4 dark:border-strokedark sm:px-6 xl:px-7.5 flex justify-between items-center">
              <h3 class="font-medium text-black dark:text-white">Tabel Data Jenis Pekerjaan</h3>
              <button onclick="toggleModal('modalTambahJenis')"
                class="btn-tambah-jenis inline-flex items-center gap-2 text-xs font-medium text-white transition rounded-lg bg-brand-500 shadow-theme-xs hover:bg-brand-600">
                <svg class="fill-current" width="16" height="16" viewBox="0 0 20 20" fill="none"
                  xmlns="http://www.w3.org/2000/svg">
                  <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M10 3a1 1 0 00-1 1v5H4a1 1 0 100 2h5v5a1 1 0 102 0v-5h5a1 1 0 100-2h-5V4a1 1 0 00-1-1z"
                    fill="" />
                </svg>
                Tambah Jenis
              </button>
            </div>
            <div class="p-4 sm:p-6 xl:p-7.5">
              <!-- DataTables Controls Container - Fixed -->
              <div id="tableControls" class="mb-2">
                <!-- Length Menu dan Search akan di-inject oleh DataTables di sini -->
              </div>

              <!-- Table Container dengan Horizontal Scroll -->
              <div class="table-wrapper overflow-x-auto">
                <table id="jenisTable" class="w-full table-auto border-collapse text-left">
                  <thead>
                    <tr class="bg-gray-2 dark:bg-meta-4">
                      <th class="px-4 py-3 font-medium text-black dark:text-white">ID Jenis</th>
                      <th class="px-4 py-3 font-medium text-black dark:text-white">Nama Jenis</th>
                      <th class="px-4 py-3 font-medium text-black dark:text-white">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    if (!empty($data_jenis)) {
                      foreach ($data_jenis as $row) {
                        $format_id = "J" . sprintf("%04d", $row['id_jenis']);
                        ?>
                        <tr class="border-b border-[#eee] dark:border-strokedark">
                          <td class="px-4 py-3">
                            <p class="text-black dark:text-white font-medium"><?php echo $format_id; ?></p>
                          </td>
                          <td class="px-4 py-3">
                            <p class="text-sm text-black dark:text-white">
                              <?php echo htmlspecialchars($row['nama_jenis']); ?>
                            </p>
                          </td>
                          <td class="px-4 py-3">
                            <div class="flex gap-2">
                              <button
                                onclick="showEditForm(<?php echo $row['id_jenis']; ?>, '<?php echo addslashes($row['nama_jenis']); ?>')"
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
                                onclick="showDeleteConfirmation(<?php echo $row['id_jenis']; ?>, '<?php echo addslashes($row['nama_jenis']); ?>')"
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
                          </td>
                        </tr>
                        <?php
                      }
                    } else {
                      echo "<tr><td colspan='3' class='text-center py-4'>Tidak ada data jenis</td></tr>";
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

  <!-- Modal Tambah Jenis -->
  <div id="modalTambahJenis"
    class="modal-container fixed inset-0 hidden items-center justify-center transition-all duration-300 z-9999">
    <div class="absolute inset-0 bg-black/20" onclick="hideAllModals()"></div>
    <div
      class="modal-content bg-white dark:bg-boxdark w-full max-w-md mx-4 rounded-xl shadow-xl relative flex flex-col overflow-hidden max-h-[85vh]">
      <div class="sticky top-0 z-10 bg-white dark:bg-boxdark border-b border-stroke dark:border-strokedark px-6 py-4">
        <div class="flex items-center justify-between">
          <div>
            <h5 class="text-lg font-bold text-black dark:text-white">Tambah Jenis Baru</h5>
            <p class="text-xs text-gray-500">Isi form untuk menambah data jenis pekerjaan.</p>
          </div>
          <button onclick="hideAllModals()" class="text-gray-400 hover:text-red-500 transition-colors">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M18 6L6 18M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
      <div class="p-6 overflow-y-auto flex-1">
        <form id="formTambahJenis" action="" method="POST" class="space-y-4">
          <input type="hidden" name="action" value="tambah">
          <div>
            <label class="block text-sm font-medium mb-1 dark:text-white">Nama Jenis *</label>
            <input type="text" name="nama_jenis" required
              class="w-full rounded-lg border border-stroke bg-transparent px-3 py-2 text-sm outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4">
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

  <!-- Modal Edit Jenis -->
  <div id="modalEditJenis"
    class="modal-container fixed inset-0 hidden items-center justify-center transition-all duration-300 z-9999">
    <div class="absolute inset-0 bg-black/20" onclick="hideAllModals()"></div>
    <div
      class="modal-content bg-white dark:bg-boxdark w-full max-w-md mx-4 rounded-xl shadow-xl relative flex flex-col overflow-hidden max-h-[85vh]">
      <div class="sticky top-0 z-10 bg-white dark:bg-boxdark border-b border-stroke dark:border-strokedark px-6 py-4">
        <div class="flex items-center justify-between">
          <div>
            <h5 class="text-lg font-bold text-black dark:text-white">Edit Jenis</h5>
            <p class="text-xs text-gray-500">Edit data jenis pekerjaan.</p>
          </div>
          <button onclick="hideAllModals()" class="text-gray-400 hover:text-red-500 transition-colors">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M18 6L6 18M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
      <div class="p-6 overflow-y-auto flex-1">
        <form id="formEditJenis" action="" method="POST" class="space-y-4">
          <input type="hidden" name="action" value="edit">
          <input type="hidden" name="id_jenis" id="edit_id_jenis">
          <div>
            <label class="block text-sm font-medium mb-1 dark:text-white">Nama Jenis *</label>
            <input type="text" name="nama_jenis" id="edit_nama_jenis" required
              class="w-full rounded-lg border border-stroke bg-transparent px-3 py-2 text-sm outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4">
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

  <!-- Modal Konfirmasi Hapus -->
  <div id="modalHapusJenis" class="fixed inset-0 hidden items-center justify-center transition-all duration-300 z-9999">
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
          <p class="text-sm text-gray-600 dark:text-gray-400" id="hapusMessageJenis">
            Apakah Anda yakin ingin menghapus data ini?
          </p>
        </div>
        <div class="flex justify-center gap-2">
          <button type="button" onclick="hideAllModals()"
            class="px-4 py-2 text-xs font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
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

  <!-- Alert Container -->
  <div id="alertContainer" class="alert-container"></div>

  <!-- ===== Page Wrapper End ===== -->
  <script defer src="bundle.js"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
  <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
  <script src="https://cdn.datatables.net/2.0.8/js/dataTables.tailwindcss.js"></script>

  <script>
    $(document).ready(function () {
      $('#jenisTable').DataTable({
        "dom": '<"flex flex-wrap items-center justify-between gap-3 mb-2"lf>rt<"flex flex-wrap items-center justify-between gap-3 mt-2"ip>',
        "language": {
          "search": "",
          "searchPlaceholder": "Cari jenis...",
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
          var $wrapper = $('#jenisTable_wrapper');

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

          // PERBAIKAN INI: Pastikan menggunakan class v2
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
      const modals = ['modalTambahJenis', 'modalEditJenis', 'modalHapusJenis'];
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

    function showEditForm(id, nama) {
      document.getElementById('edit_id_jenis').value = id;
      document.getElementById('edit_nama_jenis').value = nama;
      toggleModal('modalEditJenis');
    }

    function showDeleteConfirmation(id, nama) {
      const message = document.getElementById('hapusMessageJenis');
      message.textContent = `Apakah Anda yakin ingin menghapus jenis "${nama}"?`;

      const deleteLink = document.getElementById('confirmDeleteLink');
      deleteLink.onclick = async function () {
        const originalText = deleteLink.innerHTML;
        deleteLink.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Menghapus...';
        deleteLink.disabled = true;
        
        try {
          const response = await fetch(`datajenis.php?action=hapus&id=${id}`, {
            method: 'GET'
          });
          
          const result = await response.json();
          
          if (result.success) {
            showNotification('success', 'Data jenis berhasil dihapus!', 'Hapus Berhasil');
            toggleModal('modalHapusJenis', false);
            // Auto refresh setelah 2 detik
            setTimeout(() => {
              location.reload();
            }, 2000);
          } else {
            showNotification('error', result.message || 'Gagal menghapus data jenis', 'Hapus Gagal');
          }
        } catch (error) {
          console.error('Error:', error);
          showNotification('error', 'Terjadi kesalahan saat menghapus data', 'Error Server');
        } finally {
          deleteLink.innerHTML = originalText;
          deleteLink.disabled = false;
        }
      };

      toggleModal('modalHapusJenis');
    }

    // ========== AJAX FORM SUBMIT HANDLERS ==========
    // Handle form tambah jenis dengan AJAX
    document.getElementById('formTambahJenis').addEventListener('submit', async function(e) {
      e.preventDefault();
      
      const formData = new FormData(this);
      const submitBtn = this.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;
      
      // Tampilkan loading
      submitBtn.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Menyimpan...';
      submitBtn.disabled = true;
      
      try {
        const response = await fetch('datajenis.php', {
          method: 'POST',
          body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
          showNotification('success', 'Data jenis berhasil ditambahkan!', 'Tambah Berhasil');
          toggleModal('modalTambahJenis', false);
          this.reset();
          // Auto refresh setelah 2 detik
          setTimeout(() => {
            location.reload();
          }, 2000);
        } else {
          showNotification('error', result.message || 'Gagal menambahkan data jenis', 'Tambah Gagal');
        }
      } catch (error) {
        console.error('Error:', error);
        showNotification('error', 'Terjadi kesalahan saat menambahkan data', 'Error Server');
      } finally {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
      }
    });
    
    // Handle form edit jenis dengan AJAX  
    document.getElementById('formEditJenis').addEventListener('submit', async function(e) {
      e.preventDefault();
      
      const formData = new FormData(this);
      const submitBtn = this.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;
      
      // Tampilkan loading
      submitBtn.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memperbarui...';
      submitBtn.disabled = true;
      
      try {
        const response = await fetch('datajenis.php', {
          method: 'POST',
          body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
          showNotification('success', 'Data jenis berhasil diperbarui!', 'Edit Berhasil');
          toggleModal('modalEditJenis', false);
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
            <path fill-rule="evenodd" clip-rule="evenodd" d="M3.6501 12.0004C3.6501 7.38852 7.38852 3.6501 12.0001 3.6501C16.6117 3.6501 20.3501 7.38852 20.3501 12.0001C20.3501 16.6117 16.6117 20.3501 12.0001 20.3501C7.38852 20.3501 3.6501 16.6117 3.6501 12.0001ZM12.0001 1.8501C6.39441 1.8501 1.8501 6.39441 1.8501 12.0001C1.8501 17.6058 6.39441 22.1501 12.0001 22.1501C17.6058 22.1501 22.1501 17.6058 22.1501 12.0001C22.1501 6.39441 17.6058 1.8501 12.0001 1.8501ZM10.9992 7.52517C10.9992 8.07746 11.4469 8.52517 11.9992 8.52517H12.0002C12.5525 8.52517 13.0002 8.07697 13.0002 7.52517C13.0002 6.97289 12.5525 6.52517 12.0002 6.52517H11.9992C11.4469 6.52517 10.9992 6.97289 10.9992 7.52517ZM12.0002 17.3715C11.586 17.3715 11.2502 17.0357 11.2502 16.6215V10.945C11.2502 10.5308 11.586 10.195 12.0002 10.195C12.4144 10.195 12.7502 10.5303 12.7502 10.945V16.6215C12.7502 17.0357 12.4144 17.3715 12.0002 17.3715Z" fill="" />
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
  </script>
</body>

</html>