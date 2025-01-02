<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    error_reporting(0);
}

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'mahasiswa') {
    header("Location: ../../index.php");
    exit;
}

include('../../koneksi.php'); // File koneksi database

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

// Ambil data mahasiswa berdasarkan sesi
$user_nim = $_SESSION['user_nim']; // NIM mahasiswa dari sesi
$query = "SELECT * FROM mahasiswa WHERE nim = '$user_nim'";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $mahasiswa = $result->fetch_assoc();
} else {
    $mahasiswa = null; // Jika data tidak ditemukan
}

$status_val = $mahasiswa['status_validasi'] == 1 ? "Valid" : "Belum Valid";

// Proses pembaruan data
if (isset($_POST['submit_data'])) {
    $nama = $conn->real_escape_string($_POST['nama']);
    $jk = $conn->real_escape_string($_POST['jk']);
    $telp = $conn->real_escape_string($_POST['telp']);
    $prodi = $conn->real_escape_string($_POST['prodi']);
    $angkatan = $conn->real_escape_string($_POST['angkatan']);
    $kelas = $conn->real_escape_string($_POST['kelas']);
    $mbkm = $conn->real_escape_string($_POST['mbkm']);

    $updateQuery = "UPDATE mahasiswa SET 
                        nama = '$nama',
                        jk = '$jk',
                        telp = '$telp', 
                        prodi = '$prodi', 
                        angkatan = '$angkatan', 
                        kelas = '$kelas', 
                        mbkm = '$mbkm' 
                    WHERE nim = '$user_nim'";

    if ($conn->query($updateQuery)) {
        $_SESSION['success_message'] = "Data berhasil diperbarui!";
        header("Location: infomhs.php");
        exit;
    } else {
        $_SESSION['error_message'] = "Gagal memperbarui data: " . $conn->error;
        header("Location: infomhs.php");
        exit;
    }
}

