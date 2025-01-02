<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    error_reporting(0);
}
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'staff_umum') {
    header("Location: ../../index.php");
    exit;
}

// Include file koneksi ke database
include '../../koneksi.php';

// Cek apakah parameter ID ada
if (!isset($_GET['id'])) {
    die("ID tidak ditemukan.");
}

$id = intval($_GET['id']);

// Query untuk mengambil data surat berdasarkan ID
$sql = "SELECT file_name FROM surat WHERE id = ? AND status_cetak = 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

// Cek apakah surat ditemukan
if ($result->num_rows === 0) {
    die("Dokumen tidak ditemukan atau sudah dicetak.");
}

$row = $result->fetch_assoc();
$fileName = $row['file_name'];

// Path file surat
$filePath = "../../../assets/uploads/surat_output/" . $fileName;

// Cek apakah file surat ada
if (!file_exists($filePath)) {
    die("File tidak ditemukan.");
}

// Logika untuk mencetak file (simulasi)
header("Content-type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
header("Content-Disposition: attachment; filename=\"" . basename($filePath) . "\"");
readfile($filePath);

// Perbarui status pencetakan menjadi "sudah"
$updateSql = "UPDATE surat SET status_cetak = 1 WHERE id = ?";
$updateStmt = $conn->prepare($updateSql);
$updateStmt->bind_param("i", $id);
$updateStmt->execute();

// Redirect kembali ke halaman sebelumnya (opsional)
header("Location: permintaan-surat.php");
exit;
?>
