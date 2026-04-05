<?php
// perusahaan/profil.php - Halaman Profil Perusahaan
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
$perusahaan = $db->getPerusahaanById($company_id);
$provinsi_list = $db->getAllProvinsi();
$stats = $db->getStats($company_id);

// Base URL untuk akses gambar dari admin panel
$base_url = 'http://localhost/web-linkup-loker/';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Perusahaan | LinkUp Perusahaan</title>
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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

        /* ========== APP CONTAINER ========== */
        .app-container {
            display: flex;
            min-height: 100vh;
        }

        /* ========== SIDEBAR ========== */
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

        /* ========== MAIN WRAPPER ========== */
        .main-wrapper {
            flex: 1;
            margin-left: 280px;
            min-height: 100vh;
        }

        /* ========== TOP NAVBAR ========== */
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

        /* ========== MAIN CONTENT ========== */
        .main-content {
            padding: 28px 32px;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }

        /* ========== HEADER ========== */
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

        /* ========== TWO COLUMNS LAYOUT ========== */
        .two-columns {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 28px;
        }

        /* ========== CARD ========== */
        .card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }

        .card-header {
            padding: 18px 24px;
            border-bottom: 1px solid #eef2ff;
            background: white;
        }

        .card-header h3 {
            font-size: 1rem;
            font-weight: 600;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-header h3 i {
            color: #2563eb;
            font-size: 1.1rem;
        }

        .card-body {
            padding: 24px;
        }

        /* ========== FORM STYLES ========== */
        .form-group {
            margin-bottom: 20px;
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
            padding: 12px 14px;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
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

        .form-group input:read-only {
            background: #f8fafc;
            cursor: not-allowed;
        }

        .form-two-columns {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .form-actions {
            margin-top: 24px;
            padding-top: 16px;
            border-top: 1px solid #eef2ff;
        }

        /* Logo Upload */
        .logo-upload {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .logo-preview {
            width: 100px;
            height: 100px;
            border-radius: 16px;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border: 2px solid #e2e8f0;
        }

        .logo-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .logo-preview .no-logo {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            color: #94a3b8;
            font-size: 0.7rem;
        }

        .logo-preview .no-logo i {
            font-size: 2rem;
        }

        /* Buttons */
        .btn-primary {
            background: #2563eb;
            border: none;
            padding: 10px 24px;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-family: 'Poppins', sans-serif;
        }

        .btn-primary:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }

        .btn-primary:disabled {
            background: #94a3b8;
            cursor: not-allowed;
            transform: none;
        }

        .btn-secondary {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            padding: 8px 16px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 500;
            transition: 0.2s;
            font-size: 0.75rem;
            color: #475569;
            font-family: 'Poppins', sans-serif;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-secondary:hover {
            background: #e2e8f0;
            border-color: #cbd5e1;
        }

        /* Stats Mini */
        .stats-mini {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .stat-mini-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .stat-mini-item:last-child {
            border-bottom: none;
        }

        .stat-mini-label {
            font-size: 0.8rem;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .stat-mini-label i {
            color: #2563eb;
            width: 20px;
        }

        .stat-mini-value {
            font-size: 1.5rem;
            font-weight: 700;
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
            padding: 14px 16px;
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

        /* ========== RESPONSIVE ========== */
        @media (max-width: 1024px) {
            .two-columns {
                grid-template-columns: 1fr;
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
            .form-two-columns {
                grid-template-columns: 1fr;
                gap: 0;
            }
            .logo-upload {
                flex-direction: column;
                align-items: flex-start;
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
                <h1 class="page-title">Profil Perusahaan</h1>
                <p class="page-subtitle">Kelola informasi profil perusahaan Anda</p>
            </div>

            <div class="two-columns">
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-building"></i> Informasi Perusahaan</h3>
                    </div>
                    <div class="card-body">
                        <form id="profilForm" enctype="multipart/form-data">
                            <div class="form-group">
                                <label><i class="fas fa-image"></i> Logo Perusahaan</label>
                                <div class="logo-upload">
                                    <div class="logo-preview" id="logoPreview">
                                        <?php if (!empty($perusahaan['logo_perusahaan'])): ?>
                                            <?php 
                                            // Path ke folder adminpanel
                                            $logo_path = $base_url . 'adminpanel/src/images/company/' . $perusahaan['logo_perusahaan'];
                                            ?>
                                            <img id="logoImg" src="<?php echo $logo_path; ?>" alt="Logo Perusahaan"
                                                onerror="this.parentElement.innerHTML='<div class=\'no-logo\'><i class=\'fas fa-building\'></i><span>Tidak ada logo</span></div>'">
                                        <?php else: ?>
                                            <div class="no-logo">
                                                <i class="fas fa-building"></i>
                                                <span>Tidak ada logo</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <input type="file" id="logoUpload" name="logo" accept="image/*" style="display:none">
                                        <button type="button" class="btn-secondary" id="uploadLogoBtn">
                                            <i class="fas fa-upload"></i> Pilih Logo
                                        </button>
                                        <p style="font-size: 0.65rem; color: #94a3b8; margin-top: 8px;">
                                            Format: JPG, PNG, GIF. Max 2MB
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label><i class="fas fa-store"></i> Nama Perusahaan *</label>
                                <input type="text" id="namaPerusahaan" name="nama_perusahaan" required 
                                    value="<?php echo htmlspecialchars($perusahaan['nama_perusahaan'] ?? ''); ?>"
                                    placeholder="Masukkan nama perusahaan">
                            </div>

                            <div class="form-group">
                                <label><i class="fas fa-envelope"></i> Email Perusahaan</label>
                                <input type="email" id="emailPerusahaan" readonly 
                                    value="<?php echo htmlspecialchars($perusahaan['email_perusahaan'] ?? ''); ?>">
                            </div>

                            <div class="form-group">
                                <label><i class="fas fa-phone"></i> No Telepon</label>
                                <input type="text" id="noHp" name="no_hp" 
                                    value="<?php echo htmlspecialchars($perusahaan['nohp_perusahaan'] ?? ''); ?>"
                                    placeholder="Contoh: 08123456789">
                            </div>

                            <div class="form-group">
                                <label><i class="fas fa-location-dot"></i> Alamat Lengkap</label>
                                <textarea id="alamat" name="alamat" rows="2" 
                                    placeholder="Masukkan alamat lengkap perusahaan"><?php echo htmlspecialchars($perusahaan['alamat_perusahaan'] ?? ''); ?></textarea>
                            </div>

                            <div class="form-two-columns">
                                <div class="form-group">
                                    <label><i class="fas fa-map-marker-alt"></i> Provinsi</label>
                                    <select id="id_provinsi" name="id_provinsi">
                                        <option value="">Pilih Provinsi</option>
                                        <?php foreach ($provinsi_list as $prov): ?>
                                            <option value="<?php echo $prov['id_provinsi']; ?>" 
                                                <?php echo ($perusahaan['id_provinsi'] == $prov['id_provinsi']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($prov['nama_provinsi']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label><i class="fas fa-city"></i> Kota</label>
                                    <select id="id_kota" name="id_kota">
                                        <option value="">Pilih Kota</option>
                                        <?php if ($perusahaan['id_kota']): ?>
                                            <option value="<?php echo $perusahaan['id_kota']; ?>" selected>
                                                <?php echo htmlspecialchars($perusahaan['nama_kota'] ?? ''); ?>
                                            </option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label><i class="fas fa-align-left"></i> Deskripsi Perusahaan</label>
                                <textarea id="deskripsiPerusahaan" name="deskripsi_perusahaan" rows="5" 
                                    placeholder="Ceritakan tentang perusahaan Anda..."><?php echo htmlspecialchars($perusahaan['deskripsi_perusahaan'] ?? ''); ?></textarea>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn-primary" id="submitBtn">
                                    <i class="fas fa-save"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-chart-simple"></i> Statistik Perusahaan</h3>
                    </div>
                    <div class="card-body">
                        <div class="stats-mini">
                            <div class="stat-mini-item">
                                <span class="stat-mini-label">
                                    <i class="fas fa-briefcase"></i> Total Lowongan
                                </span>
                                <span class="stat-mini-value"><?php echo $stats['total_lowongan']; ?></span>
                            </div>
                            <div class="stat-mini-item">
                                <span class="stat-mini-label">
                                    <i class="fas fa-users"></i> Total Pelamar
                                </span>
                                <span class="stat-mini-value"><?php echo $stats['total_pelamar']; ?></span>
                            </div>
                            <div class="stat-mini-item">
                                <span class="stat-mini-label">
                                    <i class="fas fa-clock"></i> Lamaran Diproses
                                </span>
                                <span class="stat-mini-value" id="statDiproses">
                                    <?php 
                                    $diproses = 0;
                                    $lamaran_all = $db->getLamaranByPerusahaan($company_id);
                                    foreach ($lamaran_all as $l) {
                                        if ($l['status_lamaran'] == 'Diproses') $diproses++;
                                    }
                                    echo $diproses;
                                    ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Alert Container -->
<div id="alertContainer" class="alert-container"></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Base URL
    var BASE_URL = '<?php echo $base_url; ?>';

    // Load kota berdasarkan provinsi yang dipilih
    $('#id_provinsi').on('change', function() {
        var provinsiId = $(this).val();
        if (provinsiId) {
            $.ajax({
                url: 'ajax_get_kota.php',
                type: 'GET',
                data: { id_provinsi: provinsiId },
                dataType: 'json',
                success: function(data) {
                    $('#id_kota').empty().append('<option value="">Pilih Kota</option>');
                    $.each(data, function(i, kota) {
                        $('#id_kota').append('<option value="' + kota.id_kota + '">' + kota.nama_kota + '</option>');
                    });
                }
            });
        } else {
            $('#id_kota').empty().append('<option value="">Pilih Kota</option>');
        }
    });

    // Trigger change untuk load kota awal jika ada provinsi terpilih
    var selectedProvinsi = $('#id_provinsi').val();
    if (selectedProvinsi) {
        $.ajax({
            url: 'ajax_get_kota.php',
            type: 'GET',
            data: { id_provinsi: selectedProvinsi },
            dataType: 'json',
            success: function(data) {
                $('#id_kota').empty().append('<option value="">Pilih Kota</option>');
                $.each(data, function(i, kota) {
                    var selected = (<?php echo $perusahaan['id_kota'] ?? 0; ?> == kota.id_kota) ? 'selected' : '';
                    $('#id_kota').append('<option value="' + kota.id_kota + '" ' + selected + '>' + kota.nama_kota + '</option>');
                });
            }
        });
    }

    // Upload logo button
    $('#uploadLogoBtn').click(function() {
        $('#logoUpload').click();
    });

    // Preview logo before upload
    $('#logoUpload').change(function(e) {
        var file = e.target.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(ev) {
                $('#logoPreview').html('<img id="logoImg" src="' + ev.target.result + '" alt="Logo Preview" style="width:100%;height:100%;object-fit:cover">');
            };
            reader.readAsDataURL(file);
        }
    });

    // Submit form
    $('#profilForm').submit(function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        formData.append('action', 'update_profil');
        
        var submitBtn = $('#submitBtn');
        var originalText = submitBtn.html();
        submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...').prop('disabled', true);
        
        $.ajax({
            url: 'ajax_profil.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    showNotification('success', res.message, 'Berhasil');
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                } else {
                    showNotification('error', res.message, 'Gagal');
                    submitBtn.html(originalText).prop('disabled', false);
                }
            },
            error: function() {
                showNotification('error', 'Terjadi kesalahan saat menghubungi server', 'Error');
                submitBtn.html(originalText).prop('disabled', false);
            }
        });
    });

    // Notification function
    function showNotification(type, message, title) {
        var icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        var titleText = type === 'success' ? 'Berhasil!' : 'Gagal!';
        
        var alertHtml = `
            <div class="alert-item ${type}">
                <i class="fas ${icon}"></i>
                <div class="alert-content">
                    <div class="alert-title">${titleText}</div>
                    <div class="alert-message">${message}</div>
                </div>
                <button class="alert-close" onclick="$(this).closest('.alert-item').remove()">&times;</button>
            </div>
        `;
        
        $('#alertContainer').append(alertHtml);
        
        setTimeout(function() {
            $('#alertContainer .alert-item:first-child').fadeOut(300, function() {
                $(this).remove();
            });
        }, 4000);
    }
</script>

</body>
</html>