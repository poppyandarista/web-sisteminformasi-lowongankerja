<?php
require_once 'session_check.php';
include 'koneksi.php';

$db = new database();

$data_pelamar = $db->tampil_data_pelamar();

// Tangani hapus
if (isset($_GET['action']) && $_GET['action'] == 'hapus' && isset($_GET['id'])) {
  $id = $_GET['id'];

  if (is_numeric($id)) {
    if ($db->hapus_pelamar($id)) {
      echo json_encode([
        'success' => true,
        'message' => 'Data pelamar berhasil dihapus'
      ]);
      exit();
    } else {
      echo json_encode([
        'success' => false,
        'message' => 'Gagal menghapus data pelamar'
      ]);
      exit();
    }
  } else {
    echo json_encode([
      'success' => false,
      'message' => 'ID tidak valid'
    ]);
    exit();
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
  <title>Data Pelamar | LinkUp</title>
  <link rel="icon" type="image/png" href="src/images/logo/favicon.png">
  <link href="style.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.tailwindcss.css">
  <style>
    /* Alert System Styles - Sesuai Template */
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

    .alert-message {
      font-size: 13px;
      line-height: 1.4;
    }

    .alert-close {
      background: none;
      border: none;
      cursor: pointer;
      padding: 0;
      margin-left: 8px;
      opacity: 0.7;
      transition: opacity 0.2s;
    }

    .alert-close:hover {
      opacity: 1;
    }

    /* Dark mode support */
    .dark .alert-item {
      background: #1f2937;
      color: #f9fafb;
    }

    .dark .alert-item.success {
      background: #064e3b;
      border-left-color: #10b981;
    }

    .dark .alert-item.error {
      background: #7f1d1d;
      border-left-color: #ef4444;
    }

    .dark .alert-item.warning {
      background: #78350f;
      border-left-color: #f59e0b;
    }

    .dark .alert-item.info {
      background: #1e3a8a;
      border-left-color: #3b82f6;
    }

    /* COPY PASTE DARI dataperusahaan.php */
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

    /* Modal background color */
    .modal-container .absolute.inset-0 {
      background-color: rgba(0, 0, 0, 0.2) !important;
    }

    /* Avatar styling */
    .avatar-pelamar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid #e5e7eb;
    }

    .avatar-placeholder {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      font-size: 16px;
      border: 2px solid #e5e7eb;
    }

    /* Spinner untuk loading */
    .spinner {
      display: inline-block;
      width: 16px;
      height: 16px;
      border: 2px solid rgba(255, 255, 255, 0.3);
      border-radius: 50%;
      border-top-color: #fff;
      animation: spin 1s ease-in-out infinite;
      margin-right: 8px;
    }

    @keyframes spin {
      to {
        transform: rotate(360deg);
      }
    }

    /* DataTables styling */
    .dt-length,
    .dt-search,
    .dt-info,
    .dt-paging {
      font-size: 0.75rem !important;
    }

    .dt-length select,
    .dt-search input {
      height: 1.75rem !important;
      font-size: 0.75rem !important;
    }

    .dt-paging .dt-paging-button {
      min-height: 1.5rem !important;
      height: 1.5rem !important;
    }

    /* Tambahan untuk modal edit */
    #modalEditPelamar .modal-content {
      width: 500px !important;
      max-width: 95vw;
      max-height: 90vh;
    }

    /* Tambahkan ke CSS yang sudah ada */
    #modalEditPelamar .modal-content {
      width: 600px !important;
      /* Lebarkan modal */
      max-width: 95vw;
      max-height: 90vh;
      overflow-y: auto;
    }

    #modalEditPelamar .modal-content::-webkit-scrollbar {
      width: 6px;
    }

    #modalEditPelamar .modal-content::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 3px;
    }

    #modalEditPelamar .modal-content::-webkit-scrollbar-thumb {
      background: #c1c1c1;
      border-radius: 3px;
    }

    /* ========== IMPROVED MODAL DETAIL ========== */
    #modalDetailPelamar .modal-content {
      width: 700px !important;
      /* Lebarkan modal detail */
      max-width: 95vw;
      max-height: 85vh;
      /* Kurangi sedikit untuk tidak terlalu tinggi */
      overflow: hidden;
      /* Hilangkan scroll dari modal utama */
    }

    .detail-tabs {
      display: flex;
      border-bottom: 1px solid #e5e7eb;
      margin-bottom: 1rem;
      overflow-x: auto;
    }

    .detail-tab {
      padding: 0.75rem 1rem;
      background: none;
      border: none;
      font-size: 0.875rem;
      font-weight: 500;
      color: #6b7280;
      cursor: pointer;
      border-bottom: 2px solid transparent;
      transition: all 0.2s;
      white-space: nowrap;
    }

    .detail-tab:hover {
      color: #374151;
      background-color: #f9fafb;
    }

    .detail-tab.active {
      color: #2563eb;
      border-bottom-color: #2563eb;
      background-color: #eff6ff;
    }

    .dark .detail-tab {
      color: #9ca3af;
    }

    .dark .detail-tab:hover {
      color: #d1d5db;
      background-color: #374151;
    }

    .dark .detail-tab.active {
      color: #60a5fa;
      border-bottom-color: #60a5fa;
      background-color: #1e3a8a;
    }

    .detail-tab-content {
      max-height: calc(75vh - 150px);
      /* Tinggi maksimal untuk konten tab */
      overflow-y: auto;
      padding-right: 4px;
    }

    .detail-tab-content::-webkit-scrollbar {
      width: 6px;
    }

    .detail-tab-content::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 3px;
    }

    .dark .detail-tab-content::-webkit-scrollbar-track {
      background: #374151;
    }

    .detail-tab-content::-webkit-scrollbar-thumb {
      background: #c1c1c1;
      border-radius: 3px;
    }

    .dark .detail-tab-content::-webkit-scrollbar-thumb {
      background: #4b5563;
    }

    /* Info grid yang lebih rapi */
    .info-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 0.75rem;
    }

    .info-card {
      background: #f9fafb;
      border: 1px solid #e5e7eb;
      border-radius: 0.5rem;
      padding: 0.75rem;
      transition: all 0.2s;
    }

    .dark .info-card {
      background: #374151;
      border-color: #4b5563;
    }

    .info-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .info-label {
      font-size: 0.75rem;
      color: #6b7280;
      margin-bottom: 0.25rem;
      font-weight: 500;
    }

    .dark .info-label {
      color: #9ca3af;
    }

    .info-value {
      font-size: 0.875rem;
      color: #111827;
      font-weight: 500;
      line-height: 1.4;
    }

    .dark .info-value {
      color: #f9fafb;
    }

    .info-value a {
      color: #2563eb;
      text-decoration: none;
      transition: color 0.2s;
    }

    .dark .info-value a {
      color: #60a5fa;
    }

    .info-value a:hover {
      text-decoration: underline;
    }

    /* Text area untuk deskripsi panjang */
    .long-text {
      max-height: 120px;
      overflow-y: auto;
      font-size: 0.875rem;
      line-height: 1.5;
      color: #4b5563;
    }

    .dark .long-text {
      color: #d1d5db;
    }

    /* Social media badges */
    .social-badges {
      display: flex;
      flex-wrap: wrap;
      gap: 0.5rem;
      margin-top: 0.5rem;
    }

    .social-badge {
      display: inline-flex;
      align-items: center;
      gap: 0.25rem;
      padding: 0.25rem 0.5rem;
      border-radius: 0.375rem;
      font-size: 0.75rem;
      font-weight: 500;
      text-decoration: none;
      transition: all 0.2s;
    }

    .social-badge:hover {
      transform: translateY(-1px);
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .social-badge.instagram {
      background: linear-gradient(45deg, #833AB4, #E1306C, #F77737);
      color: white;
    }

    .social-badge.facebook {
      background: #1877F2;
      color: white;
    }

    .social-badge.linkedin {
      background: #0A66C2;
      color: white;
    }

    /* Portfolio section */
    .portfolio-item {
      border: 1px solid #e5e7eb;
      border-radius: 0.5rem;
      padding: 0.75rem;
      margin-bottom: 0.5rem;
      background: white;
    }

    .dark .portfolio-item {
      background: #374151;
      border-color: #4b5563;
    }

    /* Profile header */
    .profile-header {
      text-align: center;
      padding: 1rem 0;
      border-bottom: 1px solid #e5e7eb;
      margin-bottom: 1rem;
    }

    .dark .profile-header {
      border-bottom-color: #4b5563;
    }

    .profile-avatar {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid white;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .dark .profile-avatar {
      border-color: #374151;
    }

    .profile-initials {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2rem;
      font-weight: bold;
      color: white;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      margin: 0 auto;
      border: 3px solid white;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .dark .profile-initials {
      border-color: #374151;
    }

    /* Tab content yang tersembunyi */
    .tab-pane {
      display: none;
    }

    .tab-pane.active {
      display: block;
    }

    /* ========== FILE UPLOAD STYLING ========== */
    .file-upload-container {
      margin-top: 0.5rem;
    }

    .file-upload-box {
      border: 2px dashed #d1d5db;
      border-radius: 0.5rem;
      padding: 1rem;
      text-align: center;
      background: #f9fafb;
      cursor: pointer;
      transition: all 0.2s;
    }

    .dark .file-upload-box {
      border-color: #4b5563;
      background: #374151;
    }

    .file-upload-box:hover {
      border-color: #2563eb;
      background: #eff6ff;
    }

    .dark .file-upload-box:hover {
      border-color: #60a5fa;
      background: #1e3a8a;
    }

    .file-upload-icon {
      width: 48px;
      height: 48px;
      margin: 0 auto 0.5rem;
      color: #9ca3af;
    }

    .file-upload-text {
      font-size: 0.875rem;
      color: #6b7280;
      margin-bottom: 0.25rem;
    }

    .file-upload-hint {
      font-size: 0.75rem;
      color: #9ca3af;
    }

    .file-preview-container {
      margin-top: 1rem;
    }

    .current-photo-container {
      text-align: center;
      margin-bottom: 1rem;
    }

    .photo-preview {
      width: 150px;
      height: 150px;
      border-radius: 0.5rem;
      object-fit: cover;
      border: 2px solid #e5e7eb;
      margin: 0 auto;
    }

    .dark .photo-preview {
      border-color: #4b5563;
    }

    .file-name-box {
      display: flex;
      align-items: center;
      justify-content: space-between;
      background: #f3f4f6;
      border: 1px solid #e5e7eb;
      border-radius: 0.375rem;
      padding: 0.5rem 0.75rem;
      margin-top: 0.5rem;
    }

    .dark .file-name-box {
      background: #1f2937;
      border-color: #374151;
    }

    .file-name-text {
      font-size: 0.875rem;
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

    .file-error {
      font-size: 0.75rem;
      margin-top: 0.25rem;
      min-height: 1rem;
    }

    .text-success {
      color: #10b981 !important;
    }

    .text-error {
      color: #ef4444 !important;
    }

    /* ========== IMPROVED PROFILE HEADER ========== */
    .profile-header {
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      padding: 1.5rem 0;
      border-bottom: 1px solid #e5e7eb;
      margin-bottom: 1.5rem;
      position: relative;
    }

    .dark .profile-header {
      border-bottom-color: #4b5563;
    }

    /* Foto profil di tengah */
    .profile-avatar {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      object-fit: cover;
      border: 4px solid white;
      box-shadow: 0 6px 12px -1px rgba(0, 0, 0, 0.1);
      margin-bottom: 1rem;
      position: relative;
      z-index: 1;
    }

    .dark .profile-avatar {
      border-color: #374151;
      box-shadow: 0 6px 12px -1px rgba(0, 0, 0, 0.3);
    }

    .profile-initials {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2.5rem;
      font-weight: bold;
      color: white;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      margin-bottom: 1rem;
      border: 4px solid white;
      box-shadow: 0 6px 12px -1px rgba(0, 0, 0, 0.1);
    }

    .dark .profile-initials {
      border-color: #374151;
    }

    /* Nama di bawah foto */
    .profile-name-container {
      text-align: center;
      width: 100%;
    }

    .profile-name {
      font-size: 1.5rem;
      font-weight: bold;
      color: #111827;
      margin-bottom: 0.25rem;
      line-height: 1.2;
    }

    .dark .profile-name {
      color: #f9fafb;
    }

    .profile-meta {
      font-size: 0.875rem;
      color: #6b7280;
      margin-bottom: 0.5rem;
    }

    .dark .profile-meta {
      color: #9ca3af;
    }

    /* ========== IMPROVED TAB CONTENT ========== */
    .detail-tab-content {
      max-height: calc(75vh - 200px);
      overflow-y: auto;
      padding-right: 8px;
    }

    /* Prestasi dan bagian yang panjang */
    .prestasi-section {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 0.75rem;
      padding: 1.25rem;
      margin-bottom: 1rem;
      transition: all 0.3s ease;
    }

    .dark .prestasi-section {
      background: #1e293b;
      border-color: #334155;
    }

    .prestasi-section:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .prestasi-header {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      margin-bottom: 1rem;
    }

    .prestasi-icon {
      width: 40px;
      height: 40px;
      border-radius: 10px;
      background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
    }

    .dark .prestasi-icon {
      background: linear-gradient(135deg, #60a5fa 0%, #2563eb 100%);
    }

    .prestasi-title {
      font-size: 1.125rem;
      font-weight: 600;
      color: #1e293b;
    }

    .dark .prestasi-title {
      color: #f1f5f9;
    }

    /* Konten prestasi yang panjang */
    .prestasi-content {
      max-height: 200px;
      overflow-y: auto;
      padding-right: 8px;
      line-height: 1.6;
      color: #475569;
      font-size: 0.9375rem;
    }

    .dark .prestasi-content {
      color: #cbd5e1;
    }

    .prestasi-content::-webkit-scrollbar {
      width: 6px;
    }

    .prestasi-content::-webkit-scrollbar-track {
      background: #f1f5f9;
      border-radius: 3px;
    }

    .dark .prestasi-content::-webkit-scrollbar-track {
      background: #334155;
    }

    .prestasi-content::-webkit-scrollbar-thumb {
      background: #94a3b8;
      border-radius: 3px;
    }

    .dark .prestasi-content::-webkit-scrollbar-thumb {
      background: #475569;
    }

    /* ========== IMPROVED MODAL LAYOUT ========== */
    #modalDetailPelamar .modal-content {
      width: 750px !important;
      max-width: 95vw;
      max-height: 85vh;
      display: flex;
      flex-direction: column;
    }

    .modal-body {
      flex: 1;
      overflow: hidden;
      display: flex;
      flex-direction: column;
    }

    /* Responsive untuk mobile */
    @media (max-width: 768px) {
      #modalDetailPelamar .modal-content {
        width: 95vw !important;
        margin: 0 10px;
        max-height: 90vh;
      }

      .profile-avatar,
      .profile-initials {
        width: 80px;
        height: 80px;
        font-size: 2rem;
      }

      .profile-name {
        font-size: 1.25rem;
      }

      .detail-tab-content {
        max-height: calc(70vh - 180px);
      }
    }

    /* ========== CUSTOM SCROLLBAR ========== */
    .custom-scrollbar::-webkit-scrollbar {
      width: 8px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 4px;
    }

    .dark .custom-scrollbar::-webkit-scrollbar-track {
      background: #374151;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
      background: #c1c1c1;
      border-radius: 4px;
    }

    .dark .custom-scrollbar::-webkit-scrollbar-thumb {
      background: #4b5563;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
      background: #a1a1a1;
    }

    .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
      background: #6b7280;
    }

    /* Modal container untuk scroll yang lebih baik */
    .modal-content {
      animation: modalSlideIn 0.3s ease-out;
    }

    @keyframes modalSlideIn {
      from {
        opacity: 0;
        transform: translateY(-20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Pastikan konten modal tidak overflow */
    #modalDetailPelamar .modal-body {
      min-height: 0;
    }

    /* ========== IMPROVED SPACING FOR PROFILE SECTIONS ========== */
    .profile-section {
      margin-bottom: 1.5rem;
    }

    .profile-section:last-child {
      margin-bottom: 0;
    }

    /* Jarak antar card di dalam tab profil */
    #tab-profile .info-card {
      margin-bottom: 1rem;
      border-radius: 0.75rem;
      border: 1px solid #e5e7eb;
      background: white;
      padding: 1.25rem;
      transition: all 0.2s ease;
    }

    .dark #tab-profile .info-card {
      background: #374151;
      border-color: #4b5563;
    }

    #tab-profile .info-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .dark #tab-profile .info-card:hover {
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2);
    }

    /* Jarak antar bagian di tab profil */
    #tab-profile .space-y-4>*+* {
      margin-top: 1.5rem !important;
    }

    /* ========== IMPROVED PORTFOLIO SECTION ========== */
    #tab-portfolio .info-card {
      margin-bottom: 1.5rem;
      border-radius: 0.75rem;
      border: 1px solid #e5e7eb;
      background: white;
      padding: 1.25rem;
    }

    .dark #tab-portfolio .info-card {
      background: #374151;
      border-color: #4b5563;
    }

    #tab-portfolio .space-y-4>*+* {
      margin-top: 1.5rem !important;
    }

    /* ========== IMPROVED SOCIAL MEDIA BUTTONS ========== */
    .compact-social-grid {
      display: flex;
      flex-wrap: wrap;
      gap: 0.75rem;
      justify-content: center;
      align-items: center;
    }

    .compact-social-badge {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.375rem;
      padding: 0.5rem 0.75rem;
      border-radius: 0.5rem;
      font-size: 0.75rem;
      font-weight: 500;
      text-decoration: none;
      transition: all 0.2s ease;
      min-width: 120px;
      border: 1px solid transparent;
    }

    .compact-social-badge:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    /* Instagram badge compact */
    .compact-social-badge.instagram {
      background: linear-gradient(45deg, #833AB4, #E1306C, #F77737);
      color: white;
    }

    .compact-social-badge.instagram:hover {
      background: linear-gradient(45deg, #6a2d91, #c2265c, #d8642b);
    }

    /* Facebook badge compact */
    .compact-social-badge.facebook {
      background: #1877F2;
      color: white;
    }

    .compact-social-badge.facebook:hover {
      background: #1464d1;
    }

    /* LinkedIn badge compact */
    .compact-social-badge.linkedin {
      background: #0A66C2;
      color: white;
    }

    .compact-social-badge.linkedin:hover {
      background: #0957a8;
    }

    .compact-social-badge svg {
      width: 14px;
      height: 14px;
    }

    /* Center social media section */
    .social-section {
      text-align: center;
      padding: 1rem 0;
    }

    /* ========== IMPROVED PRESTASI SECTION ========== */
    #tab-profile .prestasi-section {
      margin-top: 1.5rem;
      margin-bottom: 0.5rem;
    }

    /* ========== NO DATA STYLING ========== */
    .no-data-message {
      text-align: center;
      padding: 3rem 1rem;
      color: #6b7280;
      background: #f9fafb;
      border-radius: 0.75rem;
      border: 1px dashed #d1d5db;
    }

    .dark .no-data-message {
      background: #374151;
      color: #9ca3af;
      border-color: #4b5563;
    }

    .no-data-message svg {
      width: 48px;
      height: 48px;
      margin-bottom: 1rem;
      opacity: 0.5;
    }

    .no-data-message p {
      margin-bottom: 0.5rem;
    }

    .no-data-message .text-sm {
      font-size: 0.875rem;
    }

    /* ========== IMPROVED HORIZONTAL INFO LAYOUT ========== */
    .horizontal-info-card {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      background: #f9fafb;
      border: 1px solid #e5e7eb;
      border-radius: 0.75rem;
      padding: 1rem;
      transition: all 0.2s ease;
    }

    .dark .horizontal-info-card {
      background: #374151;
      border-color: #4b5563;
    }

    .horizontal-info-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .dark .horizontal-info-card:hover {
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2);
    }

    .horizontal-info-icon {
      flex-shrink: 0;
      width: 40px;
      height: 40px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
    }

    /* Warna berbeda untuk setiap ikon */
    .horizontal-info-icon.email {
      background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    }

    .horizontal-info-icon.username {
      background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    }

    .horizontal-info-icon.phone {
      background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .horizontal-info-icon.birthday {
      background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    .horizontal-info-icon.location {
      background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    }

    .horizontal-info-content {
      flex: 1;
      min-width: 0;
      /* Untuk mencegah overflow */
    }

    .horizontal-info-label {
      font-size: 0.75rem;
      color: #6b7280;
      margin-bottom: 0.25rem;
      font-weight: 500;
    }

    .dark .horizontal-info-label {
      color: #9ca3af;
    }

    .horizontal-info-value {
      font-size: 0.9375rem;
      color: #111827;
      font-weight: 500;
      line-height: 1.4;
      word-break: break-word;
      /* Untuk wrap text yang panjang */
    }

    .dark .horizontal-info-value {
      color: #f9fafb;
    }

    .horizontal-info-value a {
      color: #2563eb;
      text-decoration: none;
      transition: color 0.2s;
    }

    .dark .horizontal-info-value a {
      color: #60a5fa;
    }

    .horizontal-info-value a:hover {
      text-decoration: underline;
    }

    /* Responsif untuk layout horizontal */
    @media (max-width: 640px) {
      .horizontal-info-card {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
      }

      .horizontal-info-icon {
        width: 36px;
        height: 36px;
      }
    }

    /* ========== IMPROVED SPACING FOR INFORMATION BOXES ========== */
    .spaced-info-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 1rem;
      margin-top: 1rem;
    }

    .spaced-info-card {
      background: white;
      border: 1px solid #e5e7eb;
      border-radius: 0.75rem;
      padding: 1.25rem;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .dark .spaced-info-card {
      background: #374151;
      border-color: #4b5563;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
    }

    .spaced-info-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .spaced-info-card .info-header {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      margin-bottom: 0.75rem;
      padding-bottom: 0.75rem;
      border-bottom: 1px solid #f3f4f6;
    }

    .dark .spaced-info-card .info-header {
      border-bottom-color: #4b5563;
    }

    .spaced-info-icon {
      width: 40px;
      height: 40px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    /* Warna icon untuk kategori berbeda */
    .spaced-info-icon.email {
      background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    }

    .spaced-info-icon.phone {
      background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .spaced-info-icon.location {
      background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    .spaced-info-icon.birth {
      background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    }

    .spaced-info-icon.username {
      background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);
    }

    .spaced-info-icon.description {
      background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
    }

    .spaced-info-icon.skills {
      background: linear-gradient(135deg, #84cc16 0%, #65a30d 100%);
    }

    .spaced-info-icon.history {
      background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
    }

    .spaced-info-icon.portfolio {
      background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    }

    .spaced-info-icon.link {
      background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    }

    .spaced-info-icon.achievement {
      background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    .spaced-info-icon.social {
      background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    }

    .spaced-info-icon svg {
      width: 20px;
      height: 20px;
      color: white;
    }

    .spaced-info-title {
      font-size: 0.875rem;
      font-weight: 600;
      color: #374151;
      margin: 0;
    }

    .dark .spaced-info-title {
      color: #f9fafb;
    }

    .spaced-info-content {
      font-size: 0.875rem;
      line-height: 1.5;
      color: #4b5563;
      word-break: break-word;
    }

    .dark .spaced-info-content {
      color: #d1d5db;
    }

    .spaced-info-content a {
      color: #2563eb;
      text-decoration: none;
      transition: color 0.2s;
      display: inline-flex;
      align-items: center;
      gap: 0.25rem;
    }

    .dark .spaced-info-content a {
      color: #60a5fa;
    }

    .spaced-info-content a:hover {
      text-decoration: underline;
    }

    /* Untuk konten yang panjang */
    .spaced-info-content.long-text {
      max-height: 200px;
      overflow-y: auto;
      padding-right: 8px;
      line-height: 1.6;
    }

    .spaced-info-content.long-text::-webkit-scrollbar {
      width: 6px;
    }

    .spaced-info-content.long-text::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 3px;
    }

    .dark .spaced-info-content.long-text::-webkit-scrollbar-track {
      background: #4b5563;
    }

    .spaced-info-content.long-text::-webkit-scrollbar-thumb {
      background: #c1c1c1;
      border-radius: 3px;
    }

    .dark .spaced-info-content.long-text::-webkit-scrollbar-thumb {
      background: #6b7280;
    }

    /* Badge untuk status atau label */
    .info-badge {
      display: inline-flex;
      align-items: center;
      gap: 0.25rem;
      padding: 0.25rem 0.5rem;
      background-color: #f3f4f6;
      border-radius: 0.375rem;
      font-size: 0.75rem;
      font-weight: 500;
      color: #374151;
      margin-top: 0.5rem;
    }

    .dark .info-badge {
      background-color: #4b5563;
      color: #f9fafb;
    }

    /* ========== ENHANCED TAB CONTENT ========== */
    .enhanced-tab-content {
      padding: 1.5rem;
      background: #f9fafb;
      border-radius: 0.75rem;
      border: 1px solid #e5e7eb;
    }

    .dark .enhanced-tab-content {
      background: #1f2937;
      border-color: #374151;
    }

    /* Grid untuk informasi dasar */
    .basic-info-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1rem;
      margin-bottom: 1.5rem;
    }

    /* Section dengan title */
    .info-section {
      margin-bottom: 2rem;
    }

    .info-section-title {
      font-size: 1rem;
      font-weight: 600;
      color: #111827;
      margin-bottom: 1rem;
      padding-bottom: 0.5rem;
      border-bottom: 2px solid #e5e7eb;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .dark .info-section-title {
      color: #f9fafb;
      border-bottom-color: #4b5563;
    }

    /* Responsive adjustments */
    @media (max-width: 640px) {
      .spaced-info-grid {
        grid-template-columns: 1fr;
      }

      .basic-info-grid {
        grid-template-columns: 1fr;
      }

      .spaced-info-card {
        padding: 1rem;
      }
    }

    /* ========== IMPROVED TABLE SPACING ========== */
    #pelamarTable tbody tr td {
      padding-top: 1rem;
      padding-bottom: 1rem;
    }

    #pelamarTable tbody tr:hover {
      background-color: #f8fafc;
    }

    .dark #pelamarTable tbody tr:hover {
      background-color: #1e293b;
    }

    /* Spacing khusus untuk kolom email dan kontak */
    #pelamarTable tbody tr td:nth-child(3),
    #pelamarTable tbody tr td:nth-child(4) {
      min-width: 200px;
    }

    /* Card style untuk konten dalam tabel */
    .table-card {
      background: #f9fafb;
      border-radius: 0.5rem;
      padding: 0.5rem;
      margin-bottom: 0.25rem;
    }

    .dark .table-card {
      background: #374151;
    }

    /* ========== SMALLER PORTFOLIO CARD ========== */
    .compact-spaced-info-card {
      background: white;
      border: 1px solid #e5e7eb;
      border-radius: 0.5rem;
      padding: 0.75rem;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
      box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
      margin-bottom: 0.5rem;
    }

    .dark .compact-spaced-info-card {
      background: #374151;
      border-color: #4b5563;
      box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    }

    .compact-spaced-info-card:hover {
      transform: translateY(-1px);
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .compact-spaced-info-card .info-header {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      margin-bottom: 0.5rem;
      padding-bottom: 0.5rem;
      border-bottom: 1px solid #f3f4f6;
    }

    .dark .compact-spaced-info-card .info-header {
      border-bottom-color: #4b5563;
    }

    .compact-spaced-info-icon {
      width: 32px;
      height: 32px;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .compact-spaced-info-icon svg {
      width: 16px;
      height: 16px;
      color: white;
    }

    .compact-spaced-info-title {
      font-size: 0.8125rem;
      font-weight: 600;
      color: #374151;
      margin: 0;
    }

    .dark .compact-spaced-info-title {
      color: #f9fafb;
    }

    .compact-spaced-info-content {
      font-size: 0.8125rem;
      line-height: 1.4;
      color: #4b5563;
      word-break: break-word;
    }

    .dark .compact-spaced-info-content {
      color: #d1d5db;
    }

    /* Icon khusus untuk jenis kelamin */
    .spaced-info-icon.gender {
      background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);
    }

    /* Icon khusus untuk Instagram */
    .spaced-info-icon.instagram-gradient {
      background: linear-gradient(45deg, #833AB4, #E1306C, #F77737);
    }

    /* Social media icon colors */
    .social-media-icon.instagram {
      background: linear-gradient(45deg, #833AB4, #E1306C, #F77737);
    }

    .social-media-icon.facebook {
      background: #1877F2;
    }

    .social-media-icon.linkedin {
      background: #0A66C2;
    }

    /* Smaller social media cards */
    .compact-social-card {
      background: white;
      border: 1px solid #e5e7eb;
      border-radius: 0.5rem;
      padding: 0.75rem;
      transition: all 0.3s ease;
    }

    .dark .compact-social-card {
      background: #374151;
      border-color: #4b5563;
    }

    .compact-social-card:hover {
      transform: translateY(-1px);
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    /* ========== INLINE SOCIAL MEDIA LAYOUT ========== */
    .inline-social-media {
      display: flex;
      flex-wrap: wrap;
      gap: 0.75rem;
      margin-top: 0.5rem;
    }

    .inline-social-item {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.5rem 0.75rem;
      border-radius: 0.5rem;
      background: white;
      border: 1px solid #e5e7eb;
      transition: all 0.3s ease;
      flex: 1;
      min-width: 120px;
      max-width: 180px;
    }

    .dark .inline-social-item {
      background: #374151;
      border-color: #4b5563;
    }

    .inline-social-item:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .inline-social-icon {
      width: 32px;
      height: 32px;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .inline-social-icon.instagram {
      background: linear-gradient(45deg, #833AB4, #E1306C, #F77737);
    }

    .inline-social-icon.facebook {
      background: #1877F2;
    }

    .inline-social-icon.linkedin {
      background: #0A66C2;
    }

    .inline-social-icon svg {
      width: 16px;
      height: 16px;
      color: white;
    }

    .inline-social-content {
      flex: 1;
      min-width: 0;
    }

    .inline-social-platform {
      font-size: 0.75rem;
      font-weight: 600;
      color: #374151;
      margin-bottom: 0.125rem;
    }

    .dark .inline-social-platform {
      color: #f9fafb;
    }

    .inline-social-link {
      font-size: 0.6875rem;
      color: #6b7280;
      text-decoration: none;
      display: block;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .dark .inline-social-link {
      color: #9ca3af;
    }

    .inline-social-link:hover {
      color: #2563eb;
      text-decoration: underline;
    }

    .dark .inline-social-link:hover {
      color: #60a5fa;
    }

    /* Responsive untuk media sosial inline */
    @media (max-width: 640px) {
      .inline-social-media {
        gap: 0.5rem;
      }

      .inline-social-item {
        min-width: 100px;
        padding: 0.375rem 0.5rem;
      }
    }

    /* Untuk 3 item dalam satu baris */
    .inline-social-3-col {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 0.75rem;
    }

    @media (max-width: 768px) {
      .inline-social-3-col {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    @media (max-width: 480px) {
      .inline-social-3-col {
        grid-template-columns: 1fr;
      }
    }

    /* ==================== RESPONSIVE TABEL PELAMAR ==================== */
    /* Tampilan Desktop - Normal */
    #pelamarTable {
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
      #pelamarTable {
        min-width: 768px;
        font-size: 0.875rem;
      }

      #pelamarTable th,
      #pelamarTable td {
        padding: 0.5rem 0.75rem !important;
      }
    }

    /* Tampilan Mobile (<= 768px) */
    @media (max-width: 768px) {
      #pelamarTable {
        min-width: 600px;
        font-size: 0.75rem;
      }

      #pelamarTable th,
      #pelamarTable td {
        padding: 0.375rem 0.5rem !important;
      }

      .avatar-pelamar,
      .avatar-placeholder {
        width: 32px !important;
        height: 32px !important;
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
      #pelamarTable {
        min-width: 500px;
      }

      .flex.items-center.gap-2 {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.25rem !important;
      }

      .avatar-pelamar,
      .avatar-placeholder {
        width: 28px !important;
        height: 28px !important;
      }
    }

    /* ========== PERBAIKAN MODAL EXPORT PELAMAR ========== */
    #modalExportPelamar .modal-content {
      width: 500px !important;
      max-width: 95vw;
      max-height: 85vh;
      /* Kurangi dari 90vh ke 85vh */
    }

    /* Pastikan body modal bisa scroll */
    #modalExportPelamar .modal-body {
      flex: 1;
      overflow-y: auto;
      min-height: 0;
      /* Penting untuk flexbox scrolling */
    }

    /* Form dalam modal */
    #exportFormPelamar {
      max-height: none;
      overflow: visible;
    }

    /* Bagian filter dengan grid 2 kolom - perbaiki tinggi */
    #modalExportPelamar .grid.grid-cols-2 {
      max-height: 300px;
      /* Batasi tinggi maksimum */
      overflow-y: auto;
      padding-right: 4px;
    }

    /* ========== PERBAIKAN TOMBOL EXPORT ========== */
    button[onclick*="modalExportPelamar"] {
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

    button[onclick*="modalExportPelamar"]:hover {
      background-color: #15803d !important;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
    }

    button[onclick*="modalExportPelamar"] svg {
      width: 1rem !important;
      height: 1rem !important;
    }

    /* Custom scrollbar untuk modal */
    #modalExportPelamar .modal-body::-webkit-scrollbar {
      width: 6px;
    }

    #modalExportPelamar .modal-body::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 3px;
    }

    .dark #modalExportPelamar .modal-body::-webkit-scrollbar-track {
      background: #374151;
    }

    #modalExportPelamar .modal-body::-webkit-scrollbar-thumb {
      background: #c1c1c1;
      border-radius: 3px;
    }

    .dark #modalExportPelamar .modal-body::-webkit-scrollbar-thumb {
      background: #4b5563;
    }

    /* Responsive untuk modal export */
    @media (max-width: 640px) {
      #modalExportPelamar .modal-content {
        width: 95vw !important;
        margin: 0 10px;
        max-height: 90vh;
      }

      #modalExportPelamar .grid.grid-cols-2 {
        grid-template-columns: 1fr !important;
        gap: 0.75rem;
      }
    }

    /* PERBAIKAN DROPDOWN TAMPILKAN DATA */
    .dt-length {
      position: relative;
      z-index: 10;
      margin-right: 1rem !important;
      min-width: 140px;
    }

    .dt-length select {
      appearance: none;
      -webkit-appearance: none;
      -moz-appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' stroke='currentColor' viewBox='0 0 24 24'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 0.5rem center;
      background-size: 1rem;
      padding-right: 2rem !important;
      min-width: 120px;
      cursor: pointer;
      line-height: 1.5;
      height: 2rem !important;
    }

    .dt-length select option {
      padding: 8px 12px !important;
      font-size: 13px !important;
      background-color: white;
      color: #333;
    }

    .dark .dt-length select option {
      background-color: #1f2937;
      color: #f3f4f6;
    }

    /* Perbaikan container tableControls */
    #tableControls {
      display: flex !important;
      flex-wrap: wrap !important;
      align-items: center !important;
      justify-content: space-between !important;
      gap: 1rem !important;
      min-height: 45px !important;
      margin-bottom: 0.75rem !important;
      position: relative;
      z-index: 5;
    }

    /* Perbaikan untuk search bar */
    .dt-search {
      max-width: 300px !important;
      min-width: 200px !important;
      flex: 0 1 auto !important;
    }

    .dt-search input {
      width: 100% !important;
      box-sizing: border-box !important;
    }

    /* Container tableControls yang lebih baik */
    #tableControls {
      display: flex !important;
      flex-wrap: wrap !important;
      align-items: center !important;
      justify-content: space-between !important;
      gap: 1rem !important;
      min-height: 45px !important;
      margin-bottom: 0.75rem !important;
      position: relative;
      z-index: 5;
    }

    /* Bagian kiri (dropdown) */
    #tableControls .flex.items-center:first-child {
      flex: 0 0 auto;
      margin-right: auto;
    }

    /* Bagian kanan (search) */
    #tableControls .flex.items-center:last-child {
      flex: 0 1 auto;
      max-width: 300px;
    }

    /* Responsive untuk mobile */
    @media (max-width: 768px) {
      #tableControls {
        flex-direction: row !important;
        flex-wrap: wrap !important;
      }

      .dt-search {
        max-width: 100% !important;
        width: 100% !important;
        margin-top: 0.5rem !important;
      }

      #tableControls .flex.items-center:last-child {
        max-width: 100%;
        width: 100%;
      }
    }

    /* Untuk memastikan search tidak terlalu panjang */
    .dt-search label {
      width: 100%;
      display: block !important;
    }

    .dt-search label input {
      width: 100% !important;
    }
  </style>
