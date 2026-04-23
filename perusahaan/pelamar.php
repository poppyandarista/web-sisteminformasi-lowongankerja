<?php
// perusahaan/pelamar.php - Halaman Data Pelamar
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
$pelamar_list = $db->getPelamarByPerusahaan($company_id);

// Base URL untuk akses gambar dari admin panel - SESUAIKAN DENGAN DOMAIN ANDA
$base_url = 'http://localhost/web-linkup-loker/';

// Ambil data untuk filter
$provinsi_list = $db->getAllProvinsi();
$kategori_list = $db->getAllKategori(); // Untuk filter lowongan
$jenis_list = $db->getAllJenis(); // Untuk filter lowongan
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pelamar | LinkUp Perusahaan</title>
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <style>
        /* ========== STYLES WITH HORIZONTAL SCROLL FIX ========== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f8fafc;
            color: #0f172a;
            overflow-x: hidden;
        }

        /* Class untuk menahan scroll body saat modal terbuka */
        body.modal-open {
            overflow: hidden !important;
        }

        .app-container {
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
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
            overflow-x: hidden;
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

        /* ========== CONTAINER SCROLL HORIZONTAL UNTUK TABEL ========== */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            overflow-y: visible;
            -webkit-overflow-scrolling: touch;
            position: relative;
        }

        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .modern-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
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
            white-space: nowrap;
        }

        .modern-table td {
            padding: 16px 20px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.85rem;
            vertical-align: middle;
            white-space: nowrap;
        }

        .modern-table td:first-child {
            white-space: normal;
            min-width: 250px;
        }

        .modern-table td:nth-child(2) {
            white-space: normal;
            min-width: 180px;
        }

        .modern-table td:nth-child(3) {
            white-space: normal;
            min-width: 150px;
        }

        .modern-table td:last-child {
            white-space: nowrap;
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
            flex-shrink: 0;
        }

        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .user-info {
            display: flex;
            flex-direction: column;
            min-width: 0;
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

        .contact-cell {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .contact-phone {
            font-size: 0.8rem;
            color: #1e293b;
            white-space: nowrap;
        }

        .contact-phone i {
            font-size: 0.7rem;
            color: #10b981;
            margin-right: 6px;
        }

        .contact-birth {
            font-size: 0.7rem;
            color: #64748b;
            white-space: nowrap;
        }

        .contact-birth i {
            font-size: 0.65rem;
            color: #f59e0b;
            margin-right: 6px;
        }

        .gender-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #f1f5f9;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.65rem;
            color: #475569;
            width: fit-content;
        }

        .city-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f1f5f9;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            color: #475569;
        }

        .city-badge i {
            color: #ef4444;
            font-size: 0.65rem;
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

        .btn-view {
            background: #eef2ff;
            color: #4f46e5;
            font-family: 'Poppins', sans-serif;
        }

        .btn-view:hover {
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

        /* ========== MODAL STYLES DENGAN SCROLL VERTIKAL ========== */
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
            visibility: visible !important;
            opacity: 1 !important;
        }

        /* Force close all modals on page load */
        .modal {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
        }

        .modal-content {
            background: white;
            border-radius: 20px;
            max-width: 700px;
            width: 90%;
            max-height: 85vh;
            /* PENTING: Scroll vertikal */
            overflow-y: auto !important;
            overflow-x: hidden;
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
            padding: 18px 24px;
            border-bottom: 1px solid #eef2ff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            border-radius: 20px 20px 0 0;
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
            position: sticky;
            bottom: 0;
            background: white;
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

        /* Detail content - TIDAK ADA OVERFLOW AGAR MODAL YANG MENGATUR SCROLL */
        .detail-content {
            padding: 24px;
        }

        .detail-section {
            margin-bottom: 24px;
            padding-bottom: 20px;
            border-bottom: 1px solid #f1f5f9;
        }

        .detail-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .section-title {
            font-size: 0.75rem;
            font-weight: 700;
            color: #2563eb;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-title i {
            font-size: 0.85rem;
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
            word-wrap: break-word;
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
            flex-shrink: 0;
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
            flex-wrap: wrap;
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
            word-wrap: break-word;
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

            .table-responsive {
                overflow-x: auto;
            }

            .detail-grid {
                grid-template-columns: 1fr;
            }

            .detail-item.full-width {
                grid-column: span 1;
            }

            .form-two-columns {
                grid-template-columns: 1fr;
                gap: 14px;
                padding: 20px;
            }

            .full-width {
                grid-column: span 1;
            }

            .profile-header {
                flex-direction: column;
                text-align: center;
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
                        <h1>Data Pelamar</h1>
                        <p>Kelola semua kandidat yang mendaftar melalui perusahaan Anda</p>
                    </div>
                    <button class="btn-secondary" id="btnExportPelamar"
                        style="display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fas fa-download"></i> Export Data
                    </button>
                </div>

                <div class="card">
                    <div class="card-body">
                        <?php if (count($pelamar_list) > 0): ?>
                            <div class="table-responsive">
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
                                </div>
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
                            </div>
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
                        </div>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-icon btn-view detailPelamar" data-id="<?php echo $pelamar['id_user']; ?>"
                                    data-foto="<?php echo $foto_url; ?>" data-initial="<?php echo $initial; ?>"
                                    title="Lihat Detail Lengkap">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                </div>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </div>
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
    <div id="exportModalPelamar" class="modal">
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
                    <select id="filterJk" name="filterJk">
                        <option value="">Semua</option>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Filter Lowongan Dilamar</label>
                    <select id="filterLowongan" name="filterLowongan">
                        <option value="">Semua Lowongan</option>
                        <?php
                        $lowongan_list = $db->getLowonganByPerusahaan($company_id);
                        foreach ($lowongan_list as $low): ?>
                            <option value="<?php echo $low['id_lowongan']; ?>">
                                <?php echo htmlspecialchars($low['judul_lowongan']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Filter Tanggal Lahir (Minimal)</label>
                    <input type="date" id="filterTanggalLahirMin" name="filterTanggalLahirMin">
                </div>
                <div class="form-group">
                    <label>Filter Tanggal Lahir (Maksimal)</label>
                    <input type="date" id="filterTanggalLahirMax" name="filterTanggalLahirMax">
                </div>
                <div class="form-group">
                    <label>Filter Status Lamaran</label>
                    <select id="filterStatusLamaran" name="filterStatusLamaran">
                        <option value="">Semua Status</option>
                        <option value="pending">Pending</option>
                        <option value="diterima">Diterima</option>
                        <option value="ditolak">Ditolak</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary modal-cancel">Batal</button>
                <button type="button" class="btn-secondary" id="btnResetFilterPelamar">
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

        // Notification function
        function showNotification(type, message, title) {
            if ($('#alertContainer').length === 0) {
                $('body').append('<div id="alertContainer" class="alert-container"></div>');
            }

            var icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
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

        // DataTable initialization
        $(document).ready(function () {
            $('.modal').removeClass('show').hide();
            $('body').css('overflow', '');

            if (typeof (Storage) !== "undefined") {
                localStorage.removeItem('modalState');
                sessionStorage.removeItem('modalState');
            }

            if ($('#pelamarTable tbody tr').length > 0) {
                $('#pelamarTable').DataTable({
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
                    },
                    pageLength: 10,
                    order: [[0, 'desc']]
                });
            }
        });

        $(window).on('load', function () {
            setTimeout(function () {
                $('.modal').removeClass('show').hide();
                $('body').css('overflow', '');
            }, 100);
        });

        // Modal functions dengan class modal-open pada body
        function openModal() {
            $('#detailModal').addClass('show');
            $('body').addClass('modal-open');
        }

        function closeModal() {
            $('#detailModal').removeClass('show');
            $('body').removeClass('modal-open');
            $('#detailContent').html('<div style="text-align: center; padding: 20px;"><i class="fas fa-spinner fa-spin"></i> Memuat data...</div>');
        }

        // Format tanggal
        function formatDate(dateString) {
            if (!dateString) return '-';
            var date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'long',
                year: 'numeric'
            });
        }

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
                            fotoImg = `<img src="${fotoPath}" alt="Foto" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null; this.parentElement.style.background='linear-gradient(135deg, #2563eb, #4f46e5)'; this.parentElement.innerHTML='${nama.charAt(0).toUpperCase()}'; this.style.display='none';">`;
                        } else if (fotoUrl) {
                            fotoImg = `<img src="${fotoUrl}" alt="Foto" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null; this.parentElement.style.background='linear-gradient(135deg, #2563eb, #4f46e5)'; this.parentElement.innerHTML='${nama.charAt(0).toUpperCase()}'; this.style.display='none';">`;
                        } else {
                            fotoImg = nama.charAt(0).toUpperCase();
                        }

                        var html = `
                        <div class="profile-header">
                            <div class="profile-avatar">${fotoImg}</div>
                            <div class="profile-name">
                                <h4>${escapeHtml(nama)}</h4>
                                <p>
                                    <span><i class="fas fa-envelope"></i> ${escapeHtml(data.email_user || '-')}</span>
                                    ${jk_text ? `<span><i class="fas ${jk_icon}"></i> ${jk_text}</span>` : ''}
                                </p>
                                <div class="social-links">
                                    ${data.instagram_user ? `<a href="${escapeHtml(data.instagram_user)}" target="_blank" class="social-link"><i class="fab fa-instagram"></i> Instagram</a>` : ''}
                                    ${data.facebook_user ? `<a href="${escapeHtml(data.facebook_user)}" target="_blank" class="social-link"><i class="fab fa-facebook"></i> Facebook</a>` : ''}
                                    ${data.linkedin_user ? `<a href="${escapeHtml(data.linkedin_user)}" target="_blank" class="social-link"><i class="fab fa-linkedin"></i> LinkedIn</a>` : ''}
                                </div>
                            </div>
                        </div>
                    `;

                        html += `
                        <div class="detail-section">
                            <div class="section-title">
                                <i class="fas fa-user"></i> Informasi Pribadi
                            </div>
                            <div class="detail-grid">
                                <div class="detail-item">
                                    <div class="detail-label"><i class="fas fa-phone"></i> No. Telepon</div>
                                    <div class="detail-value">${escapeHtml(data.nohp_user || '-')}</div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label"><i class="fas fa-calendar"></i> Tanggal Lahir</div>
                                    <div class="detail-value">${formatDate(data.tanggallahir_user)}</div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label"><i class="fas fa-map-marker-alt"></i> Provinsi</div>
                                    <div class="detail-value">${escapeHtml(data.nama_provinsi || '-')}</div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label"><i class="fas fa-city"></i> Kota</div>
                                    <div class="detail-value">${escapeHtml(data.nama_kota || '-')}</div>
                                </div>
                            </div>
                        </div>
                    `;

                        if (data.deskripsi_user) {
                            html += `
                            <div class="detail-section">
                                <div class="section-title">
                                    <i class="fas fa-align-left"></i> Tentang Diri
                                </div>
                                <div class="detail-value">${escapeHtml(data.deskripsi_user).replace(/\n/g, '<br>')}</div>
                            </div>
                        `;
                        }

                        if (data.kelebihan_user) {
                            html += `
                            <div class="detail-section">
                                <div class="section-title">
                                    <i class="fas fa-star"></i> Kelebihan / Skills
                                </div>
                                <div class="detail-value">${escapeHtml(data.kelebihan_user).replace(/\n/g, '<br>')}</div>
                            </div>
                        `;
                        }

                        if (data.riwayatpekerjaan_user) {
                            html += `
                            <div class="detail-section">
                                <div class="section-title">
                                    <i class="fas fa-briefcase"></i> Riwayat Pekerjaan
                                </div>
                                <div class="detail-value">${escapeHtml(data.riwayatpekerjaan_user).replace(/\n/g, '<br>')}</div>
                            </div>
                        `;
                        }

                        if (data.prestasi_user) {
                            html += `
                            <div class="detail-section">
                                <div class="section-title">
                                    <i class="fas fa-trophy"></i> Prestasi
                                </div>
                                <div class="detail-value">${escapeHtml(data.prestasi_user).replace(/\n/g, '<br>')}</div>
                            </div>
                        `;
                        }

                        if (data.judul_porto || data.link_porto || data.gambar_porto) {
                            var gambarPortoHtml = '';
                            if (data.gambar_porto) {
                                var portoPath = BASE_URL + 'adminpanel/src/images/portofolio/' + data.gambar_porto;
                                gambarPortoHtml = `<div style="margin-top: 10px;"><img src="${portoPath}" style="max-width: 100%; max-height: 200px; border-radius: 10px;" onerror="this.style.display='none'"></div>`;
                            }

                            html += `
                            <div class="detail-section">
                                <div class="section-title">
                                    <i class="fas fa-folder-open"></i> Portfolio
                                </div>
                                <div class="portfolio-item">
                                    ${data.judul_porto ? `<div class="portfolio-title">${escapeHtml(data.judul_porto)}</div>` : ''}
                                    ${data.link_porto ? `<a href="${escapeHtml(data.link_porto)}" target="_blank" class="portfolio-link"><i class="fas fa-external-link-alt"></i> ${escapeHtml(data.link_porto)}</a>` : ''}
                                    ${gambarPortoHtml}
                                </div>
                            </div>
                        `;
                        }

                        $('#detailContent').html(html);
                    } else {
                        $('#detailContent').html(`
                        <div style="text-align: center; padding: 40px;">
                            <i class="fas fa-exclamation-circle" style="font-size: 2rem; color: #ef4444; margin-bottom: 12px; display: block;"></i>
                            <p>${res.message}</p>
                        </div>
                    `);
                    }
                },
                error: function () {
                    $('#detailContent').html(`
                    <div style="text-align: center; padding: 40px;">
                        <i class="fas fa-exclamation-circle" style="font-size: 2rem; color: #ef4444; margin-bottom: 12px; display: block;"></i>
                        <p>Terjadi kesalahan saat memuat data</p>
                    </div>
                `);
                }
            });
        });

        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/[&<>]/g, function (m) {
                if (m === '&') return '&amp;';
                if (m === '<') return '&lt;';
                if (m === '>') return '&gt;';
                return m;
            });
        }

        $('.modal-close').click(closeModal);
        $(window).click(function (e) {
            if ($(e.target).hasClass('modal')) {
                closeModal();
            }
        });

        // ========== EXPORT PELAMAR FUNCTIONS ==========
        function openExportModal() {
            $('#exportModalPelamar').addClass('show');
            $('body').addClass('modal-open');
        }

        function closeExportModal() {
            $('#exportModalPelamar').removeClass('show');
            $('body').removeClass('modal-open');
        }

        $('#btnExportPelamar').click(function () {
            openExportModal();
        });

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

        $('#btnResetFilterPelamar').click(function () {
            $('#exportModalPelamar select').each(function () {
                $(this).val('');
            });
            $('#exportModalPelamar input[type="date"]').each(function () {
                $(this).val('');
            });
            $('#filterKota').empty().append('<option value="">Semua Kota</option>');
            $('#filterProvinsi').trigger('change');
        });

        $('.modal-cancel').click(function () {
            if ($(this).closest('#exportModalPelamar').length) {
                closeExportModal();
            }
        });

        function exportPelamarData(format) {
            var filters = {
                provinsi: $('#filterProvinsi').val(),
                kota: $('#filterKota').val(),
                jk: $('#filterJk').val(),
                id_lowongan: $('#filterLowongan').val(),
                tanggal_lahir_min: $('#filterTanggalLahirMin').val(),
                tanggal_lahir_max: $('#filterTanggalLahirMax').val(),
                status_lamaran: $('#filterStatusLamaran').val(),
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
                success: function (res) {
                    $(btnId).html(originalText).prop('disabled', false);
                    if (res.success) {
                        if (res.data_count > 0) {
                            var downloadUrl = 'download.php?file=' + encodeURIComponent(res.download_url.replace('exports/', ''));
                            window.location.href = downloadUrl;
                            showNotification('success', `Berhasil export ${res.data_count} data pelamar`, 'Export Berhasil');
                            closeExportModal();
                        } else {
                            showNotification('error', 'Tidak ada data pelamar yang sesuai dengan filter yang dipilih', 'Tidak Ada Data');
                        }
                    } else {
                        showNotification('error', res.message || 'Gagal melakukan export', 'Export Gagal');
                    }
                },
                error: function (xhr, status, error) {
                    $(btnId).html(originalText).prop('disabled', false);
                    showNotification('error', 'Terjadi kesalahan saat export data', 'Error');
                }
            });
        }

        $('#btnExportCSV').click(function () { exportPelamarData('csv'); });
        $('#btnExportXLS').click(function () { exportPelamarData('xls'); });
        $('#btnExportPDF').click(function () { exportPelamarData('pdf'); });
    </script>

</body>

</html>