<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    error_reporting(0);
}

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'mahasiswa') {
    header("Location: ../../index.php");
    exit;
}

require_once '../../koneksi.php';

// Cek apakah mahasiswa memiliki kelompok
if($_SESSION['no_kelompok'] == '-') {
    $is_no_kelompok_empty = 1;
}

// Query untuk memeriksa apakah ada anggota dengan status_validasi = 0
$query = "
    SELECT COUNT(*) AS count
    FROM kpconnection kc
    INNER JOIN mahasiswa m ON kc.nim = m.nim
    INNER JOIN kelompok k ON kc.no_kelompok = k.no_kelompok
    WHERE k.no_kelompok = ? AND m.status_validasi = 0
";

$stmt = $conn->prepare($query);

if ($stmt) {
    // Bind parameter (asumsikan no_kelompok adalah integer)
    $stmt->bind_param("i", $_SESSION['no_kelompok']);
    
    // Jalankan statement
    $stmt->execute();

    // Ambil hasilnya
    $result = $stmt->get_result();

    // Fetch sebagai array asosiatif
    $data = $result->fetch_assoc();

    // Akses data dengan key 'count'
    $is_disabled = $data['count'] > 0;

    // Tutup statement
    $stmt->close();
} else {
    die("Query failed: " . $conn->error);
}

// Ambil no_kelompok dari sesi login
$no_kelompok = (int) $_SESSION['no_kelompok']; // Ensure it's an integer

// Query untuk mengambil data mahasiswa berdasarkan no_kelompok
$sql = "
    SELECT 
        m.nim, 
        m.nama,
        m.telp,
        m.prodi,
        m.angkatan, 
        m.kelas,
        m.jaket,
        m.status,
        m.status_validasi, 
        COALESCE(kc.no_kelompok, '-') AS no_kelompok,
        COALESCE(k.judul_kp, '-') AS judul_kp,
        COALESCE(d.nama_dosen,  '-') AS nama_dosen,
        COALESCE(mt.nama_mitra, '-') AS nama_mitra,
        COALESCE(mt.lokasi, '-') AS lokasi
    FROM mahasiswa m
    JOIN kpconnection kc ON m.nim = kc.nim
    LEFT JOIN kelompok k ON kc.no_kelompok = k.no_kelompok
    LEFT JOIN mitra mt ON kc.id_mitra = mt.id_mitra
    LEFT JOIN dosen d ON kc.nik = d.nik
    WHERE kc.no_kelompok = ?;
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $no_kelompok); // Use 'i' for integer type
$stmt->execute();
$result = $stmt->get_result();

