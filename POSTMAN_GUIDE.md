# Panduan Pengujian API di Postman
## Sekretariat Jemaat GPdI — Sahabat Allah Palembang

---

## Daftar Isi

1. [Setup Awal](#1-setup-awal)
2. [Environment Variables](#2-environment-variables)
3. [Collection Structure](#3-collection-structure)
4. [Autentikasi](#4-autentikasi)
5. [Public — Beranda](#5-public--beranda)
6. [Member — Self-Service](#6-member--self-service)
7. [Admin — Manajemen Jemaat](#7-admin--manajemen-jemaat)
8. [Admin — Manajemen Surat](#8-admin--manajemen-surat)
9. [Admin — Jadwal Pelayanan](#9-admin--jadwal-pelayanan)
10. [Admin — Galeri Foto](#10-admin--galeri-foto)
11. [Admin — Pengumuman](#11-admin--pengumuman)
12. [Admin — Pengajuan Surat](#12-admin--pengajuan-surat)
13. [Health Check](#13-health-check)
14. [Skenario Uji End-to-End](#14-skenario-uji-end-to-end)
15. [Tips & Troubleshooting](#15-tips--troubleshooting)

---

## 1. Setup Awal

### 1.1 Pastikan server berjalan

```bash
php artisan serve
# Server berjalan di http://localhost:8000
```

### 1.2 Buat Collection baru di Postman

1. Buka Postman → klik **New** → **Collection**
2. Beri nama: `Sekretariat GPdI API`
3. Tambahkan deskripsi (opsional): `REST API untuk sistem sekretariat GPdI Jemaat Sahabat Allah Palembang`
4. Klik **Create**

### 1.3 Buat Folder dalam Collection

Buat folder-folder berikut di dalam collection:
- `Auth`
- `Public - Beranda`
- `Member - Self Service`
- `Admin - Jemaat`
- `Admin - Surat`
- `Admin - Jadwal`
- `Admin - Galeri`
- `Admin - Pengumuman`
- `Admin - Pengajuan`
- `Health`

---

## 2. Environment Variables

Buat dua environment: **Local** dan **Production**.

### Cara membuat Environment:

1. Klik ikon **Environments** (mata + kunci) di sidebar kiri
2. Klik **+** untuk buat environment baru
3. Beri nama: `GPdI Local`

### Variabel yang harus diisi:

| Variable | Initial Value | Current Value | Keterangan |
|----------|--------------|---------------|------------|
| `base_url` | `http://localhost:8000` | `http://localhost:8000` | Base URL API |
| `admin_token` | *(kosong)* | *(kosong)* | Auto-terisi saat login admin |
| `member_token` | *(kosong)* | *(kosong)* | Auto-terisi saat login member |
| `member_id` | *(kosong)* | *(kosong)* | Auto-terisi setelah tambah jemaat |
| `letter_id` | *(kosong)* | *(kosong)* | Auto-terisi setelah buat surat |
| `jadwal_id` | *(kosong)* | *(kosong)* | Auto-terisi setelah tambah jadwal |
| `galeri_id` | *(kosong)* | *(kosong)* | Auto-terisi setelah upload foto |
| `pengumuman_id` | *(kosong)* | *(kosong)* | Auto-terisi setelah buat pengumuman |
| `pengajuan_id` | *(kosong)* | *(kosong)* | Auto-terisi setelah pengajuan surat |

### Cara mengaktifkan Environment:

Klik dropdown **No Environment** di pojok kanan atas Postman → pilih **GPdI Local**

---

## 3. Collection Structure

```
📁 Sekretariat GPdI API
├── 📁 Auth
│   ├── POST Login (Admin)
│   ├── POST Login (Member)
│   └── DELETE Logout
│
├── 📁 Public - Beranda
│   ├── GET Jadwal Pelayanan (Publik)
│   ├── GET Galeri Foto (Publik)
│   └── GET Pengumuman Aktif (Publik)
│
├── 📁 Member - Self Service
│   ├── GET Profil Saya
│   ├── PUT Update Profil
│   ├── GET Daftar Surat Saya
│   ├── GET Download PDF Surat Saya
│   ├── GET Daftar Pengajuan Saya
│   └── POST Ajukan Surat Baru
│
├── 📁 Admin - Jemaat
│   ├── GET Daftar Jemaat
│   ├── GET Daftar Jemaat (dengan Filter)
│   ├── POST Tambah Jemaat
│   ├── GET Detail Jemaat
│   ├── PUT Update Jemaat
│   └── DELETE Hapus Jemaat
│
├── 📁 Admin - Surat
│   ├── GET Daftar Surat
│   ├── GET Daftar Surat (dengan Filter)
│   ├── POST Buat Surat Tugas Pelayanan
│   ├── POST Buat Surat Pengantar
│   ├── POST Buat Surat Keterangan Jemaat Aktif
│   ├── POST Buat Surat Nilai Sekolah
│   ├── POST Buat Surat Pengajuan Baptisan
│   ├── POST Buat Surat Penyerahan Anak
│   ├── POST Buat Surat Pengajuan Pernikahan
│   ├── GET Detail Surat
│   ├── PUT Update Surat
│   ├── GET Download PDF Surat (Admin)
│   └── DELETE Hapus Surat
│
├── 📁 Admin - Jadwal
│   ├── GET Daftar Jadwal
│   ├── POST Tambah Jadwal
│   ├── PUT Update Jadwal
│   └── DELETE Hapus Jadwal
│
├── 📁 Admin - Galeri
│   ├── GET Daftar Galeri
│   ├── POST Upload Foto
│   └── DELETE Hapus Foto
│
├── 📁 Admin - Pengumuman
│   ├── GET Daftar Pengumuman
│   ├── POST Buat Pengumuman
│   ├── PUT Update Pengumuman
│   └── DELETE Hapus Pengumuman
│
├── 📁 Admin - Pengajuan
│   ├── GET Daftar Pengajuan
│   ├── GET Detail Pengajuan
│   ├── PUT Setujui Pengajuan
│   ├── PUT Tolak Pengajuan
│   └── DELETE Hapus Pengajuan
│
└── 📁 Health
    └── GET Health Check
```

---

## 4. Autentikasi

### 4.1 POST Login (Admin)

**Deskripsi:** Login sebagai admin. Token tersimpan otomatis ke variabel `admin_token`.

| Field | Value |
|-------|-------|
| Method | `POST` |
| URL | `{{base_url}}/api/auth/login` |
| Body Type | `raw → JSON` |

**Headers:**

```
Content-Type: application/json
Accept: application/json
```

**Body:**

```json
{
    "id_jemaat": "01011980",
    "password": "12345"
}
```

**Tests (Script Postman — tab Tests):**

```javascript
if (pm.response.code === 200) {
    const data = pm.response.json();
    pm.environment.set("admin_token", data.data.token);
    pm.environment.set("admin_member_id", data.data.member.id);
    console.log("Admin token tersimpan:", data.data.token);
}

pm.test("Status code adalah 200", () => {
    pm.response.to.have.status(200);
});

pm.test("Response punya field token", () => {
    const json = pm.response.json();
    pm.expect(json.data).to.have.property("token");
    pm.expect(json.data.token).to.be.a("string");
});

pm.test("Status response adalah 'success'", () => {
    pm.expect(pm.response.json().status).to.eql("success");
});

pm.test("Member memiliki role 'admin'", () => {
    pm.expect(pm.response.json().data.member.role).to.eql("admin");
});
```

**Expected Response (200):**

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
        "token": "1|AbCdEfGhIjKlMnOpQrStUvWxYz..."
    }
}
```

---

### 4.2 POST Login (Member)

| Field | Value |
|-------|-------|
| Method | `POST` |
| URL | `{{base_url}}/api/auth/login` |
| Body Type | `raw → JSON` |

**Body:**

```json
{
    "id_jemaat": "15051980",
    "password": "12345"
}
```

**Tests:**

```javascript
if (pm.response.code === 200) {
    const data = pm.response.json();
    pm.environment.set("member_token", data.data.token);
    pm.environment.set("current_member_id", data.data.member.id);
    console.log("Member token tersimpan:", data.data.token);
}

pm.test("Status code adalah 200", () => {
    pm.response.to.have.status(200);
});

pm.test("Member memiliki role 'member'", () => {
    pm.expect(pm.response.json().data.member.role).to.eql("member");
});
```

---

### 4.3 DELETE Logout

| Field | Value |
|-------|-------|
| Method | `DELETE` |
| URL | `{{base_url}}/api/me/logout` |

**Headers:**

```
Authorization: Bearer {{admin_token}}
Accept: application/json
```

**Tests:**

```javascript
pm.test("Status code adalah 204", () => {
    pm.response.to.have.status(204);
});

pm.test("Body kosong", () => {
    pm.expect(pm.response.text()).to.be.empty;
});

if (pm.response.code === 204) {
    pm.environment.unset("admin_token");
    console.log("Token admin dihapus dari environment");
}
```

**Expected Response:** `204 No Content` (body kosong)

---

### 4.4 Uji Error Login

**Test: ID Jemaat salah (401)**

```json
{
    "id_jemaat": "99999999",
    "password": "12345"
}
```

**Tests:**

```javascript
pm.test("Status code adalah 401", () => {
    pm.response.to.have.status(401);
});

pm.test("Pesan error sesuai", () => {
    pm.expect(pm.response.json().message).to.eql("ID Jemaat atau password salah");
});
```

**Test: Akun tidak aktif (403)**

Ubah status jemaat di DB terlebih dahulu, lalu:

```javascript
pm.test("Status code adalah 403", () => {
    pm.response.to.have.status(403);
});

pm.test("Pesan akun tidak aktif", () => {
    pm.expect(pm.response.json().message).to.include("tidak aktif");
});
```

---

## 5. Public — Beranda

> Endpoint ini **tidak memerlukan token**. Dapat diakses langsung oleh website/mobile tanpa login.

### Cara set Authorization di Folder:

1. Klik folder **Public - Beranda**
2. Tab **Authorization** → Type: **No Auth**

---

### 5.1 GET Jadwal Pelayanan (Publik)

**Deskripsi:** Menampilkan jadwal ibadah yang aktif, diurutkan berdasarkan field `urutan`.

| Field | Value |
|-------|-------|
| Method | `GET` |
| URL | `{{base_url}}/api/public/jadwal` |

**Tests:**

```javascript
pm.test("Status code adalah 200", () => {
    pm.response.to.have.status(200);
});

pm.test("Data adalah array", () => {
    pm.expect(pm.response.json().data).to.be.an("array");
});

pm.test("Semua jadwal berstatus aktif", () => {
    const jadwals = pm.response.json().data;
    jadwals.forEach(j => {
        pm.expect(j.aktif).to.be.true;
    });
});
```

**Expected Response (200):**

```json
{
    "status": "success",
    "data": [
        {
            "id": 1,
            "nama_kegiatan": "Ibadah Raya Minggu",
            "kategori": "Ibadah",
            "deskripsi": "Ibadah umum seluruh jemaat",
            "hari": "Minggu",
            "waktu": "09:00",
            "urutan": 1,
            "aktif": true
        }
    ]
}
```

---

### 5.2 GET Galeri Foto (Publik)

**Deskripsi:** Menampilkan semua foto galeri, diurutkan berdasarkan field `urutan`.

| Field | Value |
|-------|-------|
| Method | `GET` |
| URL | `{{base_url}}/api/public/galeri` |

**Tests:**

```javascript
pm.test("Status code adalah 200", () => {
    pm.response.to.have.status(200);
});

pm.test("Data adalah array", () => {
    pm.expect(pm.response.json().data).to.be.an("array");
});

pm.test("Setiap foto punya field foto (path)", () => {
    const galeri = pm.response.json().data;
    if (galeri.length > 0) {
        pm.expect(galeri[0]).to.have.property("foto");
        pm.expect(galeri[0]).to.have.property("judul");
    }
});
```

---

### 5.3 GET Pengumuman Aktif (Publik)

**Deskripsi:** Menampilkan pengumuman yang aktif dan masih dalam rentang tanggal berlaku (tanggal_mulai ≤ hari ini ≤ tanggal_akhir).

| Field | Value |
|-------|-------|
| Method | `GET` |
| URL | `{{base_url}}/api/public/pengumuman` |

**Tests:**

```javascript
pm.test("Status code adalah 200", () => {
    pm.response.to.have.status(200);
});

pm.test("Data adalah array", () => {
    pm.expect(pm.response.json().data).to.be.an("array");
});

pm.test("Semua pengumuman berstatus aktif", () => {
    const pengumuman = pm.response.json().data;
    pengumuman.forEach(p => {
        pm.expect(p.aktif).to.be.true;
    });
});
```

**Expected Response (200):**

```json
{
    "status": "success",
    "data": [
        {
            "id": 1,
            "judul": "Ibadah Natal 2026",
            "isi": "Ibadah Natal akan diadakan pada 25 Desember 2026...",
            "tanggal_mulai": "2026-12-01",
            "tanggal_akhir": "2026-12-31",
            "aktif": true,
            "created_at": "2026-05-01T00:00:00+00:00",
            "updated_at": "2026-05-01T00:00:00+00:00"
        }
    ]
}
```

---

## 6. Member — Self-Service

> Gunakan header `Authorization: Bearer {{member_token}}` untuk semua request di bagian ini.

### Cara set Authorization di Collection/Folder:

1. Klik folder **Member - Self Service**
2. Tab **Authorization**
3. Type: **Bearer Token**
4. Token: `{{member_token}}`

---

### 6.1 GET Profil Saya

| Field | Value |
|-------|-------|
| Method | `GET` |
| URL | `{{base_url}}/api/me` |

**Tests:**

```javascript
pm.test("Status code adalah 200", () => {
    pm.response.to.have.status(200);
});

pm.test("Data member ada", () => {
    const json = pm.response.json();
    pm.expect(json.status).to.eql("success");
    pm.expect(json.data).to.have.property("id_jemaat");
    pm.expect(json.data).to.have.property("nama_lengkap");
    pm.expect(json.data).to.have.property("role");
});

pm.test("Password tidak tampil di response", () => {
    pm.expect(pm.response.json().data).to.not.have.property("password");
});
```

---

### 6.2 PUT Update Profil

| Field | Value |
|-------|-------|
| Method | `PUT` |
| URL | `{{base_url}}/api/me` |
| Body Type | `raw → JSON` |

**Body:**

```json
{
    "nama_lengkap": "Sari Dewi Updated",
    "alamat": "Jl. Baru No. 99",
    "no_telepon": "08199988877"
}
```

**Tests:**

```javascript
pm.test("Status code adalah 200", () => {
    pm.response.to.have.status(200);
});

pm.test("Nama berhasil diupdate", () => {
    pm.expect(pm.response.json().data.nama_lengkap).to.eql("Sari Dewi Updated");
});

pm.test("Message sesuai", () => {
    pm.expect(pm.response.json().message).to.eql("Biodata berhasil diperbarui");
});
```

---

### 6.3 GET Daftar Surat Saya

| Field | Value |
|-------|-------|
| Method | `GET` |
| URL | `{{base_url}}/api/me/letters` |

**Params (opsional):**

| Key | Value | Keterangan |
|-----|-------|------------|
| `keyword` | `pengantar` | Cari berdasarkan tipe atau nomor surat |

**Tests:**

```javascript
pm.test("Status code adalah 200", () => {
    pm.response.to.have.status(200);
});

pm.test("Data adalah array", () => {
    pm.expect(pm.response.json().data).to.be.an("array");
});

pm.test("Setiap surat punya has_pdf field", () => {
    const letters = pm.response.json().data;
    if (letters.length > 0) {
        pm.expect(letters[0]).to.have.property("has_pdf");
        pm.expect(letters[0].has_pdf).to.be.a("boolean");
    }
});
```

---

### 6.4 GET Download PDF Surat Saya

| Field | Value |
|-------|-------|
| Method | `GET` |
| URL | `{{base_url}}/api/me/letters/{{letter_id}}/download` |

> **Catatan:** Hanya bisa download jika `has_pdf = true`. PDF harus sudah digenerate oleh admin terlebih dahulu.

**Tests:**

```javascript
pm.test("Status 200 atau 404", () => {
    pm.expect([200, 404]).to.include(pm.response.code);
});

if (pm.response.code === 200) {
    pm.test("Content-Type adalah PDF", () => {
        pm.expect(pm.response.headers.get("Content-Type")).to.include("application/pdf");
    });
}

if (pm.response.code === 404) {
    pm.test("Pesan error sesuai", () => {
        const msg = pm.response.json().message;
        pm.expect(["Surat tidak ditemukan", "File PDF belum tersedia"]).to.include(msg);
    });
}
```

---

### 6.5 GET Daftar Pengajuan Saya

**Deskripsi:** Member melihat riwayat pengajuan surat yang pernah diajukannya beserta statusnya.

| Field | Value |
|-------|-------|
| Method | `GET` |
| URL | `{{base_url}}/api/me/pengajuan` |

**Tests:**

```javascript
pm.test("Status code adalah 200", () => {
    pm.response.to.have.status(200);
});

pm.test("Data adalah array", () => {
    pm.expect(pm.response.json().data).to.be.an("array");
});

pm.test("Setiap pengajuan punya field status", () => {
    const pengajuans = pm.response.json().data;
    if (pengajuans.length > 0) {
        pm.expect(pengajuans[0]).to.have.property("status");
        pm.expect(pengajuans[0]).to.have.property("letter_type");
        pm.expect(pengajuans[0]).to.have.property("tipe_surat");
    }
});
```

**Expected Response (200):**

```json
{
    "status": "success",
    "data": [
        {
            "id": 1,
            "letter_type": "surat_pengantar",
            "tipe_surat": "Surat Pengantar",
            "status": "Dalam Proses",
            "letter_id": null,
            "catatan": null,
            "created_at": "2026-05-01T00:00:00+00:00"
        }
    ]
}
```

> **Status pengajuan:** `Dalam Proses` | `Disetujui` | `Ditolak`
> Jika `status = Disetujui`, field `letter_id` akan berisi ID surat yang sudah dibuat.

---

### 6.6 POST Ajukan Surat Baru

**Deskripsi:** Member mengajukan permohonan surat. Admin akan mereview dan menyetujui/menolak pengajuan ini.

| Field | Value |
|-------|-------|
| Method | `POST` |
| URL | `{{base_url}}/api/me/pengajuan` |
| Body Type | `raw → JSON` |

**Contoh body untuk tiap tipe surat:**

**Surat Pengantar:**

```json
{
    "letter_type": "surat_pengantar",
    "keterangan": "Untuk keperluan melamar pekerjaan di PT. Maju Jaya Palembang"
}
```

**Surat Keterangan Jemaat Aktif:**

```json
{
    "letter_type": "surat_keterangan_jemaat_aktif",
    "tahun_bergabung": 2015
}
```

**Surat Tugas Pelayanan:**

```json
{
    "letter_type": "surat_tugas_pelayanan",
    "tgl_mulai_tugas": "2026-06-01",
    "tgl_akhir_tugas": "2026-06-07",
    "tujuan_tugas": "Pelayanan kebangunan rohani di Jl. Merdeka Palembang"
}
```

**Surat Nilai Sekolah:**

```json
{
    "letter_type": "surat_nilai_sekolah",
    "asal_sekolah": "SD Negeri 10 Palembang",
    "kelas": "6A",
    "semester": "Ganjil"
}
```

**Surat Pengajuan Baptisan:**

```json
{
    "letter_type": "surat_pengajuan_baptisan"
}
```

**Surat Penyerahan Anak:**

```json
{
    "letter_type": "surat_pengajuan_penyerahan_anak",
    "nama_ayah": "Budi Santoso",
    "nama_ibu": "Sari Dewi",
    "nama_anak": "Anugerah Santoso",
    "tempat_lahir_anak": "Palembang",
    "tanggal_lahir_anak": "2026-01-15"
}
```

**Surat Pengajuan Pernikahan:**

```json
{
    "letter_type": "surat_pengajuan_pernikahan",
    "id_jemaat_pria": "01011990",
    "id_jemaat_wanita": "15051992",
    "tanggal_pernikahan": "2026-08-17"
}
```

> Untuk surat pernikahan, gunakan `id_jemaat` (bukan `id` numerik).

**Tests:**

```javascript
pm.test("Status code adalah 201", () => {
    pm.response.to.have.status(201);
});

pm.test("Status pengajuan adalah 'Dalam Proses'", () => {
    pm.expect(pm.response.json().data.status).to.eql("Dalam Proses");
});

pm.test("Message sesuai", () => {
    pm.expect(pm.response.json().message).to.include("berhasil dikirim");
});

if (pm.response.code === 201) {
    pm.environment.set("pengajuan_id", pm.response.json().data.id);
}
```

**Tabel validasi pengajuan per tipe surat:**

| `letter_type` | Field wajib tambahan |
|---------------|----------------------|
| `surat_tugas_pelayanan` | `tgl_mulai_tugas`, `tgl_akhir_tugas`, `tujuan_tugas` |
| `surat_pengantar` | `keterangan` |
| `surat_keterangan_jemaat_aktif` | `tahun_bergabung` |
| `surat_nilai_sekolah` | `asal_sekolah`, `kelas`, `semester` |
| `surat_pengajuan_baptisan` | *(tidak ada)* |
| `surat_pengajuan_penyerahan_anak` | `nama_ayah`, `nama_ibu`, `nama_anak`, `tempat_lahir_anak`, `tanggal_lahir_anak` |
| `surat_pengajuan_pernikahan` | `id_jemaat_pria`, `id_jemaat_wanita`, `tanggal_pernikahan` |

---

## 7. Admin — Manajemen Jemaat

> Gunakan header `Authorization: Bearer {{admin_token}}` untuk semua request di bagian ini.

### Cara set Authorization di Folder:

1. Klik folder **Admin - Jemaat**
2. Tab **Authorization** → Type: **Bearer Token** → `{{admin_token}}`

---

### 7.1 GET Daftar Jemaat

| Field | Value |
|-------|-------|
| Method | `GET` |
| URL | `{{base_url}}/api/admin/members` |

**Tests:**

```javascript
pm.test("Status code adalah 200", () => {
    pm.response.to.have.status(200);
});

pm.test("Response punya pagination meta", () => {
    const json = pm.response.json();
    pm.expect(json.data).to.have.property("meta");
    pm.expect(json.data.meta).to.have.property("total");
    pm.expect(json.data.meta).to.have.property("current_page");
});

pm.test("Data adalah array", () => {
    pm.expect(pm.response.json().data.data).to.be.an("array");
});
```

---

### 7.2 GET Daftar Jemaat dengan Filter

| Field | Value |
|-------|-------|
| Method | `GET` |
| URL | `{{base_url}}/api/admin/members` |

**Params:**

| Key | Value | Keterangan |
|-----|-------|------------|
| `search` | `Tamrin` | Cari nama atau id_jemaat |
| `status` | `Aktif` | Filter: `Aktif` / `Tidak Aktif` / `Dipindahkan` |
| `per_page` | `5` | Jumlah per halaman |

**Tests:**

```javascript
pm.test("Status code adalah 200", () => {
    pm.response.to.have.status(200);
});

pm.test("Filter status bekerja", () => {
    const members = pm.response.json().data.data;
    members.forEach(m => {
        pm.expect(m.status_aktif).to.eql("Aktif");
    });
});

pm.test("Per page sesuai", () => {
    pm.expect(pm.response.json().data.meta.per_page).to.eql(5);
});
```

---

### 7.3 POST Tambah Jemaat

| Field | Value |
|-------|-------|
| Method | `POST` |
| URL | `{{base_url}}/api/admin/members` |
| Body Type | `raw → JSON` |

**Body:**

```json
{
    "nama_lengkap": "Budi Santoso",
    "jenis_kelamin": "Laki-laki",
    "tanggal_lahir": "1995-08-17",
    "tempat_lahir": "Jakarta",
    "alamat": "Jl. Merdeka No. 5, Palembang",
    "no_telepon": "081199887766",
    "status_aktif": "Aktif"
}
```

**Tests:**

```javascript
pm.test("Status code adalah 201", () => {
    pm.response.to.have.status(201);
});

pm.test("id_jemaat auto-generated dari tanggal_lahir", () => {
    const member = pm.response.json().data;
    // 17 Agustus 1995 → 17081995
    pm.expect(member.id_jemaat).to.match(/^17081995\d*$/);
});

pm.test("Password tidak tampil", () => {
    pm.expect(pm.response.json().data).to.not.have.property("password");
});

pm.test("Message sesuai", () => {
    pm.expect(pm.response.json().message).to.eql("Data jemaat berhasil ditambahkan");
});

if (pm.response.code === 201) {
    const member = pm.response.json().data;
    pm.environment.set("member_id", member.id);
    pm.environment.set("member_id_jemaat", member.id_jemaat);
    console.log("Member ID:", member.id, "| ID Jemaat:", member.id_jemaat);
}
```

**Test Kasus Error — Validasi Gagal (422):**

```json
{
    "nama_lengkap": "",
    "jenis_kelamin": "Tidak_Valid",
    "tanggal_lahir": "2099-01-01"
}
```

```javascript
pm.test("Status code adalah 422", () => {
    pm.response.to.have.status(422);
});

pm.test("Response punya field errors", () => {
    pm.expect(pm.response.json()).to.have.property("errors");
});
```

---

### 7.4 GET Detail Jemaat

| Field | Value |
|-------|-------|
| Method | `GET` |
| URL | `{{base_url}}/api/admin/members/{{member_id}}` |

**Tests:**

```javascript
pm.test("Status code adalah 200", () => {
    pm.response.to.have.status(200);
});

pm.test("ID sesuai", () => {
    const member = pm.response.json().data;
    pm.expect(member.id).to.eql(parseInt(pm.environment.get("member_id")));
});
```

---

### 7.5 PUT Update Jemaat

| Field | Value |
|-------|-------|
| Method | `PUT` |
| URL | `{{base_url}}/api/admin/members/{{member_id}}` |
| Body Type | `raw → JSON` |

**Body (partial update — semua field opsional):**

```json
{
    "nama_lengkap": "Budi Santoso Updated",
    "status_aktif": "Tidak Aktif",
    "no_telepon": "082233445566"
}
```

**Tests:**

```javascript
pm.test("Status code adalah 200", () => {
    pm.response.to.have.status(200);
});

pm.test("Data terupdate", () => {
    const member = pm.response.json().data;
    pm.expect(member.nama_lengkap).to.eql("Budi Santoso Updated");
    pm.expect(member.status_aktif).to.eql("Tidak Aktif");
});
```

**Test Khusus — Update tanggal_lahir (id_jemaat akan regenerasi):**

```json
{
    "tanggal_lahir": "1990-12-25"
}
```

```javascript
pm.test("id_jemaat berubah mengikuti tanggal_lahir baru", () => {
    // 25 Desember 1990 → 25121990
    pm.expect(pm.response.json().data.id_jemaat).to.match(/^25121990\d*$/);
});
```

---

### 7.6 DELETE Hapus Jemaat

| Field | Value |
|-------|-------|
| Method | `DELETE` |
| URL | `{{base_url}}/api/admin/members/{{member_id}}` |

**Tests:**

```javascript
pm.test("Status code adalah 204", () => {
    pm.response.to.have.status(204);
});

pm.test("Body kosong", () => {
    pm.expect(pm.response.text()).to.be.empty;
});

if (pm.response.code === 204) {
    pm.environment.unset("member_id");
}
```

---

## 8. Admin — Manajemen Surat

> Gunakan `Authorization: Bearer {{admin_token}}` untuk semua request.
> Pastikan `member_id` sudah ada di environment (dari request Tambah Jemaat).

---

### 8.1 GET Daftar Surat

| Field | Value |
|-------|-------|
| Method | `GET` |
| URL | `{{base_url}}/api/admin/letters` |

**Params opsional:**

| Key | Value | Keterangan |
|-----|-------|------------|
| `search` | `Budi` | Cari nama jemaat atau nomor surat |
| `letter_type` | `surat_pengantar` | Filter tipe surat |
| `per_page` | `10` | Jumlah per halaman (default: 15) |

**Tests:**

```javascript
pm.test("Status code adalah 200", () => {
    pm.response.to.have.status(200);
});

pm.test("Response punya pagination", () => {
    pm.expect(pm.response.json().data).to.have.property("meta");
});
```

---

### 8.2 POST Buat Surat Tugas Pelayanan

| Field | Value |
|-------|-------|
| Method | `POST` |
| URL | `{{base_url}}/api/admin/letters` |
| Body Type | `raw → JSON` |

**Body:**

```json
{
    "letter_type": "surat_tugas_pelayanan",
    "member_id": "{{member_id}}",
    "tanggal_surat": "2026-05-04",
    "tgl_mulai_tugas": "2026-05-10",
    "tgl_akhir_tugas": "2026-05-17",
    "tujuan_tugas": "Pelayanan kebangunan rohani di Jl. Merdeka Palembang"
}
```

**Tests:**

```javascript
pm.test("Status code adalah 201", () => {
    pm.response.to.have.status(201);
});

pm.test("Nomor surat format TP benar", () => {
    pm.expect(pm.response.json().data.nomor_surat).to.match(/^\d{3}\/GPdI\/SA\/TP\/\d{4}$/);
});

pm.test("has_pdf = false saat baru dibuat", () => {
    pm.expect(pm.response.json().data.has_pdf).to.be.false;
});

if (pm.response.code === 201) {
    pm.environment.set("letter_id", pm.response.json().data.id);
}
```

**Test Error — tgl_akhir sebelum tgl_mulai (422):**

```json
{
    "letter_type": "surat_tugas_pelayanan",
    "member_id": "{{member_id}}",
    "tanggal_surat": "2026-05-04",
    "tgl_mulai_tugas": "2026-05-17",
    "tgl_akhir_tugas": "2026-05-10",
    "tujuan_tugas": "Pelayanan di tempat"
}
```

```javascript
pm.test("Error tgl_akhir sebelum tgl_mulai (422)", () => {
    pm.response.to.have.status(422);
    pm.expect(pm.response.json().errors).to.have.property("tgl_akhir_tugas");
});
```

---

### 8.3 POST Buat Surat Pengantar

**Body:**

```json
{
    "letter_type": "surat_pengantar",
    "member_id": "{{member_id}}",
    "tanggal_surat": "2026-05-04",
    "keterangan": "Untuk keperluan melamar pekerjaan di PT. Maju Jaya Palembang"
}
```

**Tests:**

```javascript
pm.test("Status code adalah 201", () => {
    pm.response.to.have.status(201);
});

pm.test("Nomor surat format SP benar", () => {
    pm.expect(pm.response.json().data.nomor_surat).to.match(/^\d{3}\/GPdI\/SA\/SP\/\d{4}$/);
});

pm.test("Keterangan tersimpan", () => {
    pm.expect(pm.response.json().data.keterangan).to.include("PT. Maju Jaya");
});

if (pm.response.code === 201) {
    pm.environment.set("letter_id", pm.response.json().data.id);
}
```

---

### 8.4 POST Buat Surat Keterangan Jemaat Aktif

**Body:**

```json
{
    "letter_type": "surat_keterangan_jemaat_aktif",
    "member_id": "{{member_id}}",
    "tanggal_surat": "2026-05-04",
    "tahun_bergabung": 2015
}
```

**Tests:**

```javascript
pm.test("Status code adalah 201", () => {
    pm.response.to.have.status(201);
});

pm.test("Nomor surat format KJA benar", () => {
    pm.expect(pm.response.json().data.nomor_surat).to.match(/^\d{3}\/GPdI\/SA\/KJA\/\d{4}$/);
});

pm.test("tahun_bergabung tersimpan", () => {
    pm.expect(pm.response.json().data.tahun_bergabung).to.eql(2015);
});
```

---

### 8.5 POST Buat Surat Nilai Sekolah

**Body:**

```json
{
    "letter_type": "surat_nilai_sekolah",
    "member_id": "{{member_id}}",
    "tanggal_surat": "2026-05-04",
    "asal_sekolah": "SD Negeri 10 Palembang",
    "kelas": "6A",
    "semester": "Ganjil",
    "nilai": 88
}
```

> Field `nilai` bersifat opsional (nullable), range 0–100.

**Tests:**

```javascript
pm.test("Status code adalah 201", () => {
    pm.response.to.have.status(201);
});

pm.test("Nomor surat format NS benar", () => {
    pm.expect(pm.response.json().data.nomor_surat).to.match(/^\d{3}\/GPdI\/SA\/NS\/\d{4}$/);
});

pm.test("Data sekolah tersimpan", () => {
    const data = pm.response.json().data;
    pm.expect(data.asal_sekolah).to.eql("SD Negeri 10 Palembang");
    pm.expect(data.nilai).to.eql(88);
});
```

---

### 8.6 POST Buat Surat Pengajuan Baptisan

**Body:**

```json
{
    "letter_type": "surat_pengajuan_baptisan",
    "member_id": "{{member_id}}",
    "tanggal_surat": "2026-05-04"
}
```

**Tests:**

```javascript
pm.test("Status code adalah 201", () => {
    pm.response.to.have.status(201);
});

pm.test("Nomor surat format PB benar", () => {
    pm.expect(pm.response.json().data.nomor_surat).to.match(/^\d{3}\/GPdI\/SA\/PB\/\d{4}$/);
});
```

---

### 8.7 POST Buat Surat Penyerahan Anak

**Body:**

```json
{
    "letter_type": "surat_pengajuan_penyerahan_anak",
    "member_id": "{{member_id}}",
    "tanggal_surat": "2026-05-04",
    "nama_ayah": "Budi Santoso",
    "nama_ibu": "Sari Dewi",
    "nama_anak": "Anugerah Santoso",
    "tempat_lahir_anak": "Palembang",
    "tanggal_lahir_anak": "2026-01-15"
}
```

**Tests:**

```javascript
pm.test("Status code adalah 201", () => {
    pm.response.to.have.status(201);
});

pm.test("Nomor surat format PA benar", () => {
    pm.expect(pm.response.json().data.nomor_surat).to.match(/^\d{3}\/GPdI\/SA\/PA\/\d{4}$/);
});

pm.test("Data anak tersimpan", () => {
    const data = pm.response.json().data;
    pm.expect(data.nama_anak).to.eql("Anugerah Santoso");
    pm.expect(data.nama_ayah).to.eql("Budi Santoso");
});
```

---

### 8.8 POST Buat Surat Pengajuan Pernikahan

> Membutuhkan **dua member berbeda**: satu pria dan satu wanita. Gunakan ID numerik (`id`), bukan `id_jemaat`.

**Body:**

```json
{
    "letter_type": "surat_pengajuan_pernikahan",
    "tanggal_surat": "2026-05-04",
    "member_pria_id": 3,
    "member_wanita_id": 4,
    "tanggal_pernikahan": "2026-08-17"
}
```

> `tanggal_pernikahan` harus lebih dari atau sama dengan `tanggal_surat`.

**Tests:**

```javascript
pm.test("Status code adalah 201", () => {
    pm.response.to.have.status(201);
});

pm.test("Nomor surat format PP benar", () => {
    pm.expect(pm.response.json().data.nomor_surat).to.match(/^\d{3}\/GPdI\/SA\/PP\/\d{4}$/);
});

pm.test("member_pria dan member_wanita ada", () => {
    const data = pm.response.json().data;
    pm.expect(data.member_pria_id).to.be.a("number");
    pm.expect(data.member_wanita_id).to.be.a("number");
});
```

**Test Error — member_pria_id = member_wanita_id (422):**

```json
{
    "letter_type": "surat_pengajuan_pernikahan",
    "tanggal_surat": "2026-05-04",
    "member_pria_id": 3,
    "member_wanita_id": 3,
    "tanggal_pernikahan": "2026-08-17"
}
```

```javascript
pm.test("Error pria dan wanita tidak boleh sama (422)", () => {
    pm.response.to.have.status(422);
    pm.expect(pm.response.json().errors).to.have.property("member_wanita_id");
});
```

---

### 8.9 GET Detail Surat

| Field | Value |
|-------|-------|
| Method | `GET` |
| URL | `{{base_url}}/api/admin/letters/{{letter_id}}` |

**Tests:**

```javascript
pm.test("Status code adalah 200", () => {
    pm.response.to.have.status(200);
});

pm.test("Data surat lengkap", () => {
    const data = pm.response.json().data;
    pm.expect(data).to.have.property("nomor_surat");
    pm.expect(data).to.have.property("letter_type");
    pm.expect(data).to.have.property("member");
    pm.expect(data).to.have.property("has_pdf");
});

pm.test("Member ter-include dalam response", () => {
    const member = pm.response.json().data.member;
    pm.expect(member).to.have.property("nama_lengkap");
});
```

---

### 8.10 PUT Update Surat

**Deskripsi:** Update data surat yang sudah ada (misalnya koreksi keterangan atau tanggal).

| Field | Value |
|-------|-------|
| Method | `PUT` |
| URL | `{{base_url}}/api/admin/letters/{{letter_id}}` |
| Body Type | `raw → JSON` |

**Body (contoh koreksi keterangan):**

```json
{
    "keterangan": "Untuk keperluan melamar pekerjaan di PT. Berkah Jaya Palembang",
    "tanggal_surat": "2026-05-05"
}
```

**Tests:**

```javascript
pm.test("Status code adalah 200", () => {
    pm.response.to.have.status(200);
});

pm.test("Data terupdate", () => {
    pm.expect(pm.response.json().status).to.eql("success");
});
```

---

### 8.11 GET Download PDF Surat (Admin)

| Field | Value |
|-------|-------|
| Method | `GET` |
| URL | `{{base_url}}/api/admin/letters/{{letter_id}}/pdf` |

> Endpoint ini **selalu bisa download** — jika PDF belum ada, akan digenerate otomatis dari template DomPDF dan disimpan ke storage.

**Tests:**

```javascript
pm.test("Status code adalah 200", () => {
    pm.response.to.have.status(200);
});

pm.test("Content-Type adalah PDF", () => {
    pm.expect(pm.response.headers.get("Content-Type")).to.include("application/pdf");
});

pm.test("Ada header Content-Disposition dengan nama file", () => {
    const disposition = pm.response.headers.get("Content-Disposition");
    pm.expect(disposition).to.include("attachment");
    pm.expect(disposition).to.include(".pdf");
});
```

**Cara menyimpan file PDF di Postman:**

1. Klik **Send and Download** (bukan Send biasa)
2. Pilih lokasi simpan
3. File tersimpan dengan nama sesuai header `Content-Disposition`

**Verifikasi PDF tersimpan** — Setelah download, GET Detail Surat dan cek:

```javascript
pm.test("has_pdf menjadi true setelah download", () => {
    pm.expect(pm.response.json().data.has_pdf).to.be.true;
});
```

---

### 8.12 DELETE Hapus Surat

| Field | Value |
|-------|-------|
| Method | `DELETE` |
| URL | `{{base_url}}/api/admin/letters/{{letter_id}}` |

> File PDF di storage akan ikut dihapus secara otomatis.

**Tests:**

```javascript
pm.test("Status code adalah 204", () => {
    pm.response.to.have.status(204);
});

pm.test("Body kosong", () => {
    pm.expect(pm.response.text()).to.be.empty;
});

if (pm.response.code === 204) {
    pm.environment.unset("letter_id");
}
```

---

## 9. Admin — Jadwal Pelayanan

> Gunakan `Authorization: Bearer {{admin_token}}` untuk semua request.

**Catatan:** Endpoint publik `GET /api/public/jadwal` hanya menampilkan jadwal yang `aktif = true`. Endpoint admin menampilkan semua jadwal tanpa filter.

---

### 9.1 GET Daftar Jadwal (Admin)

| Field | Value |
|-------|-------|
| Method | `GET` |
| URL | `{{base_url}}/api/admin/jadwal` |

**Tests:**

```javascript
pm.test("Status code adalah 200", () => {
    pm.response.to.have.status(200);
});

pm.test("Data adalah array", () => {
    pm.expect(pm.response.json().data).to.be.an("array");
});
```

---

### 9.2 POST Tambah Jadwal

| Field | Value |
|-------|-------|
| Method | `POST` |
| URL | `{{base_url}}/api/admin/jadwal` |
| Body Type | `raw → JSON` |

**Body:**

```json
{
    "nama_kegiatan": "Ibadah Pemuda",
    "kategori": "Ibadah",
    "deskripsi": "Ibadah khusus untuk pemuda-pemudi jemaat",
    "hari": "Sabtu",
    "waktu": "17:00",
    "urutan": 2,
    "aktif": true
}
```

**Field validasi:**

| Field | Wajib | Tipe | Keterangan |
|-------|-------|------|------------|
| `nama_kegiatan` | Ya | string, max:100 | Nama kegiatan ibadah |
| `kategori` | Ya | string, max:50 | Kategori (contoh: Ibadah, Doa, Pelayanan) |
| `deskripsi` | Tidak | string | Deskripsi kegiatan |
| `hari` | Ya | string, max:20 | Hari kegiatan (contoh: Minggu, Sabtu) |
| `waktu` | Ya | string, max:10 | Waktu (format: HH:MM) |
| `urutan` | Tidak | integer ≥ 0 | Urutan tampil, default 0 |
| `aktif` | Tidak | boolean | Default true |

**Tests:**

```javascript
pm.test("Status code adalah 201", () => {
    pm.response.to.have.status(201);
});

pm.test("Data jadwal tersimpan", () => {
    const data = pm.response.json().data;
    pm.expect(data.nama_kegiatan).to.eql("Ibadah Pemuda");
    pm.expect(data.hari).to.eql("Sabtu");
});

if (pm.response.code === 201) {
    pm.environment.set("jadwal_id", pm.response.json().data.id);
}
```

---

### 9.3 PUT Update Jadwal

| Field | Value |
|-------|-------|
| Method | `PUT` |
| URL | `{{base_url}}/api/admin/jadwal/{{jadwal_id}}` |
| Body Type | `raw → JSON` |

**Body (semua field opsional — partial update):**

```json
{
    "waktu": "18:00",
    "aktif": false
}
```

**Tests:**

```javascript
pm.test("Status code adalah 200", () => {
    pm.response.to.have.status(200);
});

pm.test("Waktu terupdate", () => {
    pm.expect(pm.response.json().data.waktu).to.eql("18:00");
});

pm.test("Status aktif terupdate", () => {
    pm.expect(pm.response.json().data.aktif).to.be.false;
});
```

---

### 9.4 DELETE Hapus Jadwal

| Field | Value |
|-------|-------|
| Method | `DELETE` |
| URL | `{{base_url}}/api/admin/jadwal/{{jadwal_id}}` |

**Tests:**

```javascript
pm.test("Status code adalah 204", () => {
    pm.response.to.have.status(204);
});

if (pm.response.code === 204) {
    pm.environment.unset("jadwal_id");
}
```

---

## 10. Admin — Galeri Foto

> Gunakan `Authorization: Bearer {{admin_token}}` untuk semua request.

**Penting:** Upload foto menggunakan `form-data`, **bukan** `raw JSON`.

---

### 10.1 GET Daftar Galeri (Admin)

| Field | Value |
|-------|-------|
| Method | `GET` |
| URL | `{{base_url}}/api/admin/galeri` |

**Tests:**

```javascript
pm.test("Status code adalah 200", () => {
    pm.response.to.have.status(200);
});

pm.test("Data adalah array", () => {
    pm.expect(pm.response.json().data).to.be.an("array");
});
```

---

### 10.2 POST Upload Foto

| Field | Value |
|-------|-------|
| Method | `POST` |
| URL | `{{base_url}}/api/admin/galeri` |
| Body Type | `form-data` (**BUKAN raw JSON**) |

**Body (form-data):**

| Key | Value | Type |
|-----|-------|------|
| `judul` | `Kebaktian Natal 2025` | Text |
| `deskripsi` | `Dokumentasi kebaktian natal jemaat` | Text |
| `foto` | *(pilih file gambar)* | File |
| `urutan` | `1` | Text |

**Aturan file foto:**

- Format: `jpeg`, `jpg`, `png`, `webp`
- Ukuran maksimal: **5 MB** (5120 KB)

**Cara upload file di Postman:**

1. Pilih Body Type: **form-data**
2. Tambahkan key `foto`, ubah type ke **File**
3. Klik **Select Files** dan pilih gambar dari komputer

**Tests:**

```javascript
pm.test("Status code adalah 201", () => {
    pm.response.to.have.status(201);
});

pm.test("Field foto terisi (path storage)", () => {
    const data = pm.response.json().data;
    pm.expect(data).to.have.property("foto");
    pm.expect(data.foto).to.be.a("string");
    pm.expect(data.foto).to.include("galeri/");
});

pm.test("Judul tersimpan", () => {
    pm.expect(pm.response.json().data.judul).to.eql("Kebaktian Natal 2025");
});

if (pm.response.code === 201) {
    pm.environment.set("galeri_id", pm.response.json().data.id);
}
```

**Test Error — format file tidak valid (422):**

Upload file `.pdf` atau `.txt` → harus error 422.

**Test Error — file terlalu besar (422):**

Upload gambar > 5 MB → harus error 422.

---

### 10.3 DELETE Hapus Foto

| Field | Value |
|-------|-------|
| Method | `DELETE` |
| URL | `{{base_url}}/api/admin/galeri/{{galeri_id}}` |

> File foto di storage akan ikut dihapus secara otomatis.

**Tests:**

```javascript
pm.test("Status code adalah 204", () => {
    pm.response.to.have.status(204);
});

if (pm.response.code === 204) {
    pm.environment.unset("galeri_id");
}
```

---

## 11. Admin — Pengumuman

> Gunakan `Authorization: Bearer {{admin_token}}` untuk semua request.

**Catatan:** Endpoint publik `GET /api/public/pengumuman` hanya menampilkan pengumuman yang `aktif = true` dan masih dalam rentang tanggal berlaku.

---

### 11.1 GET Daftar Pengumuman (Admin)

| Field | Value |
|-------|-------|
| Method | `GET` |
| URL | `{{base_url}}/api/admin/pengumuman` |

> Admin melihat **semua** pengumuman (aktif maupun tidak aktif), diurutkan dari terbaru.

**Tests:**

```javascript
pm.test("Status code adalah 200", () => {
    pm.response.to.have.status(200);
});

pm.test("Data adalah array", () => {
    pm.expect(pm.response.json().data).to.be.an("array");
});
```

---

### 11.2 POST Buat Pengumuman

| Field | Value |
|-------|-------|
| Method | `POST` |
| URL | `{{base_url}}/api/admin/pengumuman` |
| Body Type | `raw → JSON` |

**Body:**

```json
{
    "judul": "Ibadah Natal 2026",
    "isi": "Ibadah Natal akan diadakan pada 25 Desember 2026 pukul 19:00 WIB di gedung gereja utama. Semua jemaat diundang hadir.",
    "tanggal_mulai": "2026-12-01",
    "tanggal_akhir": "2026-12-31",
    "aktif": true
}
```

**Field validasi:**

| Field | Wajib | Tipe | Keterangan |
|-------|-------|------|------------|
| `judul` | Ya | string, max:200 | Judul pengumuman |
| `isi` | Ya | string | Isi pengumuman |
| `tanggal_mulai` | Ya | date | Tanggal mulai tampil |
| `tanggal_akhir` | Tidak | date ≥ tanggal_mulai | Tanggal berakhir (null = tidak berakhir) |
| `aktif` | Tidak | boolean | Default true |

**Tests:**

```javascript
pm.test("Status code adalah 201", () => {
    pm.response.to.have.status(201);
});

pm.test("Judul tersimpan", () => {
    pm.expect(pm.response.json().data.judul).to.eql("Ibadah Natal 2026");
});

if (pm.response.code === 201) {
    pm.environment.set("pengumuman_id", pm.response.json().data.id);
}
```

**Test Error — tanggal_akhir sebelum tanggal_mulai (422):**

```json
{
    "judul": "Test",
    "isi": "Test pengumuman",
    "tanggal_mulai": "2026-12-31",
    "tanggal_akhir": "2026-12-01"
}
```

```javascript
pm.test("Error tanggal_akhir sebelum tanggal_mulai (422)", () => {
    pm.response.to.have.status(422);
    pm.expect(pm.response.json().errors).to.have.property("tanggal_akhir");
});
```

---

### 11.3 PUT Update Pengumuman

| Field | Value |
|-------|-------|
| Method | `PUT` |
| URL | `{{base_url}}/api/admin/pengumuman/{{pengumuman_id}}` |
| Body Type | `raw → JSON` |

**Body (partial update):**

```json
{
    "aktif": false,
    "tanggal_akhir": "2026-11-30"
}
```

**Tests:**

```javascript
pm.test("Status code adalah 200", () => {
    pm.response.to.have.status(200);
});

pm.test("Status aktif terupdate", () => {
    pm.expect(pm.response.json().data.aktif).to.be.false;
});
```

---

### 11.4 DELETE Hapus Pengumuman

| Field | Value |
|-------|-------|
| Method | `DELETE` |
| URL | `{{base_url}}/api/admin/pengumuman/{{pengumuman_id}}` |

**Tests:**

```javascript
pm.test("Status code adalah 204", () => {
    pm.response.to.have.status(204);
});

if (pm.response.code === 204) {
    pm.environment.unset("pengumuman_id");
}
```

---

## 12. Admin — Pengajuan Surat

> Gunakan `Authorization: Bearer {{admin_token}}` untuk semua request.
>
> Alur: Member mengajukan surat via `POST /api/me/pengajuan` → Admin mereview dan menyetujui/menolak → Jika disetujui, surat otomatis dibuat dan nomor surat digenerate.

---

### 12.1 GET Daftar Pengajuan

| Field | Value |
|-------|-------|
| Method | `GET` |
| URL | `{{base_url}}/api/admin/pengajuan` |

**Params opsional:**

| Key | Value | Keterangan |
|-----|-------|------------|
| `status` | `Dalam Proses` | Filter: `Dalam Proses` / `Disetujui` / `Ditolak` |

**Tests:**

```javascript
pm.test("Status code adalah 200", () => {
    pm.response.to.have.status(200);
});

pm.test("Data adalah array", () => {
    pm.expect(pm.response.json().data).to.be.an("array");
});

pm.test("Setiap pengajuan punya field member", () => {
    const pengajuans = pm.response.json().data;
    if (pengajuans.length > 0) {
        pm.expect(pengajuans[0]).to.have.property("member");
        pm.expect(pengajuans[0]).to.have.property("status");
        pm.expect(pengajuans[0]).to.have.property("letter_type");
    }
});
```

---

### 12.2 GET Detail Pengajuan

| Field | Value |
|-------|-------|
| Method | `GET` |
| URL | `{{base_url}}/api/admin/pengajuan/{{pengajuan_id}}` |

> Menampilkan detail pengajuan beserta data member, dan jika sudah disetujui, data surat yang dibuat.

**Tests:**

```javascript
pm.test("Status code adalah 200", () => {
    pm.response.to.have.status(200);
});

pm.test("Data lengkap (member, status, tipe surat)", () => {
    const data = pm.response.json().data;
    pm.expect(data).to.have.property("member");
    pm.expect(data).to.have.property("status");
    pm.expect(data).to.have.property("letter_type");
    pm.expect(data).to.have.property("tipe_surat");
});
```

---

### 12.3 PUT Setujui Pengajuan

**Deskripsi:** Menyetujui pengajuan surat dari member. Sistem otomatis membuat surat dengan nomor surat baru.

| Field | Value |
|-------|-------|
| Method | `PUT` |
| URL | `{{base_url}}/api/admin/pengajuan/{{pengajuan_id}}/setujui` |
| Body Type | `raw → JSON` |

**Body (opsional):**

```json
{
    "tanggal_surat": "2026-05-04"
}
```

> Jika `tanggal_surat` tidak diisi, otomatis menggunakan tanggal hari ini.

**Tests:**

```javascript
pm.test("Status code adalah 200", () => {
    pm.response.to.have.status(200);
});

pm.test("Response berisi letter_id dan nomor_surat", () => {
    const data = pm.response.json().data;
    pm.expect(data).to.have.property("letter_id");
    pm.expect(data).to.have.property("nomor_surat");
    pm.expect(data.letter_id).to.be.a("number");
});

pm.test("Nomor surat ter-generate", () => {
    const nomor = pm.response.json().data.nomor_surat;
    pm.expect(nomor).to.match(/^\d{3}\/GPdI\/SA\/\w+\/\d{4}$/);
});

pm.test("Message sesuai", () => {
    pm.expect(pm.response.json().message).to.include("disetujui");
});

if (pm.response.code === 200) {
    pm.environment.set("letter_id", pm.response.json().data.letter_id);
    console.log("Surat dibuat dengan ID:", pm.response.json().data.letter_id);
}
```

**Test Error — pengajuan sudah diproses sebelumnya (422):**

```javascript
pm.test("Error jika pengajuan sudah diproses (422)", () => {
    pm.response.to.have.status(422);
    pm.expect(pm.response.json().message).to.include("sudah diproses");
});
```

---

### 12.4 PUT Tolak Pengajuan

**Deskripsi:** Menolak pengajuan surat dari member. Bisa disertai catatan alasan penolakan.

| Field | Value |
|-------|-------|
| Method | `PUT` |
| URL | `{{base_url}}/api/admin/pengajuan/{{pengajuan_id}}/tolak` |
| Body Type | `raw → JSON` |

**Body (opsional):**

```json
{
    "catatan": "Data yang diberikan tidak lengkap. Mohon melengkapi dokumen pendukung."
}
```

**Tests:**

```javascript
pm.test("Status code adalah 200", () => {
    pm.response.to.have.status(200);
});

pm.test("Message sesuai", () => {
    pm.expect(pm.response.json().message).to.include("ditolak");
});
```

**Verifikasi setelah tolak** — GET Detail Pengajuan:

```javascript
pm.test("Status pengajuan menjadi 'Ditolak'", () => {
    pm.expect(pm.response.json().data.status).to.eql("Ditolak");
});

pm.test("Catatan penolakan tersimpan", () => {
    pm.expect(pm.response.json().data.catatan).to.include("tidak lengkap");
});
```

---

### 12.5 DELETE Hapus Pengajuan

| Field | Value |
|-------|-------|
| Method | `DELETE` |
| URL | `{{base_url}}/api/admin/pengajuan/{{pengajuan_id}}` |

**Tests:**

```javascript
pm.test("Status code adalah 204", () => {
    pm.response.to.have.status(204);
});

if (pm.response.code === 204) {
    pm.environment.unset("pengajuan_id");
}
```

---

## 13. Health Check

### GET Health Check

| Field | Value |
|-------|-------|
| Method | `GET` |
| URL | `{{base_url}}/api/health` |

> Tidak memerlukan autentikasi.

**Tests:**

```javascript
pm.test("Status code adalah 200", () => {
    pm.response.to.have.status(200);
});

pm.test("Status adalah 'ok'", () => {
    pm.expect(pm.response.json().status).to.eql("ok");
});

pm.test("Timestamp ada", () => {
    pm.expect(pm.response.json()).to.have.property("timestamp");
});

pm.test("Response time di bawah 500ms", () => {
    pm.expect(pm.response.responseTime).to.be.below(500);
});
```

**Expected Response:**

```json
{
    "status": "ok",
    "timestamp": "2026-05-04T08:00:00+00:00"
}
```

---

## 14. Skenario Uji End-to-End

### Skenario A: Siklus Penuh Surat Jemaat (Admin buat langsung)

```
1.  Health Check                          ← verifikasi server hidup
2.  Login Admin                           ← simpan admin_token
3.  Tambah Jemaat Baru                    ← simpan member_id
4.  Buat Surat Pengantar                  ← simpan letter_id
5.  GET Detail Surat (has_pdf = false)
6.  Download PDF Surat (admin)            ← generate & simpan PDF
7.  GET Detail Surat (has_pdf = true)     ← verifikasi tersimpan
8.  Login Member                          ← simpan member_token
9.  GET Profil Saya                       ← verifikasi data member
10. GET Daftar Surat Saya                 ← surat muncul
11. Download PDF (member)                 ← download yang sudah ada
12. Logout Member
13. Hapus Surat                           ← cleanup
14. Hapus Jemaat                          ← cleanup (cascade)
15. Logout Admin
```

### Cara menjalankan dengan Collection Runner:

1. Klik **Run Collection** (ikon ▶ di samping nama collection)
2. Pilih folder atau semua request
3. Atur **Delay** antar request: 200ms
4. Klik **Run Sekretariat GPdI API**

---

### Skenario B: Alur Pengajuan Surat (Member → Admin)

```
1.  Login Member                                    ← simpan member_token
2.  POST /api/me/pengajuan (surat_pengantar)        ← simpan pengajuan_id
3.  GET /api/me/pengajuan                           ← status = "Dalam Proses"
4.  Login Admin                                     ← simpan admin_token
5.  GET /api/admin/pengajuan?status=Dalam Proses    ← lihat pengajuan masuk
6.  GET /api/admin/pengajuan/{id}                   ← detail pengajuan
7.  PUT /api/admin/pengajuan/{id}/setujui           ← simpan letter_id
8.  GET /api/admin/letters/{letter_id}              ← surat berhasil dibuat
9.  GET /api/admin/letters/{letter_id}/pdf          ← download PDF surat
10. GET /api/me/pengajuan (member)                  ← status = "Disetujui"
11. GET /api/me/letters (member)                    ← surat muncul di daftar
```

---

### Skenario C: Uji Pengajuan Ditolak

```
1.  Login Member                          ← simpan member_token
2.  POST /api/me/pengajuan                ← kirim pengajuan
3.  Login Admin                           ← simpan admin_token
4.  PUT /api/admin/pengajuan/{id}/tolak   ← tolak dengan catatan
5.  GET /api/admin/pengajuan/{id}         ← status = "Ditolak", catatan terisi
6.  Coba setujui pengajuan yang sudah ditolak → harus 422
```

---

### Skenario D: Uji Keamanan Role

```
1. Login sebagai Member                 ← simpan member_token
2. Coba GET /api/admin/members          ← harus 403 Forbidden
3. Coba POST /api/admin/letters         ← harus 403 Forbidden
4. Coba GET /api/admin/pengajuan        ← harus 403 Forbidden
5. Coba POST /api/admin/jadwal          ← harus 403 Forbidden
```

```javascript
pm.test("Member tidak bisa akses admin endpoint (403)", () => {
    pm.response.to.have.status(403);
});
```

---

### Skenario E: Uji Tanpa Token

```
1. GET /api/me (tanpa Authorization header)       → 401
2. GET /api/admin/members (tanpa token)           → 401
3. DELETE /api/me/logout (tanpa token)            → 401
4. GET /api/public/jadwal (tanpa token)           → 200 (publik)
5. GET /api/health (tanpa token)                  → 200 (publik)
```

---

### Skenario F: Uji Manajemen Konten (Jadwal + Galeri + Pengumuman)

```
1.  Login Admin
2.  POST /api/admin/jadwal              ← tambah jadwal baru, simpan jadwal_id
3.  GET /api/public/jadwal              ← jadwal muncul di publik (aktif=true)
4.  PUT /api/admin/jadwal/{id}          ← nonaktifkan (aktif=false)
5.  GET /api/public/jadwal              ← jadwal tidak muncul lagi
6.  POST /api/admin/galeri              ← upload foto, simpan galeri_id
7.  GET /api/public/galeri              ← foto muncul
8.  DELETE /api/admin/galeri/{id}       ← hapus foto
9.  POST /api/admin/pengumuman          ← buat pengumuman, simpan pengumuman_id
10. GET /api/public/pengumuman          ← pengumuman muncul (jika dalam rentang tanggal)
11. PUT /api/admin/pengumuman/{id}      ← nonaktifkan
12. GET /api/public/pengumuman          ← pengumuman tidak muncul
13. DELETE /api/admin/jadwal/{id}       ← cleanup
14. DELETE /api/admin/pengumuman/{id}   ← cleanup
```

---

## 15. Tips & Troubleshooting

### Tips Umum

**Selalu set Content-Type dan Accept header di level Collection:**

1. Klik nama Collection → Tab **Headers**
2. Tambahkan:

| Key | Value |
|-----|-------|
| `Content-Type` | `application/json` |
| `Accept` | `application/json` |

> **Pengecualian:** Request upload foto (galeri) menggunakan `form-data` — **jangan** set `Content-Type` manual untuk request itu karena Postman akan otomatis mengatur boundary yang benar.

---

**Gunakan Pre-request Script untuk token otomatis:**

Tambahkan script ini di Collection > Pre-request Script:

```javascript
// Auto-login jika token admin belum ada
if (!pm.environment.get("admin_token")) {
    pm.sendRequest({
        url: pm.environment.get("base_url") + "/api/auth/login",
        method: "POST",
        header: { "Content-Type": "application/json" },
        body: {
            mode: "raw",
            raw: JSON.stringify({ id_jemaat: "01011980", password: "12345" })
        }
    }, (err, res) => {
        if (!err && res.code === 200) {
            pm.environment.set("admin_token", res.json().data.token);
            console.log("Auto-login admin berhasil");
        }
    });
}
```

---

### Troubleshooting

| Masalah | Kemungkinan Penyebab | Solusi |
|---------|---------------------|--------|
| `401 Unauthenticated` | Token tidak ada atau expired | Login ulang, cek `{{admin_token}}` di environment |
| `403 Forbidden` | Role bukan admin | Pastikan login dengan akun admin (`id_jemaat: 01011980`) |
| `422 Unprocessable Entity` | Validasi gagal | Cek field yang dikirim, lihat `errors` di response body |
| `404 Not Found` | ID tidak ada di DB | Cek `{{member_id}}`, `{{letter_id}}`, atau ID lainnya di environment |
| `500 Internal Server Error` | Error di server | Cek `storage/logs/laravel.log` |
| CORS Error | Request dari browser | Tidak relevan di Postman; jika dari browser cek `FRONTEND_URL` di `.env` |
| PDF tidak bisa dibuka | File corrupt saat download | Gunakan **Send and Download** bukan Send biasa untuk download binary |
| Token tidak tersimpan | Tests script tidak jalan | Pastikan tab **Tests** tidak kosong, cek Postman Console |
| Upload foto gagal 422 | Body bukan form-data | Pastikan Body Type adalah **form-data**, bukan raw JSON |
| Pengajuan 422 "sudah diproses" | Pengajuan sudah disetujui/ditolak | Buat pengajuan baru untuk uji ulang |
| `id_jemaat` tidak sesuai | MemberObserver belum terpicu | Pastikan `tanggal_lahir` diisi saat tambah jemaat |

---

### Melihat Log Postman Console

1. Klik **View** → **Show Postman Console** (atau `Ctrl+Alt+C`)
2. Semua `console.log()` dari Tests script akan tampil di sini
3. Berguna untuk debug nilai variabel dan response

---

### Reset State untuk Testing Ulang

Jika ingin mulai dari awal, jalankan script ini di Postman Console:

```javascript
pm.environment.unset("admin_token");
pm.environment.unset("member_token");
pm.environment.unset("member_id");
pm.environment.unset("member_id_jemaat");
pm.environment.unset("letter_id");
pm.environment.unset("jadwal_id");
pm.environment.unset("galeri_id");
pm.environment.unset("pengumuman_id");
pm.environment.unset("pengajuan_id");
pm.environment.unset("admin_member_id");
pm.environment.unset("current_member_id");
console.log("Semua environment variable berhasil direset");
```

---

### Ringkasan Semua Endpoint

| Method | Endpoint | Auth | Keterangan |
|--------|----------|------|------------|
| POST | `/api/auth/login` | - | Login |
| DELETE | `/api/me/logout` | member/admin | Logout |
| GET | `/api/me` | member/admin | Profil sendiri |
| PUT | `/api/me` | member/admin | Update profil |
| GET | `/api/me/letters` | member/admin | Daftar surat sendiri |
| GET | `/api/me/letters/{id}/download` | member/admin | Download PDF (tersimpan saja) |
| GET | `/api/me/pengajuan` | member/admin | Daftar pengajuan sendiri |
| POST | `/api/me/pengajuan` | member/admin | Ajukan surat baru |
| GET | `/api/public/jadwal` | - | Jadwal ibadah aktif |
| GET | `/api/public/galeri` | - | Galeri foto |
| GET | `/api/public/pengumuman` | - | Pengumuman aktif |
| GET | `/api/health` | - | Health check |
| GET | `/api/admin/members` | admin | Daftar jemaat |
| POST | `/api/admin/members` | admin | Tambah jemaat |
| GET | `/api/admin/members/{id}` | admin | Detail jemaat |
| PUT | `/api/admin/members/{id}` | admin | Update jemaat |
| DELETE | `/api/admin/members/{id}` | admin | Hapus jemaat |
| GET | `/api/admin/letters` | admin | Daftar surat |
| POST | `/api/admin/letters` | admin | Buat surat |
| GET | `/api/admin/letters/{id}` | admin | Detail surat |
| PUT | `/api/admin/letters/{id}` | admin | Update surat |
| DELETE | `/api/admin/letters/{id}` | admin | Hapus surat |
| GET | `/api/admin/letters/{id}/pdf` | admin | Download PDF (generate jika belum ada) |
| GET | `/api/admin/jadwal` | admin | Daftar jadwal |
| POST | `/api/admin/jadwal` | admin | Tambah jadwal |
| PUT | `/api/admin/jadwal/{id}` | admin | Update jadwal |
| DELETE | `/api/admin/jadwal/{id}` | admin | Hapus jadwal |
| GET | `/api/admin/galeri` | admin | Daftar galeri |
| POST | `/api/admin/galeri` | admin | Upload foto |
| DELETE | `/api/admin/galeri/{id}` | admin | Hapus foto |
| GET | `/api/admin/pengumuman` | admin | Daftar pengumuman |
| POST | `/api/admin/pengumuman` | admin | Buat pengumuman |
| PUT | `/api/admin/pengumuman/{id}` | admin | Update pengumuman |
| DELETE | `/api/admin/pengumuman/{id}` | admin | Hapus pengumuman |
| GET | `/api/admin/pengajuan` | admin | Daftar pengajuan |
| GET | `/api/admin/pengajuan/{id}` | admin | Detail pengajuan |
| PUT | `/api/admin/pengajuan/{id}/setujui` | admin | Setujui pengajuan → buat surat |
| PUT | `/api/admin/pengajuan/{id}/tolak` | admin | Tolak pengajuan |
| DELETE | `/api/admin/pengajuan/{id}` | admin | Hapus pengajuan |
