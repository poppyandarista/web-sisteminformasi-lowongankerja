<?php
session_start();
include 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$db = new database();
$user_id = $_SESSION['user_id'];

// Get user profile data
$profile = $db->get_user_profile($user_id);

// Get all provinces for dropdown
$provinces = $db->get_all_provinces();

// Get cities based on selected province
$selected_province = isset($profile['id_provinsi']) ? $profile['id_provinsi'] : 0;
$cities = [];
if ($selected_province > 0) {
    $cities = $db->get_cities_by_province($selected_province);
}

// Handle form submission
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_profile'])) {
        // Update profile data
        $data = [
            'nama_user' => $_POST['nama_user'] ?? '',
            'nohp_user' => $_POST['nohp_user'] ?? '',
            'tanggallahir_user' => $_POST['tanggallahir_user'] ?? null,
            'jk_user' => $_POST['jk_user'] ?? '',
            'deskripsi_user' => $_POST['deskripsi_user'] ?? '',
            'kelebihan_user' => $_POST['kelebihan_user'] ?? '',
            'riwayatpekerjaan_user' => $_POST['riwayatpekerjaan_user'] ?? '',
            'prestasi_user' => $_POST['prestasi_user'] ?? '',
            'instagram_user' => $_POST['instagram_user'] ?? '',
            'facebook_user' => $_POST['facebook_user'] ?? '',
            'linkedin_user' => $_POST['linkedin_user'] ?? '',
            'id_provinsi' => $_POST['id_provinsi'] ? intval($_POST['id_provinsi']) : null,
            'id_kota' => $_POST['id_kota'] ? intval($_POST['id_kota']) : null
        ];

        if ($db->update_user_profile($user_id, $data)) {
            $message = 'Profil berhasil diperbarui!';
            $message_type = 'success';
            $profile = $db->get_user_profile($user_id);
        } else {
            $message = 'Gagal memperbarui profil. Silakan coba lagi.';
            $message_type = 'danger';
        }
    } elseif (isset($_POST['update_portfolio'])) {
        // Update portfolio
        $upload_dir = 'adminpanel/src/images/portfolio/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $judul_porto = $_POST['judul_porto'] ?? '';
        $link_porto = $_POST['link_porto'] ?? '';
        $gambar_porto = $profile['gambar_porto'] ?? '';

        // Handle file upload
        if (isset($_FILES['gambar_porto']) && $_FILES['gambar_porto']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
            if (in_array($_FILES['gambar_porto']['type'], $allowed_types)) {
                // Delete old image
                if (!empty($gambar_porto) && file_exists($upload_dir . $gambar_porto)) {
                    unlink($upload_dir . $gambar_porto);
                }

                $extension = pathinfo($_FILES['gambar_porto']['name'], PATHINFO_EXTENSION);
                $gambar_porto = 'portfolio_' . $user_id . '_' . time() . '.' . $extension;
                $destination = $upload_dir . $gambar_porto;
                move_uploaded_file($_FILES['gambar_porto']['tmp_name'], $destination);
            }
        }

        // Panggil method update_portfolio dengan 4 parameter sesuai yang diharapkan database.php
        if ($db->update_portfolio($user_id, $judul_porto, $gambar_porto, $link_porto)) {
            $message = 'Portofolio berhasil diperbarui!';
            $message_type = 'success';
            $profile = $db->get_user_profile($user_id);
        } else {
            $message = 'Gagal memperbarui portofolio.';
            $message_type = 'danger';
        }
    } elseif (isset($_POST['update_username'])) {
        $new_username = trim($_POST['username']);
        if (!empty($new_username)) {
            if ($db->update_username($user_id, $new_username)) {
                $_SESSION['username'] = $new_username;
                $message = 'Username berhasil diperbarui!';
                $message_type = 'success';
                $profile = $db->get_user_profile($user_id);
            } else {
                $message = 'Gagal memperbarui username.';
                $message_type = 'danger';
            }
        }
    } elseif (isset($_POST['update_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        $query = "SELECT password_user FROM user WHERE id_user = ?";
        $stmt = $db->koneksi->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if (password_verify($current_password, $user['password_user'])) {
            if ($new_password === $confirm_password) {
                if (strlen($new_password) >= 6) {
                    if ($db->update_password($user_id, $new_password)) {
                        $message = 'Password berhasil diperbarui!';
                        $message_type = 'success';
                    } else {
                        $message = 'Gagal memperbarui password.';
                        $message_type = 'danger';
                    }
                } else {
                    $message = 'Password minimal 6 karakter.';
                    $message_type = 'warning';
                }
            } else {
                $message = 'Password baru dan konfirmasi password tidak cocok.';
                $message_type = 'warning';
            }
        } else {
            $message = 'Password saat ini salah.';
            $message_type = 'danger';
        }
    } elseif (isset($_FILES['foto_user']) && $_FILES['foto_user']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
        $file_type = $_FILES['foto_user']['type'];
        $file_size = $_FILES['foto_user']['size'];

        if (in_array($file_type, $allowed_types)) {
            if ($file_size <= 2 * 1024 * 1024) {
                $upload_dir = 'adminpanel/src/images/user/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                if (!empty($profile['foto_user']) && file_exists($upload_dir . $profile['foto_user'])) {
                    unlink($upload_dir . $profile['foto_user']);
                }

                $extension = pathinfo($_FILES['foto_user']['name'], PATHINFO_EXTENSION);
                $filename = 'user_' . $user_id . '_' . time() . '.' . $extension;
                $destination = $upload_dir . $filename;

                if (move_uploaded_file($_FILES['foto_user']['tmp_name'], $destination)) {
                    if ($db->update_profile_photo($user_id, $filename)) {
                        $message = 'Foto profil berhasil diunggah!';
                        $message_type = 'success';
                        $profile = $db->get_user_profile($user_id);
                    } else {
                        $message = 'Gagal menyimpan foto profil.';
                        $message_type = 'danger';
                    }
                } else {
                    $message = 'Gagal mengunggah file.';
                    $message_type = 'danger';
                }
            } else {
                $message = 'Ukuran file terlalu besar. Maksimal 2MB.';
                $message_type = 'warning';
            }
        } else {
            $message = 'Format file tidak didukung. Gunakan JPG, JPEG, atau PNG.';
            $message_type = 'warning';
        }
    }
}

