<?php
session_start(); // WAJIB ADA agar status Online berfungsi
include 'koneksi.php';
$db = new database();
$data_admin = $db->tampil_data_admin();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="apple-touch-icon" sizes="76x76" href="build/assets/img/apple-icon.png" />
  <link rel="icon" type="image/png" href="build/assets/img/favicon.png" />
  <title>Data Admin - LinkUp</title>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <!-- Nucleo Icons -->
  <link href="build/assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="build/assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Main Styling -->
  <link href="build/assets/css/soft-ui-dashboard-tailwind.css?v=1.0.5" rel="stylesheet" />

  <!-- Nepcha Analytics (nepcha.com) -->
  <!-- Nepcha is a easy-to-use web analytics. No cookies and fully compliant with GDPR, CCPA and PECR. -->
  <script defer data-site="YOUR_DOMAIN_HERE" src="https://api.nepcha.com/js/nepcha-analytics.js"></script>
</head>

<body class="m-0 font-sans text-base antialiased font-normal leading-default bg-gray-50 text-slate-500">

  <?php include "sidebar.php"; ?>

  <main class="ease-soft-in-out xl:ml-68.5 relative h-full max-h-screen rounded-xl transition-all duration-200">
    <!-- Navbar -->
    <nav
      class="relative flex flex-wrap items-center justify-between px-0 py-2 mx-6 transition-all shadow-none duration-250 ease-soft-in rounded-2xl lg:flex-nowrap lg:justify-start"
      navbar-main navbar-scroll="true">
      <div class="flex items-center justify-between w-full px-4 py-1 mx-auto flex-wrap-inherit">
        <nav>
          <!-- breadcrumb -->
          <ol class="flex flex-wrap pt-1 mr-12 bg-transparent rounded-lg sm:mr-16">
            <li class="text-sm leading-normal">
              <a class="opacity-50 text-slate-700" href="javascript:;">LinkUp</a>
            </li>
            <li
              class="text-sm pl-2 capitalize leading-normal text-slate-700 before:float-left before:pr-2 before:text-gray-600 before:content-['/']"
              aria-current="page">Manajemen Pengguna</li>
          </ol>
          <h6 class="mb-0 font-bold capitalize">Data Admin</h6>
        </nav>

        <div class="flex items-center mt-2 grow sm:mt-0 sm:mr-6 md:mr-0 lg:flex lg:basis-auto">
          <div class="flex items-center md:ml-auto md:pr-4">
            <div class="relative flex flex-wrap items-stretch w-full transition-all rounded-lg ease-soft">
              <span
                class="text-sm ease-soft leading-5.6 absolute z-50 -ml-px flex h-full items-center whitespace-nowrap rounded-lg rounded-tr-none rounded-br-none border border-r-0 border-transparent bg-transparent py-2 px-2.5 text-center font-normal text-slate-500 transition-all">
                <i class="fas fa-search" aria-hidden="true"></i>
              </span>
              <input type="text"
                class="pl-8.75 text-sm focus:shadow-soft-primary-outline ease-soft w-1/100 leading-5.6 relative -ml-px block min-w-0 flex-auto rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding py-2 pr-3 text-gray-700 transition-all placeholder:text-gray-500 focus:border-fuchsia-300 focus:outline-none focus:transition-shadow"
                placeholder="Type here..." />
            </div>
          </div>
          <ul class="flex flex-row justify-end pl-0 mb-0 list-none md-max:w-full">
            <!-- online builder btn  -->
            <!-- <li class="flex items-center">
                <a class="inline-block px-8 py-2 mb-0 mr-4 text-xs font-bold text-center uppercase align-middle transition-all bg-transparent border border-solid rounded-lg shadow-none cursor-pointer leading-pro border-fuchsia-500 ease-soft-in hover:scale-102 active:shadow-soft-xs text-fuchsia-500 hover:border-fuchsia-500 active:bg-fuchsia-500 active:hover:text-fuchsia-500 hover:text-fuchsia-500 tracking-tight-soft hover:bg-transparent hover:opacity-75 hover:shadow-none active:text-white active:hover:bg-transparent" target="_blank" href="https://www.creative-tim.com/builder/soft-ui?ref=navbar-dashboard&amp;_ga=2.76518741.1192788655.1647724933-1242940210.1644448053">Online Builder</a>
              </li> -->
            <li class="flex items-center">
              <a href="build/pages/sign-in.html"
                class="block px-0 py-2 text-sm font-semibold transition-all ease-nav-brand text-slate-500">
                <i class="fa fa-user sm:mr-1" aria-hidden="true"></i>
                <span class="hidden sm:inline">Sign In</span>
              </a>
            </li>
            <li class="flex items-center pl-4 xl:hidden">
              <a href="javascript:;" class="block p-0 text-sm transition-all ease-nav-brand text-slate-500"
                sidenav-trigger>
                <div class="w-4.5 overflow-hidden">
                  <i class="ease-soft mb-0.75 relative block h-0.5 rounded-sm bg-slate-500 transition-all"></i>
                  <i class="ease-soft mb-0.75 relative block h-0.5 rounded-sm bg-slate-500 transition-all"></i>
                  <i class="ease-soft relative block h-0.5 rounded-sm bg-slate-500 transition-all"></i>
                </div>
              </a>
            </li>
            <li class="flex items-center px-4">
              <a href="javascript:;" class="p-0 text-sm transition-all ease-nav-brand text-slate-500">
                <i fixed-plugin-button-nav class="cursor-pointer fa fa-cog" aria-hidden="true"></i>
                <!-- fixed-plugin-button-nav  -->
              </a>
            </li>

            <!-- notifications -->

            <li class="relative flex items-center pr-2">
              <p class="hidden transform-dropdown-show"></p>
              <a href="javascript:;" class="block p-0 text-sm transition-all ease-nav-brand text-slate-500"
                dropdown-trigger aria-expanded="false">
                <i class="cursor-pointer fa fa-bell" aria-hidden="true"></i>
              </a>

              <ul dropdown-menu
                class="text-sm transform-dropdown before:font-awesome before:leading-default before:duration-350 before:ease-soft lg:shadow-soft-3xl duration-250 min-w-44 before:sm:right-7.5 before:text-5.5 pointer-events-none absolute right-0 top-0 z-50 origin-top list-none rounded-lg border-0 border-solid border-transparent bg-white bg-clip-padding px-2 py-4 text-left text-slate-500 opacity-0 transition-all before:absolute before:right-2 before:left-auto before:top-0 before:z-50 before:inline-block before:font-normal before:text-white before:antialiased before:transition-all before:content-['\f0d8'] sm:-mr-6 lg:absolute lg:right-0 lg:left-auto lg:mt-2 lg:block lg:cursor-pointer">
                <!-- add show class on dropdown open js -->
                <li class="relative mb-2">
                  <a class="ease-soft py-1.2 clear-both block w-full whitespace-nowrap rounded-lg bg-transparent px-4 duration-300 hover:bg-gray-200 hover:text-slate-700 lg:transition-colors"
                    href="javascript:;">
                    <div class="flex py-1">
                      <div class="my-auto">
                        <img src="build/assets/img/team-2.jpg"
                          class="inline-flex items-center justify-center mr-4 text-sm text-white h-9 w-9 max-w-none rounded-xl" />
                      </div>
                      <div class="flex flex-col justify-center">
                        <h6 class="mb-1 text-sm font-normal leading-normal"><span class="font-semibold">New
                            message</span> from Laur</h6>
                        <p class="mb-0 text-xs leading-tight text-slate-400">
                          <i class="mr-1 fa fa-clock" aria-hidden="true"></i>
                          13 minutes ago
                        </p>
                      </div>
                    </div>
                  </a>
                </li>

                <li class="relative mb-2">
                  <a class="ease-soft py-1.2 clear-both block w-full whitespace-nowrap rounded-lg px-4 transition-colors duration-300 hover:bg-gray-200 hover:text-slate-700"
                    href="javascript:;">
                    <div class="flex py-1">
                      <div class="my-auto">
                        <img src="build/assets/img/small-logos/logo-spotify.svg"
                          class="inline-flex items-center justify-center mr-4 text-sm text-white bg-gradient-to-tl from-gray-900 to-slate-800 h-9 w-9 max-w-none rounded-xl" />
                      </div>
                      <div class="flex flex-col justify-center">
                        <h6 class="mb-1 text-sm font-normal leading-normal"><span class="font-semibold">New album</span>
                          by Travis Scott</h6>
                        <p class="mb-0 text-xs leading-tight text-slate-400">
                          <i class="mr-1 fa fa-clock" aria-hidden="true"></i>
                          1 day
                        </p>
                      </div>
                    </div>
                  </a>
                </li>

                <li class="relative">
                  <a class="ease-soft py-1.2 clear-both block w-full whitespace-nowrap rounded-lg px-4 transition-colors duration-300 hover:bg-gray-200 hover:text-slate-700"
                    href="javascript:;">
                    <div class="flex py-1">
                      <div
                        class="inline-flex items-center justify-center my-auto mr-4 text-sm text-white transition-all duration-200 ease-nav-brand bg-gradient-to-tl from-slate-600 to-slate-300 h-9 w-9 rounded-xl">
                        <svg width="12px" height="12px" viewBox="0 0 43 36" version="1.1"
                          xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                          <title>credit-card</title>
                          <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <g transform="translate(-2169.000000, -745.000000)" fill="#FFFFFF" fill-rule="nonzero">
                              <g transform="translate(1716.000000, 291.000000)">
                                <g transform="translate(453.000000, 454.000000)">
                                  <path class="color-background"
                                    d="M43,10.7482083 L43,3.58333333 C43,1.60354167 41.3964583,0 39.4166667,0 L3.58333333,0 C1.60354167,0 0,1.60354167 0,3.58333333 L0,10.7482083 L43,10.7482083 Z"
                                    opacity="0.593633743"></path>
                                  <path class="color-background"
                                    d="M0,16.125 L0,32.25 C0,34.2297917 1.60354167,35.8333333 3.58333333,35.8333333 L39.4166667,35.8333333 C41.3964583,35.8333333 43,34.2297917 43,32.25 L43,16.125 L0,16.125 Z M19.7083333,26.875 L7.16666667,26.875 L7.16666667,23.2916667 L19.7083333,23.2916667 L19.7083333,26.875 Z M35.8333333,26.875 L28.6666667,26.875 L28.6666667,23.2916667 L35.8333333,23.2916667 L35.8333333,26.875 Z">
                                  </path>
                                </g>
                              </g>
                            </g>
                          </g>
                        </svg>
                      </div>
                      <div class="flex flex-col justify-center">
                        <h6 class="mb-1 text-sm font-normal leading-normal">Payment successfully completed</h6>
                        <p class="mb-0 text-xs leading-tight text-slate-400">
                          <i class="mr-1 fa fa-clock" aria-hidden="true"></i>
                          2 days
                        </p>
                      </div>
                    </div>
                  </a>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="w-full px-6 py-6 mx-auto">
      <!-- table 1 -->

      <div class="flex flex-wrap -mx-3">
        <div class="flex-none w-full max-w-full px-3">
          <div
            class="relative flex flex-col min-w-0 mb-6 break-words bg-white border-0 border-transparent border-solid shadow-soft-xl rounded-2xl bg-clip-border">
            <div class="p-6 pb-0 mb-0 bg-white border-b-0 border-b-solid rounded-t-2xl border-b-transparent">
              <div class="flex justify-between items-center">
                <h6>Tabel Data Admin</h6>
                <button type="button" onclick="openModal()"
                  class="inline-block px-6 py-3 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-gradient-to-tl from-blue-600 to-cyan-400 leading-pro text-xs ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25 hover:scale-102 active:opacity-85">
                  <i class="fas fa-plus mr-2"></i>Tambah Admin
                </button>
              </div>
            </div>
            <div class="flex-auto px-0 pt-0 pb-2">
              <div class="p-6 overflow-x-auto">
                <table id="tabelAdmin" class="items-center w-full mb-0 align-top border-gray-200 text-slate-500">
                  <thead class="align-bottom">
                    <tr>
                      <th
                        class="px-6 py-3 pl-2 font-bold text-left uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">
                        ID Admin</th>
                      <th
                        class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">
                        Profil Admin</th>
                      <th
                        class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">
                        Status</th>
                      <th
                        class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">
                        Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (!empty($data_admin)):
                      foreach ($data_admin as $row):
                        $format_id = "A" . str_pad($row['id_admin'], 4, "0", STR_PAD_LEFT);
                        $is_online = (isset($_SESSION['id_admin']) && $_SESSION['id_admin'] == $row['id_admin']);
                        $status_label = $is_online ? "Online" : "Offline";
                        $status_class = $is_online ? "from-green-600 to-lime-400" : "from-slate-600 to-slate-300";
                        ?>
                        <tr>
                          <td class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent text-sm">
                            <span class="font-semibold leading-tight"><?php echo $format_id; ?></span>
                          </td>
                          <td class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                            <div class="flex px-2 py-1">
                              <div>
                                <img src="build/assets/img/team-2.jpg"
                                  class="inline-flex items-center justify-center mr-4 text-sm text-white h-9 w-9 rounded-xl"
                                  alt="user" />
                              </div>
                              <div class="flex flex-col justify-center">
                                <h6 class="mb-0 text-sm leading-normal"><?php echo $row['nama_admin']; ?></h6>
                                <p class="mb-0 text-xs leading-tight text-slate-400"><?php echo $row['email_admin']; ?></p>
                              </div>
                            </div>
                          </td>
                          <td
                            class="p-2 text-sm leading-normal text-center align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                            <span
                              class="bg-gradient-to-tl <?php echo $status_class; ?> px-2.5 text-xs rounded-1.8 py-1.4 inline-block whitespace-nowrap text-center align-baseline font-bold uppercase leading-none text-white">
                              <?php echo $status_label; ?>
                            </span>
                          </td>
                          <td
                            class="p-2 text-center align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                            <div class="flex justify-center items-center">
                              <a href="edit_admin.php?id=<?php echo $row['id_admin']; ?>"
                                class="inline-block px-4 py-2 mr-2 mb-0 font-bold text-center text-white uppercase align-middle transition-all border-0 rounded-lg cursor-pointer shadow-soft-md bg-gradient-to-tl from-yellow-400 to-yellow-200 leading-pro text-xxs ease-soft-in hover:scale-102 active:opacity-85">EDIT</a>
                              <a href="hapus_admin.php?id=<?php echo $row['id_admin']; ?>"
                                onclick="return confirm('Yakin ingin menghapus?')"
                                class="inline-block px-4 py-2 mb-0 font-bold text-center text-white uppercase align-middle transition-all border-0 rounded-lg cursor-pointer shadow-soft-md bg-gradient-to-tl from-red-600 to-rose-400 leading-pro text-xxs ease-soft-in hover:scale-102 active:opacity-85">HAPUS</a>
                            </div>
                          </td>
                        </tr>
                      <?php endforeach; endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div id="tambahAdminModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
          <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeModal()"></div>

          <div
            class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle w-full sm:max-w-7xl md:w-[90%]">
            <form id="formTambahAdmin" action="tambah_admin.php" method="POST">
              <div class="bg-white px-10 pt-10 pb-8">
                <div class="border-b pb-4 mb-8">
                  <h3 class="text-2xl font-bold text-slate-700 flex items-center">
                    <i class="fas fa-user-plus mr-3 text-blue-500"></i>Tambah Admin Baru
                  </h3>
                  <p class="text-sm text-slate-400 mt-1">Masukkann data di bawah ini untuk menambahkan akses
                    administrator baru.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-16 gap-y-8">

                  <div class="space-y-6">
                    <div>
                      <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Nama
                        Admin</label>
                      <input type="text" name="nama_admin" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-400 outline-none transition-all"
                        placeholder="Contoh: Budi Santoso">
                    </div>
                    <div>
                      <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Alamat Email
                      </label>
                      <input type="email" name="email_admin" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-400 outline-none transition-all"
                        placeholder="admin@linkup.com">
                    </div>
                  </div>

                  <div class="space-y-6">
                    <div>
                      <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Password
                        Akun</label>
                      <input type="password" name="password_admin" id="password_admin" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-400 outline-none transition-all"
                        placeholder="Minimal 8 karakter">
                      <div class="flex items-center mt-3">
                        <input type="checkbox" id="showPassword"
                          class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer"
                          onchange="togglePassword()">
                        <label for="showPassword"
                          class="ml-2 text-xs font-medium text-slate-500 cursor-pointer italic">Lihat kata sandi</label>
                      </div>
                    </div>
                    <div>
                      <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Konfirmasi
                        Password</label>
                      <input type="password" name="confirm_password" id="confirm_password" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-400 outline-none transition-all"
                        placeholder="Ulangi password di atas">
                    </div>
                  </div>

                </div>
              </div>

              <div class="bg-gray-50 px-10 py-6 sm:flex sm:flex-row-reverse gap-4 border-t">
                <button type="submit"
                  class="w-full sm:w-auto px-10 py-3.5 font-bold text-white bg-gradient-to-tl from-blue-600 to-cyan-400 rounded-xl uppercase text-xs shadow-lg hover:shadow-blue-200 transition-all active:scale-95">
                  <i class="fas fa-check-circle mr-2"></i>Simpan
                </button>
                <button type="button" onclick="closeModal()"
                  class="w-full sm:w-auto px-10 py-3.5 font-bold text-slate-600 bg-white border border-gray-300 rounded-xl uppercase text-xs hover:bg-gray-50 transition-all">
                  Batal
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <footer class="pt-4">
        <div class="w-full px-6 mx-auto">
          <div class="flex flex-wrap items-center -mx-3 lg:justify-between">
            <div class="w-full max-w-full px-3 mt-0 mb-6 shrink-0 lg:mb-0 lg:w-1/2 lg:flex-none">
              <div class="text-sm leading-normal text-center text-slate-500 lg:text-left">
                ©
                <script>
                  document.write(new Date().getFullYear() + ",");
                </script>
                made with <i class="fa fa-heart"></i> by
                <a href="https://www.creative-tim.com" class="font-semibold text-slate-700" target="_blank">Creative
                  Tim</a>
                for a better web.
              </div>
            </div>
          </div>
        </div>
      </footer>
    </div>
  </main>
  <div fixed-plugin>
    <!-- -right-90 in loc de 0-->
    <div fixed-plugin-card
      class="z-sticky shadow-soft-3xl w-90 ease-soft -right-90 fixed top-0 left-auto flex h-full min-w-0 flex-col break-words rounded-none border-0 bg-white bg-clip-border px-2.5 duration-200">
      <div class="px-6 pt-4 pb-0 mb-0 bg-white border-b-0 rounded-t-2xl">
        <div class="float-left">
          <h5 class="mt-4 mb-0">Soft UI Configurator</h5>
          <p>See our dashboard options.</p>
        </div>
        <div class="float-right mt-6">
          <button fixed-plugin-close-button
            class="inline-block p-0 mb-4 text-xs font-bold text-center uppercase align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer hover:scale-102 leading-pro ease-soft-in tracking-tight-soft bg-150 bg-x-25 active:opacity-85 text-slate-700">
            <i class="fa fa-close"></i>
          </button>
        </div>
        <!-- End Toggle Button -->
      </div>
      <hr class="h-px mx-0 my-1 bg-transparent bg-gradient-to-r from-transparent via-black/40 to-transparent" />
      <div class="flex-auto p-6 pt-0 sm:pt-4">
        <!-- Sidebar Backgrounds -->
        <div>
          <h6 class="mb-0">Sidebar Colors</h6>
        </div>
        <a href="javascript:void(0)">
          <div class="my-2 text-left" sidenav-colors>
            <span
              class="text-xs rounded-circle h-5.75 mr-1.25 w-5.75 ease-soft-in-out bg-gradient-to-tl from-purple-700 to-pink-500 relative inline-block cursor-pointer whitespace-nowrap border border-solid border-slate-700 text-center align-baseline font-bold uppercase leading-none text-white transition-all duration-200 hover:border-slate-700"
              active-color data-color-from="purple-700" data-color-to="pink-500" onclick="sidebarColor(this)"></span>
            <span
              class="text-xs rounded-circle h-5.75 mr-1.25 w-5.75 ease-soft-in-out bg-gradient-to-tl from-gray-900 to-slate-800 relative inline-block cursor-pointer whitespace-nowrap border border-solid border-white text-center align-baseline font-bold uppercase leading-none text-white transition-all duration-200 hover:border-slate-700"
              data-color-from="gray-900" data-color-to="slate-800" onclick="sidebarColor(this)"></span>
            <span
              class="text-xs rounded-circle h-5.75 mr-1.25 w-5.75 ease-soft-in-out bg-gradient-to-tl from-blue-600 to-cyan-400 relative inline-block cursor-pointer whitespace-nowrap border border-solid border-white text-center align-baseline font-bold uppercase leading-none text-white transition-all duration-200 hover:border-slate-700"
              data-color-from="blue-600" data-color-to="cyan-400" onclick="sidebarColor(this)"></span>
            <span
              class="text-xs rounded-circle h-5.75 mr-1.25 w-5.75 ease-soft-in-out bg-gradient-to-tl from-green-600 to-lime-400 relative inline-block cursor-pointer whitespace-nowrap border border-solid border-white text-center align-baseline font-bold uppercase leading-none text-white transition-all duration-200 hover:border-slate-700"
              data-color-from="green-600" data-color-to="lime-400" onclick="sidebarColor(this)"></span>
            <span
              class="text-xs rounded-circle h-5.75 mr-1.25 w-5.75 ease-soft-in-out bg-gradient-to-tl from-red-500 to-yellow-400 relative inline-block cursor-pointer whitespace-nowrap border border-solid border-white text-center align-baseline font-bold uppercase leading-none text-white transition-all duration-200 hover:border-slate-700"
              data-color-from="red-500" data-color-to="yellow-400" onclick="sidebarColor(this)"></span>
            <span
              class="text-xs rounded-circle h-5.75 mr-1.25 w-5.75 ease-soft-in-out bg-gradient-to-tl from-red-600 to-rose-400 relative inline-block cursor-pointer whitespace-nowrap border border-solid border-white text-center align-baseline font-bold uppercase leading-none text-white transition-all duration-200 hover:border-slate-700"
              data-color-from="red-600" data-color-to="rose-400" onclick="sidebarColor(this)"></span>
          </div>
        </a>
        <!-- Sidenav Type -->
        <div class="mt-4">
          <h6 class="mb-0">Sidenav Type</h6>
          <p class="text-sm leading-normal">Choose between 2 different sidenav types.</p>
        </div>
        <div class="flex">
          <button transparent-style-btn
            class="inline-block w-full px-4 py-3 mb-2 text-xs font-bold text-center text-white uppercase align-middle transition-all border border-transparent border-solid rounded-lg cursor-pointer xl-max:cursor-not-allowed xl-max:opacity-65 xl-max:pointer-events-none xl-max:bg-gradient-to-tl xl-max:from-purple-700 xl-max:to-pink-500 xl-max:text-white xl-max:border-0 hover:scale-102 hover:shadow-soft-xs active:opacity-85 leading-pro ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25 bg-gradient-to-tl from-purple-700 to-pink-500 bg-fuchsia-500 hover:border-fuchsia-500"
            data-class="bg-transparent" active-style>Transparent</button>
          <button white-style-btn
            class="inline-block w-full px-4 py-3 mb-2 ml-2 text-xs font-bold text-center uppercase align-middle transition-all bg-transparent border border-solid rounded-lg cursor-pointer xl-max:cursor-not-allowed xl-max:opacity-65 xl-max:pointer-events-none xl-max:bg-gradient-to-tl xl-max:from-purple-700 xl-max:to-pink-500 xl-max:text-white xl-max:border-0 hover:scale-102 hover:shadow-soft-xs active:opacity-85 leading-pro ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25 border-fuchsia-500 bg-none text-fuchsia-500 hover:border-fuchsia-500"
            data-class="bg-white">White</button>
        </div>
        <p class="block mt-2 text-sm leading-normal xl:hidden">You can change the sidenav type just on desktop view.</p>
        <!-- Navbar Fixed -->
        <div class="mt-4">
          <h6 class="mb-0">Navbar Fixed</h6>
        </div>
        <div class="min-h-6 mb-0.5 block pl-0">
          <input
            class="rounded-10 duration-250 ease-soft-in-out after:rounded-circle after:shadow-soft-2xl after:duration-250 checked:after:translate-x-5.25 h-5 relative float-left mt-1 ml-auto w-10 cursor-pointer appearance-none border border-solid border-gray-200 bg-slate-800/10 bg-none bg-contain bg-left bg-no-repeat align-top transition-all after:absolute after:top-px after:h-4 after:w-4 after:translate-x-px after:bg-white after:content-[''] checked:border-slate-800/95 checked:bg-slate-800/95 checked:bg-none checked:bg-right"
            type="checkbox" navbarFixed />
        </div>
        <hr class="h-px bg-transparent bg-gradient-to-r from-transparent via-black/40 to-transparent sm:my-6" />
        <a class="inline-block w-full px-6 py-3 mb-4 text-xs font-bold text-center text-white uppercase align-middle transition-all bg-transparent border-0 rounded-lg cursor-pointer leading-pro ease-soft-in hover:shadow-soft-xs hover:scale-102 active:opacity-85 tracking-tight-soft shadow-soft-md bg-150 bg-x-25 bg-gradient-to-tl from-gray-900 to-slate-800"
          href="https://www.creative-tim.com/product/soft-ui-dashboard-tailwind" target="_blank">Free Download</a>
        <a class="inline-block w-full px-6 py-3 mb-4 text-xs font-bold text-center uppercase align-middle transition-all bg-transparent border border-solid rounded-lg shadow-none cursor-pointer active:shadow-soft-xs hover:scale-102 active:opacity-85 leading-pro ease-soft-in tracking-tight-soft bg-150 bg-x-25 border-slate-700 text-slate-700 hover:bg-transparent hover:text-slate-700 hover:shadow-none active:bg-slate-700 active:text-white active:hover:bg-transparent active:hover:text-slate-700 active:hover:shadow-none"
          href="https://www.creative-tim.com/learning-lab/tailwind/html/quick-start/soft-ui-dashboard/" target="_blank"">View documentation</a>
          <div class=" w-full text-center">
          <a class="github-button" href="https://github.com/creativetimofficial/soft-ui-dashboard-tailwind"
            data-icon="octicon-star" data-size="large" data-show-count="true"
            aria-label="Star creativetimofficial/soft-ui-dashboard on GitHub">Star</a>
          <h6 class="mt-4">Thank you for sharing!</h6>
          <a href="https://twitter.com/intent/tweet?text=Check%20Soft%20UI%20Dashboard%20Tailwind%20made%20by%20%40CreativeTim&hashtags=webdesign,dashboard,tailwindcss&amp;url=https%3A%2F%2Fwww.creative-tim.com%2Fproduct%2Fsoft-ui-dashboard-tailwind"
            class="inline-block px-6 py-3 mb-0 mr-2 text-xs font-bold text-center text-white uppercase align-middle transition-all border-0 rounded-lg cursor-pointer hover:shadow-soft-xs hover:scale-102 active:opacity-85 leading-pro ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25 me-2 border-slate-700 bg-slate-700"
            target="_blank"> <i class="mr-1 fab fa-twitter" aria-hidden="true"></i> Tweet </a>
          <a href="https://www.facebook.com/sharer/sharer.php?u=https://www.creative-tim.com/product/soft-ui-dashboard-tailwind"
            class="inline-block px-6 py-3 mb-0 mr-2 text-xs font-bold text-center text-white uppercase align-middle transition-all border-0 rounded-lg cursor-pointer hover:shadow-soft-xs hover:scale-102 active:opacity-85 leading-pro ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25 me-2 border-slate-700 bg-slate-700"
            target="_blank"> <i class="mr-1 fab fa-facebook-square" aria-hidden="true"></i> Share </a>
      </div>
    </div>
  </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
  <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>

  <script>
    $(document).ready(function () {
      $('#tabelAdmin').DataTable({
        "paging": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        "dom": '<"flex flex-wrap justify-between items-center px-4 py-2"l f>rt<"flex flex-wrap justify-between items-center px-4 py-2"i p>',
        "language": {
          "search": "",
          "searchPlaceholder": "Cari Admin...",
          "lengthMenu": "Tampilkan _MENU_",
          "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ admin",
          "paginate": {
            "previous": "<",
            "next": ">"
          }
        }
      });
    });


  </script>

  <script>
    // Fungsi untuk modal
    function openModal() {
      document.getElementById('tambahAdminModal').classList.remove('hidden');
      document.body.classList.add('overflow-hidden');
    }

    function closeModal() {
      document.getElementById('tambahAdminModal').classList.add('hidden');
      document.body.classList.remove('overflow-hidden');
      document.getElementById('formTambahAdmin').reset();
    }

    // Fungsi toggle password visibility
    function togglePassword() {
      const passwordInput = document.getElementById('password_admin');
      const showPasswordCheckbox = document.getElementById('showPassword');

      if (showPasswordCheckbox.checked) {
        passwordInput.type = 'text';
      } else {
        passwordInput.type = 'password';
      }
    }

    // Validasi form sebelum submit
    document.getElementById('formTambahAdmin').addEventListener('submit', function (e) {
      const password = document.getElementById('password_admin').value;
      const confirmPassword = document.getElementById('confirm_password').value;

      if (password !== confirmPassword) {
        e.preventDefault();
        Swal.fire({
          icon: 'error',
          title: 'Password Tidak Cocok',
          text: 'Password dan konfirmasi password harus sama!',
          confirmButtonColor: '#3085d6',
        });
        return false;
      }

      if (password.length < 6) {
        e.preventDefault();
        Swal.fire({
          icon: 'error',
          title: 'Password Terlalu Pendek',
          text: 'Password minimal 6 karakter!',
          confirmButtonColor: '#3085d6',
        });
        return false;
      }

      // Tampilkan loading
      Swal.fire({
        title: 'Menyimpan...',
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });
    });

    // Close modal dengan ESC key
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        closeModal();
      }
    });

    // Tambahkan data ke tabel setelah submit berhasil (AJAX)
    $('#formTambahAdmin').on('submit', function (e) {
      e.preventDefault();

      const formData = $(this).serialize();

      $.ajax({
        url: 'tambah_admin.php',
        type: 'POST',
        data: formData,
        success: function (response) {
          const result = JSON.parse(response);

          if (result.success) {
            Swal.fire({
              icon: 'success',
              title: 'Berhasil!',
              text: result.message,
              confirmButtonColor: '#3085d6',
            }).then(() => {
              closeModal();

              // Reload data tabel
              const table = $('#tabelAdmin').DataTable();
              table.ajax.reload();

              // Atau jika tidak pakai AJAX reload, redirect
              window.location.reload();
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Gagal!',
              text: result.message,
              confirmButtonColor: '#3085d6',
            });
          }
        },
        error: function () {
          Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat menyimpan data.',
            confirmButtonColor: '#3085d6',
          });
        }
      });
    });
  </script>

  <style>
    /* Mengurangi jarak agar lebih rapat */
    .dataTables_wrapper .flex {
      margin: 0 !important;
    }

    .dataTables_length,
    .dataTables_info {
      font-size: 0.75rem !important;
      font-weight: 600;
      color: #8392ab !important;
      padding: 10px 0 !important;
    }

    /* MEMPERBAIKI TOMBOL PAGINATION (Angka & Panah) */
    .dataTables_paginate {
      display: flex !important;
      align-items: center;
      gap: 4px;
      padding: 10px 0;
    }

    .dataTables_paginate .paginate_button {
      display: inline-flex !important;
      align-items: center;
      justify-content: center;
      min-width: 32px !important;
      height: 32px !important;
      padding: 0 8px !important;
      margin: 0 !important;
      border-radius: 8px !important;
      border: none !important;
      font-size: 0.75rem !important;
      font-weight: 700 !important;
      cursor: pointer !important;
      background: #fff !important;
      color: #8392ab !important;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
      transition: all 0.3s ease;
    }

    /* Warna Biru Gradasi untuk Tombol Aktif */
    .dataTables_paginate .paginate_button.current {
      background-image: linear-gradient(310deg, #2152ff 0%, #21d4fd 100%) !important;
      color: white !important;
      box-shadow: 0 4px 7px -1px rgba(0, 0, 0, 0.15) !important;
    }

    /* Hover efek */
    .dataTables_paginate .paginate_button:hover:not(.current) {
      background: #e9ecef !important;
      color: #2152ff !important;
    }

    /* Menampilkan Ikon < > dengan benar */
    .dataTables_paginate .paginate_button i {
      font-size: 10px !important;
    }

    /* Merapikan Input Search */
    .dataTables_filter input {
      font-size: 0.75rem !important;
      padding: 0.4rem 0.75rem !important;
      border-radius: 8px !important;
      border: 1px solid #d2d6da !important;
      outline: none !important;
    }

    /* Hilangkan padding top bawaan container agar tidak terlalu jauh dari judul */
    .flex-auto.px-0.pt-0.pb-2 .p-6.overflow-x-auto {
      padding-top: 0px !important;
    }

    /* PERBAIKAN STYLING MODAL YANG LEBIH BAIK */
    #tambahAdminModal {
      display: none !important;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: 9999;
    }

    #tambahAdminModal:not(.hidden) {
      display: flex !important;
      align-items: center;
      justify-content: center;
    }

    /* Overlay background */
    #tambahAdminModal .fixed {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.5);
      z-index: 10;
    }

    /* Modal container */
    #tambahAdminModal .inline-block {
      position: relative;
      z-index: 20;
      width: 100%;
      max-width: 500px;
      /* Lebar modal lebih besar */
      margin: 1rem;
      background: white;
      border-radius: 0.75rem;
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    /* Modal content */
    #tambahAdminModal .bg-white {
      padding: 1.5rem;
    }

    /* Modal header */
    #tambahAdminModal h3 {
      font-size: 1.25rem;
      font-weight: 600;
      color: #1e293b;
      margin-bottom: 1.5rem;
      padding-bottom: 1rem;
      border-bottom: 1px solid #e2e8f0;
    }

    /* Form fields */
    #tambahAdminModal .space-y-4>div {
      margin-bottom: 1.25rem;
    }

    #tambahAdminModal label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 500;
      color: #475569;
    }

    #tambahAdminModal input[type="text"],
    #tambahAdminModal input[type="email"],
    #tambahAdminModal input[type="password"] {
      width: 100%;
      padding: 0.625rem 0.875rem;
      border: 1px solid #cbd5e1;
      border-radius: 0.5rem;
      font-size: 0.875rem;
      transition: all 0.2s;
    }

    #tambahAdminModal input[type="text"]:focus,
    #tambahAdminModal input[type="email"]:focus,
    #tambahAdminModal input[type="password"]:focus {
      outline: none;
      border-color: #3b82f6;
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    /* Checkbox styling */
    #tambahAdminModal input[type="checkbox"] {
      width: 1rem;
      height: 1rem;
      border-radius: 0.25rem;
      border: 1px solid #cbd5e1;
    }

    /* Modal footer buttons */
    #tambahAdminModal .bg-gray-50 {
      padding: 1.25rem 1.5rem;
      border-top: 1px solid #e2e8f0;
      display: flex;
      gap: 0.75rem;
      justify-content: flex-end;
    }

    /* Primary button */
    #tambahAdminModal button[type="submit"] {
      background: linear-gradient(310deg, #2152ff 0%, #21d4fd 100%);
      color: white;
      padding: 0.625rem 1.5rem;
      border-radius: 0.5rem;
      font-weight: 500;
      border: none;
      cursor: pointer;
      transition: all 0.2s;
    }

    #tambahAdminModal button[type="submit"]:hover {
      opacity: 0.9;
      transform: translateY(-1px);
    }

    /* Secondary button */
    #tambahAdminModal button[type="button"] {
      background: white;
      color: #64748b;
      padding: 0.625rem 1.5rem;
      border-radius: 0.5rem;
      font-weight: 500;
      border: 1px solid #cbd5e1;
      cursor: pointer;
      transition: all 0.2s;
    }

    #tambahAdminModal button[type="button"]:hover {
      background: #f8fafc;
      border-color: #94a3b8;
    }

    /* Responsive adjustments */
    @media (max-width: 640px) {
      #tambahAdminModal .inline-block {
        margin: 0.5rem;
        max-width: 95%;
      }

      #tambahAdminModal .bg-gray-50 {
        flex-direction: column-reverse;
      }

      #tambahAdminModal button[type="submit"],
      #tambahAdminModal button[type="button"] {
        width: 100%;
      }
    }

    /* Animasi modal */
    @keyframes modalFadeIn {
      from {
        opacity: 0;
        transform: scale(0.95) translateY(-10px);
      }

      to {
        opacity: 1;
        transform: scale(1) translateY(0);
      }
    }

    #tambahAdminModal .inline-block {
      animation: modalFadeIn 0.3s ease-out;
    }

    /* Pastikan body tidak scroll saat modal terbuka */
    body.modal-open {
      overflow: hidden;
    }
  </style>

</body>
<!-- plugin for scrollbar  -->
<script src="build/assets/js/plugins/perfect-scrollbar.min.js" async></script>
<!-- github button -->
<script async defer src="https://buttons.github.io/buttons.js"></script>
<!-- main script file  -->
<script src="build/assets/js/soft-ui-dashboard-tailwind.js?v=1.0.5" async></script>

</html>