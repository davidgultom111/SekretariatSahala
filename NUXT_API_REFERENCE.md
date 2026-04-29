# Referensi API untuk Nuxt.js Frontend
# GPdI Sekretariat — Laravel 12 REST API

---

## 1. Konfigurasi Awal

### Laravel `.env` (sudah diset)
```
APP_URL=http://localhost:8000
FRONTEND_URL=http://localhost:3000
```

### `nuxt.config.ts`
```ts
export default defineNuxtConfig({
  runtimeConfig: {
    public: {
      apiBase: 'http://127.0.0.1:8000/api',
    },
  },
})
```

### `composables/useApi.ts`
```ts
export const useApi = () => {
  const config = useRuntimeConfig()
  const token = useCookie('auth_token')

  const $api = $fetch.create({
    baseURL: config.public.apiBase,
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
    },
    onRequest({ options }) {
      if (token.value) {
        options.headers = {
          ...options.headers,
          Authorization: `Bearer ${token.value}`,
        }
      }
    },
  })

  return { $api }
}
```

---

## 2. TypeScript Types — `types/api.ts`

```ts
export type MemberStatus = 'Aktif' | 'Tidak Aktif' | 'Dipindahkan'
export type MemberRole   = 'admin' | 'member'
export type JenisKelamin = 'Laki-laki' | 'Perempuan'

export type LetterType =
  | 'surat_tugas_pelayanan'
  | 'surat_pengantar'
  | 'surat_keterangan_jemaat_aktif'
  | 'surat_nilai_sekolah'
  | 'surat_pengajuan_baptisan'
  | 'surat_pengajuan_penyerahan_anak'
  | 'surat_pengajuan_pernikahan'

export interface MemberResource {
  id: number
  id_jemaat: string
  nama_lengkap: string
  jenis_kelamin: JenisKelamin
  tanggal_lahir: string        // "YYYY-MM-DD"
  tempat_lahir: string
  alamat: string
  no_telepon: string
  status_aktif: MemberStatus
  role: MemberRole
  created_at: string           // ISO 8601
  updated_at: string
}

export interface LetterResource {
  id: number
  member_id: number
  member?: {
    id: number
    id_jemaat: string
    nama_lengkap: string
  }
  tipe_surat: string           // Nama tampil, e.g. "Surat Pengantar"
  letter_type: LetterType      // Key mesin, e.g. "surat_pengantar"
  nomor_surat: string          // e.g. "001/GPdI/SA/SP/2026"
  tanggal_surat: string        // "YYYY-MM-DD"
  keterangan: string | null
  has_pdf: boolean
  pdf_url: string | null
  created_at: string
  updated_at: string
}

export interface ApiSuccess<T> {
  status: 'success'
  message: string
  data: T
}

export interface ApiError {
  status: 'error'
  message: string
  errors?: Record<string, string[]>
}

export interface PaginatedData<T> {
  data: T[]
  links: {
    first: string | null
    last: string | null
    prev: string | null
    next: string | null
  }
  meta: {
    current_page: number
    last_page: number
    per_page: number
    total: number
    from: number
    to: number
  }
}
```

---

## 3. HTTP Status Code

| Code | Artinya | Yang harus dilakukan di Nuxt |
|------|---------|------------------------------|
| 200  | Sukses (read/update) | Ambil `res.data` |
| 201  | Sukses (create) | Ambil `res.data`, redirect jika perlu |
| 204  | Sukses (delete) | **Jangan `.json()`** — body kosong |
| 401  | Token tidak valid / salah login | Redirect ke `/login` |
| 403  | Role tidak cukup | Tampilkan pesan "Akses ditolak" |
| 404  | Data tidak ada | Tampilkan pesan not found |
| 422  | Validasi gagal | Tampilkan `err.data.errors` per field |
| 500  | Error server | Tampilkan pesan umum |

---

## 4. Semua Endpoint

### 🔓 Public

