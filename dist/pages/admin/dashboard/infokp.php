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

include '../../koneksi.php';

// Hitung jumlah mahasiswa
$resultMahasiswa = $conn->query("SELECT COUNT(*) AS total_mahasiswa FROM mahasiswa");
$totalMahasiswa = $resultMahasiswa->fetch_assoc()['total_mahasiswa'];

// Hitung jumlah kelompok yang memiliki mahasiswa melalui tabel kpconnection
$resultKelompok = $conn->query("
    SELECT COUNT(DISTINCT k.no_kelompok) AS total_kelompok
    FROM kpconnection kc
    INNER JOIN kelompok k ON kc.no_kelompok = k.no_kelompok
    INNER JOIN mahasiswa m ON kc.nim = m.nim
    WHERE kc.no_kelompok IS NOT NULL
");
$totalKelompok = $resultKelompok->fetch_assoc()['total_kelompok'];


// Hitung jumlah dosen
$resultDosen = $conn->query("SELECT COUNT(*) AS total_dosen FROM dosen");
$totalDosen = $resultDosen->fetch_assoc()['total_dosen'];

// Hitung jumlah mitra
// $resultMitra = $conn->query("SELECT COUNT(*) AS total_mitra FROM mitra");
$resultMitra = $conn->query("
    SELECT COUNT(DISTINCT m.id_mitra) AS total_mitra
    FROM kpconnection kc
    INNER JOIN kelompok k ON kc.no_kelompok = k.no_kelompok
    INNER JOIN mitra m ON kc.id_mitra = m.id_mitra
    WHERE kc.no_kelompok IS NOT NULL
");
$totalMitra = $resultMitra->fetch_assoc()['total_mitra'];

// Tutup koneksi
$conn->close();

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
                                <img src="../../../assets/img/user2-160x160.jpg" class="user-image rounded-circle shadow" alt="User Image">
                                <span class="d-none d-md-inline">
                                    <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                                </span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                                <li class="user-header text-bg-primary">
                                    <img src="../../../assets/img/user2-160x160.jpg" class="rounded-circle shadow" alt="User Image">
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
                    <a href="infokp.php" class="brand-link">
                        <img src="../../../assets/img/LOGOFKOM.png" alt="AdminLTE Logo" class="brand-image opacity-75 shadow">
                    </a>
                </div>
                <div class="sidebar-wrapper">
                    <nav class="mt-2">
                        <ul class="nav sidebar-menu flex-column" role="menu">
                            <li class="nav-item"> <a href="infokp.php" class="nav-link"> <i class="nav-icon bi bi-info-circle-fill"></i>
                                <p>Informasi KP</p>
                            </a> </li>
                            <li class="nav-item"> <a href="mahasiswa.php" class="nav-link"> <i class="nav-icon bi bi-person-fill"></i>
                                    <p>Data Mahasiswa</p>
                                </a> </li>
                            <li class="nav-item"> <a href="kelompok.php" class="nav-link"> <i class="nav-icon bi bi-people-fill"></i>
                                    <p>Data Kelompok</p>
                                </a> </li>
                            <li class="nav-item"> <a href="dosen.php" class="nav-link"> <i class="nav-icon bi bi-mortarboard-fill"></i>
                                    <p>Data Dosen</p>
                                </a> </li>
                            <li class="nav-item"> <a href="mitra.php" class="nav-link"> <i class="nav-icon bi bi-building-fill"></i>
                                    <p>Data Mitra</p>
                                </a> </li>
                            <!-- <li class="nav-item"> <a href="staff.php" class="nav-link"> <i class="nav-icon bi bi-circle"></i>
                                    <p>Data Staff</p>
                                </a> </li> -->
                            <li class="nav-item"> <a href="../../logout.php" class="nav-link"> <i class="nav-icon bi bi-box-arrow-left"></i>
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
                            <img src="../../../assets/img/logo-fkom-putih.png" alt="logo" width="100%">
                        </div>
                        <div class="col-md-10">
                            <h1>KERJA PRAKTEK</h1>
                            <h3>Fakultas Ilmu Komputer 2024</h3>
                        </div>
                    </div>
                    <div class="row mt-3 px-5 py-3"> <!--begin::Col-->
                        <div class="col-lg-3 col-6"> <!--begin::Small Box Widget 1-->
                            <div class="small-box text-bg-primary">
                                <div class="inner">
                                    <h3><?php echo $totalMahasiswa; ?></h3>
                                    <p>Mahasiswa Peserta KP</p>
                                </div> <a href="mahasiswa.php" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                                    More info <i class="bi bi-link-45deg"></i> </a>
                            </div> <!--end::Small Box Widget 1-->
                        </div> <!--end::Col-->
                        <div class="col-lg-3 col-6"> <!--begin::Small Box Widget 2-->
                            <div class="small-box text-bg-success">
                                <div class="inner">
                                    <h3><?php echo $totalKelompok; ?></h3>
                                    <p>Kelompok KP</p>
                                </div> <a href="kelompok.php" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                                    More info <i class="bi bi-link-45deg"></i> </a>
                            </div> <!--end::Small Box Widget 2-->
                        </div> <!--end::Col-->
                        <div class="col-lg-3 col-6"> <!--begin::Small Box Widget 3-->
                            <div class="small-box text-bg-warning">
                                <div class="inner">
                                    <h3><?php echo $totalDosen; ?></h3>
                                    <p>Dosen Pembimbing</p>
                                </div> <a href="dosen.php" class="small-box-footer link-dark link-underline-opacity-0 link-underline-opacity-50-hover">
                                    More info <i class="bi bi-link-45deg"></i> </a>
                            </div> <!--end::Small Box Widget 3-->
                        </div> <!--end::Col-->
                        <div class="col-lg-3 col-6"> <!--begin::Small Box Widget 4-->
                            <div class="small-box text-bg-danger">
                                <div class="inner">
                                    <h3><?php echo $totalMitra; ?></h3>
                                    <p>Mitra yang Bekerjasama</p>
                                </div><a href="mitra.php" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                                    More info <i class="bi bi-link-45deg"></i> </a>
                            </div> <!--end::Small Box Widget 4-->
                        </div> <!--end::Col-->
                    </div> <!--end::Row--> 

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
