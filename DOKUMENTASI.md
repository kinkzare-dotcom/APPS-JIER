# Dokumentasi Sistem Pengaduan Sarana Sekolah (PSS)
UKK Rekayasa Perangkat Lunak 2025/2026

## 1. Deskripsi Program
Aplikasi ini adalah sistem berbasis web untuk menangani laporan kerusakan sarana dan prasarana di lingkungan sekolah. Dibangun menggunakan PHP Native dan MySQL, aplikasi ini memudahkan siswa melaporkan aspirasi dan admin/sekolah untuk menindaklanjuti.

## 2. Struktur Database (ERD Simple)
- **users**: Menyimpan data pengguna (Siswa & Admin).
- **kategori**: Menyimpan kategori laporan (Sarana, Prasarana, Kebersihan, dll).
- **aspirasi**: Menyimpan data laporan pengaduan, termasuk foto bukti.
- **umpan_balik**: Menyimpan respon dari admin terhadap aspirasi.

## 3. Alur Sistem
1. **Siswa** login ke aplikasi.
2. **Siswa** mengisi form aspirasi dan mengupload foto bukti (opsional).
3. **Admin** melihat laporan masuk di dashboard.
4. **Admin** memverifikasi laporan, mengubah status (Proses/Selesai), dan memberikan tanggapan.
5. **Siswa** dapat melihat status laporan dan tanggapan admin di menu Riwayat.

## 4. Fungsi & Prosedur Penting
- `check_login()`: Memastikan user sudah login sebelum akses halaman tertentu.
- `check_role($role)`: Membatasi akses halaman berdasarkan privilege (Admin/Siswa).
- `sanitize($data)`: Membersihkan input user untuk mencegah XSS/SQL Injection.
- `redirect()`: Helper untuk navigasi halaman dengan pesan notifikasi.

## 5. Akun Testing
- **Admin**:
  - Username: `admin`
  - Password: `admin123`
- **Siswa**:
  - Username: `siswa`
  - Password: `siswa123`

## 6. Cara Instalasi
1. Copy folder `PSS jier` ke `htdocs`.
2. Buat database `pss_jier` di PHPMyAdmin.
3. Import file `database.sql`.
4. Buka browser: `http://localhost/PSS jier`.

## 7. Catatan Debugging
- Jika gambar tidak terupload: Pastikan folder `uploads/` ada dan memiliki permission write.
- Jika error database: Periksa setelan di `includes/db.php`.
