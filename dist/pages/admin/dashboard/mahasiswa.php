<?php
// Inisialisasi sesi jika belum dimulai
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
$searchKeyword = "";
$searchCategory = "nim"; // Default kategori pencarian adalah NIM

if (isset($_GET['search']) && isset($_GET['category'])) {
    $searchKeyword = $_GET['search'];
    $searchCategory = $_GET['category'];
}

// Validasi kategori pencarian
$allowedCategories = ['nim', 'no_kelompok'];
if (!in_array($searchCategory, $allowedCategories)) {
    $searchCategory = 'nim'; // Default jika kategori tidak valid
}

// Tentukan jumlah data per halaman
$itemsPerPage = 10;

// Ambil halaman yang diminta dari query string, default ke 1 jika tidak ada
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page); // Pastikan halaman minimal 1

// Hitung offset untuk query SQL
$offset = ($page - 1) * $itemsPerPage;

if ($searchCategory === 'nim') {
    $searchCategory = 'mahasiswa.nim';
} elseif ($searchCategory === 'no_kelompok') {
    $searchCategory = 'kelompok.no_kelompok';
}

// Query untuk mengambil data sesuai dengan pencarian dan halaman
$whereClause = !empty($searchKeyword) ? "WHERE $searchCategory = '" . $conn->real_escape_string($searchKeyword) . "'" : "";
$query = "
    SELECT mahasiswa.nim, mahasiswa.nama, mahasiswa.prodi, mahasiswa.angkatan, mahasiswa.kelas, mahasiswa.telp, 
           kelompok.no_kelompok
    FROM mahasiswa
    LEFT JOIN kpconnection ON mahasiswa.nim = kpconnection.nim
    LEFT JOIN kelompok ON kpconnection.no_kelompok = kelompok.no_kelompok
    $whereClause
    LIMIT $itemsPerPage OFFSET $offset
";

$result = $conn->query($query);

// Hitung total jumlah data
$totalQuery = "
    SELECT COUNT(*) AS total
    FROM mahasiswa
    LEFT JOIN kpconnection ON mahasiswa.nim = kpconnection.nim
    LEFT JOIN kelompok ON kpconnection.no_kelompok = kelompok.no_kelompok
    $whereClause
