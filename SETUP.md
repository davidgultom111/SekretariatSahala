# Sistem Informasi Admin Sekretariat Gereja

Sistem informasi admin berbasis Laravel dan Tailwind CSS untuk mengelola data jemaat dan surat-surat gereja.

## Fitur Utama

✅ **Autentikasi Admin**: Login dengan email dan password
✅ **Dashboard**: Tampilkan statistik jumlah jemaat dan surat
✅ **Data Diri Jemaat**: CRUD lengkap untuk data jemaat gereja
✅ **Surat-Menyurat**: 8 jenis surat dalam tampilan card interaktif
✅ **Fitur Tersimpan**: Arsip digital dengan search dan filter surat
✅ **UI/UX Modern**: Desain profesional dengan Tailwind CSS

## Stack Teknologi

- **Backend**: Laravel 12
- **Frontend**: Blade Template Engine
- **CSS Framework**: Tailwind CSS
- **Database**: MySQL
- **PHP**: 8.2+

## Setup dan Instalasi

### 1. Clone dan Setup Database

```bash
# Pastikan database MySQL sudah berjalan
# Buat database baru
mysql -u root -p
CREATE DATABASE sekretariat;
EXIT;
```

### 2. Instalasi Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### 3. Konfigurasi Environment

```bash
# Copy env file
cp .env.example .env

# Generate application key
php artisan key:generate
```

Pastikan konfigurasi database di `.env` sudah benar:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sekretariat
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Jalankan Migrations dan Seeders

```bash
# Jalankan migrations
php artisan migrate

# (Opsional) Jalankan seeders untuk data demo
php artisan db:seed
```

### 5. Build Tailwind CSS

```bash
npm run build
```

Atau untuk development dengan hot reload:

```bash
npm run dev
```

### 6. Jalankan Server

```bash
# Terminal 1: Start Laravel server
php artisan serve

# Terminal 2: Watch Tailwind CSS (opsional)
npm run dev
```

Server akan berjalan di `http://localhost:8000`

## Login Credentials

Setelah menjalankan seeders, gunakan credentials berikut untuk login:

- **Email**: `admin@gereja.com`
- **Password**: `password`

Atau

- **Email**: `staff@gereja.com`
- **Password**: `password`

## Struktur Proyek

```
sekretariat/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── AuthController.php          # Authentication
│   │       ├── DashboardController.php     # Dashboard
│   │       ├── MemberController.php        # CRUD Jemaat
│   │       └── LetterController.php        # Surat Management
│   └── Models/
│       ├── User.php                        # User model
│       ├── Member.php                      # Member model
│       └── Letter.php                      # Letter model
├── database/
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 2026_03_31_000001_create_members_table.php
│   │   └── 2026_03_31_000002_create_letters_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── UserSeeder.php
│       └── MemberSeeder.php
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   └── app.blade.php               # Main layout dengan sidebar
│   │   ├── auth/
│   │   │   └── login.blade.php
│   │   ├── dashboard.blade.php
│   │   ├── member/
│   │   │   ├── index.blade.php
│   │   │   ├── create.blade.php
│   │   │   ├── edit.blade.php
│   │   │   └── show.blade.php
│   │   └── letter/
│   │       ├── types.blade.php
│   │       ├── create.blade.php
│   │       ├── index.blade.php
│   │       └── show.blade.php
│   ├── css/
│   │   └── app.css
│   └── js/
│       └── app.js
├── routes/
│   └── web.php                             # Web routes
├── tailwind.config.js                      # Tailwind configuration
└── vite.config.js                          # Vite configuration
```

## Alur Penggunaan

### 1. Login

Admin membuka halaman login di `/login` dan memasukkan email dan password

### 2. Dashboard

Setelah login, admin akan melihat dashboard dengan statistik:

- Total Jemaat
- Jemaat Aktif
- Total Surat
- Surat Terbaru

### 3. Data Diri Jemaat

Menu "Data Diri Jemaat" memungkinkan admin untuk:

- **Lihat**: Melihat daftar semua jemaat
- **Tambah**: Menambahkan data jemaat baru
- **Edit**: Mengubah data jemaat
- **Hapus**: Menghapus data jemaat

### 4. Surat-Menyurat

Menu "Surat-Menyurat" menampilkan 8 jenis surat dalam card interaktif:

1. Surat Baptisan ✝️
2. Surat Pernikahan 💍
3. Surat Serah Nikah 💒
4. Surat Kematian 🕊️
5. Surat Keluar Jemaat 📤
6. Surat Masuk Jemaat 📥
7. Surat Keterangan Jemaat 📋
8. Surat Rekomendasi ⭐

