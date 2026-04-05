<?php
session_start();
include 'koneksi.php';

$db = new database();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $email = $_POST['email_perusahaan'] ?? '';
  $password = $_POST['password_perusahaan'] ?? '';
  $nama = $_POST['nama_perusahaan'] ?? '';
  $logo = $_FILES['logo_perusahaan'] ?? null;
  $deskripsi = $_POST['deskripsi_perusahaan'] ?? '';
  $id_provinsi = $_POST['id_provinsi'] ?? null;
  $id_kota = $_POST['id_kota'] ?? null;
  $alamat = $_POST['alamat_perusahaan'] ?? '';
  $nohp = $_POST['nohp_perusahaan'] ?? '';

  // Validasi input wajib
  if (empty($email) || empty($password) || empty($nama)) {
    echo "<script>
                alert('Email, Password, dan Nama Perusahaan wajib diisi!');
                window.history.back();
              </script>";
    exit();
  }

  // Validasi provinsi dan kota
  if (empty($id_provinsi) || empty($id_kota)) {
    echo "<script>
                alert('Provinsi dan Kota wajib dipilih!');
                window.history.back();
              </script>";
    exit();
  }

  // Panggil fungsi tambah_perusahaan
  $simpan = $db->tambah_perusahaan($email, $password, $nama, $logo, $deskripsi, $id_provinsi, $id_kota, $alamat, $nohp);

  if ($simpan) {
    header("Location: dataperusahaan.php");
    exit();
  } else {
    echo "<script>
                alert('Gagal menambah data perusahaan!');
                window.history.back();
              </script>";
  }
} else {
  header("Location: dataperusahaan.php");
  exit();
}
?>