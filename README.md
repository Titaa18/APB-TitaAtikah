# Dokumentasi Sistem Informasi Perpustakaan Digital

Aplikasi ini adalah sistem manajemen perpustakaan sekolah berbasis web yang dibangun menggunakan PHP Native dan MySQL.

## 1. Skema Database (ERD Deskripsi)
- **Tabel `users`**: Menyimpan data pengguna (admin dan siswa).
  - `id_user` (Primary Key, AI)
  - `nama`, `email`, `password` (MD5), `role` (admin/user)
- **Tabel `buku`**: Menyimpan data koleksi buku.
  - `id_buku` (Primary Key, AI)
  - `judul`, `pengarang`, `penerbit`, `tahun`, `stok`
- **Tabel `transaksi`**: Menghubungkan user dan buku (Relasi One-to-Many).
  - `id_transaksi` (Primary Key, AI)
  - `id_user` (Foreign Key -> users)
  - `id_buku` (Foreign Key -> buku)
  - `tanggal_pinjam`, `tanggal_kembali`, `status` (dipinjam/dikembalikan)

**Relasi**:
- Satu `user` dapat memiliki banyak `transaksi` (peminjaman).
- Satu `buku` dapat muncul di banyak `transaksi`.

## 2. Fitur Utama
### Admin
- **Dashboard**: Visualisasi data menggunakan Chart.js dan statistik total.
- **Manajemen Buku**: CRUD (Create, Read, Update, Delete) data koleksi buku.
- **Manajemen Anggota**: CRUD data user/siswa yang terdaftar.
- **Manajemen Transaksi**: Melacak peminjaman, memproses pengembalian buku, dan update stok otomatis.

### User (Siswa)
- **Registrasi & Login**: Keamanan menggunakan MD5 hashing dan session.
- **Katalog Buku**: Melihat daftar buku tersedia secara real-time.
- **Peminjaman**: Melakukan peminjaman mandiri dengan pengecekan stok otomatis.
- **Riwayat**: Melihat daftar buku yang sedang dipinjam atau sudah dikembalikan.

## 3. Alur Sistem
1. **Login**: User memasukkan email dan password. Sistem memvalidasi role.
2. **Peminjaman**: 
   - Siswa memilih buku -> Sistem cek `stok > 0`.
   - Jika ok, `stok` dikurangi 1, data masuk ke `transaksi`.
3. **Pengembalian**: 
   - Admin melihat daftar `transaksi` berstatus 'dipinjam'.
   - Admin klik 'Kembali' -> `stok` buku bertambah 1, status transaksi jadi 'dikembalikan'.

## 4. Teknologi yang Digunakan
- **Frontend**: Bootstrap 5, Font Awesome, SweetAlert2 (Notifikasi), Chart.js (Grafik).
- **Backend**: PHP Native (mysqli).
- **Database**: MySQL.

## 5. Panduan Instalasi (XAMPP)
1. Pindahkan folder `APB-Tita` ke dalam `C:/xampp/htdocs/`.
2. Nyalakan **Apache** dan **MySQL** di XAMPP Control Panel.
3. Buka browser, akses `http://localhost/phpmyadmin`.
4. Buat database baru bernama `db_perpustakaan`.
5. Import file `database.sql`.
6. Akses aplikasi di `http://localhost/APB-Tita`.

**Akun Login Default:**
- **Admin**: admin@gmail.com (Password: admin123)
- **User**: user@gmail.com (Password: user123)

## 6. Dokumentasi Debugging Singkat
- **Koneksi Gagal**: Cek `koneksi.php`, pastikan username (`root`) dan password database sesuai.
- **Error 404**: Pastikan folder `admin` dan `user` berada dalam direktori yang benar.
- **Stok Tidak Update**: Cek query UPDATE pada `user/pinjam.php` dan `admin/transaksi.php`.
- **Query Error**: Gunakan `mysqli_error($conn)` untuk melihat pesan error spesifik dari MySQL.