function getGenderLabel($gender)
{
    if ($gender == 'L')
        return 'Laki-laki';
    if ($gender == 'P')
        return 'Perempuan';
    return 'Tidak ditentukan';
}

function formatDate($date)
{
    if (empty($date))
        return '';
    return date('Y-m-d', strtotime($date));
}

// ========== FUNGSI UNTUK EKSTRAK USERNAME DARI URL (VERSI AMAN) ==========
function extractUsername($url, $platform)
{
    if (empty($url))
        return '';

    // Hapus protokol http/https dan www
    $url = str_replace(['https://', 'http://', 'www.'], '', $url);

    // Hapus query string (?...) dan fragment (#...)
    $url = strtok($url, '?#');

    // Hapus trailing slash di akhir
    $url = rtrim($url, '/');

    switch ($platform) {
        case 'instagram':
            if (strpos($url, 'instagram.com/') !== false) {
                $parts = explode('instagram.com/', $url);
                return isset($parts[1]) ? $parts[1] : $url;
            }
            break;
        case 'facebook':
            if (strpos($url, 'facebook.com/') !== false) {
                $parts = explode('facebook.com/', $url);
                return isset($parts[1]) ? $parts[1] : $url;
            }
            if (strpos($url, 'fb.com/') !== false) {
                $parts = explode('fb.com/', $url);
                return isset($parts[1]) ? $parts[1] : $url;
            }
            break;
        case 'linkedin':
            if (strpos($url, 'linkedin.com/in/') !== false) {
                $parts = explode('linkedin.com/in/', $url);
                return isset($parts[1]) ? $parts[1] : $url;
            }
            if (strpos($url, 'linkedin.com/company/') !== false) {
                $parts = explode('linkedin.com/company/', $url);
                return isset($parts[1]) ? $parts[1] : $url;
            }
            break;
    }

    $lastSlash = strrpos($url, '/');
    if ($lastSlash !== false) {
        return substr($url, $lastSlash + 1);
    }

    return $url;
}

