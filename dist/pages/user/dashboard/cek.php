<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

require '../../../../vendor/autoload.php';
require '../../koneksi.php';
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

use PhpOffice\PhpWord\TemplateProcessor;

$alertMessage = ''; // Variabel untuk alert message
$alertType = ''; // Variabel untuk tipe alert (success, danger, etc.)

// Validasi session nomor kelompok
if (!isset($_SESSION['no_kelompok'])) {
    $alertMessage = 'Nomor kelompok tidak ditemukan dalam session!';
    $alertType = 'danger';
}

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
    $alertMessage = 'Gagal mengambil data mitra.';
    $alertType = 'danger';
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
    $alertMessage = 'Gagal mengambil data mahasiswa.';
    $alertType = 'danger';
    exit;
}

// Proses pembuatan surat
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $nomorSurat = $_POST['nomor_surat'];
        
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
            $alertMessage = 'Gagal mengatur zona waktu.';
            $alertType = 'danger';
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

        // Simpan file DOCX
        if ($templateProcessor->saveAs($outputFile)) {
            // Log dan simpan data sebelum insert ke database
            error_log("File berhasil disimpan di: $outputFile");

            // Simpan data ke database
            $queryInsertHistory = "INSERT INTO surat (no_kelompok, no_surat, tanggal, file_name) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $queryInsertHistory);
            if (!$stmt) {
                error_log("Gagal menyiapkan statement: " . mysqli_error($conn));
                $alertMessage = 'Gagal menyiapkan statement untuk database.';
                $alertType = 'danger';
                exit;
            }

            $newTanggal = date('Y-m-d');  // Format: YYYY-MM-DD
            mysqli_stmt_bind_param($stmt, 'isss', $noKelompok, $nomorSurat, $newTanggal, $fileName);

            if (mysqli_stmt_execute($stmt)) {
                $alertMessage = 'Surat berhasil dibuat dan disimpan!';
                $alertType = 'success';
            } else {
                error_log('Gagal menyimpan data surat ke database: ' . mysqli_stmt_error($stmt));
                $alertMessage = 'Gagal menyimpan data surat ke database.';
                $alertType = 'danger';
            }

            mysqli_stmt_close($stmt);
        } else {
            // Jika gagal menyimpan file
            error_log("Gagal menyimpan file. Periksa apakah pathnya benar dan memiliki izin tulis: $outputFile");
            $alertMessage = 'Gagal menyimpan file surat.';
            $alertType = 'danger';
        }

    } catch (Exception $e) {
        error_log('Terjadi kesalahan: ' . $e->getMessage());
        $alertMessage = 'Terjadi kesalahan: ' . $e->getMessage();
        $alertType = 'danger';
    }
}
?>