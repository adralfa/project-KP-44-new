<?php
// Include file koneksi ke database
include '../../koneksi.php';

// Cek apakah data dikirim dengan metode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nim = $_POST['nim'] ?? null;
    $statusValidasi = $_POST['status_validasi'] ?? null;

    if ($nim && $statusValidasi !== null) {
        // Update status_validasi di database
        $stmt = $conn->prepare("UPDATE mahasiswa SET status_validasi = ? WHERE nim = ?");
        $stmt->bind_param("is", $statusValidasi, $nim);

        if ($stmt->execute()) {
            header("Location: " . $_SERVER['HTTP_REFERER']); // Kembali ke halaman sebelumnya
            exit;
        } else {
            echo "Gagal menyimpan perubahan.";
        }
        $stmt->close();
    } else {
        echo "Data tidak valid.";
    }
} else {
    echo "Metode tidak diizinkan.";
}
?>