$mahasiswa_data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $mahasiswa_data[] = $row;
    }
}
$stmt->close();

    // Update status ketua jika form diperbarui
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Ambil data dari form
        $ketua_nim = $_POST['ketua_nim'];
        $judul_kp = $_POST['judul_kp'];
        $nama_mitra = $_POST['nama_mitra'];
        $lokasi_mitra = $_POST['lokasi']; // Ambil lokasi dari form
    
        $no_kelompok = (int)$_SESSION['no_kelompok'];
    
        // Update status ketua jika ada perubahan
        $get_kelompok_sql = "SELECT no_kelompok FROM kpconnection WHERE nim = ?";
        $stmt = $conn->prepare($get_kelompok_sql);
        $stmt->bind_param("s", $ketua_nim);
        $stmt->execute();
        $result = $stmt->get_result();
        $no_kelompok_db = null;
    
        if ($row = $result->fetch_assoc()) {
            $no_kelompok_db = $row['no_kelompok'];
        }
        $stmt->close();
    
        if ($no_kelompok_db) {
            // Update status anggota lama dan ketua baru
            $update_anggota_sql = "UPDATE mahasiswa m
                                   JOIN kpconnection kc ON m.nim = kc.nim
                                   SET m.status = 'anggota'
                                   WHERE kc.no_kelompok = ? AND m.status = 'ketua';";
            $stmt = $conn->prepare($update_anggota_sql);
            $stmt->bind_param("i", $no_kelompok);
            $stmt->execute();
            $stmt->close();
    
            $update_ketua_sql = "UPDATE mahasiswa m
                                 JOIN kpconnection kc ON m.nim = kc.nim
                                 SET m.status = 'ketua'
                                 WHERE m.nim = ? AND kc.no_kelompok = ?";
            $stmt = $conn->prepare($update_ketua_sql);
            $stmt->bind_param("si", $ketua_nim, $no_kelompok);
            $stmt->execute();
            $stmt->close();
    
            // Update judul KP
            $update_data_sql = "UPDATE kelompok k
                                JOIN kpconnection kc ON k.no_kelompok = kc.no_kelompok
                                SET k.judul_kp = ?
                                WHERE kc.no_kelompok = ?";
            $stmt = $conn->prepare($update_data_sql);
            $stmt->bind_param("si", $judul_kp, $no_kelompok);
            $stmt->execute();
            $stmt->close();
    
            // Cari id_mitra berdasarkan nama mitra
            $get_mitra_sql = "SELECT id_mitra FROM mitra WHERE nama_mitra = ?";
            $stmt = $conn->prepare($get_mitra_sql);
            $stmt->bind_param("s", $nama_mitra);
            $stmt->execute();
            $result = $stmt->get_result();
            $id_mitra = null;
    
            if ($row = $result->fetch_assoc()) {
                $id_mitra = $row['id_mitra'];
            }
            $stmt->close();
    
            if ($id_mitra !== null) {
                // Update mitra jika sudah ada
                $update_mitra_sql = "UPDATE mitra
                                     SET nama_mitra = ?, lokasi = ?
                                     WHERE id_mitra = ?";
                $stmt = $conn->prepare($update_mitra_sql);
                $stmt->bind_param("ssi", $nama_mitra, $lokasi_mitra, $id_mitra);
                $stmt->execute();
                $stmt->close();
            } else {
                // Tambahkan mitra baru
                $insert_mitra_sql = "INSERT INTO mitra (nama_mitra, lokasi) VALUES (?, ?)";
                $stmt = $conn->prepare($insert_mitra_sql);
                $stmt->bind_param("ss", $nama_mitra, $lokasi_mitra);
                $stmt->execute();
                $id_mitra = $stmt->insert_id;
                $stmt->close();
            }
    
            // Update id_mitra di kpconnection
            $update_mitra_sql = "UPDATE kpconnection kc
                                 SET kc.id_mitra = ?
                                 WHERE kc.no_kelompok = ?";
            $stmt = $conn->prepare($update_mitra_sql);
            $stmt->bind_param("ii", $id_mitra, $no_kelompok);
            $stmt->execute();
            $stmt->close();
    
            // Redirect setelah update
            header("Location: infokelompok.php");
            exit;
        } else {
            echo "No Kelompok tidak ditemukan.";
        }
    }
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
    <style>
        .alert-fixed {
            position: fixed;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1050;
            margin: 10px 0;
            width: auto;
            max-width: 80%;
        }

        .nav-link.disabled {
    pointer-events: none; /* Mencegah klik */
    color: #6c757d; /* Warna abu-abu untuk tampilan disable */
    cursor: not-allowed; /* Ubah kursor menjadi tanda larangan */
}

    </style>
