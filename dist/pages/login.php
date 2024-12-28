<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Sanitasi input
    $email = $conn->real_escape_string($email);
    $password = $conn->real_escape_string($password);

    // Query untuk memeriksa login
    $query = "SELECT * FROM admin WHERE email = '$email' AND password = '$password'";
    $result = $conn->query($query);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['nama'];
        $_SESSION['user_role'] = $user['role'];

        // Set pesan berhasil login
        $_SESSION['success'] = "Login berhasil! Selamat datang, {$user['nama']}.";

        // Redirect berdasarkan role
        if ($user['role'] == 'admin') {
            header("Location: admin/dashboard/infokp.php");
        } elseif ($user['role'] == 'staff_umum') {
            header("Location: staff-umum/dashboard/infokp.php");
        } elseif ($user['role'] == 'staff_keu') {
            header("Location: staff-keu/dashboard/infokp.php");
        }
        exit;
    } else {
        // Jika login gagal
        $_SESSION['error'] = "Email atau password salah!";
        header("Location: index.php");
        exit;
    }
}
?>
