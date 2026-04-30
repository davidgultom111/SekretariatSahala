# API Documentation — Sekretariat Jemaat GPdI

REST API untuk sistem sekretariat Gereja Pentekosta di Indonesia Jemaat Sahabat Allah Palembang.
Base URL: `http://localhost:8000`

---

## Daftar Isi

1. [Autentikasi](#autentikasi)
2. [Member — Self-Service](#member--self-service)
3. [Admin — Manajemen Jemaat](#admin--manajemen-jemaat)
4. [Admin — Manajemen Surat](#admin--manajemen-surat)
5. [Health Check](#health-check)
6. [Response Format](#response-format)
7. [Error Handling](#error-handling)
8. [Tipe Surat](#tipe-surat)
9. [Kredensial Testing](#kredensial-testing)

---

## Autentikasi

### POST `/api/auth/login`

Login jemaat. Hanya jemaat dengan `status_aktif = "Aktif"` yang bisa login.

**Request Body:**

```json
{
    "id_jemaat": "01011980",
    "password": "12345"
}
```

**Response 200:**

```json
{
    "status": "success",
    "message": "Login berhasil",
    "data": {
        "member": {
            "id": 1,
            "id_jemaat": "01011980",
            "nama_lengkap": "Tamrin Gultom",
            "jenis_kelamin": "Laki-laki",
            "tanggal_lahir": "1980-01-01",
            "tempat_lahir": "Palembang",
            "alamat": "Jl. Sejahtera Lr. Sahabat",
            "no_telepon": "081234567890",
            "status_aktif": "Aktif",
            "role": "admin",
            "created_at": "2026-01-01T00:00:00+00:00",
            "updated_at": "2026-01-01T00:00:00+00:00"
        },
        "token": "1|abcdefghijklmnopqrstuvwxyz"
    }
}
```

**Response 401** — ID atau password salah:

```json
{
    "status": "error",
    "message": "ID Jemaat atau password salah"
}
```

**Response 403** — Akun tidak aktif:

```json
{
    "status": "error",
    "message": "Akun jemaat tidak aktif"
}
```

---

### DELETE `/api/me/logout`

Logout — mencabut token yang sedang aktif.

**Header:** `Authorization: Bearer {token}`

**Response:** `204 No Content`

---

## Member — Self-Service

Semua endpoint di bagian ini memerlukan:

```
Authorization: Bearer {token}
```

---

### GET `/api/me`

Ambil profil jemaat yang sedang login.

**Response 200:**

```json
{
    "status": "success",
    "data": {
        "id": 2,
        "id_jemaat": "15051980",
        "nama_lengkap": "Sari Dewi",
        "jenis_kelamin": "Perempuan",
        "tanggal_lahir": "1980-05-15",
        "tempat_lahir": "Palembang",
        "alamat": "Jl. Contoh No. 10",
        "no_telepon": "082345678901",
        "status_aktif": "Aktif",
        "role": "member",
        "created_at": "2026-01-01T00:00:00+00:00",
        "updated_at": "2026-01-01T00:00:00+00:00"
    }
}
```

---

### PUT `/api/me`

Update profil sendiri. Hanya 3 field yang boleh diubah.

**Request Body** (semua opsional):

| Field | Tipe | Validasi |
|-------|------|----------|
| `nama_lengkap` | string | max 255 |
| `alamat` | string | max 500 |
| `no_telepon` | string | max 20 |

**Contoh:**

```json
{
    "nama_lengkap": "Sari Dewi Updated",
    "no_telepon": "08111222333"
}
```

**Response 200:**

```json
{
    "status": "success",
    "message": "Biodata berhasil diperbarui",
    "data": { ...member object... }
}
```

---

### GET `/api/me/letters`

Daftar surat milik jemaat yang sedang login.

**Query Parameters:**

| Parameter | Tipe | Keterangan |
|-----------|------|------------|
| `keyword` | string | Cari berdasarkan `tipe_surat` atau `nomor_surat` |

**Response 200:**

```json
{
    "status": "success",
    "data": [
        {
            "id": 1,
            "member_id": 2,
            "tipe_surat": "Surat Pengantar",
            "letter_type": "surat_pengantar",
            "nomor_surat": "001/GPdI/SA/SP/2026",
            "tanggal_surat": "2026-04-01",
            "keterangan": "Untuk keperluan melamar pekerjaan",
            "has_pdf": true,
            "pdf_url": "http://localhost:8000/api/admin/letters/1/pdf",
            "created_at": "2026-04-01T08:00:00+00:00",
            "updated_at": "2026-04-01T08:00:00+00:00"
        }
    ]
}
```

> Field yang bukan milik tipe surat akan bernilai `null`.

---

### GET `/api/me/letters/{id}/download`

Download PDF surat milik sendiri. **Hanya bisa download jika PDF sudah tersimpan** (`has_pdf = true`).

**Response 200:** Binary file (PDF)

**Response 404** — Surat tidak ditemukan atau bukan milik user:

```json
{
    "status": "error",
    "message": "Surat tidak ditemukan"
}
```

**Response 404** — PDF belum tersedia:

```json
{
    "status": "error",
    "message": "File PDF belum tersedia"
}
```

---

## Admin — Manajemen Jemaat

Semua endpoint di bagian ini memerlukan:

```
Authorization: Bearer {token}   ← token dari akun dengan role "admin"
```

---

### GET `/api/admin/members`

Daftar semua jemaat dengan pagination.

**Query Parameters:**

| Parameter | Tipe | Keterangan |
|-----------|------|------------|
| `search` | string | Cari berdasarkan `nama_lengkap` atau `id_jemaat` |
| `status` | string | Filter: `Aktif`, `Tidak Aktif`, `Dipindahkan` |
| `per_page` | integer | Jumlah per halaman (default: 15) |

**Response 200:**

```json
{
    "status": "success",
    "data": {
        "data": [
            {
                "id": 1,
                "id_jemaat": "01011980",
                "nama_lengkap": "Tamrin Gultom",
                "jenis_kelamin": "Laki-laki",
                "tanggal_lahir": "1980-01-01",
                "tempat_lahir": "Palembang",
                "alamat": "Jl. Sejahtera Lr. Sahabat",
                "no_telepon": "081234567890",
                "status_aktif": "Aktif",
                "role": "admin",
                "created_at": "2026-01-01T00:00:00+00:00",
                "updated_at": "2026-01-01T00:00:00+00:00"
            }
        ],
        "links": { "first": "...", "last": "...", "prev": null, "next": null },
        "meta": {
            "current_page": 1,
            "last_page": 1,
            "per_page": 15,
            "total": 1
        }
    }
}
```

---

### POST `/api/admin/members`

Tambah jemaat baru. Password default otomatis `12345`. `id_jemaat` digenerate otomatis dari `tanggal_lahir` (format `DDMMYYYY`).

**Request Body:**

| Field | Tipe | Wajib | Validasi |
|-------|------|-------|----------|
| `nama_lengkap` | string | Ya | max 255 |
| `jenis_kelamin` | string | Ya | `Laki-laki` atau `Perempuan` |
| `tanggal_lahir` | date | Ya | Format `Y-m-d`, harus sebelum hari ini |
| `tempat_lahir` | string | Ya | max 255 |
| `alamat` | string | Ya | max 500 |
| `no_telepon` | string | Ya | max 20 |
| `status_aktif` | string | Ya | `Aktif`, `Tidak Aktif`, atau `Dipindahkan` |

**Contoh:**

```json
{
    "nama_lengkap": "Budi Santoso",
    "jenis_kelamin": "Laki-laki",
    "tanggal_lahir": "1995-08-17",
    "tempat_lahir": "Jakarta",
    "alamat": "Jl. Merdeka No. 5",
    "no_telepon": "081199887766",
    "status_aktif": "Aktif"
}
```

**Response 201:**

```json
{
    "status": "success",
    "message": "Data jemaat berhasil ditambahkan",
    "data": {
        "id": 3,
        "id_jemaat": "17081995",
        ...
    }
}
```

---

### GET `/api/admin/members/{id}`

Detail satu jemaat berdasarkan ID database.

**Response 200:**

```json
{
    "status": "success",
    "data": { ...member object... }
}
```

**Response 404:** `{ "status": "error", "message": "Not Found" }`

---

### PUT `/api/admin/members/{id}`

Update data jemaat. Semua field opsional. Jika `tanggal_lahir` diubah, `id_jemaat` akan digenerate ulang otomatis.

**Request Body** (semua opsional):

| Field | Tipe | Validasi |
|-------|------|----------|
| `nama_lengkap` | string | max 255 |
| `jenis_kelamin` | string | `Laki-laki` atau `Perempuan` |
| `tanggal_lahir` | date | Format `Y-m-d`, sebelum hari ini |
| `tempat_lahir` | string | max 255 |
| `alamat` | string | max 500 |
| `no_telepon` | string | max 20 |
| `status_aktif` | string | `Aktif`, `Tidak Aktif`, atau `Dipindahkan` |
| `password` | string | min 6 karakter |

**Response 200:**

```json
{
    "status": "success",
    "message": "Data jemaat berhasil diperbarui",
    "data": { ...member object... }
}
```

---

### DELETE `/api/admin/members/{id}`

Hapus permanen jemaat beserta seluruh suratnya (cascade).

**Response:** `204 No Content`

---

## Admin — Manajemen Surat

### GET `/api/admin/letters`

Daftar semua surat dengan pagination.

**Query Parameters:**

| Parameter | Tipe | Keterangan |
|-----------|------|------------|
| `search` | string | Cari berdasarkan `nama_lengkap` jemaat atau `nomor_surat` |
| `letter_type` | string | Filter berdasarkan tipe surat (slug) |
| `per_page` | integer | Jumlah per halaman (default: 15) |

**Response 200:**

```json
{
    "status": "success",
    "data": {
        "data": [ ...array of letter objects... ],
        "links": { ... },
        "meta": { "current_page": 1, "last_page": 2, "per_page": 15, "total": 25 }
    }
}
```

---

### POST `/api/admin/letters`

Buat surat baru. Field yang wajib berbeda sesuai `letter_type`. Nomor surat digenerate otomatis.

**Field Umum:**

| Field | Tipe | Wajib | Keterangan |
|-------|------|-------|------------|
| `letter_type` | string | Ya | Slug tipe surat (lihat [Tipe Surat](#tipe-surat)) |
| `tanggal_surat` | date | Ya | Format `Y-m-d` |
| `keterangan` | string | Lihat per tipe | Catatan tambahan |

**Field Tambahan per Tipe Surat:**

#### `surat_tugas_pelayanan`

```json
{
    "letter_type": "surat_tugas_pelayanan",
    "member_id": 2,
    "tanggal_surat": "2026-04-30",
    "tgl_mulai_tugas": "2026-05-01",
    "tgl_akhir_tugas": "2026-05-07",
    "tujuan_tugas": "Pelayanan kebangunan rohani di Jl. Merdeka"
}
```

#### `surat_pengantar`

```json
{
    "letter_type": "surat_pengantar",
    "member_id": 2,
    "tanggal_surat": "2026-04-30",
    "keterangan": "Untuk keperluan melamar pekerjaan di PT. Maju Jaya"
}
```

#### `surat_keterangan_jemaat_aktif`

```json
{
    "letter_type": "surat_keterangan_jemaat_aktif",
    "member_id": 2,
    "tanggal_surat": "2026-04-30",
    "tahun_bergabung": 2015
}
```

#### `surat_nilai_sekolah`

```json
{
    "letter_type": "surat_nilai_sekolah",
    "member_id": 2,
    "tanggal_surat": "2026-04-30",
    "asal_sekolah": "SD Negeri 01 Palembang",
    "kelas": "6A",
    "semester": "Ganjil",
    "nilai": 90
}
```

#### `surat_pengajuan_baptisan`

```json
{
    "letter_type": "surat_pengajuan_baptisan",
    "member_id": 2,
    "tanggal_surat": "2026-04-30"
}
```

#### `surat_pengajuan_penyerahan_anak`

```json
{
    "letter_type": "surat_pengajuan_penyerahan_anak",
    "member_id": 2,
    "tanggal_surat": "2026-04-30",
    "nama_ayah": "Budi Santoso",
    "nama_ibu": "Sari Dewi",
    "nama_anak": "Anugerah Santoso",
    "tempat_lahir_anak": "Palembang",
    "tanggal_lahir_anak": "2026-01-15"
}
```

#### `surat_pengajuan_pernikahan`

> Tidak menggunakan `member_id`. Gunakan `member_pria_id` dan `member_wanita_id`.

```json
{
    "letter_type": "surat_pengajuan_pernikahan",
    "tanggal_surat": "2026-04-30",
    "member_pria_id": 3,
    "member_wanita_id": 4,
    "tanggal_pernikahan": "2026-06-15"
}
```

**Response 201:**

```json
{
    "status": "success",
    "message": "Surat berhasil dibuat",
    "data": {
        "id": 5,
        "member_id": 2,
        "member": {
            "id": 2,
            "id_jemaat": "15051980",
            "nama_lengkap": "Sari Dewi",
            "tempat_lahir": "Palembang",
            "tanggal_lahir": "1980-05-15",
            "alamat": "Jl. Contoh No. 10",
            "no_telepon": "082345678901",
            "jenis_kelamin": "Perempuan"
        },
        "tipe_surat": "Surat Pengantar",
        "letter_type": "surat_pengantar",
        "nomor_surat": "001/GPdI/SA/SP/2026",
        "tanggal_surat": "2026-04-30",
        "keterangan": "Untuk keperluan melamar pekerjaan di PT. Maju Jaya",
        "tgl_mulai_tugas": null,
        "tgl_akhir_tugas": null,
        "tujuan_tugas": null,
        "tahun_bergabung": null,
        "asal_sekolah": null,
        "kelas": null,
        "semester": null,
        "nilai": null,
        "nama_ayah": null,
        "nama_ibu": null,
        "nama_anak": null,
        "tempat_lahir_anak": null,
        "tanggal_lahir_anak": null,
        "member_pria_id": null,
        "member_wanita_id": null,
        "tanggal_pernikahan": null,
        "has_pdf": false,
        "pdf_url": null,
        "created_at": "2026-04-30T08:00:00+00:00",
        "updated_at": "2026-04-30T08:00:00+00:00"
    }
}
```

---

### GET `/api/admin/letters/{id}`

Detail satu surat. Response menyertakan data `member`, `member_pria`, dan `member_wanita` (untuk surat pernikahan).

**Response 200:**

```json
{
    "status": "success",
    "data": { ...letter object lengkap dengan relasi member... }
}
```

---

### DELETE `/api/admin/letters/{id}`

Hapus surat. Jika ada file PDF tersimpan, file juga ikut dihapus.

**Response:** `204 No Content`

---

### GET `/api/admin/letters/{id}/pdf`

Download PDF surat. Jika belum ada PDF tersimpan, akan digenerate otomatis dari template dan disimpan untuk download berikutnya.

**Response 200:** Binary file (PDF, Content-Type: `application/pdf`)

Nama file: `surat_{nomor_surat_dengan_dash}.pdf`

---

## Health Check

### GET `/api/health`

Cek status server. Tidak memerlukan autentikasi.

**Response 200:**

```json
{
    "status": "ok",
    "timestamp": "2026-04-30T08:00:00+00:00"
}
```

---

## Response Format

Semua endpoint menggunakan format JSON seragam:

**Sukses:**

```json
{
    "status": "success",
    "message": "Deskripsi opsional",
    "data": { ... }
}
```

**Error:**

```json
{
    "status": "error",
    "message": "Deskripsi error",
    "errors": { ... }
}
```

**Dibuat (201):**

```json
{
    "status": "success",
    "message": "...",
    "data": { ... }
}
```

**Tanpa konten (204):** Body kosong.

---

## Error Handling

### HTTP Status Codes

| Kode | Keterangan |
|------|------------|
| 200 | Request berhasil |
| 201 | Resource berhasil dibuat |
| 204 | Berhasil, tidak ada konten (logout, delete) |
| 401 | Tidak terautentikasi — token tidak ada atau tidak valid |
| 403 | Tidak punya akses — role bukan admin, atau akun tidak aktif |
| 404 | Resource tidak ditemukan |
| 422 | Validasi gagal |
| 500 | Error internal server |

### Contoh Error Validasi (422)

```json
{
    "status": "error",
    "message": "The given data was invalid.",
    "errors": {
        "letter_type": ["Tipe surat tidak valid"],
        "member_id": ["Jemaat tidak ditemukan"]
    }
}
```

---

## Tipe Surat

| `letter_type` (slug) | Nama Tampilan | Kode | Field Tambahan Wajib |
|----------------------|--------------|------|----------------------|
| `surat_tugas_pelayanan` | Surat Tugas Pelayanan | TP | `member_id`, `tgl_mulai_tugas`, `tgl_akhir_tugas`, `tujuan_tugas` |
| `surat_pengantar` | Surat Pengantar | SP | `member_id`, `keterangan` |
| `surat_keterangan_jemaat_aktif` | Surat Keterangan Jemaat Aktif | KJA | `member_id`, `tahun_bergabung` |
| `surat_nilai_sekolah` | Surat Nilai Sekolah | NS | `member_id`, `asal_sekolah`, `kelas`, `semester` |
| `surat_pengajuan_baptisan` | Surat Pengajuan Baptisan | PB | `member_id` |
| `surat_pengajuan_penyerahan_anak` | Surat Pengajuan Penyerahan Anak | PA | `member_id`, `nama_ayah`, `nama_ibu`, `nama_anak`, `tempat_lahir_anak`, `tanggal_lahir_anak` |
| `surat_pengajuan_pernikahan` | Surat Pengajuan Pernikahan | PP | `member_pria_id`, `member_wanita_id`, `tanggal_pernikahan` |

**Format nomor surat:** `NNN/GPdI/SA/{KODE}/{TAHUN}`

Contoh: `001/GPdI/SA/SP/2026`

---

## Kredensial Testing

| Role | ID Jemaat | Password |
|------|-----------|----------|
| Admin | `01011980` | `12345` |
| Member | `15051980` | `12345` |

### Contoh cURL

```bash
# Login sebagai admin
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"id_jemaat": "01011980", "password": "12345"}'

# Simpan token
TOKEN="1|token_dari_response_login"

# List jemaat (admin)
curl http://localhost:8000/api/admin/members \
  -H "Authorization: Bearer $TOKEN"

# Buat surat pengantar (admin)
curl -X POST http://localhost:8000/api/admin/letters \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "letter_type": "surat_pengantar",
    "member_id": 2,
    "tanggal_surat": "2026-04-30",
    "keterangan": "Untuk keperluan melamar pekerjaan"
  }'

# Download PDF surat (admin) — generate otomatis jika belum ada
curl http://localhost:8000/api/admin/letters/1/pdf \
  -H "Authorization: Bearer $TOKEN" \
  -o surat_001.pdf

# Logout
curl -X DELETE http://localhost:8000/api/me/logout \
  -H "Authorization: Bearer $TOKEN"
```

### Contoh Axios (JavaScript)

```javascript
const api = axios.create({ baseURL: 'http://localhost:8000' });

// Login
const { data } = await api.post('/api/auth/login', {
    id_jemaat: '01011980',
    password: '12345',
});
api.defaults.headers.common['Authorization'] = `Bearer ${data.data.token}`;

// List surat sendiri (member)
const letters = await api.get('/api/me/letters', { params: { keyword: 'pengantar' } });

// Buat surat (admin)
await api.post('/api/admin/letters', {
    letter_type: 'surat_keterangan_jemaat_aktif',
    member_id: 2,
    tanggal_surat: '2026-04-30',
    tahun_bergabung: 2015,
});

// Download PDF (admin) — buka di tab baru
window.open(`http://localhost:8000/api/admin/letters/1/pdf?token=${token}`);
```

---

## Catatan

- **`id_jemaat`** — Digenerate otomatis dari `tanggal_lahir` dalam format `DDMMYYYY`. Jika ada duplikat, ditambah counter (misal `01011980`, `010119801`, dst).
- **Password default** — Saat jemaat dibuat via admin, password otomatis `12345`.
- **PDF download member** — `GET /api/me/letters/{id}/download` hanya bisa jika PDF sudah pernah digenerate admin. Gunakan `has_pdf` untuk mengeceknya.
- **PDF admin** — `GET /api/admin/letters/{id}/pdf` selalu bisa; jika PDF belum ada akan digenerate on-the-fly dan disimpan.
- **CORS** — Set `FRONTEND_URL` di `.env` sesuai origin frontend. Wildcard `*` tidak kompatibel dengan `supports_credentials = true`.
- **OpenAPI Docs** — Tersedia otomatis di `/docs/api` (via Scramble).
