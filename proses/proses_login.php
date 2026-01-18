<?php
session_start();
include "../config/koneksi.php";

$username = mysqli_real_escape_string($koneksi, $_POST['username']);
$password = $_POST['password'];

$q = mysqli_query($koneksi, "SELECT * FROM tbl_user WHERE username='$username' LIMIT 1");
$user = mysqli_fetch_assoc($q);

if ($user) {
    //  Verifikasi password aman
    if (password_verify($password, $user['password'])) {
        $_SESSION['id_user']  = $user['id_user'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['level']    = $user['level'];

        header("Location: ../halaman/dashboard.php");
        exit;
    }
}

// Jika tidak cocok atau user tidak ditemukan
echo "<script>alert('Login gagal! Username atau password salah'); window.location='../index.php';</script>";
?>
