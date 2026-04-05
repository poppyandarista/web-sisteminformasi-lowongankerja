<?php
// perusahaan/lowongan.php - Halaman Manajemen Lowongan
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['company_id']) || !isset($_SESSION['company_logged_in']) || $_SESSION['company_logged_in'] !== true) {
    // Redirect ke halaman login dengan pesan
    header('Location: login.php?message=Silakan login terlebih dahulu');
    exit();
}

// Cache control headers untuk mencegah back button setelah logout
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

require_once 'koneksi_perusahaan.php';

// Base URL untuk akses gambar dari admin panel
define('BASE_URL', 'http://localhost/web-linkup-loker/');
define('ADMIN_IMG_URL', BASE_URL . 'adminpanel/src/images/jobs/');

$company_id = $_SESSION['company_id'];
$lowongan_list = $db->getLowonganByPerusahaan($company_id);
$provinsi_list = $db->getAllProvinsi();
$kategori_list = $db->getAllKategori();
$jenis_list = $db->getAllJenis();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lowongan Saya | LinkUp Perusahaan</title>
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <style>
        /* ========== SAME STYLES AS BEFORE (keep all CSS) ========== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f8fafc;
            color: #0f172a;
        }

        .app-container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 280px;
            background: #ffffff;
            color: #475569;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            z-index: 100;
            transition: all 0.3s ease;
            box-shadow: 1px 0 3px rgba(0, 0, 0, 0.1);
            border-right: 1px solid #e2e8f0;
        }

        .sidebar-logo {
            padding: 28px 24px;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 24px;
        }

        .sidebar-logo h2 {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #fff, #60a5fa);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .sidebar-logo h2 i {
            margin-right: 10px;
            background: none;
            -webkit-background-clip: unset;
            background-clip: unset;
            color: #3b82f6;
        }

        .sidebar-nav {
            list-style: none;
            padding: 0 16px;
        }

        .sidebar-nav li {
            margin: 4px 0;
        }

        .sidebar-nav li a {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 18px;
            color: #64748b;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .sidebar-nav li a i {
            width: 22px;
            font-size: 1.1rem;
        }

        .sidebar-nav li.active a,
        .sidebar-nav li a:hover {
            background: #f1f5f9;
            color: #2563eb;
        }

        .main-wrapper {
            flex: 1;
            margin-left: 280px;
            min-height: 100vh;
        }

        .top-navbar {
            background: white;
            padding: 14px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e2e8f0;
            position: sticky;
            top: 0;
            z-index: 99;
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }

        .menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #1e293b;
        }

        .company-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .company-name {
            font-weight: 600;
            color: #0f172a;
            font-size: 0.9rem;
        }

        .avatar-dropdown {
            position: relative;
            cursor: pointer;
        }

        .avatar-icon {
            width: 38px;
            height: 38px;
            background: #eef2ff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: #2563eb;
            transition: all 0.2s;
        }

        .dropdown-menu {
            position: absolute;
            right: 0;
            top: 50px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            width: 180px;
            display: none;
            z-index: 200;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }

        .dropdown-menu a {
            display: block;
            padding: 10px 20px;
            color: #1e293b;
            text-decoration: none;
            transition: 0.2s;
            font-size: 0.85rem;
        }

        .dropdown-menu a:hover {
            background: #f8fafc;
        }

        .main-content {
            padding: 28px 32px;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }

        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .header-left h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 6px;
            letter-spacing: -0.02em;
        }

        .header-left p {
            color: #64748b;
            font-size: 0.875rem;
        }

        .btn-primary {
            background: #2563eb;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-family: 'Poppins', sans-serif;
        }

        .btn-primary:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: white;
            border: 1px solid #e2e8f0;
            padding: 8px 16px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 500;
            transition: 0.2s;
            font-size: 0.85rem;
            color: #475569;
            font-family: 'Poppins', sans-serif;
        }

        .btn-secondary:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        .card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }

        .card-body {
            padding: 0;
        }

        .modern-table {
            width: 100%;
            border-collapse: collapse;
        }

        .modern-table thead tr {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        .modern-table th {
            padding: 14px 20px;
            text-align: left;
            font-size: 0.7rem;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .modern-table td {
            padding: 16px 20px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.85rem;
            vertical-align: middle;
        }

        .modern-table tbody tr:hover {
            background: #fafcff;
        }

        .job-info-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .job-image {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            object-fit: cover;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
        }

        .job-image-placeholder {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: linear-gradient(135deg, #eef2ff, #e0e7ff);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #4f46e5;
            font-size: 1.2rem;
        }

        .job-details {
            flex: 1;
        }

        .job-title {
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
            font-size: 0.9rem;
        }

        .job-category {
            font-size: 0.7rem;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .job-category i {
            font-size: 0.6rem;
            color: #94a3b8;
        }

        .location-cell i {
            color: #ef4444;
            font-size: 0.7rem;
            margin-right: 6px;
        }

        .salary-cell {
            font-weight: 600;
            color: #059669;
        }

        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            display: inline-block;
        }

        .badge-active {
            background: #dcfce7;
            color: #15803d;
        }

        .badge-nonactive {
            background: #f1f5f9;
            color: #475569;
        }

        .action-buttons {
            display: flex;
            gap: 6px;
        }

        .btn-icon {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            font-family: 'Poppins', sans-serif;
        }

        .btn-edit {
            background: #eef2ff;
            color: #4f46e5;
        }

        .btn-edit:hover {
            background: #4f46e5;
            color: white;
        }

        .btn-delete {
            background: #fef2f2;
            color: #ef4444;
        }

        .btn-delete:hover {
            background: #ef4444;
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 60px 40px;
        }

        .empty-state-icon {
            width: 70px;
            height: 70px;
            background: #f1f5f9;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .empty-state-icon i {
            font-size: 2rem;
            color: #94a3b8;
        }

        .empty-state h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 6px;
        }

        .empty-state p {
            font-size: 0.85rem;
            color: #64748b;
            margin-bottom: 20px;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .modal.show {
            display: flex !important;
        }

        .modal-content {
            background: white;
            border-radius: 20px;
            max-width: 900px;
            width: 90%;
            max-height: 85vh;
            overflow-y: auto;
            position: relative;
            margin: auto;
            animation: modalFadeIn 0.25s ease;
        }

        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: scale(0.96) translateY(-10px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .modal-content.modal-lg {
            max-width: 900px;
        }

        .modal-header {
            padding: 18px 24px;
            border-bottom: 1px solid #eef2ff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            border-radius: 20px 20px 0 0;
        }

        .modal-header h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #0f172a;
            margin: 0;
        }

        .modal-header h3 i {
            color: #2563eb;
            margin-right: 8px;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.3rem;
            cursor: pointer;
            color: #94a3b8;
            transition: color 0.2s;
        }

        .modal-close:hover {
            color: #ef4444;
        }

        .modal-footer {
            padding: 16px 24px;
            border-top: 1px solid #eef2ff;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            background: #fafcfc;
            border-radius: 0 0 20px 20px;
        }

        .form-two-columns {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            padding: 24px;
        }

        .form-group {
            margin-bottom: 0;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            font-size: 0.75rem;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 0.85rem;
            font-family: 'Poppins', sans-serif;
            transition: all 0.2s;
            background: white;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.08);
        }

        .full-width {
            grid-column: span 2;
        }

        .image-preview {
            margin-top: 10px;
        }

        .image-preview img {
            max-width: 100px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
        }

        .current-image-info {
            margin-top: 8px;
            font-size: 0.7rem;
            color: #64748b;
        }

        /* Alert Container */
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
            background: white;
            border-radius: 12px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            animation: slideIn 0.3s ease;
            border-left: 4px solid;
        }

        .alert-item.success {
            border-left-color: #10b981;
            background: #f0fdf4;
        }

        .alert-item.error {
            border-left-color: #ef4444;
            background: #fef2f2;
        }

        .alert-item i {
            font-size: 1.2rem;
        }

        .alert-item.success i {
            color: #10b981;
        }

        .alert-item.error i {
            color: #ef4444;
        }

        .alert-content {
            flex: 1;
        }

        .alert-title {
            font-weight: 600;
            font-size: 0.85rem;
            margin-bottom: 2px;
        }

        .alert-message {
            font-size: 0.75rem;
            color: #475569;
        }

        .alert-close {
            background: none;
            border: none;
            cursor: pointer;
            color: #94a3b8;
            font-size: 1rem;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            padding: 14px 20px;
        }

        .dataTables_wrapper .dataTables_filter input {
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: 7px 12px;
            margin-left: 8px;
            font-size: 0.8rem;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 5px 10px;
            border-radius: 8px;
        }

        @media (max-width: 768px) {
            .sidebar {
                left: -280px;
            }

            .sidebar.active {
                left: 0;
            }

            .main-wrapper {
                margin-left: 0;
            }

            .menu-toggle {
                display: block;
            }

            .main-content {
                padding: 20px;
            }

            .content-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .form-two-columns {
                grid-template-columns: 1fr;
                gap: 14px;
                padding: 20px;
            }

            .full-width {
                grid-column: span 1;
            }

            .modern-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }

            .job-info-cell {
                min-width: 220px;
            }
        }
    </style>
