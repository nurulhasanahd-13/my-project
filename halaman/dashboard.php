<?php
include "../fungsi/autentikasi.php";
include "../config/koneksi.php";

cekLogin(); // memastikan user sudah login

$level   = $_SESSION['level'];
$id_user = $_SESSION['id_user'];

// Hitung total produk, pelanggan, user
$totalProduk    = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM tbl_produk"))['jml'];
$totalPelanggan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM tbl_pelanggan"))['jml'];
$totalUser      = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM tbl_user"))['jml'];

// Total penjualan sesuai level
if ($level == 'admin') {
    $totalPenjualan = mysqli_fetch_assoc(
        mysqli_query($koneksi, "SELECT SUM(total_harga) as total FROM tbl_penjualan")
    )['total'];
} else {
    $totalPenjualan = mysqli_fetch_assoc(
        mysqli_query($koneksi, "SELECT SUM(total_harga) as total FROM tbl_penjualan WHERE id_user = '$id_user'")
    )['total'];
}

if (!$totalPenjualan) $totalPenjualan = 0;

// Ambil semua produk terbaru
$produk = mysqli_query($koneksi, "SELECT * FROM tbl_produk ORDER BY id_produk DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    .card a {
      text-decoration: none;
      color: inherit;
      display: block;
    }
    .card:hover {
      background: #f0f0f0;
      cursor: pointer;
    }
    .product-table {
      margin-top: 30px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }
    th, td {
      border: 1px solid #ddd;
      padding: 8px;
      text-align: left;
    }
    th {
      background-color: #f2f2f2;
    }
    tr:hover {
      background-color: #f9f9f9;
    }
  </style>
</head>
<body>
<div class="dashboard">

  <?php if (file_exists("sidebar.php")) include("sidebar.php"); ?>

  <main class="content">
    <h2>Selamat datang, <?= htmlspecialchars($_SESSION['username']); ?>!</h2>

    <div class="cards">
      <!-- Semua user bisa lihat -->
      <div class="card">
        <a href="penjualan.php">
          <h3>Rp <?= number_format($totalPenjualan, 0, ',', '.') ?></h3>
          <p>Total Penjualan</p>
        </a>
      </div>

      <div class="card">
        <a href="produk.php">
          <h3><?= $totalProduk ?></h3>
          <p>Produk</p>
        </a>
      </div>

      <!-- Hanya admin yang bisa lihat -->
      <?php if ($level == 'admin'): ?>
      <div class="card">
        <a href="pelanggan.php">
          <h3><?= $totalPelanggan ?></h3>
          <p>Pelanggan</p>
        </a>
      </div>

      <div class="card">
        <a href="user.php">
          <h3><?= $totalUser ?></h3>
          <p>User</p>
        </a>
      </div>
      <?php endif; ?>
    </div>

    <div class="product-table">
      <h3>Daftar Produk</h3>
      <table>
        <thead>
          <tr>
            <th>Nama Produk</th>
            <th>Harga</th>
            <th>Stok</th>
          </tr>
        </thead>
        <tbody>
          <?php if (mysqli_num_rows($produk) === 0): ?>
            <tr><td colspan="3" style="text-align:center;">Belum ada produk</td></tr>
          <?php else: ?>
            <?php while ($row = mysqli_fetch_assoc($produk)): ?>
              <tr>
                <td><?= htmlspecialchars($row['nama_produk']) ?></td>
                <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                <td><?= $row['stok'] ?></td>
              </tr>
            <?php endwhile; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

  </main>
</div>
</body>
</html>
