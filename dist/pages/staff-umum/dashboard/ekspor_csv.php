<?php
// Menghubungkan ke database
include '../../koneksi.php';

// Query untuk mendapatkan data surat, mitra, dan mahasiswa
$sql = "
    SELECT surat.no_surat, surat.tanggal, surat.no_kelompok, mitra.nama_mitra AS mitra, 
           mahasiswa.nama, mahasiswa.prodi
    FROM surat
    JOIN kelompok ON surat.no_kelompok = kelompok.no_kelompok
    JOIN kpconnection ON kelompok.no_kelompok = kpconnection.no_kelompok
    JOIN mahasiswa ON kpconnection.nim = mahasiswa.nim
    JOIN mitra ON kpconnection.id_mitra = mitra.id_mitra
    WHERE surat.status_cetak = 0
    ORDER BY surat.no_kelompok
";

// Menjalankan query
$result = $conn->query($sql);

$jumlahSurat = 0;

$count = "SELECT COUNT(*) AS jumlah FROM surat WHERE status_cetak = 0";
$count_result = $conn->query($count);

if ($count_result) {
    $row = $count_result->fetch_assoc();
    $jumlahSurat = (int) $row['jumlah'];
} else {
    echo "Error: " . $conn->error;
}

// Membuat file CSV
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="surat_mahasiswa.csv"');

// Membuka output stream untuk menulis file CSV
$output = fopen('php://output', 'w');

// Menulis header CSV
fputcsv($output, ['No Surat', 'Tanggal', 'No Kelompok', 'Nama Mitra', 
                  'Nama Mahasiswa 1', 'Prodi 1', 'Jenjang 1',
                  'Nama Mahasiswa 2', 'Prodi 2', 'Jenjang 2',
                  'Nama Mahasiswa 3', 'Prodi 3', 'Jenjang 3',
                  'Nama Mahasiswa 4', 'Prodi 4', 'Jenjang 4',
                  'Nama Mahasiswa 5', 'Prodi 5', 'Jenjang 5']);

// Variabel untuk melacak kelompok sebelumnya
$kelompokPrev = null;
$mahasiswaList = []; // Daftar mahasiswa untuk kelompok tertentu
$kelompokData = [];  // Menyimpan data kelompok saat ini

// Fungsi untuk menulis data kelompok ke CSV
function tulisKelompokCSV($output, $kelompokData, $mahasiswaList) {
    $dataRow = [
        $kelompokData['no_surat'], $kelompokData['tanggal'], $kelompokData['no_kelompok'], $kelompokData['mitra']
    ];

    // Tambahkan data mahasiswa
    for ($i = 0; $i < 5; $i++) {
        if (isset($mahasiswaList[$i])) {
            $prodi = $mahasiswaList[$i]['prodi'];
            $jenjang = ($prodi === 'MI') ? 'D3' : 'S1';
            $dataRow[] = $mahasiswaList[$i]['nama'];
            $dataRow[] = $prodi;
            $dataRow[] = $jenjang;
        } else {
            $dataRow[] = '';
            $dataRow[] = '';
            $dataRow[] = '';
        }
    }

    // Tulis ke file CSV
    fputcsv($output, $dataRow);
}

// Proses setiap baris hasil query
while ($row = $result->fetch_assoc()) {
    // Jika kelompok berubah, tulis data kelompok sebelumnya ke CSV
    if ($kelompokPrev !== null && $kelompokPrev !== $row['no_kelompok']) {
        tulisKelompokCSV($output, $kelompokData, $mahasiswaList);
        $mahasiswaList = []; // Reset daftar mahasiswa
    }

    // Perbarui data kelompok
    $kelompokPrev = $row['no_kelompok'];
    $kelompokData = [
        'no_surat' => $row['no_surat'],
        'tanggal' => $row['tanggal'],
        'no_kelompok' => $row['no_kelompok'],
        'mitra' => $row['mitra']
    ];

    // Tambahkan mahasiswa ke daftar
    $mahasiswaList[] = [
        'nama' => $row['nama'],
        'prodi' => $row['prodi']
    ];
}

// Tulis data kelompok terakhir (jika ada)
if (!empty($mahasiswaList)) {
    tulisKelompokCSV($output, $kelompokData, $mahasiswaList);
}

// Menutup output stream
fclose($output);
?>