</head>

<body
  x-data="{ 
    page: 'dataPelamar', 'loaded': true, 'darkMode': false, 'stickyMenu': false, 'sidebarToggle': false, 'scrollTop': false, 'modalOpen': false }"
  x-init="
         darkMode = JSON.parse(localStorage.getItem('darkMode'));
         $watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)))"
  :class="{'dark bg-gray-900': darkMode === true}" class="relative"> <!-- Tambah class relative di sini -->

  <!-- ===== Preloader Start ===== -->
  <div x-show="loaded"
    x-init="window.addEventListener('DOMContentLoaded', () => {setTimeout(() => loaded = false, 500)})"
    class="fixed left-0 top-0 z-999999 flex h-screen w-screen items-center justify-center bg-white dark:bg-black">
    <div class="h-16 w-16 animate-spin rounded-full border-4 border-solid border-brand-500 border-t-transparent"></div>
  </div>
  <!-- ===== Preloader End ===== -->

  <!-- Alert Container -->
  <div id="alertContainer" class="alert-container"></div>

  <!-- Overlay untuk seluruh halaman - INI HARUS DILUAR #main-wrapper -->
  <div id="pageOverlay" class="page-overlay hidden" onclick="hideAllModals()"></div>

  <!-- ===== Page Wrapper Start ===== -->
  <div id="main-wrapper" class="flex h-screen overflow-hidden transition-all duration-300">
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
              Data Pelamar
            </h2>
            <nav>
              <ol class="flex items-center gap-2">
                <li><a class="font-medium" href="index.php">Home ></a></li>
                <li class="font-medium text-primary">Data Pelamar</li>
              </ol>
            </nav>
          </div>

          <!-- Tampilkan pesan sukses/error -->
          <?php if (isset($success)): ?>
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
              <?php echo $success; ?>
            </div>
          <?php endif; ?>

          <?php if (isset($error)): ?>
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
              <?php echo $error; ?>
            </div>
          <?php endif; ?>

          <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
            <div
              class="border-b border-stroke px-4 py-4 dark:border-strokedark sm:px-6 xl:px-7.5 flex justify-between items-center">
              <h3 class="font-medium text-black dark:text-white">Tabel Data Pelamar</h3>
              <button onclick="toggleModal('modalExportPelamar')"
                class="inline-flex items-center justify-center rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 transition-colors duration-200 gap-2 shadow-sm hover:shadow-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export
              </button>
            </div>
            <div class="p-4 sm:p-6 xl:p-7.5">
              <!-- DataTables Controls Container - Fixed -->
              <div id="tableControls" class="mb-2">
                <!-- Length Menu dan Search akan di-inject oleh DataTables di sini -->
              </div>

              <!-- Table Container dengan Horizontal Scroll -->
              <div class="table-wrapper overflow-x-auto">
                <table id="pelamarTable" class="w-full table-auto border-collapse text-left">
                  <thead>
                    <tr class="bg-gray-2 dark:bg-meta-4">
                      <th class="px-4 py-3 font-medium text-black dark:text-white">ID</th>
                      <th class="px-4 py-3 font-medium text-black dark:text-white">Pelamar</th>
                      <th class="px-4 py-3 font-medium text-black dark:text-white">Email & Username</th>
                      <th class="px-4 py-3 font-medium text-black dark:text-white">Kontak & Lokasi</th>
                      <th class="px-4 py-3 font-medium text-black dark:text-white">Detail</th>
                      <th class="px-4 py-3 font-medium text-black dark:text-white">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    if (!empty($data_pelamar)) {
                      foreach ($data_pelamar as $row) {
                        $format_id = "U" . sprintf("%04d", $row['id_user']);
                        $inisial = !empty($row['nama_user']) ? substr($row['nama_user'], 0, 2) :
                          (!empty($row['username_user']) ? substr($row['username_user'], 0, 2) : '??');
                        ?>
                        <tr class="border-b border-[#eee] dark:border-strokedark hover:bg-gray-50 dark:hover:bg-meta-4">
                          <td class="px-4 py-3">
                            <p class="text-black dark:text-white font-medium text-sm"><?php echo $format_id; ?></p>
                          </td>

                          <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                              <?php if (!empty($row['foto_user'])): ?>
                                <img src="src/images/user/<?php echo $row['foto_user']; ?>" alt="Foto Profil"
                                  class="avatar-pelamar">
                              <?php else: ?>
                                <div class="avatar-placeholder">
                                  <?php echo strtoupper($inisial); ?>
                                </div>
                              <?php endif; ?>
                              <div>
                                <h5 class="font-medium text-black dark:text-white text-sm">
                                  <?php echo !empty($row['nama_user']) ? htmlspecialchars($row['nama_user']) : 'Nama belum diisi'; ?>
                                </h5>
                                <?php if (!empty($row['jk_user'])): ?>
                                  <p class="text-xs text-gray-500">
                                    <?php echo $row['jk_user'] == 'L' ? 'Laki-laki' : ($row['jk_user'] == 'P' ? 'Perempuan' : '-'); ?>
                                  </p>
                                <?php endif; ?>
                              </div>
                            </div>
                          </td>

                          <td class="px-4 py-3">
                            <p class="text-sm text-black dark:text-white">
                              <?php echo $row['email_user']; ?>
                            </p>
                            <p class="text-xs text-gray-500">
                              <?php echo $row['username_user'] ?: 'No username'; ?>
                            </p>
                          </td>

                          <td class="px-4 py-3">
                            <p class="text-sm text-black dark:text-white">
                              <?php echo $row['nohp_user'] ?: '-'; ?>
                            </p>
                            <p class="text-xs text-gray-500">
                              <?php
                              $lokasi = '';
                              if (!empty($row['nama_kota']) && !empty($row['nama_provinsi'])) {
                                $lokasi = $row['nama_kota'] . ", " . $row['nama_provinsi'];
                              } elseif (!empty($row['nama_kota'])) {
                                $lokasi = $row['nama_kota'];
                              } elseif (!empty($row['nama_provinsi'])) {
                                $lokasi = $row['nama_provinsi'];
                              } else {
                                $lokasi = '-';
                              }
                              echo htmlspecialchars($lokasi);
                              ?>
                            </p>
                          </td>

                          <td class="px-4 py-3">
                            <button onclick="showDetailPelamar(<?php echo $row['id_user']; ?>)"
                              style="background-color: #06b6d4;"
                              class="inline-flex items-center gap-1 px-3 py-2 text-xs font-medium text-white transition rounded-lg hover:opacity-80">
                              <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                                <circle cx="12" cy="12" r="3" />
                              </svg>
                              Detail
                            </button>
                          </td>

                          <td class="px-4 py-3">
                            <div class="flex gap-2">
                              <!-- Tombol Edit -->
                              <button onclick="editPelamar(<?php echo $row['id_user']; ?>)"
                                style="background-color: #2563eb;"
                                class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium text-white transition rounded-lg hover:opacity-80">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20"
                                  fill="currentColor">
                                  <path
                                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                </svg>
                                Edit
                              </button>

                              <!-- Tombol Hapus -->
                              <button
                                onclick="showDeleteConfirmation(<?php echo $row['id_user']; ?>, '<?php echo addslashes($row['nama_user'] ?: $row['email_user']); ?>')"
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
                      echo "<tr><td colspan='6' class='text-center py-4'>Tidak ada data pelamar</td></tr>";
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

  <!-- ========== MODAL EDIT PELAMAR ========== -->
  <div id="modalEditPelamar" class="modal-container fixed inset-0 hidden items-center justify-center z-[9999]">
    <div class="absolute inset-0 bg-black/50" onclick="toggleModal('modalEditPelamar', false)"></div>
    <div class="modal-content relative bg-white dark:bg-boxdark rounded-lg shadow-xl w-full max-w-md mx-4">
      <div class="sticky top-0 z-10 bg-white dark:bg-boxdark border-b border-stroke dark:border-strokedark px-4 py-3">
        <div class="flex items-center justify-between">
          <div>
            <h5 class="text-base font-bold text-black dark:text-white">Edit Data Pelamar</h5>
            <p class="text-xs text-gray-500">Ubah data pelamar</p>
          </div>
          <button onclick="toggleModal('modalEditPelamar')" class="text-gray-400 hover:text-red-500 transition-colors">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M18 6L6 18M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>

      <div class="p-4 overflow-y-auto flex-1" id="editPelamarContent">
        <!-- Form akan diisi via JavaScript -->
      </div>
    </div>
  </div>

  <!-- ========== MODAL DETAIL PELAMAR ========== -->
  <div id="modalDetailPelamar" class="modal-container fixed inset-0 hidden items-center justify-center z-[9999]">
    <div class="absolute inset-0 bg-black/50" onclick="toggleModal('modalDetailPelamar', false)"></div>
    <div class="modal-content relative bg-white dark:bg-boxdark rounded-lg shadow-xl w-full max-w-md mx-4">
      <div
        class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-900 px-4 py-3 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
              <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
            </div>
            <div>
              <h5 class="text-sm font-bold text-gray-800 dark:text-white">Detail Pelamar</h5>
              <p class="text-xs text-gray-500 dark:text-gray-400" id="detailPelamarEmail"></p>
            </div>
          </div>
          <button onclick="toggleModal('modalDetailPelamar')"
            class="w-6 h-6 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 flex items-center justify-center transition-colors">
            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>

      <div class="p-4 overflow-y-auto flex-1 max-h-[calc(85vh-80px)]">
        <div id="detailPelamarContent">
          <!-- Detail akan diisi via JavaScript -->
        </div>
      </div>
    </div>
  </div>

  <!-- ========== MODAL HAPUS PELAMAR ========== -->
  <div id="modalHapusPelamar" class="modal-container fixed inset-0 hidden items-center justify-center z-[9999]">
    <div class="absolute inset-0 bg-black/50" onclick="toggleModal('modalHapusPelamar', false)"></div>
    <div class="modal-content relative bg-white dark:bg-boxdark rounded-lg shadow-xl w-full max-w-xs mx-4">
      <div class="p-4">
        <div class="mx-auto w-10 h-10 mb-2 rounded-full bg-red-100 flex items-center justify-center">
          <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.994-.833-2.764 0L4.406 16.5c-.77.833.192 2.5 1.732 2.5z" />
          </svg>
        </div>

        <div class="text-center mb-3">
          <h5 class="text-sm font-bold text-black dark:text-white mb-1">Konfirmasi Hapus</h5>
          <p class="text-xs text-gray-600 dark:text-gray-400" id="hapusMessagePelamar">
            Apakah Anda yakin ingin menghapus data ini?
          </p>
        </div>

        <div class="flex justify-center gap-2">
          <button type="button" onclick="toggleModal('modalHapusPelamar')"
            class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 rounded hover:bg-gray-200 transition-colors">
            Batal
          </button>
          <button type="button" id="confirmDeleteBtnPelamar" style="background-color: #dc2626;"
            class="inline-flex items-center gap-2 px-4 py-3 text-sm font-medium text-white transition rounded-lg hover:bg-red-700">
            Ya, Hapus
          </button>
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
    // ========== VARIABEL GLOBAL ==========
    let pelamarToDelete = null;
    let currentEditId = null;

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

    function toggleModal(modalId, show = true) {
      const modal = document.getElementById(modalId);
      const overlay = document.getElementById('pageOverlay');
      const body = document.body;

      if (show) {
        // Tutup semua modal lain
        document.querySelectorAll('.modal-container').forEach(m => {
          if (m.id !== modalId) {
            m.style.display = 'none';
            m.classList.remove('flex');
            m.classList.add('hidden');
          }
        });

        // Tampilkan overlay dan modal
        if (overlay) {
          overlay.classList.remove('hidden');
          overlay.classList.add('block');
        }

        modal.style.display = 'flex';
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        // Tambah class ke body untuk styling khusus
        body.classList.add('modal-open');

        // Nonaktifkan scroll di body
        body.style.overflow = 'hidden';

      } else {
        // Sembunyikan modal
        modal.style.display = 'none';
        modal.classList.remove('flex');
        modal.classList.add('hidden');

        // Cek apakah ada modal lain yang masih terbuka
        const anyModalOpen = Array.from(document.querySelectorAll('.modal-container'))
          .some(modal => modal.style.display === 'flex' || modal.classList.contains('flex'));

        if (!anyModalOpen && overlay) {
          overlay.classList.remove('block');
          overlay.classList.add('hidden');
          body.classList.remove('modal-open');
          body.style.overflow = 'auto';
        }
      }
    }

    // Di bagian bawah script, tambah ini:
    document.addEventListener('DOMContentLoaded', function () {
      // Overlay klik untuk tutup semua modal
      const overlay = document.getElementById('pageOverlay');
      if (overlay) {
        overlay.addEventListener('click', function (e) {
          if (e.target === this) {
            hideAllModals();
          }
        });
      }

      // ESC key untuk tutup modal
      document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
          hideAllModals();
        }
      });
    });

    function hideAllModals() {
      // Tutup semua modal
      document.querySelectorAll('.modal-container').forEach(modal => {
        modal.style.display = 'none';
        modal.classList.add('hidden');
        modal.classList.remove('flex');
      });

      // Sembunyikan overlay
      const overlay = document.getElementById('pageOverlay');
      if (overlay) {
        overlay.classList.add('hidden');
        overlay.classList.remove('block');
      }

      // Reset body
      document.body.classList.remove('modal-open');
      document.body.style.overflow = 'auto';
    }

    function editPelamar(id) {
      console.log('Edit pelamar dengan ID:', id);
      currentEditId = id;

      // Tampilkan loading di modal
      document.getElementById('editPelamarContent').innerHTML = `
        <div class="flex flex-col items-center justify-center py-12">
            <div class="w-12 h-12 border-4 border-blue-500 border-t-transparent rounded-full animate-spin mb-4"></div>
            <p class="text-sm text-gray-500">Memuat data pelamar...</p>
        </div>
    `;

      // Buka modal terlebih dahulu
      toggleModal('modalEditPelamar', true);

      // PERBAIKAN: Gunakan get_pelamar.php untuk edit (bukan getdetailpelamar.php)
      fetch(`get_pelamar.php?id=${id}`)
        .then(response => {
          if (!response.ok) {
            throw new Error('Network response was not ok');
          }
          return response.json();
        })
        .then(data => {
          console.log('Data diterima:', data);
          if (data.success) {
            loadEditForm(data.data);
          } else {
            alert('Gagal memuat data: ' + (data.message || 'Unknown error'));
            toggleModal('modalEditPelamar', false);
          }
        })
        .catch(error => {
          console.error('Error fetching data:', error);
          alert('Terjadi kesalahan saat memuat data');
          toggleModal('modalEditPelamar', false);
        });
    }

    // ========== FUNGSI LOAD FORM EDIT DENGAN SEMUA FIELD ==========
    function loadEditForm(pelamar) {
      console.log('Loading form edit untuk pelamar:', pelamar);

      // Format tanggal lahir jika ada
      let tanggalLahir = '';
      if (pelamar.tanggallahir_user) {
        const date = new Date(pelamar.tanggallahir_user);
        tanggalLahir = date.toISOString().split('T')[0];
      }

      const formHtml = `
    <form id="formEditPelamar" class="space-y-4" enctype="multipart/form-data">
        <input type="hidden" name="id_user" value="${pelamar.id_user}">

        <div class="space-y-4">
            <!-- Bagian Foto Profil -->
            <div class="current-photo-container">
                ${pelamar.foto_user ?
          `<img src="src/images/user/${pelamar.foto_user}" alt="Foto Profil" class="photo-preview" id="currentPhotoPreview">` :
          `<div class="profile-initials mx-auto">${pelamar.nama_user ? pelamar.nama_user.substring(0, 2).toUpperCase() : '??'}</div>`
        }
                <p class="text-xs text-gray-500 mt-1">Foto Profil Saat Ini</p>
            </div>

            <div class="file-upload-container">
                <div class="file-upload-box" onclick="document.getElementById('fotoInput').click()">
                    <div class="file-upload-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="file-upload-text">Upload Foto Baru</div>
                    <div class="file-upload-hint">Klik untuk memilih file (JPG, PNG, max 2MB)</div>
                </div>
                <input type="file" name="foto_user" id="fotoInput" class="hidden" accept="image/*" onchange="handleFotoUpload(event)">
                
            </div>

            <!-- Data Dasar -->
            <div class="grid grid-cols-2 gap-3">
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-xs font-medium mb-1 dark:text-white">Email *</label>
                    <input type="email" name="email_user" value="${pelamar.email_user || ''}" required
                        class="w-full rounded border border-stroke bg-transparent px-3 py-2 text-sm outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4">
                </div>

                <div class="col-span-2 md:col-span-1">
                    <label class="block text-xs font-medium mb-1 dark:text-white">Username</label>
                    <input type="text" name="username_user" value="${pelamar.username_user || ''}"
                        class="w-full rounded border border-stroke bg-transparent px-3 py-2 text-sm outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4">
                </div>

                <div class="col-span-2">
                    <label class="block text-xs font-medium mb-1 dark:text-white">Nama Lengkap *</label>
                    <input type="text" name="nama_user" value="${pelamar.nama_user || ''}" required
                        class="w-full rounded border border-stroke bg-transparent px-3 py-2 text-sm outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4">
                </div>

                <div class="col-span-2 md:col-span-1">
                    <label class="block text-xs font-medium mb-1 dark:text-white">No. HP</label>
                    <input type="tel" name="nohp_user" value="${pelamar.nohp_user || ''}"
                        class="w-full rounded border border-stroke bg-transparent px-3 py-2 text-sm outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4">
                </div>

                <div class="col-span-2 md:col-span-1">
                    <label class="block text-xs font-medium mb-1 dark:text-white">Jenis Kelamin</label>
                    <select name="jk_user" class="w-full rounded border border-stroke bg-transparent px-3 py-2 text-sm outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4">
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="L" ${pelamar.jk_user == 'L' ? 'selected' : ''}>Laki-laki</option>
                        <option value="P" ${pelamar.jk_user == 'P' ? 'selected' : ''}>Perempuan</option>
                    </select>
                </div>

                <div class="col-span-2">
                    <label class="block text-xs font-medium mb-1 dark:text-white">Tanggal Lahir</label>
                    <input type="date" name="tanggallahir_user" value="${tanggalLahir}"
                        class="w-full rounded border border-stroke bg-transparent px-3 py-2 text-sm outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4">
                </div>

                <div class="col-span-2 md:col-span-1">
                    <label class="block text-xs font-medium mb-1 dark:text-white">Provinsi</label>
                    <select name="id_provinsi" id="edit_id_provinsi" 
                        class="w-full rounded border border-stroke bg-transparent px-3 py-2 text-sm outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4">
                        <option value="">Pilih Provinsi</option>
                    </select>
                </div>

                <div class="col-span-2 md:col-span-1">
                    <label class="block text-xs font-medium mb-1 dark:text-white">Kota</label>
                    <select name="id_kota" id="edit_id_kota" 
                        class="w-full rounded border border-stroke bg-transparent px-3 py-2 text-sm outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4">
                        <option value="">Pilih Kota</option>
                    </select>
                </div>

                <div class="col-span-2">
                    <label class="block text-xs font-medium mb-1 dark:text-white">Password (Kosongkan jika tidak ingin mengubah)</label>
                    <input type="password" name="password_user" 
                        class="w-full rounded border border-stroke bg-transparent px-3 py-2 text-sm outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4">
                </div>
            </div>

            <!-- Deskripsi Diri -->
            <div>
                <label class="block text-xs font-medium mb-1 dark:text-white">Deskripsi Diri</label>
                <textarea name="deskripsi_user" rows="3"
                    class="w-full rounded border border-stroke bg-transparent px-3 py-2 text-sm outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4">${pelamar.deskripsi_user || ''}</textarea>
            </div>

            <!-- Kelebihan & Skill -->
            <div>
                <label class="block text-xs font-medium mb-1 dark:text-white">Kelebihan & Skill</label>
                <textarea name="kelebihan_user" rows="2"
                    class="w-full rounded border border-stroke bg-transparent px-3 py-2 text-sm outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4">${pelamar.kelebihan_user || ''}</textarea>
            </div>

            <!-- Riwayat Pekerjaan -->
            <div>
                <label class="block text-xs font-medium mb-1 dark:text-white">Riwayat Pekerjaan</label>
                <textarea name="riwayatpekerjaan_user" rows="3"
                    class="w-full rounded border border-stroke bg-transparent px-3 py-2 text-sm outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4">${pelamar.riwayatpekerjaan_user || ''}</textarea>
            </div>

            <!-- Prestasi -->
            <div>
                <label class="block text-xs font-medium mb-1 dark:text-white">Prestasi & Penghargaan</label>
                <textarea name="prestasi_user" rows="2"
                    class="w-full rounded border border-stroke bg-transparent px-3 py-2 text-sm outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4">${pelamar.prestasi_user || ''}</textarea>
            </div>

            <!-- Portfolio -->
            <div class="grid grid-cols-2 gap-3">
                <div class="col-span-2">
                    <label class="block text-xs font-medium mb-1 dark:text-white">Judul Portfolio</label>
                    <input type="text" name="judul_porto" value="${pelamar.judul_porto || ''}"
                        class="w-full rounded border border-stroke bg-transparent px-3 py-2 text-sm outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-medium mb-1 dark:text-white">Link Portfolio</label>
                    <input type="url" name="link_porto" value="${pelamar.link_porto || ''}"
                        class="w-full rounded border border-stroke bg-transparent px-3 py-2 text-sm outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4" placeholder="https://">
                </div>
            </div>

            <!-- Media Sosial -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium mb-1 dark:text-white">Instagram</label>
                    <input type="url" name="instagram_user" value="${pelamar.instagram_user || ''}"
                        class="w-full rounded border border-stroke bg-transparent px-3 py-2 text-sm outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4" placeholder="https://instagram.com/username">
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1 dark:text-white">Facebook</label>
                    <input type="url" name="facebook_user" value="${pelamar.facebook_user || ''}"
                        class="w-full rounded border border-stroke bg-transparent px-3 py-2 text-sm outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4" placeholder="https://facebook.com/username">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-medium mb-1 dark:text-white">LinkedIn</label>
                    <input type="url" name="linkedin_user" value="${pelamar.linkedin_user || ''}"
                        class="w-full rounded border border-stroke bg-transparent px-3 py-2 text-sm outline-none focus:border-blue-500 dark:border-strokedark dark:bg-meta-4" placeholder="https://linkedin.com/in/username">
                </div>
            </div>

            <!-- Footer Buttons -->
            <div class="flex justify-end gap-2 pt-4 border-t border-stroke dark:border-strokedark">
                <button type="button" onclick="toggleModal('modalEditPelamar')"
                    class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded hover:bg-gray-200 dark:bg-meta-4 dark:text-gray-300 transition">
                    Batal
                </button>
                <button type="submit" id="submitEditBtn"
                    class="px-4 py-2 text-sm font-medium text-white bg-brand-500 rounded hover:bg-brand-600 transition inline-flex items-center">
                    <span>Update Data</span>
                </button>
            </div>
        </div>
    </form>
    `;

      document.getElementById('editPelamarContent').innerHTML = formHtml;

      // Load data provinsi dan kota setelah form dimuat
      loadProvinsiData(pelamar.id_provinsi, pelamar.id_kota);

      // Tambahkan event listener untuk form submit
      document.getElementById('formEditPelamar').addEventListener('submit', function (e) {
        e.preventDefault();
        updatePelamar();
      });
    }

    // Fungsi untuk clear foto input
    function clearFotoInput() {
      const fileInput = document.getElementById('fotoInput');
      const fileNameContainer = document.getElementById('fileNameContainer');
      const fileError = document.getElementById('fileError');
      const preview = document.getElementById('currentPhotoPreview');

      fileInput.value = '';
      fileNameContainer.classList.add('hidden');
      fileError.innerHTML = '';

      // Reset ke foto lama
      const pelamarId = document.querySelector('input[name="id_user"]').value;
      fetch(`get_pelamar.php?id=${pelamarId}`)
        .then(response => response.json())
        .then(data => {
          if (data.success && data.data.foto_user) {
            preview.src = `src/images/user/${data.data.foto_user}`;
          }
        });
    }

    // Fungsi untuk load data provinsi dan kota
    function loadProvinsiData(selectedProvinsiId = '', selectedKotaId = '') {
      console.log('Loading provinsi data, selected:', selectedProvinsiId, selectedKotaId);

      // Load provinsi
      fetch('get_provinsi.php')
        .then(response => response.json())
        .then(data => {
          console.log('Provinsi data:', data);
          if (data.success) {
            const provinsiSelect = document.getElementById('edit_id_provinsi');
            if (provinsiSelect) {
              provinsiSelect.innerHTML = '<option value="">Pilih Provinsi</option>';

              data.data.forEach(provinsi => {
                const option = document.createElement('option');
                option.value = provinsi.id_provinsi;
                option.textContent = provinsi.nama_provinsi;
                if (provinsi.id_provinsi == selectedProvinsiId) {
                  option.selected = true;
                }
                provinsiSelect.appendChild(option);
              });

              // Jika ada provinsi terpilih, load kota
              if (selectedProvinsiId) {
                loadKotaData(selectedProvinsiId, selectedKotaId);
              }
            }
          }
        })
        .catch(error => {
          console.error('Error loading provinsi:', error);
          // Jika gagal load, set ke nilai yang ada
          const provinsiSelect = document.getElementById('edit_id_provinsi');
          if (provinsiSelect && selectedProvinsiId) {
            const option = document.createElement('option');
            option.value = selectedProvinsiId;
            option.textContent = "Data provinsi tidak tersedia";
            option.selected = true;
            provinsiSelect.appendChild(option);
          }
        });

      // Event listener untuk perubahan provinsi
      const provinsiSelect = document.getElementById('edit_id_provinsi');
      if (provinsiSelect) {
        provinsiSelect.addEventListener('change', function () {
          const provinsiId = this.value;
          loadKotaData(provinsiId);
        });
      }
    }

    function loadKotaData(provinsiId, selectedKotaId = '') {
      console.log('Loading kota data for provinsi:', provinsiId, 'selected kota:', selectedKotaId);

      if (!provinsiId) {
        const kotaSelect = document.getElementById('edit_id_kota');
        if (kotaSelect) {
          kotaSelect.innerHTML = '<option value="">Pilih Kota</option>';
        }
        return;
      }

      fetch(`get_kota_by_provinsi.php?id_provinsi=${provinsiId}`)
        .then(response => response.json())
        .then(data => {
          console.log('Kota data:', data);
          if (data.success) {
            const kotaSelect = document.getElementById('edit_id_kota');
            if (kotaSelect) {
              kotaSelect.innerHTML = '<option value="">Pilih Kota</option>';

              data.data.forEach(kota => {
                const option = document.createElement('option');
                option.value = kota.id_kota;
                option.textContent = kota.nama_kota;
                if (kota.id_kota == selectedKotaId) {
                  option.selected = true;
                }
                kotaSelect.appendChild(option);
              });
            }
          }
        })
        .catch(error => {
          console.error('Error loading kota:', error);
          // Jika gagal load, set ke nilai yang ada
          const kotaSelect = document.getElementById('edit_id_kota');
          if (kotaSelect && selectedKotaId) {
            const option = document.createElement('option');
            option.value = selectedKotaId;
            option.textContent = "Data kota tidak tersedia";
            option.selected = true;
            kotaSelect.appendChild(option);
          }
        });
    }

    function loadKotaData(provinsiId, selectedKotaId = '') {
      if (!provinsiId) {
        const kotaSelect = document.getElementById('edit_id_kota');
        kotaSelect.innerHTML = '<option value="">Pilih Kota</option>';
        return;
      }

      fetch(`get_kota_by_provinsi.php?id_provinsi=${provinsiId}`)
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            const kotaSelect = document.getElementById('edit_id_kota');
            kotaSelect.innerHTML = '<option value="">Pilih Kota</option>';

            data.data.forEach(kota => {
              const option = document.createElement('option');
              option.value = kota.id_kota;
              option.textContent = kota.nama_kota;
              if (kota.id_kota == selectedKotaId) {
                option.selected = true;
              }
              kotaSelect.appendChild(option);
            });
          }
        })
        .catch(error => console.error('Error loading kota:', error));
    }

    // ========== FUNGSI UPDATE PELAMAR ==========
    function updatePelamar() {
      const form = document.getElementById('formEditPelamar');
      const formData = new FormData(form);
      const submitBtn = document.getElementById('submitEditBtn');
      const originalText = submitBtn.innerHTML;

      // Tampilkan loading
      submitBtn.innerHTML = '<span class="spinner"></span> Memproses...';
      submitBtn.disabled = true;

      // Kirim data via AJAX
      fetch('updatepelamar_ajax.php', {
        method: 'POST',
        body: formData
      })
        .then(response => {
          if (!response.ok) {
            throw new Error('Network response was not ok');
          }
          return response.json();
        })
        .then(data => {
          if (data.success) {
            showNotification('success', 'Data pelamar berhasil diperbarui!', 'Edit Berhasil');
            toggleModal('modalEditPelamar', false);
            // Refresh halaman setelah 2 detik
            setTimeout(() => {
              location.reload();
            }, 2000);
          } else {
            showNotification('error', 'Gagal mengupdate data: ' + (data.message || 'Unknown error'), 'Edit Gagal');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
          }
        })
        .catch(error => {
          console.error('Error updating data:', error);
          showNotification('error', 'Terjadi kesalahan saat mengupdate data', 'Error Server');
          submitBtn.innerHTML = originalText;
          submitBtn.disabled = false;
        });
    }

    // ========== FUNGSI DETAIL PELAMAR ==========
    function showDetailPelamar(id) {
      // Tampilkan loading
      document.getElementById('detailPelamarContent').innerHTML = `
        <div class="flex flex-col items-center justify-center py-12">
          <div class="w-12 h-12 border-4 border-blue-500 border-t-transparent rounded-full animate-spin mb-4"></div>
          <p class="text-sm text-gray-500">Memuat data pelamar...</p>
        </div>
      `;

      toggleModal('modalDetailPelamar', true);

      fetch(`getdetailpelamar.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            displayDetailData(data.data);
          } else {
            alert('Gagal memuat detail: ' + data.message);
            toggleModal('modalDetailPelamar', false);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Terjadi kesalahan');
          toggleModal('modalDetailPelamar', false);
        });
    }

    function displayDetailData(pelamar) {
      const formatId = "U" + String(pelamar.id_user).padStart(4, '0');
      const inisial = pelamar.nama_user ? pelamar.nama_user.substring(0, 2).toUpperCase() : '??';
      const tanggalLahir = pelamar.tanggallahir_user ? formatDate(pelamar.tanggallahir_user) : '-';
      let usia = '-';
      if (pelamar.tanggallahir_user) {
        usia = calculateAge(pelamar.tanggallahir_user) + ' tahun';
      }
      const jenisKelamin = pelamar.jk_user == 'L' ? 'Laki-laki' :
        pelamar.jk_user == 'P' ? 'Perempuan' : 'Tidak diketahui';

      const detailHtml = `
    <div class="space-y-6">
        <!-- Profile Header -->
        <div class="profile-header">
            ${pelamar.foto_user ?
          `<img src="src/images/user/${pelamar.foto_user}" alt="Foto" class="profile-avatar">` :
          `<div class="profile-initials">${inisial}</div>`
        }
            
            <div class="profile-name-container">
                <h3 class="profile-name">${pelamar.nama_user || 'N/A'}</h3>
                <div class="profile-meta">
                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300 mr-2">
                        ${formatId}
                    </span>
                    <span class="text-gray-600 dark:text-gray-400">${jenisKelamin} • ${usia}</span>
                </div>
                ${pelamar.email_user ? `
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    ${pelamar.email_user}
                </p>
                ` : ''}
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="detail-tabs">
            <button class="detail-tab active" data-tab="info">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Informasi
            </button>
            <button class="detail-tab" data-tab="profile">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Profil
            </button>
            <button class="detail-tab" data-tab="portfolio">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                Portfolio
            </button>
        </div>

        <!-- Tab Content -->
        <div class="detail-tab-content custom-scrollbar enhanced-tab-content">
            <!-- Tab 1: Informasi Dasar -->
            <div class="tab-pane active" id="tab-info">
                <div class="spaced-info-grid">
                    <!-- Email -->
                    <div class="spaced-info-card">
                        <div class="info-header">
                            <div class="spaced-info-icon email">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <h4 class="spaced-info-title">Email</h4>
                        </div>
                        <div class="spaced-info-content">
                            ${pelamar.email_user || '-'}
                        </div>
                    </div>

                    <!-- Username -->
                    <div class="spaced-info-card">
                        <div class="info-header">
                            <div class="spaced-info-icon username">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <h4 class="spaced-info-title">Username</h4>
                        </div>
                        <div class="spaced-info-content">
                            ${pelamar.username_user || '-'}
                        </div>
                    </div>

                    <!-- No. HP -->
                    <div class="spaced-info-card">
                        <div class="info-header">
                            <div class="spaced-info-icon phone">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                            <h4 class="spaced-info-title">No. HP</h4>
                        </div>
                        <div class="spaced-info-content">
                            ${pelamar.nohp_user || '-'}
                        </div>
                    </div>

                    <!-- Tanggal Lahir -->
                    <div class="spaced-info-card">
                        <div class="info-header">
                            <div class="spaced-info-icon birth">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <h4 class="spaced-info-title">Tanggal Lahir</h4>
                        </div>
                        <div class="spaced-info-content">
                            ${tanggalLahir}
                            ${usia !== '-' ? `<div class="info-badge">${usia}</div>` : ''}
                        </div>
                    </div>

                    <!-- Lokasi -->
                    <div class="spaced-info-card">
                        <div class="info-header">
                            <div class="spaced-info-icon location">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <h4 class="spaced-info-title">Lokasi</h4>
                        </div>
                        <div class="spaced-info-content">
                            ${pelamar.nama_kota ?
          `<span class="font-medium">${pelamar.nama_kota}</span>` : ''}
                            ${pelamar.nama_kota && pelamar.nama_provinsi ? ', ' : ''}
                            ${pelamar.nama_provinsi ?
          `<span class="text-gray-600 dark:text-gray-400">${pelamar.nama_provinsi}</span>` :
          '-'
        }
                        </div>
                    </div>

                    <!-- Jenis Kelamin -->
                    <div class="spaced-info-card">
                        <div class="info-header">
                            <div class="spaced-info-icon gender">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"/>
                                </svg>
                            </div>
                            <h4 class="spaced-info-title">Jenis Kelamin</h4>
                        </div>
                        <div class="spaced-info-content">
                            ${jenisKelamin}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 2: Profil & CV -->
            <div class="tab-pane" id="tab-profile">
                <div class="space-y-6">
                    ${pelamar.deskripsi_user ? `
                    <div class="info-section">
                        <h4 class="info-section-title">
                            <div class="spaced-info-icon description">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                </svg>
                            </div>
                            Deskripsi Diri
                        </h4>
                        <div class="spaced-info-card">
                            <div class="spaced-info-content long-text">
                                ${formatText(pelamar.deskripsi_user)}
                            </div>
                        </div>
                    </div>
                    ` : ''}
                    
                    ${pelamar.kelebihan_user ? `
                    <div class="info-section">
                        <h4 class="info-section-title">
                            <div class="spaced-info-icon skills">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            Kelebihan & Skill
                        </h4>
                        <div class="spaced-info-card">
                            <div class="spaced-info-content long-text">
                                ${formatText(pelamar.kelebihan_user)}
                            </div>
                        </div>
                    </div>
                    ` : ''}
                    
                    ${pelamar.riwayatpekerjaan_user ? `
                    <div class="info-section">
                        <h4 class="info-section-title">
                            <div class="spaced-info-icon history">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            Riwayat Pekerjaan
                        </h4>
                        <div class="spaced-info-card">
                            <div class="spaced-info-content long-text">
                                ${formatText(pelamar.riwayatpekerjaan_user)}
                            </div>
                        </div>
                    </div>
                    ` : ''}
                    
                    ${pelamar.prestasi_user ? `
                    <div class="info-section">
                        <h4 class="info-section-title">
                            <div class="spaced-info-icon achievement">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                                </svg>
                            </div>
                            Prestasi & Penghargaan
                        </h4>
                        <div class="spaced-info-card">
                            <div class="spaced-info-content long-text">
                                ${formatText(pelamar.prestasi_user)}
                            </div>
                        </div>
                    </div>
                    ` : ''}
                    
                    ${!pelamar.deskripsi_user && !pelamar.kelebihan_user && !pelamar.riwayatpekerjaan_user && !pelamar.prestasi_user ?
          `<div class="no-data-message">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="font-medium">Belum ada data profil</p>
                            <p class="text-sm">Pelamar belum mengisi data profil</p>
                        </div>` : ''}
                </div>
            </div>

            <!-- Tab 3: Portfolio dengan Media Sosial Satu Baris -->
            <div class="tab-pane" id="tab-portfolio">
                <div class="space-y-6">
                    ${pelamar.judul_porto || pelamar.link_porto || pelamar.instagram_user || pelamar.facebook_user || pelamar.linkedin_user ? `
                    ${pelamar.judul_porto ? `
                    <div class="info-section">
                        <h4 class="info-section-title">
                            <div class="spaced-info-icon portfolio">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                                </svg>
                            </div>
                            Judul Portfolio
                        </h4>
                        <div class="compact-spaced-info-card">
                            <div class="spaced-info-content">
                                <p class="font-medium text-sm">${pelamar.judul_porto}</p>
                            </div>
                        </div>
                    </div>
                    ` : ''}
                    
                    ${pelamar.link_porto ? `
                    <div class="info-section">
                        <h4 class="info-section-title">
                            <div class="spaced-info-icon link">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            </div>
                            Link Portfolio
                        </h4>
                        <div class="compact-spaced-info-card">
                            <div class="spaced-info-content">
                                <a href="${pelamar.link_porto}" target="_blank" 
                                   class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 transition-colors text-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                    <span>Buka Portfolio</span>
                                </a>
                                <div class="mt-1.5 text-xs text-gray-600 dark:text-gray-400 break-all">
                                    ${pelamar.link_porto}
                                </div>
                            </div>
                        </div>
                    </div>
                    ` : ''}
                    
                    <!-- Media Sosial dalam Satu Baris -->
                    ${pelamar.instagram_user || pelamar.facebook_user || pelamar.linkedin_user ? `
                    <div class="info-section">
                        <h4 class="info-section-title">
                            <div class="spaced-info-icon social">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                                </svg>
                            </div>
                            Media Sosial
                        </h4>
                            <div class="spaced-info-content">
                                <!-- Container untuk media sosial berjejer -->
                                <div class="inline-social-media inline-social-3-col">
                                    ${pelamar.instagram_user ? `
                                    <a href="${pelamar.instagram_user}" target="_blank" class="inline-social-item">
                                        <div class="inline-social-icon instagram">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073z"/>
                                            </svg>
                                        </div>
                                        <div class="inline-social-content">
                                            <div class="inline-social-platform">Instagram</div>
                                            <span class="inline-social-link">
                                                ${pelamar.instagram_user.replace('https://', '').replace('www.', '').replace('instagram.com/', '@')}
                                            </span>
                                        </div>
                                    </a>
                                    ` : ''}
                                    
                                    ${pelamar.facebook_user ? `
                                    <a href="${pelamar.facebook_user}" target="_blank" class="inline-social-item">
                                        <div class="inline-social-icon facebook">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                            </svg>
                                        </div>
                                        <div class="inline-social-content">
                                            <div class="inline-social-platform">Facebook</div>
                                            <span class="inline-social-link">
                                                ${pelamar.facebook_user.replace('https://', '').replace('www.', '').replace('facebook.com/', '@')}
                                            </span>
                                        </div>
                                    </a>
                                    ` : ''}
                                    
                                    ${pelamar.linkedin_user ? `
                                    <a href="${pelamar.linkedin_user}" target="_blank" class="inline-social-item">
                                        <div class="inline-social-icon linkedin">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                            </svg>
                                        </div>
                                        <div class="inline-social-content">
                                            <div class="inline-social-platform">LinkedIn</div>
                                            <span class="inline-social-link">
                                                ${pelamar.linkedin_user.replace('https://', '').replace('www.', '').replace('linkedin.com/in/', '@')}
                                            </span>
                                        </div>
                                    </a>
                                    ` : ''}
                                </div>
                            </div>
                    </div>
                    ` : ''}
                ` : `<div class="no-data-message">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        <p class="font-medium">Belum ada portfolio</p>
                        <p class="text-sm">Pelamar belum menambahkan portfolio</p>
                    </div>`}
                </div>
            </div>
        </div>
    </div>
    `;

      document.getElementById('detailPelamarContent').innerHTML = detailHtml;
      document.getElementById('detailPelamarEmail').textContent = pelamar.email_user || '';

      // Setup tabs
      setupDetailTabs();
    }

    // Helper function untuk format teks
    function formatText(text) {
      if (!text) return '';
      // Ganti newline dengan <br> dan trim spasi berlebih
      return text.replace(/\n/g, '<br>').trim();
    }

    // Fungsi untuk setup tab navigation
    function setupDetailTabs() {
      const tabs = document.querySelectorAll('.detail-tab');
      const tabPanes = document.querySelectorAll('.tab-pane');

      tabs.forEach(tab => {
        tab.addEventListener('click', () => {
          // Remove active class from all tabs and panes
          tabs.forEach(t => t.classList.remove('active'));
          tabPanes.forEach(pane => pane.classList.remove('active'));

          // Add active class to clicked tab
          tab.classList.add('active');

          // Show corresponding pane
          const tabId = tab.getAttribute('data-tab');
          const targetPane = document.getElementById(`tab-${tabId}`);
          if (targetPane) {
            targetPane.classList.add('active');
          }
        });
      });

      // Aktifkan tab pertama jika ada
      if (tabs.length > 0 && tabPanes.length > 0) {
        tabs[0].classList.add('active');
        tabPanes[0].classList.add('active');
      }
    }

    // Fungsi untuk menghitung usia
    function calculateAge(birthDate) {
      const birth = new Date(birthDate);
      const today = new Date();
      let age = today.getFullYear() - birth.getFullYear();
      const monthDiff = today.getMonth() - birth.getMonth();

      if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
        age--;
      }

      return age;
    }

    // Helper function untuk format tanggal
    function formatDate(dateString) {
      if (!dateString) return '-';
      const date = new Date(dateString);
      return date.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
    }

    // ========== FUNGSI KONFIRMASI HAPUS ==========
    function showDeleteConfirmation(id, name) {
      pelamarToDelete = id;

      document.getElementById('hapusMessagePelamar').textContent =
        `Apakah Anda yakin ingin menghapus pelamar "${name}"?`;

      document.getElementById('confirmDeleteBtnPelamar').onclick = function () {
        // Gunakan fetch API untuk hapus
        fetch(`datapelamar.php?action=hapus&id=${id}`)
          .then(response => response.text())
          .then(text => {
            // Cek apakah response adalah HTML (redirect) atau JSON
            try {
              const data = JSON.parse(text);
              if (data.success) {
                showNotification('success', 'Data pelamar berhasil dihapus!', 'Hapus Berhasil');
                setTimeout(() => {
                  location.reload();
                }, 2000);
              } else {
                showNotification('error', data.message || 'Gagal menghapus data', 'Hapus Gagal');
              }
            } catch (e) {
              // Jika response adalah HTML, berarti hapus berhasil (redirect)
              showNotification('success', 'Data pelamar berhasil dihapus!', 'Hapus Berhasil');
              setTimeout(() => {
                location.reload();
              }, 2000);
            }
          })
          .catch(error => {
            console.error('Error:', error);
            showNotification('error', 'Terjadi kesalahan saat menghapus data', 'Error Server');
          });
      };

      toggleModal('modalHapusPelamar', true);
    }

    // ========== INISIALISASI DATATABLES ==========
    $(document).ready(function () {
      $('#pelamarTable').DataTable({
        "dom": '<"flex flex-wrap items-center justify-between gap-3 mb-3"<"flex items-center gap-3"l><"flex items-center"f>>rt<"flex flex-wrap items-center justify-between gap-3 mt-3"ip>',
        "language": {
          "search": "",
          "searchPlaceholder": "Cari pelamar...",
          "lengthMenu": "Tampilkan _MENU_ data",
          "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
          "paginate": {
            "previous": "‹",
            "next": "›"
          }
        },
        "pageLength": 10,
        "responsive": true,
        "order": [[0, "desc"]],
        "initComplete": function () {
          // Pindahkan kontrol DataTables ke container kustom
          var $wrapper = $('#pelamarTable_wrapper');

          // Pindahkan Length Menu ke container atas
          $wrapper.find('.dt-length').appendTo('#tableControls');

          // Pindahkan Search ke container atas
          $wrapper.find('.dt-search').appendTo('#tableControls');

          $('#tableControls').addClass('flex flex-wrap items-center justify-between gap-3');

          // Pindahkan Info dan Pagination ke container bawah
          $wrapper.find('.dt-info, .dt-paging').appendTo('#tableFooter');
          $('#tableFooter').addClass('flex flex-wrap items-center justify-between gap-3');

          // Styling untuk DataTables
          $('.dt-search input').addClass(
            'rounded-lg border border-stroke bg-transparent py-1.5 px-3 outline-none focus:border-primary dark:border-strokedark dark:bg-meta-4 text-xs h-8'
          );

          $('.dt-length select').addClass(
            'rounded-lg border border-stroke bg-transparent py-1.5 px-3 outline-none dark:border-strokedark dark:bg-meta-4 text-xs h-8'
          );

          $('.dt-paging .dt-paging-button').addClass(
            'rounded-md border border-stroke bg-transparent px-2 py-1 text-xs h-7 min-w-7 mx-1 dark:border-strokedark dark:bg-meta-4'
          );

          $('.dt-paging .dt-paging-button.current').addClass('bg-blue-500 text-white border-blue-500');

          // Hapus wrapper asli untuk mencegah duplikasi
          $wrapper.find('.dt-length, .dt-search, .dt-info, .dt-paging').remove();

          // Tambahkan styling khusus untuk select dropdown
          $('.dt-length select').css({
            'min-width': '70px',
            'background-position': 'right 0.5rem center',
            'padding-right': '2rem'
          });

          // Pastikan dropdown options tidak terpotong
          $('.dt-length option').css({
            'font-size': '12px',
            'padding': '4px 8px'
          });

          // BATASI LEBAR SEARCH INPUT
          $('.dt-search').css({
            'max-width': '300px',
            'min-width': '200px'
          });

          $('.dt-search input').css({
            'width': '100%'
          });
        }
      });
    });

    // ========== PRINT & PREVIEW FUNCTIONS ==========
    function printPelamarData() {
      const form = document.getElementById('exportFormPelamar');
      const formData = new FormData(form);
      formData.append('format', 'print');
      fetch('export_pelamar.php', {
        method: 'POST',
        body: formData
      })
        .then(response => response.text())
        .then(html => {
          const printWindow = window.open('', '_blank');
          printWindow.document.write(html);
          printWindow.document.close();
          printWindow.focus();
          printWindow.print();
          printWindow.close();
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Gagal mencetak data');
        });
    }

    function printPelamarPreview() {
      const form = document.getElementById('exportFormPelamar');
      const formData = new FormData(form);
      formData.append('format', 'preview');

      fetch('export_pelamar.php', {
        method: 'POST',
        body: formData
      })
        .then(response => response.text())
        .then(html => {
          const previewWindow = window.open('', '_blank', 'width=800,height=600');
          previewWindow.document.write(html);
          previewWindow.document.close();
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Gagal menampilkan preview');
        });
    }

    // ========== EVENT LISTENER UNTUK MODAL ==========
    document.addEventListener('DOMContentLoaded', function () {
      // Close modal ketika klik overlay
      document.querySelectorAll('.modal-container .absolute.inset-0').forEach(overlay => {
        overlay.addEventListener('click', function () {
          const modal = this.closest('.modal-container');
          if (modal) {
            toggleModal(modal.id, false);
          }
        });
      });

      // Close modal ketika klik tombol close (X)
      document.querySelectorAll('.modal-container button[onclick*="toggleModal"]').forEach(button => {
        const oldOnClick = button.getAttribute('onclick');
        if (oldOnClick && oldOnClick.includes("toggleModal('")) {
          const modalId = oldOnClick.match(/toggleModal\('([^']+)'\)/)[1];
          button.removeAttribute('onclick');
          button.addEventListener('click', function () {
            toggleModal(modalId, false);
          });
        }
      });
    });  </script>

  <div id="modalExportPelamar"
    class="modal-container fixed inset-0 hidden items-center justify-center transition-all duration-300 z-9999">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="toggleModal('modalExportPelamar')"></div>
    <div
      class="modal-content bg-white dark:bg-boxdark w-full max-w-md mx-4 rounded-2xl shadow-2xl relative flex flex-col overflow-hidden max-h-[75vh]">
      <!-- Modal Header -->
      <div
        class="sticky top-0 z-10 bg-gradient-to-r from-blue-600 to-blue-700 dark:from-blue-800 dark:to-blue-900 px-6 py-5">
        <div class="flex items-center justify-between">
          <div>
            <h4 class="text-lg font-bold text-white">Export Data Pelamar</h4>
            <p class="text-xs text-blue-100 mt-1">Pilih filter dan format export data</p>
          </div>
          <button onclick="toggleModal('modalExportPelamar')"
            class="text-white/80 hover:text-white transition-colors p-2 hover:bg-white/10 rounded-lg">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M18 6L6 18M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Modal Body -->
      <div class="p-6 overflow-y-auto">
        <form id="exportFormPelamar" action="export_pelamar.php" method="POST" class="space-y-6">
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

              <!-- Filter by Nama Pelamar -->
              <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Nama Pelamar</label>
                <input type="text" name="filter_nama"
                  class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                  placeholder="Cari nama pelamar...">
              </div>

              <!-- Filter by Email -->
              <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                <input type="text" name="filter_email"
                  class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                  placeholder="Cari email...">
              </div>

              <!-- Filter by Jenis Kelamin -->
              <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Jenis Kelamin</label>
                <select name="filter_jk"
                  class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                  <option value="">Semua</option>
                  <option value="L">Laki-laki</option>
                  <option value="P">Perempuan</option>
                </select>
              </div>

              <!-- Filter by Lokasi -->
              <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Lokasi</label>
                <input type="text" name="filter_lokasi"
                  class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                  placeholder="Cari lokasi...">
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
                <input type="checkbox" name="columns[]" value="id_user" checked
                  class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                <div class="flex-1">
                  <span class="text-sm font-medium text-gray-700 dark:text-gray-300">ID Pelamar</span>
                  <p class="text-xs text-gray-500">Nomor identifikasi pelamar</p>
                </div>
              </label>

              <label
                class="flex items-center gap-3 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <input type="checkbox" name="columns[]" value="nama_user" checked
                  class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                <div class="flex-1">
                  <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Nama Lengkap</span>
                  <p class="text-xs text-gray-500">Nama lengkap pelamar</p>
                </div>
              </label>

              <label
                class="flex items-center gap-3 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <input type="checkbox" name="columns[]" value="email_user" checked
                  class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                <div class="flex-1">
                  <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Email</span>
                  <p class="text-xs text-gray-500">Email pelamar</p>
                </div>
              </label>

              <label
                class="flex items-center gap-3 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <input type="checkbox" name="columns[]" value="username_user"
                  class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                <div class="flex-1">
                  <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Username</span>
                  <p class="text-xs text-gray-500">Username untuk login</p>
                </div>
              </label>

              <label
                class="flex items-center gap-3 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <input type="checkbox" name="columns[]" value="nohp_user"
                  class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                <div class="flex-1">
                  <span class="text-sm font-medium text-gray-700 dark:text-gray-300">No. HP</span>
                  <p class="text-xs text-gray-500">Nomor telepon pelamar</p>
                </div>
              </label>

              <label
                class="flex items-center gap-3 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <input type="checkbox" name="columns[]" value="jk_user"
                  class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                <div class="flex-1">
                  <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Jenis Kelamin</span>
                  <p class="text-xs text-gray-500">Laki-laki/Perempuan</p>
                </div>
              </label>

              <label
                class="flex items-center gap-3 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <input type="checkbox" name="columns[]" value="lokasi"
                  class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                <div class="flex-1">
                  <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Lokasi</span>
                  <p class="text-xs text-gray-500">Kota, Provinsi</p>
                </div>
              </label>

              <label
                class="flex items-center gap-3 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <input type="checkbox" name="columns[]" value="tanggal_daftar"
                  class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                <div class="flex-1">
                  <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Daftar</span>
                  <p class="text-xs text-gray-500">Tanggal registrasi user</p>
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
                <button type="button" onclick="printPelamarData()"
                  class="flex items-center gap-2 px-4 py-2 rounded-lg font-medium text-sm transition-colors shadow-sm"
                  style="background-color: #7c3aed; color: white; hover:background-color: #6d28d9;">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm-8 0h2v2H7v-2z" />
                  </svg>
                  Print Data
                </button>

                <button type="button" onclick="printPelamarPreview()"
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