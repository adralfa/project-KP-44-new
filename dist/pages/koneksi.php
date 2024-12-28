<?php
    session_start(); // Mulai session
    // Koneksi ke database MySQL
    $host = "localhost";
    $user = "root";
    $password = "";
    $database = "kpfkom"; // Nama database
    
    $conn = new mysqli($host, $user, $password, $database);
    
    // Periksa koneksi
    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }
?>