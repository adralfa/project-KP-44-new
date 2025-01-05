<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    ini_set('display_errors', 0);
    error_reporting(0);
}

require '../../../../vendor/autoload.php';
require '../../koneksi.php';

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


use PhpOffice\PhpWord\TemplateProcessor;

// Validasi session nomor kelompok
if (!isset($_SESSION['no_kelompok'])) {
    $_SESSION['error_message'] = 'Nomor kelompok tidak ditemukan dalam session!';
}

$noKelompok = $_SESSION['no_kelompok'];

// Query mitra
$queryMitra = "
    SELECT m.id_mitra, m.nama_mitra 
    FROM mitra m 
    INNER JOIN kpconnection kc ON m.id_mitra = kc.id_mitra
    WHERE kc.no_kelompok = '$noKelompok'
";
$resultMitra = mysqli_query($conn, $queryMitra);
if (!$resultMitra) {
    error_log('Query mitra gagal: ' . mysqli_error($conn));
    $_SESSION['error_message'] = 'Gagal mengambil data mitra.';
    exit;
}

$mitra = mysqli_fetch_assoc($resultMitra);

// Query mahasiswa
$queryMahasiswa = "
    SELECT m.nama, m.nim, m.prodi 
    FROM mahasiswa m 
    INNER JOIN kpconnection kc ON kc.nim = m.nim
    WHERE kc.no_kelompok = '$noKelompok'
";
$resultMahasiswa = mysqli_query($conn, $queryMahasiswa);
if (!$resultMahasiswa) {
    error_log('Query mahasiswa gagal: ' . mysqli_error($conn));
    $_SESSION['error_message'] = 'Gagal mengambil data mahasiswa.';
    exit;
}

// Query riwayat surat
$queryRiwayatSurat = "SELECT * FROM surat WHERE no_kelompok = '$noKelompok' ORDER BY tanggal DESC";
$resultRiwayatSurat = mysqli_query($conn, $queryRiwayatSurat);

if (!$resultRiwayatSurat) {
    error_log('Query riwayat surat gagal: ' . mysqli_error($conn));
}

// Query untuk menghitung jumlah surat yang ada di database
$queryCountSurat = "SELECT COUNT(*) AS total FROM surat";
$resultCountSurat = mysqli_query($conn, $queryCountSurat);
if ($resultCountSurat) {
    $rowCount = mysqli_fetch_assoc($resultCountSurat);
    $totalSurat = $rowCount['total'];
    if ($totalSurat < 10) {
        $nextNomorSurat = "00" . ($totalSurat + 1); // Nomor surat berikutnya
    } elseif ($totalSurat < 100) {
        $nextNomorSurat = "0" . ($totalSurat + 1);
    } else {
        $nextNomorSurat = ($totalSurat + 1);
    }
} else {
    error_log('Gagal menghitung jumlah surat: ' . mysqli_error($conn));
    $nextNomorSurat = 1;
}


