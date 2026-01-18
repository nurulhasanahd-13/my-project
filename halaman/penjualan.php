<?php
include("../fungsi/autentikasi.php");
include("../config/koneksi.php");
cekLogin();

$id_user = $_SESSION['id_user'];
$level   = $_SESSION['level'] ?? '';

/* ==========================================
   MODE STRUK (Langsung tampil di halaman ini)
========================================== */
if (isset($_GET['struk'])) {
    $id = $_GET['struk'];

    // Cek kepemilikan data
    $query = "
      SELECT p.*, COALESCE(pl.nama_pelanggan, 'Pelanggan') AS nama_pelanggan, u.username
      FROM tbl_penjualan p
      LEFT JOIN tbl_pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
      JOIN tbl_user u ON p.id_user = u.id_user
      WHERE p.id_penjualan='$id'
    ";
    if ($level === 'petugas') {
        $query .= " AND p.id_user = '$id_user'";
    }

    $penjualan = mysqli_fetch_assoc(mysqli_query($koneksi, $query));
    if (!$penjualan) {
        echo "<script>alert('Anda tidak memiliki akses ke struk ini!');window.location='penjualan.php';</script>";
        exit;
    }

    $detail = mysqli_query($koneksi, "
        SELECT d.*, pr.nama_produk 
        FROM tbl_detailpenjualan d 
        JOIN tbl_produk pr ON d.id_produk = pr.id_produk
        WHERE d.id_penjualan='$id'
    ");
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
      <meta charset="UTF-8">
      <title>Struk Penjualan</title>
      <style>
        body { font-family: monospace; width: 300px; margin: auto; background: #fff; color: #000; }
        h2, h3 { text-align: center; margin: 0; }
        .center { text-align: center; }
        table { width: 100%; border-collapse: collapse; }
        td, th { font-size: 13px; padding: 3px; }
        .total { border-top: 1px dashed #000; border-bottom: 1px dashed #000; font-weight: bold; }
        hr { border: 0; border-top: 1px dashed #000; margin: 5px 0; }
        .btn-print, .btn-back {
          display: block; text-align: center; margin: 10px auto;
          padding: 6px 10px; border-radius: 5px; width: 100px; text-decoration: none;
        }
        .btn-print { background: #007bff; color: #fff; }
        .btn-back { background: #6c757d; color: #fff; }
        .btn-print:hover { background: #0056b3; }
        .btn-back:hover { background: #5a6268; }
        @media print { .btn-print, .btn-back { display: none; } }
      </style>
    </head>
    <body>
      <h2>🛒 APLIKASI KASIR</h2>
      <h3>Struk Penjualan</h3>
      <hr>

      <p>
        <strong>No. Transaksi:</strong> <?= $penjualan['id_penjualan'] ?><br>
        <strong>Tanggal:</strong> <?= $penjualan['tgl_penjualan'] ?><br>
        <strong>Pelanggan:</strong> <?= $penjualan['nama_pelanggan'] ?><br>
        <strong>Kasir:</strong> <?= $penjualan['username'] ?><br>
      </p>

      <hr>
      <table>
        <thead>
          <tr>
            <th>Produk</th>
            <th>Jml</th>
            <th>Harga</th>
            <th>Subtotal</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $total = 0;
          while($row = mysqli_fetch_assoc($detail)): 
            $harga = $row['subtotal'] / $row['jumlah_produk'];
            $total += $row['subtotal'];
          ?>
          <tr>
            <td><?= $row['nama_produk'] ?></td>
            <td><?= $row['jumlah_produk'] ?></td>
            <td><?= number_format($harga,0,',','.') ?></td>
            <td><?= number_format($row['subtotal'],0,',','.') ?></td>
          </tr>
          <?php endwhile; ?>
          <tr class="total">
            <td colspan="3">Total</td>
            <td>Rp <?= number_format($total,0,',','.') ?></td>
          </tr>
        </tbody>
      </table>

      <hr>
      <p class="center">
        Terima kasih sudah berbelanja <br>
        Barang yang sudah dibeli tidak dapat dikembalikan.
      </p>

      <a href="#" class="btn-print" onclick="window.print()">🖨 Cetak</a>
      <a href="penjualan.php" class="btn-back">⬅ Kembali</a>
    </body>
    </html>
    <?php
    exit;
}

/* ==========================================
   MODE DEFAULT (Daftar & Tambah Penjualan)
========================================== */

// Ambil data penjualan
if ($level === 'admin') {
    $queryPenjualan = "
        SELECT p.id_penjualan, p.tgl_penjualan, p.total_harga, 
               COALESCE(pl.nama_pelanggan, 'Pelanggan') AS nama_pelanggan,
               u.username 
        FROM tbl_penjualan p 
        LEFT JOIN tbl_pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
        JOIN tbl_user u ON p.id_user = u.id_user
        ORDER BY p.id_penjualan DESC
    ";
} else {
    $queryPenjualan = "
        SELECT p.id_penjualan, p.tgl_penjualan, p.total_harga, 
               COALESCE(pl.nama_pelanggan, 'Pelanggan') AS nama_pelanggan,
               u.username 
        FROM tbl_penjualan p 
        LEFT JOIN tbl_pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
        JOIN tbl_user u ON p.id_user = u.id_user
        WHERE p.id_user = '$id_user'
        ORDER BY p.id_penjualan DESC
    ";
}
$penjualan = mysqli_query($koneksi, $queryPenjualan);

// Ambil pelanggan & produk
$pelanggan = mysqli_query($koneksi, "SELECT * FROM tbl_pelanggan ORDER BY id_pelanggan ASC");
$produk    = mysqli_query($koneksi, "SELECT * FROM tbl_produk ORDER BY nama_produk ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Penjualan - Aplikasi Kasir</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    .btn-print { background-color: #28a745; color: #fff; padding: 4px 8px; border-radius: 4px; text-decoration: none; font-size: 13px; }
    .btn-print:hover { background-color: #218838; }
  </style>
</head>
<body>
<div class="dashboard">
  <?php include("sidebar.php"); ?>

  <main class="content">
    <h2>Data Penjualan</h2>
    <a href="?add" class="btn">+ Tambah Penjualan</a>

    <?php if(isset($_GET['add'])): ?>
      <!-- FORM TAMBAH PENJUALAN -->
      <form action="../proses/proses_penjualan.php" method="POST" class="form-box" id="formPenjualan">
        <label>Pelanggan</label>
        <select name="id_pelanggan">
          <option value="">Pelanggan</option>
          <?php while($pl = mysqli_fetch_assoc($pelanggan)): ?>
            <option value="<?= $pl['id_pelanggan'] ?>"><?= $pl['nama_pelanggan'] ?></option>
          <?php endwhile; ?>
        </select>

        <label>Produk</label>
        <?php
        // 🔹 Tambahan penting agar semua produk muncul
        mysqli_data_seek($produk, 0);
        while($pr = mysqli_fetch_assoc($produk)):
        ?>
          <div class="produk-box">
            <input type="checkbox" name="produk[]" value="<?= $pr['id_produk'] ?>" class="produk-check"> 
            <?= $pr['nama_produk'] ?> - Rp <?= number_format($pr['harga'],0,',','.') ?> (Stok: <?= $pr['stok'] ?>)
            <input type="number" name="jumlah[<?= $pr['id_produk'] ?>]" min="1" placeholder="Jumlah" class="jumlah-input" disabled>
          </div>
        <?php endwhile; ?>

        <button type="submit" name="tambah" class="btn">Simpan Transaksi</button>
      </form>

      <script>
      document.querySelectorAll(".produk-box").forEach(function(box) {
          let checkbox = box.querySelector(".produk-check");
          let jumlah = box.querySelector(".jumlah-input");
          checkbox.addEventListener("change", function() {
              jumlah.disabled = !this.checked;
              if (this.checked && (!jumlah.value || jumlah.value <= 0)) jumlah.value = 1;
          });
      });
      document.getElementById("formPenjualan").addEventListener("submit", function(e) {
          let valid = true;
          document.querySelectorAll(".produk-box").forEach(function(box) {
              let checkbox = box.querySelector(".produk-check");
              let jumlah = box.querySelector(".jumlah-input");
              if (checkbox.checked && (!jumlah.value || jumlah.value <= 0)) valid = false;
          });
          if (!valid) {
              e.preventDefault();
              alert("Jumlah wajib diisi untuk setiap produk yang dipilih!");
          }
      });
      </script>

    <?php else: ?>
      <!-- TABEL PENJUALAN -->
      <table>
        <thead>
          <tr>
            <th>Tanggal</th>
            <th>Pelanggan</th>
            <th>Total Harga</th>
            <th>Kasir</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (mysqli_num_rows($penjualan) === 0): ?>
            <tr><td colspan="5" style="text-align:center;">Belum ada transaksi</td></tr>
          <?php else: ?>
            <?php while($row = mysqli_fetch_assoc($penjualan)): ?>
            <tr>
              <td><?= $row['tgl_penjualan'] ?></td>
              <td><?= $row['nama_pelanggan'] ?></td>
              <td>Rp <?= number_format($row['total_harga'],0,',','.') ?></td>
              <td><?= $row['username'] ?></td>
              <td>
                <a href="?struk=<?= $row['id_penjualan'] ?>" class="btn-print"><i class="fa fa-print"></i> Cetak</a>
                <a href="../proses/proses_penjualan.php?hapus=<?= $row['id_penjualan'] ?>" class="btn-delete" onclick="return confirm('Yakin hapus transaksi ini?')">Hapus</a>
              </td>
            </tr>
            <?php endwhile; ?>
          <?php endif; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </main>
</div>
</body>
</html>
