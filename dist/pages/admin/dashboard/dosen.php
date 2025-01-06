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

// Proses Tambah/Edit/Hapus Data
// Proses Tambah/Edit/Hapus Data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $nik = $conn->real_escape_string($_POST['nik']);
    $nama_dosen = $conn->real_escape_string($_POST['nama_dosen']);

    if ($action === 'add') {
        $check_sql = "SELECT COUNT(*) AS count FROM dosen WHERE nik = '$nik'";
        $check_result = $conn->query($check_sql);
        $check_count = $check_result->fetch_assoc()['count'];

        if ($check_count > 0) {
            // Jika NIK sudah ada, tampilkan pesan error
            $error_message = "NIK sudah terdaftar. Silakan gunakan NIK yang lain.";
        } else {
            // Tambah data
            $sql = "INSERT INTO dosen (nik, nama_dosen) VALUES ('$nik', '$nama_dosen')";
            $conn->query($sql);
            header("Location: dosen.php");
            exit;
        }
    } elseif ($action === 'edit') {
        $sql = "UPDATE dosen SET nama_dosen = '$nama_dosen' WHERE nik = '$nik'";
        $conn->query($sql);
        $_SESSION['success_message'] = "Data dosen berhasil diperbarui.";
    } elseif ($action === 'delete') {
        $sql = "DELETE FROM dosen WHERE nik = '$nik'";
        $conn->query($sql);
        $_SESSION['success_message'] = "Data dosen berhasil dihapus.";
    }
    header("Location: dosen.php");
    exit;
}

    // Default nilai form kosong
$edit_nik = '';
$edit_nama_dosen = '';

if (isset($_GET['edit_nik'])) {
    $edit_nik = $conn->real_escape_string($_GET['edit_nik']);
    $sql_edit = "SELECT * FROM dosen WHERE nik = '$edit_nik'";
    $result_edit = $conn->query($sql_edit);

    if ($result_edit->num_rows > 0) {
        $row_edit = $result_edit->fetch_assoc();
        $edit_nik = $row_edit['nik'];
        $edit_nama_dosen = $row_edit['nama_dosen'];
    }
}  


// Konfigurasi pagination
$items_per_page = 10; // Jumlah data per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1); // Pastikan halaman minimal 1
$search = isset($_GET['search']) ? trim($_GET['search']) : ''; // Pencarian

// Kondisi pencarian
$where_clause = '';
if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $where_clause = "WHERE d.nama_dosen LIKE '%$search%' OR d.nik LIKE '%$search%'";
}

// Hitung total data
$sql_count = "SELECT COUNT(DISTINCT d.nik) AS total FROM dosen d LEFT JOIN kpconnection kc ON d.nik = kc.nik $where_clause";
$total_result = $conn->query($sql_count);
$total_rows = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $items_per_page);

// Ambil data sesuai halaman
$offset = ($page - 1) * $items_per_page;
$sql = "
    SELECT 
        d.nik, 
        d.nama_dosen, 
        COALESCE(GROUP_CONCAT(DISTINCT kc.no_kelompok ORDER BY kc.no_kelompok ASC SEPARATOR ', '), '-') AS kelompok
    FROM dosen d
    LEFT JOIN kpconnection kc ON d.nik = kc.nik
    $where_clause
    GROUP BY d.nik, d.nama_dosen
    LIMIT $offset, $items_per_page
";  
$result = $conn->query($sql);

?>  

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Dosen</title>
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
            <!-- App Main -->
            <main class="app-main">
            <div class="container-fluid px-5 py-3">
        <h1>Data Dosen Pembimbing Lapangan</h1>
        <p>Data Dosen Pembimbing Lapangan Kerja Praktek</p>
        <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>
    <form method="POST" action="dosen.php">
        <input type="hidden" name="action" value="<?php echo empty($edit_nik) ? 'add' : 'edit'; ?>">
        <div class="mb-3">
            <label for="nik" class="form-label">NIK</label>
            <input type="text" id="nik" name="nik" class="form-control" value="<?php echo htmlspecialchars($edit_nik); ?>" <?php echo empty($edit_nik) ? '' : 'readonly'; ?> required>
        </div>
        <div class="mb-3">
            <label for="nama_dosen" class="form-label">Nama Dosen</label>
            <input type="text" id="nama_dosen" name="nama_dosen" class="form-control" value="<?php echo htmlspecialchars($edit_nama_dosen); ?>" required>
        </div>
        <button type="submit" class="btn btn-success"><?php echo empty($edit_nik) ? 'Tambah' : 'Perbarui'; ?></button>
        <a href="dosen.php" class="btn btn-secondary">Batal</a>
    </form>
