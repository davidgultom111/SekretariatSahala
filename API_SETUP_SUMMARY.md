# API Setup - Ringkasan Perubahan

## 📋 File yang Dibuat/Diubah

### 🆕 File Baru

#### Controllers (API)

- `app/Http/Controllers/API/MemberAuthController.php` - Authentikasi jemaat
- `app/Http/Controllers/API/MemberApiController.php` - Biodata & Surat
- `app/Http/Controllers/API/AdminApiController.php` - Admin operations

#### Resources

- `app/Http/Resources/MemberResource.php` - Transform Member model
- `app/Http/Resources/LetterResource.php` - Transform Letter model

#### Form Requests

- `app/Http/Requests/API/MemberLoginRequest.php` - Validasi login
- `app/Http/Requests/API/UpdateMemberBiodataRequest.php` - Validasi update biodata

#### Middleware

- `app/Http/Middleware/CheckRole.php` - Role-based access control

#### Routes

- `routes/api.php` - API routing (PUBLIC & PROTECTED)

#### Configuration

- `config/sanctum.php` - Konfigurasi Sanctum (NEW)
- `config/cors.php` - Konfigurasi CORS (NEW)

#### Documentation

- `API_DOCUMENTATION.md` - Dokumentasi lengkap REST API
- `NUXT_INTEGRATION_GUIDE.md` - Panduan integrasi Nuxt.js
- `API_SETUP_SUMMARY.md` - File ini

### ✏️ File yang Diubah

#### Model

- `app/Models/Member.php`
    - ✅ Tambah `use HasApiTokens`
    - ✅ Update `$fillable` + `id_jemaat`, `password`, `role`
    - ✅ Tambah `$hidden` untuk password

#### Configuration

- `config/auth.php`
    - ✅ Tambah import `use App\Models\Member`
    - ✅ Tambah guard 'sanctum' dengan provider 'members'
    - ✅ Tambah provider 'members' untuk Member model

- `bootstrap/app.php`
    - ✅ Tambah `api` routing
    - ✅ Tambah `role` middleware alias
    - ✅ Tambah CORS middleware ke API

#### Migration

- `database/migrations/2026_04_20_094914_add_password_and_role_to_members_table.php`
    - ✅ Tambah kolom `id_jemaat` (string, unique)
    - ✅ Tambah kolom `password` (string, nullable)
    - ✅ Tambah kolom `role` (string, default: 'member')

---

## 🗺️ Struktur Folder API

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── API/                           ← BARU
│   │   │   ├── MemberAuthController.php
│   │   │   ├── MemberApiController.php
│   │   │   └── AdminApiController.php
│   │   └── ...
│   ├── Middleware/
│   │   ├── CheckRole.php                  ← BARU
│   │   └── ...
│   ├── Requests/
│   │   ├── API/                           ← BARU
│   │   │   ├── MemberLoginRequest.php
│   │   │   └── UpdateMemberBiodataRequest.php
│   │   └── ...
│   └── Resources/
│       ├── MemberResource.php             ← BARU
│       ├── LetterResource.php             ← BARU
│       └── ...
├── Models/
│   ├── Member.php                         ← DIUBAH
│   ├── Letter.php
│   └── ...
└── ...

config/
├── app.php
├── auth.php                               ← DIUBAH
├── sanctum.php                            ← BARU
├── cors.php                               ← BARU
└── ...

database/
├── migrations/
│   └── 2026_04_20_094914_add_password_and_role_to_members_table.php ← BARU
└── ...

routes/
├── web.php
├── api.php                                ← BARU
└── ...

bootstrap/
├── app.php                                ← DIUBAH
└── ...
```

---

## 🔄 Endpoint yang Tersedia

### Public Endpoints

```
POST   /api/jemaat/login                   - Login jemaat
```

### Protected Endpoints (auth:sanctum)

```
POST   /api/jemaat/logout                  - Logout jemaat
GET    /api/jemaat/biodata                 - Get biodata
PUT    /api/jemaat/biodata                 - Update biodata
GET    /api/jemaat/surat                   - Get surat list
GET    /api/jemaat/surat/{id}/download     - Download surat PDF
```

### Admin Endpoints (auth:sanctum + role:admin)

```
DELETE /api/admin/jemaat/{id}              - Hapus jemaat
DELETE /api/admin/surat/{id}               - Hapus surat
```

---

## 🚀 Langkah Selanjutnya

### 1. Database Seeding (Testing)

```bash
# Edit database/seeders/MemberSeeder.php
php artisan make:seeder MemberSeeder
```

**Contoh seeder:**

```php
Member::create([
    'id_jemaat' => '31051990',
    'nama_lengkap' => 'John Doe',
    'jenis_kelamin' => 'Laki-laki',
    'tanggal_lahir' => '1990-05-31',
    'tempat_lahir' => 'Jakarta',
    'alamat' => 'Jl. Contoh No. 123',
    'no_telepon' => '081234567890',
    'status_aktif' => true,
    'password' => Hash::make('12345'),
    'role' => 'member'
]);
```

```bash
php artisan db:seed --class=MemberSeeder
```

### 2. Testing API

```bash
# Gunakan Postman atau cURL
# Lihat API_DOCUMENTATION.md untuk contoh lengkap

