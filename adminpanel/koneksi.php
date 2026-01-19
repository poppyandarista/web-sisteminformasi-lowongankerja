<?php
class database
{
    var $host = "localhost";
    var $username = "root";
    var $password = "";
    var $database = "lowongankerja";
    var $koneksi;

    function __construct()
    {
        $this->koneksi = mysqli_connect($this->host, $this->username, $this->password, $this->database);
        if (mysqli_connect_errno()) {
            echo "Koneksi database gagal : " . mysqli_connect_error();
        }
    }

    // --- TAMBAHKAN FUNGSI INI ---
    function tampil_data_admin()
    {
        // Mengambil hanya kolom yang dibutuhkan saja
        $data = mysqli_query($this->koneksi, "SELECT id_admin, email_admin, nama_admin FROM admin");
        while ($row = mysqli_fetch_assoc($data)) {
            $hasil[] = $row;
        }
        return $hasil ?? []; // Mengembalikan array kosong jika tabel admin kosong
    }

    function tampil_data_perusahaan()
    {
        $data = mysqli_query($this->koneksi, "SELECT id_perusahaan, email_perusahaan, nama_perusahaan, logo_perusahaan, deskripsi_perusahaan, provinsi_perusahaan, kota_perusahaan, alamat_perusahaan, nohp_perusahaan FROM perusahaan");
        while ($row = mysqli_fetch_assoc($data)) {
            $hasil[] = $row;
        }
        return $hasil ?? []; // Mengembalikan array kosong jika tabel perusahaan kosong
    }

    function tampil_data_pelamar()
    {
        $data = mysqli_query($this->koneksi, "SELECT id_user, email_user, username_user FROM user");
        while ($row = mysqli_fetch_assoc($data)) {
            $hasil[] = $row;
        }
        return $hasil ?? []; // Mengembalikan array kosong jika tabel pelamar kosong
    }

    function tampil_data_lowongan()
    {
        // Mengambil semua kolom dari lowongan dan nama/logo dari perusahaan
        $query = "SELECT lowongan.*, perusahaan.nama_perusahaan, perusahaan.logo_perusahaan 
              FROM lowongan 
              INNER JOIN perusahaan ON lowongan.id_perusahaan = perusahaan.id_perusahaan 
              ORDER BY lowongan.id_lowongan DESC";

        $data = mysqli_query($this->koneksi, $query);
        $hasil = [];
        while ($row = mysqli_fetch_assoc($data)) {
            $hasil[] = $row;
        }
        return $hasil;
    }

    function tampil_data_lamaran()
    {
        // Query ini menghubungkan lamaran dengan lowongan dan user
        // Sesuaikan nama_pelamar & email_pelamar dengan isi 'desc user' Anda
        $query = "SELECT lamaran.*, 
                     lowongan.judul_lowongan, 
                     user.username_user, user.email_user
              FROM lamaran
              INNER JOIN lowongan ON lamaran.id_lowongan = lowongan.id_lowongan
              INNER JOIN user ON lamaran.id_user = user.id_user
              ORDER BY lamaran.id_lamaran DESC";

        $data = mysqli_query($this->koneksi, $query);

        // Debugging jika query gagal
        if (!$data) {
            die("Kesalahan Query: " . mysqli_error($this->koneksi));
        }

        $hasil = [];
        while ($row = mysqli_fetch_assoc($data)) {
            $hasil[] = $row;
        }
        return $hasil ?? [];
    }

    function login_admin($username, $password)
    {
        $query = "SELECT * FROM admin WHERE nama_admin = '$username' AND password_admin = '$password'";
        $data = mysqli_query($this->koneksi, $query);

        if ($data && mysqli_num_rows($data) > 0) {
            return mysqli_fetch_assoc($data);
        }
        return false;
    }

    function jumlah_perusahaan()
    {
        $data = mysqli_query($this->koneksi, "SELECT COUNT(*) as total FROM perusahaan");
        $row = mysqli_fetch_assoc($data);
        return $row['total'];
    }

    function jumlah_pelamar()
    {
        $data = mysqli_query($this->koneksi, "SELECT COUNT(*) as total FROM user");
        $row = mysqli_fetch_assoc($data);
        return $row['total'];
    }

    function jumlah_lamaran()
    {
        $data = mysqli_query($this->koneksi, "SELECT COUNT(*) as total FROM lamaran");
        $row = mysqli_fetch_assoc($data);
        return $row['total'];
    }

    function jumlah_lowongan()
    {
        $data = mysqli_query($this->koneksi, "SELECT COUNT(*) as total FROM lowongan");
        $row = mysqli_fetch_assoc($data);
        return $row['total'];
    }

    // Tambahkan method ini di class database
    function tambah_data_admin($email, $password, $nama)
    {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO admin (email_admin, password_admin, nama_admin) 
              VALUES ('$email', '$hashed_password', '$nama')";

        return mysqli_query($this->koneksi, $query);
    }

    // Fungsi untuk menambah data perusahaan
    function tambah_data_perusahaan($email, $password, $nama, $logo, $deskripsi, $provinsi, $kota, $alamat, $nohp)
    {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Handle file upload untuk logo
        $logo_name = '';
        if ($logo && $logo['error'] == 0) {
            $logo_name = time() . '_' . basename($logo['name']);
            $target_dir = "build/assets/img/logos/";

            // Buat direktori jika belum ada
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $target_file = $target_dir . $logo_name;
            move_uploaded_file($logo['tmp_name'], $target_file);
        }

        $query = "INSERT INTO perusahaan (
        email_perusahaan, 
        password_perusahaan, 
        nama_perusahaan, 
        logo_perusahaan, 
        deskripsi_perusahaan, 
        provinsi_perusahaan, 
        kota_perusahaan, 
        alamat_perusahaan, 
        nohp_perusahaan
    ) VALUES (
        '$email',
        '$hashed_password',
        '$nama',
        '$logo_name',
        '$deskripsi',
        '$provinsi',
        '$kota',
        '$alamat',
        '$nohp'
    )";

        return mysqli_query($this->koneksi, $query);
    }
}
?>