</head>

<body>

    <div class="app-container">
        <?php include 'includes/sidebar.php'; ?>
        <div class="main-wrapper">
            <?php include 'includes/navbar.php'; ?>
            <main class="main-content">
                <div class="content-header">
                    <div class="header-left">
                        <h1>Lowongan Saya</h1>
                        <p>Kelola dan pantau semua lowongan pekerjaan perusahaan Anda</p>
                    </div>
                    <div style="display: flex; gap: 12px;">
                        <button class="btn-secondary" id="btnExportLowongan"
                            style="display: inline-flex; align-items: center; gap: 8px;">
                            <i class="fas fa-download"></i> Export Data
                        </button>
                        <button class="btn-primary" id="btnTambahLowongan">
                            <i class="fas fa-plus"></i> Buat Lowongan Baru
                        </button>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <?php if (count($lowongan_list) > 0): ?>
                            <table class="modern-table" id="lowonganTable">
                                <thead>
                                    <tr>
                                        <th>Informasi Lowongan</th>
                                        <th>Lokasi</th>
                                        <th>Gaji</th>
                                        <th>Tanggal Posting</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($lowongan_list as $low):
                                        $has_gambar = !empty($low['gambar']);
                                        $gambar_url = $has_gambar ? ADMIN_IMG_URL . $low['gambar'] : '';
                                        $gambar_path_server = '../adminpanel/src/images/jobs/' . $low['gambar'];
                                        $gambar_exists = $has_gambar && file_exists($gambar_path_server);
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="job-info-cell">
                                                    <?php if ($gambar_exists): ?>
                                                        <img src="<?php echo $gambar_url; ?>" class="job-image"
                                                            alt="<?php echo htmlspecialchars($low['judul_lowongan']); ?>"
                                                            onerror="this.onerror=null; this.style.display='none'; this.parentElement.querySelector('.job-image-placeholder').style.display='flex';">
                                                        <div class="job-image-placeholder" style="display: none;">
                                                            <i class="fas fa-briefcase"></i>
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="job-image-placeholder">
                                                            <i class="fas fa-briefcase"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div class="job-details">
                                                        <div class="job-title">
                                                            <?php echo htmlspecialchars($low['judul_lowongan']); ?>
                                                        </div>
                                                        <div class="job-category">
                                                            <i class="fas fa-tag"></i>
                                                            <?php
                                                            $kategori_nama = '';
                                                            foreach ($kategori_list as $kat) {
                                                                if ($kat['id_kategori'] == $low['kategori_lowongan']) {
                                                                    $kategori_nama = $kat['nama_kategori'];
                                                                    break;
                                                                }
                                                            }
                                                            echo htmlspecialchars($kategori_nama ?: 'Tidak ada kategori');
                                                            ?>
                                                        </div>
                                                    </div>
                                                </div>
                            </div>
                            <td>
                                <div class="location-cell">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?php echo htmlspecialchars($low['lokasi_lowongan'] ?? $low['nama_kota'] ?? '-'); ?>
                                </div>
                        </div>
                        <td>
                            <span class="salary-cell">
                                Rp <?php echo number_format($low['gaji_lowongan'] ?? 0, 0, ',', '.'); ?>
                            </span>
                </div>
                <td>
                    <i class="far fa-calendar-alt" style="color: #94a3b8; font-size: 0.7rem; margin-right: 6px;"></i>
                    <?php echo date('d/m/Y', strtotime($low['tanggal_posting'])); ?>
            </div>
            <td>
                <span class="badge <?php echo $low['status'] == 'Aktif' ? 'badge-active' : 'badge-nonactive'; ?>">
                    <?php echo $low['status']; ?>
                </span>
                </div>
            <td>
                <div class="action-buttons">
                    <button class="btn-icon btn-edit editLowongan" data-id="<?php echo $low['id_lowongan']; ?>" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn-icon btn-delete deleteLowongan" data-id="<?php echo $low['id_lowongan']; ?>"
                        title="Hapus">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                </div>
                </tr>
            <?php endforeach; ?>
            </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-briefcase"></i>
                </div>
                <h3>Belum Ada Lowongan</h3>
                <p>Mulai buat lowongan pertama Anda untuk mencari kandidat terbaik</p>
                <button class="btn-primary" id="btnTambahLowonganEmpty">
                    <i class="fas fa-plus"></i> Tambah Lowongan Pertama
                </button>
            </div>
        <?php endif; ?>
        </div>
        </div>
        </main>
        </div>
        </div>

        <!-- Alert Container -->
        <div id="alertContainer" class="alert-container"></div>

        <!-- Modal Tambah/Edit Lowongan -->
        <div id="lowonganModal" class="modal">
            <div class="modal-content modal-lg">
                <div class="modal-header">
                    <h3 id="modalTitle"><i class="fas fa-plus-circle"></i> Tambah Lowongan</h3>
                    <button class="modal-close">&times;</button>
                </div>
                <form id="lowonganForm" enctype="multipart/form-data">
                    <input type="hidden" id="lowonganId" name="lowongan_id">
                    <div class="form-two-columns">
                        <div class="form-group">
                            <label>Judul Lowongan *</label>
                            <input type="text" id="judul" name="judul" required
                                placeholder="Contoh: Frontend Developer">
                        </div>
                        <div class="form-group">
                            <label>Kategori</label>
                            <select id="kategori" name="kategori">
                                <option value="">Pilih Kategori</option>
                                <?php foreach ($kategori_list as $kat): ?>
                                    <option value="<?php echo $kat['id_kategori']; ?>">
                                        <?php echo htmlspecialchars($kat['nama_kategori']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Jenis Pekerjaan</label>
                            <select id="jenis" name="jenis">
                                <option value="">Pilih Jenis</option>
                                <?php foreach ($jenis_list as $jenis): ?>
                                    <option value="<?php echo $jenis['id_jenis']; ?>">
                                        <?php echo htmlspecialchars($jenis['nama_jenis']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Provinsi</label>
                            <select id="id_provinsi" name="id_provinsi">
                                <option value="">Pilih Provinsi</option>
                                <?php foreach ($provinsi_list as $prov): ?>
                                    <option value="<?php echo $prov['id_provinsi']; ?>">
                                        <?php echo htmlspecialchars($prov['nama_provinsi']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Kota</label>
                            <select id="id_kota" name="id_kota">
                                <option value="">Pilih Kota</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Lokasi Lengkap</label>
                            <input type="text" id="lokasi" name="lokasi" placeholder="Alamat kantor">
                        </div>
                        <div class="form-group">
                            <label>Gaji</label>
                            <input type="number" id="gaji" name="gaji" placeholder="Contoh: 8000000">
                        </div>
                        <div class="form-group">
                            <label>Tanggal Tutup</label>
                            <input type="date" id="tgl_tutup" name="tgl_tutup">
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select id="status" name="status">
                                <option value="Aktif">Aktif</option>
                                <option value="Nonaktif">Nonaktif</option>
                            </select>
                        </div>
                        <div class="form-group full-width">
                            <label>Kualifikasi</label>
                            <textarea id="kualifikasi" name="kualifikasi" rows="3"
                                placeholder="Persyaratan dan kualifikasi..."></textarea>
                        </div>
                        <div class="form-group full-width">
                            <label>Deskripsi Pekerjaan</label>
                            <textarea id="deskripsi" name="deskripsi" rows="4"
                                placeholder="Deskripsi lengkap lowongan..."></textarea>
                        </div>
                        <div class="form-group full-width">
                            <label>Upload Gambar/Logo</label>
                            <input type="file" id="gambar" name="gambar" accept="image/*">
                            <div id="gambarPreview" class="image-preview"></div>
                            <div id="currentGambarInfo" class="current-image-info"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-secondary modal-cancel">Batal</button>
                        <button type="submit" class="btn-primary">Simpan Lowongan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Export Filter -->
        <div id="exportModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3><i class="fas fa-download"></i> Export Data Lowongan</h3>
                    <button class="modal-close">&times;</button>
                </div>
                <div class="form-two-columns">
                    <div class="form-group">
                        <label>Filter Provinsi</label>
                        <select id="filterProvinsi" name="filterProvinsi">
                            <option value="">Semua Provinsi</option>
                            <?php foreach ($provinsi_list as $prov): ?>
                                <option value="<?php echo $prov['id_provinsi']; ?>">
                                    <?php echo htmlspecialchars($prov['nama_provinsi']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Filter Kota</label>
                        <select id="filterKota" name="filterKota">
                            <option value="">Semua Kota</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Filter Kategori</label>
                        <select id="filterKategori" name="filterKategori">
                            <option value="">Semua Kategori</option>
                            <?php foreach ($kategori_list as $kat): ?>
                                <option value="<?php echo $kat['id_kategori']; ?>">
                                    <?php echo htmlspecialchars($kat['nama_kategori']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Filter Jenis Pekerjaan</label>
                        <select id="filterJenis" name="filterJenis">
                            <option value="">Semua Jenis</option>
                            <?php foreach ($jenis_list as $jenis): ?>
                                <option value="<?php echo $jenis['id_jenis']; ?>">
                                    <?php echo htmlspecialchars($jenis['nama_jenis']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Filter Gaji Minimal</label>
                        <input type="number" id="filterGajiMin" name="filterGajiMin" placeholder="Contoh: 5000000">
                    </div>
                    <div class="form-group">
                        <label>Filter Gaji Maksimal</label>
                        <input type="number" id="filterGajiMax" name="filterGajiMax" placeholder="Contoh: 20000000">
                    </div>
                    <div class="form-group">
                        <label>Filter Status</label>
                        <select id="filterStatus" name="filterStatus">
                            <option value="">Semua Status</option>
                            <option value="Aktif">Aktif</option>
                            <option value="Nonaktif">Nonaktif</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Filter Tanggal Posting</label>
                        <input type="date" id="filterTanggalPosting" name="filterTanggalPosting">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary modal-cancel">Batal</button>
                    <button type="button" class="btn-secondary" id="btnResetFilter">
                        <i class="fas fa-redo"></i> Reset Filter
                    </button>
                    <button type="button" class="btn-primary" id="btnExportCSV">
                        <i class="fas fa-file-csv"></i> Export CSV
                    </button>
                    <button type="button" class="btn-primary" id="btnExportXLS">
                        <i class="fas fa-file-excel"></i> Export XLS
                    </button>
                    <button type="button" class="btn-primary" id="btnExportPDF">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                </div>
            </div>
        </div>

        <script>
            // Base URL untuk akses gambar dari admin panel
            var ADMIN_IMG_URL = '<?php echo ADMIN_IMG_URL; ?>';

            // Fungsi untuk mendapatkan path gambar yang benar
            function getImagePath(gambarName) {
                if (!gambarName) return null;
                return ADMIN_IMG_URL + gambarName;
            }

            // Notification function (sama seperti di lamaran)
            function showNotification(type, message, title) {
                if ($('#alertContainer').length === 0) {
                    $('body').append('<div id="alertContainer" style="position: fixed; top: 20px; right: 20px; z-index: 9999; display: flex; flex-direction: column; gap: 10px; max-width: 400px;"></div>');
                }

                var icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
                var bgColor = type === 'success' ? '#dcfce7' : '#fee2e2';
                var textColor = type === 'success' ? '#15803d' : '#dc2626';

                var alertHtml = `
                <div class="alert-item ${type}">
                    <i class="fas ${icon}" style="color: ${textColor}; font-size: 1.2rem;"></i>
                    <div class="alert-content">
                        <div class="alert-title" style="color: ${textColor};">${title}</div>
                        <div class="alert-message">${message}</div>
                    </div>
                    <button class="alert-close" onclick="$(this).closest('.alert-item').remove()">&times;</button>
                </div>
            `;

                $('#alertContainer').append(alertHtml);

                setTimeout(function () {
                    $('#alertContainer .alert-item:first-child').fadeOut(300, function () {
                        $(this).remove();
                    });
                }, 4000);
            }

            // Load kota berdasarkan provinsi
            $('#id_provinsi').on('change', function () {
                var provinsiId = $(this).val();
                if (provinsiId) {
                    $.ajax({
                        url: 'ajax_get_kota.php',
                        type: 'GET',
                        data: { id_provinsi: provinsiId },
                        dataType: 'json',
                        success: function (data) {
                            $('#id_kota').empty().append('<option value="">Pilih Kota</option>');
                            $.each(data, function (i, kota) {
                                $('#id_kota').append('<option value="' + kota.id_kota + '">' + kota.nama_kota + '</option>');
                            });
                        }
                    });
                } else {
                    $('#id_kota').empty().append('<option value="">Pilih Kota</option>');
                }
            });

            // Empty state button
            $('#btnTambahLowonganEmpty').on('click', function () {
                $('#btnTambahLowongan').click();
            });

            // Modal functions
            function openModal() {
                $('#lowonganModal').addClass('show');
                $('body').css('overflow', 'hidden');
            }

            function closeModal() {
                $('#lowonganModal').removeClass('show');
                $('body').css('overflow', '');
            }

            // Tombol tambah lowongan
            $('#btnTambahLowongan').click(function () {
                $('#modalTitle').html('<i class="fas fa-plus-circle"></i> Tambah Lowongan');
                $('#lowonganForm')[0].reset();
                $('#lowonganId').val('');
                $('#gambarPreview').html('');
                $('#currentGambarInfo').html('');
                openModal();
            });

            // Tombol edit
            $(document).on('click', '.editLowongan', function () {
                var id = $(this).data('id');
                $.ajax({
                    url: 'ajax_lowongan.php',
                    type: 'GET',
                    data: { action: 'get_detail', id: id },
                    dataType: 'json',
                    success: function (res) {
                        if (res.success) {
                            var data = res.data;
                            $('#modalTitle').html('<i class="fas fa-edit"></i> Edit Lowongan');
                            $('#lowonganId').val(data.id_lowongan);
                            $('#judul').val(data.judul_lowongan);
                            $('#kategori').val(data.kategori_lowongan);
                            $('#jenis').val(data.id_jenis);
                            $('#id_provinsi').val(data.id_provinsi).trigger('change');
                            setTimeout(function () {
                                $('#id_kota').val(data.id_kota);
                            }, 500);
                            $('#lokasi').val(data.lokasi_lowongan);
                            $('#gaji').val(data.gaji_lowongan);
                            $('#tgl_tutup').val(data.tanggal_tutup);
                            $('#status').val(data.status);
                            $('#kualifikasi').val(data.kualifikasi);
                            $('#deskripsi').val(data.deskripsi_lowongan);

                            if (data.gambar) {
                                var gambarUrl = getImagePath(data.gambar);
                                $('#gambarPreview').html('<img src="' + gambarUrl + '" width="100">');
                                $('#currentGambarInfo').html('<small>Gambar saat ini: ' + data.gambar + '</small>');
                            } else {
                                $('#gambarPreview').html('');
                                $('#currentGambarInfo').html('');
                            }
                            openModal();
                        } else {
                            showNotification('error', res.message, 'Gagal');
                        }
                    },
                    error: function () {
                        showNotification('error', 'Terjadi kesalahan saat memuat data', 'Error');
                    }
                });
            });

            // Tombol hapus
            $(document).on('click', '.deleteLowongan', function () {
                if (confirm('Yakin ingin menghapus lowongan ini?')) {
                    var id = $(this).data('id');
                    $.ajax({
                        url: 'ajax_lowongan.php',
                        type: 'POST',
                        data: { action: 'delete', id: id },
                        dataType: 'json',
                        success: function (res) {
                            if (res.success) {
                                showNotification('success', res.message, 'Berhasil');
                                setTimeout(function () {
                                    location.reload();
                                }, 1500);
                            } else {
                                showNotification('error', res.message, 'Gagal');
                            }
                        },
                        error: function () {
                            showNotification('error', 'Terjadi kesalahan saat menghapus data', 'Error');
                        }
                    });
                }
            });

            // Submit form
            $('#lowonganForm').submit(function (e) {
                e.preventDefault();
                var formData = new FormData(this);
                formData.append('action', 'save');

                // Disable button
                var submitBtn = $(this).find('button[type="submit"]');
                var originalText = submitBtn.html();
                submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...').prop('disabled', true);

                $.ajax({
                    url: 'ajax_lowongan.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function (res) {
                        if (res.success) {
                            showNotification('success', res.message, 'Berhasil');
                            setTimeout(function () {
                                location.reload();
                            }, 1500);
                        } else {
                            showNotification('error', res.message, 'Gagal');
                            submitBtn.html(originalText).prop('disabled', false);
                        }
                    },
                    error: function () {
                        showNotification('error', 'Terjadi kesalahan saat menyimpan data', 'Error');
                        submitBtn.html(originalText).prop('disabled', false);
                    }
                });
            });

            // Close modal
            $('.modal-close, .modal-cancel').click(closeModal);

            $(window).click(function (e) {
                if ($(e.target).hasClass('modal')) {
                    closeModal();
                }
            });

            // Preview gambar
            $('#gambar').change(function (e) {
                var file = e.target.files[0];
                if (file) {
                    var reader = new FileReader();
                    reader.onload = function (ev) {
                        $('#gambarPreview').html('<img src="' + ev.target.result + '" width="100">');
                    };
                    reader.readAsDataURL(file);
                } else {
                    var lowonganId = $('#lowonganId').val();
                    if (lowonganId) {
                        $.ajax({
                            url: 'ajax_lowongan.php',
                            type: 'GET',
                            data: { action: 'get_detail', id: lowonganId },
                            dataType: 'json',
                            success: function (res) {
                                if (res.success && res.data.gambar) {
                                    var gambarUrl = getImagePath(res.data.gambar);
                                    $('#gambarPreview').html('<img src="' + gambarUrl + '" width="100">');
                                } else {
                                    $('#gambarPreview').html('');
                                }
                            }
                        });
                    } else {
                        $('#gambarPreview').html('');
                    }
                }
            });

            // Export Modal Functions
            function openExportModal() {
                $('#exportModal').addClass('show');
                $('body').css('overflow', 'hidden');
            }

            function closeExportModal() {
                $('#exportModal').removeClass('show');
                $('body').css('overflow', '');
            }

            // Tombol export
            $('#btnExportLowongan').click(function () {
                openExportModal();
            });

            // Load kota untuk filter provinsi
            $('#filterProvinsi').on('change', function () {
                var provinsiId = $(this).val();
                if (provinsiId) {
                    $.ajax({
                        url: 'ajax_get_kota.php',
                        type: 'GET',
                        data: { id_provinsi: provinsiId },
                        dataType: 'json',
                        success: function (data) {
                            $('#filterKota').empty().append('<option value="">Semua Kota</option>');
                            $.each(data, function (i, kota) {
                                $('#filterKota').append('<option value="' + kota.id_kota + '">' + kota.nama_kota + '</option>');
                            });
                        }
                    });
                } else {
                    $('#filterKota').empty().append('<option value="">Semua Kota</option>');
                }
            });

            // Reset filter
            $('#btnResetFilter').click(function () {
                // Reset semua select dan input
                $('#exportModal select').each(function () {
                    $(this).val('');
                });
                $('#exportModal input[type="number"], #exportModal input[type="date"]').each(function () {
                    $(this).val('');
                });

                // Reset kota options
                $('#filterKota').empty().append('<option value="">Semua Kota</option>');

                // Trigger change untuk provinsi agar kota ter-reset
                $('#filterProvinsi').trigger('change');
            });

            // Close export modal
            $('.modal-cancel').click(function () {
                if ($(this).closest('#exportModal').length) {
                    closeExportModal();
                }
            });

            // Export functions
            function exportData(format) {
                var filters = {
                    provinsi: $('#filterProvinsi').val(),
                    kota: $('#filterKota').val(),
                    kategori: $('#filterKategori').val(),
                    jenis: $('#filterJenis').val(),
                    gaji_min: $('#filterGajiMin').val(),
                    gaji_max: $('#filterGajiMax').val(),
                    status: $('#filterStatus').val(),
                    tanggal_posting: $('#filterTanggalPosting').val(),
                    format: format
                };

                // Show loading
                var btnId = '#btnExport' + format.toUpperCase();
                var originalText = $(btnId).html();
                $(btnId).html('<i class="fas fa-spinner fa-spin"></i> Exporting...').prop('disabled', true);

                $.ajax({
                    url: 'ajax_export_lowongan.php',
                    type: 'POST',
                    data: filters,
                    dataType: 'json',
                    success: function (res) {
                        $(btnId).html(originalText).prop('disabled', false);

                        if (res.success) {
                            if (res.data_count > 0) {
                                // Download file using download handler
                                var downloadUrl = 'download.php?file=' + encodeURIComponent(res.download_url.replace('exports/', ''));
                                window.location.href = downloadUrl;
                                showNotification('success', `Berhasil export ${res.data_count} data lowongan`, 'Export Berhasil');
                                closeExportModal();
                            } else {
                                showNotification('error', 'Tidak ada data yang sesuai dengan filter yang dipilih', 'Tidak Ada Data');
                            }
                        } else {
                            showNotification('error', res.message || 'Gagal melakukan export', 'Export Gagal');
                        }
                    },
                    error: function () {
                        $(btnId).html(originalText).prop('disabled', false);
                        showNotification('error', 'Terjadi kesalahan saat export data', 'Error');
                    }
                });
            }

            // Export button handlers
            $('#btnExportCSV').click(function () {
                exportData('csv');
            });

            $('#btnExportXLS').click(function () {
                exportData('xls');
            });

            $('#btnExportPDF').click(function () {
                exportData('pdf');
            });

            // DataTable initialization
            $(document).ready(function () {
                if ($('#lowonganTable tbody tr').length > 0) {
                    $('#lowonganTable').DataTable({
                        language: {
                            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
                        },
                        pageLength: 10,
                        order: [[3, 'desc']]
                    });
                }
            });
        </script>
</body>

</html>