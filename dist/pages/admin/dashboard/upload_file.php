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
        return "File berhasil diunggah dan diperbarui: <a href='$targetFilePath' target='_blank'>Lihat File</a>";
    } else {
        return "Maaf, ada kesalahan saat mengunggah file.";
    }
}

// Penanganan form upload untuk file jadwal
if (isset($_POST['uploadJadwal'])) {
    $fileType = strtolower(pathinfo($_FILES['jadwalFile']['name'], PATHINFO_EXTENSION));
    $allowedTypes = array('jpg', 'jpeg', 'png', 'gif'); // Format gambar yang diperbolehkan

    if (in_array($fileType, $allowedTypes)) {
        // Nama file tetap untuk jadwal
        echo replaceFile($uploadDir, "jadwal_kp.jpg", $_FILES['jadwalFile']['tmp_name']);
    } else {
        echo "Hanya file gambar yang diperbolehkan (JPG, JPEG, PNG, GIF).";
    }
}

// Penanganan form upload untuk file pedoman
if (isset($_POST['uploadPedoman'])) {
    $fileType = strtolower(pathinfo($_FILES['pedomanFile']['name'], PATHINFO_EXTENSION));

    if ($fileType == 'pdf') {
        // Nama file tetap untuk pedoman
        echo replaceFile($uploadDir, "pedoman_kp.pdf", $_FILES['pedomanFile']['tmp_name']);
    } else {
        echo "Hanya file PDF yang diperbolehkan.";
    }
}
?>
