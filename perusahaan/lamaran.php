<?php
// perusahaan/lamaran.php - Halaman Lamaran Masuk
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

$company_id = $_SESSION['company_id'];
$lamaran_list = $db->getLamaranByPerusahaan($company_id);
$lowongan_list = $db->getLowonganByPerusahaan($company_id);

// Base URL untuk akses gambar dari admin panel
$base_url = 'http://localhost/web-linkup-loker/';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lamaran Masuk | LinkUp Perusahaan</title>
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <style>
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

        .page-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 6px;
            letter-spacing: -0.02em;
        }

        .page-subtitle {
            color: #64748b;
            font-size: 0.875rem;
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .filter-select {
            padding: 8px 16px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 0.85rem;
            font-family: 'Poppins', sans-serif;
            background: white;
            cursor: pointer;
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
            display: inline-flex;
            align-items: center;
            gap: 8px;
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

        .user-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #eef2ff, #e0e7ff);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #4f46e5;
            font-size: 1rem;
            font-weight: 600;
            overflow: hidden;
        }

        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .user-info {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 2px;
            font-size: 0.9rem;
        }

        .user-email {
            font-size: 0.7rem;
            color: #64748b;
        }

        .job-title {
            font-weight: 600;
            color: #0f172a;
            font-size: 0.85rem;
        }

        .job-title i {
            color: #3b82f6;
            font-size: 0.7rem;
            margin-right: 6px;
        }

        .date-cell {
            font-size: 0.8rem;
            color: #475569;
        }

        .date-cell i {
            color: #94a3b8;
            font-size: 0.7rem;
            margin-right: 6px;
        }

        .badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            display: inline-block;
        }

        .badge-diproses {
            background: #fef3c7;
            color: #d97706;
        }

        .badge-diterima {
            background: #dcfce7;
            color: #15803d;
        }

        .badge-ditolak {
            background: #fee2e2;
            color: #dc2626;
        }

        .catatan-cell {
            max-width: 200px;
            font-size: 0.75rem;
            color: #64748b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .catatan-cell i {
            color: #8b5cf6;
            margin-right: 6px;
        }

        .catatan-cell.has-catatan {
            color: #0f172a;
            font-weight: 500;
        }

        .action-buttons {
            display: flex;
            gap: 6px;
        }

        .btn-update {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 6px 14px;
            background: #eef2ff;
            border: none;
            border-radius: 8px;
            color: #4f46e5;
            font-size: 0.75rem;
            font-weight: 500;
            cursor: pointer;
        }

        .btn-update:hover {
            background: #4f46e5;
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
            border-radius: 24px;
            max-width: 600px;
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

        .modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid #eef2ff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            border-radius: 24px 24px 0 0;
            position: sticky;
            top: 0;
            z-index: 10;
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
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
        }

        .modal-close:hover {
            background: #f1f5f9;
            color: #ef4444;
        }

        .modal-footer {
            padding: 16px 24px;
            border-top: 1px solid #eef2ff;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            background: #fafcfc;
            border-radius: 0 0 24px 24px;
            flex-wrap: wrap;
        }

        /* Form Styles */
        .form-two-columns {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            padding: 24px;
        }

        .form-group {
            margin-bottom: 0;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 0.7rem;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-group label i {
            margin-right: 6px;
            color: #2563eb;
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

        .modal-info-card {
            background: #f8fafc;
            border-radius: 12px;
            padding: 12px 16px;
            margin: 0 24px 20px 24px;
            border: 1px solid #e2e8f0;
        }

        .modal-info-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 0;
        }

        .modal-info-icon {
            width: 32px;
            height: 32px;
            background: #eef2ff;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #4f46e5;
        }

        .modal-info-content {
            flex: 1;
        }

        .modal-info-label {
            font-size: 0.65rem;
            color: #64748b;
        }

        .modal-info-value {
            font-size: 0.85rem;
            font-weight: 600;
            color: #0f172a;
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

            .form-two-columns {
                grid-template-columns: 1fr;
                gap: 14px;
            }

            .modal-footer {
                flex-wrap: wrap;
            }

            .modern-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
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
                    <div>
                        <h1 class="page-title">Lamaran Masuk</h1>
                        <p class="page-subtitle">Kelola dan pantau semua lamaran yang masuk ke perusahaan Anda</p>
                    </div>
                    <div style="display: flex; gap: 12px;">
                        <select id="filterStatus" class="filter-select">
                            <option value="all">Semua Status</option>
                            <option value="Diproses">🟡 Diproses</option>
                            <option value="Diterima">🟢 Diterima</option>
                            <option value="Ditolak">🔴 Ditolak</option>
                        </select>
                        <button class="btn-secondary" id="btnExportLamaran">
                            <i class="fas fa-download"></i> Export Data
                        </button>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <?php if (count($lamaran_list) > 0): ?>
                            <table class="modern-table" id="lamaranTable">
                                <thead>
                                    <tr>
                                        <th>Informasi Pelamar</th>
                                        <th>Lowongan</th>
                                        <th>Tanggal Lamar</th>
                                        <th>Status</th>
                                        <th>Catatan HRD</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($lamaran_list as $lamaran):
                                        $nama = htmlspecialchars($lamaran['nama_user'] ?? $lamaran['username_user'] ?? '-');
                                        $initial = strtoupper(substr($nama, 0, 1));
                                        $foto_url = '';
                                        $has_foto = !empty($lamaran['foto_user']);
                                        if ($has_foto) {
                                            $foto_url = $base_url . 'adminpanel/src/images/user/' . $lamaran['foto_user'];
                                        }
                                        $has_catatan = !empty($lamaran['catatan_hrd']);
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="user-cell">
                                                    <?php if ($has_foto): ?>
                                                        <div class="user-avatar" style="background: none; padding: 0;">
                                                            <img src="<?php echo $foto_url; ?>"
                                                                style="width: 40px; height: 40px; object-fit: cover; border-radius: 12px;"
                                                                onerror="this.onerror=null; this.parentElement.style.background='linear-gradient(135deg, #eef2ff, #e0e7ff)'; this.parentElement.innerHTML='<?php echo $initial; ?>'; this.style.display='none';">
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="user-avatar"><?php echo $initial; ?></div>
                                                    <?php endif; ?>
                                                    <div class="user-info">
                                                        <div class="user-name"><?php echo $nama; ?></div>
                                                        <div class="user-email">
                                                            <?php echo htmlspecialchars($lamaran['email_user'] ?? '-'); ?>
                                                        </div>
                                                    </div>
                                                </div>
                            </div>
                            <td>
                                <div class="job-title">
                                    <i class="fas fa-briefcase"></i>
                                    <?php echo htmlspecialchars($lamaran['judul_lowongan']); ?>
                                </div>
                        </div>
                        <td>
                            <div class="date-cell">
                                <i class="far fa-calendar-alt"></i>
                                <?php echo date('d/m/Y H:i', strtotime($lamaran['tanggal_lamar'])); ?>
                            </div>
                </div>
                <td>
                    <span
                        class="badge 
                                                    <?php echo $lamaran['status_lamaran'] == 'Diterima' ? 'badge-diterima' :
                                                        ($lamaran['status_lamaran'] == 'Ditolak' ? 'badge-ditolak' : 'badge-diproses'); ?>">
                        <?php echo $lamaran['status_lamaran']; ?>
                    </span>
            </div>
            <td>
                <div class="catatan-cell <?php echo $has_catatan ? 'has-catatan' : ''; ?>">
                    <?php if ($has_catatan): ?>
                        <i class="fas fa-sticky-note"></i>
                        <?php echo htmlspecialchars(substr($lamaran['catatan_hrd'], 0, 50)) . (strlen($lamaran['catatan_hrd']) > 50 ? '...' : ''); ?>
                    <?php else: ?>
                        <i class="fas fa-sticky-note"></i>
                        <span style="color: #94a3b8;">Tidak ada catatan</span>
                    <?php endif; ?>
                </div>
                </div>
            <td>
                <div class="action-buttons">
                    <button class="btn-update updateLamaran" data-id="<?php echo $lamaran['id_lamaran']; ?>"
                        data-status="<?php echo $lamaran['status_lamaran']; ?>"
                        data-catatan="<?php echo htmlspecialchars($lamaran['catatan_hrd'] ?? ''); ?>"
                        data-nama="<?php echo $nama; ?>"
                        data-email="<?php echo htmlspecialchars($lamaran['email_user'] ?? '-'); ?>"
                        data-lowongan="<?php echo htmlspecialchars($lamaran['judul_lowongan']); ?>">
                        <i class="fas fa-pen"></i> Update
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
                    <i class="fas fa-inbox"></i>
                </div>
                <h3>Belum Ada Lamaran</h3>
                <p>Belum ada kandidat yang melamar ke lowongan Anda</p>
            </div>
        <?php endif; ?>
        </div>
        </div>
        </main>
        </div>
        </div>

        <!-- Modal Update Status & Catatan -->
        <div id="catatanModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3><i class="fas fa-pen"></i> Update Status Lamaran</h3>
                    <button class="modal-close">&times;</button>
                </div>
                <div class="modal-info-card">
                    <div class="modal-info-item">
                        <div class="modal-info-icon"><i class="fas fa-user"></i></div>
                        <div class="modal-info-content">
                            <div class="modal-info-label">Nama Pelamar</div>
                            <div class="modal-info-value" id="modalNamaPelamar">-</div>
                        </div>
                    </div>
                    <div class="modal-info-item">
                        <div class="modal-info-icon"><i class="fas fa-briefcase"></i></div>
                        <div class="modal-info-content">
                            <div class="modal-info-label">Lowongan</div>
                            <div class="modal-info-value" id="modalJudulLowongan">-</div>
                        </div>
                    </div>
                </div>
                <form id="catatanForm">
                    <input type="hidden" id="lamaranId">
                    <div class="form-group" style="padding: 0 24px;">
                        <label><i class="fas fa-tag"></i> Status Lamaran</label>
                        <select id="updateStatus">
                            <option value="Diproses">🟡 Diproses</option>
                            <option value="Diterima">🟢 Diterima</option>
                            <option value="Ditolak">🔴 Ditolak</option>
                        </select>
                    </div>
                    <div class="form-group" style="padding: 0 24px;">
                        <label><i class="fas fa-sticky-note"></i> Catatan HRD</label>
                        <textarea id="updateCatatan" rows="4"
                            placeholder="Masukkan catatan untuk pelamar..."></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-secondary modal-cancel"><i class="fas fa-times"></i>
                            Batal</button>
                        <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Export Filter Lamaran -->
        <div id="exportModalLamaran" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3><i class="fas fa-download"></i> Export Data Lamaran</h3>
                    <button class="modal-close">&times;</button>
                </div>
                <div class="form-two-columns">
                    <div class="form-group">
                        <label><i class="fas fa-tag"></i> Filter Status</label>
                        <select id="filterStatusLamaran">
                            <option value="">Semua Status</option>
                            <option value="Diproses">Diproses</option>
                            <option value="Diterima">Diterima</option>
                            <option value="Ditolak">Ditolak</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-briefcase"></i> Filter Lowongan</label>
                        <select id="filterLowonganLamaran">
                            <option value="">Semua Lowongan</option>
                            <?php foreach ($lowongan_list as $low): ?>
                                <option value="<?php echo $low['id_lowongan']; ?>">
                                    <?php echo htmlspecialchars($low['judul_lowongan']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-calendar-alt"></i> Tanggal Mulai</label>
                        <input type="date" id="filterTanggalMulai">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-calendar-alt"></i> Tanggal Akhir</label>
                        <input type="date" id="filterTanggalAkhir">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" id="btnResetFilterLamaran">
                        <i class="fas fa-redo"></i> Reset
                    </button>
                    <button type="button" class="btn-secondary modal-cancel">Batal</button>
                    <button type="button" class="btn-primary" id="btnExportCSVLamaran">
                        <i class="fas fa-file-csv"></i> CSV
                    </button>
                    <button type="button" class="btn-primary" id="btnExportXLSLamaran">
                        <i class="fas fa-file-excel"></i> Excel
                    </button>
                    <button type="button" class="btn-primary" id="btnExportPDFLamaran">
                        <i class="fas fa-file-pdf"></i> PDF
                    </button>
                </div>
            </div>
        </div>

        <div id="alertContainer" class="alert-container"></div>

        <script>
            var BASE_URL = '<?php echo $base_url; ?>';

            // DataTable initialization
            $(document).ready(function () {
                if ($('#lamaranTable tbody tr').length > 0) {
                    var table = $('#lamaranTable').DataTable({
                        language: {
                            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
                        },
                        pageLength: 10,
                        order: [[2, 'desc']],
                        columnDefs: [{ orderable: false, targets: [5] }]
                    });

                    $('#filterStatus').on('change', function () {
                        var status = $(this).val();
                        if (status === 'all') {
                            table.column(3).search('').draw();
                        } else {
                            table.column(3).search(status).draw();
                        }
                    });
                }
            });

            // Update Status Modal
            function openUpdateModal() {
                $('#catatanModal').addClass('show');
                $('body').css('overflow', 'hidden');
            }

            function closeUpdateModal() {
                $('#catatanModal').removeClass('show');
                $('body').css('overflow', '');
                $('#catatanForm')[0].reset();
                $('#lamaranId').val('');
            }

            $(document).on('click', '.updateLamaran', function () {
                $('#lamaranId').val($(this).data('id'));
                $('#updateStatus').val($(this).data('status'));
                $('#updateCatatan').val($(this).data('catatan') || '');
                $('#modalNamaPelamar').html($(this).data('nama'));
                $('#modalJudulLowongan').html($(this).data('lowongan'));
                openUpdateModal();
            });

            $('#catatanForm').submit(function (e) {
                e.preventDefault();
                var submitBtn = $(this).find('button[type="submit"]');
                var originalText = submitBtn.html();
                submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...').prop('disabled', true);

                $.ajax({
                    url: 'ajax_lamaran.php',
                    type: 'POST',
                    data: {
                        action: 'update_status',
                        id: $('#lamaranId').val(),
                        status: $('#updateStatus').val(),
                        catatan: $('#updateCatatan').val()
                    },
                    dataType: 'json',
                    success: function (res) {
                        if (res.success) {
                            showNotification('success', res.message, 'Berhasil');
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            showNotification('error', res.message, 'Gagal');
                            submitBtn.html(originalText).prop('disabled', false);
                        }
                    },
                    error: function () {
                        showNotification('error', 'Terjadi kesalahan', 'Error');
                        submitBtn.html(originalText).prop('disabled', false);
                    }
                });
            });

            // Notification function
            function showNotification(type, message, title) {
                var icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
                var textColor = type === 'success' ? '#15803d' : '#dc2626';
                var alertHtml = `<div class="alert-item ${type}">
                <i class="fas ${icon}" style="color: ${textColor};"></i>
                <div class="alert-content">
                    <div class="alert-title" style="color: ${textColor};">${title}</div>
                    <div class="alert-message">${message}</div>
                </div>
                <button class="alert-close" onclick="$(this).closest('.alert-item').remove()">&times;</button>
            </div>`;
                $('#alertContainer').append(alertHtml);
                setTimeout(() => $('#alertContainer .alert-item:first-child').fadeOut(300, function () { $(this).remove(); }), 4000);
            }

            // Close modals
            $('.modal-close, .modal-cancel').click(function () {
                closeUpdateModal();
                closeExportModalLamaran();
            });
            $(window).click(function (e) {
                if ($(e.target).hasClass('modal')) {
                    closeUpdateModal();
                    closeExportModalLamaran();
                }
            });

            // ========== EXPORT FUNCTIONS ==========
            function openExportModalLamaran() {
                $('#exportModalLamaran').addClass('show');
                $('body').css('overflow', 'hidden');
            }

            function closeExportModalLamaran() {
                $('#exportModalLamaran').removeClass('show');
                $('body').css('overflow', '');
            }

            $('#btnExportLamaran').click(function () {
                openExportModalLamaran();
            });

            $('#btnResetFilterLamaran').click(function () {
                $('#filterStatusLamaran').val('');
                $('#filterLowonganLamaran').val('');
                $('#filterTanggalMulai').val('');
                $('#filterTanggalAkhir').val('');
            });

            function exportLamaranData(format) {
                var filters = {
                    status_lamaran: $('#filterStatusLamaran').val(),
                    id_lowongan: $('#filterLowonganLamaran').val(),
                    tanggal_mulai: $('#filterTanggalMulai').val(),
                    tanggal_akhir: $('#filterTanggalAkhir').val(),
                    format: format
                };

                console.log('Exporting to:', format, filters); // Debug

                var btnId = '';
                if (format === 'csv') btnId = '#btnExportCSVLamaran';
                else if (format === 'xls') btnId = '#btnExportXLSLamaran';
                else if (format === 'pdf') btnId = '#btnExportPDFLamaran';

                var originalText = $(btnId).html();
                $(btnId).html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);

                $.ajax({
                    url: 'ajax_export_lamaran.php',
                    type: 'POST',
                    data: filters,
                    dataType: 'json',
                    timeout: 30000,
                    success: function (res) {
                        console.log('Response:', res); // Debug
                        $(btnId).html(originalText).prop('disabled', false);

                        if (res.success) {
                            if (res.data_count > 0) {
                                // Gunakan download handler seperti di lowongan.php
                                var downloadUrl = 'download.php?file=' + encodeURIComponent(res.download_url.replace('exports/', ''));
                                window.location.href = downloadUrl;
                                showNotification('success', `Berhasil export ${res.data_count} data lamaran`, 'Export Berhasil');
                                closeExportModalLamaran();
                            } else {
                                showNotification('error', 'Tidak ada data lamaran yang sesuai dengan filter', 'Tidak Ada Data');
                            }
                        } else {
                            showNotification('error', res.message || 'Gagal melakukan export', 'Export Gagal');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.log('AJAX Error:', status, error);
                        console.log('Response Text:', xhr.responseText);
                        $(btnId).html(originalText).prop('disabled', false);
                        showNotification('error', 'Terjadi kesalahan: ' + error, 'Error');
                    }
                });
            }

            // Export button handlers dengan event binding yang lebih aman
            $(document).on('click', '#btnExportCSVLamaran', function (e) {
                e.preventDefault();
                exportLamaranData('csv');
            });

            $(document).on('click', '#btnExportXLSLamaran', function (e) {
                e.preventDefault();
                exportLamaranData('xls');
            });

            $(document).on('click', '#btnExportPDFLamaran', function (e) {
                e.preventDefault();
                exportLamaranData('pdf');
            });

        </script>

</body>

</html>