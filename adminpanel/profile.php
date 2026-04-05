<?php
session_start();
require_once 'koneksi.php';
require_once 'auth.php';

$db = new database();

// Ambil data admin yang sedang login
if (isset($_SESSION['id_admin'])) {
  $admin_data = $db->get_admin_by_id($_SESSION['id_admin']);
  $current_admin = $admin_data;
} else {
  // Redirect ke login jika tidak ada session
  header("Location: signin.php");
  exit();
}

// Proses update profile
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
  if ($_POST['action'] === 'update_profile') {
    $nama = $_POST['nama_admin'] ?? '';
    $email = $_POST['email_admin'] ?? '';
    $password = !empty($_POST['password_admin']) ? $_POST['password_admin'] : null;

    // Handle upload foto
    $foto = null;
    if (isset($_FILES['foto_admin']) && $_FILES['foto_admin']['error'] === UPLOAD_ERR_OK) {
      $upload_dir = 'src/images/user/';
      $file_name = time() . '_' . basename($_FILES['foto_admin']['name']);
      $target_file = $upload_dir . $file_name;

      // Validasi file
      $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
      $file_type = mime_content_type($_FILES['foto_admin']['tmp_name']);

      if (in_array($file_type, $allowed_types)) {
        if (move_uploaded_file($_FILES['foto_admin']['tmp_name'], $target_file)) {
          $foto = $file_name;
        }
      }
    }

    // Update data admin
    if ($db->update_admin($_SESSION['id_admin'], $nama, $email, $password, $foto)) {
      // Update session
      $_SESSION['nama_admin'] = $nama;
      $_SESSION['email_admin'] = $email;
      if ($foto) {
        $_SESSION['foto_admin'] = $foto;
      }

      // Refresh data
      $admin_data = $db->get_admin_by_id($_SESSION['id_admin']);
      $current_admin = $admin_data;

      $success_message = "Profile berhasil diperbarui!";
    } else {
      $error_message = "Gagal memperbarui profile!";
    }
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
  <title>Profile | LinkUp</title>
  <link rel="icon" href="favicon.ico">
  <link href="style.css" rel="stylesheet">
</head>

<body
  x-data="{ page: 'profile', 'loaded': true, 'darkMode': false, 'stickyMenu': false, 'sidebarToggle': false, 'scrollTop': false, 'isProfileInfoModal': false, 'isProfileAddressModal': false }"
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
  <div class="flex h-screen overflow-hidden">
    <!-- ===== Sidebar Start ===== -->
    <?php include 'sidebar.php'; ?>

    <!-- ===== Sidebar End ===== -->

    <!-- ===== Content Area Start ===== -->
    <div class="relative flex flex-col flex-1 overflow-x-hidden overflow-y-auto">
      <!-- Small Device Overlay Start -->
      <div @click="sidebarToggle = false" :class="sidebarToggle ? 'block lg:hidden' : 'hidden'"
        class="fixed w-full h-screen z-9 bg-gray-900/50"></div>
      <!-- Small Device Overlay End -->

      <!-- ===== Header Start ===== -->
      <?php include("header.php") ?>
      <!-- ===== Header End ===== -->

      <!-- ===== Main Content Start ===== -->
      <main>
        <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
          <!-- Breadcrumb Start -->
          <div x-data="{ pageName: `Profile`}">
            <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
              <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90" x-text="pageName"></h2>

              <nav>
                <ol class="flex items-center gap-1.5">
                  <li>
                    <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400"
                      href="index.php">
                      Home
                      <svg class="stroke-current" width="17" height="16" viewBox="0 0 17 16" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M6.0765 12.667L10.2432 8.50033L6.0765 4.33366" stroke="" stroke-width="1.2"
                          stroke-linecap="round" stroke-linejoin="round" />
                      </svg>
                    </a>
                  </li>
                  <li class="text-sm text-gray-800 dark:text-white/90" x-text="pageName"></li>
                </ol>
              </nav>
            </div>
          </div>
          <!-- Breadcrumb End -->

          <!-- Notifikasi -->
          <?php if (isset($success_message)): ?>
            <div
              class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 dark:border-green-800 dark:bg-green-900/20">
              <div class="flex items-center gap-3">
                <svg class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                  viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-sm font-medium text-green-800 dark:text-green-200">
                  <?php echo $success_message; ?>
                </p>
              </div>
            </div>
          <?php endif; ?>

          <?php if (isset($error_message)): ?>
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-900/20">
              <div class="flex items-center gap-3">
                <svg class="h-5 w-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-sm font-medium text-red-800 dark:text-red-200">
                  <?php echo $error_message; ?>
                </p>
              </div>
            </div>
          <?php endif; ?>

          <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
            <h3 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white/90 lg:mb-7">
              Profile
            </h3>

            <div class="p-5 mb-6 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
              <div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">
                <div class="flex flex-col items-center w-full gap-6 xl:flex-row">
                  <div class="w-20 h-20 overflow-hidden border border-gray-200 rounded-full dark:border-gray-800">
                    <img src="src/images/user/<?php echo $current_admin['foto_admin'] ?? 'default.jpg'; ?>"
                      alt="user" />
                  </div>
                  <div class="order-3 xl:order-2">
                    <h4 class="mb-2 text-lg font-semibold text-center text-gray-800 dark:text-white/90 xl:text-left">
                      <?php echo htmlspecialchars($current_admin['nama_admin']); ?>
                    </h4>
                    <div class="flex flex-col items-center gap-1 text-center xl:flex-row xl:gap-3 xl:text-left">
                      <p class="text-sm text-gray-500 dark:text-gray-400">
                        ID: <?php echo 'A' . str_pad($current_admin['id_admin'], 4, '0', STR_PAD_LEFT); ?>
                      </p>
                      <div class="hidden h-3.5 w-px bg-gray-300 dark:bg-gray-700 xl:block"></div>
                      <p class="text-sm text-gray-500 dark:text-gray-400">
                        <?php echo date('d M Y', strtotime($current_admin['created_at'] ?? 'now')); ?>
                      </p>
                    </div>
                  </div>
                </div>

                <button @click="isProfileInfoModal = true"
                  class="flex w-full items-center justify-center gap-2 rounded-full border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200 lg:inline-flex lg:w-auto">
                  <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                      d="M15.0911 2.78206C14.2125 1.90338 12.7878 1.90338 11.9092 2.78206L4.57524 10.116C4.26682 10.4244 4.0547 10.8158 3.96468 11.2426L3.31231 14.3352C3.25997 14.5833 3.33653 14.841 3.51583 15.0203C3.69512 15.1996 3.95286 15.2761 4.20096 15.2238L7.29355 14.5714C7.72031 14.4814 8.11172 14.2693 8.42013 13.9609L15.7541 6.62695C16.6327 5.74827 16.6327 4.32365 15.7541 3.44497L15.0911 2.78206ZM12.9698 3.84272C13.2627 3.54982 13.7376 3.54982 14.0305 3.84272L14.6934 4.50563C14.9863 4.79852 14.9863 5.2734 14.6934 5.56629L14.044 6.21573L12.3204 4.49215L12.9698 3.84272ZM11.2597 5.55281L5.6359 11.1766C5.53309 11.2794 5.46238 11.4099 5.43238 11.5522L5.01758 13.5185L6.98394 13.1037C7.1262 13.0737 7.25666 13.003 7.35947 12.9002L12.9833 7.27639L11.2597 5.55281Z"
                      fill="" />
                  </svg>
                  Edit
                </button>
              </div>
            </div>

            <div class="p-5 mb-6 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
              <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                <div>
                  <h4 class="text-lg font-semibold text-gray-800 dark:text-white/90 lg:mb-6">
                    Informasi Pribadi
                  </h4>

                  <div class="grid grid-cols-1 gap-4 lg:grid-cols-2 lg:gap-7 2xl:gap-x-32">


                    <div>
                      <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">
                        Nama
                      </p>
                      <p class="text-sm font-medium text-gray-800 dark:text-white/90">
                        <?php echo htmlspecialchars($current_admin['nama_admin']); ?>
                      </p>
                    </div>

                    <div>
                      <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">
                        Email
                      </p>
                      <p class="text-sm font-medium text-gray-800 dark:text-white/90">
                        <?php echo htmlspecialchars($current_admin['email_admin']); ?>
                      </p>
                    </div>

                    <div>
                      <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">
                        Terdaftar pada
                      </p>
                      <p class="text-sm font-medium text-gray-800 dark:text-white/90">
                        <?php echo date('d F Y', strtotime($current_admin['created_at'] ?? 'now')); ?>
                      </p>
                    </div>

                  </div>
                </div>

                <button @click="isProfileInfoModal = true"
                  class="flex w-full items-center justify-center gap-2 rounded-full border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200 lg:inline-flex lg:w-auto">
                  <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                      d="M15.0911 2.78206C14.2125 1.90338 12.7878 1.90338 11.9092 2.78206L4.57524 10.116C4.26682 10.4244 4.0547 10.8158 3.96468 11.2426L3.31231 14.3352C3.25997 14.5833 3.33653 14.841 3.51583 15.0203C3.69512 15.1996 3.95286 15.2761 4.20096 15.2238L7.29355 14.5714C7.72031 14.4814 8.11172 14.2693 8.42013 13.9609L15.7541 6.62695C16.6327 5.74827 16.6327 4.32365 15.7541 3.44497L15.0911 2.78206ZM12.9698 3.84272C13.2627 3.54982 13.7376 3.54982 14.0305 3.84272L14.6934 4.50563C14.9863 4.79852 14.9863 5.2734 14.6934 5.56629L14.044 6.21573L12.3204 4.49215L12.9698 3.84272ZM11.2597 5.55281L5.6359 11.1766C5.53309 11.2794 5.46238 11.4099 5.43238 11.5522L5.01758 13.5185L6.98394 13.1037C7.1262 13.0737 7.25666 13.003 7.35947 12.9002L12.9833 7.27639L11.2597 5.55281Z"
                      fill="" />
                  </svg>
                  Edit
                </button>
              </div>
            </div>
          </div>
        </div>
      </main>
      <!-- ===== Main Content End ===== -->
    </div>
    <!-- ===== Content Area End ===== -->
  </div>
  <!-- ===== Page Wrapper End ===== -->

  <!-- BEGIN MODAL -->
  <div x-show="isProfileInfoModal" class="fixed inset-0 flex items-center justify-center p-5 overflow-y-auto z-99999">
    <div class="modal-close-btn fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]"></div>
    <div @click.outside="isProfileInfoModal = false"
      class="no-scrollbar relative w-full max-w-[700px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-11">
      <!-- close btn -->
      <button @click="isProfileInfoModal = false"
        class="transition-color absolute right-5 top-5 z-999 flex h-11 w-11 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-600 dark:bg-gray-700 dark:bg-white/[0.05] dark:text-gray-400 dark:hover:bg-white/[0.07] dark:hover:text-gray-300">
        <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none"
          xmlns="http://www.w3.org/2000/svg">
          <path fill-rule="evenodd" clip-rule="evenodd"
            d="M6.04289 16.5418C5.65237 16.9323 5.65237 17.5655 6.04289 17.956C6.43342 18.3465 7.06658 18.3465 7.45711 17.956L11.9987 13.4144L16.5408 17.9565C16.9313 18.347 17.5645 18.347 17.955 17.9565C18.3455 17.566 18.3455 16.9328 17.955 16.5423L13.4129 12.0002L17.955 7.45808C18.3455 7.06756 18.3455 6.43439 17.955 6.04387C17.5645 5.65335 16.9313 5.65335 16.5408 6.04387L11.9987 10.586L7.45711 6.04439C7.06658 5.65386 6.43342 5.65386 6.04289 6.04439C5.65237 6.43491 5.65237 7.06808 6.04289 7.4586L10.5845 12.0002L6.04289 16.5418Z"
            fill="" />
        </svg>
      </button>
      <div class="px-2 pr-14">
        <h4 class="mb-2 text-2xl font-semibold text-gray-800 dark:text-white/90">
          Edit Personal Information
        </h4>
        <p class="mb-6 text-sm text-gray-500 dark:text-gray-400 lg:mb-7">
          Update your details to keep your profile up-to-date.
        </p>
      </div>
      <form method="POST" enctype="multipart/form-data" class="flex flex-col">
        <input type="hidden" name="action" value="update_profile">
        <div class="custom-scrollbar h-[450px] overflow-y-auto px-2">
          <div>
            <h5 class="mb-5 text-lg font-medium text-gray-800 dark:text-white/90 lg:mb-6">
              Personal Information
            </h5>

            <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
              <div class="col-span-2 lg:col-span-1">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                  Nama Lengkap
                </label>
                <input type="text" name="nama_admin"
                  value="<?php echo htmlspecialchars($current_admin['nama_admin']); ?>"
                  class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                  required />
              </div>

              <div class="col-span-2 lg:col-span-1">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                  Email Address
                </label>
                <input type="email" name="email_admin"
                  value="<?php echo htmlspecialchars($current_admin['email_admin']); ?>"
                  class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                  required />
              </div>

              <div class="col-span-2 lg:col-span-1">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                  Password Baru (Kosongkan jika tidak ingin mengubah)
                </label>
                <input type="password" name="password_admin"
                  class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
              </div>

              <div class="col-span-2 lg:col-span-1">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                  Foto Profile
                </label>
                <input type="file" name="foto_admin" accept="image/*"
                  class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG, GIF. Max 2MB</p>
              </div>
            </div>
          </div>
        </div>
        <div class="flex items-center gap-3 px-2 mt-6 lg:justify-end">
          <button @click="isProfileInfoModal = false" type="button"
            class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] sm:w-auto">
            Tutup
          </button>
          <button type="submit"
            class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto">
            Simpan Perubahan
          </button>
        </div>
      </form>
    </div>
  </div>
  <div x-show="isProfileAddressModal"
    class="fixed inset-0 flex items-center justify-center p-5 overflow-y-auto z-99999">
    <div class="modal-close-btn fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]"></div>
    <div @click.outside="isProfileAddressModal = false"
      class="no-scrollbar relative flex w-full max-w-[700px] flex-col overflow-y-auto rounded-3xl bg-white p-6 dark:bg-gray-900 lg:p-11">
      <!-- close btn -->
      <button @click="isProfileAddressModal = false"
        class="transition-color absolute right-5 top-5 z-999 flex h-11 w-11 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-600 dark:bg-gray-700 dark:bg-white/[0.05] dark:text-gray-400 dark:hover:bg-white/[0.07] dark:hover:text-gray-300">
        <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none"
          xmlns="http://www.w3.org/2000/svg">
          <path fill-rule="evenodd" clip-rule="evenodd"
            d="M6.04289 16.5418C5.65237 16.9323 5.65237 17.5655 6.04289 17.956C6.43342 18.3465 7.06658 18.3465 7.45711 17.956L11.9987 13.4144L16.5408 17.9565C16.9313 18.347 17.5645 18.347 17.955 17.9565C18.3455 17.566 18.3455 16.9328 17.955 16.5423L13.4129 12.0002L17.955 7.45808C18.3455 7.06756 18.3455 6.43439 17.955 6.04387C17.5645 5.65335 16.9313 5.65335 16.5408 6.04387L11.9987 10.586L7.45711 6.04439C7.06658 5.65386 6.43342 5.65386 6.04289 6.04439C5.65237 6.43491 5.65237 7.06808 6.04289 7.4586L10.5845 12.0002L6.04289 16.5418Z"
            fill="" />
        </svg>
      </button>

      <div class="px-2 pr-14">
        <h4 class="mb-2 text-2xl font-semibold text-gray-800 dark:text-white/90">
          Edit Address
        </h4>
        <p class="mb-6 text-sm text-gray-500 dark:text-gray-400 lg:mb-7">
          Update your details to keep your profile up-to-date.
        </p>
      </div>
      <form class="flex flex-col">
        <div class="px-2 overflow-y-auto custom-scrollbar">
          <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
            <div>
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                Country
              </label>
              <input type="text" value="United States"
                class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
            </div>

            <div>
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                City/State
              </label>
              <input type="text" value="Arizona, United States"
                class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
            </div>

            <div>
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                Postal Code
              </label>
              <input type="text" value="ERT 2489"
                class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
            </div>

            <div>
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                TAX ID
              </label>
              <input type="text" value="AS4568384"
                class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
            </div>
          </div>
        </div>
        <div class="flex items-center gap-3 mt-6 lg:justify-end">
          <button @click="isProfileAddressModal = false" type="button"
            class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] sm:w-auto">
            Close
          </button>
          <button type="button"
            class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto">
            Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
  <!-- END MODAL -->
  <script defer src="bundle.js"></script>
</body>

</html>