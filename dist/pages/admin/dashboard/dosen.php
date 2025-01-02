<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
    error_reporting(0);
}

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../../index.php");
    exit;
}

// Include file koneksi database
include '../../koneksi.php';

// Query untuk mengambil data dosen dari database
$sql = "
    SELECT d.nik, d.nama_dosen, GROUP_CONCAT(DISTINCT kc.no_kelompok ORDER BY kc.no_kelompok ASC SEPARATOR ', ') AS kelompok
    FROM dosen d
    JOIN kpconnection kc ON d.nik = kc.nik
    GROUP BY d.nik, d.nama_dosen
";  
$result = $conn->query($sql);

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
            </aside><!--begin::App Main-->
            <main class="app-main">
                <div class="container-fluid px-5 py-3">
                    <h1>Data Dosen Pembimbing Lapangan</h1>
                    <p>Data Dosen Pembimbing Lapangan Kerja Praktek</p>
                    <button class="btn btn-primary" type="button">Tambah Dosen</button>
                    <table class="table table-bordered mt-3">
                        <thead>
                            <tr>
                                <th>NIK</th>
                                <th>Nama Dosen</th>
                                <th>Kelompok</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Menampilkan data dosen
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr class='align-middle'>";
                                    echo "<td>" . $row['nik'] . "</td>";
                                    echo "<td>" . $row['nama_dosen'] . "</td>";
                                    echo "<td>" . $row['kelompok'] . "</td>"; // Kolom kelompok kosong untuk sementara
                                    echo "<td><button class='btn btn-primary' type='button'>Ubah</button>
                                          <button class='btn btn-danger' type='button'>Hapus</button></td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4'>Data tidak ditemukan.</td></tr>";
                            }
                            ?>
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

<?php
$conn->close();
?>
