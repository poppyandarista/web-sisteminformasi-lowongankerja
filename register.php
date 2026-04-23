<?php
session_start();
require_once 'config/database.php';

$db = new database();

// Cek apakah user sudah login
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Handle registrasi form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $errors = [];

    // Validasi input
    if (empty($fullname)) {
        $errors['fullname'] = 'Nama lengkap harus diisi';
    } elseif (strlen($fullname) < 3) {
        $errors['fullname'] = 'Nama minimal 3 karakter';
    }

    if (empty($email)) {
        $errors['email'] = 'Email harus diisi';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Format email tidak valid';
    }

    if (empty($password)) {
        $errors['password'] = 'Password harus diisi';
    } elseif (strlen($password) < 6) {
        $errors['password'] = 'Password minimal 6 karakter';
    }

    // Cek apakah email sudah terdaftar
    if (empty($errors)) {
        $check_query = "SELECT id_user FROM user WHERE email_user = ?";
        $check_stmt = $db->koneksi->prepare($check_query);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $errors['email'] = 'Email sudah terdaftar. Silakan gunakan email lain.';
        }
        $check_stmt->close();
    }

    // Jika tidak ada error, simpan ke database
    if (empty($errors)) {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Buat username dari email (sebelum @)
        $username = explode('@', $email)[0];

        // Insert ke tabel user
        $query = "INSERT INTO user (email_user, password_user, username_user) VALUES (?, ?, ?)";
        $stmt = $db->koneksi->prepare($query);
        $stmt->bind_param("sss", $email, $hashed_password, $username);

        if ($stmt->execute()) {
            $user_id = $db->koneksi->insert_id;

            // Insert ke tabel profil
            $profile_query = "INSERT INTO profil (id_user, nama_user) VALUES (?, ?)";
            $profile_stmt = $db->koneksi->prepare($profile_query);
            $profile_stmt->bind_param("is", $user_id, $fullname);
            $profile_stmt->execute();
            $profile_stmt->close();

            // Set session
            $_SESSION['user_id'] = $user_id;
            $_SESSION['email'] = $email;
            $_SESSION['username'] = $username;
            $_SESSION['fullname'] = $fullname;

            echo json_encode([
                'success' => true,
                'message' => 'Registrasi berhasil! Mengalihkan ke halaman utama...',
                'redirect' => 'index.php'
            ]);
            exit();
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Registrasi gagal: ' . $stmt->error
            ]);
            exit();
        }
        $stmt->close();
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Registrasi gagal. Periksa kembali data Anda.',
            'errors' => $errors
        ]);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LinkUp | Daftar</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/img/favicon.png">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Inter", sans-serif;
            background: linear-gradient(135deg, #f0f7ff 0%, #e6f0ff 50%, #ffffff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            line-height: 1.6;
            position: relative;
            overflow: hidden;
        }

        .professional-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
        }

        .floating-icon {
            position: absolute;
            width: 24px;
            height: 24px;
            opacity: 0.4;
            animation: floatIcon 8s ease-in-out infinite;
        }

        .icon-1 {
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .icon-2 {
            top: 60%;
            left: 85%;
            animation-delay: 2s;
        }

        .icon-3 {
            top: 80%;
            left: 15%;
            animation-delay: 4s;
        }

        .icon-4 {
            top: 30%;
            left: 75%;
            animation-delay: 6s;
        }

        @keyframes floatIcon {

            0%,
            100% {
                transform: translateY(0);
                opacity: 0.4;
            }

            50% {
                transform: translateY(-20px);
                opacity: 0.7;
            }
        }

        .login-container {
            width: 100%;
            max-width: 440px;
            position: relative;
            z-index: 1;
        }

        .career-card {
            background: #ffffff;
            padding: 30px 40px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 440px;
            position: relative;
            overflow: hidden;
            z-index: 5;
            margin: 10px;
        }

        .professional-border {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #3d8eff, #5da1ff, #7db4ff, #5da1ff, #3d8eff);
            background-size: 200% 100%;
            animation: professionalFlow 4s ease-in-out infinite;
            border-radius: 32px 32px 0 0;
        }

        @keyframes professionalFlow {

            0%,
            100% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }
        }

        .career-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .career-logo {
            position: relative;
            width: 80px;
            height: 80px;
            margin: 0 auto 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #3d8eff;
        }

        .career-glow {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(61, 142, 255, 0.2) 0%, transparent 70%);
            border-radius: 50%;
            animation: careerGlow 3s ease-in-out infinite;
        }

        @keyframes careerGlow {

            0%,
            100% {
                transform: scale(1);
                opacity: 0.6;
            }

            50% {
                transform: scale(1.2);
                opacity: 0.8;
            }
        }

        .career-header h1 {
            color: #3d8eff;
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 8px;
            letter-spacing: -0.02em;
        }

        .career-header p {
            color: #5da1ff;
            font-size: 15px;
            font-weight: 400;
        }

        .professional-field {
            position: relative;
            margin-bottom: 32px;
        }

        .field-professional {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(61, 142, 255, 0.05);
            border: 1.5px solid rgba(61, 142, 255, 0.2);
            border-radius: 20px;
            transition: all 0.3s ease;
        }

        .professional-field input {
            width: 100%;
            background: transparent;
            border: none;
            padding: 18px 60px 18px 20px;
            color: #3d8eff;
            font-size: 16px;
            font-weight: 400;
            outline: none;
            position: relative;
            z-index: 2;
            font-family: inherit;
        }

        .professional-field input::placeholder {
            color: transparent;
        }

        .professional-field label {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #7db4ff;
            font-size: 16px;
            font-weight: 400;
            pointer-events: none;
            transition: all 0.3s ease;
            z-index: 3;
            background: rgba(255, 255, 255, 0.95);
            padding: 0 8px;
        }

        .professional-field input:focus+label,
        .professional-field input:not(:placeholder-shown)+label {
            top: 0;
            font-size: 13px;
            font-weight: 500;
            color: #3d8eff;
            transform: translateY(-50%);
        }

        .professional-field input:focus~.field-professional {
            border-color: #3d8eff;
            background: rgba(61, 142, 255, 0.1);
            box-shadow: 0 0 0 2px rgba(61, 142, 255, 0.2);
        }

        /* Success state untuk input field */
        .professional-field.success .field-professional {
            border-color: #10b981;
            background: rgba(16, 185, 129, 0.05);
        }

        .professional-field.success label {
            color: #10b981;
        }

        .professional-field.success .career-sprout {
            background: #10b981;
        }

        .growth-indicator {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            width: 8px;
            height: 8px;
            z-index: 4;
        }

        .career-sprout {
            width: 100%;
            height: 100%;
            background: #3d8eff;
            border-radius: 50%;
            opacity: 0;
            transform: scale(0);
            transition: all 0.3s ease;
        }

        .professional-field input:focus~.growth-indicator .career-sprout,
        .professional-field input:valid~.growth-indicator .career-sprout {
            opacity: 1;
            transform: scale(1);
        }

        .professional-toggle {
            position: absolute;
            right: 12px;
            top: 16px;
            background: none;
            border: none;
            cursor: pointer;
            color: #7db4ff;
            padding: 4px;
            border-radius: 8px;
            transition: all 0.3s ease;
            z-index: 4;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
        }

        .professional-toggle:hover {
            color: #3d8eff;
            background: rgba(61, 142, 255, 0.1);
        }

        .eye-hidden {
            display: none;
        }

        .professional-toggle.toggle-visible .eye-visible {
            display: none;
        }

        .professional-toggle.toggle-visible .eye-hidden {
            display: block;
        }

        .career-button {
            width: 100%;
            background: transparent;
            color: #ffffff;
            border: none;
            border-radius: 20px;
            padding: 0;
            cursor: pointer;
            font-family: inherit;
            font-size: 16px;
            font-weight: 600;
            position: relative;
            margin-bottom: 32px;
            overflow: hidden;
            min-height: 54px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .button-career {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, #3d8eff, #5da1ff, #7db4ff);
            border-radius: 20px;
            transition: all 0.3s ease;
        }

        .career-button:hover .button-career {
            background: linear-gradient(135deg, #2a6fd1, #3d8eff, #5da1ff);
            transform: scale(1.02);
        }

        .button-text {
            position: relative;
            z-index: 2;
            transition: opacity 0.3s ease;
        }

        .button-growth {
            position: absolute;
            z-index: 2;
            opacity: 0;
            transition: opacity 0.3s ease;
            display: flex;
            gap: 6px;
        }

        .growing-circle {
            width: 8px;
            height: 8px;
            background: #ffffff;
            border-radius: 50%;
            animation: professionalGrow 1.5s ease-in-out infinite;
        }

        .circle-2 {
            animation-delay: 0.2s;
        }

        .circle-3 {
            animation-delay: 0.4s;
        }

        @keyframes professionalGrow {

            0%,
            80%,
            100% {
                transform: scale(0.8);
                opacity: 0.6;
            }

            40% {
                transform: scale(1.2);
                opacity: 1;
            }
        }

        .career-button.loading .button-text {
            opacity: 0;
        }

        .career-button.loading .button-growth {
            opacity: 1;
        }

        .career-signup {
            text-align: center;
            font-size: 14px;
            color: #5da1ff;
        }

        .growth-link {
            color: #3d8eff;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
        }

        .growth-link::after {
            content: "";
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #3d8eff, #5da1ff);
            transition: width 0.3s ease;
        }

        .growth-link:hover::after {
            width: 100%;
        }

        .growth-link:hover {
            color: #2a6fd1;
        }

        .gentle-error {
            color: #ff7043;
            font-size: 12px;
            font-weight: 500;
            position: absolute;
            bottom: -20px;
            left: 5px;
            right: 0;
            padding: 0 5px;
            opacity: 0;
            transform: translateY(5px);
            transition: all 0.3s ease;
            z-index: 5;
            text-align: left;
        }

        .gentle-error.show {
            opacity: 1;
            transform: translateY(0);
        }

        .professional-field.error .field-professional {
            border-color: #ff7043;
            background: rgba(255, 112, 67, 0.1);
        }

        .professional-field.error label {
            color: #ff5722;
        }

        /* Career Success Animation */
        .career-success {
            display: none;
            text-align: center;
            padding: 40px 20px;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.5s ease;
        }

        .career-success.show {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }

        .success-mandala {
            position: relative;
            width: 90px;
            height: 90px;
            margin: 0 auto 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .mandala-ring {
            position: absolute;
            border: 2px solid #3d8eff;
            border-radius: 50%;
            animation: mandalaGrow 1.2s ease-out forwards;
            opacity: 0;
        }

        .ring-1 {
            width: 60px;
            height: 60px;
            top: 15px;
            left: 15px;
            animation-delay: 0s;
        }

        .ring-2 {
            width: 75px;
            height: 75px;
            top: 7.5px;
            left: 7.5px;
            animation-delay: 0.2s;
        }

        .ring-3 {
            width: 90px;
            height: 90px;
            top: 0;
            left: 0;
            animation-delay: 0.4s;
        }

        @keyframes mandalaGrow {
            0% {
                opacity: 0;
                transform: scale(0) rotate(0deg);
            }

            50% {
                opacity: 1;
                transform: scale(1.1) rotate(180deg);
            }

            100% {
                opacity: 0.8;
                transform: scale(1) rotate(360deg);
            }
        }

        .mandala-center {
            position: relative;
            z-index: 2;
            color: #3d8eff;
            animation: centerBloom 0.8s ease-out 0.6s forwards;
            opacity: 0;
        }

        @keyframes centerBloom {
            0% {
                opacity: 0;
                transform: scale(0);
            }

            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        .career-success h3 {
            color: #3d8eff;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .career-success p {
            color: #5da1ff;
            font-size: 14px;
        }

        @media (max-width: 480px) {
            .career-card {
                padding: 36px 28px;
            }

            .career-header h1 {
                font-size: 1.75rem;
            }
        }
    </style>
</head>

<body>
    <div class="professional-background">
        <div class="floating-icon icon-1">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <circle cx="11" cy="11" r="8" stroke="#3d8eff" stroke-width="1.5" />
                <path d="M16.5 16.5L21 21" stroke="#3d8eff" stroke-width="1.5" stroke-linecap="round" />
            </svg>
        </div>
        <div class="floating-icon icon-2">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <circle cx="11" cy="11" r="8" stroke="#3d8eff" stroke-width="1.5" />
                <path d="M16.5 16.5L21 21" stroke="#3d8eff" stroke-width="1.5" stroke-linecap="round" />
            </svg>
        </div>
        <div class="floating-icon icon-3">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <circle cx="11" cy="11" r="8" stroke="#3d8eff" stroke-width="1.5" />
                <path d="M16.5 16.5L21 21" stroke="#3d8eff" stroke-width="1.5" stroke-linecap="round" />
            </svg>
        </div>
        <div class="floating-icon icon-4">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <circle cx="11" cy="11" r="8" stroke="#3d8eff" stroke-width="1.5" />
                <path d="M16.5 16.5L21 21" stroke="#3d8eff" stroke-width="1.5" stroke-linecap="round" />
            </svg>
        </div>
    </div>

    <div class="login-container">
        <div class="career-card">
            <div class="professional-border"></div>

            <div class="career-header">
                <div class="career-logo">
                    <img src="assets/img/favicon.png" alt="LinkUp Logo" style="width: 100%; height: auto;">
                    <div class="career-glow"></div>
                </div>
                <h1>LinkUp</h1>
                <p>Baru di LinkUp? Bergabunglah dengan ribuan profesional!</p>
            </div>

            <form class="harmony-form" id="registerForm" method="POST" action="register.php">
                <div class="professional-field" id="fullnameField">
                    <div class="field-professional"></div>
                    <input type="text" id="fullname" name="fullname" required autocomplete="name" placeholder=" ">
                    <label for="fullname">Nama Lengkap</label>
                    <div class="growth-indicator">
                        <div class="career-sprout"></div>
                    </div>
                    <span class="gentle-error" id="fullnameError"></span>
                </div>

                <div class="professional-field" id="emailField">
                    <div class="field-professional"></div>
                    <input type="email" id="email" name="email" required autocomplete="email" placeholder=" ">
                    <label for="email">Email</label>
                    <div class="growth-indicator">
                        <div class="career-sprout"></div>
                    </div>
                    <span class="gentle-error" id="emailError"></span>
                </div>

                <div class="professional-field" id="passwordField">
                    <div class="field-professional"></div>
                    <input type="password" id="password" name="password" required autocomplete="new-password"
                        placeholder=" ">
                    <label for="password">Kata Sandi</label>
                    <button type="button" class="professional-toggle" id="passwordToggle"
                        aria-label="Toggle password visibility">
                        <svg class="eye-visible" width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path
                                d="M10 4c-4 0-7 3-8 6 1 3 4 6 8 6s7-3 8-6c-1-3-4-6-8-6zm0 10a4 4 0 110-8 4 4 0 010 8zm0-6a2 2 0 100 4 2 2 0 000-4z"
                                fill="currentColor" />
                        </svg>
                        <svg class="eye-hidden" width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path
                                d="M3 3l14 14M8.5 8.5a2 2 0 002.83 2.83m-.83-4.83a4 4 0 014 4M10 6C6 6 3 9 2 12c.5 1.5 2 3.5 4 4.5M10 14c4 0 7-3 8-6-.5-1.5-2-3.5-4-4.5"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </button>
                    <span class="gentle-error" id="passwordError"></span>
                </div>

                <button type="submit" class="career-button">
                    <div class="button-career"></div>
                    <span class="button-text">Daftar</span>
                    <div class="button-growth">
                        <div class="growing-circle circle-1"></div>
                        <div class="growing-circle circle-2"></div>
                        <div class="growing-circle circle-3"></div>
                    </div>
                </button>
            </form>

            <div class="career-signup">
                <span>Sudah punya akun?</span>
                <a href="login.php" class="growth-link">Masuk</a>
            </div>

            <div class="career-success" id="successMessage">
                <div class="success-mandala">
                    <div class="mandala-ring ring-1"></div>
                    <div class="mandala-ring ring-2"></div>
                    <div class="mandala-ring ring-3"></div>
                    <div class="mandala-center">
                        <svg width="28" height="28" viewBox="0 0 28 28" fill="none">
                            <path d="M8 14l6 6 12-12" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </div>
                </div>
                <h3>Selamat Datang!</h3>
                <p>Akun Anda berhasil dibuat. Mengalihkan ke halaman utama...</p>
            </div>

            <script>
                class RegisterForm {
                    constructor() {
                        this.form = document.getElementById('registerForm');
                        this.fullnameInput = document.getElementById('fullname');
                        this.emailInput = document.getElementById('email');
                        this.passwordInput = document.getElementById('password');
                        this.passwordToggle = document.getElementById('passwordToggle');
                        this.submitButton = this.form.querySelector('.career-button');
                        this.successMessage = document.getElementById('successMessage');

                        this.init();
                    }

                    init() {
                        this.bindEvents();
                        this.setupPasswordToggle();
                        this.setupPlaceholders();
                        this.setupInputValidation();
                    }

                    bindEvents() {
                        this.form.addEventListener('submit', (e) => this.handleSubmit(e));
                        this.fullnameInput.addEventListener('blur', () => this.validateFullname());
                        this.emailInput.addEventListener('blur', () => this.validateEmail());
                        this.passwordInput.addEventListener('blur', () => this.validatePassword());
                        this.fullnameInput.addEventListener('input', () => this.clearError('fullname'));
                        this.emailInput.addEventListener('input', () => this.clearError('email'));
                        this.passwordInput.addEventListener('input', () => this.clearError('password'));
                    }

                    setupPlaceholders() {
                        this.fullnameInput.setAttribute('placeholder', ' ');
                        this.emailInput.setAttribute('placeholder', ' ');
                        this.passwordInput.setAttribute('placeholder', ' ');
                    }

                    setupPasswordToggle() {
                        this.passwordToggle.addEventListener('click', () => {
                            const type = this.passwordInput.type === 'password' ? 'text' : 'password';
                            this.passwordInput.type = type;
                            this.passwordToggle.classList.toggle('toggle-visible', type === 'text');
                        });
                    }

                    setupInputValidation() {
                        // Real-time validation dengan efek success
                        this.fullnameInput.addEventListener('input', () => {
                            if (this.fullnameInput.value.trim().length >= 3) {
                                this.showSuccess('fullnameField');
                            } else {
                                this.removeSuccess('fullnameField');
                            }
                        });

                        this.emailInput.addEventListener('input', () => {
                            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                            if (emailRegex.test(this.emailInput.value.trim())) {
                                this.showSuccess('emailField');
                            } else {
                                this.removeSuccess('emailField');
                            }
                        });

                        this.passwordInput.addEventListener('input', () => {
                            if (this.passwordInput.value.length >= 6) {
                                this.showSuccess('passwordField');
                            } else {
                                this.removeSuccess('passwordField');
                            }
                        });
                    }

                    showSuccess(fieldId) {
                        const field = document.getElementById(fieldId);
                        field.classList.add('success');
                    }

                    removeSuccess(fieldId) {
                        const field = document.getElementById(fieldId);
                        field.classList.remove('success');
                    }

                    validateFullname() {
                        const fullname = this.fullnameInput.value.trim();
                        if (!fullname) {
                            this.showError('fullname', 'Mohon masukkan nama lengkap');
                            this.removeSuccess('fullnameField');
                            return false;
                        }
                        if (fullname.length < 3) {
                            this.showError('fullname', 'Nama minimal 3 karakter');
                            this.removeSuccess('fullnameField');
                            return false;
                        }
                        this.clearError('fullname');
                        this.showSuccess('fullnameField');
                        return true;
                    }

                    validateEmail() {
                        const email = this.emailInput.value.trim();
                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!email) {
                            this.showError('email', 'Mohon masukkan alamat email');
                            this.removeSuccess('emailField');
                            return false;
                        }
                        if (!emailRegex.test(email)) {
                            this.showError('email', 'Format email tidak valid');
                            this.removeSuccess('emailField');
                            return false;
                        }
                        this.clearError('email');
                        this.showSuccess('emailField');
                        return true;
                    }

                    validatePassword() {
                        const password = this.passwordInput.value;
                        if (!password) {
                            this.showError('password', 'Mohon masukkan kata sandi');
                            this.removeSuccess('passwordField');
                            return false;
                        }
                        if (password.length < 6) {
                            this.showError('password', 'Kata sandi minimal 6 karakter');
                            this.removeSuccess('passwordField');
                            return false;
                        }
                        this.clearError('password');
                        this.showSuccess('passwordField');
                        return true;
                    }

                    showError(field, message) {
                        const inputField = document.getElementById(field);
                        const professionalField = inputField.closest('.professional-field');
                        const errorElement = document.getElementById(`${field}Error`);
                        professionalField.classList.add('error');
                        professionalField.classList.remove('success');
                        errorElement.textContent = message;
                        errorElement.classList.add('show');
                    }

                    clearError(field) {
                        const inputField = document.getElementById(field);
                        const professionalField = inputField?.closest('.professional-field');
                        const errorElement = document.getElementById(`${field}Error`);
                        if (professionalField) {
                            professionalField.classList.remove('error');
                        }
                        if (errorElement) {
                            errorElement.classList.remove('show');
                            setTimeout(() => { errorElement.textContent = ''; }, 300);
                        }
                    }

                    showAlert(type, message) {
                        // Remove existing alerts
                        const existingAlert = document.querySelector('.custom-alert');
                        if (existingAlert) {
                            existingAlert.remove();
                        }

                        // Create alert element
                        const alert = document.createElement('div');
                        alert.className = `custom-alert alert-${type}`;
                        alert.innerHTML = `
        <div class="alert-content">
            <div class="alert-icon">
                ${type === 'success' ?
                                '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2ZM16.2803 9.21967C16.5732 8.92678 16.5732 8.4519 16.2803 8.15899C15.9874 7.86609 15.5126 7.86609 15.2197 8.15899L10.5 12.8787L8.78033 11.159C8.48744 10.8661 8.01256 10.8661 7.71967 11.159C7.42678 11.4519 7.42678 11.9268 7.71967 12.2197L9.96967 14.4697C10.2626 14.7626 10.7374 14.7626 11.0303 14.4697L16.2803 9.21967Z" fill="#10b981"/></svg>' :
                                '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2ZM11.25 7.5C11.25 6.94772 11.6977 6.5 12.25 6.5H12.75C13.3023 6.5 13.75 6.94772 13.75 7.5C13.75 8.05228 13.3023 8.5 12.75 8.5H12.25C11.6977 8.5 11.25 8.05228 11.25 7.5ZM12 10.25C12.4142 10.25 12.75 10.5858 12.75 11V16C12.75 16.4142 12.4142 16.75 12 16.75C11.5858 16.75 11.25 16.4142 11.25 16V11C11.25 10.5858 11.5858 10.25 12 10.25Z" fill="#ef4444"/></svg>'
                            }
            </div>
            <div class="alert-text">
                <div class="alert-title">${type === 'success' ? 'Registrasi Berhasil' : 'Registrasi Gagal'}</div>
                <div class="alert-message">${message}</div>
            </div>
            <button class="alert-close" onclick="this.parentElement.parentElement.remove()">×</button>
        </div>
    `;

                        // Add styles
                        alert.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 10000;
        min-width: 350px;
        max-width: 450px;
        border-radius: 12px;
        padding: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transform: translateX(100%);
        transition: all 0.3s ease;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    `;

                        const alertContent = alert.querySelector('.alert-content');
                        alertContent.style.cssText = `
        display: flex;
        align-items: flex-start;
        gap: 12px;
    `;

                        const alertIcon = alert.querySelector('.alert-icon');
                        alertIcon.style.cssText = `
        width: 24px;
        height: 24px;
        flex-shrink: 0;
        margin-top: 2px;
    `;

                        const alertText = alert.querySelector('.alert-text');
                        alertText.style.cssText = `
        flex: 1;
    `;

                        const alertTitle = alert.querySelector('.alert-title');
                        alertTitle.style.cssText = `
        font-size: 14px;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 4px;
        line-height: 1.4;
    `;

                        const alertMessage = alert.querySelector('.alert-message');
                        alertMessage.style.cssText = `
        font-size: 13px;
        font-weight: 400;
        color: #6b7280;
        line-height: 1.4;
    `;

                        const alertClose = alert.querySelector('.alert-close');
                        alertClose.style.cssText = `
        background: none;
        border: none;
        font-size: 20px;
        cursor: pointer;
        color: #9ca3af;
        padding: 0;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.2s;
        flex-shrink: 0;
    `;

                        // Set colors based on type
                        if (type === 'success') {
                            alert.style.background = '#ecfdf3';
                            alert.style.border = '1px solid #10b981';
                            alertTitle.style.color = '#065f46';
                            alertMessage.style.color = '#047857';
                            alertClose.style.color = '#10b981';
                        } else if (type === 'error') {
                            alert.style.background = '#fef3f2';
                            alert.style.border = '1px solid #ef4444';
                            alertTitle.style.color = '#991b1b';
                            alertMessage.style.color = '#b91c1c';
                            alertClose.style.color = '#ef4444';
                        }

                        // Add hover effects
                        alertClose.addEventListener('mouseenter', () => {
                            alertClose.style.background = 'rgba(0, 0, 0, 0.05)';
                        });
                        alertClose.addEventListener('mouseleave', () => {
                            alertClose.style.background = 'none';
                        });

                        // Append to body
                        document.body.appendChild(alert);

                        // Animate in
                        setTimeout(() => {
                            alert.style.transform = 'translateX(0)';
                        }, 100);

                        // Auto remove after 3 seconds
                        setTimeout(() => {
                            if (alert.parentElement) {
                                alert.style.transform = 'translateX(100%)';
                                setTimeout(() => alert.remove(), 300);
                            }
                        }, 3000);
                    }

                    showCareerSuccess() {
                        // Hide form with transition
                        this.form.style.transform = 'scale(0.95)';
                        this.form.style.opacity = '0';

                        setTimeout(() => {
                            this.form.style.display = 'none';
                            // Hapus baris document.querySelector('.professional-social') karena tidak ada di register
                            document.querySelector('.career-signup').style.display = 'none';
                            // Hapus baris document.querySelector('.career-divider') karena tidak ada di register

                            // Show success message with custom alert (SAMA PERSIS DENGAN LOGIN)
                            this.showAlert('success', 'Registrasi Berhasil! Mengalihkan ke halaman utama...');
                            this.successMessage.classList.add('show');

                        }, 300);
                    }

                    async handleSubmit(e) {
                        e.preventDefault();

                        const isFullnameValid = this.validateFullname();
                        const isEmailValid = this.validateEmail();
                        const isPasswordValid = this.validatePassword();

                        if (!isFullnameValid || !isEmailValid || !isPasswordValid) {
                            return;
                        }

                        this.setLoading(true);

                        try {
                            const formData = new FormData(this.form);
                            const response = await fetch('register.php', {
                                method: 'POST',
                                body: formData
                            });

                            const result = await response.json();

                            if (result.success) {
                                this.showCareerSuccess();
                                setTimeout(() => {
                                    window.location.href = result.redirect;
                                }, 2500);
                            } else {
                                this.showAlert('error', result.message);
                                if (result.errors) {
                                    if (result.errors.fullname) this.showError('fullname', result.errors.fullname);
                                    if (result.errors.email) this.showError('email', result.errors.email);
                                    if (result.errors.password) this.showError('password', result.errors.password);
                                }
                            }
                        } catch (error) {
                            console.error('Registration error:', error);
                            this.showAlert('error', 'Terjadi kesalahan koneksi. Silakan coba lagi.');
                        } finally {
                            this.setLoading(false);
                        }
                    }

                    setLoading(loading) {
                        this.submitButton.classList.toggle('loading', loading);
                        this.submitButton.disabled = loading;
                    }
                }

                document.addEventListener('DOMContentLoaded', () => {
                    new RegisterForm();
                });
            </script>
</body>

</html>