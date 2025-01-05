<?php
// Pastikan session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Koneksi ke database
include '../../koneksi.php';

// Cek apakah data dikirim dengan POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nim = $_POST['nim'] ?? null;
    $statusValidasi = $_POST['status_validasi'] ?? null;

    // Pastikan nilai validasi dan nim ada
    if ($nim && $statusValidasi !== null) {
        // Update status validasi mahasiswa
        $stmt = $conn->prepare("UPDATE mahasiswa SET status_validasi = ? WHERE nim = ?");
        $stmt->bind_param("is", $statusValidasi, $nim);

        if ($stmt->execute()) {
            // Jika sukses, kembalikan ke halaman sebelumnya
            header("Location: " . $_SERVER['HTTP_REFERER']);
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