#### `POST /auth/login`
```ts
// Request
{ id_jemaat: string, password: string }

// Response 200
{
  status: 'success',
  data: {
    token: string,
    member: MemberResource
  }
}

// Contoh penggunaan
const token = useCookie('auth_token')
const res = await $api<ApiSuccess<{ token: string; member: MemberResource }>>('/auth/login', {
  method: 'POST',
  body: { id_jemaat, password },
})
token.value = res.data.token
```

#### `GET /health`
```ts
// Response 200
{ status: 'ok', timestamp: '2026-04-27T...' }
```

---

### 🔐 Member Self-Service (butuh token)

#### `DELETE /me/logout`
```ts
// Response 204 — body kosong!
await $api('/me/logout', { method: 'DELETE' })
token.value = null
navigateTo('/login')
```

#### `GET /me`
```ts
// Response 200
{ status: 'success', data: MemberResource }
```

#### `PUT /me`
```ts
// Request — semua field opsional, kirim yang mau diubah saja
{
  nama_lengkap?: string,  // max 255
  alamat?: string,        // max 500
  no_telepon?: string,    // max 20
}
// ⚠️ Tidak bisa ubah: jenis_kelamin, tanggal_lahir, status_aktif, role

// Response 200
{ status: 'success', data: MemberResource }
```

#### `GET /me/letters`
```ts
// Query params (opsional)
?keyword=string   // cari di tipe_surat atau nomor_surat

// Response 200 — BUKAN paginated, langsung array
{ status: 'success', data: LetterResource[] }
```

#### `GET /me/letters/{id}/download`
```ts
// Response: file PDF — BUKAN JSON, gunakan blob
const token = useCookie('auth_token')
const config = useRuntimeConfig()

const blob = await $fetch(`${config.public.apiBase}/me/letters/${id}/download`, {
  headers: { Authorization: `Bearer ${token.value}` },
  responseType: 'blob',
})
const url = URL.createObjectURL(blob)
window.open(url)
```

---

### 🔐🛡️ Admin (butuh token + role admin)

#### `GET /admin/members`
```ts
// Query params (semua opsional)
?search=string    // cari nama_lengkap atau id_jemaat
?status=string    // 'Aktif' | 'Tidak Aktif' | 'Dipindahkan'
?per_page=number  // default 15

// Response 200 — PAGINATED
{
  status: 'success',
  data: {
    data: MemberResource[],
    links: { first, last, prev, next },
    meta: { current_page, last_page, per_page, total, from, to }
  }
}

// Contoh akses
const res = await $api<ApiSuccess<PaginatedData<MemberResource>>>('/admin/members', {
  query: { page: 1, per_page: 15, search: keyword }
})
const members = res.data.data
const totalPages = res.data.meta.last_page
```

#### `POST /admin/members`
```ts
// Request — semua required
{
  nama_lengkap: string,
  jenis_kelamin: 'Laki-laki' | 'Perempuan',
  tanggal_lahir: string,  // "YYYY-MM-DD", harus sebelum hari ini
  tempat_lahir: string,
  alamat: string,
  no_telepon: string,
  status_aktif: 'Aktif' | 'Tidak Aktif' | 'Dipindahkan',
}
// ✅ id_jemaat & password ('12345') di-generate otomatis

// Response 201
{ status: 'success', data: MemberResource }
```

#### `GET /admin/members/{id}`
```ts
// Response 200
{ status: 'success', data: MemberResource }
// Response 404 jika tidak ada
```

#### `PUT /admin/members/{id}`
```ts
// Request — semua opsional
{
  nama_lengkap?: string,
  jenis_kelamin?: 'Laki-laki' | 'Perempuan',
  tanggal_lahir?: string,
  tempat_lahir?: string,
  alamat?: string,
  no_telepon?: string,
  status_aktif?: 'Aktif' | 'Tidak Aktif' | 'Dipindahkan',
  password?: string,   // min 6 karakter
}

// Response 200
{ status: 'success', data: MemberResource }
```

#### `DELETE /admin/members/{id}`
```ts
// Response 204 — body kosong, soft delete
```

---

