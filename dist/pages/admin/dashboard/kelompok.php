<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    error_reporting(0);

}
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../../index.php");
    exit;
}

require '../../koneksi.php'; // File koneksi ke database

// Konfigurasi Pagination
$limit_per_page = 3; // Menampilkan 3 kelompok per halaman
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit_per_page;

// Fungsi Pencarian
$search_keyword = isset($_GET['search']) ? trim($_GET['search']) : '';
$where_clause = !empty($search_keyword) ? "AND k.no_kelompok = ?" : "";

// Hitung Total Kelompok
$total_group_sql = "
    SELECT COUNT(DISTINCT k.no_kelompok) AS total 
    FROM kelompok k 
    LEFT JOIN kpconnection kc ON k.no_kelompok = kc.no_kelompok 
    WHERE kc.nim IS NOT NULL $where_clause";
$total_group_stmt = $conn->prepare($total_group_sql);
if (!empty($search_keyword)) {
    $total_group_stmt->bind_param("s", $search_keyword);
}
$total_group_stmt->execute();
$total_groups = $total_group_stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_groups / $limit_per_page);

// Ambil Kelompok Berdasarkan Pagination
$group_sql = "
    SELECT DISTINCT k.no_kelompok 
    FROM kelompok k 
    LEFT JOIN kpconnection kc ON k.no_kelompok = kc.no_kelompok 
    WHERE kc.nim IS NOT NULL $where_clause 
    LIMIT ? OFFSET ?";
$group_stmt = $conn->prepare($group_sql);
if (!empty($search_keyword)) {
    $group_stmt->bind_param("ssi", $search_keyword, $limit_per_page, $offset);
} else {
    $group_stmt->bind_param("ii", $limit_per_page, $offset);
}
$group_stmt->execute();
$group_result = $group_stmt->get_result();

// Ambil Data Kelompok
$kelompok_ids = [];
while ($group_row = $group_result->fetch_assoc()) {
    $kelompok_ids[] = $group_row['no_kelompok'];
}

// Jika Tidak Ada Kelompok
if (empty($kelompok_ids)) {
    echo "Tidak ada data untuk ditampilkan.";
    exit;
}

// Ambil Data Mahasiswa Berdasarkan Kelompok
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

// Kelompokkan Data
$kelompok_data = [];
while ($row = $data_result->fetch_assoc()) {
    $kelompok_data[$row['no_kelompok']][] = $row;
}

