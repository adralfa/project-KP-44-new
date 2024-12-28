<?php
include 'koneksi.php';

// Ambil data dari form
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Enkripsi password
$nim = $_POST['nim'];
$nama = $_POST['nama'];
$jenis_kelamin = $_POST['jk'];
$program_studi = $_POST['prodi'];
$angkatan = $_POST['angkatan'];
$kelas = $_POST['kelas'];
$keikutsertaan_mbkm = $_POST['mbkm'];
$ukuran_jaket = $_POST['jaket'];

// Query insert data
$sql = "INSERT INTO mahasiswa (email, password, nim, nama, jk, prodi, angkatan, kelas, mbkm, jaket, status) 
        VALUES ('$email', '$password', '$nim', '$nama', '$jenis_kelamin', '$program_studi', '$angkatan', '$kelas', '$keikutsertaan_mbkm', '$ukuran_jaket', 'anggota')";

if ($conn->query($sql) === TRUE) {
    echo "Pendaftaran berhasil disimpan!";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Tutup koneksi
$conn->close();
?>
