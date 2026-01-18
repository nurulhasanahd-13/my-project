<?php
include("../fungsi/autentikasi.php");
include("../config/koneksi.php");
cekLogin();

// Mode Edit
$editMode = false;
$editData = null;

if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    if ($id == 0) {
        header("Location: pelanggan.php");
        exit;
    }
    $result = mysqli_query($koneksi, "SELECT * FROM tbl_pelanggan WHERE id_pelanggan='$id'");
    if ($result) {
        $editData = mysqli_fetch_assoc($result);
        $editMode = true;
    }
}

// Ambil semua pelanggan
$data = mysqli_query($koneksi, "SELECT * FROM tbl_pelanggan ORDER BY id_pelanggan DESC");
if (!$data) {
    die("Query error: " . mysqli_error($koneksi));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Pelanggan</title>
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
    .error {
        color: red;
        font-size: 14px;
    }
  </style>
</head>
<body>
<div class="dashboard">
  <?php include("sidebar.php"); ?>
  <main class="content">
    <h2>Manajemen Pelanggan</h2>

    <!-- Form Tambah / Edit -->
    <form id="formPelanggan" action="../proses/proses_pelanggan.php" method="POST" enctype="multipart/form-data" class="form-box">
      <?php if($editMode): ?>
        <input type="hidden" name="id_pelanggan" value="<?= $editData['id_pelanggan'] ?>">
      <?php endif; ?>

      <input type="text" name="nama_pelanggan" placeholder="Nama Pelanggan" 
             value="<?= $editMode ? $editData['nama_pelanggan'] : '' ?>" required>

      <textarea name="alamat" placeholder="Alamat"><?= $editMode ? $editData['alamat'] : '' ?></textarea>

      <input type="text" id="no_hp" name="no_hp" placeholder="No HP" 
             value="<?= $editMode ? $editData['no_hp'] : '' ?>" required>
      <span id="errorNoHP" class="error"></span>

      <label>Foto (opsional)</label>
      <input type="file" name="foto" accept=".jpg,.jpeg,.png">

      <?php if ($editMode && !empty($editData['foto'])): ?>
        <p>Foto Sekarang:</p>
        <img src="../uploads/<?= $editData['foto'] ?>" class="foto-preview" alt="Foto pelanggan">
      <?php endif; ?>

      <button type="submit" name="<?= $editMode ? 'update' : 'tambah' ?>" class="btn">
        <?= $editMode ? 'Update' : 'Tambah' ?>
      </button>
    </form>

    <hr>

    <!-- Tabel Pelanggan -->
    <table>
      <thead>
        <tr>
          <th>No</th>
          <th>Nama</th>
          <th>Alamat</th>
          <th>No HP</th>
          <th>Foto</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php $no=1; while($row=mysqli_fetch_assoc($data)): ?>
        <tr>
          <td><?= $no++ ?></td>
          <td><?= htmlspecialchars($row['nama_pelanggan']) ?></td>
          <td><?= htmlspecialchars($row['alamat']) ?></td>
          <td><?= htmlspecialchars($row['no_hp']) ?></td>
          <td>
            <?php if(!empty($row['foto'])): ?>
              <img src="../uploads/<?= $row['foto'] ?>" class="foto-preview" alt="Foto">
            <?php else: ?>
              <span style="color: gray;">(Belum ada foto)</span>
            <?php endif; ?>
          </td>
          <td>
            <?php if($row['id_pelanggan'] != 0): ?>
              <a href="?edit=<?= $row['id_pelanggan'] ?>" class="btn-edit">Edit</a>
              <a href="../proses/proses_pelanggan.php?hapus=<?= $row['id_pelanggan'] ?>" 
                 class="btn-delete" onclick="return confirm('Yakin ingin menghapus pelanggan ini?')">Hapus</a>
            <?php else: ?>
              <span style="color:gray;">(Default)</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </main>
</div>

<!-- === VALIDASI REAL-TIME NOMOR HP === -->
<script>
const noHPInput = document.getElementById('no_hp');
const errorText = document.getElementById('errorNoHP');

// Saat user mengetik
noHPInput.addEventListener('input', function(e) {
    const value = e.target.value;

    if (/[^0-9]/.test(value)) {
        errorText.textContent = "Nomor HP hanya boleh angka!";
        e.target.value = value.replace(/[^0-9]/g, ''); // hapus huruf otomatis
    } else {
        errorText.textContent = "";
    }
});

// Validasi saat submit (jaga-jaga)
document.getElementById('formPelanggan').addEventListener('submit', function(e) {
    if (noHPInput.value.trim() === "") {
        e.preventDefault();
        errorText.textContent = "Nomor HP wajib diisi!";
        noHPInput.focus();
    }
});
</script>

</body>
</html>
