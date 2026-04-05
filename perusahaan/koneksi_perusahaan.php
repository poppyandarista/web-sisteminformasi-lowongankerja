<?php
// perusahaan/koneksi_perusahaan.php
// Koneksi database untuk halaman perusahaan
// HAPUS session_start() di sini karena sudah dipanggil di file masing-masing

$host = "localhost";
$username = "root";
$password = "";
$database = "lowongankerja";

$koneksi = mysqli_connect($host, $username, $password, $database);

if (mysqli_connect_errno()) {
    die("Koneksi database gagal : " . mysqli_connect_error());
}

// Class database lengkap untuk perusahaan
class DatabasePerusahaan
{
    public $koneksi;

    function __construct()
    {
        global $koneksi;
        $this->koneksi = $koneksi;
    }

    // ==================== PROVINSI & KOTA ====================
    public function getAllProvinsi()
    {
        $query = "SELECT id_provinsi, nama_provinsi FROM provinsi ORDER BY nama_provinsi ASC";
        $result = mysqli_query($this->koneksi, $query);
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }

    public function getKotaByProvinsi($provinsi_id)
    {
        $query = "SELECT id_kota, nama_kota FROM kota WHERE id_provinsi = ? ORDER BY nama_kota ASC";
        $stmt = mysqli_prepare($this->koneksi, $query);
        mysqli_stmt_bind_param($stmt, "i", $provinsi_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        mysqli_stmt_close($stmt);
        return $data;
    }

    // ==================== PERUSAHAAN ====================
    public function cekEmailExists($email)
    {
        $query = "SELECT id_perusahaan FROM perusahaan WHERE email_perusahaan = ?";
        $stmt = mysqli_prepare($this->koneksi, $query);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $exists = mysqli_stmt_num_rows($stmt) > 0;
        mysqli_stmt_close($stmt);
        return $exists;
    }

    // Tambahkan fungsi ini di class DatabasePerusahaan
    public function validatePasswordStrength($password)
    {
        $errors = [];

        if (strlen($password) < 8) {
            $errors[] = "Password minimal 8 karakter";
        }
        if (!preg_match("/[A-Z]/", $password)) {
            $errors[] = "Password harus mengandung huruf besar (A-Z)";
        }
        if (!preg_match("/[a-z]/", $password)) {
            $errors[] = "Password harus mengandung huruf kecil (a-z)";
        }
        if (!preg_match("/[0-9]/", $password)) {
            $errors[] = "Password harus mengandung angka (0-9)";
        }
        if (!preg_match("/[!@#$%^&*(),.?\":{}|<>]/", $password)) {
            $errors[] = "Password harus mengandung karakter spesial (!@#$%^&* dll)";
        }

        return $errors;
    }

    // Ubah fungsi loginPerusahaan - BUKA HASH (jika password disimpan plain text)
    public function loginPerusahaan($email, $password)
    {
        $query = "SELECT * FROM perusahaan WHERE email_perusahaan = ?";
        $stmt = mysqli_prepare($this->koneksi, $query);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            // Bandingkan password langsung (TANPA HASH)
            if ($password === $row['password_perusahaan']) {
                mysqli_stmt_close($stmt);
                return $row;
            }
        }
        mysqli_stmt_close($stmt);
        return false;
    }

    // Ubah fungsi registerPerusahaan - Simpan password plain text
    public function registerPerusahaan($data)
    {
        // TANPA HASH - simpan password apa adanya
        $plain_password = $data['password']; // Password tanpa hash

        $query = "INSERT INTO perusahaan (nama_perusahaan, email_perusahaan, password_perusahaan, id_provinsi, id_kota, alamat_perusahaan, nohp_perusahaan) 
              VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($this->koneksi, $query);
        mysqli_stmt_bind_param(
            $stmt,
            "sssiiss",
            $data['nama_perusahaan'],
            $data['email'],
            $plain_password, // Simpan password plain text
            $data['id_provinsi'],
            $data['id_kota'],
            $data['alamat'],
            $data['nohp']
        );
        $result = mysqli_stmt_execute($stmt);
        $insert_id = mysqli_insert_id($this->koneksi);
        mysqli_stmt_close($stmt);

        if ($result) {
            return $insert_id;
        }
        return false;
    }

