<?php
session_start();
include "../config/koneksi.php";

// Pastikan hanya admin yang bisa menambah user
if (!isset($_SESSION['username']) || $_SESSION['level'] != "admin") {
    die("Akses ditolak!");
}

// Proses form register
if (isset($_POST['register'])) {
    // Bersihkan input agar aman dari SQL Injection
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $level    = mysqli_real_escape_string($koneksi, $_POST['level']);

    // Cek apakah username sudah ada
    $cek = mysqli_query($koneksi, "SELECT * FROM tbl_user WHERE username='$username'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Username sudah dipakai!'); window.location='../halaman/register.php';</script>";
        exit;
    }

    // Simpan user baru
    $query = "INSERT INTO tbl_user (username, password, level) VALUES ('$username', '$password', '$level')";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('User baru berhasil ditambahkan!'); window.location='../halaman/register.php';</script>";
        exit;
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>
