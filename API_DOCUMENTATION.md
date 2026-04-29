# REST API Documentation - Sekretariat Gereja

## 📋 Daftar Isi

1. [Instalasi & Konfigurasi](#instalasi--konfigurasi)
2. [Autentikasi](#autentikasi)
3. [Endpoint - Member](#endpoint---member)
4. [Endpoint - Admin](#endpoint---admin)
5. [Response Format](#response-format)
6. [Error Handling](#error-handling)
7. [Testing](#testing)

---

## 🔧 Instalasi & Konfigurasi

### Langkah-langkah yang sudah diselesaikan:

1. ✅ **Laravel Sanctum** - Sudah diinstal untuk token-based authentication
2. ✅ **Database Migration** - Menambahkan fields:
    - `id_jemaat` (string, unique) - Format: DDMMYYYY
    - `password` (string, hashed) - Password jemaat
    - `role` (string, default: 'member') - Admin/Member

3. ✅ **Konfigurasi CORS** - File `config/cors.php`
    - Mengizinkan request dari Nuxt.js (localhost:3000, localhost:8080, dll)
    - Supports credentials untuk token-based auth

4. ✅ **Konfigurasi Sanctum** - File `config/sanctum.php`
    - Guard 'sanctum' untuk API authentication
    - Provider 'members' menggunakan Model `Member`

### Environment Configuration (.env)

```env
# Tambahkan atau ubah ini di .env
SANCTUM_STATEFUL_DOMAINS=localhost:3000,localhost:8080,127.0.0.1:3000,127.0.0.1:8080
FRONTEND_URL=http://localhost:3000
```

### API Routing

File: `routes/api.php` sudah dikonfigurasi dengan routing berikut:

- Public: Login endpoint
- Protected: Biodata & Surat endpoints
- Admin: Delete operations

---

## 🔐 Autentikasi

### Sistem Login

#### POST `/api/jemaat/login`

**Request:**

```json
{
    "id_jemaat": "31051990",
    "password": "12345"
}
```

**Success Response (200):**

```json
{
    "status": "success",
    "message": "Login berhasil",
    "data": {
        "member": {
            "id": 1,
            "id_jemaat": "31051990",
            "nama_lengkap": "John Doe",
            "jenis_kelamin": "Laki-laki",
            "tanggal_lahir": "1990-05-31",
            "tempat_lahir": "Jakarta",
            "alamat": "Jl. Contoh No. 123",
            "no_telepon": "081234567890",
            "status_aktif": true,
            "role": "member",
            "created_at": "2026-04-20T10:00:00Z",
            "updated_at": "2026-04-20T10:00:00Z"
        },
        "token": "1|XXXXXXXXXXXXXXXXXXXX"
    }
}
```

**Error Response (401):**

```json
{
    "status": "error",
    "message": "ID Jemaat atau password salah"
}
```

### Token Usage

Setelah login, gunakan token di header:

```
Authorization: Bearer {token}
```

Contoh dengan cURL:

```bash
curl -X GET http://localhost:8000/api/jemaat/biodata \
  -H "Authorization: Bearer 1|XXXXXXXXXXXXXXXXXXXX"
```

### Logout

#### POST `/api/jemaat/logout`

```bash
curl -X POST http://localhost:8000/api/jemaat/logout \
  -H "Authorization: Bearer {token}"
```

Response:

```json
{
    "status": "success",
    "message": "Logout berhasil"
}
```

---

## 👤 Endpoint - Member (Protected)

**Middleware:** `auth:sanctum`
**Header:** `Authorization: Bearer {token}`

### 1. GET `/api/jemaat/biodata` - Ambil Biodata Jemaat

Mengambil data profil jemaat yang sedang login.

**Request:**

```bash
curl -X GET http://localhost:8000/api/jemaat/biodata \
  -H "Authorization: Bearer {token}"
```

**Response (200):**

```json
{
    "status": "success",
    "data": {
        "id": 1,
        "id_jemaat": "31051990",
        "nama_lengkap": "John Doe",
        "jenis_kelamin": "Laki-laki",
        "tanggal_lahir": "1990-05-31",
        "tempat_lahir": "Jakarta",
        "alamat": "Jl. Contoh No. 123",
        "no_telepon": "081234567890",
        "status_aktif": true,
        "role": "member",
        "created_at": "2026-04-20T10:00:00Z",
        "updated_at": "2026-04-20T10:00:00Z"
    }
}
```

---

### 2. PUT `/api/jemaat/biodata` - Update Biodata Jemaat

Update data biodata jemaat. **Catatan:** Tidak bisa mengupdate tanggal/bulan/tahun lahir.

**Request:**

```bash
curl -X PUT http://localhost:8000/api/jemaat/biodata \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "nama_lengkap": "John Updated",
    "alamat": "Jl. Baru No. 456",
    "no_telepon": "082345678901"
  }'
```

**Allowed Fields:**

- `nama_lengkap` (string, max 255)
- `jenis_kelamin` (enum: "Laki-laki", "Perempuan")
- `tempat_lahir` (string, max 255)
- `alamat` (string)
- `no_telepon` (string, max 20)
- `status_aktif` (boolean)

**Response (200):**

```json
{
    "status": "success",
    "message": "Data biodata berhasil diperbarui",
    "data": {
        "id": 1,
        "id_jemaat": "31051990",
        "nama_lengkap": "John Updated",
        "jenis_kelamin": "Laki-laki",
        "tanggal_lahir": "1990-05-31",
        "tempat_lahir": "Jakarta",
        "alamat": "Jl. Baru No. 456",
        "no_telepon": "082345678901",
        "status_aktif": true,
        "role": "member"
    }
}
```

---

### 3. GET `/api/jemaat/surat` - List Surat Jemaat

Menampilkan daftar surat milik jemaat yang sedang login dengan support pencarian.

**Request tanpa pencarian:**

```bash
curl -X GET http://localhost:8000/api/jemaat/surat \
  -H "Authorization: Bearer {token}"
```

**Request dengan pencarian:**

```bash
curl -X GET "http://localhost:8000/api/jemaat/surat?keyword=surat%20nikah" \
  -H "Authorization: Bearer {token}"
```

**Query Parameters:**

- `keyword` (optional) - Cari berdasarkan nama/jenis surat dengan LIKE %keyword%

**Response (200):**

```json
{
    "status": "success",
    "data": [
        {
            "id": 1,
            "member_id": 1,
            "tipe_surat": "Surat Nikah",
            "letter_type": "marriage_letter",
            "nomor_surat": "001/SK/2026",
            "tanggal_surat": "2026-04-20",
            "keterangan": "Surat nikah",
            "pdf_path": "letters/001-surat-nikah.pdf",
            "created_at": "2026-04-20T10:00:00Z",
            "updated_at": "2026-04-20T10:00:00Z"
        },
        {
            "id": 2,
            "member_id": 1,
            "tipe_surat": "Surat Keterangan Aktif",
            "letter_type": "active_letter",
            "nomor_surat": "002/SK/2026",
            "tanggal_surat": "2026-04-15",
            "keterangan": "Surat keterangan aktif jemaat",
            "pdf_path": "letters/002-surat-aktif.pdf",
            "created_at": "2026-04-15T10:00:00Z",
            "updated_at": "2026-04-15T10:00:00Z"
        }
    ]
}
```

**Response (200) - Kosong:**

```json
{
    "status": "success",
    "data": []
}
```

---

### 4. GET `/api/jemaat/surat/{id}/download` - Download Surat PDF

Download file PDF surat. Jemaat hanya bisa download surat miliknya sendiri.

**Request:**

```bash
curl -X GET http://localhost:8000/api/jemaat/surat/1/download \
  -H "Authorization: Bearer {token}" \
  -o surat_001.pdf
```

**Response (200):** File binary (PDF)

**Error Response (404):**

```json
{
    "status": "error",
    "message": "Surat tidak ditemukan"
}
```

---

## 🔑 Endpoint - Admin (Protected)

**Middleware:** `auth:sanctum` + `role:admin`
**Header:** `Authorization: Bearer {token}` (dari user dengan role 'admin')

### 1. DELETE `/api/admin/jemaat/{id}` - Hapus Jemaat

Menghapus data jemaat dan semua surat terkait (cascade delete).

**Request:**

```bash
curl -X DELETE http://localhost:8000/api/admin/jemaat/5 \
  -H "Authorization: Bearer {admin_token}"
```

**Response (200):**

```json
{
    "status": "success",
    "message": "Jemaat dan data terkait berhasil dihapus"
}
```

**Error Response (404):**

```json
{
    "status": "error",
    "message": "Jemaat tidak ditemukan"
}
```

---

### 2. DELETE `/api/admin/surat/{id}` - Hapus Surat

Menghapus data surat dan file PDF terkait.

**Request:**

```bash
curl -X DELETE http://localhost:8000/api/admin/surat/10 \
  -H "Authorization: Bearer {admin_token}"
```

**Response (200):**

```json
{
    "status": "success",
    "message": "Surat berhasil dihapus"
}
```

**Error Response (404):**

```json
{
    "status": "error",
    "message": "Surat tidak ditemukan"
}
```

---

## 📊 Response Format

Semua response mengikuti format standar:

```json
{
    "status": "success|error",
    "message": "Deskripsi operasi",
    "data": {}
}
```

**Status:**

- `success` - Operasi berhasil (HTTP 200)
- `error` - Operasi gagal (HTTP 400, 401, 403, 404, 500)

---

## ⚠️ Error Handling

### HTTP Status Codes

- **200 OK** - Request berhasil
- **400 Bad Request** - Validasi gagal
- **401 Unauthorized** - Token tidak valid atau tidak ada
- **403 Forbidden** - User tidak punya akses/role tidak sesuai
- **404 Not Found** - Resource tidak ditemukan
- **500 Internal Server Error** - Error di server

### Contoh Error Response

```json
{
    "status": "error",
    "message": "ID Jemaat atau password salah"
}
```

---

## 🧪 Testing

### Menggunakan Postman

1. **Import Collection:**
    - POST `{{base_url}}/api/jemaat/login`
    - GET `{{base_url}}/api/jemaat/biodata`
    - PUT `{{base_url}}/api/jemaat/biodata`
    - GET `{{base_url}}/api/jemaat/surat`
    - GET `{{base_url}}/api/jemaat/surat/1/download`
    - DELETE `{{base_url}}/api/admin/jemaat/1`
    - DELETE `{{base_url}}/api/admin/surat/1`

2. **Variable Postman:**

```
base_url = http://localhost:8000
token = {token dari login response}
admin_token = {token dari admin account}
```

### Menggunakan cURL

```bash
# Test login
curl -X POST http://localhost:8000/api/jemaat/login \
  -H "Content-Type: application/json" \
  -d '{
    "id_jemaat": "31051990",
    "password": "12345"
  }'

# Test get biodata dengan token
TOKEN="1|XXXXXXXXXXXXXXXXXXXX"
curl -X GET http://localhost:8000/api/jemaat/biodata \
  -H "Authorization: Bearer $TOKEN"
```

### Menggunakan Axios (JavaScript/Nuxt.js)

```javascript
// Login
const response = await axios.post("/api/jemaat/login", {
    id_jemaat: "31051990",
    password: "12345",
});

const token = response.data.data.token;

// Simpan token di localStorage atau sessionStorage
localStorage.setItem("api_token", token);

// Buat axios instance dengan default header
const api = axios.create({
    baseURL: "http://localhost:8000",
    headers: {
        Authorization: `Bearer ${token}`,
    },
});

// Get biodata
const biodata = await api.get("/api/jemaat/biodata");
console.log(biodata.data);

// Get surat dengan pencarian
const surat = await api.get("/api/jemaat/surat?keyword=nikah");
console.log(surat.data);

// Update biodata
await api.put("/api/jemaat/biodata", {
    nama_lengkap: "John Updated",
    alamat: "Jl. Baru No. 456",
});
```

---

## 📝 Catatan Penting

1. **Password Hashing:**
    - Password disimpan dengan bcrypt hash
    - Default password bisa disetting di seeder atau dashboard admin

2. **ID Jemaat Format:**
    - Format: DDMMYYYY (tanggal lahir jemaat)
    - Contoh: "31051990" = 31 Mei 1990

3. **Token Security:**
    - Token valid selamanya (bisa dikonfigurasi di `config/sanctum.php`)
    - Saat logout, token akan dihapus
    - Untuk security, simpan token di httpOnly cookie (jika SPA)

4. **CORS:**
    - API mengizinkan request dari domain Nuxt.js
    - Konfigurasi di `config/cors.php`
    - Ubah `FRONTEND_URL` di `.env` sesuai deployment

5. **Member Filter:**
    - Setiap jemaat hanya bisa akses data miliknya sendiri
    - Server side filtering pada endpoint biodata dan surat
    - Admin bisa akses delete operations

---

## 🚀 Deployment

Sebelum production:

1. Ubah `FRONTEND_URL` di `.env`
2. Update `SANCTUM_STATEFUL_DOMAINS` di `.env`
3. Ubah CORS `allowed_origins` di `config/cors.php`
4. Set `APP_DEBUG=false` di `.env`
5. Set `APP_ENV=production` di `.env`
