<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
//     ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
    error_reporting(0);

}

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'mahasiswa') {
    header("Location: ../../index.php");
    exit;
}

include '../../koneksi.php';

// Cek apakah mahasiswa memiliki kelompok
if($_SESSION['no_kelompok'] == '-') {
    $is_no_kelompok_empty = 1;
}

// Query untuk memeriksa apakah ada anggota dengan status_validasi = 0
$query = "
    SELECT COUNT(*) AS count
    FROM kpconnection kc
    INNER JOIN mahasiswa m ON kc.nim = m.nim
    INNER JOIN kelompok k ON kc.no_kelompok = k.no_kelompok
    WHERE k.no_kelompok = ? AND m.status_validasi = 0
";

$stmt = $conn->prepare($query);

if ($stmt) {
    // Bind parameter (asumsikan no_kelompok adalah integer)
    $stmt->bind_param("i", $_SESSION['no_kelompok']);
    
    // Jalankan statement
    $stmt->execute();

    // Ambil hasilnya
    $result = $stmt->get_result();

    // Fetch sebagai array asosiatif
    $data = $result->fetch_assoc();

    // Akses data dengan key 'count'
    $is_disabled = $data['count'] > 0;

    // Tutup statement
    $stmt->close();
} else {
    die("Query failed: " . $conn->error);
}


// Direktori tempat file disimpan
$uploadDir = "../../../assets/uploads/";

// Nama file default
$imageFile = $uploadDir . "jadwal_kp.jpg";
$pdfFile = $uploadDir . "pedoman_kp.pdf";

// Cek keberadaan file
$imageExists = file_exists($imageFile);
$pdfExists = file_exists($pdfFile);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../../../css/adminlte.min.css" crossorigin="anonymous"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="../../../js/adminlte.min.js" crossorigin="anonymous"></script>
    <style>
        .nav-link.disabled {
    pointer-events: none; /* Mencegah klik */
    color: #6c757d; /* Warna abu-abu untuk tampilan disable */
    cursor: not-allowed; /* Ubah kursor menjadi tanda larangan */
}

    </style>
