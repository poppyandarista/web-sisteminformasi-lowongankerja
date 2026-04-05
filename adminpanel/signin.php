<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['id_admin'])) {
  header("Location: index.php");
  exit();
}

require_once 'koneksi.php';
$db = new database();

$error = "";

if (isset($_POST['login'])) {
  $username = $_POST['nama_admin'];
  $password = $_POST['password_admin'];

  // Validasi input
  if (empty($username)) {
    echo json_encode([
      'success' => false,
      'message' => 'Username tidak boleh kosong!',
      'title' => 'Error'
    ]);
    exit();
  }

  if (empty($password)) {
    echo json_encode([
      'success' => false,
      'message' => 'Password tidak boleh kosong!',
      'title' => 'Error'
    ]);
    exit();
  }

  // Panggil fungsi login_admin
  $data_user = $db->login_admin($username, $password);

  if ($data_user) {
    // Set session
    $_SESSION['id_admin'] = $data_user['id_admin'];
    $_SESSION['nama_admin'] = $data_user['nama_admin'];
    $_SESSION['email_admin'] = $data_user['email_admin'];
    $_SESSION['foto_admin'] = $data_user['foto_admin'];

    echo json_encode([
      'success' => true,
      'message' => 'Login berhasil! Mengalihkan ke dashboard...',
      'title' => 'Login Berhasil'
    ]);
    exit();
  } else {
    // Cek apakah username ada di database
    $check_username = $db->koneksi->prepare("SELECT id_admin FROM admin WHERE nama_admin = ?");
    $check_username->bind_param("s", $username);
    $check_username->execute();
    $result = $check_username->get_result();

    if ($result->num_rows === 0) {
      echo json_encode([
        'success' => false,
        'message' => 'Username anda tidak terdaftar sebagai admin!',
        'title' => 'Error Login'
      ]);
    } else {
      echo json_encode([
        'success' => false,
        'message' => 'Username atau password salah!',
        'title' => 'Error Login'
      ]);
    }
    $check_username->close();
    exit();
  }
}
?>


<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport"
    content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>Sign In | LinkUp</title>
  <link rel="icon" href="src/images/logo/favicon.png">
  <link href="style.css" rel="stylesheet">
  <style>
    /* Alert System Styles - Sesuai Template */
    .alert-container {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 9999;
      display: flex;
      flex-direction: column;
      gap: 10px;
      max-width: 400px;
    }

    .alert-item {
      border-radius: 0.75rem;
      padding: 1rem;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      border: 1px solid;
      display: flex;
      align-items: flex-start;
      gap: 0.75rem;
      transform: translateX(100%);
      opacity: 0;
      transition: all 0.3s ease-out;
    }

    .alert-item.show {
      transform: translateX(0);
      opacity: 1;
    }

    .alert-item.hide {
      transform: translateX(100%);
      opacity: 0;
    }

    .alert-item.success {
      border-color: #10b981;
      background-color: #f0fdf4;
      color: #065f46;
    }

    .alert-item.error {
      border-color: #ef4444;
      background-color: #fef2f2;
      color: #991b1b;
    }

    .alert-item.warning {
      border-color: #f59e0b;
      background-color: #fffbeb;
      color: #92400e;
    }

    .alert-item.info {
      border-color: #3b82f6;
      background-color: #eff6ff;
      color: #1e40af;
    }

    /* Dark mode support */
    .dark .alert-item {
      background: #1f2937;
      color: #f9fafb;
    }

    .dark .alert-item.success {
      background: rgba(16, 185, 129, 0.15);
      border-color: #10b981;
      color: #34d399;
    }

    .dark .alert-item.error {
      background: rgba(239, 68, 68, 0.15);
      border-color: #ef4444;
      color: #f87171;
    }

    .dark .alert-item.warning {
      background: rgba(245, 158, 11, 0.15);
      border-color: #f59e0b;
      color: #fbbf24;
    }

    .dark .alert-item.info {
      background: rgba(59, 130, 246, 0.15);
      border-color: #3b82f6;
      color: #60a5fa;
    }

    .alert-icon {
      flex-shrink: 0;
      width: 20px;
      height: 20px;
    }

    .alert-content {
      flex: 1;
    }

    .alert-title {
      font-weight: 600;
      margin-bottom: 4px;
      font-size: 14px;
    }

    .alert-message {
      font-size: 13px;
      line-height: 1.4;
    }

    .alert-close {
      background: none;
      border: none;
      cursor: pointer;
      padding: 0;
      margin-left: 8px;
      opacity: 0.7;
      transition: opacity 0.2s;
    }

    .alert-close:hover {
      opacity: 1;
    }

    /* Dark mode support */
    .dark .alert-item {
      background: #1f2937;
      color: #f9fafb;
    }

    .dark .alert-item.success {
      background: #064e3b;
      border-left-color: #10b981;
    }

    .dark .alert-item.error {
      background: #7f1d1d;
      border-left-color: #ef4444;
    }

    .dark .alert-item.warning {
      background: #78350f;
      border-left-color: #f59e0b;
    }

    .dark .alert-item.info {
      background: #1e3a8a;
      border-left-color: #3b82f6;
    }
  </style>
