<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    error_reporting(0); // Matikan error reporting untuk produksi
    ini_set('display_errors', 0); // Jangan tampilkan error di browser
}
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../../index.php");
    exit;
}

require '../../koneksi.php'; // File koneksi ke database

if (isset($_POST['generate_team'])) {
    // Ambil mahasiswa yang belum tergabung dalam kelompok
    $query = "SELECT * FROM mahasiswa WHERE nim NOT IN (SELECT nim FROM kpconnection) ORDER BY nim ASC";
    $result = $conn->query($query);
    if ($result->num_rows == 0) {
        echo "Tidak ada mahasiswa yang belum tergabung dalam kelompok.";
        exit();
    }
    $mahasiswa = $result->fetch_all(MYSQLI_ASSOC);

    // Pengelompokan awal berdasarkan prodi, angkatan, kelas, MBKM
    $grouped_mahasiswa = [];
    foreach ($mahasiswa as $mhs) {
        $key = $mhs['prodi'] . '-' . $mhs['angkatan'] . '-' . $mhs['kelas'] . '-' . $mhs['mbkm'];
        $grouped_mahasiswa[$key][] = $mhs;
    }

    $valid_groups = [];
    $remaining_students = [];
    $kelompok_counter = 1;

    // Tahap 1: Buat kelompok 4 orang berdasarkan kategori penuh
    foreach ($grouped_mahasiswa as $key => $students) {
        if (count($students) >= 4) {
            shuffle($students);
            $chunks = array_chunk($students, 4);
            foreach ($chunks as $chunk) {
                if (count($chunk) == 4) {
                    $valid_groups[] = $chunk;
                } else {
                    $remaining_students = array_merge($remaining_students, $chunk);
                }
            }
        } else {
            $remaining_students = array_merge($remaining_students, $students);
        }
    }

    // Tahap 2: Gabungkan sisa mahasiswa berdasarkan prodi dan MBKM
    $grouped_remaining = [];
    foreach ($remaining_students as $mhs) {
        $key = $mhs['prodi'] . '-' . $mhs['mbkm'];
        $grouped_remaining[$key][] = $mhs;
    }

    $remaining_students = [];
    foreach ($grouped_remaining as $key => $students) {
        if (count($students) >= 4) {
            shuffle($students);
            $chunks = array_chunk($students, 4);
            foreach ($chunks as $chunk) {
                if (count($chunk) == 4) {
                    $valid_groups[] = $chunk;
                } else {
                    $remaining_students = array_merge($remaining_students, $chunk);
                }
            }
        } else {
            $remaining_students = array_merge($remaining_students, $students);
        }
    }

    // Tahap 3: Masukkan sisa mahasiswa ke kelompok yang sudah ada (maksimal 5 anggota per kelompok)
    foreach ($remaining_students as $mhs) {
        $added = false;
        foreach ($valid_groups as &$group) {
            if (count($group) < 5 && $group[0]['prodi'] == $mhs['prodi'] && $group[0]['mbkm'] == $mhs['mbkm']) {
                $group[] = $mhs;
                $added = true;
                break;
            }
        }
        if (!$added) {
            $valid_groups[] = [$mhs];
        }
    }

    // Simpan kelompok ke database
    foreach ($valid_groups as $group) {
        if (count($group) > 0) { // Kelompok tidak kosong
            $conn->query("INSERT INTO kelompok (no_kelompok) VALUES ($kelompok_counter)");
            foreach ($group as $mhs) {
                $conn->query("INSERT INTO kpconnection (nim, no_kelompok) VALUES ('{$mhs['nim']}', $kelompok_counter)");
            }
            $kelompok_counter++;
        }
    }

    // Bagikan dosen ke kelompok
    $kelompok = $conn->query("SELECT no_kelompok FROM kelompok")->fetch_all(MYSQLI_ASSOC);
    $dosen = $conn->query("SELECT nik FROM dosen")->fetch_all(MYSQLI_ASSOC);

    // Jumlah kelompok dan dosen
    $total_kelompok = count($kelompok);
    $total_dosen = count($dosen);

    $base_count = intdiv($total_kelompok, $total_dosen);
    $remainder = $total_kelompok % $total_dosen;

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
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <?php
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
    ?>
    <div class="app-wrapper">
        <nav class="app-header navbar navbar-expand bg-body">
            <div class="container-fluid">
                <h5 class="brand-text text-dark ms-2 align-middle">PENGELOLAAN DATA KERJA PRAKTEK</h5>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown user-menu">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <img src="/dist/assets/img/user2-160x160.jpg" class="user-image rounded-circle shadow" alt="User Image">
                            <span class="d-none d-md-inline">Alexander Pierce</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                            <li class="user-header text-bg-primary">
                                <img src="/dist/assets/img/user2-160x160.jpg" class="rounded-circle shadow" alt="User Image">
                                <p>
                                    Alexander Pierce - Web Developer
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
                <nav class="mt-2">
                    <ul class="nav sidebar-menu flex-column" role="menu">
                        <li class="nav-item"><a href="infokp.php" class="nav-link"><p>Informasi KP</p></a></li>
                        <li class="nav-item"><a href="mahasiswa.php" class="nav-link"><p>Data Mahasiswa</p></a></li>
                        <li class="nav-item"><a href="kelompok.php" class="nav-link"><p>Data Kelompok</p></a></li>
                        <li class="nav-item"><a href="dosen.php" class="nav-link"><p>Data Dosen</p></a></li>
                        <li class="nav-item"><a href="../../logout.php" class="nav-link"><p>Logout</p></a></li>
                    </ul>
                </nav>
            </div>
        </aside>
        <main class="app-main">
            <div class="container-fluid px-5 py-3">
                <h1>Data Kelompok Kerja Praktek</h1>
                <p>Data Mahasiswa Kerja Praktek Per Kelompok</p>
                <?php 
                if (isset($_SESSION['success'])): 
                    header('Location: kelompok.php');
                    ?>
                    
                    <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
                <?php endif; ?>
                <form method="POST">
                    <input type="submit" class="btn btn-primary" name="generate_team" value="BUAT KELOMPOK BARU">
                </form>
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
        echo "<td rowspan=\"$group_count\">{$row['no_kelompok']}</td>";
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
                <nav>
    <ul class="pagination justify-content-center">
        <!-- Tombol "First" -->
        <?php if ($current_page > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?page=1" aria-label="First">
                    <span aria-hidden="true">&laquo;&laquo;</span>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link" href="?page=<?= $current_page - 1 ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        <?php else: ?>
            <li class="page-item disabled">
                <a class="page-link" href="#" aria-label="First">
                    <span aria-hidden="true">&laquo;&laquo;</span>
                </a>
            </li>
            <li class="page-item disabled">
                <a class="page-link" href="#" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        <?php endif; ?>

        <!-- Range Angka Pagination -->
        <?php
        $start = max(1, $current_page - 2); // Awal angka pagination
        $end = min($total_pages, $current_page + 2); // Akhir angka pagination

        if ($start > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?= $start - 1 ?>">...</a>
            </li>
        <?php endif;

        for ($i = $start; $i <= $end; $i++): ?>
            <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
            </li>
        <?php endfor;

        if ($end < $total_pages): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?= $end + 1 ?>">...</a>
            </li>
        <?php endif; ?>

        <!-- Tombol "Next" dan "Last" -->
        <?php if ($current_page < $total_pages): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?= $current_page + 1 ?>" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link" href="?page=<?= $total_pages ?>" aria-label="Last">
                    <span aria-hidden="true">&raquo;&raquo;</span>
                </a>
            </li>
        <?php else: ?>
            <li class="page-item disabled">
                <a class="page-link" href="#" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
            <li class="page-item disabled">
                <a class="page-link" href="#" aria-label="Last">
                    <span aria-hidden="true">&raquo;&raquo;</span>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</nav>

            </div>
        </main>
        <footer class="app-footer">
            <strong>Copyright &copy; 2024 Kelompok 44 Kerja Praktek Universitas Kuningan</strong>
            All rights reserved.
        </footer>
    </div>
</body>
</html>