";
$totalResult = $conn->query($totalQuery);
$totalRow = $totalResult->fetch_assoc();
$totalItems = $totalRow['total'];
$totalPages = ceil($totalItems / $itemsPerPage);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mahasiswa</title>
    <link rel="stylesheet" href="../../../css/adminlte.min.css" crossorigin="anonymous"/>
    <script src="../../../js/adminlte.min.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        table.table td,
        table.table th {
            padding: 12px;
        }
    </style>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        <!-- Header -->
        <nav class="app-header navbar navbar-expand bg-body">
            <div class="container-fluid">
                <h5 class="brand-text text-dark ms-2 align-middle">PENGELOLAAN DATA KERJA PRAKTEK</h5>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown user-menu">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <img src="/dist/assets/img/user2-160x160.jpg" class="user-image rounded-circle shadow" alt="User Image">
                            <span class="d-none d-md-inline"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                            <li class="user-header text-bg-primary">
                                <img src="/dist/assets/img/user2-160x160.jpg" class="rounded-circle shadow" alt="User Image">
                                <p><?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
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
        <!-- Sidebar -->
        <aside class="app-sidebar shadow" style="background-color: rgb(0, 0, 58); color: white;" data-bs-theme="dark">
            <div class="sidebar-brand bg-light">
                    <a href="infokp.php" class="brand-link">
                        <img src="../../../assets/img/LOGOFKOM.png" alt="AdminLTE Logo" class="brand-image opacity-75 shadow">
                    </a>
                </div>
            <div class="sidebar-wrapper">
                <nav class="mt-2">
                    <ul class="nav sidebar-menu flex-column" role="menu">
                        <li class="nav-item">
                            <a href="infokp.php" class="nav-link">
                                <i class="nav-icon bi bi-info-circle-fill"></i>
                                <p>Informasi KP</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="mahasiswa.php" class="nav-link">
                                <i class="nav-icon bi bi-person-fill"></i>
                                <p>Data Mahasiswa</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="kelompok.php" class="nav-link">
                                <i class="nav-icon bi bi-people-fill"></i>
                                <p>Data Kelompok</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="dosen.php" class="nav-link">
                                <i class="nav-icon bi bi-mortarboard-fill"></i>
                                <p>Data Dosen</p>
                            </a>
                        </li>
                        <li class="nav-item"> <a href="mitra.php" class="nav-link"> <i class="nav-icon bi bi-building-fill"></i>
                                    <p>Data Mitra</p>
                                </a> </li>
                        <li class="nav-item">
                            <a href="../../logout.php" class="nav-link">
                                <i class="nav-icon bi bi-box-arrow-left"></i>
                                <p>Logout</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>
        <!-- Main Content -->
        <main class="app-main">
            <div class="container-fluid px-5 py-3">
                <h1>Data Mahasiswa</h1>
                <p class="mb-5">Data Mahasiswa Peserta Kerja Praktek</p>
                <form method="get" action="">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <p class="text-end">Cari berdasarkan : </p>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="category">
                                <option value="nim" <?php if ($searchCategory === 'nim') echo 'selected'; ?>>NIM</option>
                                <option value="no_kelompok" <?php if ($searchCategory === 'no_kelompok') echo 'selected'; ?>>Nomor Kelompok</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <input type="text" class="form-control" name="search" placeholder="Cari..." value="<?php echo htmlspecialchars($searchKeyword); ?>">
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-primary">Cari</button>
                        </div>
                    </div>
                </form>
                <table class="table table-bordered text-center">
    <thead>
        <tr>
            <th>No</th>
            <th>NIM</th>
            <th>Nama Mahasiswa</th>
            <th>Kelas</th>
            <th>Kelompok</th>
            <th>Telp</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php $no = $offset + 1; ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo htmlspecialchars($row['nim']); ?></td>
                    <td class="text-start"><?php echo htmlspecialchars($row['nama']); ?></td>
                    <td><?php echo htmlspecialchars($row['prodi'] . '-' . $row['angkatan'] . '-' . $row['kelas']); ?></td>
                    <td><?php echo htmlspecialchars($row['no_kelompok'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($row['telp']); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" class="text-center">Tidak ada data ditemukan</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

                <!-- Pagination -->
                <ul class="pagination pagination-sm m-0 float-end">
                    <li class="page-item <?php if ($page <= 1) echo 'disabled'; ?>">
                        <a class="page-link" href="?search=<?php echo urlencode($searchKeyword); ?>&category=<?php echo urlencode($searchCategory); ?>&page=<?php echo $page - 1; ?>">«</a>
                    </li>
                    <?php
                    $range_start = max(1, $page - 2);
                    $range_end = min($totalPages, $page + 2);

                    if ($range_start > 1) {
                        echo '<li class="page-item"><a class="page-link" href="?page=1">1</a></li>';
                        echo '<li class="page-item disabled"><a class="page-link">...</a></li>';
                    }

                    for ($i = $range_start; $i <= $range_end; $i++) {
                        echo '<li class="page-item ' . ($page == $i ? 'active' : '') . '">';
                        echo '<a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
                    }

                    if ($range_end < $totalPages) {
                        echo '<li class="page-item disabled"><a class="page-link">...</a></li>';
                        echo '<li class="page-item"><a class="page-link" href="?page=' . $totalPages . '">' . $totalPages . '</a></li>';
                    }
                    ?>
                    <li class="page-item <?php if ($page >= $totalPages) echo 'disabled'; ?>">
                        <a class="page-link" href="?search=<?php echo urlencode($searchKeyword); ?>&category=<?php echo urlencode($searchCategory); ?>&page=<?php echo $page + 1; ?>">»</a>
                    </li>
                </ul>
            </div>
        </main>
    </div>
</body>
</html>