// Proses upload file
if (isset($_POST['submit_upload'])) {
    if (isset($_FILES['file_bukti']) && $_FILES['file_bukti']['error'] == 0) {
        $file_name = $mahasiswa['nim'] . "_bukti-pembayaran." . pathinfo($_FILES['file_bukti']['name'], PATHINFO_EXTENSION);
        $file_tmp = $_FILES['file_bukti']['tmp_name'];
        $upload_dir = '../../../assets/uploads/bukti-pembayaran/';
        $file_path = $upload_dir . $file_name;

        // Validasi ekstensi file
        $allowed_extensions = ['jpg', 'jpeg', 'png'];
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);

        if (in_array($file_ext, $allowed_extensions)) {
            $existing_file = $upload_dir . $mahasiswa['nim'] . "_bukti-pembayaran." . pathinfo($mahasiswa['file_upload'], PATHINFO_EXTENSION);
            if (file_exists($existing_file)) {
                unlink($existing_file); // Hapus file lama
            }
            if (move_uploaded_file($file_tmp, $file_path)) {
                // Simpan nama file ke database
                $updateFileQuery = "UPDATE mahasiswa SET file_upload = '$file_name' WHERE nim = '$user_nim'";
                if ($conn->query($updateFileQuery)) {
                    $_SESSION['success_message'] = "File berhasil diunggah dan disimpan!";
                    header("Location: infomhs.php");
                    exit;
                } else {
                    $_SESSION['error_message'] = "Gagal menyimpan nama file: " . $conn->error;
                    header("Location: infomhs.php");
                }
            } else {
                $_SESSION['error_message'] = "Gagal mengunggah file.";
                header("Location: infomhs.php");
            }
        } else {
            $_SESSION['error_message'] = "Ekstensi file tidak diizinkan. Hanya JPG, JPEG, PNG yang diperbolehkan.";
            header("Location: infomhs.php");
        }
    } else {
        $_SESSION['error_message'] = "Terjadi kesalahan saat mengunggah file.";
        header("Location: infomhs.php");
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
    <body class="layout-fixed sidebar-expand-lg">

    <?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success alert-dismissible alert-fixed mx-3 mt-3 fade show" id="alert" role="alert">
        <?= $_SESSION['success_message'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['success_message']); endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger alert-dismissible alert-fixed mx-3 mt-3 fade show" id="alert" role="alert">
        <?= $_SESSION['error_message'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['error_message']); endif; ?>

        <div class="app-wrapper">
            <nav class="app-header navbar navbar-expand bg-body">
                <div class="container-fluid">
                    <h5 class="brand-text text-dark ms-2 align-middle">KERJA PRAKTEK FAKULTAS ILMU KOMPUTER</h5>
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown user-menu">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                                <img src="/dist/assets/img/user2-160x160.jpg" class="user-image rounded-circle shadow" alt="User Image">
                                <span class="d-none d-md-inline"><?php echo htmlspecialchars($_SESSION['user_name']);?></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
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
                <div class="container-fluid px-5 py-3">
                    <h1>Informasi Mahasiswa</h1>
                    

                    <?php if ($mahasiswa): ?>
                    <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) . '?v=' . time(); ?>" method="post" class="row g-3 py-5">
                        <div class="row mb-3">
                            <label for="emailform" class="col-sm-2 col-form-label">Email</label>
                            <div class="col-sm-10">
                                <input type="email" class="form-control" name="email" id="emailform" value="<?= htmlspecialchars($mahasiswa['email']) ?>" disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="nim" class="col-sm-2 col-form-label">NIM</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="nim" id="nim" value="<?= htmlspecialchars($mahasiswa['nim']) ?>" disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="nama" class="col-sm-2 col-form-label">Nama</label>
                            <div class="col-sm-10">
                                <input type="text" name="nama" class="form-control" id="nama" value="<?= htmlspecialchars($mahasiswa['nama']) ?>" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="jk" class="col-sm-2 col-form-label">Jenis Kelamin</label>
                            <div class="col-sm-10">
                                <select name="jk" id="jk" class="form-select">
                                    <option value="L" <?= $mahasiswa['jk'] === 'L' ? 'selected' : '' ?>>Laki-Laki</option>
                                    <option value="P" <?= $mahasiswa['jk'] === 'P' ? 'selected' : '' ?>>Perempuan</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="telp" class="col-sm-2 col-form-label">Telp</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="telp" id="telp" value="<?= htmlspecialchars($mahasiswa['telp']) ?>">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="prodi" class="col-sm-2 col-form-label">Program Studi</label>
                            <div class="col-sm-10">
                                <select name="prodi" id="prodi" class="form-select">
                                    <option value="TI" <?= $mahasiswa['prodi'] === 'TI' ? 'selected' : '' ?>>Teknik Informatika - S1</option>
                                    <option value="SI" <?= $mahasiswa['prodi'] === 'SI' ? 'selected' : '' ?>>Sistem Informasi - S1</option>
                                    <option value="DKV" <?= $mahasiswa['prodi'] === 'DKV' ? 'selected' : '' ?>>Desain Komunikasi Visual - S1</option>
                                    <option value="MI" <?= $mahasiswa['prodi'] === 'MI' ? 'selected' : '' ?>>Manajemen Informatika - D3</option>
                                    <option value="TS" <?= $mahasiswa['prodi'] === 'TS' ? 'selected' : '' ?>>Teknik Sipil - S1</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="angkatan" class="col-sm-2 col-form-label">Angkatan</label>
                            <div class="col-sm-10">
                                <input type="text" name="angkatan" class="form-control" id="angkatan" value="<?= htmlspecialchars($mahasiswa['angkatan']) ?>" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="kelas" class="col-sm-2 col-form-label">Kelas</label>
                            <div class="col-sm-10">
                                <select name="kelas" id="kelas" class="form-select">
                                    <option value="01" <?= $mahasiswa['kelas'] === '01' ? 'selected' : '' ?>>01</option>
                                    <option value="02" <?= $mahasiswa['kelas'] === '02' ? 'selected' : '' ?>>02</option>
                                    <option value="03" <?= $mahasiswa['kelas'] === '03' ? 'selected' : '' ?>>03</option>
                                    <option value="04" <?= $mahasiswa['kelas'] === '04' ? 'selected' : '' ?>>04</option>
                                    <option value="05" <?= $mahasiswa['kelas'] === '05' ? 'selected' : '' ?>>05</option>
                                    <option value="06" <?= $mahasiswa['kelas'] === '06' ? 'selected' : '' ?>>06</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="mbkm" class="col-sm-2 col-form-label">Keikutsertaan MBKM</label>
                            <div class="col-sm-10">
                                <select name="mbkm" id="mbkm" class="form-select">
                                    <option value="1" <?= $mahasiswa['mbkm'] === '1' ? 'selected' : '' ?>>Pernah ikut serta</option>
                                    <option value="0" <?= $mahasiswa['mbkm'] === '0' ? 'selected' : '' ?>>Tidak pernah ikut serta</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="status_validasi" class="col-sm-2 col-form-label">Status Validasi</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="status_validasi" id="status_validasi" placeholder="Status Validasi" value="<?= htmlspecialchars($status_val) ?>" disabled>
                            </div>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary" type="submit" name="submit_data">PERBARUI</button>
                        </div>
                    </form>
                    <?php endif; ?>
                    
                    <hr>

                    <h1>Upload Bukti Pembayaran</h1>

                    <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) . '?v=' . time(); ?>" method="post" enctype="multipart/form-data">
                        <div class="row py-3">
                            <label for="formFile" class="col-sm-2 col-form-label">Upload Bukti Pembayaran</label>
                            <div class="col-sm-10">
                                <input class="form-control" type="file" name="file_bukti" id="formFile" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" name="submit_upload" class="btn btn-primary">Upload</button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
        <script>
        document.addEventListener('DOMContentLoaded', function () {
        const alert = document.getElementById('alert');
        if (alert) {
            setTimeout(() => {
                alert.classList.remove('show'); // Menghilangkan animasi fade
                setTimeout(() => {
                    alert.remove(); // Menghapus elemen dari DOM
                }, 150); // Waktu sinkron dengan animasi fade
            }, 3000); // Durasi 3 detik sebelum alert dihapus
        }
    });
    </script>
    </body>
</html>
