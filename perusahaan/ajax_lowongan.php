<?php
// perusahaan/ajax_lowongan.php
session_start();
require_once 'koneksi_perusahaan.php';

header('Content-Type: application/json');

if (!isset($_SESSION['company_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$company_id = $_SESSION['company_id'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Path ke folder adminpanel untuk menyimpan gambar lowongan (SAMA UNTUK INSERT DAN UPDATE)
$upload_dir = '../adminpanel/src/images/jobs/';

switch ($action) {
    case 'get_detail':
        $id = intval($_GET['id'] ?? 0);
        $lowongan = $db->getLowonganById($id, $company_id);
        if ($lowongan) {
            echo json_encode(['success' => true, 'data' => $lowongan]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Lowongan tidak ditemukan']);
        }
        break;

    case 'save':
        $id = intval($_POST['lowongan_id'] ?? 0);

        // Upload gambar ke folder adminpanel
        $gambar_name = '';
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
            // Buat folder jika belum ada
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (in_array($ext, $allowed)) {
                $gambar_name = time() . '_' . uniqid() . '.' . $ext;
                $upload_result = move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_dir . $gambar_name);

                if (!$upload_result) {
                    echo json_encode(['success' => false, 'message' => 'Gagal upload gambar']);
                    exit();
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Format gambar tidak didukung. Gunakan JPG, PNG, GIF, atau WEBP']);
                exit();
            }
        }

        $data = [
            'judul' => $_POST['judul'] ?? '',
            'kategori' => !empty($_POST['kategori']) ? $_POST['kategori'] : null,
            'jenis' => !empty($_POST['jenis']) ? $_POST['jenis'] : null,
            'id_provinsi' => !empty($_POST['id_provinsi']) ? $_POST['id_provinsi'] : null,
            'id_kota' => !empty($_POST['id_kota']) ? $_POST['id_kota'] : null,
            'lokasi' => $_POST['lokasi'] ?? '',
            'gaji' => !empty($_POST['gaji']) ? $_POST['gaji'] : null,
            'kualifikasi' => $_POST['kualifikasi'] ?? '',
            'deskripsi' => $_POST['deskripsi'] ?? '',
            'tgl_tutup' => !empty($_POST['tgl_tutup']) ? $_POST['tgl_tutup'] : null,
            'status' => $_POST['status'] ?? 'Aktif',
            'gambar' => $gambar_name,
            'id_perusahaan' => $company_id
        ];

        if ($id > 0) {
            // UPDATE: Hapus gambar lama jika upload gambar baru
            if (!empty($gambar_name)) {
                $old = $db->getLowonganById($id, $company_id);
                if ($old && !empty($old['gambar'])) {
                    $old_file = $upload_dir . $old['gambar'];
                    if (file_exists($old_file)) {
                        unlink($old_file);
                    }
                }
            } else {
                // Gunakan gambar lama
                $old = $db->getLowonganById($id, $company_id);
                $data['gambar'] = $old['gambar'] ?? '';
            }

            $result = $db->updateLowongan($id, $data, $company_id);
            echo json_encode(['success' => $result, 'message' => $result ? 'Lowongan berhasil diupdate' : 'Gagal update lowongan']);
        } else {
            // INSERT: Langsung simpan dengan gambar yang sudah diupload
            $result = $db->insertLowongan($data);
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Lowongan berhasil ditambahkan']);
            } else {
                // Jika insert gagal, hapus gambar yang sudah terupload
                if (!empty($gambar_name) && file_exists($upload_dir . $gambar_name)) {
                    unlink($upload_dir . $gambar_name);
                }
                echo json_encode(['success' => false, 'message' => 'Gagal menambahkan lowongan']);
            }
        }
        break;

    case 'delete':
        $id = intval($_POST['id'] ?? 0);
        // Hapus gambar dari folder adminpanel
        $low = $db->getLowonganById($id, $company_id);
        if ($low && !empty($low['gambar'])) {
            $file = $upload_dir . $low['gambar'];
            if (file_exists($file)) {
                unlink($file);
            }
        }
        $result = $db->deleteLowongan($id, $company_id);
        echo json_encode(['success' => $result, 'message' => $result ? 'Lowongan berhasil dihapus' : 'Gagal hapus lowongan']);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>