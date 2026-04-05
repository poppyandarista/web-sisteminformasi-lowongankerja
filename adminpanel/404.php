<?php
// Error Handler untuk semua jenis HTTP error
// Redirect dari .htaccess untuk semua error (401, 403, 404, 500, 503, 400)

// Set header untuk menghindari caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

// Fungsi untuk mendapatkan kode error HTTP
function get_http_status_code()
{
  // Prioritaskan REDIRECT_STATUS dari Apache
  if (isset($_SERVER['REDIRECT_STATUS']) && !empty($_SERVER['REDIRECT_STATUS'])) {
    return (int) $_SERVER['REDIRECT_STATUS'];
  }

  // Coba http_response_code jika tersedia
  if (function_exists('http_response_code')) {
    $code = http_response_code();
    if ($code !== false && $code !== 200) {
      return $code;
    }
  }

  // Cek REQUEST_URI untuk error 404
  if (isset($_SERVER['REQUEST_URI']) && !file_exists(__DIR__ . $_SERVER['REQUEST_URI'])) {
    return 404;
  }

  // Default ke 404
  return 404;
}

// Ambil kode error
$error_code = get_http_status_code();

// Mapping error code ke gambar dan deskripsi
$error_data = [
  401 => [
    'image' => '401.png',
    'title' => '401 Unauthorized',
    'message' => 'Anda tidak memiliki izin untuk mengakses halaman ini!'
  ],
  403 => [
    'image' => '403.png',
    'title' => '403 Forbidden',
    'message' => 'Akses ke halaman ini ditolak!'
  ],
  404 => [
    'image' => '404.png',
    'title' => '404 Not Found',
    'message' => 'Kami tidak dapat menemukan halaman yang Anda cari!'
  ],
  500 => [
    'image' => '500.png',
    'title' => '500 Server Error',
    'message' => 'Terjadi kesalahan pada server kami!'
  ],
  503 => [
    'image' => '503.png',
    'title' => '503 Service Unavailable',
    'message' => 'Layanan sedang tidak tersedia, silakan coba lagi nanti!'
  ],
  400 => [
    'image' => '400.png',
    'title' => '400 Bad Request',
    'message' => 'Permintaan Anda tidak valid!'
  ]
];

// Default ke 404 jika error code tidak ada di mapping
$current_error = $error_data[$error_code] ?? $error_data[404];

// Set header HTTP yang benar (hanya jika ini adalah error asli)
if (!isset($_GET['from_error'])) {
  http_response_code($error_code);
}
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport"
    content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>
    <?php echo htmlspecialchars($current_error['title']); ?> | LinkUp
  </title>
  <link rel="icon" href="/adminpanel/favicon.ico">
  <link href="/adminpanel/style.css" rel="stylesheet">
</head>

<body
  x-data="{ page: 'page404', 'loaded': true, 'darkMode': false, 'stickyMenu': false, 'sidebarToggle': false, 'scrollTop': false }"
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
  <div class="relative z-1 flex min-h-screen flex-col items-center justify-center overflow-hidden p-6">
    <!-- ===== Common Grid Shape Start ===== -->
    <div class="absolute right-0 top-0 -z-1 w-full max-w-[250px] xl:max-w-[450px]">
      <img src="/adminpanel/src/images/shape/grid-01.svg" alt="grid" />
    </div>
    <div class="absolute bottom-0 left-0 -z-1 w-full max-w-[250px] rotate-180 xl:max-w-[450px]">
      <img src="/adminpanel/src/images/shape/grid-01.svg" alt="grid" />
    </div>

    <!-- ===== Common Grid Shape End ===== -->

    <!-- Centered Content -->
    <div class="mx-auto w-full max-w-[242px] text-center sm:max-w-[472px]">
      <h1 class="mb-8 text-title-md font-bold text-gray-800 dark:text-white/90 xl:text-title-2xl">
        ERROR
      </h1>

      <img src="/adminpanel/src/images/error/<?php echo htmlspecialchars($current_error['image']); ?>"
        alt="<?php echo htmlspecialchars($current_error['title']); ?>" class="dark:hidden" />
      <img src="/adminpanel/src/images/error/<?php echo htmlspecialchars($current_error['image']); ?>"
        alt="<?php echo htmlspecialchars($current_error['title']); ?>" class="hidden dark:block" />

      <p class="mb-6 mt-10 text-base text-gray-700 dark:text-gray-400 sm:text-lg">
        <?php echo htmlspecialchars($current_error['message']); ?>
      </p>

      <a href="/adminpanel/index.php"
        class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-3.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
        Kembali ke Beranda
      </a>
    </div>
    <!-- Footer -->
    <p class="absolute bottom-6 left-1/2 -translate-x-1/2 text-center text-sm text-gray-500 dark:text-gray-400">
      &copy; <span id="year"></span> - LinkUp
    </p>
  </div>

  <!-- ===== Page Wrapper End ===== -->
  <script defer src="/adminpanel/bundle.js"></script>
</body>

</html>