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

// Mengambil nilai pencarian berdasarkan pilihan
$searchBy = isset($_GET['search_by']) ? $_GET['search_by'] : 'nim';
$searchTerm = isset($_GET['search_term']) ? $_GET['search_term'] : '';

// Pagination konfigurasi
$limit = 8; // Jumlah data per halaman
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Menentukan kolom yang akan digunakan untuk pencarian
if ($searchBy == 'kelompok') {
    if (empty($searchTerm)) {
        $sql = "
            SELECT 
                m.nim, 
                m.nama AS nama_mahasiswa,
                m.prodi,
                m.angkatan, 
                m.kelas,
                m.telp,
                m.file_upload,
                m.status_validasi,    
                k.no_kelompok
            FROM mahasiswa m
            LEFT JOIN kpconnection kc ON m.nim = kc.nim
            LEFT JOIN kelompok k ON kc.no_kelompok = k.no_kelompok
            LIMIT ? OFFSET ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $limit, $offset);
    } else {
        $sql = "
            SELECT 
                m.nim, 
                m.nama AS nama_mahasiswa,
                m.prodi,
                m.angkatan, 
                m.kelas,
                m.telp,
                m.file_upload,
                m.status_validasi,    
                k.no_kelompok
            FROM mahasiswa m
            LEFT JOIN kpconnection kc ON m.nim = kc.nim
            LEFT JOIN kelompok k ON kc.no_kelompok = k.no_kelompok
            WHERE k.no_kelompok LIKE ? 
            LIMIT ? OFFSET ?";
        $stmt = $conn->prepare($sql);
        $searchTermWithWildcard = "%" . $searchTerm . "%";
        $stmt->bind_param("sii", $searchTermWithWildcard, $limit, $offset);
    }
} else {
    // Default pencarian berdasarkan NIM
    if (empty($searchTerm)) {
        $sql = "
            SELECT 
                m.nim, 
                m.nama AS nama_mahasiswa,
                m.prodi,
                m.angkatan, 
                m.kelas,
                m.telp,
                m.file_upload,
                m.status_validasi,    
                k.no_kelompok
            FROM mahasiswa m
            LEFT JOIN kpconnection kc ON m.nim = kc.nim
            LEFT JOIN kelompok k ON kc.no_kelompok = k.no_kelompok
            LIMIT ? OFFSET ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $limit, $offset);
    } else {
        $sql = "
            SELECT 
                m.nim, 
                m.nama AS nama_mahasiswa,
                m.prodi,
                m.angkatan, 
                m.kelas,
                m.telp,
                m.file_upload,
                m.status_validasi,    
                k.no_kelompok
            FROM mahasiswa m
            LEFT JOIN kpconnection kc ON m.nim = kc.nim
            LEFT JOIN kelompok k ON kc.no_kelompok = k.no_kelompok
            WHERE m.nim LIKE ? 
            LIMIT ? OFFSET ?";
        $stmt = $conn->prepare($sql);
        $searchTermWithWildcard = "%" . $searchTerm . "%";
        $stmt->bind_param("sii", $searchTermWithWildcard, $limit, $offset);
    }
}

// Execute the query
$stmt->execute();
$result = $stmt->get_result();

