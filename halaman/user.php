<?php
include("../fungsi/autentikasi.php");
include("../config/koneksi.php");
cekLogin();

// Pastikan hanya admin yang bisa akses halaman ini
if ($_SESSION['level'] != 'admin') {
    die("Akses ditolak! Halaman ini hanya untuk admin.");
}

$editMode = false;
$editData = [
    'id_user' => '',
    'username' => '',
    'level' => ''
];

if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $q = mysqli_query($koneksi, "SELECT * FROM tbl_user WHERE id_user=$id");
    if ($q && mysqli_num_rows($q) === 1) {
        $editData = mysqli_fetch_assoc($q);
        $editMode = true;
    }
}

$data = mysqli_query($koneksi, "SELECT * FROM tbl_user ORDER BY id_user DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Manajemen User</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="dashboard">
  <?php include("sidebar.php"); ?>
  <main class="content">
    <h2>Manajemen User</h2>

    <form action="../proses/proses_user.php" method="POST" class="form-box">
      <?php if($editMode): ?>
        <input type="hidden" name="id_user" value="<?= $editData['id_user'] ?>">
      <?php endif; ?>

      <input type="text" name="username" placeholder="Username"
             value="<?= htmlspecialchars($editData['username']) ?>" required>

      <input type="password" name="password"
             placeholder="<?= $editMode ? 'Password (kosongkan jika tidak diganti)' : 'Password' ?>">

      <!-- Batasi agar hanya level petugas yang bisa ditambahkan -->
      <select name="level" required <?= !$editMode ? 'disabled' : '' ?>>
        <?php if($editMode): ?>
          <option value="admin" <?= $editData['level']=='admin' ? 'selected' : '' ?>>Admin</option>
          <option value="petugas" <?= $editData['level']=='petugas' ? 'selected' : '' ?>>Petugas</option>
        <?php else: ?>
          <option value="petugas" selected>Petugas</option>
        <?php endif; ?>
      </select>

      <?php if(!$editMode): ?>
        <!-- Saat tambah user baru, level otomatis petugas -->
        <input type="hidden" name="level" value="petugas">
      <?php endif; ?>

      <button type="submit" name="aksi" value="<?= $editMode ? 'update' : 'tambah' ?>" class="btn">
        <?= $editMode ? 'Update' : 'Tambah' ?>
      </button>
      <?php if($editMode): ?>
        <a href="user.php" class="btn-secondary">Batal</a>
      <?php endif; ?>
    </form>

    <table>
      <thead>
        <tr>
          <th>No</th>
          <th>Username</th>
          <th>Level</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php $no=1; while($row=mysqli_fetch_assoc($data)): ?>
        <tr>
          <td><?= $no++ ?></td>
          <td><?= htmlspecialchars($row['username']) ?></td>
          <td><?= $row['level'] ?></td>
          <td>
            <a href="user.php?edit=<?= $row['id_user'] ?>" class="btn-edit">Edit</a>
            <a href="../proses/proses_user.php?hapus=<?= $row['id_user'] ?>"
               class="btn-delete" onclick="return confirm('Yakin hapus user ini?')">Hapus</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </main>
</div>
</body>
</html>
