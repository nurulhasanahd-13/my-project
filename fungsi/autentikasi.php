<?php
/**
 * File: autentikasi.php
 * Deskripsi: Berisi fungsi autentikasi dan pemeriksaan sesi login.
 * 
 * File ini memastikan bahwa hanya pengguna yang sudah login
 * yang dapat mengakses halaman tertentu dalam aplikasi kasir.
 * 
 * @package AplikasiKasirDian
 * @subpackage Fungsi
 * @author Dian
 * @version 1.1
 * @since 2025-10-13
 */

/**
 * Fungsi cekLogin()
 * Memeriksa apakah pengguna sudah login.
 * Jika belum, diarahkan ke halaman index.php (login).
 */
if (!function_exists('cekLogin')) {
    function cekLogin() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Jika session belum ada, arahkan ke halaman login
        if (empty($_SESSION['id_user'])) {
            header("Location: ../index.php");
            exit();
        }
    }
}

/**
 * Fungsi logout()
 * Menghapus semua session dan mengarahkan ke halaman login.
 */
if (!function_exists('logout')) {
    function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Hapus semua session
        session_destroy();

        // Arahkan ke halaman login
        header("Location: ../index.php");
        exit();
    }
}
?>
