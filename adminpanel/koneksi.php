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

    function login_admin($username, $password)
    {
        $query = "SELECT * FROM admin WHERE nama_admin = ? AND password_admin = ?";
        $stmt = $this->koneksi->prepare($query);

        if ($stmt === false) {
            return false;
        }

        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $data = $stmt->get_result();

        if ($data->num_rows > 0) {
            $row = $data->fetch_assoc();
            $stmt->close();
            return $row; // Kembalikan data user
        }
        $stmt->close();
        return false;
    }

    function tampil_data_admin()
    {
        $data = mysqli_query($this->koneksi, "SELECT id_admin, email_admin, nama_admin, foto_admin FROM admin ORDER BY id_admin DESC");
        $hasil = [];
        while ($row = mysqli_fetch_assoc($data)) {
            $hasil[] = $row;
        }
        return $hasil;
    }

    function tambah_admin($nama, $email, $password, $foto)
    {
        // Upload Foto sederhana
        $nama_foto = "user-01.png"; // default

        if ($foto && $foto['name'] != "") {
            $nama_foto = time() . "_" . basename($foto['name']);
            $target_dir = "src/images/user/";

            // Pastikan folder tujuan ada
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $target_file = $target_dir . $nama_foto;

            // Cek ukuran file (maks 2MB)
            if ($foto['size'] > 2000000) {
                return false;
            }

            // Cek tipe file
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($foto['type'], $allowed_types)) {
                return false;
            }

            if (!move_uploaded_file($foto['tmp_name'], $target_file)) {
                $nama_foto = "user-01.png";
            }
        }

        // Gunakan prepared statement untuk keamanan
        $stmt = $this->koneksi->prepare("INSERT INTO admin (nama_admin, email_admin, password_admin, foto_admin, created_at) 
                                       VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)");

        $stmt->bind_param("ssss", $nama, $email, $password, $nama_foto);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    // Tambahkan fungsi hapus_admin di class database
    function hapus_admin($id)
    {
        // Gunakan prepared statement untuk keamanan
        $stmt = $this->koneksi->prepare("DELETE FROM admin WHERE id_admin = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    // Fungsi untuk cek apakah admin sedang aktif (memiliki session aktif)
    function is_admin_active($id)
    {
        // Cek di tabel session tracking atau bisa menggunakan cara lain
        // Untuk sekarang, kita gunakan pendekatan sederhana dengan session file
        $session_path = session_save_path();
        if (!$session_path) {
            $session_path = sys_get_temp_dir();
        }

        // Scan session files untuk mencari admin yang aktif
        if (is_dir($session_path)) {
            $files = glob($session_path . '/sess_*');
            foreach ($files as $file) {
                $session_data = file_get_contents($file);
                if ($session_data && strpos($session_data, '"id_admin";s:' . strlen($id) . ':"' . $id . '"') !== false) {
                    return true;
                }
            }
        }

        return false;
    }

    // Fungsi untuk mendapatkan semua admin yang sedang aktif
    function get_active_admins()
    {
        $active_admins = array();
        $session_path = session_save_path();
        if (!$session_path) {
            $session_path = sys_get_temp_dir();
        }

        // Scan session files
        if (is_dir($session_path)) {
            $files = glob($session_path . '/sess_*');
            foreach ($files as $file) {
                $session_data = file_get_contents($file);
                if ($session_data) {
                    // Extract id_admin dari session data
                    if (preg_match('/"id_admin";s:\d+:"(\d+)"/', $session_data, $matches)) {
                        $admin_id = $matches[1];
                        if (!in_array($admin_id, $active_admins)) {
                            $active_admins[] = $admin_id;
                        }
                    }
                }
            }
        }

        return $active_admins;
    }

    // Fungsi untuk menampilkan data perusahaan
    function tampil_data_perusahaan()
    {
        $data = mysqli_query(
            $this->koneksi,
            "SELECT p.id_perusahaan, p.email_perusahaan, p.nama_perusahaan, 
                p.logo_perusahaan, p.deskripsi_perusahaan, 
                p.id_provinsi, p.id_kota, p.alamat_perusahaan, 
                p.nohp_perusahaan, pr.nama_provinsi, k.nama_kota
         FROM perusahaan p
         LEFT JOIN provinsi pr ON p.id_provinsi = pr.id_provinsi
         LEFT JOIN kota k ON p.id_kota = k.id_kota
         ORDER BY p.id_perusahaan DESC"
        );

        $hasil = [];
        while ($row = mysqli_fetch_assoc($data)) {
            $hasil[] = $row;
        }
        return $hasil;
    }

    // Fungsi untuk menambah perusahaan
    function tambah_perusahaan($email, $password, $nama, $logo, $deskripsi, $id_provinsi, $id_kota, $alamat, $nohp)
    {
        // Upload Logo sederhana
        $nama_logo = "company-default.png"; // default

        if ($logo && $logo['name'] != "") {
            $nama_logo = time() . "_" . basename($logo['name']);
            $target_dir = "src/images/company/";

            // Pastikan folder tujuan ada
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $target_file = $target_dir . $nama_logo;

            // Cek ukuran file (maks 2MB)
            if ($logo['size'] > 2000000) {
                return false;
            }

            // Cek tipe file
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($logo['type'], $allowed_types)) {
                return false;
            }

            if (!move_uploaded_file($logo['tmp_name'], $target_file)) {
                $nama_logo = "company-default.png";
            }
        }

        // Hash password jika perlu (gunakan password_hash() di production)
        $hashed_password = $password; // Untuk sekarang, simpan plain text

        $query = "INSERT INTO perusahaan 
              (email_perusahaan, password_perusahaan, nama_perusahaan, logo_perusahaan, 
               deskripsi_perusahaan, id_provinsi, id_kota, 
               alamat_perusahaan, nohp_perusahaan) 
              VALUES ('$email', '$hashed_password', '$nama', '$nama_logo', 
                      '$deskripsi', '$id_provinsi', '$id_kota', '$alamat', '$nohp')";

        return mysqli_query($this->koneksi, $query);
    }

    // Fungsi untuk menghapus perusahaan (VERSI DIPERBAIKI dengan cascade)
    function hapus_perusahaan($id)
    {
        // Mulai transaksi
        $this->koneksi->begin_transaction();

        try {
            // 1. Hapus semua lamaran yang terkait dengan lowongan dari perusahaan ini
            $query1 = "DELETE lamaran 
                   FROM lamaran 
                   INNER JOIN lowongan ON lamaran.id_lowongan = lowongan.id_lowongan 
                   WHERE lowongan.id_perusahaan = ?";
            $stmt1 = $this->koneksi->prepare($query1);
            $stmt1->bind_param("i", $id);
            $stmt1->execute();

            // 2. Hapus semua lowongan dari perusahaan ini
            $query2 = "DELETE FROM lowongan WHERE id_perusahaan = ?";
            $stmt2 = $this->koneksi->prepare($query2);
            $stmt2->bind_param("i", $id);
            $stmt2->execute();

            // 3. Hapus perusahaan
            $query3 = "DELETE FROM perusahaan WHERE id_perusahaan = ?";
            $stmt3 = $this->koneksi->prepare($query3);
            $stmt3->bind_param("i", $id);
            $stmt3->execute();

            // Commit transaksi
            $this->koneksi->commit();
            return true;

        } catch (Exception $e) {
            // Rollback jika ada error
            $this->koneksi->rollback();
            error_log("Error hapus perusahaan: " . $e->getMessage());
            return false;
        }
    }

    public function tampil_data_pelamar()
    {
        $query = "SELECT u.*, p.*, 
                 pr.nama_provinsi, 
                 k.nama_kota
          FROM user u 
          LEFT JOIN profil p ON u.id_user = p.id_user 
          LEFT JOIN provinsi pr ON p.id_provinsi = pr.id_provinsi
          LEFT JOIN kota k ON p.id_kota = k.id_kota
          ORDER BY u.id_user DESC";

        $result = $this->koneksi->query($query);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }
    public function tambah_pelamar($data)
    {
        // Mulai transaksi
        $this->koneksi->begin_transaction();

        try {
            // 1. Insert ke tabel user
            $query1 = "INSERT INTO user (email_user, password_user, username_user) 
                   VALUES (?, ?, ?)";
            $stmt1 = $this->koneksi->prepare($query1);
            $stmt1->bind_param("sss", $data['email'], $data['password'], $data['username']);
            $stmt1->execute();

            $user_id = $this->koneksi->insert_id;

            // 2. Insert ke tabel profil (DENGAN id_provinsi dan id_kota)
            $query2 = "INSERT INTO profil (id_user, nama_user, nohp_user, id_provinsi, 
                  id_kota, tanggallahir_user, jk_user, deskripsi_user, 
                  kelebihan_user, riwayatpekerjaan_user, prestasi_user, foto_user,
                  instagram_user, facebook_user, linkedin_user) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt2 = $this->koneksi->prepare($query2);
            $stmt2->bind_param(
                "issssssssssssss",
                $user_id,
                $data['nama_user'],
                $data['nohp_user'],
                $data['id_provinsi'],     // PERUBAHAN: dari provinsi_user
                $data['id_kota'],         // PERUBAHAN: dari kota_user
                $data['tanggallahir_user'],
                $data['jk_user'],
                $data['deskripsi_user'],
                $data['kelebihan_user'],
                $data['riwayatpekerjaan_user'],
                $data['prestasi_user'],
                $data['foto_user'],
                $data['instagram_user'],
                $data['facebook_user'],
                $data['linkedin_user']
            );
            $stmt2->execute();

            // Commit transaksi
            $this->koneksi->commit();
            return true;

        } catch (Exception $e) {
            // Rollback jika ada error
            $this->koneksi->rollback();
            return false;
        }
    }

    public function hapus_pelamar($id)
    {
        // Mulai transaksi
        $this->koneksi->begin_transaction();

        try {
            // 1. Hapus dari profil terlebih dahulu (karena foreign key)
            $query1 = "DELETE FROM profil WHERE id_user = ?";
            $stmt1 = $this->koneksi->prepare($query1);
            $stmt1->bind_param("i", $id);
            $stmt1->execute();

            // 2. Hapus dari user
            $query2 = "DELETE FROM user WHERE id_user = ?";
            $stmt2 = $this->koneksi->prepare($query2);
            $stmt2->bind_param("i", $id);
            $stmt2->execute();

            // Commit transaksi
            $this->koneksi->commit();
            return true;

        } catch (Exception $e) {
            // Rollback jika ada error
            $this->koneksi->rollback();
            return false;
        }
    }

    public function get_pelamar_by_id($id)
    {
        try {
            $query = "SELECT 
                u.*, 
                p.*,
                pr.nama_provinsi, 
                k.nama_kota
              FROM user u 
              LEFT JOIN profil p ON u.id_user = p.id_user 
              LEFT JOIN provinsi pr ON p.id_provinsi = pr.id_provinsi
              LEFT JOIN kota k ON p.id_kota = k.id_kota
              WHERE u.id_user = ?";
            $stmt = $this->koneksi->prepare($query);

            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->koneksi->error);
            }

            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                return $result->fetch_assoc();
            } else {
                return null;
            }
        } catch (Exception $e) {
            error_log("Error get_pelamar_by_id: " . $e->getMessage());
            return null;
        }
    }

    public function update_pelamar($id_user, $data)
    {
        try {
            // Update tabel user
            $user_fields = [];
            $user_values = [];

            if (isset($data['email_user'])) {
                $user_fields[] = "email_user = ?";
                $user_values[] = $data['email_user'];
            }

            if (isset($data['username_user'])) {
                $user_fields[] = "username_user = ?";
                $user_values[] = $data['username_user'];
            }

            if (isset($data['password_user'])) {
                $user_fields[] = "password_user = ?";
                $user_values[] = $data['password_user'];
            }

            if (!empty($user_fields)) {
                $user_sql = "UPDATE user SET " . implode(", ", $user_fields) . " WHERE id_user = ?";
                $user_values[] = $id_user;

                $stmt = $this->koneksi->prepare($user_sql);
                $types = str_repeat("s", count($user_values) - 1) . "i";
                $stmt->bind_param($types, ...$user_values);
                $stmt->execute();
            }

            // Update tabel profil - TAMBAHKAN SEMUA FIELD
            $profil_fields = [];
            $profil_values = [];

            // Daftar semua field profil
            $profil_fields_map = [
                'nama_user',
                'nohp_user',
                'id_provinsi',
                'id_kota',
                'tanggallahir_user',
                'jk_user',
                'deskripsi_user',
                'kelebihan_user',
                'riwayatpekerjaan_user',
                'prestasi_user',
                'foto_user',
                'judul_porto',
                'gambar_porto',
                'link_porto',
                'instagram_user',
                'facebook_user',
                'linkedin_user'
            ];

            foreach ($profil_fields_map as $field) {
                if (isset($data[$field])) {
                    $profil_fields[] = "$field = ?";
                    $profil_values[] = $data[$field];
                }
            }

            if (!empty($profil_fields)) {
                // Cek apakah profil sudah ada
                $check_sql = "SELECT id_user FROM profil WHERE id_user = ?";
                $check_stmt = $this->koneksi->prepare($check_sql);
                $check_stmt->bind_param("i", $id_user);
                $check_stmt->execute();
                $check_result = $check_stmt->get_result();

                if ($check_result->num_rows > 0) {
                    // Update existing
                    $profil_sql = "UPDATE profil SET " . implode(", ", $profil_fields) . " WHERE id_user = ?";
                    $profil_values[] = $id_user;
                } else {
                    // Insert new
                    $profil_fields[] = "id_user";
                    $profil_values[] = $id_user;
                    $profil_sql = "INSERT INTO profil SET " . implode(", ", $profil_fields);
                }

                $stmt = $this->koneksi->prepare($profil_sql);
                $types = str_repeat("s", count($profil_values));
                $stmt->bind_param($types, ...$profil_values);
                $stmt->execute();
            }

            return true;
        } catch (Exception $e) {
            error_log("Error update_pelamar: " . $e->getMessage());
            return false;
        }
    }

    public function tampil_data_lowongan()
    {
        $query = "SELECT l.*, p.nama_perusahaan, pr.nama_provinsi, k.nama_kota, kat.nama_kategori, j.nama_jenis
              FROM lowongan l 
              LEFT JOIN perusahaan p ON l.id_perusahaan = p.id_perusahaan 
              LEFT JOIN provinsi pr ON l.id_provinsi = pr.id_provinsi
              LEFT JOIN kota k ON l.id_kota = k.id_kota
              LEFT JOIN kategori kat ON l.kategori_lowongan = kat.id_kategori
              LEFT JOIN jenis j ON l.id_jenis = j.id_jenis
              ORDER BY l.id_lowongan DESC";
        $result = $this->koneksi->query($query);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    public function tambah_lowongan($data)
    {
        $query = "INSERT INTO lowongan (
            id_perusahaan, judul_lowongan, kategori_lowongan, 
            id_jenis, id_provinsi, id_kota, 
            lokasi_lowongan, gaji_lowongan, kualifikasi, 
            deskripsi_lowongan, pertanyaan, tanggal_tutup, 
            status, gambar
          ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
          )";

        $stmt = $this->koneksi->prepare($query);

        $stmt->bind_param(
            "isissssdssssss", // <- Update: 'waktukerja' diubah menjadi 'id_jenis' (integer)
            $data['id_perusahaan'],
            $data['judul_lowongan'],
            $data['kategori_lowongan'],
            $data['id_jenis'], // SEKARANG id_jenis (integer) bukan waktukerja (string)
            $data['id_provinsi'],
            $data['id_kota'],
            $data['lokasi_lowongan'],
            $data['gaji_lowongan'],
            $data['kualifikasi'],
            $data['deskripsi_lowongan'],
            $data['pertanyaan'],
            $data['tanggal_tutup'],
            $data['status'],
            $data['gambar']
        );

        return $stmt->execute();
    }
    // Method untuk menghapus lowongan (versi dengan foreign key constraint)
    public function hapus_lowongan($id)
    {
        // Mulai transaksi
        $this->koneksi->begin_transaction();

        try {
            // 1. Hapus semua lamaran yang terkait dengan lowongan ini terlebih dahulu
            $query1 = "DELETE FROM lamaran WHERE id_lowongan = ?";
            $stmt1 = $this->koneksi->prepare($query1);
            $stmt1->bind_param("i", $id);
            $stmt1->execute();

            // 2. Hapus lowongan
            $query2 = "DELETE FROM lowongan WHERE id_lowongan = ?";
            $stmt2 = $this->koneksi->prepare($query2);
            $stmt2->bind_param("i", $id);
            $stmt2->execute();

            // Commit transaksi
            $this->koneksi->commit();
            return true;

        } catch (Exception $e) {
            // Rollback jika ada error
            $this->koneksi->rollback();
            return false;
        }
    }
    // Method untuk menampilkan data lamaran
    public function tampil_data_lamaran()
    {
        $query = "SELECT l.*, 
                     low.judul_lowongan, 
                     per.nama_perusahaan,
                     u.email_user,
                     p.nama_user, p.foto_user
              FROM lamaran l
              LEFT JOIN lowongan low ON l.id_lowongan = low.id_lowongan
              LEFT JOIN perusahaan per ON low.id_perusahaan = per.id_perusahaan
              LEFT JOIN user u ON l.id_user = u.id_user
              LEFT JOIN profil p ON l.id_user = p.id_user
              ORDER BY l.id_lamaran DESC";

        $result = $this->koneksi->query($query);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    // Method untuk menghapus lamaran
    public function hapus_lamaran($id)
    {
        $stmt = $this->koneksi->prepare("DELETE FROM lamaran WHERE id_lamaran = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    // Method untuk update status lamaran
    public function update_status_lamaran($id, $status, $catatan = '')
    {
        $query = "UPDATE lamaran SET status_lamaran = ?, catatan_hrd = ? WHERE id_lamaran = ?";
        $stmt = $this->koneksi->prepare($query);
        $stmt->bind_param("ssi", $status, $catatan, $id);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }
    // Method untuk mengecek apakah lamaran sudah ada
    public function cek_lamaran_exists($id_lowongan, $id_user)
    {
        $query = "SELECT COUNT(*) as total FROM lamaran WHERE id_lowongan = ? AND id_user = ?";
        $stmt = $this->koneksi->prepare($query);
        $stmt->bind_param("ii", $id_lowongan, $id_user);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['total'] > 0;
    }
    // Method untuk menampilkan data kategori dengan jumlah lowongan
    public function tampil_data_kategori()
    {
        $query = "SELECT k.*, COUNT(l.id_lowongan) as jumlah_lowongan
                  FROM kategori k 
                  LEFT JOIN lowongan l ON k.id_kategori = l.kategori_lowongan AND l.status = 'Aktif'
                  GROUP BY k.id_kategori, k.nama_kategori
                  ORDER BY k.id_kategori DESC";
        $result = $this->koneksi->query($query);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    // Method untuk mendapatkan lowongan berdasarkan kategori
    public function get_jobs_by_category($kategori_id)
    {
        $query = "SELECT l.*, p.nama_perusahaan, pr.nama_provinsi, k.nama_kota, kat.nama_kategori
                  FROM lowongan l 
                  LEFT JOIN perusahaan p ON l.id_perusahaan = p.id_perusahaan 
                  LEFT JOIN provinsi pr ON l.id_provinsi = pr.id_provinsi
                  LEFT JOIN kota k ON l.id_kota = k.id_kota
                  LEFT JOIN kategori kat ON l.kategori_lowongan = kat.id_kategori
                  WHERE l.kategori_lowongan = ?
                  ORDER BY l.id_lowongan DESC";
        $stmt = $this->koneksi->prepare($query);
        $stmt->bind_param("i", $kategori_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $stmt->close();
        return $data;
    }

    // Method untuk mendapatkan kategori berdasarkan ID
    public function get_kategori_by_id($id)
    {
        $query = "SELECT * FROM kategori WHERE id_kategori = ?";
        $stmt = $this->koneksi->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Method untuk menghapus kategori
    public function hapus_kategori($id)
    {
        $stmt = $this->koneksi->prepare("DELETE FROM kategori WHERE id_kategori = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    // Method untuk update kategori
    public function update_kategori($id, $nama_kategori)
    {
        $query = "UPDATE kategori SET nama_kategori = ? WHERE id_kategori = ?";
        $stmt = $this->koneksi->prepare($query);
        $stmt->bind_param("si", $nama_kategori, $id);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    // Method untuk menambah kategori
    public function tambah_kategori($nama_kategori)
    {
        $query = "INSERT INTO kategori (nama_kategori) VALUES (?)";
        $stmt = $this->koneksi->prepare($query);
        $stmt->bind_param("s", $nama_kategori);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    // Tambahkan method ini di dalam class database

    // Method untuk menampilkan data jenis
    public function tampil_data_jenis()
    {
        $query = "SELECT * FROM jenis ORDER BY id_jenis DESC";
        $result = $this->koneksi->query($query);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    // Method untuk menambah jenis
    public function tambah_jenis($nama_jenis)
    {
        $query = "INSERT INTO jenis (nama_jenis) VALUES (?)";
        $stmt = $this->koneksi->prepare($query);
        $stmt->bind_param("s", $nama_jenis);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    // Method untuk menghapus jenis
    public function hapus_jenis($id)
    {
        $stmt = $this->koneksi->prepare("DELETE FROM jenis WHERE id_jenis = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    // Method untuk update jenis
    public function update_jenis($id, $nama_jenis)
    {
        $query = "UPDATE jenis SET nama_jenis = ? WHERE id_jenis = ?";
        $stmt = $this->koneksi->prepare($query);
        $stmt->bind_param("si", $nama_jenis, $id);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    // Method untuk menampilkan data provinsi
    public function tampil_data_provinsi()
    {
        $query = "SELECT * FROM provinsi ORDER BY id_provinsi DESC";
        $result = $this->koneksi->query($query);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    // Method untuk menampilkan data kota dengan join provinsi
    public function tampil_data_kota()
    {
        $query = "SELECT k.*, p.nama_provinsi 
              FROM kota k 
              LEFT JOIN provinsi p ON k.id_provinsi = p.id_provinsi 
              ORDER BY k.id_kota DESC";
        $result = $this->koneksi->query($query);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    // Method untuk mendapatkan jumlah kota berdasarkan provinsi
    public function get_jumlah_kota_by_provinsi($id_provinsi)
    {
        $query = "SELECT COUNT(*) as jumlah FROM kota WHERE id_provinsi = ?";
        $stmt = $this->koneksi->prepare($query);
        $stmt->bind_param("i", $id_provinsi);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['jumlah'];
    }

    // Method untuk menambah provinsi
    public function tambah_provinsi($nama_provinsi)
    {
        $query = "INSERT INTO provinsi (nama_provinsi) VALUES (?)";
        $stmt = $this->koneksi->prepare($query);
        $stmt->bind_param("s", $nama_provinsi);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    // Method untuk update provinsi
    public function update_provinsi($id, $nama_provinsi)
    {
        $query = "UPDATE provinsi SET nama_provinsi = ? WHERE id_provinsi = ?";
        $stmt = $this->koneksi->prepare($query);
        $stmt->bind_param("si", $nama_provinsi, $id);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    // Method untuk menghapus provinsi
    public function hapus_provinsi($id)
    {
        // Mulai transaksi
        $this->koneksi->begin_transaction();

        try {
            // Hapus kota yang terkait terlebih dahulu
            $query1 = "DELETE FROM kota WHERE id_provinsi = ?";
            $stmt1 = $this->koneksi->prepare($query1);
            $stmt1->bind_param("i", $id);
            $stmt1->execute();

            // Hapus provinsi
            $query2 = "DELETE FROM provinsi WHERE id_provinsi = ?";
            $stmt2 = $this->koneksi->prepare($query2);
            $stmt2->bind_param("i", $id);
            $stmt2->execute();

            // Commit transaksi
            $this->koneksi->commit();
            return true;

        } catch (Exception $e) {
            // Rollback jika ada error
            $this->koneksi->rollback();
            return false;
        }
    }

    // Method untuk menambah kota
    public function tambah_kota($id_provinsi, $nama_kota)
    {
        $query = "INSERT INTO kota (id_provinsi, nama_kota) VALUES (?, ?)";
        $stmt = $this->koneksi->prepare($query);
        $stmt->bind_param("is", $id_provinsi, $nama_kota);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    // Method untuk update kota
    public function update_kota($id, $id_provinsi, $nama_kota)
    {
        $query = "UPDATE kota SET id_provinsi = ?, nama_kota = ? WHERE id_kota = ?";
        $stmt = $this->koneksi->prepare($query);
        $stmt->bind_param("isi", $id_provinsi, $nama_kota, $id);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    // Method untuk menghapus kota
    public function hapus_kota($id)
    {
        $query = "DELETE FROM kota WHERE id_kota = ?";
        $stmt = $this->koneksi->prepare($query);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    // Method untuk mendapatkan kota berdasarkan provinsi
    public function get_kota_by_provinsi($id_provinsi)
    {
        $query = "SELECT k.*, p.nama_provinsi 
              FROM kota k 
              LEFT JOIN provinsi p ON k.id_provinsi = p.id_provinsi 
              WHERE k.id_provinsi = ? 
              ORDER BY k.nama_kota ASC";
        $stmt = $this->koneksi->prepare($query);
        $stmt->bind_param("i", $id_provinsi);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }
    public function update_lowongan($id, $data)
    {
        $query = "UPDATE lowongan SET 
    id_perusahaan = ?, 
    judul_lowongan = ?, 
    kategori_lowongan = ?, 
    id_jenis = ?, 
    id_provinsi = ?, 
    id_kota = ?, 
    lokasi_lowongan = ?, 
    gaji_lowongan = ?, 
    kualifikasi = ?, 
    deskripsi_lowongan = ?, 
    pertanyaan = ?, 
    tanggal_tutup = ?, 
    status = ?, 
    gambar = COALESCE(?, gambar) 
    WHERE id_lowongan = ?";

        $stmt = $this->koneksi->prepare($query);

        $stmt->bind_param(
            "isissssdssssssi", // <- Update: 'waktukerja' diubah menjadi 'id_jenis' (integer)
            $data['id_perusahaan'],
            $data['judul_lowongan'],
            $data['kategori_lowongan'],
            $data['id_jenis'], // SEKARANG id_jenis (integer) bukan waktukerja (string)
            $data['id_provinsi'],
            $data['id_kota'],
            $data['lokasi_lowongan'],
            $data['gaji_lowongan'],
            $data['kualifikasi'],
            $data['deskripsi_lowongan'],
            $data['pertanyaan'],
            $data['tanggal_tutup'],
            $data['status'],
            $data['gambar'],
            $id
        );

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }
    // Method untuk mendapatkan lowongan berdasarkan ID
    public function get_lowongan_by_id($id)
    {
        $query = "SELECT l.*, pr.nama_provinsi, k.nama_kota
                  FROM lowongan l
                  LEFT JOIN provinsi pr ON l.id_provinsi = pr.id_provinsi
                  LEFT JOIN kota k ON l.id_kota = k.id_kota
                  WHERE l.id_lowongan = ?";
        $stmt = $this->koneksi->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Method untuk update perusahaan
// Method untuk update perusahaan (VERSI DIPERBAIKI)
    public function update_perusahaan($id, $data)
    {
        // Mulai transaksi
        $this->koneksi->begin_transaction();

        try {
            // Siapkan query update
            $query = "UPDATE perusahaan SET 
            nama_perusahaan = ?, 
            email_perusahaan = ?";

            // Tambahkan password jika diisi
            if (!empty($data['password_perusahaan'])) {
                $query .= ", password_perusahaan = ?";
            }

            // Lanjutkan query dengan id_provinsi dan id_kota
            $query .= ", id_provinsi = ?, 
            id_kota = ?, 
            alamat_perusahaan = ?, 
            nohp_perusahaan = ?, 
            deskripsi_perusahaan = ?";

            // Tambahkan logo jika ada
            if (!empty($data['logo_perusahaan'])) {
                $query .= ", logo_perusahaan = ?";
            }

            $query .= " WHERE id_perusahaan = ?";

            $stmt = $this->koneksi->prepare($query);

            // Hitung jumlah parameter
            $paramCount = 7; // nama, email, id_provinsi, id_kota, alamat, nohp, deskripsi, id

            // Bind parameters berdasarkan kondisi
            if (!empty($data['password_perusahaan']) && !empty($data['logo_perusahaan'])) {
                // Semua field diisi termasuk password dan logo
                $stmt->bind_param(
                    "sssssssssi",
                    $data['nama_perusahaan'],
                    $data['email_perusahaan'],
                    $data['password_perusahaan'],
                    $data['id_provinsi'],
                    $data['id_kota'],
                    $data['alamat_perusahaan'],
                    $data['nohp_perusahaan'],
                    $data['deskripsi_perusahaan'],
                    $data['logo_perusahaan'],
                    $id
                );
            } elseif (!empty($data['password_perusahaan']) && empty($data['logo_perusahaan'])) {
                // Hanya password diisi, logo tidak
                $stmt->bind_param(
                    "ssssssssi",
                    $data['nama_perusahaan'],
                    $data['email_perusahaan'],
                    $data['password_perusahaan'],
                    $data['id_provinsi'],
                    $data['id_kota'],
                    $data['alamat_perusahaan'],
                    $data['nohp_perusahaan'],
                    $data['deskripsi_perusahaan'],
                    $id
                );
            } elseif (empty($data['password_perusahaan']) && !empty($data['logo_perusahaan'])) {
                // Hanya logo diisi, password tidak
                $stmt->bind_param(
                    "ssssssssi",
                    $data['nama_perusahaan'],
                    $data['email_perusahaan'],
                    $data['id_provinsi'],
                    $data['id_kota'],
                    $data['alamat_perusahaan'],
                    $data['nohp_perusahaan'],
                    $data['deskripsi_perusahaan'],
                    $data['logo_perusahaan'],
                    $id
                );
            } else {
                // Tidak ada password dan tidak ada logo
                $stmt->bind_param(
                    "sssssssi",
                    $data['nama_perusahaan'],
                    $data['email_perusahaan'],
                    $data['id_provinsi'],
                    $data['id_kota'],
                    $data['alamat_perusahaan'],
                    $data['nohp_perusahaan'],
                    $data['deskripsi_perusahaan'],
                    $id
                );
            }

            if ($stmt->execute()) {
                $this->koneksi->commit();
                $stmt->close();
                return true;
            } else {
                $this->koneksi->rollback();
                $stmt->close();
                return false;
            }

        } catch (Exception $e) {
            $this->koneksi->rollback();
            error_log("Error update perusahaan: " . $e->getMessage());
            return false;
        }
    }

    // Method untuk mendapatkan perusahaan berdasarkan ID
    public function get_perusahaan_by_id($id)
    {
        $query = "SELECT p.*, pr.nama_provinsi, k.nama_kota
                  FROM perusahaan p
                  LEFT JOIN provinsi pr ON p.id_provinsi = pr.id_provinsi
                  LEFT JOIN kota k ON p.id_kota = k.id_kota
                  WHERE p.id_perusahaan = ?";
        $stmt = $this->koneksi->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Tambahkan method ini di class database (di koneksi.php)

    // Method untuk mendapatkan admin berdasarkan ID
    function get_admin_by_id($id)
    {
        $stmt = $this->koneksi->prepare("SELECT * FROM admin WHERE id_admin = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Method untuk update admin
    function update_admin($id, $nama, $email, $password = null, $foto = null)
    {
        // Mulai transaksi
        $this->koneksi->begin_transaction();

        try {
            // Cek apakah ada password baru
            if ($password !== null && $password !== '') {
                $query = "UPDATE admin SET nama_admin = ?, email_admin = ?, password_admin = ?";
                $params = [$nama, $email, $password];

                // Jika ada foto baru
                if ($foto !== null && $foto !== '') {
                    $query .= ", foto_admin = ?";
                    $params[] = $foto;
                }

                $query .= " WHERE id_admin = ?";
                $params[] = $id;
            } else {
                $query = "UPDATE admin SET nama_admin = ?, email_admin = ?";
                $params = [$nama, $email];

                // Jika ada foto baru
                if ($foto !== null && $foto !== '') {
                    $query .= ", foto_admin = ?";
                    $params[] = $foto;
                }

                $query .= " WHERE id_admin = ?";
                $params[] = $id;
            }

            // Prepare statement
            $stmt = $this->koneksi->prepare($query);

            // Bind parameters berdasarkan jumlah parameter
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);

            if ($stmt->execute()) {
                $this->koneksi->commit();
                $stmt->close();
                return true;
            } else {
                $this->koneksi->rollback();
                $stmt->close();
                return false;
            }

        } catch (Exception $e) {
            $this->koneksi->rollback();
            error_log("Error update admin: " . $e->getMessage());
            return false;
        }
    }

    // Method untuk mendapatkan data admin untuk session update
    function get_admin_session_data($id)
    {
        $stmt = $this->koneksi->prepare("SELECT id_admin, nama_admin, email_admin, foto_admin FROM admin WHERE id_admin = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

}
?>