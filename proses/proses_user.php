<?php
include("../config/koneksi.php");

if (isset($_POST['aksi'])) {
    $aksi = $_POST['aksi'];
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];
    $level = $_POST['level'];

    if ($aksi === 'tambah') {
        if (empty($password)) {
            echo "<script>alert('Password wajib diisi untuk user baru!');window.location='../halaman/user.php';</script>";
            exit();
        }
        $hash = password_hash($password, PASSWORD_DEFAULT);
        mysqli_query($koneksi, "INSERT INTO tbl_user (username,password,level) 
                                VALUES('$username','$hash','$level')") 
            or die(mysqli_error($koneksi));
        header("Location: ../halaman/user.php");
        exit();
    }

    if ($aksi === 'update') {
        $id = (int)$_POST['id_user'];
        if (!empty($password)) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE tbl_user SET username='$username', password='$hash', level='$level' WHERE id_user=$id";
        } else {
            $sql = "UPDATE tbl_user SET username='$username', level='$level' WHERE id_user=$id";
        }
        mysqli_query($koneksi, $sql) or die(mysqli_error($koneksi));
        header("Location: ../halaman/user.php");
        exit();
    }
}

if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM tbl_user WHERE id_user=$id") or die(mysqli_error($koneksi));
    header("Location: ../halaman/user.php");
    exit();
}
?>
