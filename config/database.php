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

    // Method untuk menampilkan kategori yang memiliki lowongan aktif (untuk filter)
    public function tampil_kategori_filter()
    {
        $query = "SELECT DISTINCT k.id_kategori, k.nama_kategori, COUNT(l.id_lowongan) as jumlah_lowongan
                  FROM kategori k 
                  INNER JOIN lowongan l ON k.id_kategori = l.kategori_lowongan 
                  WHERE l.status = 'Aktif'
                  GROUP BY k.id_kategori, k.nama_kategori
                  ORDER BY jumlah_lowongan DESC, k.nama_kategori ASC";
        $result = $this->koneksi->query($query);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    // Method untuk filter lowongan berdasarkan kategori
    public function filter_lowongan_kategori($kategori_id = null)
    {
        if ($kategori_id && $kategori_id !== 'all') {
            $query = "SELECT l.*, p.nama_perusahaan, pr.nama_provinsi, k.nama_kota, kat.nama_kategori, j.nama_jenis
                      FROM lowongan l 
                      LEFT JOIN perusahaan p ON l.id_perusahaan = p.id_perusahaan 
                      LEFT JOIN provinsi pr ON l.id_provinsi = pr.id_provinsi
                      LEFT JOIN kota k ON l.id_kota = k.id_kota
                      LEFT JOIN kategori kat ON l.kategori_lowongan = kat.id_kategori
                      LEFT JOIN jenis j ON l.id_jenis = j.id_jenis
                      WHERE l.status = 'Aktif' AND l.kategori_lowongan = ?
                      ORDER BY l.id_lowongan DESC";
            $stmt = $this->koneksi->prepare($query);
            $stmt->bind_param("i", $kategori_id);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $query = "SELECT l.*, p.nama_perusahaan, pr.nama_provinsi, k.nama_kota, kat.nama_kategori, j.nama_jenis
                      FROM lowongan l 
                      LEFT JOIN perusahaan p ON l.id_perusahaan = p.id_perusahaan 
                      LEFT JOIN provinsi pr ON l.id_provinsi = pr.id_provinsi
                      LEFT JOIN kota k ON l.id_kota = k.id_kota
                      LEFT JOIN kategori kat ON l.kategori_lowongan = kat.id_kategori
                      LEFT JOIN jenis j ON l.id_jenis = j.id_jenis
                      WHERE l.status = 'Aktif'
                      ORDER BY l.id_lowongan DESC";
            $result = $this->koneksi->query($query);
        }

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        if (isset($stmt)) {
            $stmt->close();
        }

        return $data;
    }

    // Method untuk menampilkan data perusahaan dengan jumlah lowongan
    public function tampil_data_perusahaan()
    {
        $query = "SELECT p.*, COUNT(l.id_lowongan) as jumlah_lowongan,
                  CASE WHEN p.logo_perusahaan IS NOT NULL AND p.logo_perusahaan != '' 
                       THEN p.logo_perusahaan 
                       ELSE CONCAT('img', ((p.id_perusahaan - 1) % 3) + 1, '.png') 
                  END as logo_display
                  FROM perusahaan p 
                  LEFT JOIN lowongan l ON p.id_perusahaan = l.id_perusahaan AND l.status = 'Aktif'
                  GROUP BY p.id_perusahaan
                  ORDER BY jumlah_lowongan DESC, p.nama_perusahaan ASC
                  LIMIT 10";
        $result = $this->koneksi->query($query);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    // Method untuk menampilkan semua perusahaan dengan jumlah lowongan
    public function tampil_semua_perusahaan()
    {
        $query = "SELECT p.*, COUNT(l.id_lowongan) as jumlah_lowongan,
                  CASE WHEN p.logo_perusahaan IS NOT NULL AND p.logo_perusahaan != '' 
                       THEN p.logo_perusahaan 
                       ELSE CONCAT('img', ((p.id_perusahaan - 1) % 3) + 1, '.png') 
                  END as logo_display
                  FROM perusahaan p 
                  LEFT JOIN lowongan l ON p.id_perusahaan = l.id_perusahaan AND l.status = 'Aktif'
                  GROUP BY p.id_perusahaan
                  ORDER BY p.id_perusahaan DESC";
        $result = $this->koneksi->query($query);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    // Method untuk mendapatkan data statistik untuk counter
    public function get_statistics()
    {
        $stats = [];

        // Total Jobs Posted (lowongan aktif)
        $query = "SELECT COUNT(*) as total FROM lowongan WHERE status = 'Aktif'";
        $result = $this->koneksi->query($query);
        $row = $result->fetch_assoc();
        $stats['total_jobs'] = $row['total'];

        // Total Companies (perusahaan)
        $query = "SELECT COUNT(*) as total FROM perusahaan";
        $result = $this->koneksi->query($query);
        $row = $result->fetch_assoc();
        $stats['total_companies'] = $row['total'];

        // Total Users (pelamar)
        $query = "SELECT COUNT(*) as total FROM user";
        $result = $this->koneksi->query($query);
        $row = $result->fetch_assoc();
        $stats['total_users'] = $row['total'];

        // Total Applications (lamaran)
        $query = "SELECT COUNT(*) as total FROM lamaran";
        $result = $this->koneksi->query($query);
        $row = $result->fetch_assoc();
        $stats['total_applications'] = $row['total'];

        return $stats;
    }

    // Method untuk mendapatkan statistik lamaran user
    public function get_user_application_stats($user_id)
    {
        $stats = [];

        // Total lamaran
        $query = "SELECT COUNT(*) as total FROM lamaran WHERE id_user = ?";
        $stmt = $this->koneksi->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stats['total'] = $row['total'];

        // Lamaran diproses
        $query = "SELECT COUNT(*) as total FROM lamaran WHERE id_user = ? AND status_lamaran = 'Diproses'";
        $stmt = $this->koneksi->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stats['diproses'] = $row['total'];

        // Lamaran diterima
        $query = "SELECT COUNT(*) as total FROM lamaran WHERE id_user = ? AND status_lamaran = 'Diterima'";
        $stmt = $this->koneksi->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stats['diterima'] = $row['total'];

        // Lamaran ditolak
        $query = "SELECT COUNT(*) as total FROM lamaran WHERE id_user = ? AND status_lamaran = 'Ditolak'";
        $stmt = $this->koneksi->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stats['ditolak'] = $row['total'];

        $stmt->close();
        return $stats;
    }

    // Method untuk mendapatkan lamaran user
    public function get_user_applications($user_id, $status_filter = 'all')
    {
        $query = "SELECT l.*, lo.judul_lowongan, lo.gaji_lowongan, lo.tanggal_posting, lo.gambar,
                          p.nama_perusahaan, p.logo_perusahaan, k.nama_kota, kat.nama_kategori
                  FROM lamaran l
                  LEFT JOIN lowongan lo ON l.id_lowongan = lo.id_lowongan
                  LEFT JOIN perusahaan p ON lo.id_perusahaan = p.id_perusahaan
                  LEFT JOIN kota k ON lo.id_kota = k.id_kota
                  LEFT JOIN kategori kat ON lo.kategori_lowongan = kat.id_kategori
                  WHERE l.id_user = ?";

        $params = ["i", $user_id];

        if ($status_filter !== 'all') {
            $query .= " AND l.status_lamaran = ?";
            $params[0] .= "s";
            $params[] = $status_filter;
        }

        $query .= " ORDER BY l.tanggal_lamar DESC";

        $stmt = $this->koneksi->prepare($query);
        $stmt->bind_param(...$params);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        $stmt->close();
        return $data;
    }

    // Method untuk mendapatkan saved jobs user (dari localStorage simulation)
    public function get_saved_jobs($user_id)
    {
        // Untuk sekarang, kita bisa gunakan tabel temporary atau session
        // Simulasi dengan array kosong dulu
        return [];
    }

    // Method untuk mendapatkan detail perusahaan berdasarkan ID
    public function get_company_details($company_id)
    {
        $query = "SELECT p.*, pr.nama_provinsi, k.nama_kota 
              FROM perusahaan p 
              LEFT JOIN provinsi pr ON p.id_provinsi = pr.id_provinsi
              LEFT JOIN kota k ON p.id_kota = k.id_kota
              WHERE p.id_perusahaan = ?";
        $stmt = $this->koneksi->prepare($query);
        $stmt->bind_param("i", $company_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $company = $result->fetch_assoc();
        $stmt->close();
        return $company;
    }

    // Method untuk mendapatkan lowongan berdasarkan perusahaan
    public function get_jobs_by_company($company_id)
    {
        $query = "SELECT l.*, k.nama_kota, kat.nama_kategori, j.nama_jenis
              FROM lowongan l 
              LEFT JOIN kota k ON l.id_kota = k.id_kota
              LEFT JOIN kategori kat ON l.kategori_lowongan = kat.id_kategori
              LEFT JOIN jenis j ON l.id_jenis = j.id_jenis
              WHERE l.id_perusahaan = ? AND l.status = 'Aktif'
              ORDER BY l.tanggal_posting DESC";
        $stmt = $this->koneksi->prepare($query);
        $stmt->bind_param("i", $company_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $jobs = [];
        while ($row = $result->fetch_assoc()) {
            $jobs[] = $row;
        }
        $stmt->close();
        return $jobs;
    }

    // Method untuk mendapatkan total lamaran perusahaan
    public function get_company_applications_count($company_id)
    {
        $query = "SELECT COUNT(*) as total 
              FROM lamaran l 
              INNER JOIN lowongan lo ON l.id_lowongan = lo.id_lowongan 
              WHERE lo.id_perusahaan = ?";
        $stmt = $this->koneksi->prepare($query);
        $stmt->bind_param("i", $company_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_assoc();
        $stmt->close();
        return $count['total'];
    }

    // Di dalam class database, tambahkan method berikut:

    // Method untuk mendapatkan profil user berdasarkan id_user
    public function get_user_profile($user_id)
    {
        $query = "SELECT u.*, p.*, pr.nama_provinsi, k.nama_kota 
              FROM user u 
              LEFT JOIN profil p ON u.id_user = p.id_user 
              LEFT JOIN provinsi pr ON p.id_provinsi = pr.id_provinsi
              LEFT JOIN kota k ON p.id_kota = k.id_kota
              WHERE u.id_user = ?";
        $stmt = $this->koneksi->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $profile = $result->fetch_assoc();
        $stmt->close();
        return $profile;
    }

    // Method untuk update profil user
    public function update_user_profile($user_id, $data)
    {
        // Cek apakah profil sudah ada
        $check_query = "SELECT id_profil FROM profil WHERE id_user = ?";
        $check_stmt = $this->koneksi->prepare($check_query);
        $check_stmt->bind_param("i", $user_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        $exists = $check_result->num_rows > 0;
        $check_stmt->close();

        if ($exists) {
            // Update existing profile
            $query = "UPDATE profil SET 
                  nama_user = ?,
                  nohp_user = ?,
                  tanggallahir_user = ?,
                  jk_user = ?,
                  deskripsi_user = ?,
                  kelebihan_user = ?,
                  riwayatpekerjaan_user = ?,
                  prestasi_user = ?,
                  instagram_user = ?,
                  facebook_user = ?,
                  linkedin_user = ?,
                  id_provinsi = ?,
                  id_kota = ?
                  WHERE id_user = ?";
            $stmt = $this->koneksi->prepare($query);
            $stmt->bind_param(
                "sssssssssssiii",
                $data['nama_user'],
                $data['nohp_user'],
                $data['tanggallahir_user'],
                $data['jk_user'],
                $data['deskripsi_user'],
                $data['kelebihan_user'],
                $data['riwayatpekerjaan_user'],
                $data['prestasi_user'],
                $data['instagram_user'],
                $data['facebook_user'],
                $data['linkedin_user'],
                $data['id_provinsi'],
                $data['id_kota'],
                $user_id
            );
        } else {
            // Insert new profile
            $query = "INSERT INTO profil (
                  id_user, nama_user, nohp_user, tanggallahir_user, jk_user,
                  deskripsi_user, kelebihan_user, riwayatpekerjaan_user, prestasi_user,
                  instagram_user, facebook_user, linkedin_user, id_provinsi, id_kota
                  ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->koneksi->prepare($query);
            $stmt->bind_param(
                "isssssssssssii",
                $user_id,
                $data['nama_user'],
                $data['nohp_user'],
                $data['tanggallahir_user'],
                $data['jk_user'],
                $data['deskripsi_user'],
                $data['kelebihan_user'],
                $data['riwayatpekerjaan_user'],
                $data['prestasi_user'],
                $data['instagram_user'],
                $data['facebook_user'],
                $data['linkedin_user'],
                $data['id_provinsi'],
                $data['id_kota']
            );
        }

        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    // Method untuk update username di tabel user
    public function update_username($user_id, $username)
    {
        $query = "UPDATE user SET username_user = ? WHERE id_user = ?";
        $stmt = $this->koneksi->prepare($query);
        $stmt->bind_param("si", $username, $user_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    // Method untuk update password
    public function update_password($user_id, $new_password)
    {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $query = "UPDATE user SET password_user = ? WHERE id_user = ?";
        $stmt = $this->koneksi->prepare($query);
        $stmt->bind_param("si", $hashed_password, $user_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    // Method untuk update foto profil
    public function update_profile_photo($user_id, $filename)
    {
        $query = "UPDATE profil SET foto_user = ? WHERE id_user = ?";
        $stmt = $this->koneksi->prepare($query);
        $stmt->bind_param("si", $filename, $user_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    // Method untuk update portfolio
    public function update_portfolio($user_id, $judul_porto, $gambar_porto, $link_porto)
    {
        $query = "UPDATE profil SET judul_porto = ?, gambar_porto = ?, link_porto = ? WHERE id_user = ?";
        $stmt = $this->koneksi->prepare($query);
        $stmt->bind_param("sssi", $judul_porto, $gambar_porto, $link_porto, $user_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    // Method untuk mendapatkan semua provinsi
    public function get_all_provinces()
    {
        $query = "SELECT id_provinsi, nama_provinsi FROM provinsi ORDER BY nama_provinsi ASC";
        $result = $this->koneksi->query($query);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    // Method untuk mendapatkan kota berdasarkan provinsi
    public function get_cities_by_province($province_id)
    {
        $query = "SELECT id_kota, nama_kota FROM kota WHERE id_provinsi = ? ORDER BY nama_kota ASC";
        $stmt = $this->koneksi->prepare($query);
        $stmt->bind_param("i", $province_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $stmt->close();
        return $data;
    }
}
?>