Klik salah satu card untuk membuat surat baru dengan form yang sesuai dengan tipe surat.

### 5. Fitur Tersimpan (Arsip)

Menu "Fitur Tersimpan" menampilkan semua surat yang telah dibuat dengan:

- **Search**: Cari surat berdasarkan nama jemaat
- **Filter**: Filter surat berdasarkan jenis surat
- **Lihat**: Melihat detail surat
- **Hapus**: Menghapus surat

## Desain dan Warna

### Skema Warna Profesional

| Elemen           | Warna        | Kode      |
| ---------------- | ------------ | --------- |
| Sidebar & Accent | Biru Gelap   | `#1e3a8a` |
| Tombol Aksi      | Kuning       | `#eab308` |
| Tombol Hapus     | Merah        | `#dc2626` |
| Teks Aktif       | Hijau        | `#16a34a` |
| Background       | Abu-abu Muda | `#f9fafb` |

### Komponen UI

- **Sidebar Navigasi**: Biru gelap (#1e3a8a) dengan menu interaktif
- **Tombol Aksi/Edit**: Kuning (#eab308) dengan hover effect
- **Tombol Hapus**: Merah (#dc2626) dengan konfirmasi hapus
- **Cards**: Putih dengan shadow, hover scale effect
- **Pagination**: Bootstrap-style pagination

## Database Schema

### Users Table

```
id (PK)
name
email (UNIQUE)
email_verified_at
password
remember_token
timestamps
```

### Members Table

```
id (PK)
nama_lengkap
jenis_kelamin (ENUM: Laki-laki, Perempuan)
tanggal_lahir
tempat_lahir
no_identitas (UNIQUE)
alamat
kelurahan
kecamatan
kota
provinsi
kode_pos
no_telepon
email
status_perkawinan
pekerjaan
tanggal_bergabung
status_aktif (ENUM: Aktif, Tidak Aktif, Dipindahkan)
timestamps
```

### Letters Table

```
id (PK)
member_id (FK → members.id)
tipe_surat (ENUM: 8 jenis surat)
nomor_surat (UNIQUE)
tanggal_surat
keterangan
isi_surat
file_path
timestamps
```

## Routes

### Authentication Routes

```
POST   /login              - Login admin
POST   /logout             - Logout admin
GET    /login              - Show login form
```

### Dashboard Routes

```
GET    /dashboard          - Dashboard utama
```

### Member Routes

```
GET    /member             - List semua jemaat
POST   /member             - Store jemaat baru
GET    /member/create      - Form create jemaat
GET    /member/{id}        - Show detail jemaat
GET    /member/{id}/edit   - Form edit jemaat
PUT    /member/{id}        - Update jemaat
DELETE /member/{id}        - Delete jemaat
```

### Letter Routes

```
GET    /letter/types       - Tampilkan 8 jenis surat
GET    /letter/create/{type} - Form create surat
GET    /letter             - List semua surat (Fitur Tersimpan)
POST   /letter             - Store surat baru
GET    /letter/{id}        - Show detail surat
DELETE /letter/{id}        - Delete surat
GET    /letter/search      - Search & filter surat
```

## Fitur-Fitur Lanjutan

### 1. Search & Filter Surat

- Search berdasarkan nama jemaat
- Filter berdasarkan jenis surat
- Kombinasi search + filter

### 2. Validasi Data

- Validasi email unik
- Validasi nomor identitas unik
- Validasi nomor surat unik
- Validasi field required

### 3. Pagination

- Daftar jemaat: 10 per halaman
- Daftar surat: 10 per halaman

### 4. Flash Messages

- Sukses message saat data berhasil disimpan
- Error message saat validasi gagal
- Konfirmasi sebelum hapus data

## Troubleshooting

### Database Connection Error

```bash
# Pastikan MySQL service berjalan
# Windows
net start mysql80

# Linux
sudo systemctl start mysql

# Mac
brew services start mysql
```

### Permission Denied Error

```bash
# Pastikan storage folder writable
chmod -R 775 storage bootstrap/cache
```

### Tailwind CSS Not Showing

```bash
# Rebuild Tailwind
npm run build

# Atau gunakan dev mode
npm run dev
```

### Session Error

```bash
# Run migrations untuk session table
php artisan migrate
```

## Support & Kontak

Untuk pertanyaan atau masalah, silakan hubungi tim development.

## License

MIT License
