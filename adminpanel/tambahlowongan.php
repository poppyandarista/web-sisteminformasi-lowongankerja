<?php
session_start();
include 'koneksi.php';

$db = new database();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $id_perusahaan = $_POST['id_perusahaan'] ?? '';
    $judul_lowongan = $_POST['judul_lowongan'] ?? '';
    $kategori_lowongan = $_POST['kategori_lowongan'] ?? '';
    $waktukerja = $_POST['waktukerja'] ?? '';
    $id_provinsi = $_POST['id_provinsi'] ?? null;
    $id_kota = $_POST['id_kota'] ?? null;
    $lokasi_lowongan = $_POST['lokasi_lowongan'] ?? '';
    $gaji_lowongan = $_POST['gaji_lowongan'] ? floatval($_POST['gaji_lowongan']) : null;
    $kualifikasi = $_POST['kualifikasi'] ?? '';
    $deskripsi_lowongan = $_POST['deskripsi_lowongan'] ?? '';
    $pertanyaan = $_POST['pertanyaan'] ?? '';
    $tanggal_tutup = $_POST['tanggal_tutup'] ?? null;
    $status = $_POST['status'] ?? 'Aktif';
    $gambar = $_FILES['gambar'] ?? null;

    // Pastikan kategori_lowongan adalah integer
    if (!is_numeric($kategori_lowongan)) {
        echo "<script>
                alert('Kategori tidak valid! Harap pilih kategori dari dropdown.');
                window.history.back();
              </script>";
        exit();
    }
    $kategori_lowongan = (int) $kategori_lowongan;

    // Validasi input wajib
    if (empty($id_perusahaan) || empty($judul_lowongan) || empty($kategori_lowongan) || empty($waktukerja)) {
        echo "<script>
                alert('Perusahaan, Judul, Kategori, dan Waktu Kerja wajib diisi!');
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

    // Handle upload gambar
    $nama_gambar = null;
    if ($gambar && $gambar['name'] != "") {
        $nama_gambar = time() . "_" . basename($gambar['name']);
        $target_dir = "src/images/jobs/";

        // Pastikan folder tujuan ada
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $target_file = $target_dir . $nama_gambar;

        // Cek ukuran file (maks 2MB)
        if ($gambar['size'] > 2000000) {
            echo "<script>
                    alert('Ukuran gambar terlalu besar! Maksimal 2MB.');
                    window.history.back();
                  </script>";
            exit();
        }

        // Cek tipe file
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($gambar['type'], $allowed_types)) {
            echo "<script>
                    alert('Format gambar tidak didukung! Gunakan JPG, PNG, atau GIF.');
                    window.history.back();
                  </script>";
            exit();
        }

        if (!move_uploaded_file($gambar['tmp_name'], $target_file)) {
            $nama_gambar = null; // Jika upload gagal, set null
        }
    }

    // Siapkan data untuk disimpan
    $data = [
        'id_perusahaan' => $id_perusahaan,
        'judul_lowongan' => $judul_lowongan,
        'kategori_lowongan' => $kategori_lowongan,
        'waktukerja' => $waktukerja,
        'id_provinsi' => $id_provinsi,
        'id_kota' => $id_kota,
        'lokasi_lowongan' => $lokasi_lowongan,
        'gaji_lowongan' => $gaji_lowongan,
        'kualifikasi' => $kualifikasi,
        'deskripsi_lowongan' => $deskripsi_lowongan,
        'pertanyaan' => $pertanyaan,
        'tanggal_tutup' => $tanggal_tutup,
        'status' => $status,
        'gambar' => $nama_gambar
    ];

    // Panggil fungsi tambah_lowongan
    $simpan = $db->tambah_lowongan($data);

    if ($simpan) {
        echo "<script>
                alert('Data lowongan berhasil ditambahkan!');
                window.location.href = 'datalowongan.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal menambah data lowongan!');
                window.history.back();
              </script>";
    }
} else {
    echo "<script>
            alert('Akses tidak valid!');
            window.location.href = 'datalowongan.php';
          </script>";
}
?>