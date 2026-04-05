<?php
// perusahaan/register.php - Halaman Registrasi Perusahaan
session_start();
require_once 'koneksi_perusahaan.php';

$db = new DatabasePerusahaan();
$error = '';
$success = '';

$provinsi_list = $db->getAllProvinsi();

// Di bagian atas register.php, setelah require_once
// Ganti validasi password yang lama dengan ini:

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_perusahaan = trim($_POST['nama_perusahaan'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $konfirmasi_password = $_POST['konfirmasi_password'] ?? '';
    $id_provinsi = !empty($_POST['id_provinsi']) ? $_POST['id_provinsi'] : null;
    $id_kota = !empty($_POST['id_kota']) ? $_POST['id_kota'] : null;
    $alamat = trim($_POST['alamat_perusahaan'] ?? '');
    $nohp = trim($_POST['nohp_perusahaan'] ?? '');

    if (empty($nama_perusahaan) || empty($email) || empty($password)) {
        $error = 'Nama perusahaan, email, dan password wajib diisi';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid';
    } elseif ($password !== $konfirmasi_password) {
        $error = 'Konfirmasi password tidak cocok';
    } elseif ($db->cekEmailExists($email)) {
        $error = 'Email sudah terdaftar. Gunakan email lain.';
    } else {
        // VALIDASI KEKUATAN PASSWORD
        $passwordErrors = $db->validatePasswordStrength($password);
        if (!empty($passwordErrors)) {
            $error = implode("<br>", $passwordErrors);
        } else {
            // Password valid, lanjut registrasi TANPA HASH
            $data = [
                'nama_perusahaan' => $nama_perusahaan,
                'email' => $email,
                'password' => $password, // Kirim password plain
                'id_provinsi' => $id_provinsi,
                'id_kota' => $id_kota,
                'alamat' => $alamat,
                'nohp' => $nohp
            ];

            $company_id = $db->registerPerusahaan($data);

            if ($company_id) {
                $_SESSION['company_id'] = $company_id;
                $_SESSION['company_name'] = $nama_perusahaan;
                $_SESSION['company_email'] = $email;
                $_SESSION['company_logged_in'] = true;

                $success = 'Pendaftaran berhasil! Mengalihkan ke dashboard...';
                echo '<meta http-equiv="refresh" content="2;url=index.php">';
            } else {
                $error = 'Gagal mendaftar. Silakan coba lagi.';
            }
        }
    }
}

if (isset($_GET['ajax']) && $_GET['ajax'] == 'get_kota' && isset($_GET['id_provinsi'])) {
    $provinsi_id = intval($_GET['id_provinsi']);
    $kota_list = $db->getKotaByProvinsi($provinsi_id);
    header('Content-Type: application/json');
    echo json_encode($kota_list);
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Perusahaan | LinkUp</title>
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f0f4ff 0%, #e8eef8 100%);
            min-height: 100vh;
            padding: 30px 20px;
        }

        .register-container {
            max-width: 520px;
            width: 100%;
            margin: 0 auto;
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }

        .register-header {
            background: linear-gradient(135deg, #0f3b7a 0%, #1e4a8a 100%);
            padding: 24px 24px;
            text-align: center;
            color: white;
        }

        .logo {
            margin-bottom: 8px;
        }

        .logo img {
            height: 40px;
            width: auto;
        }

        .register-header h2 {
            font-size: 1.3rem;
            font-weight: 600;
            margin-top: 8px;
        }

        .register-header p {
            font-size: 0.75rem;
            opacity: 0.85;
            margin-top: 4px;
        }

        .register-body {
            padding: 24px 24px 28px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-weight: 500;
            margin-bottom: 6px;
            color: #1e293b;
            font-size: 0.75rem;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i.input-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 0.85rem;
            pointer-events: none;
            z-index: 1;
        }

        .input-wrapper input,
        .input-wrapper select,
        .input-wrapper textarea {
            width: 100%;
            padding: 9px 12px 9px 38px;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.8rem;
            transition: all 0.2s ease;
            background: #fafcff;
        }

        .input-wrapper textarea {
            padding: 9px 12px;
            resize: vertical;
            min-height: 70px;
        }

        .input-wrapper textarea+.input-icon {
            top: 14px;
            transform: none;
        }

        .input-wrapper select {
            appearance: none;
            cursor: pointer;
            padding-right: 32px;
        }

        .input-wrapper .select-arrow {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            pointer-events: none;
            font-size: 0.7rem;
        }

        .input-wrapper input:focus,
        .input-wrapper select:focus,
        .input-wrapper textarea:focus {
            outline: none;
            border-color: #0f3b7a;
            box-shadow: 0 0 0 3px rgba(15, 59, 122, 0.1);
            background: white;
        }

        .input-wrapper .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #94a3b8;
            background: none;
            border: none;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            width: 22px;
            height: 22px;
        }

        .input-wrapper .toggle-password:hover {
            color: #0f3b7a;
        }

        .btn-register {
            width: 100%;
            padding: 10px;
            background: linear-gradient(135deg, #0f3b7a 0%, #1e4a8a 100%);
            color: white;
            border: none;
            border-radius: 30px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px -6px rgba(15, 59, 122, 0.3);
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
            padding-top: 18px;
            border-top: 1px solid #e2e8f0;
        }

        .login-link p {
            color: #475569;
            font-size: 0.8rem;
        }

        .login-link a {
            color: #0f3b7a;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.8rem;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 10px 14px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
        }

        .alert-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #16a34a;
        }

        .back-home {
            text-align: center;
            margin-top: 16px;
        }

        .back-home a {
            color: #64748b;
            text-decoration: none;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: color 0.2s;
        }

        .back-home a:hover {
            color: #0f3b7a;
        }

        .req-star {
            color: #dc2626;
            margin-left: 2px;
        }

        @media (max-width: 520px) {
            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .register-header {
                padding: 20px 20px;
            }

            .register-body {
                padding: 20px 20px 24px;
            }

            .logo img {
                height: 34px;
            }

            .register-header h2 {
                font-size: 1.2rem;
            }
        }
    </style>
</head>

<body>
    <div class="register-container">
        <div class="register-header">
            <div class="logo">
                <img src="../assets/img/logo3.png" alt="LinkUp Logo">
            </div>
            <h2>Daftar Akun Perusahaan</h2>
            <p>Mulai rekrut kandidat terbaik untuk bisnis Anda</p>
        </div>
        <div class="register-body">
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" id="registerForm">
                <div class="form-group">
                    <label>Nama Perusahaan <span class="req-star">*</span></label>
                    <div class="input-wrapper">
                        <i class="fas fa-building input-icon"></i>
                        <input type="text" name="nama_perusahaan" placeholder="Contoh: PT Teknologi Nusantara" required
                            value="<?php echo htmlspecialchars($_POST['nama_perusahaan'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Email Perusahaan <span class="req-star">*</span></label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" name="email" placeholder="hrd@perusahaan.com" required
                            value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Password <span class="req-star">*</span></label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" name="password" id="password" placeholder="Minimal 6 karakter"
                                required>
                            <button type="button" class="toggle-password" onclick="togglePassword('password')">
                                <i class="fas fa-eye-slash"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Konfirmasi Password <span class="req-star">*</span></label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" name="konfirmasi_password" id="konfirmasi_password"
                                placeholder="Ulangi password" required>
                            <button type="button" class="toggle-password"
                                onclick="togglePassword('konfirmasi_password')">
                                <i class="fas fa-eye-slash"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Provinsi</label>
                        <div class="input-wrapper">
                            <i class="fas fa-map-marker-alt input-icon"></i>
                            <select name="id_provinsi" id="id_provinsi">
                                <option value="">Pilih Provinsi</option>
                                <?php foreach ($provinsi_list as $provinsi): ?>
                                    <option value="<?php echo $provinsi['id_provinsi']; ?>" <?php echo (isset($_POST['id_provinsi']) && $_POST['id_provinsi'] == $provinsi['id_provinsi']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($provinsi['nama_provinsi']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <i class="fas fa-chevron-down select-arrow"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Kota/Kabupaten</label>
                        <div class="input-wrapper">
                            <i class="fas fa-city input-icon"></i>
                            <select name="id_kota" id="id_kota">
                                <option value="">Pilih Kota</option>
                            </select>
                            <i class="fas fa-chevron-down select-arrow"></i>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Alamat Perusahaan</label>
                    <div class="input-wrapper">
                        <textarea name="alamat_perusahaan"
                            placeholder="Jl. Contoh No. 123, Jakarta"><?php echo htmlspecialchars($_POST['alamat_perusahaan'] ?? ''); ?></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label>Nomor Telepon / WhatsApp</label>
                    <div class="input-wrapper">
                        <i class="fas fa-phone input-icon"></i>
                        <input type="tel" name="nohp_perusahaan" placeholder="081234567890"
                            value="<?php echo htmlspecialchars($_POST['nohp_perusahaan'] ?? ''); ?>">
                    </div>
                </div>

                <button type="submit" class="btn-register">
                    <i class="fas fa-user-plus"></i> Daftar Perusahaan
                </button>
            </form>

            <div class="login-link">
                <p>Sudah punya akun? <a href="login.php">Masuk Sekarang</a></p>
            </div>

            <div class="back-home">
                <a href="../index.php">
                    <i class="fas fa-arrow-left"></i> Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const input = document.getElementById(fieldId);
            const icon = input.parentElement.querySelector('.toggle-password i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        }

        const provinsiSelect = document.getElementById('id_provinsi');
        const kotaSelect = document.getElementById('id_kota');
        const selectedKota = '<?php echo isset($_POST['id_kota']) ? $_POST['id_kota'] : ''; ?>';

        if (provinsiSelect) {
            provinsiSelect.addEventListener('change', function () {
                const provinsiId = this.value;
                if (provinsiId) {
                    fetch(`register.php?ajax=get_kota&id_provinsi=${provinsiId}`)
                        .then(response => response.json())
                        .then(data => {
                            kotaSelect.innerHTML = '<option value="">Pilih Kota</option>';
                            data.forEach(kota => {
                                const option = document.createElement('option');
                                option.value = kota.id_kota;
                                option.textContent = kota.nama_kota;
                                if (selectedKota && selectedKota == kota.id_kota) {
                                    option.selected = true;
                                }
                                kotaSelect.appendChild(option);
                            });
                        })
                        .catch(error => console.error('Error:', error));
                } else {
                    kotaSelect.innerHTML = '<option value="">Pilih Kota</option>';
                }
            });
        }

        if (provinsiSelect && provinsiSelect.value) {
            provinsiSelect.dispatchEvent(new Event('change'));
        }
    </script>
</body>

</html>