<?php
/**
 * File: koneksi.php
 * Deskripsi: Mengatur koneksi antara aplikasi dan database MySQL.
 *
 * File ini digunakan di seluruh bagian aplikasi kasir
 * untuk memastikan koneksi database aktif sebelum query dijalankan.
 *
 * @package AplikasiKasirDian
 * @subpackage Konfigurasi
 * @author Dian
 * @version 1.1
 * @since 2025-10-12
 */

/** Host server database */
$host = "localhost";

/** Username database */
$user = "root";

/** Password database */
$pass = "";

/** Nama database */
$db   = "db_kasir";

/**
 * Membuat koneksi ke database menggunakan MySQLi.
 * 
 * @return mysqli Objek koneksi database
 */
$koneksi = mysqli_connect($host, $user, $pass, $db);

/**
 * Mengecek status koneksi database.
 * Jika koneksi gagal, hentikan eksekusi program dengan pesan aman.
 */
if (!$koneksi) {
    // Catat pesan error ke log server, bukan ke layar
    error_log("[" . date("Y-m-d H:i:s") . "] Koneksi database gagal: " . mysqli_connect_error() . "\n", 3, __DIR__ . "/error_log.txt");
    
    // Pesan aman untuk user (tanpa detail teknis)
    die("Terjadi kesalahan saat menghubungkan ke database. Silakan coba lagi nanti.");
}

// Pastikan koneksi menggunakan karakter UTF-8
mysqli_set_charset($koneksi, "utf8mb4");

/**
 * Fungsi untuk mengambil koneksi aktif.
 * Dapat digunakan di file lain agar lebih aman dan terstruktur.
 * 
 * @return mysqli Objek koneksi database yang sudah aktif
 */
function getKoneksi()
{
    global $koneksi;
    return $koneksi;
}
?>
