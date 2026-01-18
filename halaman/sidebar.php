<aside class="sidebar">
  <div class="logo">
    <i class="fa-solid fa-cash-register"></i>
  </div>
  <ul>
    <li class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
      <a href="dashboard.php"><i class="fa-solid fa-table-columns"></i> Dashboard</a>
    </li>

    <li class="<?= basename($_SERVER['PHP_SELF']) == 'pelanggan.php' ? 'active' : '' ?>">
      <a href="pelanggan.php"><i class="fa-solid fa-users"></i> Pelanggan</a>
    </li>

    <li class="<?= basename($_SERVER['PHP_SELF']) == 'penjualan.php' ? 'active' : '' ?>">
      <a href="penjualan.php"><i class="fa-solid fa-cart-shopping"></i> Penjualan</a>
    </li>

    <li class="<?= basename($_SERVER['PHP_SELF']) == 'produk.php' ? 'active' : '' ?>">
      <a href="produk.php"><i class="fa-solid fa-box"></i> Produk</a>
    </li>

    <?php if ($_SESSION['level'] == 'admin'): ?>
    <li class="<?= basename($_SERVER['PHP_SELF']) == 'user.php' ? 'active' : '' ?>">
      <a href="user.php"><i class="fa-solid fa-user"></i> User</a>
    </li>
    <?php endif; ?>

    <li class="<?= basename($_SERVER['PHP_SELF']) == 'laporan.php' ? 'active' : '' ?>">
      <a href="laporan.php"><i class="fa-solid fa-file-invoice"></i> Laporan</a>
    </li>

    <li>
      <a href="../logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </li>
  </ul>
</aside>