<hr>
    <!-- </div> -->
    <hr class="my-5">
        <div class="row">
            <div class="col-6"></div>
            <div class="col-6">
                <div class="row">
                    <div class="col-9">
                    <form class="d-flex mb-3" method="GET">
                        <input type="text" name="search" class="form-control me-2" placeholder="Cari NIK atau Nama Dosen" value="<?php echo htmlspecialchars($search); ?>">
                        <button class="btn btn-primary" type="submit">Cari</button>
                    </form>
                    </div>
                    <div class="col-3">
                    <div class="row">
                    <button class="btn btn-primary" type="button">Tambah Dosen</button>
                    </div>
                    </div>
                    <div class="row"><p></p></div>
                </div>
            </div>
        </div>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>NIK</th>
                    <th>Nama Dosen</th>
                    <th>Kelompok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['nik']); ?></td>
                        <td><?php echo htmlspecialchars($row['nama_dosen']); ?></td>
                        <td><?php echo htmlspecialchars($row['kelompok']); ?></td>
                        <td>
                            <a href="dosen.php?edit_nik=<?php echo $row['nik']; ?>" class="btn btn-primary btn-sm">Ubah</a>
                            <form method="POST" action="dosen.php" class="d-inline">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="nik" value="<?php echo htmlspecialchars($row['nik']); ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">Data tidak ditemukan.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
        <ul class="pagination pagination-sm m-0 float-end">
    <!-- Tombol Previous -->
    <li class="page-item <?php if ($page <= 1) echo 'disabled'; ?>">
        <a class="page-link" href="?search=<?php echo urlencode($search); ?>&page=<?php echo max(1, $page - 1); ?>">«</a>
    </li>
    
    <?php
    // Logika pagination
    $pagination_limit = 5; // Jumlah maksimal angka halaman
    $range_start = max(1, $page - 2);
    $range_end = min($total_pages, $page + 2);

    // Halaman sebelumnya dengan ...
    if ($range_start > 1) {
        echo '<li class="page-item"><a class="page-link" href="?search=' . urlencode($search) . '&page=1">1</a></li>';
        if ($range_start > 2) {
            echo '<li class="page-item disabled"><a class="page-link">...</a></li>';
        }
    }

    // Menampilkan halaman dalam range
    for ($i = $range_start; $i <= $range_end; $i++) {
        echo '<li class="page-item ' . ($page == $i ? 'active' : '') . '">';
        echo '<a class="page-link" href="?search=' . urlencode($search) . '&page=' . $i . '">' . $i . '</a>';
        echo '</li>';
    }

    // Halaman selanjutnya dengan ...
    if ($range_end < $total_pages) {
        if ($range_end < $total_pages - 1) {
            echo '<li class="page-item disabled"><a class="page-link">...</a></li>';
        }
        echo '<li class="page-item"><a class="page-link" href="?search=' . urlencode($search) . '&page=' . $total_pages . '">' . $total_pages . '</a></li>';
    }
    ?>

    <!-- Tombol Next -->
    <li class="page-item <?php if ($page >= $total_pages) echo 'disabled'; ?>">
        <a class="page-link" href="?search=<?php echo urlencode($search); ?>&page=<?php echo min($total_pages, $page + 1); ?>">»</a>
    </li>
</ul>

    </div>
            </main>
            <footer class="app-footer">
            <strong>Copyright &copy; 2024 Kelompok 44 Kerja Praktek Universitas Kuningan</strong>
            All rights reserved.
        </footer>
        </div>
    </body>
    
</body>
</html>

<?php
$conn->close();
?>
