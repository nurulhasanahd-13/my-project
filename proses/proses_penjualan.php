<?php
include("../fungsi/autentikasi.php");
include("../config/koneksi.php");
cekLogin();

if (isset($_POST['tambah'])) {
    $tgl_penjualan = date("Y-m-d");
    $id_user = $_SESSION['id_user'];

    $id_pelanggan = !empty($_POST['id_pelanggan']) ? $_POST['id_pelanggan'] : "NULL";
    $produkDipilih = isset($_POST['produk']) ? $_POST['produk'] : [];
    $jumlah = isset($_POST['jumlah']) ? $_POST['jumlah'] : [];

    // Hitung total harga
    $total_harga = 0;
    foreach ($produkDipilih as $id_produk) {
        $jml = (int)$jumlah[$id_produk];
        if ($jml > 0) {
            $produkData = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT harga FROM tbl_produk WHERE id_produk='$id_produk'"));
            $subtotal = $produkData['harga'] * $jml;
            $total_harga += $subtotal;
        }
    }

    // Simpan ke tabel penjualan
    $query = "INSERT INTO tbl_penjualan (tgl_penjualan, total_harga, id_pelanggan, id_user) 
              VALUES ('$tgl_penjualan', '$total_harga', " . ($id_pelanggan === "NULL" ? "NULL" : "'$id_pelanggan'") . ", '$id_user')";
    mysqli_query($koneksi, $query) or die("Error insert penjualan: " . mysqli_error($koneksi));

    $id_penjualan = mysqli_insert_id($koneksi);

    // Simpan detail penjualan + kurangi stok
    foreach ($produkDipilih as $id_produk) {
        $jml = (int)$jumlah[$id_produk];
        if ($jml > 0) {
            $produkData = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT harga, stok FROM tbl_produk WHERE id_produk='$id_produk'"));
            $harga = $produkData['harga'];
            $stok_sekarang = $produkData['stok'];
            $subtotal = $harga * $jml;

            // Simpan detail transaksi
            mysqli_query($koneksi, "INSERT INTO tbl_detailpenjualan (id_penjualan, id_produk, jumlah_produk, subtotal) 
                                    VALUES ('$id_penjualan', '$id_produk', '$jml', '$subtotal')")
            or die("Error insert detail: " . mysqli_error($koneksi));

            // Kurangi stok
            $stok_baru = $stok_sekarang - $jml;
            if ($stok_baru < 0) {
                $stok_baru = 0; // Hindari stok negatif
            }
            mysqli_query($koneksi, "UPDATE tbl_produk SET stok='$stok_baru' WHERE id_produk='$id_produk'")
            or die("Error update stok: " . mysqli_error($koneksi));
        }
    }

    header("Location: ../halaman/penjualan.php");
    exit;
}

// Hapus penjualan dan kembalikan stok
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];

    // Kembalikan stok sebelum data dihapus
    $detail = mysqli_query($koneksi, "SELECT id_produk, jumlah_produk FROM tbl_detailpenjualan WHERE id_penjualan='$id'");
    while ($d = mysqli_fetch_assoc($detail)) {
        $id_produk = $d['id_produk'];
        $jumlah_produk = $d['jumlah_produk'];
        mysqli_query($koneksi, "UPDATE tbl_produk SET stok = stok + $jumlah_produk WHERE id_produk='$id_produk'");
    }

    // Hapus data transaksi
    mysqli_query($koneksi, "DELETE FROM tbl_detailpenjualan WHERE id_penjualan='$id'");
    mysqli_query($koneksi, "DELETE FROM tbl_penjualan WHERE id_penjualan='$id'");

    header("Location: ../halaman/penjualan.php");
    exit;
}
?>
