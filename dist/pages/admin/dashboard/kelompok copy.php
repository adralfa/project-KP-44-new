<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    error_reporting(E_ALL);
}

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../../index.php");
    exit;
}

require '../../koneksi.php'; // File koneksi ke database

// Pagination configuration
$limit_per_page = 3; // Display 3 groups per page
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit_per_page;

// Search functionality
$search_keyword = isset($_GET['search']) ? trim($_GET['search']) : '';
$where_clause = !empty($search_keyword) ? "AND k.no_kelompok = ?" : "";

// Count total groups
$total_group_sql = "SELECT COUNT(DISTINCT k.no_kelompok) AS total FROM kelompok k LEFT JOIN kpconnection kc ON k.no_kelompok = kc.no_kelompok WHERE kc.nim IS NOT NULL $where_clause";
$total_group_stmt = $conn->prepare($total_group_sql);
if (!empty($search_keyword)) {
    $total_group_stmt->bind_param("s", $search_keyword);
}
$total_group_stmt->execute();
$total_groups = $total_group_stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_groups / $limit_per_page);

// Fetch groups
$group_sql = "SELECT DISTINCT k.no_kelompok FROM kelompok k LEFT JOIN kpconnection kc ON k.no_kelompok = kc.no_kelompok WHERE kc.nim IS NOT NULL $where_clause LIMIT ? OFFSET ?";
$group_stmt = $conn->prepare($group_sql);
if (!empty($search_keyword)) {
    $group_stmt->bind_param("ssi", $search_keyword, $limit_per_page, $offset);
} else {
    $group_stmt->bind_param("ii", $limit_per_page, $offset);
}
$group_stmt->execute();
$group_result = $group_stmt->get_result();

// Fetch group data
$kelompok_data = [];
$kelompok_ids = [];
while ($group_row = $group_result->fetch_assoc()) {
    $kelompok_ids[] = $group_row['no_kelompok'];
}

// If no groups found
if (empty($kelompok_ids)) {
    echo "Tidak ada data untuk ditampilkan.";
    exit;
}

// Fetch student data based on groups
$placeholders = implode(',', array_fill(0, count($kelompok_ids), '?'));
$data_sql = "
    SELECT 
        m.nim, 
        m.nama AS nama_mahasiswa,
        m.telp,    
        m.prodi,
        m.angkatan, 
        m.kelas,
        k.no_kelompok,
        d.nama_dosen
    FROM mahasiswa m
    LEFT JOIN kpconnection kc ON m.nim = kc.nim
    LEFT JOIN kelompok k ON kc.no_kelompok = k.no_kelompok
    LEFT JOIN dosen d ON kc.nik = d.nik
    WHERE k.no_kelompok IN ($placeholders)
    ORDER BY k.no_kelompok, m.nama";
$data_stmt = $conn->prepare($data_sql);
$data_stmt->bind_param(str_repeat('s', count($kelompok_ids)), ...$kelompok_ids);
$data_stmt->execute();
$data_result = $data_stmt->get_result();

