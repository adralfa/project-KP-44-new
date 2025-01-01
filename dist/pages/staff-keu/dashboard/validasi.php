<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    error_reporting(0);
}
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'staff_keu') {
    header("Location: ../../index.php");
    exit;
}

// Include file koneksi ke database
include '../../koneksi.php';

// Variabel pencarian
$nimSearch = "";
if (isset($_GET['nim'])) {
    $nimSearch = $_GET['nim'];
}

// Query untuk mengambil data berdasarkan pencarian NIM (jika ada)
$sql = "SELECT * FROM mahasiswa WHERE nim LIKE ?";

// Menyiapkan query dan menghindari SQL Injection
$stmt = $conn->prepare($sql);
$searchTerm = "%$nimSearch%";
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
                            <li class="nav-item"> <a href="infokp.php" class="nav-link"> <i class="nav-icon bi bi-circle"></i> <p>Informasi KP</p> </a> </li>
                            <li class="nav-item"> <a href="validasi.php" class="nav-link"> <i class="nav-icon bi bi-circle"></i> <p>Data Validasi KP</p> </a> </li>
                            <li class="nav-item"> <a href="../../logout.php" class="nav-link"> <i class="nav-icon bi bi-circle"></i> <p>Logout</p> </a> </li>
                        </ul>
                    </nav>
                </div> 
            </aside> 
            <!-- App Main -->
            <main class="app-main">
                <div class="container-fluid px-5 py-3">
                    <h1>Data Validasi Keuangan KP</h1>
                    <div class="row d-flex align-items-center mt-5">
                        <div class="col-7 d-flex align-items-center justify-content-end">
                            <p>Cari NIM : </p>
                        </div>
                        <div class="col-5">
    <form method="get" action="">
        <div class="input-group mb-3">
            <input type="text" class="form-control" name="nim" value="<?php echo htmlspecialchars($nimSearch); ?>" placeholder="Cari..." aria-label="Cari NIM">
            <div class="input-group-append">
                <button class="btn btn-primary" type="submit" id="button-addon2">Cari</button>
            </div>
        </div>
    </form>
</div>

                    </div>
                    <!-- Table Data -->
                    <table class="table table-bordered mt-3">
                        <thead class="text-center">
                            <tr>
                                <th width="15%">NIM</th>
                                <th width="30%">Nama Mahasiswa</th>
                                <th width="10%">Kelas</th>
                                <th width="15%">No Telp</th>
                                <th width="15%">Link File</th>
                                <th width="15%">Status Validasi</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <?php
                                    // Gabungkan prodi, angkatan, dan kelas untuk kolom Kelas
                                    $kelas = $row['prodi'] . ' ' . $row['angkatan'] . ' ' . $row['kelas'];
                                    // Tentukan status validasi
                                    // $statusValidasi = $row['status_validasi'] == 1 ? 'Valid' : 'Tidak Valid';
                                ?>
                                
                                <tr>
                                    <td><?php echo htmlspecialchars($row['nim']); ?></td>
                                    <td class="text-start"><?php echo htmlspecialchars($row['nama']); ?></td>
                                    <td><?php echo htmlspecialchars($kelas); ?></td>
                                    <td><?php echo htmlspecialchars($row['telp']); ?></td>
                                    <td>
                                        <?php if ($row['file_upload']): ?>
                                            <a href="../../../assets/uploads/bukti-pembayaran/<?php echo htmlspecialchars($row['file_upload']); ?>" target="_blank">Lihat File</a>
                                        <?php else: ?>
                                            <p class="text-danger">No File</p>
                                        <?php endif; ?>
                                    </td>
                                    <form method="POST" action="<?= htmlspecialchars('update-status.php') . '?v=' . time(); ?>" id="form-status-<?= $row['nim'] ?>">
    <td>
        <input type="hidden" name="nim" value="<?= htmlspecialchars($row['nim']) ?>">
        <select name="status_validasi" class="form-select" onchange="submitForm('form-status-<?= $row['nim'] ?>')">
            <option value="1" <?= $row['status_validasi'] == 1 ? 'selected' : '' ?>>Valid</option>
            <option value="0" <?= $row['status_validasi'] == 0 ? 'selected' : '' ?>>Tidak Valid</option>
        </select>
    </td>
</form>

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
