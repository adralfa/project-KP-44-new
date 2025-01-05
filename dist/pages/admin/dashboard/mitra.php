<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    error_reporting(0);
}
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../../index.php");
    exit;
}

// Konfigurasi koneksi database
include '../../koneksi.php';

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Ambil nilai pencarian dari form
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Query untuk mendapatkan data mitra yang terhubung dengan kelompok
$query_search = "
    SELECT 
        m.id_mitra, 
        m.nama_mitra, 
        m.lokasi, 
        GROUP_CONCAT(DISTINCT kc.no_kelompok ORDER BY kc.no_kelompok ASC SEPARATOR ', ') AS no_kelompok
    FROM mitra m
    INNER JOIN kpconnection kc ON m.id_mitra = kc.id_mitra
    WHERE m.nama_mitra LIKE ? OR kc.no_kelompok LIKE ?
    GROUP BY m.id_mitra, m.nama_mitra, m.lokasi
";

// Menyiapkan query dengan parameter pencarian
$stmt = mysqli_prepare($conn, $query_search);
$searchTerm = '%' . $search . '%';
mysqli_stmt_bind_param($stmt, 'ss', $searchTerm, $searchTerm);

// Eksekusi query
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Periksa jika query gagal
if (!$result) {
    die("Query gagal: " . mysqli_error($conn));
}

// Konfigurasi pagination
$perPage = 10; // Jumlah data per halaman
$page = isset($_GET['page']) ? $_GET['page'] : 1; // Halaman yang sedang aktif
$start = ($page - 1) * $perPage; // Posisi mulai data

// Ambil nilai pencarian dari form
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Query untuk menghitung total data mitra (untuk pagination)
$totalQuery = "
    SELECT COUNT(DISTINCT m.id_mitra) AS total
    FROM mitra m
    INNER JOIN kpconnection kc ON m.id_mitra = kc.id_mitra
    WHERE m.nama_mitra LIKE ? OR kc.no_kelompok LIKE ?
";
$stmtTotal = mysqli_prepare($conn, $totalQuery);
$searchTerm = '%' . $search . '%';
mysqli_stmt_bind_param($stmtTotal, 'ss', $searchTerm, $searchTerm);
mysqli_stmt_execute($stmtTotal);
$totalResult = mysqli_stmt_get_result($stmtTotal);
$totalData = mysqli_fetch_assoc($totalResult)['total'];
$totalPages = ceil($totalData / $perPage); // Menghitung total halaman

// Query untuk mendapatkan data mitra berdasarkan pencarian dan pagination
$query = "
    SELECT 
        m.id_mitra, 
        m.nama_mitra, 
        m.lokasi, 
        GROUP_CONCAT(DISTINCT kc.no_kelompok ORDER BY kc.no_kelompok ASC SEPARATOR ', ') AS no_kelompok
    FROM mitra m
    INNER JOIN kpconnection kc ON m.id_mitra = kc.id_mitra
    WHERE m.nama_mitra LIKE ? OR kc.no_kelompok LIKE ?
    GROUP BY m.id_mitra, m.nama_mitra, m.lokasi
    LIMIT ?, ?
";

// Menyiapkan query dengan parameter pencarian dan pagination
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'ssii', $searchTerm, $searchTerm, $start, $perPage);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Periksa jika query gagal
if (!$result) {
    die("Query gagal: " . mysqli_error($conn));
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mitra</title>
    <link rel="stylesheet" href="../../../css/adminlte.min.css" crossorigin="anonymous"/>
    <script src="../../../js/adminlte.min.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
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
                <h1>Data Mitra</h1>
                <p>Data Mitra yang Menjalin Kerjasama dengan Kelompok Kerja Praktek</p>
                <div class="row">
                    <div class="col-7"></div>
                    <div class="col-5">
                    <form class="d-flex mb-3" method="GET">
        <input type="text" name="search" class="form-control me-2" placeholder="Cari Nama Mitra atau Kelompok" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
        <button class="btn btn-primary" type="submit">Cari</button>
    </form>
                    </div>
</div>

<table class="table table-bordered mt-3">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Mitra</th>
            <th>Lokasi Mitra</th>
            <th>Kelompok yang Bekerjasama</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = $start + 1;
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr class='align-middle'>";
                echo "<td>{$no}</td>";
                echo "<td>" . htmlspecialchars($row['nama_mitra']) . "</td>";
                echo "<td>" . htmlspecialchars($row['lokasi']) . "</td>";
                echo "<td>" . htmlspecialchars($row['no_kelompok']) . "</td>";
                $no++;
            }
        } else {
            // Menampilkan pesan jika tidak ada data hasil pencarian
            echo "<tr><td colspan='4' class='text-center'>Hasil pencarian tidak ditemukan</td></tr>";
        }
        ?>
    </tbody>
</table>

<!-- Pagination -->
<div class="pagination pagination-sm m-0 float-end">
    <?php if ($page > 1): ?>
        <a href="?search=<?php echo htmlspecialchars($search); ?>&page=1" class="page-item page-link">First</a>
        <a href="?search=<?php echo htmlspecialchars($search); ?>&page=<?php echo $page - 1; ?>" class="page-item page-link">Prev</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?search=<?php echo htmlspecialchars($search); ?>&page=<?php echo $i; ?>" class="page-item page-link <?php echo $i == $page ? 'active' : ''; ?>">
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>

    <?php if ($page < $totalPages): ?>
        <a href="?search=<?php echo htmlspecialchars($search); ?>&page=<?php echo $page + 1; ?>" class="page-item page-link">Next</a>
        <a href="?search=<?php echo htmlspecialchars($search); ?>&page=<?php echo $totalPages; ?>" class="page-item page-link">Last</a>
    <?php endif; ?>
</div>


            </div>
        </main>
        <footer class="app-footer">
            <strong>Copyright &copy; 2024 Kelompok 44 Kerja Praktek Universitas Kuningan</strong>
        </footer>
    </div>
</body>
</html>
