<?php
include("../fungsi/autentikasi.php");
include("../config/koneksi.php");
cekLogin();

// Ambil level dari session
$level = $_SESSION['level'] ?? '';

// Mode Edit hanya untuk admin
$editMode = false;
$editData = null;

if ($level === 'admin' && isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = mysqli_query($koneksi, "SELECT * FROM tbl_produk WHERE id_produk='$id'");
    if ($result) {
        $editData = mysqli_fetch_assoc($result);
        $editMode = true;
    }
}

// Ambil semua produk
$data = mysqli_query($koneksi, "SELECT * FROM tbl_produk ORDER BY id_produk DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Produk</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    .foto-preview {
        width: 60px;
        height: 60px;
        border-radius: 8px;
        object-fit: cover;
        border: 1px solid #ccc;
    }
    .form-box {
        display: flex;
        flex-direction: column;
        gap: 10px;
        max-width: 400px;
    }
  </style>
</head>
<body>
<div class="dashboard">
  <?php include("sidebar.php"); ?>
  <main class="content">
    <h2>Manajemen Produk</h2>

    <!-- Form Tambah / Edit Produk (khusus admin) -->
    <?php if ($level === 'admin'): ?>
      <form action="../proses/proses_produk.php" method="POST" enctype="multipart/form-data" class="form-box">
        <?php if($editMode): ?>
          <input type="hidden" name="id_produk" value="<?= $editData['id_produk'] ?>">
        <?php endif; ?>

        <input type="text" name="nama_produk" placeholder="Nama Produk"
               value="<?= $editMode ? $editData['nama_produk'] : '' ?>" required>

        <input type="number" name="harga" placeholder="Harga"
               value="<?= $editMode ? $editData['harga'] : '' ?>" required>

        <input type="number" name="stok" placeholder="Stok"
               value="<?= $editMode ? $editData['stok'] : '' ?>" required>

        <label>Foto Produk (opsional)</label>
        <input type="file" name="foto" accept=".jpg,.jpeg,.png">

        <?php if ($editMode && !empty($editData['foto'])): ?>
          <p>Foto Sekarang:</p>
          <img src="../uploads/produk/<?= $editData['foto'] ?>" class="foto-preview" alt="Foto Produk">
        <?php endif; ?>

        <button type="submit" name="<?= $editMode ? 'update' : 'tambah' ?>" class="btn">
          <?= $editMode ? 'Update' : 'Tambah' ?>
        </button>
      </form>
      <hr>
    <?php endif; ?>

    <!-- Tabel Produk -->
    <table>
      <thead>
        <tr>
          <th>No</th>
          <th>Nama Produk</th>
          <th>Harga</th>
          <th>Stok</th>
          <th>Foto</th>
          <?php if ($level === 'admin'): ?>
            <th>Aksi</th>
          <?php endif; ?>
        </tr>
      </thead>
      <tbody>
        <?php $no=1; while($row=mysqli_fetch_assoc($data)): ?>
        <tr>
          <td><?= $no++ ?></td>
          <td><?= htmlspecialchars($row['nama_produk']) ?></td>
          <td>Rp <?= number_format($row['harga'],0,',','.') ?></td>
          <td><?= $row['stok'] ?></td>
          <td>
            <?php if(!empty($row['foto'])): ?>
              <img src="../uploads/produk/<?= $row['foto'] ?>" class="foto-preview" alt="Foto Produk">
            <?php else: ?>
              <span style="color:gray;">(Belum ada foto)</span>
            <?php endif; ?>
          </td>
          <?php if ($level === 'admin'): ?>
            <td>
              <a href="?edit=<?= $row['id_produk'] ?>" class="btn-edit">Edit</a>
              <a href="../proses/proses_produk.php?hapus=<?= $row['id_produk'] ?>" 
                 class="btn-delete" onclick="return confirm('Yakin hapus produk ini?')">Hapus</a>
            </td>
          <?php endif; ?>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </main>
</div>
</body>
</html>
