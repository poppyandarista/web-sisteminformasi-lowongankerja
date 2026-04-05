<?php
session_start();
include 'koneksi.php';

$db = new database();

// Get data untuk dropdown
$data_lowongan = $db->tampil_data_lowongan();
$data_pelamar = $db->tampil_data_pelamar();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_lowongan = $_POST['id_lowongan'] ?? '';
    $id_user = $_POST['id_user'] ?? '';
    $status_lamaran = $_POST['status_lamaran'] ?? 'Diproses';
    $catatan_hrd = $_POST['catatan_hrd'] ?? '';

    // Validasi input wajib
    if (empty($id_lowongan) || empty($id_user)) {
        echo "<script>
                alert('Lowongan dan Pelamar wajib dipilih!');
                window.history.back();
              </script>";
        exit();
    }

    // Check if lamaran already exists
    $check_query = "SELECT * FROM lamaran WHERE id_lowongan = ? AND id_user = ?";
    $stmt = $db->koneksi->prepare($check_query);
    $stmt->bind_param("ii", $id_lowongan, $id_user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>
                alert('Pelamar sudah melamar untuk lowongan ini!');
                window.history.back();
              </script>";
        exit();
    }

    // Prepare data untuk disimpan
    $query = "INSERT INTO lamaran (id_lowongan, id_user, status_lamaran, catatan_hrd) 
              VALUES (?, ?, ?, ?)";

    $stmt = $db->koneksi->prepare($query);
    $stmt->bind_param("iiss", $id_lowongan, $id_user, $status_lamaran, $catatan_hrd);

    if ($stmt->execute()) {
        echo "<script>
                alert('Data lamaran berhasil ditambahkan!');
                window.location.href = 'datalamaran.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal menambah data lamaran!');
                window.history.back();
              </script>";
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
    <title>Tambah Lamaran | LinkUp</title>
    <link rel="icon" href="favicon.ico">
    <link href="style.css" rel="stylesheet">
    <style>
        .page-overlay {
            position: fixed;
            inset: 0;
            background-color: rgba(75, 85, 99, 0.5) !important;
            z-index: 9998 !important;
            pointer-events: auto;
        }

        .modal-container {
            z-index: 10000 !important;
        }

        .modal-content {
            z-index: 10001 !important;
            max-height: 90vh;
            overflow-y: auto;
        }

        .force-overlay {
            z-index: 9999 !important;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.25rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
        }

        .dark .form-label {
            color: #d1d5db;
        }

        .form-control {
            width: 100%;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            background-color: white;
        }

        .dark .form-control {
            background-color: #374151;
            border-color: #4b5563;
            color: #f3f4f6;
        }

        .form-control:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-textarea {
            min-height: 80px;
            resize: vertical;
        }
    </style>
</head>

