<?php
include("../fungsi/autentikasi.php");
include("../config/koneksi.php");
cekLogin();

$id = $_GET['id'];

$penjualan = mysqli_fetch_assoc(mysqli_query($koneksi, 
    "SELECT p.*, COALESCE(pl.nama_pelanggan, 'Pelanggan') AS nama_pelanggan, u.username
     FROM tbl_penjualan p
     LEFT JOIN tbl_pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
     JOIN tbl_user u ON p.id_user = u.id_user
     WHERE p.id_penjualan='$id'"));

$detail = mysqli_query($koneksi, 
    "SELECT d.*, pr.nama_produk 
     FROM tbl_detailpenjualan d 
     JOIN tbl_produk pr ON d.id_produk = pr.id_produk
     WHERE d.id_penjualan='$id'");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Detail Penjualan</title>
  <link rel="stylesheet" href="../assets/css/style.css">
       <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="dashboard">
  <?php include("sidebar.php"); ?>

  <main class="content">
    <h2>Detail Penjualan</h2>
    <p><b>Tanggal:</b> <?= $penjualan['tgl_penjualan'] ?></p>
    <p><b>Pelanggan:</b> <?= $penjualan['nama_pelanggan'] ?></p>
    <p><b>Kasir:</b> <?= $penjualan['username'] ?></p>

    <table>
      <thead>
        <tr>
          <th>Produk</th>
          <th>Jumlah</th>
          <th>Harga</th>
          <th>Subtotal</th>
        </tr>
      </thead>
      <tbody>
        <?php 
        $total = 0;
        while($row = mysqli_fetch_assoc($detail)): 
          $hargaSatuan = $row['subtotal'] / $row['jumlah_produk'];
          $total += $row['subtotal'];
        ?>
        <tr>
          <td><?= $row['nama_produk'] ?></td>
          <td><?= $row['jumlah_produk'] ?></td>
          <td>Rp <?= number_format($hargaSatuan,0,',','.') ?></td>
          <td>Rp <?= number_format($row['subtotal'],0,',','.') ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
      <tfoot>
        <tr>
          <th colspan="3">Total</th>
          <th>Rp <?= number_format($total,0,',','.') ?></th>
        </tr>
      </tfoot>
    </table>
  </main>
</div>
</body>
</html>
