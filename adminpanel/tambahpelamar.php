<?php
require_once 'session_check.php';
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db = new database();

    // Data dari form
    $data = [
        'email' => $_POST['email_user'],
        'username' => $_POST['username_user'] ?? '',
        'password' => password_hash($_POST['password_user'], PASSWORD_DEFAULT),
        'nama_user' => $_POST['nama_user'] ?? '',
        'nohp_user' => $_POST['nohp_user'] ?? '',
        'provinsi_user' => $_POST['provinsi_user'] ?? '',
        'kota_user' => $_POST['kota_user'] ?? '',
        'tanggallahir_user' => $_POST['tanggallahir_user'] ?? '',
        'jk_user' => $_POST['jk_user'] ?? '',
        'deskripsi_user' => $_POST['deskripsi_user'] ?? '',
        'kelebihan_user' => $_POST['kelebihan_user'] ?? '',
        'riwayatpekerjaan_user' => $_POST['riwayatpekerjaan_user'] ?? '',
        'prestasi_user' => $_POST['prestasi_user'] ?? '',
        'foto_user' => '',
        'instagram_user' => $_POST['instagram_user'] ?? '',
        'facebook_user' => $_POST['facebook_user'] ?? '',
        'linkedin_user' => $_POST['linkedin_user'] ?? ''
    ];

    // Handle file upload
    if (isset($_FILES['foto_user']) && $_FILES['foto_user']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "src/images/user/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_extension = pathinfo($_FILES['foto_user']['name'], PATHINFO_EXTENSION);
        $new_filename = 'user_' . time() . '_' . uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;

        // Check file size (max 2MB)
        if ($_FILES['foto_user']['size'] > 2097152) {
            $_SESSION['error'] = "Ukuran file terlalu besar. Maksimal 2MB.";
            header("Location: datapelamar.php");
            exit();
        }

        // Allow certain file formats
        $allowed_extensions = array("jpg", "jpeg", "png", "gif");
        if (!in_array(strtolower($file_extension), $allowed_extensions)) {
            $_SESSION['error'] = "Hanya file JPG, JPEG, PNG & GIF yang diperbolehkan.";
            header("Location: datapelamar.php");
            exit();
        }

        if (move_uploaded_file($_FILES['foto_user']['tmp_name'], $target_file)) {
            $data['foto_user'] = $new_filename;
        }
    }

    // Tambah ke database
    if ($db->tambah_pelamar($data)) {
        $_SESSION['success'] = "Data pelamar berhasil ditambahkan";
    } else {
        $_SESSION['error'] = "Gagal menambahkan data pelamar";
    }

    header("Location: datapelamar.php");
    exit();
}
?>