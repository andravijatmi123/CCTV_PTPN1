# CCTV Dashboard - Panduan Setup

## Langkah-langkah Instalasi

### 1. Membuat Database MySQL
- Buka phpMyAdmin (http://localhost/phpmyadmin)
- Buat database baru bernama `cctv_db`
- Import file `database.sql` ke database tersebut
- Atau jalankan query manual dari file `database.sql`

### 2. Konfigurasi Database di index.php
Pastikan konfigurasi berikut sesuai dengan setup Anda:
```php
$host = 'localhost';      // Host database
$user = 'root';           // Username MySQL
$password = '';           // Password MySQL (default kosong untuk XAMPP)
$database = 'cctv_db';    // Nama database
```

### 3. Struktur Tabel Database
```
Tabel: cctv
- id (INT, Primary Key, Auto Increment)
- nama_kebun (VARCHAR 100) - Nama lokasi kebun
- ip_cctv (VARCHAR 15) - Alamat IP kamera CCTV
- created_at (TIMESTAMP) - Tanggal pembuatan
- updated_at (TIMESTAMP) - Tanggal update terakhir
```

### 4. Menambah Data CCTV Baru
Gunakan query SQL:
```sql
INSERT INTO cctv (nama_kebun, ip_cctv) VALUES ('NAMA LOKASI', 'IP_ADDRESS');
```

Contoh:
```sql
INSERT INTO cctv (nama_kebun, ip_cctv) VALUES ('RUANG A', '192.168.1.100');
INSERT INTO cctv (nama_kebun, ip_cctv) VALUES ('RUANG B', '192.168.1.101');
```

### 5. Mengakses Dashboard
- Buka browser dan kunjungi: http://localhost/web_cctv/index.php
- Dashboard akan menampilkan semua kamera CCTV dari database secara dinamis

## Fitur Utama
✅ Koneksi database MySQL otomatis
✅ Menampilkan CCTV secara dinamis dari database
✅ Jam real-time (JavaScript)
✅ Responsive grid layout
✅ Error handling untuk koneksi database
✅ Secure dengan htmlspecialchars() untuk prevent XSS

## Troubleshooting

### Error: Koneksi gagal
- Pastikan MySQL server berjalan
- Cek username dan password database
- Pastikan database `cctv_db` sudah dibuat

### Error: Tabel tidak ditemukan
- Import file `database.sql` atau buat tabel manual
- Pastikan nama tabel adalah `cctv`

### Video tidak tampil
- Cek konfigurasi IP CCTV di database
- Pastikan format URL streaming sesuai dengan server CCTV Anda
- Ubah URL dari `http://IP:8889/stream` sesuai kebutuhan

## File-file Project
- `index.php` - Dashboard utama (PHP dengan database)
- `style.css` - Stylesheet/CSS
- `script.js` - JavaScript untuk jam dan fitur lainnya
- `database.sql` - Script SQL untuk membuat database dan tabel
- `README.md` - File dokumentasi ini
