<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    error_reporting(0);
}
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'staff_umum') {
    header("Location: ../../index.php");
    exit;
}

// Include file koneksi ke database
include '../../koneksi.php';

// Variabel pencarian
$kelompokSearch = "";
if (isset($_GET['no_kelompok'])) {
    $kelompokSearch = $_GET['no_kelompok'];
}

// Query untuk mengambil data berdasarkan pencarian no_kelompok (jika ada) dan mengurutkan berdasarkan tanggal
$sql = "SELECT id, no_surat, tanggal, no_kelompok, file_name, status_cetak FROM surat WHERE no_kelompok LIKE ? && status_cetak = 0 ORDER BY tanggal DESC";

// Menyiapkan query dan menghindari SQL Injection
$stmt = $conn->prepare($sql);
$searchTerm = "%$kelompokSearch%";
$stmt->bind_param("s", $searchTerm);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../../../css/adminlte.min.css" crossorigin="anonymous"/>
    <script src="../../../js/adminlte.min.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <body class="layout-fixed sidebar-expand-lg bg-body-tertiary"> <!--begin::App Wrapper-->
        <div class="app-wrapper"> <!--begin::Header-->
            <nav class="app-header navbar navbar-expand bg-body"> <!--begin::Container-->
                <div class="container-fluid"> <!--begin::Start Navbar Links-->
                    <h5 class="brand-text text-dark ms-2 align-middle">PENGELOLAAN DATA KERJA PRAKTEK</h5> <!--end::Brand Text-->
                    <ul class="navbar-nav ms-auto"> <!--begin::Navbar Search-->
                        <li class="nav-item dropdown user-menu"> 
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"> 
                                <img src="/dist/assets/img/user2-160x160.jpg" class="user-image rounded-circle shadow" alt="User Image"> 
                                <span class="d-none d-md-inline"><?php echo htmlspecialchars($_SESSION['user_name']);?></span> 
                            </a>
                            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                                <li class="user-header text-bg-primary"> 
                                    <img src="/dist/assets/img/user2-160x160.jpg" class="rounded-circle shadow" alt="User Image">
                                    <p><?php echo htmlspecialchars($_SESSION['user_name']);?></p>
                                </li>
                                <li class="user-footer"> <a href="#" class="btn btn-default btn-flat">Profile</a> <a href="#" class="btn btn-default btn-flat float-end">Sign out</a> </li>
                            </ul>
                        </li>
                    </ul>
                </div> 
            </nav> 
            <!-- Sidebar -->
            <aside class="app-sidebar shadow" style="background-color: rgb(0, 0, 58); color: white;" data-bs-theme="dark">
                <div class="sidebar-brand bg-light">
                    <a href="./home.html" class="brand-link">
                        <img src="/dist/assets/img/LOGOFKOM.png" alt="AdminLTE Logo" class="brand-image opacity-75 shadow">
                    </a>
                </div>
                <div class="sidebar-wrapper">
                    <nav class="mt-2"> 
                        <ul class="nav sidebar-menu flex-column" role="menu">
                        <li class="nav-item"> <a href="infokp.php" class="nav-link"> <i class="nav-icon bi bi-info-circle-fill"></i>
                                <p>Informasi KP</p>
                            </a> </li>
                            <li class="nav-item"> <a href="permintaan-surat.php" class="nav-link"> <i class="nav-icon bi bi-envelope-arrow-up-fill"></i>
                                <p>Data Permintaan Surat</p>
                            </a> </li>
                            <li class="nav-item"> <a href="surat-keluar.php" class="nav-link"> <i class="nav-icon bi bi-envelope-arrow-down-fill"></i>
                                    <p>Data Surat Keluar</p>
                                </a> </li>
                            <li class="nav-item"> <a href="../../logout.php" class="nav-link"> <i class="nav-icon bi bi-box-arrow-left"></i>
                                    <p>Logout</p>
                                </a> </li>
                        </ul>
                    </nav>
                </div> 
            </aside> 
            <!-- App Main -->
            <main class="app-main">
                <div class="container-fluid px-5 py-3">
                    <h1>Data Permintaan Pembuatan Surat Penelitian</h1>
                    <div class="row mt-5">
            <div class="col-4">
                <a href="ekspor_csv.php" class="btn btn-success mb-3">Ekspor Data</a>

                </div>
                <div class="col-8">
                    <div class="row">
                        <form method="get" action="">
                            <div class="input-group mb-3">
                                    <p class="me-3">Cari No Kelompok: </p>
                                <input type="text" class="form-control" name="no_kelompok" value="<?php echo htmlspecialchars($kelompokSearch); ?>" placeholder="Cari..." aria-label="Cari No Kelompok">
                                <div class="input-group-append">
                            <button class="btn btn-primary" type="submit" id="button-addon2">Cari</button>
                        </div>
                    </div>
                </form>
                </div>
            </div>
        </div>
                    <!-- Table Data -->
                    <table class="table table-bordered mt-3">
                    <thead class="text-center">
                        <tr>
                            <th width="15%">No</th>
                            <th width="20%">No Surat</th>
                            <th width="25%">Tanggal</th>
                            <th width="20%">No Kelompok</th>
                            <th width="20%">File</th>
                            <th width="20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        <?php 
                        $no = 1;  // Menambahkan counter untuk nomor urut
                        while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>  <!-- Menampilkan nomor urut -->
                                <td><?php echo htmlspecialchars($row['no_surat']); ?></td>
                                <td><?php echo htmlspecialchars($row['tanggal']); ?></td>
                                <td><?php echo htmlspecialchars($row['no_kelompok']); ?></td>
                                <td>
                                    <?php if ($row['file_name']): ?>
                                        <a href="../../../assets/uploads/surat_output/<?php echo htmlspecialchars($row['file_name']); ?>" target="_blank">Lihat File</a>
                                    <?php else: ?>
                                        <p class="text-danger">No File</p>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($row['status_cetak'] == 0): ?>
                                        <a href="cetak_surat.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">Cetak</a>
                                    <?php else: ?>
                                        <p class="text-success">Sudah Dicetak</p>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                    <ul class="pagination pagination-sm m-0 float-end">
                        <li class="page-item"> <a class="page-link" href="#">«</a> </li>
                        <li class="page-item"> <a class="page-link" href="#">1</a> </li>
                        <li class="page-item"> <a class="page-link" href="#">2</a> </li>
                        <li class="page-item"> <a class="page-link" href="#">3</a> </li>
                        <li class="page-item"> <a class="page-link" href="#">»</a> </li>
                    </ul>
                </div>
            </main>
            <footer class="app-footer"> 
                <strong>&copy; 2024 Kelompok 44 Kerja Praktek Universitas Kuningan 2024</strong> All rights reserved.
            </footer>
        </div>
        <script>
            function submitForm(formId) {
                document.getElementById(formId).submit();
            }           
        </script>

    </body>
</html>
