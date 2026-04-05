<?php
// perusahaan/pelamar.php - Halaman Data Pelamar
session_start();
require_once 'koneksi_perusahaan.php';

$company_id = $_SESSION['company_id'];
$pelamar_list = $db->getPelamarByPerusahaan($company_id);
$provinsi_list = $db->getAllProvinsi();

// Base URL untuk akses gambar dari admin panel - SESUAIKAN DENGAN DOMAIN ANDA
$base_url = 'http://localhost/web-linkup-loker/';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pelamar | LinkUp Perusahaan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <style>
        /* ========== SAME STYLES AS BEFORE ========== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            color: #0f172a;
        }

        .app-container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, #0f172a 0%, #0f172a 100%);
            color: #94a3b8;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            z-index: 100;
            transition: all 0.3s ease;
        }

        .sidebar-logo {
            padding: 28px 24px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
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
            color: #94a3b8;
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
            background: rgba(59, 130, 246, 0.12);
            color: white;
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
            margin-bottom: 28px;
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

        .card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }

        .card-body {
            padding: 24px;
        }

        .modern-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
        }

        .modern-table th {
            background: #f8fafc;
            color: #374151;
            font-weight: 600;
            text-align: left;
            padding: 16px 20px;
            border-bottom: 2px solid #e2e8f0;
            white-space: nowrap;
        }

        .modern-table td {
            padding: 16px 20px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .modern-table tbody tr:hover {
            background: #f8fafc;
        }

        .user-cell {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .user-avatar {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: linear-gradient(135deg, #eef2ff, #e0e7ff);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1.1rem;
            color: #2563eb;
        }

        .user-info {
            flex: 1;
        }

        .user-name {
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 4px;
            font-size: 0.9rem;
        }

        .user-email {
            color: #64748b;
            font-size: 0.8rem;
        }

        .contact-cell {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .contact-phone,
        .contact-birth {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #374151;
            font-size: 0.8rem;
        }

        .city-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background: #f1f5f9;
            border-radius: 20px;
            color: #475569;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .gender-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            background: #fef3c7;
            border-radius: 16px;
            color: #92400e;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn-icon {
            width: 38px;
            height: 38px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .btn-view {
            background: #3b82f6;
            color: white;
        }

        .btn-view:hover {
            background: #2563eb;
            transform: translateY(-1px);
        }

        .empty-state {
            text-align: center;
            padding: 80px 40px;
        }

        .empty-state-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 24px;
            background: #f1f5f9;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #64748b;
        }

        .empty-state h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 12px;
        }

        .empty-state p {
            color: #64748b;
            font-size: 0.875rem;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            overflow: auto;
        }

        .modal.show {
            display: block;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 0;
            border-radius: 16px;
            width: 90%;
            max-width: 800px;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 24px 24px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .modal-header h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #0f172a;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #64748b;
            padding: 0;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .modal-close:hover {
            background: #f1f5f9;
            color: #374151;
        }

        .detail-content {
            padding: 24px;
        }

        .detail-section {
            margin-bottom: 32px;
        }

        .section-title {
            font-size: 1rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .detail-item {
            margin-bottom: 0;
        }

        .detail-item.full-width {
            grid-column: span 2;
        }

        .detail-label {
            font-size: 0.7rem;
            font-weight: 500;
            color: #64748b;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .detail-label i {
            font-size: 0.65rem;
            width: 16px;
        }

        .detail-value {
            font-size: 0.85rem;
            color: #0f172a;
            line-height: 1.5;
            font-weight: 500;
        }

        .profile-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 24px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e2e8f0;
        }

        .profile-avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #2563eb, #4f46e5);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 600;
            color: white;
            overflow: hidden;
        }

        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-name h4 {
            font-size: 1.2rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 6px;
        }

        .profile-name p {
            font-size: 0.8rem;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .profile-name p i {
            margin-right: 4px;
        }

        .social-links {
            display: flex;
            gap: 12px;
            margin-top: 12px;
        }

        .social-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            background: #f1f5f9;
            border-radius: 20px;
            font-size: 0.7rem;
            color: #475569;
            text-decoration: none;
            transition: all 0.2s;
        }

        .social-link:hover {
            background: #e2e8f0;
            color: #2563eb;
        }

        .portfolio-item {
            background: #f8fafc;
            border-radius: 12px;
            padding: 12px;
            margin-bottom: 12px;
        }

        .portfolio-title {
            font-weight: 600;
            font-size: 0.85rem;
            color: #0f172a;
            margin-bottom: 8px;
        }

        .portfolio-link {
            font-size: 0.7rem;
            color: #2563eb;
            text-decoration: none;
            word-break: break-all;
        }

        .portfolio-link:hover {
            text-decoration: underline;
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

        /* Button Styles */
        .btn-primary {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.875rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary:hover {
            background: #2563eb;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: #f1f5f9;
            color: #374151;
            border: 1px solid #e2e8f0;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.875rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-secondary:hover {
            background: #e2e8f0;
            transform: translateY(-1px);
        }

        /* Form Styles */
        .form-two-columns {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            color: #374151;
            font-size: 0.875rem;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px 12px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 0.875rem;
            transition: border-color 0.2s;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #3b82f6;
        }

        .modal-footer {
            padding: 16px 24px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            gap: 12px;
            justify-content: flex-end;
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

            .modern-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }

            .user-cell {
                min-width: 180px;
            }

            .detail-grid {
                grid-template-columns: 1fr;
            }

            .detail-item.full-width {
                grid-column: span 1;
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
                        <h1 class="page-title">Data Pelamar</h1>
                        <p class="page-subtitle">Kelola semua kandidat yang mendaftar melalui perusahaan Anda</p>
                    </div>
                    <div style="display: flex; gap: 12px;">
                        <button class="btn-secondary" id="btnExportPelamar"
                            style="display: inline-flex; align-items: center; gap: 8px;">
                            <i class="fas fa-download"></i> Export Data
                        </button>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <?php if (count($pelamar_list) > 0): ?>
                            <table class="modern-table" id="pelamarTable">
                                <thead>
                                    <tr>
                                        <th>Informasi Pelamar</th>
                                        <th>Kontak & Info</th>
                                        <th>Lokasi</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pelamar_list as $pelamar):
                                        $nama = htmlspecialchars($pelamar['nama_user'] ?? $pelamar['username_user'] ?? '-');
                                        $initial = strtoupper(substr($nama, 0, 1));
                                        $jk = $pelamar['jk_user'] ?? '';
                                        $jk_text = '';
                                        $jk_icon = '';
                                        if ($jk == 'L') {
                                            $jk_text = 'Laki-laki';
                                            $jk_icon = 'fa-mars';
                                        } elseif ($jk == 'P') {
                                            $jk_text = 'Perempuan';
                                            $jk_icon = 'fa-venus';
                                        }

                                        // Path foto menggunakan URL absolut
                                        $foto_url = '';
                                        $has_foto = !empty($pelamar['foto_user']);
                                        if ($has_foto) {
                                            $foto_url = $base_url . 'adminpanel/src/images/user/' . $pelamar['foto_user'];
                                        }
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
                                                            <?php echo htmlspecialchars($pelamar['email_user'] ?? '-'); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="contact-cell">
                                                    <div class="contact-phone">
                                                        <i class="fas fa-phone-alt"></i>
                                                        <?php echo htmlspecialchars($pelamar['nohp_user'] ?? '-'); ?>
                                                    </div>
                                                    <?php if ($pelamar['tanggallahir_user']): ?>
                                                        <div class="contact-birth">
                                                            <i class="fas fa-calendar-alt"></i>
                                                            <?php echo date('d/m/Y', strtotime($pelamar['tanggallahir_user'])); ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    <?php if ($jk_text): ?>
                                                        <div class="gender-badge">
                                                            <i class="fas <?php echo $jk_icon; ?>"></i>
                                                            <?php echo $jk_text; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="city-badge">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                    <?php
                                                    $lokasi = [];
                                                    if (!empty($pelamar['nama_kota']))
                                                        $lokasi[] = $pelamar['nama_kota'];
                                                    if (!empty($pelamar['nama_provinsi']))
                                                        $lokasi[] = $pelamar['nama_provinsi'];
                                                    echo htmlspecialchars(implode(', ', $lokasi) ?: '-');
                                                    ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <button class="btn-icon btn-view detailPelamar" data-id="<?php echo $pelamar['id_user']; ?>"
                                                        data-foto="<?php echo $foto_url; ?>" data-initial="<?php echo $initial; ?>"
                                                        title="Lihat Detail Lengkap">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <h3>Belum Ada Pelamar</h3>
                                <p>Belum ada kandidat yang mendaftar melalui lowongan Anda</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal Detail Pelamar -->
    <div id="detailModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-user-circle"></i> Detail Lengkap Pelamar</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div id="detailContent" class="detail-content">
                <div style="text-align: center; padding: 20px;">
                    <i class="fas fa-spinner fa-spin"></i> Memuat data...
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Export Filter Pelamar -->
    <div id="exportModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-download"></i> Export Data Pelamar</h3>
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
                    <label>Filter Jenis Kelamin</label>
                    <select id="filterJenisKelamin" name="filterJenisKelamin">
                        <option value="">Semua Jenis Kelamin</option>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Filter Tanggal Lahir Mulai</label>
                    <input type="date" id="filterTanggalLahirMulai" name="filterTanggalLahirMulai">
                </div>
                <div class="form-group">
                    <label>Filter Tanggal Lahir Akhir</label>
                    <input type="date" id="filterTanggalLahirAkhir" name="filterTanggalLahirAkhir">
                </div>
                <div class="form-group">
                    <label>Filter Email</label>
                    <input type="text" id="filterEmail" name="filterEmail" placeholder="Masukkan email">
                </div>
                <div class="form-group">
                    <label>Filter No. HP</label>
                    <input type="text" id="filterNoHP" name="filterNoHP" placeholder="Masukkan no. HP">
                </div>
                <div class="form-group">
                    <label>Filter Nama</label>
                    <input type="text" id="filterNama" name="filterNama" placeholder="Masukkan nama pelamar">
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
        var BASE_URL = '<?php echo $base_url; ?>';

        // DataTable initialization
        $(document).ready(function () {
            if ($('#pelamarTable tbody tr').length > 0) {
                $('#pelamarTable').DataTable({
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
                    },
                    pageLength: 10,
                    order: [[0, 'asc']]
                });
            }
        });

        // Modal functions
        function openModal() {
            $('#detailModal').addClass('show');
            $('body').css('overflow', 'hidden');
        }

        function closeModal() {
            $('#detailModal').removeClass('show');
            $('body').css('overflow', '');
            $('#detailContent').html('<div style="text-align: center; padding: 20px;"><i class="fas fa-spinner fa-spin"></i> Memuat data...</div>');
        }

        // Export Modal Functions
        function openExportModal() {
            $('#exportModal').addClass('show');
            $('body').css('overflow', 'hidden');
        }

        function closeExportModal() {
            $('#exportModal').removeClass('show');
            $('body').css('overflow', '');
        }

        // Tombol export pelamar
        $('#btnExportPelamar').click(function() {
            openExportModal();
        });

        // Load kota untuk filter provinsi
        $('#filterProvinsi').on('change', function() {
            var provinsiId = $(this).val();
            if (provinsiId) {
                $.ajax({
                    url: 'ajax_get_kota.php',
                    type: 'GET',
                    data: { id_provinsi: provinsiId },
                    dataType: 'json',
                    success: function(data) {
                        $('#filterKota').empty().append('<option value="">Semua Kota</option>');
                        $.each(data, function(i, kota) {
                            $('#filterKota').append('<option value="' + kota.id_kota + '">' + kota.nama_kota + '</option>');
                        });
                    }
                });
            } else {
                $('#filterKota').empty().append('<option value="">Semua Kota</option>');
            }
        });

        // Reset filter
        $('#btnResetFilter').click(function() {
            $('#exportModal select').val('');
            $('#exportModal input').val('');
            $('#filterKota').empty().append('<option value="">Semua Kota</option>');
        });

        // Export functions
        function exportData(format) {
            var filters = {
                provinsi: $('#filterProvinsi').val(),
                kota: $('#filterKota').val(),
                jenis_kelamin: $('#filterJenisKelamin').val(),
                tanggal_lahir_mulai: $('#filterTanggalLahirMulai').val(),
                tanggal_lahir_akhir: $('#filterTanggalLahirAkhir').val(),
                email: $('#filterEmail').val(),
                nohp: $('#filterNoHP').val(),
                nama: $('#filterNama').val(),
                format: format
            };

            var btnId = '#btnExport' + format.toUpperCase();
            var originalText = $(btnId).html();
            $(btnId).html('<i class="fas fa-spinner fa-spin"></i> Exporting...').prop('disabled', true);

            $.ajax({
                url: 'ajax_export_pelamar.php',
                type: 'POST',
                data: filters,
                dataType: 'json',
                success: function(res) {
                    $(btnId).html(originalText).prop('disabled', false);
                    
                    if (res.success) {
                        if (res.data_count > 0) {
                            var downloadUrl = 'download.php?file=' + encodeURIComponent(res.download_url.replace('exports/', ''));
                            window.location.href = downloadUrl;
                            alert('Berhasil export ' + res.data_count + ' data pelamar');
                            closeExportModal();
                        } else {
                            alert('Tidak ada data yang sesuai dengan filter yang dipilih');
                        }
                    } else {
                        alert(res.message || 'Gagal melakukan export');
                    }
                },
                error: function() {
                    $(btnId).html(originalText).prop('disabled', false);
                    alert('Terjadi kesalahan saat export data');
                }
            });
        }

        // Export button handlers
        $('#btnExportCSV').click(function() {
            exportData('csv');
        });

        $('#btnExportXLS').click(function() {
            exportData('xls');
        });

        $('#btnExportPDF').click(function() {
            exportData('pdf');
        });

        // Detail Pelamar lengkap
        $(document).on('click', '.detailPelamar', function () {
            var id = $(this).data('id');
            var fotoUrl = $(this).data('foto') || '';
            var initial = $(this).data('initial') || '?';

            openModal();

            $.ajax({
                url: 'ajax_pelamar.php',
                type: 'GET',
                data: { action: 'get_detail', id: id },
                dataType: 'json',
                success: function (res) {
                    if (res.success) {
                        var data = res.data;
                        var nama = data.nama_user || data.username_user || '-';
                        var jk = data.jk_user || '';
                        var jk_text = '';
                        var jk_icon = '';
                        if (jk == 'L') {
                            jk_text = 'Laki-laki';
                            jk_icon = 'fa-mars';
                        } else if (jk == 'P') {
                            jk_text = 'Perempuan';
                            jk_icon = 'fa-venus';
                        }

                        var fotoImg = '';
                        if (data.foto_user) {
                            var fotoPath = BASE_URL + 'adminpanel/src/images/user/' + data.foto_user;
                            fotoImg = '<img src="' + fotoPath + '" alt="Foto" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null; this.parentElement.style.background=\'linear-gradient(135deg, #2563eb, #4f46e5)\'; this.parentElement.innerHTML=\'' + nama.charAt(0).toUpperCase() + '\'; this.style.display=\'none\';">';
                        } else if (fotoUrl) {
                            fotoImg = '<img src="' + fotoUrl + '" alt="Foto" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null; this.parentElement.style.background=\'linear-gradient(135deg, #2563eb, #4f46e5)\'; this.parentElement.innerHTML=\'' + nama.charAt(0).toUpperCase() + '\'; this.style.display=\'none\';">';
                        } else {
                            fotoImg = nama.charAt(0).toUpperCase();
                        }

                        var html = '<div class="profile-header"><div class="profile-avatar">' + fotoImg + '</div><div class="profile-name"><h4>' + nama + '</h4><p><span><i class="fas fa-envelope"></i> ' + (data.email_user || '-') + '</span>' + (jk_text ? '<span><i class="fas ' + jk_icon + '"></i> ' + jk_text + '</span>' : '') + '</p><div class="social-links">' + (data.instagram_user ? '<a href="' + data.instagram_user + '" target="_blank" class="social-link"><i class="fab fa-instagram"></i> Instagram</a>' : '') + (data.facebook_user ? '<a href="' + data.facebook_user + '" target="_blank" class="social-link"><i class="fab fa-facebook"></i> Facebook</a>' : '') + (data.linkedin_user ? '<a href="' + data.linkedin_user + '" target="_blank" class="social-link"><i class="fab fa-linkedin"></i> LinkedIn</a>' : '') + '</div></div></div>';

                        // Informasi Pribadi
                        html += '<div class="detail-section"><div class="section-title"><i class="fas fa-user"></i> Informasi Pribadi</div><div class="detail-grid"><div class="detail-item"><div class="detail-label"><i class="fas fa-phone"></i> No. Telepon</div><div class="detail-value">' + (data.nohp_user || '-') + '</div></div><div class="detail-item"><div class="detail-label"><i class="fas fa-calendar"></i> Tanggal Lahir</div><div class="detail-value">' + (data.tanggallahir_user ? new Date(data.tanggallahir_user).toLocaleDateString('id-ID') : '-') + '</div></div><div class="detail-item"><div class="detail-label"><i class="fas fa-map-marker-alt"></i> Provinsi</div><div class="detail-value">' + (data.nama_provinsi || '-') + '</div></div><div class="detail-item"><div class="detail-label"><i class="fas fa-city"></i> Kota</div><div class="detail-value">' + (data.nama_kota || '-') + '</div></div></div></div>';

                        // Tentang Diri
                        if (data.deskripsi_user) {
                            html += '<div class="detail-section"><div class="section-title"><i class="fas fa-align-left"></i> Tentang Diri</div><div class="detail-value">' + data.deskripsi_user.replace(/\n/g, '<br>') + '</div></div>';
                        }

                        // Kelebihan
                        if (data.kelebihan_user) {
                            html += '<div class="detail-section"><div class="section-title"><i class="fas fa-star"></i> Kelebihan / Skills</div><div class="detail-value">' + data.kelebihan_user.replace(/\n/g, '<br>') + '</div></div>';
                        }

                        // Riwayat Pekerjaan
                        if (data.riwayatpekerjaan_user) {
                            html += '<div class="detail-section"><div class="section-title"><i class="fas fa-briefcase"></i> Riwayat Pekerjaan</div><div class="detail-value">' + data.riwayatpekerjaan_user.replace(/\n/g, '<br>') + '</div></div>';
                        }

                        // Prestasi
                        if (data.prestasi_user) {
                            html += '<div class="detail-section"><div class="section-title"><i class="fas fa-trophy"></i> Prestasi</div><div class="detail-value">' + data.prestasi_user.replace(/\n/g, '<br>') + '</div></div>';
                        }

                        // Portfolio
                        if (data.judul_porto || data.link_porto || data.gambar_porto) {
                            var gambarPortoHtml = '';
                            if (data.gambar_porto) {
                                var portoPath = BASE_URL + 'adminpanel/src/images/portofolio/' + data.gambar_porto;
                                gambarPortoHtml = '<div style="margin-top: 10px;"><img src="' + portoPath + '" style="max-width: 100%; max-height: 200px; border-radius: 10px;" onerror="this.style.display=\'none\'"></div>';
                            }

                            html += '<div class="detail-section"><div class="section-title"><i class="fas fa-folder-open"></i> Portfolio</div><div class="portfolio-item">' + (data.judul_porto ? '<div class="portfolio-title">' + data.judul_porto + '</div>' : '') + (data.link_porto ? '<a href="' + data.link_porto + '" target="_blank" class="portfolio-link"><i class="fas fa-external-link-alt"></i> ' + data.link_porto + '</a>' : '') + gambarPortoHtml + '</div></div>';
                        }

                        $('#detailContent').html(html);
                    } else {
                        $('#detailContent').html('<div style="text-align: center; padding: 40px;"><i class="fas fa-exclamation-circle" style="font-size: 2rem; color: #ef4444; margin-bottom: 12px; display: block;"></i><p>' + res.message + '</p></div>');
                    }
                },
                error: function () {
                    $('#detailContent').html('<div style="text-align: center; padding: 40px;"><i class="fas fa-exclamation-circle" style="font-size: 2rem; color: #ef4444; margin-bottom: 12px; display: block;"></i><p>Terjadi kesalahan saat memuat data</p></div>');
                }
            });
        });

        // Escape HTML
        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/[&<>"']/g, function (m) {
                return {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#39;'
                }[m];
            });
        }

        // Close modal
        $('.modal-close').click(function() {
            if ($(this).closest('#detailModal').length) {
                closeModal();
            } else if ($(this).closest('#exportModal').length) {
                closeExportModal();
            }
        });
        $(window).click(function (e) {
            if ($(e.target).hasClass('modal')) {
                if ($(e.target).attr('id') === 'detailModal') {
                    closeModal();
                } else if ($(e.target).attr('id') === 'exportModal') {
                    closeExportModal();
                }
            }
        });
    </script>

</body>

</html>