# Test login
curl -X POST http://localhost:8000/api/jemaat/login \
  -H "Content-Type: application/json" \
  -d '{"id_jemaat":"31051990","password":"12345"}'
```

### 3. Setup Nuxt.js Frontend

```bash
# Di project Nuxt.js
npm install axios

# Ikuti NUXT_INTEGRATION_GUIDE.md untuk setup
```

### 4. Environment Configuration

**File: .env**

```env
SANCTUM_STATEFUL_DOMAINS=localhost:3000,localhost:8080,127.0.0.1:3000,127.0.0.1:8080
FRONTEND_URL=http://localhost:3000
```

### 5. CORS Testing

```bash
# Test CORS header
curl -i -X OPTIONS http://localhost:8000/api/jemaat/login \
  -H "Origin: http://localhost:3000" \
  -H "Access-Control-Request-Method: POST"
```

---

## 🔐 Password Setup

### Current State

- Password di-hash menggunakan bcrypt
- Default password dalam contoh: "12345"

### Ubah Password Jemaat

```php
// Via tinker
php artisan tinker

$member = Member::find(1);
$member->password = Hash::make('new_password_123');
$member->save();
```

---

## ✅ Checklist Implementasi

- [x] Install Laravel Sanctum
- [x] Publish Sanctum files
- [x] Create migration untuk password & role
- [x] Update Member model dengan HasApiTokens
- [x] Configure Sanctum guard
- [x] Create API Resources (Member, Letter)
- [x] Create Form Requests
- [x] Create CheckRole middleware
- [x] Create API Controllers
- [x] Create API routes
- [x] Configure CORS
- [x] Update auth.php
- [x] Update bootstrap/app.php
- [x] Run migration
- [ ] Create Member seeder untuk testing
- [ ] Test semua endpoints
- [ ] Setup Nuxt.js frontend
- [ ] Deploy ke production

---

## 📊 Teknologi yang Digunakan

| Layer            | Technology       |
| ---------------- | ---------------- |
| Framework        | Laravel 12       |
| Authentication   | Laravel Sanctum  |
| API Format       | RESTful JSON     |
| CORS             | Laravel CORS     |
| Frontend         | Nuxt.js 3        |
| HTTP Client      | Axios            |
| Database         | MySQL/PostgreSQL |
| Password Hashing | Bcrypt           |

---

## 🐛 Troubleshooting

### Error: "CORS policy violation"

```
✓ Update config/cors.php
✓ Add FRONTEND_URL di .env
✓ Pastikan middleware CORS di bootstrap/app.php
```

### Error: "Unauthorized"

```
✓ Pastikan token dikirim dengan header: Authorization: Bearer {token}
✓ Cek apakah token sudah expired (biasanya tidak di development)
✓ Cek role jemaat untuk admin endpoints
```

### Error: "Table members doesn't have column password"

```
✓ Jalankan: php artisan migrate
✓ Cek migration file sudah dijalankan dengan benar
```

### Login gagal dengan password benar

```
✓ Cek password di database menggunakan Hash::check()
✓ Pastikan password di-hash dengan bcrypt saat create/update
✓ Cek status_aktif member = true
```

---

## 📞 Dukungan

Untuk pertanyaan atau issue:

1. Cek API_DOCUMENTATION.md untuk endpoint details
2. Cek NUXT_INTEGRATION_GUIDE.md untuk frontend setup
3. Review code di `app/Http/Controllers/API/`
4. Test menggunakan Postman atau cURL

---

## 📄 Lisensi & Catatan

- API ini mengikuti RESTful conventions
- Semua password di-hash dengan bcrypt (aman)
- Token tidak expire (bisa dikonfigurasi di config/sanctum.php)
- CORS enabled untuk development & production

---

**Status:** ✅ SIAP DIGUNAKAN
**Last Updated:** 2026-04-20
**Version:** 1.0
