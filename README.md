# App Kasir 
Aplikasi Kasir Berbasis Web ini adalah sistem yang digunakan untuk membantu proses transaksi penjualan di toko. Aplikasi ini memungkinkan pengguna (admin/kasir) untuk melakukan login, mengelola data produk, mengelola pengguna, serta melakukan transaksi penjualan. Sistem ini dibuat menggunakan PHP dan MySQL, sehingga dapat dijalankan melalui browser dan mudah dikembangkan.

Aplikasi ini cocok digunakan untuk toko kecil hingga menengah karena memiliki fitur dasar kasir yang sederhana namun fungsional.

# User Manual

### A. Login

1. Buka aplikasi melalui browser.
2. Masukkan **username** dan **password**.
3. Klik tombol **Login**.
4. Jika data yang dimasukkan benar, pengguna akan diarahkan ke halaman **Dashboard**.

### B. Dashboard

1. Menampilkan ringkasan informasi aplikasi.
2. Menu navigasi tersedia pada **sidebar** untuk mengakses fitur lainnya.

### C. Manajemen Produk

1. Pilih menu **Produk**.
2. Untuk menambah produk:

   * Klik tombol **Tambah Produk**.
   * Isi data produk seperti **nama produk, harga, stok**, dan **unggah gambar**.
   * Klik tombol **Simpan**.
3. Untuk mengedit produk:

   * Klik tombol **Edit** pada produk yang dipilih.
   * Ubah data produk.
   * Klik tombol **Update**.
4. Untuk menghapus produk:

   * Klik tombol **Hapus** pada produk yang ingin dihapus.

### D. Transaksi Penjualan

1. Pilih menu **Penjualan**.
2. Pilih produk yang akan dijual.
3. Masukkan jumlah barang (**quantity/qty**).
4. Sistem akan menghitung total harga secara otomatis.
5. Klik tombol **Simpan** untuk menyimpan transaksi.

### E. Manajemen User (Admin)

1. Pilih menu **User**.
2. Tambah, ubah, atau hapus data pengguna.
3. Tentukan **role pengguna** sebagai **Admin** atau **Kasir**.

### F. Logout

1. Klik menu **Logout**.
2. Sistem akan mengakhiri sesi pengguna dan kembali ke halaman **Login**.