</head>
<body>
    <body class="layout-fixed sidebar-expand-lg"> <!--begin::App Wrapper-->
        <div class="app-wrapper"> <!--begin::Header-->
            <nav class="app-header navbar navbar-expand bg-body"> <!--begin::Container-->
                <div class="container-fluid"> <!--begin::Start Navbar Links-->
                    <h5 class="brand-text text-dark ms-2 align-middle">KERJA PRAKTEK FAKULTAS ILMU KOMPUTER</h5> <!--end::Brand Text-->
                    <!-- <ul class="navbar-nav">
                        <li class="nav-item"> <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"> <i class="bi bi-list"></i> </a> </li>
                        <li class="nav-item d-none d-md-block"> <a href="#" class="nav-link">Home</a> </li>
                        <li class="nav-item d-none d-md-block"> <a href="#" class="nav-link">Contact</a> </li>
                    </ul> end::Start Navbar Links begin::End Navbar Links -->
                    <ul class="navbar-nav ms-auto"> <!--begin::Navbar Search-->
                        <!--begin::User Menu Dropdown-->
                        <li class="nav-item dropdown user-menu"> <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"> <img src="/dist/assets/img/user2-160x160.jpg" class="user-image rounded-circle shadow" alt="User Image"> <span class="d-none d-md-inline"><?php echo htmlspecialchars($_SESSION['user_name']);?></span> </a>
                            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end"> <!--begin::User Image-->
                                <li class="user-header text-bg-primary"> <img src="/dist/assets/img/user2-160x160.jpg" class="rounded-circle shadow" alt="User Image">
                                    <p>
                                    <?php echo htmlspecialchars($_SESSION['user_name']);?>
                                        <small>Member since Nov. 2023</small>
                                    </p>
                                </li> <!--end::User Image--> <!--begin::Menu Body-->
                                <li class="user-body"> <!--begin::Row-->
                                    <div class="row">
                                        <div class="col-4 text-center"> <a href="#">Followers</a> </div>
                                        <div class="col-4 text-center"> <a href="#">Sales</a> </div>
                                        <div class="col-4 text-center"> <a href="#">Friends</a> </div>
                                    </div> <!--end::Row-->
                                </li> <!--end::Menu Body--> <!--begin::Menu Footer-->
                                <li class="user-footer"> <a href="#" class="btn btn-default btn-flat">Profile</a> <a href="#" class="btn btn-default btn-flat float-end">Sign out</a> </li> <!--end::Menu Footer-->
                            </ul>
                        </li> <!--end::User Menu Dropdown-->
                    </ul> <!--end::End Navbar Links-->
                </div> <!--end::Container-->
            </nav> <!--end::Header--> <!--begin::Sidebar-->
            <aside class="app-sidebar shadow" style="background-color: rgb(0, 0, 58); color: white;" data-bs-theme="dark"> <!--begin::Sidebar Brand-->
                <div class="sidebar-brand bg-light"> <!--begin::Brand Link--> <a href="./home.html" class="brand-link"> <!--begin::Brand Image--> <img src="/dist/assets/img/LOGOFKOM.png" alt="AdminLTE Logo" class="brand-image opacity-75 shadow"> <!--end::Brand Image--> <!--begin::Brand Text-->  </a> <!--end::Brand Link--> </div> <!--end::Sidebar Brand--> <!--begin::Sidebar Wrapper-->
                <div class="sidebar-wrapper">
                    <nav class="mt-2"> <!--begin::Sidebar Menu-->
                        <ul class="nav sidebar-menu flex-column" role="menu">
                            <li class="nav-item"> <a href="infokp.php" class="nav-link"><i class="nav-icon bi bi-info-circle-fill"></i>
                                <p>Informasi KP</p>
                            </a> </li>
                            <li class="nav-item"> <a href="infomhs.php" class="nav-link"> <i class="nav-icon bi bi-person-fill"></i>
                                    <p>Info Mahasiswa</p>
                                </a> </li>
                                <li class="nav-item">  <a href="infokelompok.php" 
       class="nav-link <?php echo $is_no_kelompok_empty == 1 ? 'disabled' : ''; ?>" 
       <?php echo $is_no_kelompok_empty == 1 ? 'tabindex="-1" aria-disabled="true"' : ''; ?>>
        <i class="nav-icon bi bi-people-fill"></i>
        <p>Info Kelompok</p>
    </a> </li>
                            <li class="nav-item"><a href="persuratan.php" 
       class="nav-link <?php echo ($is_no_kelompok_empty == 1 || $is_disabled) ? 'disabled' : ''; ?>" 
       <?php echo ($is_no_kelompok_empty == 1 || $is_disabled) ? 'tabindex="-1" aria-disabled="true"' : ''; ?>>
        <i class="nav-icon bi bi-envelope-fill"></i>
        <p>Persuratan</p>
    </a></li>
                            <li class="nav-item"> <a href="../../logout.php" class="nav-link"> <i class="nav-icon bi bi-box-arrow-left"></i>
                                    <p>Logout</p>
                                </a> </li>
                        </ul>
                    </nav>
                </div> <!--end::Sidebar Wrapper-->
            </aside> <!--end::Sidebar--> <!--begin::App Main-->
            <main class="app-main">
                <div class="container-fluid px-5 pt-3">
                    <h1>Informasi Kelompok</h1>
                    <form class="row g-3 py-5" method="POST">
                    <div class="row mb-3">
        <label for="emailform" class="col-sm-2 col-form-label">Nama Kelompok</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="emailform" placeholder="Nama Kelompok" value="Kelompok <?= htmlspecialchars($mahasiswa_data[0]['no_kelompok']) ?>" disabled>
        </div>
    </div>
    <div class="row mb-3">
        <label for="exampleFormControlTextarea1" class="col-sm-2 col-form-label">Judul Projek</label>
        <div class="col-sm-10">
            <textarea class="form-control" id="exampleFormControlTextarea1" name="judul_kp" rows="2" required style="padding-left: 13px; margin: 0; text-align: left;"><?= htmlspecialchars($mahasiswa_data[0]['judul_kp']) ?></textarea>
        </div>
    </div>
    <div class="row mb-3">
        <label for="validationCustom02" class="col-sm-2 col-form-label">DPL</label>
        <div class="col-sm-10"><input type="text" class="form-control" id="validationCustom02" placeholder="Masukkan Nama DPL" value="<?= htmlspecialchars($mahasiswa_data[0]['nama_dosen']) ?>" disabled></div>
    </div>
    <div class="row mb-3">
        <label for="validationCustomUsername" class="col-sm-2 col-form-label">Mitra</label>
        <div class="col-sm-10"><input type="text" class="form-control" id="validationCustom02" name="nama_mitra" placeholder="Masukkan Nama Mitra" value="<?= htmlspecialchars($mahasiswa_data[0]['nama_mitra']) ?>" required></div>
    </div>
    <div class="row mb-3">
    <label for="lokasi" class="col-sm-2 col-form-label">Lokasi</label>
    <div class="col-sm-10">
        <input type="text" class="form-control" id="lokasi" name="lokasi" placeholder="Masukkan Lokasi Mitra" value="<?= htmlspecialchars($mahasiswa_data[0]['lokasi']) ?>" required>
    </div>
