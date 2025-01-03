<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
    error_reporting(0);
}

// Cek apakah pengguna memiliki hak akses admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../../index.php");
    exit;
}

// Konfigurasi koneksi database
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

// Pagination konfigurasi
$limit = 8; // Jumlah data per halaman
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Query untuk mengambil data mahasiswa dan kelompok
$sql = "
    SELECT 
        m.nim, 
        m.nama AS nama_mahasiswa, 
        m.kelas, 
        k.no_kelompok
    FROM mahasiswa m
    LEFT JOIN kpconnection kc ON m.nim = kc.nim
    LEFT JOIN kelompok k ON kc.no_kelompok = k.no_kelompok
    WHERE m.nim LIKE ?
    LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sii", $searchTerm, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();


// Menghitung total data
$total_sql = "
    SELECT COUNT(DISTINCT m.nim) AS total 
    FROM mahasiswa m
    LEFT JOIN kpconnection kc ON m.nim = kc.nim
    LEFT JOIN kelompok k ON kc.no_kelompok = k.no_kelompok
    WHERE m.nim LIKE ?";
$total_stmt = $conn->prepare($total_sql);
$total_stmt->bind_param("s", $searchTerm);
$total_stmt->execute();
$total_result = $total_stmt->get_result();
$total_row = $total_result->fetch_assoc();
$total_data = $total_row['total'];
$total_pages = ceil($total_data / $limit);
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
            <h1>Data Mahasiswa</h1>
            <p>Data Mahasiswa yang Mendaftar KP</p>
            <div class="row">
                <div class="col-6 d-flex align-items-center justify-content-end">
                    <p>Cari NIM : </p>
                </div>
                <div class="col-4">
                    <form method="get" action="">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="nim" value="<?php echo htmlspecialchars($nimSearch); ?>" placeholder="Cari..." aria-label="Cari NIM">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit" id="button-addon2">Cari</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-2">
                    <button class="btn btn-primary" type="button">Tambah Mahasiswa</button>
                </div>
</div>
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>NIM</th>
                        <th>Nama Mahasiswa</th>
                        <th>Kelas</th>
                        <th>Kelompok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr class="align-middle">
                                <td><?php echo htmlspecialchars($row['nim']); ?></td>
                                <td><?php echo htmlspecialchars($row['nama_mahasiswa']); ?></td>
                                <td><?php echo htmlspecialchars($row['kelas']); ?></td>
                                <td><?php echo htmlspecialchars($row['no_kelompok'] ?: '-'); ?></td>
                                <td>
                                    <button class="btn btn-primary" type="button">Ubah</button>
                                    <button class="btn btn-danger" type="button">Hapus</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada data ditemukan</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <ul class="pagination pagination-sm m-0 float-end">
    <!-- Tombol Previous -->
    <li class="page-item <?php if ($page <= 1) echo 'disabled'; ?>">
        <a class="page-link" href="?nim=<?php echo urlencode($nimSearch); ?>&page=<?php echo $page - 1; ?>">«</a>
    </li>
    
    <!-- Tombol angka halaman dengan ... -->
    <?php 
    $pagination_limit = 5; // Jumlah maksimal angka halaman yang akan ditampilkan
    $range_start = max(1, $page - 2); // Menentukan awal range
    $range_end = min($total_pages, $page + 2); // Menentukan akhir range

    // Halaman sebelumnya dengan "..."
    if ($range_start > 1) {
        echo '<li class="page-item"><a class="page-link" href="?nim=' . urlencode($nimSearch) . '&page=1">1</a></li>';
        echo '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
    }

    // Menampilkan halaman dalam range
    for ($i = $range_start; $i <= $range_end; $i++) {
        echo '<li class="page-item ' . ($page == $i ? 'active' : '') . '">';
        echo '<a class="page-link" href="?nim=' . urlencode($nimSearch) . '&page=' . $i . '">' . $i . '</a>';
        echo '</li>';
    }

    // Halaman selanjutnya dengan "..."
    if ($range_end < $total_pages) {
        echo '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
        echo '<li class="page-item"><a class="page-link" href="?nim=' . urlencode($nimSearch) . '&page=' . $total_pages . '">' . $total_pages . '</a></li>';
    }
    ?>

    <!-- Tombol Next -->
    <li class="page-item <?php if ($page >= $total_pages) echo 'disabled'; ?>">
        <a class="page-link" href="?nim=<?php echo urlencode($nimSearch); ?>&page=<?php echo $page + 1; ?>">»</a>
    </li>
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