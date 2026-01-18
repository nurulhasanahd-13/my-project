<?php
include("../config/koneksi.php");

// ==========================
// PROSES TAMBAH PELANGGAN
// ==========================
if (isset($_POST['tambah'])) {
    $nama   = mysqli_real_escape_string($koneksi, $_POST['nama_pelanggan']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $no_hp  = mysqli_real_escape_string($koneksi, $_POST['no_hp']);
    $fotoName = null;

    // 🔒 Validasi nomor HP harus angka
    if (!preg_match('/^[0-9]+$/', $no_hp)) {
        echo "<script>alert('Nomor HP harus berupa angka!'); window.history.back();</script>";
        exit;
    }

    // 📸 Upload foto jika ada
    if (!empty($_FILES['foto']['name'])) {
        $targetDir = "../uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = uniqid() . "_" . basename($_FILES["foto"]["name"]);
        $targetFile = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png'];

        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES["foto"]["tmp_name"], $targetFile)) {
                $fotoName = $fileName;
            } else {
                echo "<script>alert('Gagal mengupload foto!'); window.history.back();</script>";
                exit;
            }
        } else {
            echo "<script>alert('Format foto tidak valid! Hanya JPG, JPEG, PNG yang diperbolehkan.'); window.history.back();</script>";
            exit;
        }
    }

    // 💾 Simpan ke database
    $query = "INSERT INTO tbl_pelanggan (nama_pelanggan, alamat, no_hp, foto)
              VALUES ('$nama', '$alamat', '$no_hp', '$fotoName')";

    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Pelanggan berhasil ditambahkan!'); 
              window.location.href='../halaman/pelanggan.php';</script>";
    } else {
        echo "<script>alert('Gagal menambah pelanggan: " . mysqli_error($koneksi) . "'); 
              window.history.back();</script>";
    }
}



// ==========================
// PROSES UPDATE PELANGGAN
// ==========================
if (isset($_POST['update'])) {
    $id     = mysqli_real_escape_string($koneksi, $_POST['id_pelanggan']);
    $nama   = mysqli_real_escape_string($koneksi, $_POST['nama_pelanggan']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $no_hp  = mysqli_real_escape_string($koneksi, $_POST['no_hp']);
    $fotoName = null;

    //  Validasi nomor HP harus angka
    if (!preg_match('/^[0-9]+$/', $no_hp)) {
        echo "<script>alert('Nomor HP harus berupa angka!'); window.history.back();</script>";
        exit;
    }

    //  Cek dan upload foto baru jika ada
    if (!empty($_FILES['foto']['name'])) {
        $targetDir = "../uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = uniqid() . "_" . basename($_FILES["foto"]["name"]);
        $targetFile = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png'];

        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES["foto"]["tmp_name"], $targetFile)) {
                $fotoName = $fileName;

                // 🔁 Hapus foto lama jika ada
                $old = mysqli_query($koneksi, "SELECT foto FROM tbl_pelanggan WHERE id_pelanggan='$id'");
                $oldData = mysqli_fetch_assoc($old);
                if (!empty($oldData['foto']) && file_exists("../uploads/" . $oldData['foto'])) {
                    unlink("../uploads/" . $oldData['foto']);
                }
            } else {
                echo "<script>alert('Gagal mengupload foto baru!'); window.history.back();</script>";
                exit;
            }
        } else {
            echo "<script>alert('Format foto tidak valid! Hanya JPG, JPEG, PNG.'); window.history.back();</script>";
            exit;
        }
    }

    // 🔄 Update database
    if ($fotoName) {
        $query = "UPDATE tbl_pelanggan 
                  SET nama_pelanggan='$nama', alamat='$alamat', no_hp='$no_hp', foto='$fotoName'
                  WHERE id_pelanggan='$id'";
    } else {
        $query = "UPDATE tbl_pelanggan 
                  SET nama_pelanggan='$nama', alamat='$alamat', no_hp='$no_hp'
                  WHERE id_pelanggan='$id'";
    }

    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Data pelanggan berhasil diperbarui!'); 
              window.location.href='../halaman/pelanggan.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui pelanggan: " . mysqli_error($koneksi) . "'); 
              window.history.back();</script>";
    }
}



// ==========================
// PROSES HAPUS PELANGGAN
// ==========================
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $q = mysqli_query($koneksi, "SELECT foto FROM tbl_pelanggan WHERE id_pelanggan='$id'");
    $d = mysqli_fetch_assoc($q);

    if (!empty($d['foto']) && file_exists("../uploads/" . $d['foto'])) {
        unlink("../uploads/" . $d['foto']);
    }

    mysqli_query($koneksi, "DELETE FROM tbl_pelanggan WHERE id_pelanggan='$id'");
    echo "<script>alert('Pelanggan berhasil dihapus!'); 
          window.location.href='../halaman/pelanggan.php';</script>";
    exit;
}
?>