// Group data for display
$kelompok_data = [];
while ($row = $data_result->fetch_assoc()) {
    $kelompok_data[$row['no_kelompok']][] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengelolaan Data Kerja Praktek</title>
    <link rel="stylesheet" href="../../../css/adminlte.min.css" crossorigin="anonymous"/>
    <script src="../../../js/adminlte.min.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href=" https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        <nav class="app-header navbar navbar-expand bg-body">
            <div class="container-fluid">
                <h5 class="brand-text text-dark ms-2 align-middle">PENGELOLAAN DATA KERJA PRAKTEK</h5>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown user-menu">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <img src="../../../assets/img/user2-160x160.jpg" class="user-image rounded-circle shadow" alt="User  Image">
                            <span class="d-none d-md-inline">
                                <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                            <li class="user-header text-bg-primary">
                                <img src="../../../assets/img/user2-160x160.jpg" class="rounded-circle shadow" alt="User  Image">
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
                        <li class="nav-item"> <a href="../../logout.php" class="nav-link"> <i class="nav-icon bi bi-box-arrow-left"></i>
                                <p>Logout</p>
                            </a> </li>
                    </ul>
                </nav>
            </div>
        </aside>
        <main class="app-main">
            <div class="container-fluid px-5 py-3">
                <h1>Data Kelompok Kerja Praktek</h1>
                <p>Data Mahasiswa Kerja Praktek Per Kelompok</p>
                <div class="row mb-3">
                    <div class="col-6">
                        <div class="row">
                            <form method="POST">
                                <input type="submit" class="btn btn-primary" name="generate_team" value="GENERATE">
                            </form>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="row">
                            <form method="GET" action="">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan nomor kelompok..." value="<?php echo htmlspecialchars ($_GET['search'] ?? ''); ?>">
                                    <button type="submit" class="btn btn-primary">Cari</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <p class="me-3">
                        Mahasiswa yang tergabung ke kelompok: 
                        <?php
                        $mahasiswa_counter = $conn->query("SELECT COUNT(*) as total FROM kpconnection")->fetch_assoc()['total'];
                        echo $mahasiswa_counter . "/";
                        $mahasiswa_counter2 = $conn->query("SELECT COUNT(*) as total FROM mahasiswa")->fetch_assoc()['total'];
                        echo $mahasiswa_counter2;
                        ?>
                    </p>
                </div>
                <table class="table table-bordered mt-3">
                    <thead>
                        <tr>
                            <th>Kelompok</th>
                            <th>NIM</th>
                            <th>Nama Mahasiswa</th>
                            <th>Telp</th>
                            <th>Kelas</th>
                            <th>Dosen</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($kelompok_data)) { ?>
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada data ditemukan.</td>
                        </tr>
                    <?php } else { ?>
                        <?php
                        foreach ($kelompok_data as $no_kelompok => $members) {
                            $first_member = true;
                            $first_dosen = true;
                            $rowspan = count($members); // Hitung jumlah anggota dalam kelompok

                            foreach ($members as $member) {
                                echo "<tr>";
                                // Kolom untuk nomor kelompok
                                if ($first_member) {
                                    echo "<td rowspan=\"$rowspan\">Kelompok {$no_kelompok}</td>";
                                    $first_member = false;
                                }

                                // Kolom data mahasiswa
                                echo "<td>{$member['nim']}</td>";
                                echo "<td>{$member['nama_mahasiswa']}</td>";
                                echo "<td>{$member['telp']}</td>";
                                echo "<td>{$member['prodi']}-{$member['angkatan']}-{$member['kelas']}</td>";

                                // Kolom dosen (ditampilkan sekali untuk kelompok pertama)
                                if ($first_dosen) {
                                    echo "<td rowspan=\"$rowspan\">{$member['nama_dosen']}</td>";
                                    $first_dosen = false;
                                }
                                echo "</tr>";
                            }
                        }
                        ?>
                    <?php } ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <ul class="pagination pagination-sm m-0 float-end">
                    <li class="page-item <?php if ($page <= 1) echo 'disabled'; ?>">
                        <a class="page-link" href="?search=<?php echo urlencode($search_keyword); ?>&page=<?php echo $page - 1; ?>">«</a>
                    </li>
                    <?php
                    $range_start = max(1, $page - 2);
                    $range_end = min($total_pages, $page + 2);

                    // Display the first page if the range starts after 1
                    if ($range_start > 1) {
                        echo '<li class="page-item"><a class="page-link" href="?search=' . urlencode($search_keyword) . '&page=1">1</a></li>';
                        if ($range_start > 2) {
                            echo '<li class="page-item disabled"><a class="page-link">...</a></li>';
                        }
                    }

                    // Display the range of pages
                    for ($i = $range_start; $i <= $range_end; $i++) {
                        echo '<li class="page-item ' . ($page == $i ? 'active' : '') . '">';
                        echo '<a class="page-link" href="?search=' . urlencode($search_keyword) . '&page=' . $i . '">' . $i . '</a></li>';
                    }

                    // Display the last page if the range ends before the total pages
                    if ($range_end < $total_pages) {
                        if ($range_end < $total_pages - 1) {
                            echo '<li class="page-item disabled"><a class="page-link">...</a></li>';
                        }
                        echo '<li class="page-item"><a class="page-link" href="?search=' . urlencode($search_keyword) . '&page=' . $total_pages . '">' . $total_pages . '</a></li>';
                    }
                    ?>
                    <li class="page-item <?php if ($page >= $total_pages) echo 'disabled'; ?>">
                        <a class="page-link" href="?search=<?php echo urlencode($search_keyword); ?>&page=<?php echo $page +  1; ?>">»</a>
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
</html>