</div>
    <div class="row mb-3">
        <label for="ketua_nim" class="col-sm-2 col-form-label">Nama Ketua</label>
        <div class="col-sm-10">
            <select class="form-select" aria-label="Default select example" name="ketua_nim" required>
                <option selected>--Pilih--</option>
                <?php foreach ($mahasiswa_data as $mhs): ?>
                    <option value="<?= $mhs['nim'] ?>" <?= $mhs['status'] == 'ketua' ? 'selected' : '' ?>>
                        <?= htmlspecialchars($mhs['nama']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

                        <h5>Data Mahasiswa</h5>
                        <table class="table table-bordered mt-3">
    <thead class="text-center">
        <tr>
            <th width="12%">NIM</th>
            <th width="40%">Nama Mahasiswa</th>
            <th width="12%">Kelas</th>
            <th width="12%">Telp</th>
            <th width="12%">Ukuran Jaket</th>
            <th width="12%">Status Validasi</th>
        </tr>
    </thead>
    <tbody class="text-center">
        <?php if (!empty($mahasiswa_data)): ?>
            <?php foreach ($mahasiswa_data as $mhs): ?>
    <tr>
        <td><?= htmlspecialchars($mhs['nim']) ?></td>
        <td class="text-start"><?= htmlspecialchars($mhs['nama']) ?></td>
        <td><?= htmlspecialchars($mhs['kelas']) ?></td>
        <td><?= htmlspecialchars($mhs['telp']) ?></td>
        <td><?= htmlspecialchars($mhs['jaket']) ?></td>
        <td><?= $mhs['status_validasi'] == '1' ? 'Valid' : 'Belum Valid' ?></td>
    </tr>
<?php endforeach; ?>

        <?php else: ?>
            <tr>
                <td colspan="7">Data tidak ditemukan.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

                        <div class="col-12">
                            <button class="btn btn-primary" type="submit">PERBARUI</button>
                          </div>
                    </form>
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