</head>

<body
  x-data="{ page: 'comingSoon', 'loaded': true, 'darkMode': false, 'stickyMenu': false, 'sidebarToggle': false, 'scrollTop': false }"
  x-init="
         darkMode = JSON.parse(localStorage.getItem('darkMode'));
         $watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)))"
  :class="{'dark bg-gray-900': darkMode === true}">
  <!-- ===== Preloader Start ===== -->
  <div x-show="loaded"
    x-init="window.addEventListener('DOMContentLoaded', () => {setTimeout(() => loaded = false, 500)})"
    class="fixed left-0 top-0 z-999999 flex h-screen w-screen items-center justify-center bg-white dark:bg-black">
    <div class="h-16 w-16 animate-spin rounded-full border-4 border-solid border-brand-500 border-t-transparent"></div>
  </div>

  <!-- ===== Preloader End ===== -->

  <!-- ===== Page Wrapper Start ===== -->
  <div class="relative p-6 bg-white z-1 dark:bg-gray-900 sm:p-0">
    <div class="relative flex flex-col justify-center w-full h-screen dark:bg-gray-900 sm:p-0 lg:flex-row">
      <!-- Form -->
      <div class="flex flex-col flex-1 w-full lg:w-1/2">
        <div class="flex flex-col justify-center flex-1 w-full max-w-md mx-auto">
          <div>
            <div class="mb-5 sm:mb-8">
              <h1 class="mb-2 font-semibold text-gray-800 text-title-sm dark:text-white/90 sm:text-title-md">
                Sign In
              </h1>
              <p class="text-sm text-gray-500 dark:text-gray-400">
                Enter your username and password to sign in!
              </p>
            </div>
            <div>
              <form id="loginForm" action="" method="POST">
                <div class="space-y-5">
                  <!-- Username -->
                  <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                      Username<span class="text-error-500">*</span>
                    </label>
                    <input type="text" id="username" name="nama_admin" placeholder="Username"
                      class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                  </div>
                  <!-- Password -->
                  <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                      Password<span class="text-error-500">*</span>
                    </label>
                    <div x-data="{ showPassword: false }" class="relative">
                      <input :type="showPassword ? 'text' : 'password'" name="password_admin"
                        placeholder="Enter your password"
                        class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-4 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                      <span @click="showPassword = !showPassword"
                        class="absolute z-30 text-gray-500 -translate-y-1/2 cursor-pointer right-4 top-1/2 dark:text-gray-400">
                        <svg x-show="!showPassword" class="fill-current" width="20" height="20" viewBox="0 0 20 20"
                          fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M10.0002 13.8619C7.23361 13.8619 4.86803 12.1372 3.92328 9.70241C4.86804 7.26761 7.23361 5.54297 10.0002 5.54297C12.7667 5.54297 15.1323 7.26762 16.0771 9.70243C15.1323 12.1372 12.7667 13.8619 10.0002 13.8619ZM10.0002 4.04297C6.48191 4.04297 3.49489 6.30917 2.4155 9.4593C2.3615 9.61687 2.3615 9.78794 2.41549 9.94552C3.49488 13.0957 6.48191 15.3619 10.0002 15.3619C13.5184 15.3619 16.5055 13.0957 17.5849 9.94555C17.6389 9.78797 17.6389 9.6169 17.5849 9.45932C16.5055 6.30919 13.5184 4.04297 10.0002 4.04297ZM9.99151 7.84413C8.96527 7.84413 8.13333 8.67606 8.13333 9.70231C8.13333 10.7286 8.96527 11.5605 9.99151 11.5605H10.0064C11.0326 11.5605 11.8646 10.7286 11.8646 9.70231C11.8646 8.67606 11.0326 7.84413 10.0064 7.84413H9.99151Z"
                            fill="#98A2B3" />
                        </svg>
                        <svg x-show="showPassword" class="fill-current" width="20" height="20" viewBox="0 0 20 20"
                          fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M4.63803 3.57709C4.34513 3.2842 3.87026 3.2842 3.57737 3.57709C3.28447 3.86999 3.28447 4.34486 3.57737 4.63775L4.85323 5.91362C3.74609 6.84199 2.89363 8.06395 2.4155 9.45936C2.3615 9.61694 2.3615 9.78801 2.41549 9.94558C3.49488 13.0957 6.48191 15.3619 10.0002 15.3619C11.255 15.3619 12.4422 15.0737 13.4994 14.5598L15.3625 16.4229C15.6554 16.7158 16.1302 16.7158 16.4231 16.4229C16.716 16.13 16.716 15.6551 16.4231 15.3622L4.63803 3.57709ZM12.3608 13.4212L10.4475 11.5079C10.3061 11.5423 10.1584 11.5606 10.0064 11.5606H9.99151C8.96527 11.5606 8.13333 10.7286 8.13333 9.70237C8.13333 9.5461 8.15262 9.39434 8.18895 9.24933L5.91885 6.97923C5.03505 7.69015 4.34057 8.62704 3.92328 9.70247C4.86803 12.1373 7.23361 13.8619 10.0002 13.8619C10.8326 13.8619 11.6287 13.7058 12.3608 13.4212ZM16.0771 9.70249C15.7843 10.4569 15.3552 11.1432 14.8199 11.7311L15.8813 12.7925C16.6329 11.9813 17.2187 11.0143 17.5849 9.94561C17.6389 9.78803 17.6389 9.61696 17.5849 9.45938C16.5055 6.30925 13.5184 4.04303 10.0002 4.04303C9.13525 4.04303 8.30244 4.17999 7.52218 4.43338L8.75139 5.66259C9.1556 5.58413 9.57311 5.54303 10.0002 5.54303C12.7667 5.54303 15.1323 7.26768 16.0771 9.70249Z"
                            fill="#98A2B3" />
                        </svg>
                      </span>
                    </div>
                  </div>
                  <!-- Checkbox -->
                  <!-- Button -->
                  <div>
                    <button type="submit" name="login"
                      class="flex items-center justify-center w-full px-4 py-3 text-sm font-medium text-white transition rounded-lg bg-brand-500 shadow-theme-xs hover:bg-brand-600">
                      Sign In
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      <div class="relative items-center hidden w-full h-full bg-brand-950 dark:bg-white/5 lg:grid lg:w-1/2">
        <div class="flex items-center justify-center z-1">
          <!-- ===== Common Grid Shape Start ===== -->
          <div class="absolute right-0 top-0 -z-1 w-full max-w-[250px] xl:max-w-[450px]">
            <img src="src/images/shape/grid-01.svg" alt="grid" />
          </div>
          <div class="absolute bottom-0 left-0 -z-1 w-full max-w-[250px] rotate-180 xl:max-w-[450px]">
            <img src="src/images/shape/grid-01.svg" alt="grid" />
          </div>

          <div class="flex flex-col items-center max-w-xs">
            <a href="index.html" class="block mb-4">
              <img src="src/images/logo/logo2.png" alt="Logo" />
            </a>
            <p class="text-center text-gray-400 dark:text-white/60">
              Jelajahi ribuan perusahaan terbaik dan temukan budaya kerja yang cocok untuk karir Anda
            </p>
          </div>
        </div>
      </div>
      <!-- Toggler -->
      <div class="fixed z-50 hidden bottom-6 right-6 sm:block">
        <button
          class="inline-flex items-center justify-center text-white transition-colors rounded-full size-14 bg-brand-500 hover:bg-brand-600"
          @click.prevent="darkMode = !darkMode">
          <svg class="hidden fill-current dark:block" width="20" height="20" viewBox="0 0 20 20" fill="none"
            xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd"
              d="M9.99998 1.5415C10.4142 1.5415 10.75 1.87729 10.75 2.2915V3.5415C10.75 3.95572 10.4142 4.2915 9.99998 4.2915C9.58577 4.2915 9.24998 3.95572 9.24998 3.5415V2.2915C9.24998 1.87729 9.58577 1.5415 9.99998 1.5415ZM10.0009 6.79327C8.22978 6.79327 6.79402 8.22904 6.79402 10.0001C6.79402 11.7712 8.22978 13.207 10.0009 13.207C11.772 13.207 13.2078 11.7712 13.2078 10.0001C13.2078 8.22904 11.772 6.79327 10.0009 6.79327ZM5.29402 10.0001C5.29402 7.40061 7.40135 5.29327 10.0009 5.29327C12.6004 5.29327 14.7078 7.40061 14.7078 10.0001C14.7078 12.5997 12.6004 14.707 10.0009 14.707C7.40135 14.707 5.29402 12.5997 5.29402 10.0001ZM15.9813 5.08035C16.2742 4.78746 16.2742 4.31258 15.9813 4.01969C15.6884 3.7268 15.2135 3.7268 14.9207 4.01969L14.0368 4.90357C13.7439 5.19647 13.7439 5.67134 14.0368 5.96423C14.3297 6.25713 14.8045 6.25713 15.0974 5.96423L15.9813 5.08035ZM18.4577 10.0001C18.4577 10.4143 18.1219 10.7501 17.7077 10.7501H16.4577C16.0435 10.7501 15.7077 10.4143 15.7077 10.0001C15.7077 9.58592 16.0435 9.25013 16.4577 9.25013H17.7077C18.1219 9.25013 18.4577 9.58592 18.4577 10.0001ZM14.9207 15.9806C15.2135 16.2735 15.6884 16.2735 15.9813 15.9806C16.2742 15.6877 16.2742 15.2128 15.9813 14.9199L15.0974 14.036C14.8045 13.7431 14.3297 13.7431 14.0368 14.036C13.7439 14.3289 13.7439 14.8038 14.0368 15.0967L14.9207 15.9806ZM9.99998 15.7088C10.4142 15.7088 10.75 16.0445 10.75 16.4588V17.7088C10.75 18.123 10.4142 18.4588 9.99998 18.4588C9.58577 18.4588 9.24998 18.123 9.24998 17.7088V16.4588C9.24998 16.0445 9.58577 15.7088 9.99998 15.7088ZM5.96356 15.0972C6.25646 14.8043 6.25646 14.3295 5.96356 14.0366C5.67067 13.7437 5.1958 13.7437 4.9029 14.0366L4.01902 14.9204C3.72613 15.2133 3.72613 15.6882 4.01902 15.9811C4.31191 16.274 4.78679 16.274 5.07968 15.9811L5.96356 15.0972ZM4.29224 10.0001C4.29224 10.4143 3.95645 10.7501 3.54224 10.7501H2.29224C1.87802 10.7501 1.54224 10.4143 1.54224 10.0001C1.54224 9.58592 1.87802 9.25013 2.29224 9.25013H3.54224C3.95645 9.25013 4.29224 9.58592 4.29224 10.0001ZM4.9029 5.9637C5.1958 6.25659 5.67067 6.25659 5.96356 5.9637C6.25646 5.6708 6.25646 5.19593 5.96356 4.90303L5.07968 4.01915C4.78679 3.72626 4.31191 3.72626 4.01902 4.01915C3.72613 4.31204 3.72613 4.78692 4.01902 5.07981L4.9029 5.9637Z"
              fill="" />
          </svg>
          <svg class="fill-current dark:hidden" width="20" height="20" viewBox="0 0 20 20" fill="none"
            xmlns="http://www.w3.org/2000/svg">
            <path
              d="M17.4547 11.97L18.1799 12.1611C18.265 11.8383 18.1265 11.4982 17.8401 11.3266C17.5538 11.1551 17.1885 11.1934 16.944 11.4207L17.4547 11.97ZM8.0306 2.5459L8.57989 3.05657C8.80718 2.81209 8.84554 2.44682 8.67398 2.16046C8.50243 1.8741 8.16227 1.73559 7.83948 1.82066L8.0306 2.5459ZM12.9154 13.0035C9.64678 13.0035 6.99707 10.3538 6.99707 7.08524H5.49707C5.49707 11.1823 8.81835 14.5035 12.9154 14.5035V13.0035ZM16.944 11.4207C15.8869 12.4035 14.4721 13.0035 12.9154 13.0035V14.5035C14.8657 14.5035 16.6418 13.7499 17.9654 12.5193L16.944 11.4207ZM16.7295 11.7789C15.9437 14.7607 13.2277 16.9586 10.0003 16.9586V18.4586C13.9257 18.4586 17.2249 15.7853 18.1799 12.1611L16.7295 11.7789ZM10.0003 16.9586C6.15734 16.9586 3.04199 13.8433 3.04199 10.0003H1.54199C1.54199 14.6717 5.32892 18.4586 10.0003 18.4586V16.9586ZM3.04199 10.0003C3.04199 6.77289 5.23988 4.05695 8.22173 3.27114L7.83948 1.82066C4.21532 2.77574 1.54199 6.07486 1.54199 10.0003H3.04199ZM6.99707 7.08524C6.99707 5.52854 7.5971 4.11366 8.57989 3.05657L7.48132 2.03522C6.25073 3.35885 5.49707 5.13487 5.49707 7.08524H6.99707Z"
              fill="" />
          </svg>
        </button>
      </div>
    </div>
  </div>
  <!-- ===== Page Wrapper End ===== -->
  <!-- Alert Container -->
  <div id="alertContainer" class="alert-container"></div>

  <script defer src="bundle.js"></script>
  <script>
    // Alert System Functions - Sesuai Template
    function showNotification(type, message, title, showRefreshButton = false) {
      const alertContainer = document.getElementById('alertContainer');

      // Create alert element
      const alertElement = document.createElement('div');
      alertElement.className = `alert-item ${type}`;

      // Get icon based on type - Sesuai Template
      let iconSvg = '';
      let iconColor = '';

      switch (type) {
        case 'success':
          iconSvg = `<svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M3.70186 12.0001C3.70186 7.41711 7.41711 3.70186 12.0001 3.70186C16.5831 3.70186 20.2984 7.41711 20.2984 12.0001C20.2984 16.5831 16.5831 20.2984 12.0001 20.2984C7.41711 20.2984 3.70186 16.5831 3.70186 12.0001ZM12.0001 1.90186C6.423 1.90186 1.90186 6.423 1.90186 12.0001C1.90186 17.5772 6.423 22.0984 12.0001 22.0984C17.5772 22.0984 22.0984 17.5772 22.0984 12.0001C22.0984 6.423 17.5772 1.90186 12.0001 1.90186ZM15.6197 10.7395C15.9712 10.388 15.9712 9.81819 15.6197 9.46672C15.2683 9.11525 14.6984 9.11525 14.347 9.46672L11.1894 12.6243L9.6533 11.0883C9.30183 10.7368 8.73198 10.7368 8.38051 11.0883C8.02904 11.4397 8.02904 12.0096 8.38051 12.3611L10.553 14.5335C10.7217 14.7023 10.9507 14.7971 11.1894 14.7971C11.428 14.7971 11.657 14.7023 11.8257 14.5335L15.6197 10.7395Z" fill="" />
          </svg>`;
          iconColor = 'text-success-500';
          break;
        case 'error':
          iconSvg = `<svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M20.3499 12.0004C20.3499 16.612 16.6115 20.3504 11.9999 20.3504C7.38832 20.3504 3.6499 16.612 3.6499 12.0004C3.6499 7.38881 7.38833 3.65039 11.9999 3.65039C16.6115 3.65039 20.3499 7.38881 20.3499 12.0004ZM11.9999 22.1504C17.6056 22.1504 22.1499 17.6061 22.1499 12.0004C22.1499 6.3947 17.6056 1.85039 11.9999 1.85039C6.39421 1.85039 1.8499 6.3947 1.8499 12.0004C1.8499 17.6061 6.39421 22.1504 11.9999 22.1504ZM13.0008 16.4753C13.0008 15.923 12.5531 15.4753 12.0008 15.4753L11.9998 15.4753C11.4475 15.4753 10.9998 15.923 10.9998 16.4753C10.9998 17.0276 11.4475 17.4753 11.9998 17.4753L12.0008 17.4753C12.5531 17.4753 13.0008 17.0276 13.0008 16.4753ZM11.9998 6.62898C12.414 6.62898 12.7498 6.96476 12.7498 7.37898L12.7498 13.0555C12.7498 13.4697 12.414 13.8055 11.9998 13.8055C11.5856 13.8055 11.2498 13.4697 11.2498 13.0555L11.2498 7.37898C11.2498 6.96476 11.5856 6.62898 11.9998 6.62898Z" fill="#F04438" />
          </svg>`;
          iconColor = 'text-error-500';
          break;
        case 'warning':
          iconSvg = `<svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M3.6501 12.0004C3.6501 7.38852 7.38852 3.6501 12.0001 3.6501C16.6117 3.6501 20.3501 7.38852 20.3501 12.0001C20.3501 16.6117 16.6117 20.3501 12.0001 20.3501C7.38852 20.3501 3.6501 16.6117 3.6501 12.0001ZM12.0001 1.8501C6.39441 1.8501 1.8501 6.39441 1.8501 12.0001C1.8501 17.6058 6.39441 22.1501 12.0001 22.1501C17.6058 22.1501 22.1501 17.6058 22.1501 12.0001C22.1501 6.39441 17.6058 1.8501 12.0001 1.8501ZM10.9992 7.52517C10.9992 8.07746 11.4469 8.52517 11.9992 8.52517H12.0002C12.5525 8.52517 13.0002 8.07746 13.0002 7.52517C13.0002 6.97289 12.5525 6.52517 12.0002 6.52517H11.9992C11.4469 6.52517 10.9992 6.97289 10.9992 7.52517ZM12.0002 17.3715C11.586 17.3715 11.2502 17.0357 11.2502 16.6215V10.945C11.2502 10.5308 11.586 10.195 12.0002 10.195C12.4144 10.195 12.7502 10.5308 12.7502 10.945V16.6215C12.7502 17.0357 12.4144 17.3715 12.0002 17.3715Z" fill="" />
          </svg>`;
          iconColor = 'text-warning-500 dark:text-orange-400';
          break;
        case 'info':
          iconSvg = `<svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M3.6501 11.9996C3.6501 7.38803 7.38852 3.64961 12.0001 3.64961C16.6117 3.64961 20.3501 7.38803 20.3501 11.9996C20.3501 16.6112 16.6117 20.3496 12.0001 20.3496C7.38852 20.3496 3.6501 16.6112 3.6501 11.9996ZM12.0001 1.84961C6.39441 1.84961 1.8501 6.39392 1.8501 11.9996C1.8501 17.6053 6.39441 22.1496 12.0001 22.1496C17.6058 22.1496 22.1501 17.6053 22.1501 11.9996C22.1501 6.39392 17.6058 1.84961 12.0001 1.84961ZM10.9992 7.52468C10.9992 8.07697 11.4469 8.52468 11.9992 8.52468H12.0002C12.5525 8.52468 13.0002 8.07697 13.0002 7.52468C13.0002 6.9724 12.5525 6.52468 12.0002 6.52468H11.9992C11.4469 6.52468 10.9992 6.9724 10.9992 7.52468ZM12.0002 17.371C11.586 17.371 11.2502 17.0352 11.2502 16.621V10.9445C11.2502 10.5303 11.586 10.1945 12.0002 10.1945C12.4144 10.1945 12.7502 10.5303 12.7502 10.9445V16.621C12.7502 17.0352 12.4144 17.371 12.0002 17.371Z" fill="" />
          </svg>`;
          iconColor = 'text-blue-light-500';
          break;
        default:
          iconSvg = `<svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M3.6501 11.9996C3.6501 7.38803 7.38852 3.64961 12.0001 3.64961C16.6117 3.64961 20.3501 7.38803 20.3501 11.9996C20.3501 16.6112 16.6117 20.3496 12.0001 20.3496C7.38852 20.3496 3.6501 16.6112 3.6501 11.9996ZM12.0001 1.84961C6.39441 1.84961 1.8501 6.39392 1.8501 11.9996C1.8501 17.6053 6.39441 22.1496 12.0001 22.1496C17.6058 22.1496 22.1501 17.6053 22.1501 11.9996C22.1501 6.39392 17.6058 1.84961 12.0001 1.84961ZM10.9992 7.52468C10.9992 8.07697 11.4469 8.52468 11.9992 8.52468H12.0002C12.5525 8.52468 13.0002 8.07697 13.0002 7.52468C13.0002 6.9724 12.5525 6.52468 12.0002 6.52468H11.9992C11.4469 6.52468 10.9992 6.9724 10.9992 7.52468ZM12.0002 17.371C11.586 17.371 11.2502 17.0352 11.2502 16.621V10.9445C11.2502 10.5303 11.586 10.1945 12.0002 10.1945C12.4144 10.1945 12.7502 10.5303 12.7502 10.9445V16.621C12.7502 17.0352 12.4144 17.371 12.0002 17.371Z" fill="" />
          </svg>`;
          iconColor = 'text-blue-light-500';
      }

      // Build alert HTML - Sesuai Template
      let alertHTML = `
        <div class="flex items-start gap-3">
          <div class="-mt-0.5 ${iconColor}">
            ${iconSvg}
          </div>
          <div class="flex-1">
            <h4 class="mb-1 text-sm font-semibold text-gray-800 dark:text-white/90">
              ${title}
            </h4>
            <p class="text-sm text-gray-500 dark:text-gray-400">
              ${message}
            </p>
          </div>
          <button onclick="closeAlert(this)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      `;

      if (showRefreshButton && type === 'success') {
        alertHTML += `
          <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
            <button onclick="location.reload()" class="w-full bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium py-2 px-4 rounded-lg transition-colors duration-200">
              <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
              </svg>
              Refresh Data
            </button>
          </div>
        `;
      }

      alertElement.innerHTML = alertHTML;

      // Add to container
      alertContainer.appendChild(alertElement);

      // Show animation
      setTimeout(() => {
        alertElement.classList.add('show');
      }, 10);

      // Auto hide after 5 seconds (kecuali ada tombol refresh)
      setTimeout(() => {
        closeAlert(alertElement.querySelector('button'));
      }, showRefreshButton ? 8000 : 5000);
    }

    function closeAlert(button) {
      const alertItem = button.closest('.alert-item');
      if (alertItem) {
        alertItem.classList.add('hide');
        setTimeout(() => {
          if (alertItem.parentNode) {
            alertItem.parentNode.removeChild(alertItem);
          }
        }, 300);
      }
    }

    // Form validation and submission
    document.getElementById('loginForm').addEventListener('submit', function (e) {
      e.preventDefault();

      const username = document.getElementById('username').value.trim();
      const password = document.querySelector('input[name="password_admin"]').value.trim();

      // Client-side validation
      if (!username) {
        showNotification('error', 'Username tidak boleh kosong!', 'Error');
        return;
      }

      if (!password) {
        showNotification('error', 'Password tidak boleh kosong!', 'Error');
        return;
      }

      // Create FormData
      const formData = new FormData();
      formData.append('nama_admin', username);
      formData.append('password_admin', password);
      formData.append('login', 'true');

      // Submit via fetch
      fetch('signin.php', {
        method: 'POST',
        body: formData
      })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            showNotification('success', data.message, data.title);

            // Redirect to dashboard after 2 seconds
            setTimeout(() => {
              window.location.href = 'index.php';
            }, 2000);
          } else {
            showNotification('error', data.message, data.title);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          showNotification('error', 'Terjadi kesalahan saat login', 'Error Server');
        });
    });
  </script>
</body>

</html>