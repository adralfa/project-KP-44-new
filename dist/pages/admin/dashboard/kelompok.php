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

// Number of records per page
$per_page = 10;

// Get the current page from the URL, default to page 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Calculate the offset
$offset = ($page - 1) * $per_page;

// Query to get total number of records for pagination
$total_query = "SELECT COUNT(*) as total FROM kpconnection kc
                JOIN mahasiswa m ON kc.nim = m.nim
                JOIN kelompok k ON kc.no_kelompok = k.no_kelompok
                JOIN dosen d ON kc.nik = d.nik";
$total_result = $conn->query($total_query);
$total_rows = $total_result->fetch_assoc()['total'];

// Query to fetch data for current page
$query = "SELECT k.no_kelompok, m.nim, m.nama, m.jk, m.prodi, m.angkatan, m.kelas, m.mbkm, d.nama_dosen, 
                 COUNT(*) OVER (PARTITION BY k.no_kelompok) AS jumlah_mahasiswa
          FROM kpconnection kc
          JOIN mahasiswa m ON kc.nim = m.nim
          JOIN kelompok k ON kc.no_kelompok = k.no_kelompok
          JOIN dosen d ON kc.nik = d.nik
          ORDER BY k.no_kelompok, m.nama
          LIMIT $per_page OFFSET $offset";

$result = $conn->query($query);

// Calculate total pages
$total_pages = ceil($total_rows / $per_page);

// Pagination controls
$pagination = '';
if ($page > 1) {
    $pagination .= '<a href="?page=' . ($page - 1) . '">Previous</a>';
}
for ($i = 1; $i <= $total_pages; $i++) {
    $pagination .= ' <a href="?page=' . $i . '">' . $i . '</a> ';
}
if ($page < $total_pages) {
    $pagination .= '<a href="?page=' . ($page + 1) . '">Next</a>';
}

// // Tampilkan data mahasiswa yang sesuai dengan halaman
// echo '<table>';
// echo '<tr><th>No Kelompok</th><th>NIM</th><th>Nama</th><th>Prodi</th><th>Kelas</th><th>Jumlah Mahasiswa</th><th>Nama Dosen</th></tr>';

// while ($row = $result->fetch_assoc()) {
//     echo '<tr>';
//     echo '<td>' . $row['no_kelompok'] . '</td>';
//     echo '<td>' . $row['nim'] . '</td>';
//     echo '<td>' . $row['nama'] . '</td>';
//     echo '<td>' . $row['prodi'] . '</td>';
//     echo '<td>' . $row['kelas'] . '</td>';
//     echo '<td>' . $row['jumlah_mahasiswa'] . '</td>';
//     echo '<td>' . $row['nama_dosen'] . '</td>';
//     echo '</tr>';
// }

// echo '</table>';

