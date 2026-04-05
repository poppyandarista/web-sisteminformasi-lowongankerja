<?php
require_once 'session_check.php';
require_once 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new database();

    $id_user = $_POST['id_user'];
    $data = [
        'email_user' => $_POST['email_user'],
        'username_user' => $_POST['username_user'] ?? '',
        'nama_user' => $_POST['nama_user'],
        'nohp_user' => $_POST['nohp_user'] ?? '',
        'id_provinsi' => !empty($_POST['id_provinsi']) ? $_POST['id_provinsi'] : null,
        'id_kota' => !empty($_POST['id_kota']) ? $_POST['id_kota'] : null,
        'jk_user' => $_POST['jk_user'] ?? '',
    ];

    // Handle password jika diisi
    if (!empty($_POST['password_user'])) {
        $data['password_user'] = $_POST['password_user'];
    }

    // Handle upload foto
    if (isset($_FILES['foto_user']) && $_FILES['foto_user']['error'] == 0) {
        $foto = $_FILES['foto_user'];
        $nama_foto = time() . "_" . basename($foto['name']);
        $target_dir = "src/images/user/";

        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $target_file = $target_dir . $nama_foto;

        // Validasi file
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 2 * 1024 * 1024; // 2MB

        if ($foto['size'] <= $max_size && in_array($foto['type'], $allowed_types)) {
            if (move_uploaded_file($foto['tmp_name'], $target_file)) {
                $data['foto_user'] = $nama_foto;
            }
        }
    }

    if ($db->update_pelamar($id_user, $data)) {
        header("Location: datapelamar.php?success=Data pelamar berhasil diupdate");
        exit();
    } else {
        header("Location: datapelamar.php?error=Gagal mengupdate data pelamar");
        exit();
    }
} else {
    header("Location: datapelamar.php");
    exit();
}
?>