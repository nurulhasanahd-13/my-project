<?php
include("../fungsi/autentikasi.php");
include("../config/koneksi.php");
cekLogin();

// ==========================
// TAMBAH PRODUK
// ==========================
if (isset($_POST['tambah'])) {
    $nama  = mysqli_real_escape_string($koneksi, $_POST['nama_produk']);
    $harga = mysqli_real_escape_string($koneksi, $_POST['harga']);
    $stok  = mysqli_real_escape_string($koneksi, $_POST['stok']);
    $fotoName = null;

    // Upload foto produk
    if (!empty($_FILES['foto']['name'])) {
        $targetDir = "../uploads/produk/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);

        $fileName = uniqid() . "_" . basename($_FILES["foto"]["name"]);
        $targetFile = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png'];

        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES["foto"]["tmp_name"], $targetFile)) {
                $fotoName = $fileName;
            } else {
                echo "<script>alert('Gagal mengupload foto produk!'); window.history.back();</script>";
                exit;
            }
        } else {
            echo "<script>alert('Format foto tidak valid! Hanya JPG, JPEG, PNG yang diperbolehkan.'); window.history.back();</script>";
            exit;
        }
    }

    $query = "INSERT INTO tbl_produk (nama_produk, harga, stok, foto) 
              VALUES ('$nama', '$harga', '$stok', '$fotoName')";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Produk berhasil ditambahkan!'); window.location.href='../halaman/produk.php';</script>";
    } else {
        echo "<script>alert('Gagal menambah produk: " . mysqli_error($koneksi) . "'); window.history.back();</script>";
    }
    exit;
}

// ==========================
// UPDATE PRODUK
// ==========================
if (isset($_POST['update'])) {
    $id    = $_POST['id_produk'];
    $nama  = mysqli_real_escape_string($koneksi, $_POST['nama_produk']);
    $harga = mysqli_real_escape_string($koneksi, $_POST['harga']);
    $stok  = mysqli_real_escape_string($koneksi, $_POST['stok']);
    $fotoName = null;

    // Upload foto baru jika ada
    if (!empty($_FILES['foto']['name'])) {
        $targetDir = "../uploads/produk/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);

        $fileName = uniqid() . "_" . basename($_FILES["foto"]["name"]);
        $targetFile = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png'];

        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES["foto"]["tmp_name"], $targetFile)) {
                $fotoName = $fileName;

                // Hapus foto lama jika ada
                $old = mysqli_query($koneksi, "SELECT foto FROM tbl_produk WHERE id_produk='$id'");
                $oldData = mysqli_fetch_assoc($old);
                if (!empty($oldData['foto']) && file_exists("../uploads/produk/" . $oldData['foto'])) {
                    unlink("../uploads/produk/" . $oldData['foto']);
                }
            } else {
                echo "<script>alert('Gagal mengupload foto baru!'); window.history.back();</script>";
                exit;
            }
        } else {
            echo "<script>alert('Format foto tidak valid! Hanya JPG, JPEG, PNG yang diperbolehkan.'); window.history.back();</script>";
            exit;
        }
    }

    // Update data produk
    if ($fotoName) {
        $query = "UPDATE tbl_produk 
                  SET nama_produk='$nama', harga='$harga', stok='$stok', foto='$fotoName'
                  WHERE id_produk='$id'";
    } else {
        $query = "UPDATE tbl_produk 
                  SET nama_produk='$nama', harga='$harga', stok='$stok'
                  WHERE id_produk='$id'";
    }

    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Produk berhasil diperbarui!'); window.location.href='../halaman/produk.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui produk: " . mysqli_error($koneksi) . "'); window.history.back();</script>";
    }
    exit;
}



// ==========================
// HAPUS PRODUK
// ==========================
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];

    // Cek apakah produk masih digunakan di detail penjualan
    $cek = mysqli_query($koneksi, "SELECT COUNT(*) AS jml FROM tbl_detailpenjualan WHERE id_produk='$id'");
    $data = mysqli_fetch_assoc($cek);

    if ($data['jml'] > 0) {
        echo "<script>alert('Produk ini tidak dapat dihapus karena sudah digunakan dalam transaksi!'); window.location='../halaman/produk.php';</script>";
        exit;
    }

    // Hapus foto produk dari folder
    $q = mysqli_query($koneksi, "SELECT foto FROM tbl_produk WHERE id_produk='$id'");
    $d = mysqli_fetch_assoc($q);

    if (!empty($d['foto']) && file_exists("../uploads/produk/" . $d['foto'])) {
        unlink("../uploads/produk/" . $d['foto']);
    }

    // Hapus produk dari database
    mysqli_query($koneksi, "DELETE FROM tbl_produk WHERE id_produk='$id'");

    echo "<script>alert('Produk berhasil dihapus!'); window.location.href='../halaman/produk.php';</script>";
    exit;
}
?>