</head>
<body>
    <body class="layout-fixed sidebar-expand-lg"> <!--begin::App Wrapper-->
        <div class="app-wrapper"> <!--begin::Header-->
            <nav class="app-header navbar navbar-expand bg-body"> <!--begin::Container-->
                <div class="container-fluid"> <!--begin::Start Navbar Links-->
                    <h5 class="brand-text text-dark ms-2 align-middle">KERJA PRAKTEK FAKULTAS ILMU KOMPUTER</h5> <!--end::Brand Text-->
                    <!-- <ul class="navbar-nav">
                        <li class="nav-item"> <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"> <i class="bi bi-list"></i> </a> </li>
                        <li class="nav-item d-none d-md-block"> <a href="#" class="nav-link">Home</a> </li>
                        <li class="nav-item d-none d-md-block"> <a href="#" class="nav-link">Contact</a> </li>
                    </ul> end::Start Navbar Links begin::End Navbar Links -->
                    <ul class="navbar-nav ms-auto"> <!--begin::Navbar Search-->
                        <!--begin::User Menu Dropdown-->
                        <li class="nav-item dropdown user-menu"> <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"> <img src="/dist/assets/img/user2-160x160.jpg" class="user-image rounded-circle shadow" alt="User Image"> <span class="d-none d-md-inline"><?php echo htmlspecialchars($_SESSION['user_name']);?></span> </a>
                            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end"> <!--begin::User Image-->
                                <li class="user-header text-bg-primary"> <img src="/dist/assets/img/user2-160x160.jpg" class="rounded-circle shadow" alt="User Image">
                                    <p>
                                    <?php echo htmlspecialchars($_SESSION['user_name']);?>
                                        <small>Member since Nov. 2023</small>
                                    </p>
                                </li> <!--end::User Image--> <!--begin::Menu Body-->
                                <li class="user-body"> <!--begin::Row-->
                                    <div class="row">
                                        <div class="col-4 text-center"> <a href="#">Followers</a> </div>
                                        <div class="col-4 text-center"> <a href="#">Sales</a> </div>
                                        <div class="col-4 text-center"> <a href="#">Friends</a> </div>
                                    </div> <!--end::Row-->
                                </li> <!--end::Menu Body--> <!--begin::Menu Footer-->
                                <li class="user-footer"> <a href="#" class="btn btn-default btn-flat">Profile</a> <a href="#" class="btn btn-default btn-flat float-end">Sign out</a> </li> <!--end::Menu Footer-->
                            </ul>
                        </li> <!--end::User Menu Dropdown-->
                    </ul> <!--end::End Navbar Links-->
                </div> <!--end::Container-->
            </nav> <!--end::Header--> <!--begin::Sidebar-->
            <aside class="app-sidebar shadow" style="background-color: rgb(0, 0, 58); color: white;" data-bs-theme="dark"> <!--begin::Sidebar Brand-->
                <div class="sidebar-brand bg-light"> <!--begin::Brand Link--> <a href="./home.html" class="brand-link"> <!--begin::Brand Image--> <img src="/dist/assets/img/LOGOFKOM.png" alt="AdminLTE Logo" class="brand-image opacity-75 shadow"> <!--end::Brand Image--> <!--begin::Brand Text-->  </a> <!--end::Brand Link--> </div> <!--end::Sidebar Brand--> <!--begin::Sidebar Wrapper-->
                <div class="sidebar-wrapper">
                    <nav class="mt-2"> <!--begin::Sidebar Menu-->
                        <ul class="nav sidebar-menu flex-column" role="menu">
                            <li class="nav-item"> <a href="infokp.php" class="nav-link"><i class="nav-icon bi bi-info-circle-fill"></i>
                                <p>Informasi KP</p>
                            </a> </li>
                            <li class="nav-item"> <a href="infomhs.php" class="nav-link"> <i class="nav-icon bi bi-person-fill"></i>
                                    <p>Info Mahasiswa</p>
                                </a> </li>
                            <li class="nav-item">  <a href="infokelompok.php" 
       class="nav-link <?php echo $is_no_kelompok_empty == 1 ? 'disabled' : ''; ?>" 
       <?php echo $is_no_kelompok_empty == 1 ? 'tabindex="-1" aria-disabled="true"' : ''; ?>>
        <i class="nav-icon bi bi-people-fill"></i>
        <p>Info Kelompok</p>
    </a> </li>
                            <li class="nav-item"><a href="persuratan.php" 
       class="nav-link <?php echo ($is_no_kelompok_empty == 1 || $is_disabled) ? 'disabled' : ''; ?>" 
       <?php echo ($is_no_kelompok_empty == 1 || $is_disabled) ? 'tabindex="-1" aria-disabled="true"' : ''; ?>>
        <i class="nav-icon bi bi-envelope-fill"></i>
        <p>Persuratan</p>
    </a></li>
                            <li class="nav-item"> <a href="../../logout.php" class="nav-link"> <i class="nav-icon bi bi-box-arrow-left"></i>
                                    <p>Logout</p>
                                </a> </li>
                        </ul>
                    </nav>
                </div> <!--end::Sidebar Wrapper-->
            </aside> <!--end::Sidebar--> <!--begin::App Main-->
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
                        <div class="col-lg-8">
                            <h5 class="mb-3 fw-bold">Kerja Praktik Fakultas Ilmu Komputer Universitas Kuningan Tahun Akademik 2024/2025</h5>
                        <p class="mb-3">Kerja Praktek (KP) adalah salah satu mata kuliah wajib dan salah satu syarat sebelum melaksanakan penyusunan tugas akhir/skripsi. Kerja Praktek di Fakultas Ilmu Komputer mempunyai bobot 2 SKS dan dilaksanakan dengan melakukan proyek penelitian pada DU/DI (Dunia Usaha/Dunia Industri) yang berbadan hukum (memiliki ijin usaha minimal CV) atau UMKM yang telah memiliki ijin usaha, dengan dibimbing oleh 1 (satu) orang Pembimbing Lapangan yang ditunjuk oleh tempat KP (DU/DI) dan 1 (satu) orang Dosen Pembimbing yang di SK-kan oleh Dekan atas usulan Ketua Program Studi, dimana pembimbing tersebut memberikan bimbingan kepada mahasiswa selama kegiatan dan memberikan nilai akhir. Hasil dari KP berupa produk perangkat lunak (aplikasi ataupun sistem informasi) maupun produk desain komunikasi visual yang nantinya dapat digunakan untuk memecahkan permasalahan pada DU/DI dalam bentuk project-team based. Serta harus disusun menjadi sebuah Laporan Kerja Praktek dan diseminarkan di hadapan penguji KP yang ditunjuk dan di SK-kan oleh Dekan. <br><br>    

                        Nilai tambah dari kegiatan praktek ini bagi peserta diantaranya adalah mampu membentuk sikap mental/attitude dalam bekerja; Mampu mengidentifikasi, menganalisa dan merumuskan masalah selama berada di dunia kerja yang berdasarkan rasional tertentu yang dinilai penting dan bermanfaat ditinjau dari berbagai faktor; Mampu menganalisa, merancang dan mengembangkan sebuah perangkat lunak terapan maupun sistem informasi; Mampu melakukan tahapan metodologis dalam pembuatan produk dan karya desain komunikasi visual; Mampu mempresentasikan hasil Kerja Praktek ke dalam sebuah laporan yang tersusun secara sistematis sesuai dengan masalah yang diteliti serta mempertanggung jawabkannya.</p>
                        </div>
                        <div class="col-lg-4">
                            <p class="text-center fw-bold">Informasi Jadwal Pelaksanaan Kerja Praktek</p>
                            <div class="d-flex justify-content-center">
                                <!-- <img src="/dist/assets/img/Jadwal KP.jpg" alt="jadwal" style="width: 100%;"> -->
                                <?php if ($imageExists): ?>
    <img src="<?php echo $imageFile . '?v=' . time(); ?>" alt="Jadwal KP" class="img-fluid shadow-sm" style="width: 100%;">
<?php else: ?>
    <p class="text-danger">File gambar jadwal belum diunggah.</p>
<?php endif; ?>

                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row mt-3 px-5 py-3">
                        <div class="d-flex justify-content-center">
                            <!-- <embed type="application/pdf" src="/dist/assets/pdf/Pedoman-KP-2023-2024-fix-3.pdf" width="80%" height="600"></embed> -->
                            <?php if ($pdfExists): ?>
                    <embed type="application/pdf" src="<?php echo $pdfFile. '?v=' . time(); ?>" width="100%" height="600"></embed>
                <?php else: ?>
                    <p class="text-danger">File pedoman belum diunggah.</p>
                <?php endif; ?>
                        </div>
                    </div>
                </div>
            </main>
            <footer class="app-footer"> <!--begin::To the end-->
                <!--begin::Copyright--> <strong>
                    Copyright &copy; 2024&nbsp; Kelompok 44 Kerja Praktek Universitas Kuningan 2024</a>.
                </strong>
                All rights reserved.
                <!--end::Copyright-->
            </footer> <!--end::Footer-->
        </div>
    </body><!--end::Body-->
</body>
</html>