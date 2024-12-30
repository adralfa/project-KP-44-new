<?php
// Folder tempat file akan disimpan
$uploadDir = "../../../assets/uploads/";

// Fungsi untuk mengganti file lama dengan nama tetap
function replaceFile($uploadDir, $fileName, $tmpFilePath) {
    $targetFilePath = $uploadDir . $fileName;

    // Hapus file lama jika ada
    if (file_exists($targetFilePath)) {
        unlink($targetFilePath); // Hapus file lama
    }

    // Pindahkan file baru ke lokasi target
    if (move_uploaded_file($tmpFilePath, $targetFilePath)) {
        return true; // Berhasil diunggah
    } else {
        return false; // Gagal diunggah
    }
}

$redirectURL = "infokp.php"; // URL untuk redirect setelah upload

// Penanganan form upload untuk file jadwal
if (isset($_POST['uploadJadwal'])) {
    $fileType = strtolower(pathinfo($_FILES['jadwalFile']['name'], PATHINFO_EXTENSION));
    $allowedTypes = array('jpg', 'jpeg', 'png', 'gif'); // Format gambar yang diperbolehkan

    if (in_array($fileType, $allowedTypes)) {
        if (replaceFile($uploadDir, "jadwal_kp.jpg", $_FILES['jadwalFile']['tmp_name'])) {
            header("Location: $redirectURL?status=success&file=jadwal");
        } else {
            header("Location: $redirectURL?status=error&file=jadwal");
        }
    } else {
        header("Location: $redirectURL?status=invalid&file=jadwal");
    }
    exit;
}

// Penanganan form upload untuk file pedoman
if (isset($_POST['uploadPedoman'])) {
    $fileType = strtolower(pathinfo($_FILES['pedomanFile']['name'], PATHINFO_EXTENSION));

    if ($fileType == 'pdf') {
        if (replaceFile($uploadDir, "pedoman_kp.pdf", $_FILES['pedomanFile']['tmp_name'])) {
            header("Location: $redirectURL?status=success&file=pedoman");
        } else {
            header("Location: $redirectURL?status=error&file=pedoman");
        }
    } else {
        header("Location: $redirectURL?status=invalid&file=pedoman");
    }
    exit;
}
?>
