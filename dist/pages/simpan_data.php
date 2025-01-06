<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
include 'koneksi.php';

// Ambil data dari form
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Enkripsi password
$nim = $_POST['nim'];
$nama = $_POST['nama'];
$jenis_kelamin = $_POST['jk'];
$telp = $_POST['telp'];
$program_studi = $_POST['prodi'];
$angkatan = $_POST['angkatan'];
$kelas = $_POST['kelas'];
$keikutsertaan_mbkm = $_POST['mbkm'];
$ukuran_jaket = $_POST['jaket'];

// Validasi email: harus berakhiran @uniku.ac.id
if (substr($email, -12) !== '@uniku.ac.id') {
    $_SESSION['status'] = 'danger';
    $_SESSION['message'] = 'Email harus berakhiran @uniku.ac.id';
    header("Location: pendaftaran.php"); // Kembali ke halaman pendaftaran
    exit();
}

// Validasi NIM: NIM harus sesuai dengan bagian email sebelum @
$email_nim_part = explode('@', $email)[0];
if ($nim !== $email_nim_part) {
    $_SESSION['status'] = 'danger';
    $_SESSION['message'] = 'NIM harus sesuai dengan bagian sebelum @ pada email.';
    header("Location: pendaftaran.php"); // Kembali ke halaman pendaftaran
    exit();
}

// Cek apakah email atau NIM sudah ada
$cek_query = "SELECT * FROM mahasiswa WHERE email='$email' OR nim='$nim'";
$result = $conn->query($cek_query);

if ($result->num_rows > 0) {
    $_SESSION['status'] = 'danger';
    $_SESSION['message'] = 'Email atau NIM sudah terdaftar!';
    header("Location: pendaftaran.php"); // Kembali ke halaman pendaftaran
    exit();
} else {
    // Jika lolos validasi, simpan data
    $sql = "INSERT INTO mahasiswa (email, password, nim, nama, jk, telp, prodi, angkatan, kelas, mbkm, jaket, status) 
            VALUES ('$email', '$password', '$nim', '$nama', '$jenis_kelamin', '$telp', '$program_studi', '$angkatan', '$kelas', '$keikutsertaan_mbkm', '$ukuran_jaket', 'anggota')";

    if ($conn->query($sql) === TRUE) {
        $_SESSION['status'] = 'success';
        $_SESSION['message'] = 'Sukses mendaftar. Silakan kembali ke <a href="index.php" class="border-none text-dark">Halaman Login</a>.';
        header("Location: pendaftaran.php"); // Berhasil, pindah ke halaman utama
    } else {
        $_SESSION['status'] = 'danger';
        $_SESSION['message'] = 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.';
        header("Location: pendaftaran.php"); // Gagal, kembali ke halaman pendaftaran
    }
}

$conn->close();
exit;
?>
