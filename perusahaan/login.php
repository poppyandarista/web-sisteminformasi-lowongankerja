<?php
// perusahaan/login.php - Halaman Login Perusahaan
session_start();
require_once 'koneksi_perusahaan.php';

$db = new DatabasePerusahaan();
$error = '';

// Cek apakah ada pesan dari redirect
if (isset($_GET['message'])) {
    $error = htmlspecialchars($_GET['message']);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Email dan password wajib diisi';
    } else {
        $company = $db->loginPerusahaan($email, $password);

        if ($company) {
            $_SESSION['company_id'] = $company['id_perusahaan'];
            $_SESSION['company_name'] = $company['nama_perusahaan'];
            $_SESSION['company_email'] = $company['email_perusahaan'];
            $_SESSION['company_logged_in'] = true;

            header('Location: index.php');
            exit();
        } else {
            $error = 'Email atau password salah';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk | LinkUp untuk Perusahaan</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            max-width: 400px;
            width: 100%;
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, #0f3b7a 0%, #1e4a8a 100%);
            padding: 28px 24px;
            text-align: center;
            color: white;
        }

        .logo {
            margin-bottom: 12px;
        }

        .logo img {
            height: 42px;
            width: auto;
        }

        .login-header h2 {
            font-size: 1.3rem;
            font-weight: 600;
            margin-top: 8px;
        }

        .login-header p {
            font-size: 0.75rem;
            opacity: 0.85;
            margin-top: 4px;
        }

        .login-body {
            padding: 28px 24px 32px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-weight: 500;
            margin-bottom: 6px;
            color: #1e293b;
            font-size: 0.8rem;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i.input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 0.9rem;
            pointer-events: none;
            z-index: 1;
        }

        .input-wrapper input {
            width: 100%;
            padding: 10px 14px 10px 40px;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.85rem;
            transition: all 0.2s ease;
            background: #fafcff;
        }

        .input-wrapper input:focus {
            outline: none;
            border-color: #0f3b7a;
            box-shadow: 0 0 0 3px rgba(15, 59, 122, 0.1);
            background: white;
        }

        .input-wrapper .toggle-password {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #94a3b8;
            background: none;
            border: none;
            font-size: 0.9rem;
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

        .forgot-link {
            text-align: right;
            margin-top: 6px;
        }

        .forgot-link a {
            color: #0f3b7a;
            text-decoration: none;
            font-size: 0.7rem;
            font-weight: 500;
        }

        .forgot-link a:hover {
            text-decoration: underline;
        }

        .btn-login {
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

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px -6px rgba(15, 59, 122, 0.3);
        }

        .register-link {
            text-align: center;
            margin-top: 22px;
            padding-top: 18px;
            border-top: 1px solid #e2e8f0;
        }

        .register-link p {
            color: #475569;
            font-size: 0.8rem;
        }

        .register-link a {
            color: #0f3b7a;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.8rem;
        }

        .register-link a:hover {
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

        .alert-error i {
            font-size: 0.85rem;
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

        @media (max-width: 480px) {
            .login-header {
                padding: 22px 20px;
            }

            .login-body {
                padding: 22px 20px 28px;
            }

            .logo img {
                height: 36px;
            }

            .login-header h2 {
                font-size: 1.2rem;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-header">
            <div class="logo">
                <img src="../assets/img/logo3.png" alt="LinkUp Logo">
            </div>
            <h2>Masuk ke Akun Perusahaan</h2>
            <p>Kelola lowongan dan temukan kandidat terbaik</p>
        </div>
        <div class="login-body">
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label>Email Perusahaan</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" name="email" placeholder="contoh@perusahaan.com" required
                            value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" name="password" id="password" placeholder="Masukkan password" required>
                        <button type="button" class="toggle-password" onclick="togglePassword()">
                            <i class="fas fa-eye-slash"></i>
                        </button>
                    </div>
                    <div class="forgot-link">
                        <a href="lupa_password.php">Lupa password?</a>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Masuk
                </button>
            </form>

            <div class="register-link">
                <p>Belum memiliki akun perusahaan? <a href="register.php">Daftar Sekarang</a></p>
            </div>

            <div class="back-home">
                <a href="../index.php">
                    <i class="fas fa-arrow-left"></i> Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.querySelector('.toggle-password i');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            }
        }
    </script>
</body>

</html>