<body
    x-data="{ page: 'tambahLamaran', 'loaded': true, 'darkMode': false, 'stickyMenu': false, 'sidebarToggle': false, 'scrollTop': false, 'modalOpen': false }"
    x-init="
         darkMode = JSON.parse(localStorage.getItem('darkMode'));
         $watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)))"
    :class="{'dark bg-gray-900': darkMode === true}">

    <!-- ===== Preloader Start ===== -->
    <div x-show="loaded"
        x-init="window.addEventListener('DOMContentLoaded', () => {setTimeout(() => loaded = false, 500)})"
        class="fixed left-0 top-0 z-999999 flex h-screen w-screen items-center justify-center bg-white dark:bg-black">
        <div class="h-16 w-16 animate-spin rounded-full border-4 border-solid border-brand-500 border-t-transparent">
        </div>
    </div>
    <!-- ===== Preloader End ===== -->

    <!-- ===== Page Wrapper Start ===== -->
    <div id="main-wrapper" class="flex h-screen overflow-hidden transition-all duration-300">
        <!-- ===== Sidebar Start ===== -->
        <?php include 'sidebar.php'; ?>
        <!-- ===== Sidebar End ===== -->

        <!-- ===== Content Area Start ===== -->
        <div class="relative flex flex-col flex-1 overflow-x-hidden overflow-y-auto">
            <!-- ===== Header Start ===== -->
            <?php include("header.php") ?>
            <!-- ===== Header End ===== -->

            <!-- ===== Main Content Start ===== -->
            <main>
                <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
                    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <h2 class="text-title-md2 font-bold text-black dark:text-white">
                            Tambah Lamaran
                        </h2>
                        <nav>
                            <ol class="flex items-center gap-2">
                                <li><a class="font-medium" href="index.php">Home ></a></li>
                                <li><a class="font-medium" href="datalamaran.php">Data Lamaran ></a></li>
                                <li class="font-medium text-primary">Tambah Lamaran</li>
                            </ol>
                        </nav>
                    </div>

                    <div
                        class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                        <div class="border-b border-stroke px-6 py-4 dark:border-strokedark">
                            <h3 class="font-medium text-black dark:text-white">Form Tambah Lamaran</h3>
                            <p class="text-sm text-gray-500">Isi form untuk menambah data lamaran baru</p>
                        </div>

                        <div class="p-6">
                            <form action="" method="POST" class="space-y-6 max-w-2xl">
                                <!-- Pilih Lowongan -->
                                <div class="form-group">
                                    <label class="form-label">Lowongan *</label>
                                    <select name="id_lowongan" required class="form-control">
                                        <option value="">Pilih Lowongan</option>
                                        <?php foreach ($data_lowongan as $lowongan):
                                            $format_id = "L" . sprintf("%04d", $lowongan['id_lowongan']);
                                            ?>
                                            <option value="<?php echo $lowongan['id_lowongan']; ?>">
                                                [
                                                <?php echo $format_id; ?>]
                                                <?php echo htmlspecialchars($lowongan['judul_lowongan']); ?> -
                                                <?php echo htmlspecialchars($lowongan['nama_perusahaan']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Pilih Pelamar -->
                                <div class="form-group">
                                    <label class="form-label">Pelamar *</label>
                                    <select name="id_user" required class="form-control">
                                        <option value="">Pilih Pelamar</option>
                                        <?php foreach ($data_pelamar as $pelamar):
                                            $format_id = "U" . sprintf("%04d", $pelamar['id_user']);
                                            ?>
                                            <option value="<?php echo $pelamar['id_user']; ?>">
                                                [
                                                <?php echo $format_id; ?>]
                                                <?php echo htmlspecialchars($pelamar['nama_user'] ?? $pelamar['email_user']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Status Lamaran -->
                                <div class="form-group">
                                    <label class="form-label">Status Lamaran *</label>
                                    <select name="status_lamaran" required class="form-control">
                                        <option value="Diproses" selected>Diproses</option>
                                        <option value="Diterima">Diterima</option>
                                        <option value="Ditolak">Ditolak</option>
                                    </select>
                                </div>

                                <!-- Catatan HRD -->
                                <div class="form-group">
                                    <label class="form-label">Catatan HRD (Opsional)</label>
                                    <textarea name="catatan_hrd" rows="3" class="form-control form-textarea"
                                        placeholder="Tambahkan catatan untuk lamaran ini..."></textarea>
                                </div>

                                <!-- Tombol Aksi -->
                                <div class="flex gap-3 pt-4">
                                    <a href="datalamaran.php"
                                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                                        Kembali
                                    </a>
                                    <button type="submit"
                                        class="px-4 py-2 text-sm font-medium text-white bg-brand-500 rounded-lg hover:bg-brand-600 transition-colors">
                                        Simpan Lamaran
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
            <!-- ===== Main Content End ===== -->
        </div>
        <!-- ===== Content Area End ===== -->
    </div>
    <!-- ===== Page Wrapper End ===== -->

    <script defer src="bundle.js"></script>
</body>

</html>