// // Tampilkan tombol pagination
// echo '<div>' . $pagination . '</div>';

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

    // Kelompokkan mahasiswa berdasarkan prodi
    $grouped_by_prodi = [];
    foreach ($mahasiswa as $mhs) {
        $key = $mhs['prodi'];
        $grouped_by_prodi[$key][] = $mhs;
    }

    // Atur kelompok counter
    $kelompok_counter = $conn->query("SELECT COUNT(*) as total FROM kelompok")->fetch_assoc()['total'] + 1;

    $remaining_students = []; // Array untuk menyimpan mahasiswa sisa

    foreach ($grouped_by_prodi as $prodi => $the_prodi) {
        $total_students = count($the_prodi);

        // Jika jumlah mahasiswa <= 5, buat satu kelompok
        if ($total_students <= 5) {
            $conn->query("INSERT INTO kelompok (no_kelompok) VALUES ($kelompok_counter)");
            foreach ($the_prodi as $mhs) {
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
            $chunks = array_chunk($the_prodi, ceil($total_students / 2));
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

    // Jumlah kelompok dan dosen
    $total_kelompok = count($kelompok);
    $total_dosen = count($dosen);

    // Hitung jumlah kelompok per dosen
    $base_count = intdiv($total_kelompok, $total_dosen);
    $remainder = $total_kelompok % $total_dosen;

    // Distribusi kelompok per dosen
    $dosen_kelompok = array_fill(0, $total_dosen, $base_count);
    $sisa_indices = range(0, $total_dosen - 1);
    shuffle($sisa_indices);

    for ($i = 0; $i < $remainder; $i++) {
        $dosen_kelompok[$sisa_indices[$i]]++;
    }

    shuffle($kelompok); // Acak kelompok sebelum dibagikan

    $index = 0;
    foreach ($kelompok as $grp) {
        $id_kelompok = $grp['no_kelompok'];
        for ($i = 0; $i < $total_dosen; $i++) {
            if ($dosen_kelompok[$i] > 0) {
                $id_dosen = $dosen[$i]['nik'];
                $dosen_kelompok[$i]--;
                break;
            }
        }
        $conn->query("UPDATE kpconnection SET nik = $id_dosen WHERE no_kelompok = $id_kelompok");
    }

    $_SESSION['success'] = "Kelompok dan dosen berhasil dibagi secara acak!";
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
                            <img src="/dist/assets/img/user2-160x160.jpg" class="user-image rounded-circle shadow" alt="User Image">
                            <span class="d-none d-md-inline"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                            <li class="user-header text-bg-primary">
                                <img src="/dist/assets/img/user2-160x160.jpg" class="rounded-circle shadow" alt="User Image">
                                <p>
                                <?php echo htmlspecialchars($_SESSION['user_name']); ?>
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
        <div class="sidebar-brand bg-light"> <!--begin::Brand Link--> <a href="./home.html" class="brand-link"> <!--begin::Brand Image--> <img src="/dist/assets/img/LOGOFKOM.png" alt="AdminLTE Logo" class="brand-image opacity-75 shadow"> <!--end::Brand Image--> <!--begin::Brand Text-->  </a> <!--end::Brand Link--> </div> <!--end::Sidebar Brand--> <!--begin::Sidebar Wrapper-->
        <div class="sidebar-wrapper">
                    <nav class="mt-2"> <!--begin::Sidebar Menu-->
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
                </div> <!--end::Sidebar Wrapper-->
        </aside>
        <main class="app-main">
            <div class="container-fluid px-5 py-3">
                <h1>Data Kelompok Kerja Praktek</h1>
                <p>Data Mahasiswa Kerja Praktek Per Kelompok</p>
                <div class="row">
                    <div class="col d-flex justify-content-end">
                    <p class="me-3">
                            Mahasiswa yang tergabung ke kelompok : 
                            <?php
                        $mahasiswa_counter = $conn->query("SELECT COUNT(*) as total FROM kpconnection")->fetch_assoc()['total'];
                        echo $mahasiswa_counter . "/";

                        $mahasiswa_counter2 = $conn->query("SELECT COUNT(*) as total FROM mahasiswa")->fetch_assoc()['total'];
                        echo $mahasiswa_counter2;
                    ?>
                        </p>
                    
                <form method="POST">
                    <input type="submit" class="btn btn-primary" name="generate_team" value="BUAT KELOMPOK BARU">
                </form>
                    </div>        
                </div>

                <table class="table table-bordered mt-3">
                    <thead>
                        <tr>
                            <th>Kelompok</th>
                            <th>NIM</th>
                            <th>Nama Mahasiswa</th>
                            <th>JK</th>
                            <th>Kelas</th>
                            <th>MBKM</th>
                            <th>Dosen</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
$query = "SELECT k.no_kelompok,m.nim, m.nama, m.jk, m.prodi, m.angkatan, m.kelas, m.mbkm, d.nama_dosen, 
                 COUNT(*) OVER (PARTITION BY k.no_kelompok) AS jumlah_mahasiswa
          FROM kpconnection kc
          JOIN mahasiswa m ON kc.nim = m.nim
          JOIN kelompok k ON kc.no_kelompok = k.no_kelompok
          JOIN dosen d ON kc.nik = d.nik
          ORDER BY k.no_kelompok, m.nama";

$result = $conn->query($query);
$last_group = null; // Menyimpan kelompok terakhir yang sudah diproses
$group_count = 0; // Menyimpan jumlah mahasiswa dalam satu kelompok

while ($row = $result->fetch_assoc()) {
    if ($last_group !== $row['no_kelompok']) {
        // Jika kelompok baru, tampilkan no_kelompok dan dosen dengan rowspan
        if ($last_group !== null) {
            // Jika ada kelompok sebelumnya, tampilkan sisa mahasiswa
            echo "</tr>"; 
        }
        
        // Set last group dan hitung jumlah mahasiswa
        $last_group = $row['no_kelompok'];
        $group_count = $row['jumlah_mahasiswa'];

        // Tampilkan no_kelompok dengan rowspan
        echo "<tr>";
        echo "<td rowspan=\"$group_count\">Kelompok {$row['no_kelompok']}</td>";
        echo "<td>{$row['nim']}</td>";
        echo "<td>{$row['nama']}</td>";
        echo "<td>{$row['jk']}</td>";
        echo "<td>{$row['prodi']} ". $row['angkatan'] . " " . $row['kelas'] . "</td>";
        echo "<td>{$row['mbkm']}</td>";
        echo "<td rowspan=\"$group_count\">{$row['nama_dosen']}</td>";
    } else {
        // Jika masih dalam kelompok yang sama, hanya tampilkan mahasiswa berikutnya
        echo "<tr>";
        echo "<td>{$row['nim']}</td>";
        echo "<td>{$row['nama']}</td>";
        echo "<td>{$row['jk']}</td>";
        echo "<td>{$row['prodi']} ". $row['angkatan'] . " " . $row['kelas'] . "</td>";
        echo "<td>{$row['mbkm']}</td>";
    }
    echo "</tr>";
}
?>

                    </tbody>
                </table>
            </div>
        </main>
        <footer class="app-footer">
            <strong>Copyright &copy; 2024 Kelompok 44 Kerja Praktek Universitas Kuningan</strong>
            All rights reserved.
        </footer>
    </div>
</body>
</html>