    public function getPerusahaanById($id)
    {
        $query = "SELECT p.*, pr.nama_provinsi, k.nama_kota 
                  FROM perusahaan p
                  LEFT JOIN provinsi pr ON p.id_provinsi = pr.id_provinsi
                  LEFT JOIN kota k ON p.id_kota = k.id_kota
                  WHERE p.id_perusahaan = ?";
        $stmt = mysqli_prepare($this->koneksi, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $data;
    }

    public function updatePerusahaan($id, $data)
    {
        $query = "UPDATE perusahaan SET 
                  nama_perusahaan = ?,
                  deskripsi_perusahaan = ?,
                  id_provinsi = ?,
                  id_kota = ?,
                  alamat_perusahaan = ?,
                  nohp_perusahaan = ?,
                  logo_perusahaan = ?
                  WHERE id_perusahaan = ?";
        $stmt = mysqli_prepare($this->koneksi, $query);
        mysqli_stmt_bind_param(
            $stmt,
            "ssiisssi",
            $data['nama_perusahaan'],
            $data['deskripsi'],
            $data['id_provinsi'],
            $data['id_kota'],
            $data['alamat'],
            $data['nohp'],
            $data['logo'],
            $id
        );
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }

    // ==================== LOWONGAN ====================
    public function getLowonganByPerusahaan($perusahaan_id)
    {
        $query = "SELECT l.*, pr.nama_provinsi, k.nama_kota 
                  FROM lowongan l
                  LEFT JOIN provinsi pr ON l.id_provinsi = pr.id_provinsi
                  LEFT JOIN kota k ON l.id_kota = k.id_kota
                  WHERE l.id_perusahaan = ?
                  ORDER BY l.tanggal_posting DESC";
        $stmt = mysqli_prepare($this->koneksi, $query);
        mysqli_stmt_bind_param($stmt, "i", $perusahaan_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        mysqli_stmt_close($stmt);
        return $data;
    }

    public function getLowonganById($id, $perusahaan_id)
    {
        $query = "SELECT * FROM lowongan WHERE id_lowongan = ? AND id_perusahaan = ?";
        $stmt = mysqli_prepare($this->koneksi, $query);
        mysqli_stmt_bind_param($stmt, "ii", $id, $perusahaan_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $data;
    }

    public function insertLowongan($data)
    {
        $query = "INSERT INTO lowongan (id_perusahaan, judul_lowongan, kategori_lowongan, id_jenis, 
                  id_provinsi, id_kota, lokasi_lowongan, gaji_lowongan, kualifikasi, 
                  deskripsi_lowongan, tanggal_tutup, status, gambar) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($this->koneksi, $query);
        mysqli_stmt_bind_param(
            $stmt,
            "isiiisssssssi",
            $data['id_perusahaan'],
            $data['judul'],
            $data['kategori'],
            $data['jenis'],
            $data['id_provinsi'],
            $data['id_kota'],
            $data['lokasi'],
            $data['gaji'],
            $data['kualifikasi'],
            $data['deskripsi'],
            $data['tgl_tutup'],
            $data['status'],
            $data['gambar']
        );
        $result = mysqli_stmt_execute($stmt);
        $insert_id = mysqli_insert_id($this->koneksi);
        mysqli_stmt_close($stmt);
        return $result ? $insert_id : false;
    }

    public function updateLowongan($id, $data, $perusahaan_id)
    {
        $query = "UPDATE lowongan SET 
                  judul_lowongan = ?,
                  kategori_lowongan = ?,
                  id_jenis = ?,
                  id_provinsi = ?,
                  id_kota = ?,
                  lokasi_lowongan = ?,
                  gaji_lowongan = ?,
                  kualifikasi = ?,
                  deskripsi_lowongan = ?,
                  tanggal_tutup = ?,
                  status = ?,
                  gambar = ?
                  WHERE id_lowongan = ? AND id_perusahaan = ?";
        $stmt = mysqli_prepare($this->koneksi, $query);
        mysqli_stmt_bind_param(
            $stmt,
            "siiissssssssii",
            $data['judul'],
            $data['kategori'],
            $data['jenis'],
            $data['id_provinsi'],
            $data['id_kota'],
            $data['lokasi'],
            $data['gaji'],
            $data['kualifikasi'],
            $data['deskripsi'],
            $data['tgl_tutup'],
            $data['status'],
            $data['gambar'],
            $id,
            $perusahaan_id
        );
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }

    public function deleteLowongan($id, $perusahaan_id)
    {
        $query = "DELETE FROM lowongan WHERE id_lowongan = ? AND id_perusahaan = ?";
        $stmt = mysqli_prepare($this->koneksi, $query);
        mysqli_stmt_bind_param($stmt, "ii", $id, $perusahaan_id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }

    // ==================== LAMARAN ====================
    public function getLamaranByPerusahaan($perusahaan_id)
    {
        $query = "SELECT l.*, low.judul_lowongan, u.username_user, u.email_user, p.nama_user, p.nohp_user, p.foto_user, p.deskripsi_user
                  FROM lamaran l
                  JOIN lowongan low ON l.id_lowongan = low.id_lowongan
                  JOIN user u ON l.id_user = u.id_user
                  LEFT JOIN profil p ON u.id_user = p.id_user
                  WHERE low.id_perusahaan = ?
                  ORDER BY l.tanggal_lamar DESC";
        $stmt = mysqli_prepare($this->koneksi, $query);
        mysqli_stmt_bind_param($stmt, "i", $perusahaan_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        mysqli_stmt_close($stmt);
        return $data;
    }

    public function updateLamaranStatus($id_lamaran, $status, $catatan, $perusahaan_id)
    {
        $query = "UPDATE lamaran l
                  JOIN lowongan low ON l.id_lowongan = low.id_lowongan
                  SET l.status_lamaran = ?, l.catatan_hrd = ?
                  WHERE l.id_lamaran = ? AND low.id_perusahaan = ?";
        $stmt = mysqli_prepare($this->koneksi, $query);
        mysqli_stmt_bind_param($stmt, "ssii", $status, $catatan, $id_lamaran, $perusahaan_id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }

    // ==================== PELAMAR (USER) ====================
    public function getPelamarByPerusahaan($perusahaan_id)
    {
        $query = "SELECT DISTINCT u.id_user, u.username_user, u.email_user, 
                  p.nama_user, p.nohp_user, p.tanggallahir_user, p.jk_user, 
                  p.deskripsi_user, p.kelebihan_user, p.riwayatpekerjaan_user, 
                  p.prestasi_user, p.foto_user, p.instagram_user, p.facebook_user, 
                  p.linkedin_user, prov.nama_provinsi, kota.nama_kota
                  FROM user u
                  JOIN lamaran l ON u.id_user = l.id_user
                  JOIN lowongan low ON l.id_lowongan = low.id_lowongan
                  LEFT JOIN profil p ON u.id_user = p.id_user
                  LEFT JOIN provinsi prov ON p.id_provinsi = prov.id_provinsi
                  LEFT JOIN kota kota ON p.id_kota = kota.id_kota
                  WHERE low.id_perusahaan = ?
                  ORDER BY p.nama_user ASC";
        $stmt = mysqli_prepare($this->koneksi, $query);
        mysqli_stmt_bind_param($stmt, "i", $perusahaan_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        mysqli_stmt_close($stmt);
        return $data;
    }

    public function getDetailPelamar($id_user, $perusahaan_id)
    {
        $query = "SELECT u.*, p.*, prov.nama_provinsi, kota.nama_kota
                  FROM user u
                  LEFT JOIN profil p ON u.id_user = p.id_user
                  LEFT JOIN provinsi prov ON p.id_provinsi = prov.id_provinsi
                  LEFT JOIN kota kota ON p.id_kota = kota.id_kota
                  WHERE u.id_user = ?";
        $stmt = mysqli_prepare($this->koneksi, $query);
        mysqli_stmt_bind_param($stmt, "i", $id_user);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $data;
    }

    // ==================== STATISTIK ====================
    public function getStats($perusahaan_id)
    {
        $stats = [];

        $query = "SELECT COUNT(*) as total FROM lowongan WHERE id_perusahaan = ?";
        $stmt = mysqli_prepare($this->koneksi, $query);
        mysqli_stmt_bind_param($stmt, "i", $perusahaan_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $stats['total_lowongan'] = mysqli_fetch_assoc($result)['total'];
        mysqli_stmt_close($stmt);

        $query = "SELECT COUNT(*) as total FROM lowongan WHERE id_perusahaan = ? AND status = 'Aktif'";
        $stmt = mysqli_prepare($this->koneksi, $query);
        mysqli_stmt_bind_param($stmt, "i", $perusahaan_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $stats['lowongan_aktif'] = mysqli_fetch_assoc($result)['total'];
        mysqli_stmt_close($stmt);

        $query = "SELECT COUNT(*) as total FROM lamaran l
                  JOIN lowongan low ON l.id_lowongan = low.id_lowongan
                  WHERE low.id_perusahaan = ?";
        $stmt = mysqli_prepare($this->koneksi, $query);
        mysqli_stmt_bind_param($stmt, "i", $perusahaan_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $stats['total_lamaran'] = mysqli_fetch_assoc($result)['total'];
        mysqli_stmt_close($stmt);

        $query = "SELECT COUNT(DISTINCT l.id_user) as total FROM lamaran l
                  JOIN lowongan low ON l.id_lowongan = low.id_lowongan
                  WHERE low.id_perusahaan = ?";
        $stmt = mysqli_prepare($this->koneksi, $query);
        mysqli_stmt_bind_param($stmt, "i", $perusahaan_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $stats['total_pelamar'] = mysqli_fetch_assoc($result)['total'];
        mysqli_stmt_close($stmt);

        $query = "SELECT MONTH(tanggal_lamar) as bulan, YEAR(tanggal_lamar) as tahun, COUNT(*) as jumlah
                  FROM lamaran l
                  JOIN lowongan low ON l.id_lowongan = low.id_lowongan
                  WHERE low.id_perusahaan = ? 
                  AND tanggal_lamar >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                  GROUP BY YEAR(tanggal_lamar), MONTH(tanggal_lamar)
                  ORDER BY tahun ASC, bulan ASC";
        $stmt = mysqli_prepare($this->koneksi, $query);
        mysqli_stmt_bind_param($stmt, "i", $perusahaan_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $stats['lamaran_per_bulan'] = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $stats['lamaran_per_bulan'][] = $row;
        }
        mysqli_stmt_close($stmt);

        $query = "SELECT l.*, low.judul_lowongan, u.username_user, p.nama_user
                  FROM lamaran l
                  JOIN lowongan low ON l.id_lowongan = low.id_lowongan
                  JOIN user u ON l.id_user = u.id_user
                  LEFT JOIN profil p ON u.id_user = p.id_user
                  WHERE low.id_perusahaan = ?
                  ORDER BY l.tanggal_lamar DESC
                  LIMIT 5";
        $stmt = mysqli_prepare($this->koneksi, $query);
        mysqli_stmt_bind_param($stmt, "i", $perusahaan_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $stats['lamaran_terbaru'] = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $stats['lamaran_terbaru'][] = $row;
        }
        mysqli_stmt_close($stmt);

        $query = "SELECT * FROM lowongan 
                  WHERE id_perusahaan = ? 
                  ORDER BY tanggal_posting DESC 
                  LIMIT 5";
        $stmt = mysqli_prepare($this->koneksi, $query);
        mysqli_stmt_bind_param($stmt, "i", $perusahaan_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $stats['lowongan_terbaru'] = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $stats['lowongan_terbaru'][] = $row;
        }
        mysqli_stmt_close($stmt);

        return $stats;
    }

    public function getAllKategori()
    {
        $query = "SELECT * FROM kategori ORDER BY nama_kategori ASC";
        $result = mysqli_query($this->koneksi, $query);
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }

    public function getAllJenis()
    {
        $query = "SELECT * FROM jenis ORDER BY nama_jenis ASC";
        $result = mysqli_query($this->koneksi, $query);
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }
}

// HANYA CEK LOGIN jika bukan file yang membutuhkan autentikasi
// TAPI jangan redirect otomatis di file ini karena akan menyebabkan loop
// Pindahkan logika redirect ke file masing-masing

$db = new DatabasePerusahaan();
?>