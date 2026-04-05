<?php
// Aktifkan error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'session_check.php';
include 'koneksi.php';

$db = new database();

// Debug: Cek apakah data POST terkirim
// echo "<pre>";
// print_r($_POST);
// print_r($_FILES);
// echo "</pre>";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $nama = $_POST['nama_admin'] ?? '';
  $email = $_POST['email_admin'] ?? '';
  $pass = $_POST['password_admin'] ?? '';
  $foto = $_FILES['foto_admin'] ?? null;

  // Validasi input
  if (empty($nama) || empty($email) || empty($pass)) {
    echo json_encode([
      'success' => false,
      'message' => 'Semua field wajib diisi!',
      'title' => 'Form Tidak Lengkap'
    ]);
    exit();
  }

  // Validasi nama admin tidak boleh mengandung spasi
  if (strpos($nama, ' ') !== false) {
    echo json_encode([
      'success' => false,
      'message' => 'Nama admin tidak boleh mengandung spasi! Gunakan format tanpa spasi (contoh: JohnDoe).',
      'title' => 'Error Validasi Nama'
    ]);
    exit();
  }

  // Validasi email unik
  try {
    $check_email = $db->koneksi->prepare("SELECT id_admin FROM admin WHERE email_admin = ?");
    if ($check_email === false) {
      echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $db->koneksi->error,
        'title' => 'Error Database'
      ]);
      exit();
    }

    $check_email->bind_param("s", $email);
    $check_email->execute();
    $result = $check_email->get_result();

    if ($result->num_rows > 0) {
      echo json_encode([
        'success' => false,
        'message' => 'Email sudah digunakan! Silakan gunakan email lain.',
        'title' => 'Error Email Duplikat'
      ]);
      $check_email->close();
      exit();
    }
    $check_email->close();
  } catch (Exception $e) {
    echo json_encode([
      'success' => false,
      'message' => 'Exception: ' . $e->getMessage(),
      'title' => 'Error Exception'
    ]);
    exit();
  }

  // Panggil fungsi tambah_admin
  $simpan = $db->tambah_admin($nama, $email, $pass, $foto);

  if ($simpan) {
    echo json_encode([
      'success' => true,
      'message' => 'Data admin berhasil ditambahkan!',
      'title' => 'Tambah Admin Berhasil'
    ]);
    exit();
  } else {
    echo json_encode([
      'success' => false,
      'message' => 'Gagal menambah data admin! Error: ' . mysqli_error($db->koneksi),
      'title' => 'Error Database'
    ]);
    exit();
  }
} elseif (isset($_GET['action']) && $_GET['action'] == 'hapus' && isset($_GET['id'])) {
  // Handler untuk hapus admin
  $id = $_GET['id'];

  // Validasi ID
  if (!is_numeric($id) || $id <= 0) {
    $_SESSION['alert'] = [
      'type' => 'error',
      'title' => 'ID Tidak Valid',
      'message' => 'ID admin tidak valid!'
    ];
    header("Location: dataadmin.php");
    exit();
  }

  // Cegah hapus admin yang sedang login
  if (isset($_SESSION['id_admin']) && $_SESSION['id_admin'] == $id) {
    $_SESSION['alert'] = [
      'type' => 'warning',
      'title' => 'Tidak Dapat Dihapus',
      'message' => 'Tidak dapat menghapus diri sendiri!'
    ];
    header("Location: dataadmin.php");
    exit();
  }

  // Cegah hapus admin lain yang sedang aktif/login
  $active_admins = $db->get_active_admins();
  if (in_array($id, $active_admins)) {
    // Cek apakah ini admin yang sedang login
    if (isset($_SESSION['id_admin']) && $_SESSION['id_admin'] != $id) {
      $_SESSION['alert'] = [
        'type' => 'warning',
        'title' => 'Admin Sedang Aktif',
        'message' => 'Tidak dapat menghapus admin lain yang sedang aktif/login!'
      ];
      header("Location: dataadmin.php");
      exit();
    }
  }

  // Panggil fungsi hapus_admin
  $hapus = $db->hapus_admin($id);

  if ($hapus) {
    $_SESSION['alert'] = [
      'type' => 'success',
      'title' => 'Hapus Admin Berhasil',
      'message' => 'Data admin berhasil dihapus!'
    ];
    header("Location: dataadmin.php");
  } else {
    $_SESSION['alert'] = [
      'type' => 'error',
      'title' => 'Error Database',
      'message' => 'Gagal menghapus data admin!'
    ];
    header("Location: dataadmin.php");
  }
} else {
  $_SESSION['alert'] = [
    'type' => 'error',
    'title' => 'Akses Tidak Valid',
    'message' => 'Akses tidak valid!'
  ];
  header("Location: dataadmin.php");
}

?>

<script>
  // Untuk form tambah admin (jika ada)
  document.getElementById('formTambahAdmin').addEventListener('submit', function (e) {
    e.preventDefault();

    // Validasi file
    const fileInput = document.getElementById('fileInput');
    const file = fileInput.files[0];

    if (file) {
      // Validasi ukuran file
      const maxSize = 2 * 1024 * 1024;
      if (file.size > maxSize) {
        showNotification('error', 'Ukuran foto terlalu besar! Maksimal 2MB.');
        return;
      }

      // Validasi tipe file
      const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
      if (!allowedTypes.includes(file.type)) {
        showNotification('error', 'Format file tidak didukung! Hanya JPG, PNG, atau GIF.');
        return;
      }
    }

    // Lanjutkan submit...
  });
</script>