<?php
include("../fungsi/autentikasi.php");
include("../config/koneksi.php");
cekLogin();

require_once "../vendor/autoload.php";

use Dompdf\Dompdf;

// Ambil data penjualan
if ($_SESSION['level'] == 'admin') {
    $query = "
        SELECT p.*, 
               COALESCE(pl.nama_pelanggan, 'Pelanggan') AS nama_pelanggan, 
               u.username
        FROM tbl_penjualan p
        LEFT JOIN tbl_pelanggan pl 
            ON p.id_pelanggan = pl.id_pelanggan
        JOIN tbl_user u 
            ON p.id_user = u.id_user
        ORDER BY p.id_penjualan DESC
    ";
} else {
    $id_user = $_SESSION['id_user'];
    $query = "
        SELECT p.*, 
               COALESCE(pl.nama_pelanggan, 'Pelanggan') AS nama_pelanggan, 
               u.username
        FROM tbl_penjualan p
        LEFT JOIN tbl_pelanggan pl 
            ON p.id_pelanggan = pl.id_pelanggan
        JOIN tbl_user u 
            ON p.id_user = u.id_user
        WHERE p.id_user = '$id_user'
        ORDER BY p.id_penjualan DESC
    ";
}

$data = mysqli_query($koneksi, $query);

// HTML untuk PDF
$html = '
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body {
        font-family: Arial, sans-serif;
        font-size: 12px;
    }
    h2 {
        text-align: center;
        margin-bottom: 10px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        border: 1px solid #000;
        padding: 6px;
        text-align: left;
    }
    th {
        background-color: #f2f2f2;
    }
    tfoot td {
        font-weight: bold;
    }
</style>
</head>
<body>

<h2>LAPORAN PENJUALAN</h2>

<table>
<thead>
<tr>
    <th>No</th>
    <th>Tanggal</th>
    <th>Pelanggan</th>
    <th>Total</th>
    <th>Kasir</th>
</tr>
</thead>
<tbody>
';

$no = 1;
$grand_total = 0;

while ($row = mysqli_fetch_assoc($data)) {
    $grand_total += $row['total_harga'];

    $html .= '
    <tr>
        <td>'.$no++.'</td>
        <td>'.$row['tgl_penjualan'].'</td>
        <td>'.htmlspecialchars($row['nama_pelanggan']).'</td>
        <td>Rp '.number_format($row['total_harga'],0,',','.').'</td>
        <td>'.htmlspecialchars($row['username']).'</td>
    </tr>
    ';
}

$html .= '
</tbody>
<tfoot>
<tr>
    <td colspan="3">Total Keseluruhan</td>
    <td colspan="2">Rp '.number_format($grand_total,0,',','.').'</td>
</tr>
</tfoot>
</table>

</body>
</html>
';

// Proses PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper("A4", "portrait");
$dompdf->render();

// Tampilkan PDF (bisa langsung cetak)
$dompdf->stream("laporan_penjualan.pdf", [
    "Attachment" => false
]);
