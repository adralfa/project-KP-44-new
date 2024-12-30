<?php

// session_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
    error_reporting(0);
}

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../../index.php");
    exit;
}

// Folder tempat file akan disimpan
$uploadDir = "../../../assets/uploads/";

// Fungsi untuk mengganti file lama dengan nama tetap
function replaceFile($uploadDir, $fileName, $tmpFilePath) {
    $targetFilePath = $uploadDir . $fileName;

    // Hapus file lama jika ada
    if (file_exists($targetFilePath)) {
        unlink($targetFilePath); // Hapus file lama
    }

    // Pindahkan file baru ke lokasi target
    if (move_uploaded_file($tmpFilePath, $targetFilePath)) {
        return true; // Berhasil diunggah
    } else {
        return false; // Gagal diunggah
    }
}

// Variabel untuk menyimpan status upload
$statusMessageIMG = '';
$alertClassIMG = '';
$statusMessagePDF = '';
$alertClassPDF = '';

// Penanganan upload file
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['uploadJadwal'])) {
        $fileType = strtolower(pathinfo($_FILES['jadwalFile']['name'], PATHINFO_EXTENSION));
        $allowedTypes = array('jpg', 'jpeg', 'png', 'gif'); // Format gambar yang diperbolehkan

        if (in_array($fileType, $allowedTypes)) {
            if (replaceFile($uploadDir, "jadwal_kp.jpg", $_FILES['jadwalFile']['tmp_name'])) {
                $statusMessageIMG = 'File Jadwal berhasil diunggah!';
                $alertClassIMG = 'alert-success';
            } else {
                $statusMessageIMG = 'Gagal mengunggah file Jadwal. Silakan coba lagi.';
                $alertClassIMG = 'alert-danger';
            }
        } else {
            $statusMessageIMG = 'Format file Jadwal tidak valid. Hanya JPG, JPEG, PNG, atau GIF yang diperbolehkan.';
            $alertClassIMG = 'alert-warning';
        }
    } elseif (isset($_POST['uploadPedoman'])) {
        $fileType = strtolower(pathinfo($_FILES['pedomanFile']['name'], PATHINFO_EXTENSION));

        if ($fileType == 'pdf') {
            if (replaceFile($uploadDir, "pedoman_kp.pdf", $_FILES['pedomanFile']['tmp_name'])) {
                $statusMessagePDF = 'File Pedoman berhasil diunggah!';
                $alertClassPDF = 'alert-success';
            } else {
                $statusMessagePDF = 'Gagal mengunggah file Pedoman. Silakan coba lagi.';
                $alertClassPDF = 'alert-danger';
            }
        } else {
            $statusMessagePDF = 'Format file Pedoman tidak valid. Hanya PDF yang diperbolehkan.';
            $alertClassPDF = 'alert-warning';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../../../css/adminlte.min.css" crossorigin="anonymous"/>
    <script src="../../../js/adminlte.min.js" crossorigin="anonymous"></script>
    <style>
        .alert-fixed {
            position: fixed;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1050;
            margin: 10px 0;
            width: auto;
            max-width: 80%;
        }
    </style>
</head>
<body>
    <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
        <div class="app-wrapper">
            <nav class="app-header navbar navbar-expand bg-body">
                <div class="container-fluid">
                    <h5 class="brand-text text-dark ms-2 align-middle">PENGELOLAAN DATA KERJA PRAKTEK</h5>
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown user-menu">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                                <img src="/dist/assets/img/user2-160x160.jpg" class="user-image rounded-circle shadow" alt="User Image">
                                <span class="d-none d-md-inline">
                                    <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                                </span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                                <li class="user-header text-bg-primary">
                                    <img src="/dist/assets/img/user2-160x160.jpg" class="rounded-circle shadow" alt="User Image">
                                    <p>
                                        <?php echo htmlspecialchars($_SESSION['user_name']); ?> - Admin
                                        <small>Member since Nov. 2023</small>
                                    </p>
                                </li>
                                <li class="user-footer">
                                    <a href="#" class="btn btn-default btn-flat">Profile</a>
                                    <a href="../../logout.php" class="btn btn-default btn-flat float-end">Sign out</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
            <aside class="app-sidebar shadow" style="background-color: rgb(0, 0, 58); color: white;" data-bs-theme="dark">
                <div class="sidebar-brand bg-light">
                    <a href="./home.html" class="brand-link">
                        <img src="/dist/assets/img/LOGOFKOM.png" alt="AdminLTE Logo" class="brand-image opacity-75 shadow">
                    </a>
                </div>
                <div class="sidebar-wrapper">
                    <nav class="mt-2">
                        <ul class="nav sidebar-menu flex-column" role="menu">
                            <li class="nav-item"> <a href="infokp.php" class="nav-link"> <i class="nav-icon bi bi-circle"></i>
                                <p>Informasi KP</p>
                            </a> </li>
                            <li class="nav-item"> <a href="mahasiswa.php" class="nav-link"> <i class="nav-icon bi bi-circle"></i>
                                    <p>Data Mahasiswa</p>
                                </a> </li>
                            <li class="nav-item"> <a href="kelompok.php" class="nav-link"> <i class="nav-icon bi bi-circle"></i>
                                    <p>Data Kelompok</p>
                                </a> </li>
                            <li class="nav-item"> <a href="dosen.php" class="nav-link"> <i class="nav-icon bi bi-circle"></i>
                                    <p>Data Dosen</p>
                                </a> </li>
                            <li class="nav-item"> <a href="mitra.php" class="nav-link"> <i class="nav-icon bi bi-circle"></i>
                                    <p>Data Mitra</p>
                                </a> </li>
                            <!-- <li class="nav-item"> <a href="staff.php" class="nav-link"> <i class="nav-icon bi bi-circle"></i>
                                    <p>Data Staff</p>
                                </a> </li> -->
                            <li class="nav-item"> <a href="../../logout.php" class="nav-link"> <i class="nav-icon bi bi-circle"></i>
                                    <p>Logout</p>
                                </a> </li>
                        </ul>
                    </nav>
                </div>
            </aside>
            <main class="app-main">
                <div class="container-fluid">
                    <div class="row px-5 py-3" style="background-color: rgb(0, 0, 58); color: white;">
                        <div class="col-md-2">
                            <img src="/dist/assets/img/logo-fkom-putih.png" alt="logo" width="100%">
                        </div>
                        <div class="col-md-10">
                            <h1>KERJA PRAKTEK</h1>
                            <h3>Fakultas Ilmu Komputer 2024</h3>
                        </div>
                    </div>

                    <div class="row mt-3 px-5 py-3">
                        <h5 class="mb-3">Kerja Praktik Fakultas Ilmu Komputer Universitas Kuningan Tahun Akademik 2024/2025</h5>
                        <p class="mb-3">Kerja Praktek (KP) adalah salah satu mata kuliah wajib dan salah satu syarat sebelum melaksanakan penyusunan tugas akhir/skripsi. Kerja Praktek di Fakultas Ilmu Komputer mempunyai bobot 2 SKS dan dilaksanakan dengan melakukan proyek penelitian pada DU/DI (Dunia Usaha/Dunia Industri) yang berbadan hukum (memiliki ijin usaha minimal CV) atau UMKM yang telah memiliki ijin usaha, dengan dibimbing oleh 1 (satu) orang Pembimbing Lapangan yang ditunjuk oleh tempat KP (DU/DI) dan 1 (satu) orang Dosen Pembimbing yang di SK-kan oleh Dekan atas usulan Ketua Program Studi, dimana pembimbing tersebut memberikan bimbingan kepada mahasiswa selama kegiatan dan memberikan nilai akhir. Hasil dari KP berupa produk perangkat lunak (aplikasi ataupun sistem informasi) maupun produk desain komunikasi visual yang nantinya dapat digunakan untuk memecahkan permasalahan pada DU/DI dalam bentuk project-team based. Serta harus disusun menjadi sebuah Laporan Kerja Praktek dan diseminarkan di hadapan penguji KP yang ditunjuk dan di SK-kan oleh Dekan. <br><br>    

                        Nilai tambah dari kegiatan praktek ini bagi peserta diantaranya adalah mampu membentuk sikap mental/attitude dalam bekerja; Mampu mengidentifikasi, menganalisa dan merumuskan masalah selama berada di dunia kerja yang berdasarkan rasional tertentu yang dinilai penting dan bermanfaat ditinjau dari berbagai faktor; Mampu menganalisa, merancang dan mengembangkan sebuah perangkat lunak terapan maupun sistem informasi; Mampu melakukan tahapan metodologis dalam pembuatan produk dan karya desain komunikasi visual; Mampu mempresentasikan hasil Kerja Praktek ke dalam sebuah laporan yang tersusun secara sistematis sesuai dengan masalah yang diteliti serta mempertanggung jawabkannya.</p>
                    </div>

                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="row px-5 py-3">
                            <label for="jadwalFile" class="form-label fw-bold">Upload Jadwal Kerja Praktik (Gambar)</label>
                            <?php if (!empty($statusMessageIMG)): ?>
            <div class="alert <?php echo $alertClassIMG; ?> alert-dismissible fade show alert-fixed" role="alert" id="alertIMG">
                <?php echo $statusMessageIMG; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <script>
                setTimeout(function() {
                    document.getElementById('alertIMG').classList.remove('show');
                }, 3000);
            </script>
        <?php endif; ?>
                            <div class="col-9">
                                <input class="form-control" type="file" id="jadwalFile" name="jadwalFile" accept="image/*">
                            </div>
                            <div class="col-3">
                                <button type="submit" name="uploadJadwal" class="btn btn-primary">Submit</button>
                            </div>
                        </div>

                        <div class="row px-5 py-3">
                            <label for="pedomanFile" class="form-label fw-bold">Upload Pedoman Kerja Praktik (PDF)</label>
                            <?php if (!empty($statusMessagePDF)): ?>
            <div class="alert <?php echo $alertClassPDF; ?> alert-dismissible fade show alert-fixed" role="alert" id="alertPDF">
                <?php echo $statusMessagePDF; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <script>
                setTimeout(function() {
                    document.getElementById('alertPDF').classList.remove('show');
                }, 3000);
            </script>
        <?php endif; ?>
                            <div class="col-9">
                                <input class="form-control" type="file" id="pedomanFile" name="pedomanFile" accept="application/pdf">
                            </div>
                            <div class="col-3">
                                <button type="submit" name="uploadPedoman" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </body>
</html>
