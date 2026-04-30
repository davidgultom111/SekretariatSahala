# Panduan Pengujian API di Postman
## Sekretariat Jemaat GPdI — Sahabat Allah Palembang

---

## Daftar Isi

1. [Setup Awal](#1-setup-awal)
2. [Environment Variables](#2-environment-variables)
3. [Collection Structure](#3-collection-structure)
4. [Autentikasi](#4-autentikasi)
5. [Member — Self-Service](#5-member--self-service)
6. [Admin — Manajemen Jemaat](#6-admin--manajemen-jemaat)
7. [Admin — Manajemen Surat](#7-admin--manajemen-surat)
8. [Health Check](#8-health-check)
9. [Skenario Uji End-to-End](#9-skenario-uji-end-to-end)
10. [Tips & Troubleshooting](#10-tips--troubleshooting)

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
- `Member - Self Service`
- `Admin - Jemaat`
- `Admin - Surat`
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
├── 📁 Member - Self Service
│   ├── GET Profil Saya
│   ├── PUT Update Profil
│   ├── GET Daftar Surat Saya
│   └── GET Download PDF Surat Saya
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
│   ├── GET Download PDF Surat (Admin)
│   └── DELETE Hapus Surat
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
// Simpan token admin ke environment variable
if (pm.response.code === 200) {
    const data = pm.response.json();
    pm.environment.set("admin_token", data.data.token);
    pm.environment.set("admin_member_id", data.data.member.id);
    console.log("Admin token tersimpan:", data.data.token);
}

// Validasi response
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

**Headers:**

```
Content-Type: application/json
Accept: application/json
```

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

// Hapus token dari environment
if (pm.response.code === 204) {
    pm.environment.unset("admin_token");
    console.log("Token admin dihapus dari environment");
}
```

**Expected Response:** `204 No Content` (body kosong)

---

### 4.4 Uji Error Login

**Test: ID Jemaat salah (401)**

Body:

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

**Test: Password salah (401)**

```json
{
    "id_jemaat": "01011980",
    "password": "salah123"
}
```

**Test: Akun tidak aktif (403)**

Ubah status jemaat di DB terlebih dahulu, lalu:

```json
{
    "id_jemaat": "15051980",
    "password": "12345"
}
```

**Tests:**

```javascript
pm.test("Status code adalah 403", () => {
    pm.response.to.have.status(403);
});

pm.test("Pesan akun tidak aktif", () => {
    pm.expect(pm.response.json().message).to.include("tidak aktif");
});
```

---

## 5. Member — Self-Service

> Gunakan header `Authorization: Bearer {{member_token}}` untuk semua request di bagian ini.

### Cara set Authorization di Collection/Folder:

1. Klik folder **Member - Self Service**
2. Tab **Authorization**
3. Type: **Bearer Token**
4. Token: `{{member_token}}`

Semua request dalam folder ini akan otomatis mewarisi auth ini.

---

### 5.1 GET Profil Saya

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

**Expected Response (200):**

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

### 5.2 PUT Update Profil

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

**Test validasi — field tidak diizinkan:**

```json
{
    "role": "admin",
    "status_aktif": "Aktif",
    "tanggal_lahir": "1990-01-01"
}
```

> Field `role`, `status_aktif`, `tanggal_lahir` tidak ada di `UpdateMemberBiodataRequest`, jadi akan diabaikan (tidak menyebabkan error, tapi tidak berpengaruh).

---

### 5.3 GET Daftar Surat Saya

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

### 5.4 GET Download PDF Surat Saya

| Field | Value |
|-------|-------|
| Method | `GET` |
| URL | `{{base_url}}/api/me/letters/{{letter_id}}/download` |

> **Catatan:** Hanya bisa download jika `has_pdf = true`. PDF harus sudah digenerate oleh admin terlebih dahulu.

**Tests:**

```javascript
// Jika PDF ada
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

## 6. Admin — Manajemen Jemaat

> Gunakan header `Authorization: Bearer {{admin_token}}` untuk semua request di bagian ini.

### Cara set Authorization di Folder:

1. Klik folder **Admin - Jemaat**
2. Tab **Authorization** → Type: **Bearer Token** → `{{admin_token}}`

---

### 6.1 GET Daftar Jemaat

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

### 6.2 GET Daftar Jemaat dengan Filter

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

### 6.3 POST Tambah Jemaat

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

// Simpan ID untuk digunakan di request berikutnya
if (pm.response.code === 201) {
    const member = pm.response.json().data;
    pm.environment.set("member_id", member.id);
    pm.environment.set("member_id_jemaat", member.id_jemaat);
    console.log("Member ID tersimpan:", member.id, "| ID Jemaat:", member.id_jemaat);
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

**Tests:**

```javascript
pm.test("Status code adalah 422", () => {
    pm.response.to.have.status(422);
});

pm.test("Response punya field errors", () => {
    pm.expect(pm.response.json()).to.have.property("errors");
});

pm.test("Error nama_lengkap ada", () => {
    pm.expect(pm.response.json().errors).to.have.property("nama_lengkap");
});
```

---

### 6.4 GET Detail Jemaat

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

**Test 404 — ID tidak ada:**

Ubah URL ke `/api/admin/members/99999`

```javascript
pm.test("Status code adalah 404", () => {
    pm.response.to.have.status(404);
});
```

---

### 6.5 PUT Update Jemaat

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

**Tests:**

```javascript
pm.test("id_jemaat berubah mengikuti tanggal_lahir baru", () => {
    const member = pm.response.json().data;
    // 25 Desember 1990 → 25121990
    pm.expect(member.id_jemaat).to.match(/^25121990\d*$/);
});
```

**Test — Reset Password:**

```json
{
    "password": "newpassword123"
}
```

Verifikasi dengan mencoba login menggunakan password baru.

---

### 6.6 DELETE Hapus Jemaat

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

// Bersihkan environment variable
if (pm.response.code === 204) {
    pm.environment.unset("member_id");
}
```

**Verifikasi setelah hapus** — Akses GET detail jemaat yang sama:

```javascript
pm.test("Jemaat sudah terhapus (404)", () => {
    pm.response.to.have.status(404);
});
```

---

## 7. Admin — Manajemen Surat

> Gunakan `Authorization: Bearer {{admin_token}}` untuk semua request.
> Pastikan `member_id` sudah ada di environment (dari request Tambah Jemaat).

---

### 7.1 GET Daftar Surat

| Field | Value |
|-------|-------|
| Method | `GET` |
| URL | `{{base_url}}/api/admin/letters` |

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

### 7.2 GET Daftar Surat dengan Filter

| Field | Value |
|-------|-------|
| Method | `GET` |
| URL | `{{base_url}}/api/admin/letters` |

**Params:**

| Key | Value | Keterangan |
|-----|-------|------------|
| `search` | `Budi` | Cari nama jemaat atau nomor surat |
| `letter_type` | `surat_pengantar` | Filter tipe surat |
| `per_page` | `10` | Jumlah per halaman |

---

### 7.3 POST Buat Surat Tugas Pelayanan

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
    "tanggal_surat": "2026-04-30",
    "tgl_mulai_tugas": "2026-05-01",
    "tgl_akhir_tugas": "2026-05-07",
    "tujuan_tugas": "Pelayanan kebangunan rohani di Jl. Merdeka Palembang"
}
```

**Tests:**

```javascript
pm.test("Status code adalah 201", () => {
    pm.response.to.have.status(201);
});

pm.test("letter_type tersimpan benar", () => {
    pm.expect(pm.response.json().data.letter_type).to.eql("surat_tugas_pelayanan");
});

pm.test("Nomor surat format benar", () => {
    const nomor = pm.response.json().data.nomor_surat;
    // Format: NNN/GPdI/SA/TP/YYYY
    pm.expect(nomor).to.match(/^\d{3}\/GPdI\/SA\/TP\/\d{4}$/);
});

pm.test("has_pdf = false saat baru dibuat", () => {
    pm.expect(pm.response.json().data.has_pdf).to.be.false;
});

pm.test("Field tugas pelayanan terisi", () => {
    const data = pm.response.json().data;
    pm.expect(data.tgl_mulai_tugas).to.eql("2026-05-01");
    pm.expect(data.tgl_akhir_tugas).to.eql("2026-05-07");
    pm.expect(data.tujuan_tugas).to.be.a("string");
});

// Simpan letter_id
if (pm.response.code === 201) {
    pm.environment.set("letter_id", pm.response.json().data.id);
}
```

**Test Error — tgl_akhir sebelum tgl_mulai (422):**

```json
{
    "letter_type": "surat_tugas_pelayanan",
    "member_id": "{{member_id}}",
    "tanggal_surat": "2026-04-30",
    "tgl_mulai_tugas": "2026-05-07",
    "tgl_akhir_tugas": "2026-05-01",
    "tujuan_tugas": "Pelayanan di tempat"
}
```

```javascript
pm.test("Error tgl_akhir sebelum tgl_mulai", () => {
    pm.response.to.have.status(422);
    pm.expect(pm.response.json().errors).to.have.property("tgl_akhir_tugas");
});
```

---

### 7.4 POST Buat Surat Pengantar

| Field | Value |
|-------|-------|
| Method | `POST` |
| URL | `{{base_url}}/api/admin/letters` |
| Body Type | `raw → JSON` |

**Body:**

```json
{
    "letter_type": "surat_pengantar",
    "member_id": "{{member_id}}",
    "tanggal_surat": "2026-04-30",
    "keterangan": "Untuk keperluan melamar pekerjaan di PT. Maju Jaya Palembang"
}
```

**Tests:**

```javascript
pm.test("Status code adalah 201", () => {
    pm.response.to.have.status(201);
});

pm.test("Nomor surat format SP", () => {
    pm.expect(pm.response.json().data.nomor_surat).to.match(/^\d{3}\/GPdI\/SA\/SP\/\d{4}$/);
});

pm.test("Keterangan tersimpan", () => {
    pm.expect(pm.response.json().data.keterangan).to.include("PT. Maju Jaya");
});
```

**Test Error — keterangan terlalu pendek (422):**

```json
{
    "letter_type": "surat_pengantar",
    "member_id": "{{member_id}}",
    "tanggal_surat": "2026-04-30",
    "keterangan": "ok"
}
```

---

### 7.5 POST Buat Surat Keterangan Jemaat Aktif

**Body:**

```json
{
    "letter_type": "surat_keterangan_jemaat_aktif",
    "member_id": "{{member_id}}",
    "tanggal_surat": "2026-04-30",
    "tahun_bergabung": 2015
}
```

**Tests:**

```javascript
pm.test("Status code adalah 201", () => {
    pm.response.to.have.status(201);
});

pm.test("Nomor surat format KJA", () => {
    pm.expect(pm.response.json().data.nomor_surat).to.match(/^\d{3}\/GPdI\/SA\/KJA\/\d{4}$/);
});

pm.test("tahun_bergabung tersimpan", () => {
    pm.expect(pm.response.json().data.tahun_bergabung).to.eql(2015);
});
```

---

### 7.6 POST Buat Surat Nilai Sekolah

**Body:**

```json
{
    "letter_type": "surat_nilai_sekolah",
    "member_id": "{{member_id}}",
    "tanggal_surat": "2026-04-30",
    "asal_sekolah": "SD Negeri 10 Palembang",
    "kelas": "6A",
    "semester": "Ganjil",
    "nilai": 88
}
```

**Tests:**

```javascript
pm.test("Status code adalah 201", () => {
    pm.response.to.have.status(201);
});

pm.test("Nomor surat format NS", () => {
    pm.expect(pm.response.json().data.nomor_surat).to.match(/^\d{3}\/GPdI\/SA\/NS\/\d{4}$/);
});

pm.test("Data sekolah tersimpan", () => {
    const data = pm.response.json().data;
    pm.expect(data.asal_sekolah).to.eql("SD Negeri 10 Palembang");
    pm.expect(data.nilai).to.eql(88);
});
```

---

### 7.7 POST Buat Surat Pengajuan Baptisan

**Body:**

```json
{
    "letter_type": "surat_pengajuan_baptisan",
    "member_id": "{{member_id}}",
    "tanggal_surat": "2026-04-30"
}
```

**Tests:**

```javascript
pm.test("Status code adalah 201", () => {
    pm.response.to.have.status(201);
});

pm.test("Nomor surat format PB", () => {
    pm.expect(pm.response.json().data.nomor_surat).to.match(/^\d{3}\/GPdI\/SA\/PB\/\d{4}$/);
});
```

---

### 7.8 POST Buat Surat Penyerahan Anak

**Body:**

```json
{
    "letter_type": "surat_pengajuan_penyerahan_anak",
    "member_id": "{{member_id}}",
    "tanggal_surat": "2026-04-30",
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

pm.test("Nomor surat format PA", () => {
    pm.expect(pm.response.json().data.nomor_surat).to.match(/^\d{3}\/GPdI\/SA\/PA\/\d{4}$/);
});

pm.test("Data anak tersimpan", () => {
    const data = pm.response.json().data;
    pm.expect(data.nama_anak).to.eql("Anugerah Santoso");
    pm.expect(data.nama_ayah).to.eql("Budi Santoso");
});
```

---

### 7.9 POST Buat Surat Pengajuan Pernikahan

> Surat ini membutuhkan **dua member berbeda**: satu pria dan satu wanita. Pastikan sudah ada dua jemaat di database.

**Body:**

```json
{
    "letter_type": "surat_pengajuan_pernikahan",
    "tanggal_surat": "2026-04-30",
    "member_pria_id": 3,
    "member_wanita_id": 4,
    "tanggal_pernikahan": "2026-06-15"
}
```

> Ganti `member_pria_id` dan `member_wanita_id` dengan ID jemaat yang ada di database Anda.

**Tests:**

```javascript
pm.test("Status code adalah 201", () => {
    pm.response.to.have.status(201);
});

pm.test("Nomor surat format PP", () => {
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
    "tanggal_surat": "2026-04-30",
    "member_pria_id": 3,
    "member_wanita_id": 3,
    "tanggal_pernikahan": "2026-06-15"
}
```

```javascript
pm.test("Error pria dan wanita tidak boleh sama", () => {
    pm.response.to.have.status(422);
    pm.expect(pm.response.json().errors).to.have.property("member_wanita_id");
});
```

---

### 7.10 GET Detail Surat

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

### 7.11 GET Download PDF Surat (Admin)

| Field | Value |
|-------|-------|
| Method | `GET` |
| URL | `{{base_url}}/api/admin/letters/{{letter_id}}/pdf` |

> Endpoint ini **selalu bisa download** — jika PDF belum ada, akan digenerate otomatis dari template DomPDF.

**Tests:**

```javascript
pm.test("Status code adalah 200", () => {
    pm.response.to.have.status(200);
});

pm.test("Content-Type adalah PDF", () => {
    pm.expect(pm.response.headers.get("Content-Type")).to.include("application/pdf");
});

pm.test("Ada header Content-Disposition", () => {
    const disposition = pm.response.headers.get("Content-Disposition");
    pm.expect(disposition).to.include("attachment");
    pm.expect(disposition).to.include(".pdf");
});
```

**Cara menyimpan file PDF di Postman:**

1. Klik **Send and Download** (bukan Send biasa)
2. Pilih lokasi simpan
3. File akan tersimpan dengan nama sesuai header `Content-Disposition`

**Verifikasi PDF tersimpan di DB** — Setelah download, cek GET Detail Surat:

```javascript
pm.test("has_pdf menjadi true setelah download", () => {
    pm.expect(pm.response.json().data.has_pdf).to.be.true;
});
```

---

### 7.12 DELETE Hapus Surat

| Field | Value |
|-------|-------|
| Method | `DELETE` |
| URL | `{{base_url}}/api/admin/letters/{{letter_id}}` |

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

**Verifikasi setelah hapus:**

```javascript
pm.test("Surat sudah terhapus (404)", () => {
    pm.response.to.have.status(404);
});
```

---

## 8. Health Check

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
    "timestamp": "2026-04-30T08:00:00+00:00"
}
```

---

## 9. Skenario Uji End-to-End

### Skenario A: Siklus Penuh Surat Jemaat

Jalankan request berikut **secara berurutan**:

```
1. Login Admin                          → simpan admin_token
2. Tambah Jemaat Baru                   → simpan member_id
3. Buat Surat Pengantar                 → simpan letter_id
4. GET Detail Surat (has_pdf = false)
5. Download PDF Surat (generate otomatis)
6. GET Detail Surat (has_pdf = true)    → verifikasi PDF tersimpan
7. Login Member                         → simpan member_token
8. GET Daftar Surat Saya                → surat muncul
9. Download PDF Surat (member)          → berhasil karena PDF sudah ada
10. Logout Admin
11. Logout Member
```

### Cara menjalankan dengan Collection Runner:

1. Klik **Run Collection** (ikon ▶ di samping nama collection)
2. Pilih folder atau semua request
3. Atur **Delay** antar request: 200ms
4. Klik **Run Sekretariat GPdI API**

---

### Skenario B: Uji Keamanan Role

```
1. Login sebagai Member                 → simpan member_token
2. Coba GET /api/admin/members          → harus 403 Forbidden
3. Coba POST /api/admin/letters         → harus 403 Forbidden
4. Coba DELETE /api/admin/members/1     → harus 403 Forbidden
```

**Tests untuk endpoint admin dengan token member:**

```javascript
pm.test("Member tidak bisa akses admin endpoint (403)", () => {
    pm.response.to.have.status(403);
});
```

---

### Skenario C: Uji Tanpa Token

```
1. GET /api/me (tanpa Authorization header)     → 401
2. GET /api/admin/members (tanpa token)         → 401
3. DELETE /api/me/logout (tanpa token)          → 401
```

**Tests:**

```javascript
pm.test("Tanpa token mendapat 401", () => {
    pm.response.to.have.status(401);
});
```

---

### Skenario D: Uji Cascade Delete

```
1. Tambah Jemaat A                     → catat member_id
2. Buat Surat untuk Jemaat A
3. Download PDF surat                  → PDF tersimpan
4. GET Detail Surat (has_pdf = true)
5. DELETE Jemaat A                     → 204
6. GET Surat yang tadi                 → 404 (terhapus cascade)
```

---

## 10. Tips & Troubleshooting

### Tips Umum

**Selalu set Content-Type dan Accept header:**

Tambahkan di level Collection agar diwarisi semua request:
1. Klik nama Collection → Tab **Headers**
2. Tambahkan:

| Key | Value |
|-----|-------|
| `Content-Type` | `application/json` |
| `Accept` | `application/json` |

---

**Gunakan Pre-request Script untuk token otomatis:**

Tambahkan script ini di Collection > Pre-request Script jika ingin auto-login:

```javascript
// Auto-login jika token belum ada
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
| `422 Unprocessable Entity` | Validasi gagal | Cek field yang dikirim sesuai tabel validasi di `API_DOCUMENTATION.md` |
| `404 Not Found` | ID tidak ada di DB | Cek `{{member_id}}` atau `{{letter_id}}` di environment variables |
| `500 Internal Server Error` | Error di server | Cek log Laravel: `storage/logs/laravel.log` |
| CORS Error | Request dari browser | Tidak relevan di Postman; jika dari browser cek `FRONTEND_URL` di `.env` |
| PDF tidak bisa dibuka | File corrupt | Gunakan **Send and Download** bukan Send biasa untuk download binary |
| Token tidak tersimpan | Tests script tidak jalan | Pastikan tab **Tests** tidak kosong, cek Postman Console |

---

### Melihat Log Postman Console

1. Klik **View** → **Show Postman Console** (atau `Ctrl+Alt+C`)
2. Semua `console.log()` dari Tests script akan tampil di sini
3. Berguna untuk debug nilai variabel dan response

---

### Reset State untuk Testing Ulang

Jika ingin mulai dari awal (hapus semua state):

1. Buka **Environments** → **GPdI Local**
2. Hapus value dari: `admin_token`, `member_token`, `member_id`, `letter_id`
3. Atau jalankan script ini di Postman Console:

```javascript
pm.environment.unset("admin_token");
pm.environment.unset("member_token");
pm.environment.unset("member_id");
pm.environment.unset("letter_id");
```

---

### Urutan Request yang Disarankan (untuk testing pertama kali)

```
1.  Health Check                          ← verifikasi server hidup
2.  Login Admin                           ← dapatkan admin_token
3.  Daftar Jemaat                         ← lihat data awal
4.  Tambah Jemaat Baru                    ← dapatkan member_id
5.  Detail Jemaat                         ← verifikasi data tersimpan
6.  Buat Surat Pengantar                  ← dapatkan letter_id
7.  Detail Surat (has_pdf = false)
8.  Download PDF (admin)                  ← generate & simpan PDF
9.  Detail Surat (has_pdf = true)         ← verifikasi tersimpan
10. Login Member                          ← dapatkan member_token
11. Profil Saya                           ← verifikasi data member
12. Daftar Surat Saya                     ← lihat surat
13. Download PDF (member)                 ← download yang sudah ada
14. Update Profil                         ← edit nama/alamat/telepon
15. Logout Member
16. Update Jemaat                         ← edit via admin
17. Hapus Surat                           ← cleanup
18. Hapus Jemaat                          ← cleanup (cascade)
19. Logout Admin
```
