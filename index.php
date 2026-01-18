<?php
session_start();
include "config/koneksi.php";

// kalau sudah login langsung ke dashboard
if (isset($_SESSION['username'])) {
    header("Location: halaman/dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login - Aplikasi Kasir</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <div class="login-container">
    <div class="login-box">
      <h2>APLIKASI KASIR</h2>
      <!-- Form Login -->
      <form action="proses/proses_login.php" method="POST">
        <label>Username</label>
        <input type="text" name="username" required>
        <label>Password</label>
        <input type="password" name="password" required>
        <button type="submit" class="btn">LOGIN</button>
      </form>
      <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
    </div>
  </div>
</body>
</html>
