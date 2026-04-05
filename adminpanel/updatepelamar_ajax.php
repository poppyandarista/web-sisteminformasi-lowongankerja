<?php
require_once 'session_check.php';
require_once 'koneksi.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new database();

    $id_user = $_POST['id_user'];

    // Siapkan data untuk update
    $data = [
        'email_user' => $_POST['email_user'],
        'username_user' => $_POST['username_user'] ?? '',
        'nama_user' => $_POST['nama_user'],
        'nohp_user' => $_POST['nohp_user'] ?? '',
        'jk_user' => $_POST['jk_user'] ?? '',
        'deskripsi_user' => $_POST['deskripsi_user'] ?? '',
    ];

    // Tanggal lahir jika ada
    if (!empty($_POST['tanggallahir_user'])) {
        $data['tanggallahir_user'] = $_POST['tanggallahir_user'];
    }

    // Handle password jika diisi
    if (!empty($_POST['password_user'])) {
        $data['password_user'] = $_POST['password_user'];
    }

    // Handle upload foto jika ada
    if (isset($_FILES['foto_user']) && $_FILES['foto_user']['error'] == 0) {
        $foto = $_FILES['foto_user'];

        // Validasi ukuran file (max 2MB)
        if ($foto['size'] > 2 * 1024 * 1024) {
            echo json_encode([
                'success' => false,
                'message' => 'Ukuran foto terlalu besar (maks 2MB)'
            ]);
            exit;
        }

        // Validasi tipe file
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!in_array($foto['type'], $allowed_types)) {
            echo json_encode([
                'success' => false,
                'message' => 'Format foto tidak didukung (hanya JPG, PNG, GIF)'
            ]);
            exit;
        }

        // Generate nama file unik
        $nama_foto = time() . '_' . basename($foto['name']);
        $target_dir = "src/images/user/";

        // Pastikan folder tujuan ada
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $target_file = $target_dir . $nama_foto;

        // Upload file
        if (move_uploaded_file($foto['tmp_name'], $target_file)) {
            $data['foto_user'] = $nama_foto;
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Gagal mengupload foto'
            ]);
            exit;
        }
    }

    // TAMBAHKAN FIELD BARU
    $additional_fields = [
        'kelebihan_user',
        'riwayatpekerjaan_user',
        'prestasi_user',
        'judul_porto',
        'link_porto',
        'instagram_user',
        'facebook_user',
        'linkedin_user',
        'id_provinsi',
        'id_kota'
    ];

    foreach ($additional_fields as $field) {
        if (isset($_POST[$field])) {
            $data[$field] = $_POST[$field];
        }
    }

    // Debug: log data yang akan diupdate
    error_log("Updating pelamar ID: $id_user");
    error_log("Data: " . print_r($data, true));

    if ($db->update_pelamar($id_user, $data)) {
        echo json_encode([
            'success' => true,
            'message' => 'Data pelamar berhasil diupdate'
        ]);
    } else {
        error_log("Error updating pelamar ID: $id_user");
        echo json_encode([
            'success' => false,
            'message' => 'Gagal mengupdate data pelamar. Silakan coba lagi.'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Metode tidak valid'
    ]);
}
?>