// Proses pembuatan surat
if (isset($_POST['submit'])) {
    try {
        // $nomorSurat = $_POST['nomor_surat'];
        $status_cetak = 0;
        $nomorSurat = $nextNomorSurat;
        
        // Folder tujuan
        $folderTujuan = '../../../../dist/assets/uploads/surat_output/';
        if (!is_dir($folderTujuan)) {
            mkdir($folderTujuan, 0777, true);
        }

        // Penamaan file output
        $baseFileName = "{$noKelompok}-surat-pengajuan";
        $suratCounter = 1;
        while (file_exists($folderTujuan . $baseFileName . "-{$suratCounter}.docx")) {
            $suratCounter++;
        }
        $fileName = $baseFileName . "-{$suratCounter}.docx";
        $outputFile = $folderTujuan . $fileName;
        
        // Format tanggal
        date_default_timezone_set('Asia/Jakarta');
        if (!date_default_timezone_set('Asia/Jakarta')) {
            $_SESSION['error_message'] = 'Gagal mengatur zona waktu.';
        }

        $datetime = new DateTime('now');
        $formatter = new IntlDateFormatter('id_ID', IntlDateFormatter::FULL, IntlDateFormatter::NONE);
        $formatter->setPattern('dd MMMM yyyy');
        $tanggal = $formatter->format($datetime);

        // Membuat file DOCX
        $templateProcessor = new TemplateProcessor('../../../../dist/assets/uploads/template_surat.docx');
        $templateProcessor->setValue('NOMOR_SURAT', $nomorSurat);
        $templateProcessor->setValue('TANGGAL', $tanggal);
        $templateProcessor->setValue('MITRA', $mitra['nama_mitra']);

        $jumlahMahasiswa = mysqli_num_rows($resultMahasiswa);
        $templateProcessor->cloneRow('NO', $jumlahMahasiswa);
        
        $no = 1;
        mysqli_data_seek($resultMahasiswa, 0);
        while ($row = mysqli_fetch_assoc($resultMahasiswa)) {
            $jenjang = ($row['prodi'] == 'MI') ? 'D3' : 'S1';

            $prodi = '';
            if ($row['prodi'] == 'TI') $prodi = 'Teknik Informatika S1';
            elseif ($row['prodi'] == 'SI') $prodi = 'Sistem Informasi S1';
            elseif ($row['prodi'] == 'MI') $prodi = 'Manajemen Informatika D3';
            elseif ($row['prodi'] == 'DKV') $prodi = 'Desain Komunikasi Visual S1';
            elseif ($row['prodi'] == 'TS') $prodi = 'Teknik Sipil S1';
            
            $templateProcessor->setValue("NO#{$no}", $no);
            $templateProcessor->setValue("NAMA#{$no}", $row['nama']);
            $templateProcessor->setValue("NIM#{$no}", $row['nim']);
            $templateProcessor->setValue("PRODI#{$no}", $prodi);
            $templateProcessor->setValue("JENJANG#{$no}", $jenjang);
            $no++;
        }

        // Simpan file
        // Simpan file DOCX
        if ($templateProcessor->saveAs($outputFile)) {
            // Log dan simpan data sebelum insert ke database
            // error_log("File berhasil disimpan di: $outputFile");
            // error_log("Data sebelum insert ke DB: no_kelompok={$noKelompok}, no_surat={$nomorSurat}, tanggal={$tanggal}, file_name={$fileName}");
        
            // Simpan data ke database
            echo $nomorSurat;
            $queryInsertHistory = "INSERT INTO surat (no_kelompok, no_surat, tanggal, file_name, status_cetak) VALUES (?, ?, ?, ?, ?)";

            $stmt = mysqli_prepare($conn, $queryInsertHistory);
            if (!$stmt) {
                error_log("Gagal menyiapkan statement: " . mysqli_error($conn));
                $_SESSION['error_message'] = 'Gagal menyiapkan statement untuk database.';
                exit;
            }

            $newTanggal = date('Y-m-d');  // Format: YYYY-MM-DD
            mysqli_stmt_bind_param($stmt, 'isssi', $noKelompok, $nomorSurat, $newTanggal, $fileName, $status_cetak);
        
            if (mysqli_stmt_execute($stmt)) {
                error_log("Data surat berhasil disimpan ke database.");
                $_SESSION['success_message'] = 'Surat berhasil dibuat dan disimpan!';
            } else {
                error_log('Gagal menyimpan data surat ke database: ' . mysqli_stmt_error($stmt));
                $_SESSION['error
                _message'] = 'Gagal menyimpan data surat ke database.';
            }
        
            mysqli_stmt_close($stmt);
        // } else {
        //     // Jika gagal menyimpan file, tampilkan error
        //     error_log("Gagal menyimpan file. Periksa apakah pathnya benar dan memiliki izin tulis: $outputFile");
        //     error_log("Error debug: " . json_encode(error_get_last()));
        //     echo '<div class="alert alert-danger">Gagal menyimpan file: ' . $outputFile . '.</div>';
        }

        // Simpan riwayat ke database
        $queryInsertHistory = "
            INSERT INTO surat (no_kelompok, no_surat, tanggal, file_name, status_cetak)
            VALUES ('$noKelompok', '$nomorSurat', NOW(), '$fileName', $status_cetak)
        ";

        if (mysqli_query($conn, $queryInsertHistory)) {
            error_log("Riwayat surat berhasil disimpan ke database.");
        } else {
            error_log('Gagal menyimpan riwayat surat ke database: ' . mysqli_error($conn));
        }

        // Tampilkan tautan unduh
        header('Location: persuratan.php');
        $_SESSION['success_message'] = 'Surat berhasil dibuat: <a href="' . $outputFile . '" download>Download Surat</a>';
        exit;

    } catch (Exception $e) {
        error_log('Terjadi kesalahan: ' . $e->getMessage());
        // header('Location: suratkemitra.php');
        echo '<div class="alert alert-danger">Terjadi kesalahan: ' . $e->getMessage() . '</div>';
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../../../css/adminlte.min.css" crossorigin="anonymous"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="../../../js/adminlte.min.js" crossorigin="anonymous"></script>
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
            <?php if (isset($_SESSION['alert_message'])): ?>
                    <div class="alert alert-<?= $_SESSION['alert_type'] ?> alert-dismissible fade show" role="alert">
                        <?= $_SESSION['alert_message'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php
                    // Clear session variables after displaying the message
                    unset($_SESSION['alert_message']);
                    unset($_SESSION['alert_type']);
                    ?>
                <?php endif; ?>
                <div class="container-fluid">
                <div class="content-wrapper">
        <div class="content">
            <div class="container">
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title fw-bold">Form Pengajuan Surat</h3>
                    </div>
                    <form id="suratForm" method="POST">
                        <div class="card-body">
                            <div class="form-group py-2">
                                <label for="nomor_surat">Nomor Surat</label>
                                <input type="text" class="form-control" id="nomor_surat" name="nomor_surat" value="<?= $nextNomorSurat ?>" disabled>
                            </div>
                            <div class="form-group py-2">
                                <label for="mitra">Pilih Mitra</label>
                                <input type="text" class="form-control" id="mitra" value="<?= $mitra['nama_mitra'] ?>" disabled>
                            </div>
                            <div class="form-group py-2">
                                <label for="data_mahasiswa">Data Mahasiswa</label>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>NIM</th>
                                            <th>Nama</th>
                                            <th>Prodi</th>
                                            <th>Jenjang</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
$no = 1;
mysqli_data_seek($resultMahasiswa, 0);
while ($row = mysqli_fetch_assoc($resultMahasiswa)) {
    // Menentukan jenjang pendidikan dan prodi
    $jenjang = ($row['prodi'] == 'MI') ? 'D3' : 'S1';
    $prodi = $row['prodi'];

    // Jika prodi adalah 'TI', ubah menjadi 'Teknik Informatika S1'
    switch ($prodi) {
        case 'TI':
            $prodi = 'Teknik Informatika S1';
            break;

        case 'SI':
            $prodi = 'Sistem Informasi S1';
            break;

        case 'DKV':
            $prodi = 'Desain Komunikasi Visual S1';
            break;

        case 'MI':
            $prodi = 'Manajemen Informatika D3';
            break;

        case 'TS':
            $prodi = 'Teknik Sipil S1';
            break;
        
        default:
            break;
    }

    echo "<tr>
        <td>{$no}</td>
        <td>{$row['nim']}</td>
        <td>{$row['nama']}</td>
        <td>{$prodi}</td>
        <td>{$jenjang}</td>
    </tr>";
    $no++;
}
?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" name="submit" class="btn btn-primary">Buat Surat</button>
                        </div>
                    </form>
                </div>
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title fw-bold">Riwayat Pembuatan Surat</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nomor Surat</th>
                                    <th>Tanggal</th>
                                    <th>File</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $counter = 1;
                                while ($row = mysqli_fetch_assoc($resultRiwayatSurat)) {
                                    echo "<tr>
                                        <td>{$counter}</td>
                                        <td>{$row['no_surat']}</td>
                                        <td>{$row['tanggal']}</td>
                                        <td><a href='../../../../dist/assets/uploads/surat_output/{$row['file_name']}' target='_blank'>Buka File</a></td>
                                    </tr>";
                                    $counter++;
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
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