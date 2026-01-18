<?php
session_start();
include "config/koneksi.php";

// proses register
if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $level    = $_POST['level'];

    // kalau level = petugas → tolak
    if ($level === "petugas") {
        echo "<script>alert('Akses ditolak! Hanya Admin yang boleh dibuat.'); window.location='register.php';</script>";
        exit;
    }

    // cek username unik
    $cek = mysqli_query($koneksi, "SELECT * FROM tbl_user WHERE username='$username'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Username sudah dipakai!');</script>";
    } else {
        $q = "INSERT INTO tbl_user (username, password, level) VALUES ('$username', '$password', '$level')";
        if (mysqli_query($koneksi, $q)) {
            echo "<script>alert('Akun Admin berhasil dibuat! Silakan login.'); window.location='index.php';</script>";
        } else {
            echo "<script>alert('Gagal membuat akun!');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Register - Aplikasi Kasir</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    .login-container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }
    .login-box {
        background: #fff;
        padding: 25px;
        border-radius: 10px;
        width: 300px;
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }
    .login-box h2 {
        text-align: center;
        margin-bottom: 20px;
    }
    .login-box label {
        font-weight: bold;
        margin-top: 10px;
        display: block;
    }
    .login-box input,
    .login-box select {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-sizing: border-box;
    }
    .login-box button {
        width: 100%;
        padding: 10px;
        margin-top: 20px;
        background: #1e40af;
        color: #fff;
        border: none;
        border-radius: 5px;
        font-weight: bold;
        cursor: pointer;
    }
    .login-box button:hover {
        background: #1d4ed8;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <div class="login-box">
      <h2>Register Akun</h2>
      <form method="POST">
        <label>Username</label>
        <input type="text" name="username" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <label>Level</label>
        <select name="level" required>
          <option value="admin">Admin</option>
        </select>

        <button type="submit" name="register">REGISTER</button>
      </form>
      <p style="margin-top:10px; text-align:center;">
        <a href="index.php">Kembali ke Login</a>
      </p>
    </div>
  </div>
</body>
</html>
