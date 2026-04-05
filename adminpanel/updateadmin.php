<?php
session_start();
require_once 'koneksi.php';

$db = new database();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id_admin'];
    $nama = $_POST['nama_admin'];
    $email = $_POST['email_admin'];
    $password = $_POST['password_admin'] ?? '';

    // Validasi nama admin tidak boleh mengandung spasi
    if (strpos($nama, ' ') !== false) {
        echo json_encode([
            'success' => false,
            'message' => 'Nama admin tidak boleh mengandung spasi! Gunakan format tanpa spasi (contoh: JohnDoe).'
        ]);
        exit();
    }

    // Handle foto upload dengan validasi yang lebih ketat
    $foto = null;
    $uploadError = '';

    if (isset($_FILES['foto_admin']) && $_FILES['foto_admin']['error'] != 4) { // Error 4 = No file uploaded
        if ($_FILES['foto_admin']['error'] != 0) {
            // Ada error pada upload
            switch ($_FILES['foto_admin']['error']) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $uploadError = 'Ukuran file terlalu besar. Maksimal 2MB.';
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $uploadError = 'File hanya terupload sebagian.';
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $uploadError = 'Tidak ada file yang diupload.';
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $uploadError = 'Folder temporary tidak ditemukan.';
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $uploadError = 'Gagal menulis file ke disk.';
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $uploadError = 'Upload dihentikan oleh ekstensi PHP.';
                    break;
                default:
                    $uploadError = 'Terjadi kesalahan saat upload.';
            }
        } else {
            // Validasi file
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            $fileType = mime_content_type($_FILES['foto_admin']['tmp_name']);
            $maxSize = 2 * 1024 * 1024; // 2MB

            // Cek ukuran file
            if ($_FILES['foto_admin']['size'] > $maxSize) {
                $uploadError = 'Ukuran file terlalu besar. Maksimal 2MB.';
            }
            // Cek tipe file
            elseif (!in_array($fileType, $allowedTypes)) {
                $uploadError = 'Format file tidak didukung. Hanya JPG, PNG, GIF yang diperbolehkan.';
            }
            // Cek ekstensi file
            else {
                $fileName = $_FILES['foto_admin']['name'];
                $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $allowedExt = ['jpg', 'jpeg', 'png', 'gif'];

                if (!in_array($fileExt, $allowedExt)) {
                    $uploadError = 'Ekstensi file tidak valid.';
                }
            }

            // Jika tidak ada error, proses upload
            if (empty($uploadError)) {
                $uploadDir = "src/images/user/";

                // Pastikan folder ada
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                // Generate nama file unik
                $fileName = time() . '_' . uniqid() . '.' . $fileExt;
                $targetFile = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['foto_admin']['tmp_name'], $targetFile)) {
                    $foto = $fileName;
                } else {
                    $uploadError = 'Gagal menyimpan file.';
                }
            }
        }

        // Jika ada error upload, return error
        if (!empty($uploadError)) {
            echo json_encode([
                'success' => false,
                'message' => 'Error upload foto: ' . $uploadError
            ]);
            exit();
        }
    }

    // Update data admin
    try {
        if ($db->update_admin($id, $nama, $email, $password, $foto)) {
            // Jika admin yang diedit adalah admin yang sedang login, update session
            if ($_SESSION['id_admin'] == $id) {
                $admin_data = $db->get_admin_session_data($id);
                $_SESSION['nama_admin'] = $admin_data['nama_admin'];
                $_SESSION['email_admin'] = $admin_data['email_admin'];
                if ($foto) {
                    $_SESSION['foto_admin'] = $foto;
                }
            }

            echo json_encode([
                'success' => true,
                'message' => 'Data admin berhasil diupdate'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Gagal mengupdate data admin'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Metode request tidak valid'
    ]);
}
?>