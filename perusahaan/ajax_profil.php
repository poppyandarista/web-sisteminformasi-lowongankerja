<?php
// perusahaan/ajax_profil.php
session_start();
require_once 'koneksi_perusahaan.php';

header('Content-Type: application/json');

if (!isset($_SESSION['company_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$company_id = $_SESSION['company_id'];
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'update_profil':
        $nama_perusahaan = $_POST['nama_perusahaan'] ?? '';
        $no_hp = $_POST['no_hp'] ?? '';
        $alamat = $_POST['alamat'] ?? '';
        $id_provinsi = $_POST['id_provinsi'] ?? '';
        $id_kota = $_POST['id_kota'] ?? '';
        $deskripsi = $_POST['deskripsi_perusahaan'] ?? '';

        // Handle logo upload ke folder adminpanel
        $logo_name = null;
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['logo']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (in_array($ext, $allowed)) {
                // Buat nama file unik dengan timestamp
                $logo_name = time() . '_' . uniqid() . '.' . $ext;
                // Path ke folder adminpanel
                $upload_path = '../adminpanel/src/images/company/';

                // Buat folder jika belum ada
                if (!file_exists($upload_path)) {
                    mkdir($upload_path, 0777, true);
                }

                if (move_uploaded_file($_FILES['logo']['tmp_name'], $upload_path . $logo_name)) {
                    // Hapus logo lama jika ada
                    $old_logo = $db->getPerusahaanById($company_id)['logo_perusahaan'] ?? null;
                    if ($old_logo && file_exists($upload_path . $old_logo)) {
                        unlink($upload_path . $old_logo);
                    }
                } else {
                    $logo_name = null;
                }
            }
        }

        // Update data perusahaan
        $data = [
            'nama_perusahaan' => $nama_perusahaan,
            'deskripsi' => $deskripsi,
            'id_provinsi' => $id_provinsi,
            'id_kota' => $id_kota,
            'alamat' => $alamat,
            'nohp' => $no_hp,
            'logo' => $logo_name
        ];

        $result = $db->updatePerusahaan($company_id, $data);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Profil berhasil diperbarui']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal memperbarui profil']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>