// Parse text areas to array for display
$kelebihan_list = !empty($profile['kelebihan_user']) ? explode("\n", $profile['kelebihan_user']) : [];
$prestasi_list = !empty($profile['prestasi_user']) ? explode("\n", $profile['prestasi_user']) : [];
$riwayat_list = !empty($profile['riwayatpekerjaan_user']) ? explode("\n", $profile['riwayatpekerjaan_user']) : [];

// Extract usernames untuk ditampilkan
$instagram_display = extractUsername($profile['instagram_user'] ?? '', 'instagram');
$facebook_display = extractUsername($profile['facebook_user'] ?? '', 'facebook');
$linkedin_display = extractUsername($profile['linkedin_user'] ?? '', 'linkedin');
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Profil Saya | LinkUp</title>

    <link rel="icon" type="image/png" href="assets/img/favicon.png">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/line-icons.css" />
    <link rel="stylesheet" href="assets/css/owl.carousel.min.css" />
    <link rel="stylesheet" href="assets/css/owl.theme.default.css" />
    <link rel="stylesheet" href="assets/css/slicknav.min.css" />
    <link rel="stylesheet" href="assets/css/animate.css" />
    <link rel="stylesheet" href="assets/css/main.css" />
    <link rel="stylesheet" href="assets/css/responsive.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #3d8eff;
            --primary-light: #5da1ff;
            --primary-dark: #2a6fd1;
            --gray-bg: #f8f9fa;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #f0f7ff 0%, #ffffff 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .profile-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            padding: 80px 0 50px;
            margin-top: 0;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .profile-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="rgba(255,255,255,0.1)"><polygon points="1000,100 1000,0 0,100"></polygon></svg>');
            background-size: cover;
            opacity: 0.3;
        }

        .profile-header .container {
            position: relative;
            z-index: 2;
        }

        .profile-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
        }

        .profile-avatar:hover {
            transform: scale(1.05);
        }

        .profile-name {
            font-size: 28px;
            font-weight: 700;
            margin-top: 15px;
            margin-bottom: 5px;
            color: white !important;
        }

        .profile-email {
            font-size: 16px;
            opacity: 0.95;
            color: white !important;
        }

        .profile-email i,
        .profile-name i {
            color: white;
        }

        .profile-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            padding: 25px;
            margin-bottom: 25px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .profile-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
        }

        .profile-card h3 {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid var(--primary);
            display: inline-block;
            cursor: pointer;
        }

        .profile-card h3 i {
            margin-right: 10px;
            color: var(--primary);
        }

        .profile-card h3 .toggle-icon {
            margin-left: 10px;
            font-size: 14px;
            color: #999;
            transition: transform 0.3s ease;
        }

        .profile-card h3:hover .toggle-icon {
            color: var(--primary);
        }

        /* Accordion Content */
        .accordion-content {
            display: none;
            margin-top: 15px;
        }

        .accordion-content.show {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 10px;
            background: var(--gray-bg);
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .info-item:hover {
            background: #e9ecef;
            transform: translateX(5px);
        }

        .info-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--primary);
            border-radius: 10px;
            color: white;
            margin-right: 15px;
        }

        .info-label {
            font-weight: 600;
            color: #666;
            min-width: 100px;
        }

        .info-value {
            flex: 1;
            color: #333;
        }

        .skill-tag {
            display: inline-block;
            background: linear-gradient(135deg, var(--primary-light), var(--primary));
            color: white;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 13px;
            margin: 5px;
            transition: all 0.3s ease;
        }

        .skill-tag:hover {
            transform: scale(1.05);
            box-shadow: 0 3px 10px rgba(61, 142, 255, 0.3);
        }

        .achievement-card,
        .work-card {
            background: var(--gray-bg);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 4px solid var(--primary);
            transition: all 0.3s ease;
        }

        .achievement-card:hover,
        .work-card:hover {
            background: #e9ecef;
            transform: translateX(5px);
        }

        .achievement-card i,
        .work-card i {
            color: var(--primary);
            margin-right: 10px;
        }

        .portfolio-card {
            background: var(--gray-bg);
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }

        .portfolio-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .portfolio-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .portfolio-body {
            padding: 15px;
        }

        .portfolio-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .portfolio-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .portfolio-link:hover {
            text-decoration: underline;
        }

        .social-item {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            background: var(--gray-bg);
            border-radius: 10px;
            margin-bottom: 10px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .social-item:hover {
            transform: translateX(5px);
            background: #e9ecef;
        }

        .social-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: white;
        }

        .social-icon.instagram {
            background: linear-gradient(135deg, #833ab4, #fd1d1d, #fcb045);
        }

        .social-icon.facebook {
            background: #1877f2;
        }

        .social-icon.linkedin {
            background: #0077b5;
        }

        .photo-upload {
            text-align: center;
        }

        .photo-preview {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 20px;
            border: 4px solid var(--primary);
            padding: 3px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-weight: 600;
            color: #555;
            margin-bottom: 8px;
            display: block;
        }

        .form-group label i {
            margin-right: 8px;
            color: var(--primary);
        }

        .form-control,
        .form-select {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 10px 15px;
            transition: all 0.3s ease;
            width: 100%;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(61, 142, 255, 0.15);
            outline: none;
        }

        .btn {
            border-radius: 10px;
            padding: 10px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            border: none;
        }

        .btn i {
            font-size: 14px;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(61, 142, 255, 0.3);
        }

        .btn-outline-primary {
            border: 2px solid var(--primary);
            color: var(--primary);
            background: transparent;
        }

        .btn-outline-primary:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
        }

        .btn-sm {
            padding: 6px 15px;
            font-size: 13px;
        }

        .photo-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 10px;
        }

        .alert {
            border-radius: 10px;
            margin-bottom: 25px;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert i {
            font-size: 18px;
        }

        .close {
            margin-left: auto;
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            opacity: 0.7;
        }

        .close:hover {
            opacity: 1;
        }

        @media (max-width: 768px) {
            .profile-header {
                padding: 90px 0 40px;
            }

            .profile-avatar {
                width: 100px;
                height: 100px;
            }

            .profile-name {
                font-size: 22px;
            }

            .info-item {
                flex-direction: column;
                text-align: center;
            }

            .info-icon {
                margin: 0 auto 10px;
            }

            .info-label {
                text-align: center;
                margin-bottom: 5px;
            }

            .info-value {
                text-align: center;
            }

            .photo-buttons {
                flex-direction: column;
            }

            .photo-buttons .btn {
                width: 100%;
                justify-content: center;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .profile-card {
                padding: 20px;
            }

            .profile-card h3 {
                font-size: 18px;
            }
        }
    </style>
</head>

<body>
    <?php include("header.php") ?>

    <!-- Profile Header -->
    <div class="profile-header">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <?php
                    $default_avatar = 'assets/img/default-avatar.png';
                    $foto = $default_avatar;
                    if (!empty($profile['foto_user'])) {
                        $foto_path = 'adminpanel/src/images/user/' . $profile['foto_user'];
                        if (file_exists($foto_path))
                            $foto = $foto_path;
                    }
                    ?>
                    <img src="<?php echo $foto; ?>" alt="Profile" class="profile-avatar">
                    <h1 class="profile-name">
                        <?php echo htmlspecialchars($profile['nama_user'] ?? $profile['username_user'] ?? 'Pengguna'); ?>
                    </h1>
                    <p class="profile-email"><i class="fas fa-envelope"></i>
                        <?php echo htmlspecialchars($profile['email_user']); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Content -->
    <section style="padding: 50px 0;">
        <div class="container">
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show">
                    <i class="fas fa-<?php echo $message_type == 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                    <?php echo $message; ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <div class="row">
                <!-- LEFT COLUMN -->
                <div class="col-lg-4">
                    <!-- Foto Profil -->
                    <div class="profile-card">
                        <h3><i class="fas fa-camera"></i> Foto Profil</h3>
                        <div class="photo-upload">
                            <?php
                            $preview_foto = $default_avatar;
                            if (!empty($profile['foto_user']) && file_exists('adminpanel/src/images/user/' . $profile['foto_user'])) {
                                $preview_foto = 'adminpanel/src/images/user/' . $profile['foto_user'];
                            }
                            ?>
                            <img src="<?php echo $preview_foto; ?>" class="photo-preview" id="photoPreview">
                            <form method="POST" enctype="multipart/form-data">
                                <input type="file" name="foto_user" id="foto_user" accept="image/*"
                                    style="display: none;" onchange="previewPhoto(this)">
                                <div class="photo-buttons">
                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                        onclick="document.getElementById('foto_user').click()">
                                        <i class="fas fa-camera"></i> Pilih Foto
                                    </button>
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fas fa-upload"></i> Upload
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Informasi Singkat -->
                    <div class="profile-card">
                        <h3><i class="fas fa-user-circle"></i> Informasi</h3>
                        <div class="info-item">
                            <div class="info-icon"><i class="fas fa-user"></i></div>
                            <div class="info-label">Username</div>
                            <div class="info-value"><?php echo htmlspecialchars($profile['username_user']); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon"><i class="fas fa-phone"></i></div>
                            <div class="info-label">No. HP</div>
                            <div class="info-value"><?php echo htmlspecialchars($profile['nohp_user'] ?? '-'); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
                            <div class="info-label">Lokasi</div>
                            <div class="info-value">
                                <?php
                                $loc = [];
                                if (!empty($profile['nama_kota']))
                                    $loc[] = $profile['nama_kota'];
                                if (!empty($profile['nama_provinsi']))
                                    $loc[] = $profile['nama_provinsi'];
                                echo !empty($loc) ? implode(', ', $loc) : '-';
                                ?>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon"><i class="fas fa-venus-mars"></i></div>
                            <div class="info-label">Jenis Kelamin</div>
                            <div class="info-value"><?php echo getGenderLabel($profile['jk_user'] ?? ''); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon"><i class="fas fa-birthday-cake"></i></div>
                            <div class="info-label">Tanggal Lahir</div>
                            <div class="info-value">
                                <?php echo !empty($profile['tanggallahir_user']) ? date('d F Y', strtotime($profile['tanggallahir_user'])) : '-'; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Media Sosial -->
                    <div class="profile-card">
                        <h3><i class="fas fa-share-alt"></i> Media Sosial</h3>
                        <?php if (!empty($profile['instagram_user'])): ?>
                            <a href="<?php echo htmlspecialchars($profile['instagram_user']); ?>" target="_blank"
                                class="social-item">
                                <div class="social-icon instagram"><i class="fab fa-instagram"></i></div>
                                <div class="info-value">
                                    <?php echo htmlspecialchars($instagram_display); ?>
                                </div>
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($profile['facebook_user'])): ?>
                            <a href="<?php echo htmlspecialchars($profile['facebook_user']); ?>" target="_blank"
                                class="social-item">
                                <div class="social-icon facebook"><i class="fab fa-facebook-f"></i></div>
                                <div class="info-value">
                                    <?php echo htmlspecialchars($facebook_display); ?>
                                </div>
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($profile['linkedin_user'])): ?>
                            <a href="<?php echo htmlspecialchars($profile['linkedin_user']); ?>" target="_blank"
                                class="social-item">
                                <div class="social-icon linkedin"><i class="fab fa-linkedin-in"></i></div>
                                <div class="info-value">
                                    <?php echo htmlspecialchars($linkedin_display); ?>
                                </div>
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        <?php endif; ?>
                        <?php if (empty($profile['instagram_user']) && empty($profile['facebook_user']) && empty($profile['linkedin_user'])): ?>
                            <p class="text-muted text-center mb-0"><i class="fas fa-info-circle"></i> Belum ada media sosial
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- RIGHT COLUMN - ACCORDION STYLE -->
                <div class="col-lg-8">
                    <!-- Edit Profil -->
                    <div class="profile-card">
                        <h3 onclick="toggleAccordion('editProfileForm')">
                            <i class="fas fa-edit"></i> Edit Profil
                            <i class="fas fa-chevron-down toggle-icon" id="editProfileIcon"></i>
                        </h3>
                        <div id="editProfileForm" class="accordion-content">
                            <form method="POST">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><i class="fas fa-user"></i> Nama Lengkap</label>
                                            <input type="text" class="form-control" name="nama_user"
                                                value="<?php echo htmlspecialchars($profile['nama_user'] ?? ''); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><i class="fas fa-phone"></i> Nomor HP</label>
                                            <input type="tel" class="form-control" name="nohp_user"
                                                value="<?php echo htmlspecialchars($profile['nohp_user'] ?? ''); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><i class="fas fa-calendar"></i> Tanggal Lahir</label>
                                            <input type="date" class="form-control" name="tanggallahir_user"
                                                value="<?php echo formatDate($profile['tanggallahir_user'] ?? ''); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><i class="fas fa-venus-mars"></i> Jenis Kelamin</label>
                                            <select class="form-control" name="jk_user">
                                                <option value="">Pilih</option>
                                                <option value="L" <?php echo ($profile['jk_user'] ?? '') == 'L' ? 'selected' : ''; ?>>Laki-laki</option>
                                                <option value="P" <?php echo ($profile['jk_user'] ?? '') == 'P' ? 'selected' : ''; ?>>Perempuan</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><i class="fas fa-map-marker-alt"></i> Provinsi</label>
                                            <select class="form-control" name="id_provinsi" id="provinsi"
                                                onchange="loadCities()">
                                                <option value="">Pilih</option>
                                                <?php foreach ($provinces as $province): ?>
                                                    <option value="<?php echo $province['id_provinsi']; ?>" <?php echo ($profile['id_provinsi'] ?? '') == $province['id_provinsi'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($province['nama_provinsi']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><i class="fas fa-city"></i> Kota</label>
                                            <select class="form-control" name="id_kota" id="kota">
                                                <option value="">Pilih</option>
                                                <?php foreach ($cities as $city): ?>
                                                    <option value="<?php echo $city['id_kota']; ?>" <?php echo ($profile['id_kota'] ?? '') == $city['id_kota'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($city['nama_kota']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label><i class="fas fa-align-left"></i> Deskripsi Diri</label>
                                            <textarea class="form-control" name="deskripsi_user" rows="3"
                                                placeholder="Ceritakan tentang diri Anda..."><?php echo htmlspecialchars($profile['deskripsi_user'] ?? ''); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label><i class="fas fa-star"></i> Keahlian (pisahkan dengan enter)</label>
                                            <textarea class="form-control" name="kelebihan_user" rows="3"
                                                placeholder="Contoh:&#10;PHP&#10;JavaScript&#10;UI/UX Design"><?php echo htmlspecialchars($profile['kelebihan_user'] ?? ''); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label><i class="fas fa-briefcase"></i> Riwayat Pekerjaan (pisahkan dengan
                                                enter)</label>
                                            <textarea class="form-control" name="riwayatpekerjaan_user" rows="3"
                                                placeholder="Contoh:&#10;Web Developer - PT ABC (2020-2023)&#10;UI Designer - Freelance (2023-sekarang)"><?php echo htmlspecialchars($profile['riwayatpekerjaan_user'] ?? ''); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label><i class="fas fa-trophy"></i> Prestasi & Sertifikasi (pisahkan dengan
                                                enter)</label>
                                            <textarea class="form-control" name="prestasi_user" rows="3"
                                                placeholder="Contoh:&#10;Juara 1 Hackathon 2024&#10;Sertifikasi BNSP Web Development"><?php echo htmlspecialchars($profile['prestasi_user'] ?? ''); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label><i class="fab fa-instagram"></i> Instagram</label>
                                            <input type="text" class="form-control" name="instagram_user"
                                                value="<?php echo htmlspecialchars($profile['instagram_user'] ?? ''); ?>"
                                                placeholder="https://instagram.com/username">
                                            <small class="text-muted">Masukkan URL lengkap</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label><i class="fab fa-facebook"></i> Facebook</label>
                                            <input type="text" class="form-control" name="facebook_user"
                                                value="<?php echo htmlspecialchars($profile['facebook_user'] ?? ''); ?>"
                                                placeholder="https://facebook.com/username">
                                            <small class="text-muted">Masukkan URL lengkap</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label><i class="fab fa-linkedin"></i> LinkedIn</label>
                                            <input type="text" class="form-control" name="linkedin_user"
                                                value="<?php echo htmlspecialchars($profile['linkedin_user'] ?? ''); ?>"
                                                placeholder="https://linkedin.com/in/username">
                                            <small class="text-muted">Masukkan URL lengkap</small>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" name="update_profile" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan Profil
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- PORTOFOLIO -->
                    <div class="profile-card">
                        <h3 onclick="toggleAccordion('portfolioForm')">
                            <i class="fas fa-briefcase"></i> Portofolio
                            <i class="fas fa-chevron-down toggle-icon" id="portfolioIcon"></i>
                        </h3>
                        <div id="portfolioForm" class="accordion-content">
                            <?php if (!empty($profile['judul_porto'])): ?>
                                <div class="portfolio-card">
                                    <?php if (!empty($profile['gambar_porto']) && file_exists('adminpanel/src/images/portfolio/' . $profile['gambar_porto'])): ?>
                                        <img src="adminpanel/src/images/portfolio/<?php echo $profile['gambar_porto']; ?>"
                                            class="portfolio-image" alt="Portfolio">
                                    <?php else: ?>
                                        <div
                                            style="height: 200px; background: linear-gradient(135deg, var(--primary-light), var(--primary)); display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-image" style="font-size: 60px; color: white; opacity: 0.5;"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="portfolio-body">
                                        <h4 class="portfolio-title"><?php echo htmlspecialchars($profile['judul_porto']); ?>
                                        </h4>
                                        <?php if (!empty($profile['link_porto'])): ?>
                                            <a href="<?php echo htmlspecialchars($profile['link_porto']); ?>" target="_blank"
                                                class="portfolio-link">
                                                <i class="fas fa-external-link-alt"></i> Lihat Portofolio
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php else: ?>
                                <p class="text-muted text-center"><i class="fas fa-info-circle"></i> Belum ada portofolio
                                </p>
                            <?php endif; ?>

                            <hr>
                            <form method="POST" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label><i class="fas fa-tag"></i> Judul Portofolio</label>
                                    <input type="text" class="form-control" name="judul_porto"
                                        value="<?php echo htmlspecialchars($profile['judul_porto'] ?? ''); ?>"
                                        placeholder="Contoh: Website E-commerce">
                                </div>
                                <div class="form-group">
                                    <label><i class="fas fa-image"></i> Gambar Portofolio</label>
                                    <input type="file" class="form-control" name="gambar_porto" accept="image/*">
                                    <small class="text-muted">Format: JPG, PNG (Max 2MB)</small>
                                </div>
                                <div class="form-group">
                                    <label><i class="fas fa-link"></i> Link Portofolio</label>
                                    <input type="url" class="form-control" name="link_porto"
                                        value="<?php echo htmlspecialchars($profile['link_porto'] ?? ''); ?>"
                                        placeholder="https://...">
                                </div>
                                <button type="submit" name="update_portfolio" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan Portofolio
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Ubah Username -->
                    <div class="profile-card">
                        <h3 onclick="toggleAccordion('usernameForm')">
                            <i class="fas fa-user-tag"></i> Ubah Username
                            <i class="fas fa-chevron-down toggle-icon" id="usernameIcon"></i>
                        </h3>
                        <div id="usernameForm" class="accordion-content">
                            <form method="POST">
                                <div class="form-group">
                                    <label>Username Baru</label>
                                    <input type="text" class="form-control" name="username"
                                        value="<?php echo htmlspecialchars($profile['username_user']); ?>" required>
                                </div>
                                <button type="submit" name="update_username" class="btn btn-primary">
                                    <i class="fas fa-sync-alt"></i> Ubah Username
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Ubah Password -->
                    <div class="profile-card">
                        <h3 onclick="toggleAccordion('passwordForm')">
                            <i class="fas fa-lock"></i> Ubah Password
                            <i class="fas fa-chevron-down toggle-icon" id="passwordIcon"></i>
                        </h3>
                        <div id="passwordForm" class="accordion-content">
                            <form method="POST">
                                <div class="form-group">
                                    <label>Password Saat Ini</label>
                                    <input type="password" class="form-control" name="current_password" required>
                                </div>
                                <div class="form-group">
                                    <label>Password Baru</label>
                                    <input type="password" class="form-control" name="new_password" required>
                                    <small class="text-muted">Minimal 6 karakter</small>
                                </div>
                                <div class="form-group">
                                    <label>Konfirmasi Password</label>
                                    <input type="password" class="form-control" name="confirm_password" required>
                                </div>
                                <button type="submit" name="update_password" class="btn btn-primary">
                                    <i class="fas fa-shield-alt"></i> Ubah Password
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <section class="footer-Content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 col-md-3 col-xs-12">
                        <div class="widget">
                            <div class="footer-logo">
                                <img src="assets/img/logo2.png" alt="" />
                            </div>
                            <div class="textwidget">
                                <p>Platform yang menghubungkan pencari kerja berbakat dengan perusahaan terbaik.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-4 col-xs-12">
                        <div class="widget">
                            <h3 class="block-title">Quick Links</h3>
                            <ul class="menu">
                                <li><a href="index.php">Cari Lowongan</a></li>
                                <li><a href="jelajahi-perusahaan.php">Perusahaan</a></li>
                                <li><a href="statuslamaran.php">Status Lamaran</a></li>
                            </ul>
                            <ul class="menu">
                                <li><a href="login.php">Masuk</a></li>
                                <li><a href="register.php">Daftar</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <div id="copyright">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="site-info text-center">
                            <p>© 2025 LinkUp. All rights reserved</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="assets/js/jquery-min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/owl.carousel.min.js"></script>
    <script src="assets/js/jquery.slicknav.js"></script>
    <script src="assets/js/jquery.counterup.min.js"></script>
    <script src="assets/js/waypoints.min.js"></script>
    <script src="assets/js/main.js"></script>

    <script>
        function previewPhoto(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('photoPreview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function loadCities() {
            var provinceId = document.getElementById('provinsi').value;
            var citySelect = document.getElementById('kota');
            if (provinceId) {
                fetch('config/get_cities.php?province_id=' + provinceId)
                    .then(response => response.json())
                    .then(data => {
                        citySelect.innerHTML = '<option value="">Pilih Kota</option>';
                        data.forEach(city => {
                            citySelect.innerHTML += '<option value="' + city.id_kota + '">' + city.nama_kota + '</option>';
                        });
                    })
                    .catch(error => console.error('Error:', error));
            } else {
                citySelect.innerHTML = '<option value="">Pilih Kota</option>';
            }
        }

        function toggleAccordion(id) {
            var content = document.getElementById(id);
            var icon = document.getElementById(id + 'Icon');

            if (content.classList.contains('show')) {
                content.classList.remove('show');
                if (icon) {
                    icon.classList.remove('fa-chevron-up');
                    icon.classList.add('fa-chevron-down');
                }
            } else {
                // Tutup semua accordion lain terlebih dahulu (opsional - biarkan komentar jika ingin multiple open)
                // var allContents = document.querySelectorAll('.accordion-content');
                // allContents.forEach(function(item) {
                //     item.classList.remove('show');
                // });
                // var allIcons = document.querySelectorAll('.toggle-icon');
                // allIcons.forEach(function(item) {
                //     item.classList.remove('fa-chevron-up');
                //     item.classList.add('fa-chevron-down');
                // });

                content.classList.add('show');
                if (icon) {
                    icon.classList.remove('fa-chevron-down');
                    icon.classList.add('fa-chevron-up');
                }
            }
        }
    </script>
</body>

</html>