if (isset($_POST['generate_team'])) {
    // Ambil data mahasiswa yang belum memiliki kelompok
    $query = "SELECT * FROM mahasiswa 
    WHERE nim NOT IN (SELECT nim FROM kpconnection) 
    ORDER BY CASE prodi 
    WHEN 'DKV' THEN 1
    WHEN 'MI' THEN 2
    WHEN 'SI' THEN 3
    WHEN 'TI' THEN 4
    WHEN 'TS' THEN 5
    ELSE 6 -- Jika ada prodi lain yang tidak disebutkan
    END, angkatan, kelas, mbkm, nim";
    $result = $conn->query($query);


    if ($result->num_rows == 0) {
        echo "Tidak ada mahasiswa yang belum tergabung dalam kelompok.";
        exit;
    }

    $mahasiswa = $result->fetch_all(MYSQLI_ASSOC);

    // Menghitung jumlah mahasiswa tiap prodi
    $count_query = "SELECT prodi, COUNT(*) AS total FROM mahasiswa GROUP BY prodi";
    $count_result = $conn->query($count_query);

    while ($row = $count_result->fetch_assoc()) {
        $students_per_prodi[$row['prodi']] = $row['total'];
    }
    // Kelompokkan mahasiswa berdasarkan prodi
    $grouped_by_prodi = [];
    foreach ($mahasiswa as $mhs) {
        $key = $mhs['prodi'];
        $grouped_by_prodi[$key][] = $mhs;
    }

    // Atur kelompok counter
    $kelompok_counter = $conn->query("SELECT COUNT(*) as total FROM kelompok")->fetch_assoc()['total'] + 1;

    $remaining_students = []; // Array untuk menyimpan mahasiswa sisa

    foreach ($grouped_by_prodi as $prodi => $total_students) {
        // $total_students = count($the_prodi);
        // Filter mahasiswa berdasarkan prodi
        $filtered_students = array_filter($mahasiswa, function ($mhs) use ($prodi) {
            return $mhs['prodi'] === $prodi;
        });

        $filtered_students = array_values($filtered_students); // Reset index array


        // Jika jumlah mahasiswa <= 5, buat satu kelompok
        if ($total_students <= 5) {
            $conn->query("INSERT INTO kelompok (no_kelompok) VALUES ($kelompok_counter)");
            foreach ($filtered_students as $mhs) {
                $nim = $mhs['nim'];

                // Cek apakah mahasiswa sudah tergabung dalam kelompok lain
                $check_query = "SELECT * FROM kpconnection WHERE nim = '$nim'";
                $check_result = $conn->query($check_query);
                if ($check_result->num_rows == 0) { // Jika mahasiswa belum terdaftar
                   $conn->query("INSERT INTO kpconnection (nim, no_kelompok) VALUES ('$nim', $kelompok_counter)");
                }
            }
            $kelompok_counter++;
            continue;
        }

        // Jika jumlah mahasiswa adalah 6 atau 7, bagi menjadi 2 kelompok (3-3 atau 3-4)
        if ($total_students == 6 || $total_students == 7) {
            $chunks = array_chunk($filtered_students, ceil($total_students / 2));
            foreach ($chunks as $chunk) {
                $conn->query("INSERT INTO kelompok (no_kelompok) VALUES ($kelompok_counter)");
                foreach ($chunk as $mhs) {
                    $nim = $mhs['nim'];

                    // Cek apakah mahasiswa sudah tergabung dalam kelompok lain
                    $check_query = "SELECT * FROM kpconnection WHERE nim = '$nim'";
                    $check_result = $conn->query($check_query);
                    if ($check_result->num_rows == 0) { // Jika mahasiswa belum terdaftar
                       $conn->query("INSERT INTO kpconnection (nim, no_kelompok) VALUES ('$nim', $kelompok_counter)");
                    }
                }
               $kelompok_counter++;
            }
            continue;
        }

        // === Langkah 1: Prioritas 1 (Prodi, Angkatan, Kelas, MBKM) ===
        $grouped_by_criteria = [];
        foreach ($mahasiswa as $mhs) {
            if ($mhs['prodi'] == 'SI' && $mhs['angkatan'] == 2021 && $mhs['kelas'] == 06) {
                $key = 'SI-2021-06';
                $grouped_by_criteria[$key][] = $mhs;
            } else {
                $key = $mhs['prodi'] . '-' . $mhs['angkatan'] . '-' . $mhs['kelas'] . '-' . $mhs['mbkm'];
                $grouped_by_criteria[$key][] = $mhs;
            }
        }

        $remaining_students = []; // Untuk mahasiswa yang belum masuk kelompok
        foreach ($grouped_by_criteria as $criteria_key => $group) {
            if ($key == 'SI-2021-06') {
                while (count($group) >= 4) {
                    $chunk = array_splice($group, 0, 4);
                    $conn->query("INSERT INTO kelompok (no_kelompok) VALUES ($kelompok_counter)");
                    foreach ($chunk as $mhs) {
                        $nim = $mhs['nim'];

                        // Cek apakah mahasiswa sudah tergabung dalam kelompok lain
                        $check_query = "SELECT * FROM kpconnection WHERE nim = '$nim'";
                        $check_result = $conn->query($check_query);
                        if ($check_result->num_rows == 0) { // Jika mahasiswa belum terdaftar
                            $conn->query("INSERT INTO kpconnection (nim, no_kelompok) VALUES ('$nim', $kelompok_counter)");
                        }
                    }
                    $kelompok_counter++;
                }
            } else {
                while (count($group) >= 4) {
                    $chunk = array_splice($group, 0, 4);
                    $conn->query("INSERT INTO kelompok (no_kelompok) VALUES ($kelompok_counter)");
                    foreach ($chunk as $mhs) {
                        $nim = $mhs['nim'];

                        // Cek apakah mahasiswa sudah tergabung dalam kelompok lain
                        $check_query = "SELECT * FROM kpconnection WHERE nim = '$nim'";
                        $check_result = $conn->query($check_query);
                        if ($check_result->num_rows == 0) { // Jika mahasiswa belum terdaftar
                            $conn->query("INSERT INTO kpconnection (nim, no_kelompok) VALUES ('$nim', $kelompok_counter)");
                        }
                    }
                    $kelompok_counter++;
                }
            }
            if (!empty($group)) {
                $remaining_students = array_merge($remaining_students, $group);
            }
        }

        // === Langkah 2: Prioritas 2 (Prodi, Angkatan, MBKM) ===
        $grouped_by_criteria = [];
        foreach ($remaining_students as $mhs) {
            if ($mhs['prodi'] == 'SI' && $mhs['angkatan'] == 2021 && $mhs['kelas'] == 06) {
                $key2 = 'SI-2021-06';
                $grouped_by_criteria[$key2][] = $mhs;
            } else {
                $key2 = $mhs['prodi'] . '-' . $mhs['angkatan'] . '-' . $mhs['mbkm'];
                $grouped_by_criteria[$key2][] = $mhs;
            }
        }

        $remaining_students = []; // Reset mahasiswa yang belum masuk kelompok
        foreach ($grouped_by_criteria as $criteria_key => $group) {
            if ($key2 != 'SI-2021-06') {
                while (count($group) >= 4) {
                    $chunk = array_splice($group, 0, 4);
                    $conn->query("INSERT INTO kelompok (no_kelompok) VALUES ($kelompok_counter)");
                    foreach ($chunk as $mhs) {
                        $nim = $mhs['nim'];
    
                        // Cek apakah mahasiswa sudah tergabung dalam kelompok lain
                        $check_query = "SELECT * FROM kpconnection WHERE nim = '$nim'";
                        $check_result = $conn->query($check_query);
                        if ($check_result->num_rows == 0) { // Jika mahasiswa belum terdaftar
                            $conn->query("INSERT INTO kpconnection (nim, no_kelompok) VALUES ('$nim', $kelompok_counter)");
                        }
                    }
                    $kelompok_counter++;
                }
            }
            if (!empty($group)) {
                $remaining_students = array_merge($remaining_students, $group);
            }
        }

        // === Langkah 3: Penyebaran Mahasiswa Sisa ===
        if (!empty($remaining_students)) {
            foreach ($remaining_students as $mhs) {
                $nim = $mhs['nim'];
                $prodi = $mhs['prodi'];
                $angkatan = $mhs['angkatan'];
                $kelas = $mhs['kelas'];
                $mbkm = $mhs['mbkm'];

                // Cek apakah mahasiswa sudah tergabung dalam kelompok lain
                $check_query = "SELECT * FROM kpconnection WHERE nim = '$nim'";
                $check_result = $conn->query($check_query);
                if ($check_result->num_rows == 0) { // Jika mahasiswa belum terdaftar
                    if ($prodi == 'SI' && $angkatan == 2021 && $kelas == 06) {
                        // Cari kelompok yang memenuhi Prioritas 1 (Prodi, Angkatan, Kelas, MBKM)
                        $query = "SELECT k.no_kelompok 
                            FROM kpconnection kc
                            JOIN mahasiswa m ON kc.nim = m.nim
                            JOIN kelompok k ON kc.no_kelompok = k.no_kelompok
                            WHERE m.prodi = '$prodi' AND m.angkatan = $angkatan
                            AND m.kelas = '06'
                            GROUP BY k.no_kelompok
                            HAVING COUNT(*) < 5 LIMIT 1";
                        $result = $conn->query($query);
                    } else {
                        // Cari kelompok yang memenuhi Prioritas 1 (Prodi, Angkatan, Kelas, MBKM)
                        $query = "SELECT k.no_kelompok 
                FROM kpconnection kc
                JOIN mahasiswa m ON kc.nim = m.nim
                JOIN kelompok k ON kc.no_kelompok = k.no_kelompok
                WHERE m.prodi = '$prodi' AND m.angkatan = $angkatan
                AND m.kelas = '$kelas' AND m.mbkm = $mbkm
                GROUP BY k.no_kelompok
                HAVING COUNT(*) < 5 LIMIT 1";
                        $result = $conn->query($query);
                        
                        // Jika tidak ditemukan kelompok Prioritas 1, cari Prioritas 2
                        if ($result->num_rows == 0) {
                            $query = "SELECT k.no_kelompok 
                    FROM kpconnection kc
                    JOIN mahasiswa m ON kc.nim = m.nim
                    JOIN kelompok k ON kc.no_kelompok = k.no_kelompok
                    WHERE m.prodi = '$prodi' AND m.angkatan = $angkatan
                    AND m.mbkm = $mbkm
                    GROUP BY k.no_kelompok
                    HAVING COUNT(*) < 5 LIMIT 1";
                            $result = $conn->query($query);
                        }
                    }

                    if ($result->num_rows > 0) {
                        // Tambahkan ke kelompok yang sudah ada
                        $row = $result->fetch_assoc();
                        $no_kelompok = $row['no_kelompok'];
                        $conn->query("INSERT INTO kpconnection (nim, no_kelompok) VALUES ('$nim', $no_kelompok)");
                    }
                }
            }
        }
    }
    // Bagikan dosen ke kelompok
$kelompok = $conn->query("SELECT no_kelompok FROM kelompok")->fetch_all(MYSQLI_ASSOC);
$dosen = $conn->query("SELECT nik FROM dosen")->fetch_all(MYSQLI_ASSOC);

// Kelompok yang sudah memiliki dosen
$existing_allocations = $conn->query("SELECT DISTINCT no_kelompok, nik FROM kpconnection WHERE nik IS NOT NULL")
    ->fetch_all(MYSQLI_ASSOC);

// Kelompok tanpa dosen
$kelompok_tanpa_dosen = array_filter($kelompok, function ($grp) use ($existing_allocations) {
    foreach ($existing_allocations as $allocation) {
        if ($grp['no_kelompok'] == $allocation['no_kelompok']) {
            return false;
        }
    }
    return true;
});

$total_kelompok_baru = count($kelompok_tanpa_dosen);
$total_dosen = count($dosen);

if ($total_kelompok_baru > 0) {
    // Hitung batas maksimum kelompok per dosen
    $max_kelompok_per_dosen = ceil(count($kelompok) / $total_dosen);

    // Hitung jumlah kelompok yang sudah dipegang setiap dosen
    $dosen_kelompok_count = [];
foreach ($dosen as $d) {
    $nik = $d['nik'];
    $result = $conn->query("
        SELECT COUNT(DISTINCT no_kelompok) as total 
        FROM kpconnection 
        WHERE nik = '$nik'
    ");
    $dosen_kelompok_count[$nik] = $result->fetch_assoc()['total'];
}

    // Distribusi dosen untuk kelompok baru
    shuffle($kelompok_tanpa_dosen);
    foreach ($kelompok_tanpa_dosen as $grp) {
        $id_kelompok = $grp['no_kelompok'];

        // Cari dosen yang masih bisa menerima kelompok
        foreach ($dosen as $d) {
            $id_dosen = $d['nik'];

            if ($dosen_kelompok_count[$id_dosen] < $max_kelompok_per_dosen) {
                // Tambahkan kelompok ke dosen ini
                $conn->query("UPDATE kpconnection SET nik = '$id_dosen' WHERE no_kelompok = $id_kelompok");
                $dosen_kelompok_count[$id_dosen]++;
                break;
            }
        }
    }
}

$_SESSION['success'] = "Kelompok baru berhasil dibagi ke dosen tanpa mengubah alokasi lama!";
header("Location: kelompok.php");
exit();
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
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
            </aside>
            <main class="app-main">
            <div class="container-fluid px-5 py-3">
                <h1>Data Kelompok Kerja Praktek</h1>
                <p>Data Mah asiswa Kerja Praktek Per Kelompok</p>
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
                        <?php foreach ($kelompok_data as $no_kelompok => $members): ?>
            <tr>
                <td rowspan="<?= count($members) ?>">Kelompok <?= $no_kelompok ?></td>
                <?php $first = true; ?>
                <?php foreach ($members as $member): ?>
                    <?php if (!$first) echo '<tr>'; ?>
                    <td><?= $member['nim'] ?></td>
                    <td><?= $member['nama_mahasiswa'] ?></td>
                    <td><?= $member['telp'] ?></td>
                    <td><?= $member['prodi']."-".$member['angkatan']."-".$member['kelas'] ?></td>
                    <?php if ($first): ?>
                        <td rowspan="<?= count($members) ?>"><?= $members[0]['nama_dosen'] ?></td>
                    <?php endif; ?>
                    <?php if (!$first) echo '</tr>'; ?>
                    <?php $first = false; ?>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; }?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <!-- Navigasi Pagination -->
<ul class="pagination pagination-sm m-0 float-end">
    <li class="page-item <?php if ($page <= 1) echo 'disabled'; ?>">
        <a class="page-link" href="?search=<?= urlencode($search_keyword); ?>&page=<?= $page - 1; ?>">«</a>
    </li>
    <?php
    $range_start = max(1, $page - 2);
    $range_end = min($total_pages, $page + 2);

    if ($range_start > 1) {
        echo '<li class="page-item"><a class="page-link" href="?search=' . urlencode($search_keyword) . '&page=1">1</a></li>';
        if ($range_start > 2) {
            echo '<li class="page-item disabled"><a class="page-link">...</a></li>';
        }
    }

    for ($i = $range_start; $i <= $range_end; $i++) {
        echo '<li class="page-item ' . ($page == $i ? 'active' : '') . '">';
        echo '<a class="page-link" href="?search=' . urlencode($search_keyword) . '&page=' . $i . '">' . $i . '</a></li>';
    }

    if ($range_end < $total_pages) {
        if ($range_end < $total_pages - 1) {
            echo '<li class="page-item disabled"><a class="page-link">...</a></li>';
        }
        echo '<li class="page-item"><a class="page-link" href="?search=' . urlencode($search_keyword) . '&page=' . $total_pages . '">' . $total_pages . '</a></li>';
    }
    ?>
    <li class="page-item <?php if ($page >= $total_pages) echo 'disabled'; ?>">
        <a class="page-link" href="?search=<?= urlencode($search_keyword); ?>&page=<?= $page + 1; ?>">»</a>
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