// Menghitung total data berdasarkan pencarian
if (empty($searchTerm)) {
    $total_sql = "
        SELECT COUNT(DISTINCT m.nim) AS total 
        FROM mahasiswa m
        LEFT JOIN kpconnection kc ON m.nim = kc.nim
        LEFT JOIN kelompok k ON kc.no_kelompok = k.no_kelompok";
    $total_stmt = $conn->prepare($total_sql);
    $total_stmt->execute();
} else {
    if ($searchBy == 'kelompok') {
        $total_sql = "
            SELECT COUNT(DISTINCT m.nim) AS total 
            FROM mahasiswa m
            LEFT JOIN kpconnection kc ON m.nim = kc.nim
            LEFT JOIN kelompok k ON kc.no_kelompok = k.no_kelompok
            WHERE k.no_kelompok LIKE ?";
    } else {
        $total_sql = "
            SELECT COUNT(DISTINCT m.nim) AS total 
            FROM mahasiswa m
            LEFT JOIN kpconnection kc ON m.nim = kc.nim
            LEFT JOIN kelompok k ON kc.no_kelompok = k.no_kelompok
            WHERE m.nim LIKE ?";
    }
    $total_stmt = $conn->prepare($total_sql);
    $total_stmt->bind_param("s", $searchTermWithWildcard);
}
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
                            <li class="nav-item"> <a href="validasi.php" class="nav-link"> <i class="nav-icon bi bi-receipt-cutoff"></i>
                                    <p>Data Validasi KP</p>
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
                    <h1>Data Validasi Keuangan KP</h1>
                    <div class="row d-flex align-items-center mt-5">
                        <div class="col-5 d-flex align-items-center justify-content-end">
                            <p>Cari berdasarkan: </p>
                        </div>
                        <div class="col-7">
                            <form method="get" action="">
                                <div class="input-group mb-3">
                                    <select class="form-select" name="search_by">
                                        <option value="nim" <?php echo (isset($_GET['search_by']) && $_GET['search_by'] == 'nim') ? 'selected' : ''; ?>>NIM</option>
                                        <option value="kelompok" <?php echo (isset($_GET['search_by']) && $_GET['search_by'] == 'kelompok') ? 'selected' : ''; ?>>No Kelompok</option>
                                    </select>
                                    <input type="text" class="form-control" name="search_term" value="<?php echo htmlspecialchars($searchTerm); ?>" placeholder="Cari..." aria-label="Cari">
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
                                    $kelas = $row['prodi'] . '-' . $row['angkatan'] . '-' . $row['kelas'];
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['nim']); ?></td>
                                    <td class="text-start"><?php echo htmlspecialchars($row['nama_mahasiswa']); ?></td>
                                    <td><?php echo htmlspecialchars($kelas); ?></td>
                                    <td><?php echo htmlspecialchars($row['telp']); ?></td>
                                    <td>
                                        <?php if ($row['file_upload']): ?>
                                            <a href="../../../assets/uploads/bukti-pembayaran/<?php echo htmlspecialchars($row['file_upload']); ?>" target="_blank">Lihat File</a>
                                        <?php else: ?>
                                            <p class="text-danger">No File</p>
                                        <?php endif; ?>
                                    </td>
                                    <form method="POST" action="update-status.php" id="form-status-<?= $row['nim'] ?>">
                                        <input type="hidden" name="nim" value="<?= htmlspecialchars($row['nim']) ?>">
                                        <td>
                                            <select name="status_validasi" class="form-select" onchange="document.getElementById('form-status-<?= $row['nim'] ?>').submit()" 
                                                <?php echo $row['file_upload'] ? '' : 'disabled'; ?>>
                                                <option value="1" <?= $row['status_validasi'] == 1 ? 'selected' : '' ?>>Valid</option>
                                                <option value="0" <?= $row['status_validasi'] == 0 ? 'selected' : '' ?>>Tidak Valid</option>
                                            </select>
                                        </td>
                                    </form>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <!-- Pagination Controls -->
                    <ul class="pagination pagination-sm m-0 float-end">
    <!-- Tombol Previous -->
    <li class="page-item <?php if ($page <= 1) echo 'disabled'; ?>">
        <a class="page-link" href="?search_by=<?php echo urlencode($searchBy); ?>&search_term=<?php echo urlencode($searchTerm); ?>&page=<?php echo $page - 1; ?>">«</a>
    </li>

    <!-- Tombol angka halaman dengan ... -->
    <?php 
    $pagination_limit = 5; // Jumlah maksimal angka halaman yang akan ditampilkan
    $range_start = max(1, $page - 2); // Menentukan awal range
    $range_end = min($total_pages, $page + 2); // Menentukan akhir range

    // Halaman sebelumnya dengan "..."
    if ($range_start > 1) {
        echo '<li class="page-item"><a class="page-link" href="?search_by=' . urlencode($searchBy) . '&search_term=' . urlencode($searchTerm) . '&page=1">1</a></li>';
        echo '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
    }

    // Menampilkan halaman dalam range
    for ($i = $range_start; $i <= $range_end; $i++) {
        echo '<li class="page-item ' . ($page == $i ? 'active' : '') . '">';
        echo '<a class="page-link" href="?search_by=' . urlencode($searchBy) . '&search_term=' . urlencode($searchTerm) . '&page=' . $i . '">' . $i . '</a>';
        echo '</li>';
    }

    // Halaman selanjutnya dengan "..."
    if ($range_end < $total_pages) {
        echo '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
        echo '<li class="page-item"><a class="page-link" href="?search_by=' . urlencode($searchBy) . '&search_term=' . urlencode($searchTerm) . '&page=' . $total_pages . '">' . $total_pages . '</a></li>';
    }
    ?>

    <!-- Tombol Next -->
    <li class="page-item <?php if ($page >= $total_pages) echo 'disabled'; ?>">
        <a class="page-link" href="?search_by=<?php echo urlencode($searchBy); ?>&search_term=<?php echo urlencode($searchTerm); ?>&page=<?php echo $page + 1; ?>">»</a>
    </li>
</ul>

                </div>
            </main>
            <footer class="app-footer"> 
                <strong>&copy; 2024 Kelompok 44 Kerja Praktek Universitas Kuningan 2024</strong> All rights reserved.
            </footer>
        </div>
    </body>
</html>