#### `GET /admin/letters`
```ts
// Query params (semua opsional)
?search=string       // cari nama member atau nomor surat
?letter_type=string  // filter tipe surat (gunakan key mesin)
?per_page=number     // default 15

// Response 200 — PAGINATED (sama dengan members)
```

#### `POST /admin/letters`
```ts
// Field selalu wajib
{
  letter_type: LetterType,   // key mesin
  tanggal_surat: string,     // "YYYY-MM-DD"
  keterangan?: string,
}

// + Field tambahan berdasarkan letter_type:
```

| `letter_type` | Field tambahan |
|---------------|----------------|
| `surat_tugas_pelayanan` | `member_id`, `tgl_mulai_tugas`, `tgl_akhir_tugas`, `tujuan_tugas` (min 10 huruf) |
| `surat_pengantar` | `member_id`, `keterangan` **(wajib, min 10 huruf)** |
| `surat_keterangan_jemaat_aktif` | `member_id`, `tahun_bergabung` (angka, 1900–sekarang) |
| `surat_nilai_sekolah` | `member_id`, `asal_sekolah`, `kelas`, `semester`, `nilai?` (0–100) |
| `surat_pengajuan_baptisan` | `member_id` |
| `surat_pengajuan_penyerahan_anak` | `member_id`, `nama_ayah`, `nama_ibu`, `nama_anak`, `tempat_lahir_anak`, `tanggal_lahir_anak` |
| `surat_pengajuan_pernikahan` | `member_pria_id`, `member_wanita_id`, `tanggal_pernikahan` ⚠️ **tidak ada member_id** |

```ts
// Response 201
{ status: 'success', data: LetterResource }
```

#### `GET /admin/letters/{id}`
```ts
// Response 200 — include relasi member, memberPria, memberWanita
{ status: 'success', data: LetterResource }
```

#### `DELETE /admin/letters/{id}`
```ts
// Response 204 — body kosong, hapus file PDF dari storage juga
```

#### `GET /admin/letters/{id}/pdf`
```ts
// Response: file PDF — sama seperti download member, gunakan blob
const blob = await $fetch(`${config.public.apiBase}/admin/letters/${id}/pdf`, {
  headers: { Authorization: `Bearer ${token.value}` },
  responseType: 'blob',
})
const url = URL.createObjectURL(blob)
window.open(url)
```

---

## 5. Pola Error Handling

```ts
// Tangkap error validasi
try {
  await $api('/admin/members', { method: 'POST', body: form })
} catch (err: any) {
  if (err.status === 422) {
    // err.data.errors = { nama_lengkap: ["Wajib diisi"], ... }
    fieldErrors.value = err.data.errors
  } else if (err.status === 401) {
    navigateTo('/login')
  } else if (err.status === 403) {
    alert('Anda tidak punya akses')
  }
}
```

---

## 6. ⚠️ Hal yang Sering Bikin Bug

1. **`204 DELETE` — body kosong**
   Jangan `const res = await $api(...)` lalu akses `res.data` — akan error karena tidak ada body.

2. **Download PDF — butuh blob**
   Tidak bisa `<a :href="pdf_url">` karena URL butuh Authorization header.
   Selalu gunakan `$fetch(..., { responseType: 'blob' })`.

3. **Format tanggal — selalu `"YYYY-MM-DD"`**
   Jangan kirim `"27/04/2026"` atau `"April 27, 2026"`.

4. **Pagination — dua level `data`**
   Response list admin: `res.data.data[]` (bukan `res.data[]`).
   Member self-service letters: `res.data[]` (langsung array, tidak paginated).

5. **`surat_pengajuan_pernikahan` — tidak ada `member_id`**
   Gunakan `member_pria_id` dan `member_wanita_id`.

6. **Token — simpan di `useCookie()`, bukan `localStorage`**
   `localStorage` tidak ada di SSR Nuxt, akan error saat server-side render.

7. **CORS — `FRONTEND_URL` harus sesuai port Nuxt**
   Jika Nuxt jalan di port selain 3000, update `.env` Laravel.
