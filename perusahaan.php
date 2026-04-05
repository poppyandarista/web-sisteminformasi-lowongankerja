<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Untuk Perusahaan | Rekrut Kandidat Terbaik - LinkUp</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/img/favicon.png">


    <!-- Google Fonts: Poppins -->
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
            background-color: #ffffff;
            color: #0f172a;
            scroll-behavior: smooth;
            line-height: 1.5;
        }

        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #3b82f6;
            --accent: #2563eb;
            --secondary: #1e293b;
            --gray-50: #fafcff;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-400: #94a3b8;
            --gray-600: #475569;
            --gray-800: #1e293b;
            --shadow-sm: 0 12px 30px -8px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 20px 35px -12px rgba(0, 0, 0, 0.08);
            --shadow-hover: 0 30px 40px -18px rgba(0, 0, 0, 0.2);
            --shadow-card: 0 15px 35px -12px rgba(0, 0, 0, 0.08);
        }

        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 32px;
        }

        /* ========= NAVBAR STICKY ========= */
        .navbar {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.6);
            transition: all 0.3s ease;
        }

        .nav-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            padding: 18px 0;
        }

        .logo {
            font-size: 1.9rem;
            font-weight: 800;
            background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            letter-spacing: -0.02em;
            transition: transform 0.2s ease;
            cursor: pointer;
        }

        .logo:hover {
            transform: scale(1.02);
        }

        .nav-links {
            display: flex;
            gap: 36px;
            align-items: center;
            flex-wrap: wrap;
        }

        .nav-links a {
            text-decoration: none;
            font-weight: 500;
            color: var(--gray-800);
            transition: all 0.25s ease;
            font-size: 0.95rem;
            letter-spacing: -0.2px;
            position: relative;
            cursor: pointer;
        }

        /* Hover efek garis bawah untuk navigasi biasa */
        .nav-links a:not(.btn-primary-nav):not(.btn-outline)::after {
            content: '';
            position: absolute;
            bottom: -6px;
            left: 0;
            width: 0%;
            height: 2.5px;
            background: var(--primary);
            border-radius: 4px;
            transition: width 0.3s ease;
        }

        .nav-links a:not(.btn-primary-nav):not(.btn-outline):hover::after {
            width: 100%;
        }

        .nav-links a:not(.btn-primary-nav):not(.btn-outline):hover {
            color: var(--primary);
            transform: translateY(-1px);
        }

        .btn-outline {
            border: 1.5px solid var(--gray-200);
            padding: 8px 22px;
            border-radius: 100px;
            background: white;
            transition: all 0.25s;
        }

        .btn-outline:hover {
            border-color: var(--primary);
            background: #f0f6ff;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .btn-primary-nav {
            background: var(--primary);
            color: white !important;
            padding: 8px 22px;
            border-radius: 100px;
            box-shadow: 0 3px 8px rgba(15, 59, 122, 0.15);
            transition: all 0.25s;
        }

        .btn-primary-nav:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(15, 59, 122, 0.2);
        }

        .mobile-toggle {
            display: none;
            font-size: 1.6rem;
            cursor: pointer;
            color: var(--gray-800);
        }

        /* BUTTONS */
        .btn-primary {
            background: var(--primary);
            color: white;
            border: none;
            padding: 14px 32px;
            border-radius: 100px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 6px 14px rgba(15, 59, 122, 0.2);
            letter-spacing: -0.2px;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-3px);
            box-shadow: 0 16px 28px -8px rgba(15, 59, 122, 0.3);
        }

        .btn-secondary {
            background: white;
            border: 1.5px solid var(--gray-200);
            padding: 14px 32px;
            border-radius: 100px;
            font-weight: 600;
            color: var(--gray-800);
            transition: all 0.25s;
            cursor: pointer;
        }

        .btn-secondary:hover {
            border-color: var(--primary);
            background: #fafcff;
            transform: translateY(-3px);
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.05);
        }

        /* HERO SECTION */
        .hero {
            padding: 90px 0 70px;
            position: relative;
        }

        .hero-grid {
            display: flex;
            align-items: center;
            gap: 60px;
            flex-wrap: wrap;
        }

        .hero-content {
            flex: 1.1;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.2;
            letter-spacing: -0.02em;
            background: linear-gradient(145deg, #2563eb, #2563eb);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 24px;
        }

        .hero-sub {
            font-size: 1.18rem;
            color: var(--gray-600);
            max-width: 560px;
            margin-bottom: 36px;
            line-height: 1.5;
        }

        .hero-buttons {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .hero-image {
            flex: 1;
            background: radial-gradient(circle at 30% 20%, #eef4ff, #ffffff);
            border-radius: 48px;
            padding: 24px;
            text-align: center;
            box-shadow: var(--shadow-card);
        }

        .img-modern {
            display: flex;
            justify-content: center;
            gap: 32px;
            flex-wrap: wrap;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 36px;
            padding: 32px 20px;
        }

        .img-modern i {
            font-size: 56px;
            background: white;
            padding: 20px;
            border-radius: 32px;
            box-shadow: 0 15px 30px -12px rgba(0, 0, 0, 0.1);
            color: var(--primary);
            transition: 0.2s;
        }

        /* SECTION GLOBAL */
        section {
            padding: 90px 0;
        }

        .section-title {
            font-size: 2.4rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 18px;
            letter-spacing: -0.02em;
            background: linear-gradient(120deg, #2563eb, #2563eb);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .section-desc {
            text-align: center;
            color: var(--gray-600);
            max-width: 700px;
            margin: 0 auto 56px;
            font-size: 1.05rem;
        }

        /* CARD GRID */
        .grid-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(270px, 1fr));
            gap: 36px;
            margin-top: 20px;
        }

        .card {
            background: white;
            border-radius: 32px;
            padding: 36px 28px;
            transition: all 0.35s cubic-bezier(0.2, 0, 0, 1);
            box-shadow: var(--shadow-card);
            border: 1px solid rgba(226, 232, 240, 0.7);
            cursor: pointer;
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-hover);
            border-color: #cbdff2;
        }

        .card-icon {
            background: linear-gradient(135deg, #eef4ff, #ffffff);
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 28px;
            margin-bottom: 28px;
            color: var(--primary);
            font-size: 32px;
        }

        .card h3 {
            font-size: 1.45rem;
            margin-bottom: 14px;
            font-weight: 700;
        }

        /* STEPS */
        .steps-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 28px;
            margin-top: 24px;
        }

        .step-item {
            flex: 1;
            min-width: 210px;
            text-align: center;
            background: var(--gray-50);
            padding: 34px 20px;
            border-radius: 40px;
            transition: 0.3s;
            border: 1px solid var(--gray-100);
            cursor: pointer;
        }

        .step-item:hover {
            transform: translateY(-6px);
            background: white;
            box-shadow: var(--shadow-sm);
        }

        .step-number {
            width: 54px;
            height: 54px;
            background: var(--primary);
            color: white;
            border-radius: 60px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.7rem;
            margin-bottom: 24px;
            box-shadow: 0 8px 20px rgba(15, 59, 122, 0.2);
        }

        /* FEATURE GRID */
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 32px;
        }

        .feature-item {
            background: #ffffff;
            border: 1px solid var(--gray-100);
            border-radius: 32px;
            padding: 32px 24px;
            transition: all 0.3s;
            text-align: center;
            box-shadow: var(--shadow-sm);
            cursor: pointer;
        }

        .feature-item:hover {
            transform: translateY(-5px);
            border-color: #b9d0f0;
            box-shadow: 0 25px 35px -18px rgba(0, 0, 0, 0.12);
        }

        .feature-item i {
            font-size: 2.4rem;
            background: #eef4ff;
            padding: 18px;
            border-radius: 28px;
            color: var(--primary);
            margin-bottom: 24px;
        }

        /* BLOG CARDS (6 card tips rekrutmen, tanpa tombol baca selengkapnya) */
        .tips-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 32px;
        }

        .tips-card {
            background: white;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
            border: 1px solid var(--gray-100);
            cursor: pointer;
        }

        .tips-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-hover);
            border-color: #cddff5;
        }

        .tips-content {
            padding: 28px;
        }

        .tips-icon {
            width: 52px;
            height: 52px;
            background: #eef4ff;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            color: var(--primary);
            font-size: 1.6rem;
        }

        .tips-title {
            font-weight: 700;
            font-size: 1.2rem;
            margin-bottom: 12px;
            line-height: 1.4;
        }

        .tips-desc {
            color: var(--gray-600);
            font-size: 0.9rem;
            line-height: 1.5;
        }

        /* CTA */
        .cta-section {
            background: linear-gradient(115deg, #f0f6ff 0%, #ffffff 100%);
            border-radius: 56px;
            margin: 40px auto 20px;
            padding: 70px 40px;
            text-align: center;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.8), var(--shadow-md);
        }

        .cta-title {
            font-size: 2.3rem;
            font-weight: 700;
            margin-bottom: 20px;
        }

        /* FOOTER */
        footer {
            background: #0a2540;
            color: #cbd5e6;
            padding: 64px 0 36px;
            margin-top: 60px;
            border-top-left-radius: 32px;
            border-top-right-radius: 32px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 48px;
            margin-bottom: 48px;
        }

        .footer-col h4 {
            color: white;
            margin-bottom: 22px;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .footer-col a {
            display: block;
            color: #94a3b8;
            text-decoration: none;
            margin-bottom: 12px;
            transition: all 0.2s;
            font-size: 0.9rem;
            cursor: pointer;
        }

        .footer-col a:hover {
            color: white;
            transform: translateX(4px);
        }

        .social-icons {
            display: flex;
            gap: 18px;
            margin-top: 8px;
        }

        .social-icons a {
            font-size: 1.3rem;
        }

        .copyright {
            text-align: center;
            padding-top: 32px;
            border-top: 1px solid #1e3a5f;
            font-size: 0.85rem;
        }

        /* ANIMATIONS & RESPONSIVE */
        .fade-up {
            opacity: 0;
            transform: translateY(28px);
            transition: opacity 0.7s cubic-bezier(0.2, 0.9, 0.4, 1.1), transform 0.7s ease;
        }

        .fade-up.appear {
            opacity: 1;
            transform: translateY(0);
        }

        /* Section highlight saat navigasi diklik (smooth hover tetap) */
        html {
            scroll-padding-top: 90px;
        }

        @media (max-width: 992px) {
            .hero-title {
                font-size: 2.8rem;
            }

            .container {
                padding: 0 24px;
            }

            .section-title {
                font-size: 2rem;
            }

            .tips-grid {
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .nav-links {
                display: none;
                flex-direction: column;
                width: 100%;
                background: white;
                padding: 24px 0;
                gap: 20px;
                border-radius: 24px;
                margin-top: 12px;
                box-shadow: 0 15px 30px -10px rgba(0, 0, 0, 0.1);
            }

            .nav-links.show {
                display: flex;
            }

            .mobile-toggle {
                display: block;
            }

            .hero-grid {
                flex-direction: column;
            }

            .cta-section {
                padding: 48px 24px;
            }

            .steps-container {
                flex-direction: column;
            }

            .tips-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <nav class="navbar">
        <div class="container nav-inner">
            <div class="logo" onclick="scrollToSection('hero')">
                <img src="assets/img/logo3.png" alt="LinkUp Logo" style="height: 40px; width: auto; cursor: pointer;">
            </div>
            <div class="mobile-toggle" id="mobileToggle">
                <i class="fas fa-bars"></i>
            </div>
            <div class="nav-links" id="navLinks">
                <a onclick="scrollToSection('hero')">Beranda</a>
                <a onclick="scrollToSection('keunggulan')" class="btn-primary-nav"
                    style="background:var(--primary);">Pasang Lowongan</a>
                <a onclick="scrollToSection('fitur')">Fitur</a>
                <a o nclick="scrollToSection('tips')">Tips Rekrutmen</a>
                <a href="perusahaan/login.php" class="btn-outline">Masuk</a>
                <a href="perusahaan/register.php" class="btn-primary-nav">Daftar</a>
                </di v>
            </div>
    </nav>

    <main>
        <!-- Hero Section (Beranda) -->
        <section id="hero" class="hero">
            <div class="container hero-grid">
                <div class="hero-content fade-up">
                    <h1 class="hero-title">Temukan Kandidat Terbaik untuk Bisnis Anda</h1>
                    <p class="hero-sub">Rekrutmen modern tanpa ribet. Pasang lowongan gratis, jangkau talenta unggul,
                        dan kelola semua lamaran dari satu dashboard profesional.</p>
                    <div class="hero-buttons">
                        <!-- PERBAIKAN 1: Button Pasang Lowongan -> scroll ke section keunggulan -->
                        <button class="btn-primary" onclick="scrollToSection('keunggulan')"><i
                                class="fas fa-pen-ruler"></i> Pasang Lowongan</button>
                        <!-- PERBAIKAN 2: Button Daftar Sekarang -> menuju ke register.php -->
                        <button class="btn-secondary" onclick="window.location.href='perusahaan/register.php'"><i
                                class="fas fa-building"></i> Daftar Sekarang</button>
                    </div>
                </div>
                <div class="hero-image fade-up">
                    <div class="img-modern">
                        <i class="fas fa-chart-line"></i>
                        <i class="fas fa-user-tie"></i>
                        <i class="fas fa-handshake"></i>
                        <p style="width:100%; margin-top:24px; font-weight:500; color:#1e3a8a;">Solusi rekrutmen
                            terpercaya</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Keunggulan -->
        <section id="keunggulan" style="background: #fafdff;">
            <div class="container">
                <h2 class="section-title fade-up">Keunggulan Platform Kami</h2>
                <p class="section-desc fade-up">Dibuat untuk mempermudah tim HR dan perekrut dalam setiap tahapan</p>
                <div class="grid-cards">
                    <div class="card fade-up">
                        <div class="card-icon"><i class="fas fa-bolt"></i></div>
                        <h3>Mudah Pasang Lowongan</h3>
                        <p>Form cerdas, hanya 2 menit. Template profesional dan saran AI untuk menarik lebih banyak
                            pelamar.</p>
                    </div>
                    <div class="card fade-up">
                        <div class="card-icon"><i class="fas fa-globe"></i></div>
                        <h3>Jangkauan Kandidat Luas</h3>
                        <p>Lebih dari 25.000 profesional aktif dari berbagai industri siap melamar ke perusahaan Anda.
                        </p>
                    </div>
                    <div class="card fade-up">
                        <div class="card-icon"><i class="fas fa-inbox"></i></div>
                        <h3>Kelola Lamaran dengan Mudah</h3>
                        <p>Filter, tagging, dan catatan internal untuk mempercepat proses seleksi hingga 60%.</p>
                    </div>
                    <div class="card fade-up">
                        <div class="card-icon"><i class="fas fa-desktop"></i></div>
                        <h3>Dashboard Perusahaan</h3>
                        <p>Analitik real-time, rekomendasi kandidat, dan insight untuk pengambilan keputusan lebih baik.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Cara Kerja -->
        <section>
            <div class="container">
                <h2 class="section-title fade-up">Cara Kerja LinkUp</h2>
                <p class="section-desc fade-up">Empat langkah sederhana menuju perekrutan yang lebih efektif</p>
                <div class="steps-container">
                    <div class="step-item fade-up">
                        <div class="step-number">1</div>
                        <h4>Daftar akun perusahaan</h4>
                        <p>Registrasi gratis & verifikasi identitas bisnis</p>
                    </div>
                    <div class="step-item fade-up">
                        <div class="step-number">2</div>
                        <h4>Pasang lowongan kerja</h4>
                        <p>Deskripsi menarik & benefit, unggah dalam hitungan menit</p>
                    </div>
                    <div class="step-item fade-up">
                        <div class="step-number">3</div>
                        <h4>Terima lamaran kandidat</h4>
                        <p>CV & portofolio langsung masuk ke dashboard</p>
                    </div>
                    <div class="step-item fade-up">
                        <div class="step-number">4</div>
                        <h4>Pilih kandidat terbaik</h4>
                        <p>Hubungi, wawancara, dan rekrut talenta pilihan</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Fitur Utama -->
        <section id="fitur" style="background: #fafdff;">
            <div class="container">
                <h2 class="section-title fade-up">Fitur Unggulan untuk HR Modern</h2>
                <p class="section-desc fade-up">Alat canggih yang membantu Anda menyaring & merekrut lebih cerdas</p>
                <div class="feature-grid">
                    <div class="feature-item fade-up"><i class="fas fa-bullhorn"></i>
                        <h3>Manajemen Lowongan</h3>
                        <p>Aktif/nonaktifkan, duplikasi, dan atur jadwal posting secara fleksibel.</p>
                    </div>
                    <div class="feature-item fade-up"><i class="fas fa-address-card"></i>
                        <h3>Data Pelamar Terintegrasi</h3>
                        <p>Lihat CV, portofolio, dan riwayat lamaran dalam satu tampilan.</p>
                    </div>
                    <div class="feature-item fade-up"><i class="fas fa-clipboard-list"></i>
                        <h3>Status Lamaran Real-time</h3>
                        <p>Proses, panggilan interview, diterima/ditolak update otomatis.</p>
                    </div>
                    <div class="feature-item fade-up"><i class="fas fa-building"></i>
                        <h3>Branding Perusahaan</h3>
                        <p>Tampilkan visi, budaya, dan lowongan Anda untuk menarik talenta tepat.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Tips Rekrutmen - 6 card tanpa tombol baca selengkapnya -->
        <section id="tips">
            <div class="container">
                <h2 class="section-title fade-up">Tips & Strategi Rekrutmen</h2>
                <p class="section-desc fade-up">Raih kandidat berkualitas dengan insight terbaru dari para ahli HR</p>
                <div class="tips-grid">
                    <div class="tips-card fade-up">
                        <div class="tips-content">
                            <div class="tips-icon"><i class="fas fa-lightbulb"></i></div>
                            <h3 class="tips-title">1. Optimasi Employer Branding</h3>
                            <p class="tips-desc">Bangun citra perusahaan yang positif untuk menarik talenta terbaik
                                secara organik.</p>
                        </div>
                    </div>
                    <div class="tips-card fade-up">
                        <div class="tips-content">
                            <div class="tips-icon"><i class="fas fa-file-alt"></i></div>
                            <h3 class="tips-title">2. Deskripsi Lowongan Yang Jelas</h3>
                            <p class="tips-desc">Hindari jargon rumit, fokus pada tanggung jawab dan peluang berkembang.
                            </p>
                        </div>
                    </div>
                    <div class="tips-card fade-up">
                        <div class="tips-content">
                            <div class="tips-icon"><i class="fas fa-mobile-alt"></i></div>
                            <h3 class="tips-title">3. Proses Lamaran Mobile Friendly</h3>
                            <p class="tips-desc">Pastikan kandidat dapat melamar dengan mudah dari perangkat seluler.
                            </p>
                        </div>
                    </div>
                    <div class="tips-card fade-up">
                        <div class="tips-content">
                            <div class="tips-icon"><i class="fas fa-comments"></i></div>
                            <h3 class="tips-title">4. Komunikasi Cepat & Transparan</h3>
                            <p class="tips-desc">Berikan notifikasi status lamaran untuk meningkatkan pengalaman
                                kandidat.</p>
                        </div>
                    </div>
                    <div class="tips-card fade-up">
                        <div class="tips-content">
                            <div class="tips-icon"><i class="fas fa-chart-line"></i></div>
                            <h3 class="tips-title">5. Gunakan Data dalam Rekrutmen</h3>
                            <p class="tips-desc">Analisis sumber pelamar terbaik dan waktu pengisian posisi untuk
                                efisiensi.</p>
                        </div>
                    </div>
                    <div class="tips-card fade-up">
                        <div class="tips-content">
                            <div class="tips-icon"><i class="fas fa-users"></i></div>
                            <h3 class="tips-title">6. Asah Skill Wawancara Tim HR</h3>
                            <p class="tips-desc">Latih teknik wawancara terstruktur untuk menilai kompetensi secara
                                objektif.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Penutup -->
        <div class="container">
            <div class="cta-section fade-up">
                <h2 class="cta-title">Mulai Rekrut Kandidat Sekarang</h2>
                <p style="font-size:1.08rem; margin-bottom: 32px; color:#2c3e66;">Bergabunglah dengan 500+ perusahaan
                    yang telah sukses merekrut bersama LinkUp.</p>
                <div style="display:flex; gap:20px; justify-content:center; flex-wrap:wrap;">
                    <button class="btn-primary" onclick="scrollToSection('keunggulan')"><i
                            class="fas fa-plus-circle"></i> Pasang Lowongan</button>
                    <button class="btn-secondary" style="background:white;"
                        onclick="window.location.href='perusahaan/register.php'"><i class="fas fa-user-plus"></i> Daftar
                        Perusahaan</button>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h4>LinkUp</h4>
                    <a onclick="scrollToSection('hero')">Beranda</a>
                </div>
                <div class="footer-col">
                    <h4>Layanan</h4>
                    <a onclick="scrollToSection('keunggulan')">Pasang Lowongan</a>
                    <a onclick="scrollToSection('fitur')">Fitur Unggulan</a>
                    <a onclick="scrollToSection('tips')">Tips Rekrutmen</a>
                    <a href="#">Cara Kerja</a>
                </div>
                <div class="footer-col">
                    <h4>Dukungan</h4>
                    <a href="#">Panduan Rekrutmen</a>
                    <div class="social-icons" style="margin-top:12px;">
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                    </div>
                </div>
                <div class="footer-col">
                    <h4>Legal</h4>
                    <a href="#">Kebijakan Privasi</a>
                    <a href="#">Syarat & Ketentuan</a>
                    <a href="#">Keamanan Data</a>
                </div>
            </div>
            <div class="copyright">© 2025 LinkUp. Platform rekrutmen modern untuk perusahaan di Indonesia. All rights
                reserved.</div>
        </div>
    </footer>

    <script>
        // Mobile Toggle
        const mobileToggle = document.getElementById('mobileToggle');
        const navLinks = document.getElementById('navLinks');
        if (mobileToggle) {
            mobileToggle.addEventListener('click', () => {
                navLinks.classList.toggle('show');
            });
        }

        // Smooth scroll ke section dengan efek hover tetap
        function scrollToSection(sectionId) {
            const section = document.getElementById(sectionId);
            if (section) {
                section.scrollIntoView({ behavior: 'smooth', block: 'start' });
                // Tutup mobile menu jika terbuka
                if (navLinks.classList.contains('show')) {
                    navLinks.classList.remove('show');
                }
            }
        }

        // Intersection Observer Fade-up
        const fadeElements = document.querySelectorAll('.fade-up');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('appear');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: "0px 0px -30px 0px" });
        fadeElements.forEach(el => observer.observe(el));

        // efek hover tetap untuk semua tombol navigasi (sudah di CSS)
        // tambahan event listener untuk link footer yang pakai scrollToSection
        document.querySelectorAll('a[onclick*="scrollToSection"]').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const funcCall = link.getAttribute('onclick');
                if (funcCall) eval(funcCall);
            });
        });
    </script>
</body>

</html>