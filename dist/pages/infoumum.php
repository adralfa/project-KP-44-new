<?php
// Direktori tempat file disimpan
$uploadDir = "../assets/uploads/";

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
    <link rel="stylesheet" href="../css/adminlte.min.css" crossorigin="anonymous"/>
    <script src="../js/adminlte.min.js" crossorigin="anonymous"></script>
</head>
<body>
    <body class="bg-body-tertiary"> <!--begin::App Wrapper-->
        <div class="app-wrapper"> <!--begin::Header-->
            <nav class="app-header navbar navbar-expand bg-body sticky-top"> <!--begin::Container-->
                <div class="container-fluid"> <!--begin::Start Navbar Links-->
                    <h5 class="brand-text text-dark ms-2 align-middle">KERJA PRAKTEK FAKULTAS ILMU KOMPUTER</h5> <!--end::Brand Text-->
                    <!-- <ul class="navbar-nav">
                        <li class="nav-item"> <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"> <i class="bi bi-list"></i> </a> </li>
                        <li class="nav-item d-none d-md-block"> <a href="#" class="nav-link">Home</a> </li>
                        <li class="nav-item d-none d-md-block"> <a href="#" class="nav-link">Contact</a> </li>
                    </ul> end::Start Navbar Links begin::End Navbar Links -->
                    <ul class="navbar-nav ms-auto"> <!--begin::Navbar Search-->
                        <a href="index.php" class="text-white border-none"><input type="button" class="btn btn-primary me-3" value="MASUK"></button></a>
                        <a href="pendaftaran.php" class="text-white border-none"><input type="button" class="btn btn-outline-primary" value="DAFTAR"></button></a>
                    </ul> <!--end::End Navbar Links-->
                </div> <!--end::Container-->
            </nav> <!--end::Header--> 
            <main class="app-main">
                <div class="container-fluid">
                    <div class="row px-5 py-5" style="background-color: rgb(0, 0, 58); color: white;">
                        <div class="col-lg-2 col-md-3 col-5 d-flex align-items-center">
                            <img src="/dist/assets/img/logo-fkom-putih.png" alt="logo" width="100%">
                        </div>
                        <div class="col-lg-10 col-md-9 col-7 d-flex align-items-center">
                            <div class="row ms-3">
                                <h1 class="fw-bold text-warning">KERJA PRAKTEK<br></h1>
                                <h3>Fakultas Ilmu Komputer 2024</h3>
                            </div>
                        </div>
                    </div>

                    <div class="row px-5 py-5">
                        <div class="row mt-3">
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