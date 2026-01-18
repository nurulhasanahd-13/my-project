<?php
include("../fungsi/autentikasi.php");
include("../config/koneksi.php");
cekLogin();

// Query data penjualan
if ($_SESSION['level'] == 'admin') {
    $data = mysqli_query($koneksi, "
        SELECT p.*, COALESCE(pl.nama_pelanggan, 'Pelanggan') AS nama_pelanggan, u.username 
        FROM tbl_penjualan p
        LEFT JOIN tbl_pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
        JOIN tbl_user u ON p.id_user = u.id_user
        ORDER BY p.id_penjualan DESC
    ");
} else {
    $id_user = $_SESSION['id_user'];
    $data = mysqli_query($koneksi, "
        SELECT p.*, COALESCE(pl.nama_pelanggan, 'Pelanggan') AS nama_pelanggan, u.username 
        FROM tbl_penjualan p
        LEFT JOIN tbl_pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
        JOIN tbl_user u ON p.id_user = u.id_user
        WHERE p.id_user = '$id_user'
        ORDER BY p.id_penjualan DESC
    ");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Penjualan</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    .btn-cetak {
      display: inline-block;
      margin: 10px 0;
      padding: 8px 16px;
      background: #28a745;
      color: white;
      text-decoration: none;
      border-radius: 4px;
      cursor: pointer;
    }
    .btn-cetak:hover { background: #218838; }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }
    table, th, td { border: 1px solid #ddd; }
    th, td {
      padding: 8px;
      text-align: left;
    }
    th { background: #f4f4f4; }
    @media print {
      .btn-cetak, .sidebar { display: none; }
      body { margin: 0; }
      main.content { margin: 0; width: 100%; }
    }
  </style>
</head>
<body>
<div class="dashboard">
  <?php include("sidebar.php"); ?>
  <main class="content">
    <h2>Laporan Penjualan</h2>

    <!-- ✅ Cetak tanpa buka tab baru -->
    <button class="btn-cetak" onclick="window.print()">🖨 Cetak Laporan</button>

    <table>
      <thead>
        <tr>
          <th>No</th>
          <th>Tanggal</th>
          <th>Pelanggan</th>
          <th>Total</th>
          <th>Kasir</th>
        </tr>
      </thead>
      <tbody>
        <?php 
        $no=1; 
        $grand_total = 0;
        while($row = mysqli_fetch_assoc($data)): 
          $grand_total += $row['total_harga'];
        ?>
        <tr>
          <td><?= $no++ ?></td>
          <td><?= $row['tgl_penjualan'] ?></td>
          <td><?= htmlspecialchars($row['nama_pelanggan']) ?></td>
          <td>Rp <?= number_format($row['total_harga'],0,',','.') ?></td>
          <td><?= htmlspecialchars($row['username']) ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="3"><strong>Total Keseluruhan</strong></td>
          <td colspan="2"><strong>Rp <?= number_format($grand_total,0,',','.') ?></strong></td>
        </tr>
      </tfoot>
    </table>
  </main>
</div>
</body>
</html>
