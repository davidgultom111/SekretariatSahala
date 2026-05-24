# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**Sekretariat Jemaat** is a church secretary management system for Gereja Pentekosta di Indonesia (GPdI). It manages congregation members, generates official church letters/documents as PDFs, and exposes public content (schedules, gallery, announcements) for the church website. The system is a **pure REST API** (no web UI) consumed by a Laravel Blade admin frontend (port 8001) and mobile apps.

## Common Commands

```bash
# Install dependencies and initialize
composer run setup

# Start PHP server + queue listener (API only — Vite optional)
php artisan serve
php artisan queue:listen --tries=1

# Start all dev servers (PHP + queue + Vite) concurrently
composer run dev

# Run tests (uses Pest PHP 3.8)
composer run test

# Run a single test (Pest --filter matches test description strings)
php artisan test --filter=TestName

# Lint/format code
./vendor/bin/pint

# Database migrations
php artisan migrate
php artisan migrate:fresh --seed
```

> **Vite/npm**: The only Blade view (`resources/views/letter/print.blade.php`) uses inline CSS — Tailwind v4 is set up via `@tailwindcss/vite` but not used in it. `npm run build` is not required for the API to function.

## Test Credentials (from `MemberSeeder`)

| Role | ID Jemaat | Password |
|------|-----------|----------|
| admin | `01011980` | `12345` |
| member | `15051980` | `12345` |

## Architecture

### Authentication

Two separate Sanctum guards, both using `Member` model (not a separate `User` model for API):

| Guard | Users | Token field | Middleware |
|-------|-------|-------------|------------|
| `sanctum` | `Member` | Bearer token | `auth:sanctum` |
| (internal) | `User` | session | `auth` (unused in API) |

Login uses `id_jemaat` (not email) + `password`. Default password on member creation is `12345`. Only members with `status_aktif = 'Aktif'` can log in. The `role` field on `Member` (`'admin'` or `'member'`) is checked by `CheckRole` middleware to gate admin routes.

### Route Structure

`routes/web.php` is empty. All endpoints are in `routes/api.php`:

**Public (no auth)**

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/auth/login` | Login → `{ member, token }` |
| GET | `/api/health` | Health check |
| GET | `/api/public/jadwal` | Jadwal pelayanan (website) |
| GET | `/api/public/galeri` | Galeri foto (website) |
| GET | `/api/public/pengumuman` | Pengumuman aktif (website) |

**Member (`auth:sanctum`)**

| Method | Endpoint | Description |
|--------|----------|-------------|
| DELETE | `/api/me/logout` | Logout (revoke token) |
| GET/PUT | `/api/me` | Own profile |
| GET | `/api/me/letters` | Own letters (`keyword` querystring) |
| GET | `/api/me/letters/{id}/download` | Download own letter PDF (stored only) |
| GET | `/api/me/pengajuan` | Own pengajuan surat |
| POST | `/api/me/pengajuan` | Submit pengajuan surat |

**Admin (`auth:sanctum` + `role:admin`)**

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET/POST | `/api/admin/members` | List / Create member |
| GET/PUT/DELETE | `/api/admin/members/{id}` | Show / Update / Delete member |
| GET/POST | `/api/admin/letters` | List / Create letter |
| GET/PUT/DELETE | `/api/admin/letters/{id}` | Show / Update / Delete letter |
| GET | `/api/admin/letters/{id}/pdf` | Download PDF (generates on-the-fly if missing) |
| GET/POST | `/api/admin/jadwal` | List / Create jadwal |
| PUT/DELETE | `/api/admin/jadwal/{id}` | Update / Delete jadwal |
| GET/POST | `/api/admin/galeri` | List / Upload foto |
| DELETE | `/api/admin/galeri/{id}` | Delete foto |
| GET/POST | `/api/admin/pengumuman` | List / Create pengumuman |
| PUT/DELETE | `/api/admin/pengumuman/{id}` | Update / Delete pengumuman |
| GET | `/api/admin/pengajuan` | List semua pengajuan |
| GET | `/api/admin/pengajuan/{id}` | Show detail pengajuan |
| PUT | `/api/admin/pengajuan/{id}/setujui` | Approve → buat Letter otomatis |
| PUT | `/api/admin/pengajuan/{id}/tolak` | Reject pengajuan |
| DELETE | `/api/admin/pengajuan/{id}` | Delete pengajuan |

### Core Models

- **Member** (`app/Models/Member.php`) — Congregation members. `HasApiTokens`, `SoftDeletes`. Fields: `id_jemaat`, `nama_lengkap`, `jenis_kelamin` (`Laki-laki`|`Perempuan`), `tanggal_lahir`, `tempat_lahir`, `alamat`, `no_telepon`, `status_aktif` (`Aktif`|`Tidak Aktif`|`Dipindahkan`), `role` (`admin`|`member`), `password`. Relations: `letters()`, `marriageLettersAsPria/Wanita()`, `pengajuans()`, `pengajuanPernikahanAsPria/Wanita()`.
- **Letter** (`app/Models/Letter.php`) — Church documents. BelongsTo `member` (primary), plus optional `memberPria`/`memberWanita` for marriage letters. Letter type stored in both `letter_type` (slug key) and `tipe_surat` (display name).
- **PengajuanSurat** (`app/Models/PengajuanSurat.php`) — Member letter requests. `status`: `'Dalam Proses'` → `'Disetujui'` (creates a Letter) or `'Ditolak'`. Has same field set as `Letter` for each type, plus `catatan` (admin notes) and nullable `letter_id` (set on approval). FK to `Member` three times: `member_id`, `member_pria_id`, `member_wanita_id`.
- **LetterNumberCounter** — Auto-increments per `letter_type` + `year`. Called inside `LetterTemplateService::generateLetterNumber()` which uses `firstOrCreate` + `increment` — not atomic; avoid concurrent writes.
- **JadwalPelayanan** (`app/Models/JadwalPelayanan.php`) — Church service schedules. Fields: `nama_kegiatan`, `kategori`, `deskripsi`, `hari`, `waktu`, `urutan`, `aktif`. Standalone — no foreign keys.
- **GaleriFoto** (`app/Models/GaleriFoto.php`) — Church photo gallery. `foto` stores the path on the `public` disk; `foto_url` is an appended accessor returning the full URL via `Storage::disk('public')->url()`. Standalone.
- **Pengumuman** (`app/Models/Pengumuman.php`) — Church announcements. Fields: `judul`, `isi`, `tanggal_mulai`, `tanggal_akhir`, `aktif`. Standalone.
- **User** — Standard Laravel model, unused by the API. `UserSeeder` seeds `admin@gereja.com` / `staff@gereja.com` but these accounts serve no function — API auth only uses `Member`.

> `app/Models/Models/Letters/` and `app/Console/Commands/SetupMultipleDatabase.php` are legacy stubs from an abandoned multi-database architecture. Ignore them.

### Letter System

Seven types managed by `app/Services/LetterTemplateService.php`. Each type has different required fields for `StoreLetterRequest`:

| `letter_type` slug | Abbrev | Extra required fields |
|--------------------|--------|-----------------------|
| `surat_tugas_pelayanan` | TP | `member_id`, `tgl_mulai_tugas`, `tgl_akhir_tugas`, `tujuan_tugas` |
| `surat_pengantar` | SP | `member_id`, `keterangan` |
| `surat_keterangan_jemaat_aktif` | KJA | `member_id`, `tahun_bergabung` |
| `surat_nilai_sekolah` | NS | `member_id`, `asal_sekolah`, `kelas`, `semester`, `nilai` |
| `surat_pengajuan_baptisan` | PB | `member_id` |
| `surat_pengajuan_penyerahan_anak` | PA | `member_id`, `nama_ayah`, `nama_ibu`, `nama_anak`, `tempat_lahir_anak`, `tanggal_lahir_anak` |
| `surat_pengajuan_pernikahan` | PP | `member_pria_id`, `member_wanita_id`, `tanggal_pernikahan` (no `member_id`) |

Letter numbers: `NNN/GPdI/SA/ABBREV/YEAR` (e.g. `001/GPdI/SA/SP/2026`). `generateLetterNumber()` increments the counter and commits; `getLetterNumberPreview()` shows the next value without committing. PDF export via DomPDF using `resources/views/letter/print.blade.php` (the only remaining Blade view). `LetterTemplateService` uses static methods — no constructor injection.

### API Layer

**Response shape** — `BaseController` helpers used by all controllers:
```json
{ "status": "success", "message": "...", "data": { ... } }   // success()/created()
{ "status": "error",   "message": "...", "errors": { ... } }  // error() / validation
```
204 No Content for `noContent()`.

Controllers under `app/Http/Controllers/API/`:
- `BaseController` — response helpers (all controllers extend this)
- `MemberAuthController` — login / logout
- `MemberApiController` — member self-service (profile, letters)
- `Member/PengajuanController` — member submits pengajuan surat
- `Admin/MemberController` — full CRUD
- `Admin/LetterController` — CRUD + PDF generation
- `Admin/PengajuanController` — review, approve, reject pengajuan
- `Admin/JadwalController` — CRUD jadwal pelayanan
- `Admin/GaleriController` — upload/delete galeri foto
- `Admin/PengumumanController` — CRUD pengumuman
- `Public/JadwalController`, `Public/GaleriController`, `Public/PengumumanController` — unauthenticated read-only endpoints for website

Resources: `app/Http/Resources/MemberResource.php`, `LetterResource.php`. Other models return plain Eloquent collections.
Form Requests: `app/Http/Requests/API/` (login, update biodata) and `Admin/` (store/update member, store letter).

### Key Business Logic

- **`MemberObserver`** (`app/Observers/MemberObserver.php`) auto-generates `id_jemaat` as `DDMMYYYY` from `tanggal_lahir` on create; appends a numeric counter on duplicate. Regenerates if `tanggal_lahir` changes on update. Registered in `AppServiceProvider`.
- **PengajuanSurat workflow**: Member submits via `POST /api/me/pengajuan`. Admin reviews and calls approve (`/setujui`) or reject (`/tolak`). On approval, `AdminPengajuanController` calls `LetterTemplateService` to create a `Letter` and sets `pengajuan_surat.letter_id` + `status = 'Disetujui'`.
- **Soft-delete cascade**: The `letters` table has a DB-level `ON DELETE CASCADE` on `member_id`, but since `Member` uses `SoftDeletes`, a normal admin delete only sets `deleted_at` and does not trigger the DB cascade. Letters belonging to a soft-deleted member are not automatically removed — handle this explicitly if needed.
- **PDF download difference**: `GET /api/me/letters/{id}/download` only serves pre-stored PDFs; `GET /api/admin/letters/{id}/pdf` generates on-the-fly via DomPDF if no stored file exists.
- **CORS**: `config/cors.php` is configured for API routes. Set `FRONTEND_URL` in `.env` to the Nuxt.js origin. Wildcard `*` is incompatible with `supports_credentials=true`. `Content-Disposition` is in `exposed_headers` so browsers can read the filename on PDF downloads.
- **Queue**: Configured with the `database` driver (jobs stored in DB, not Redis). No `app/Jobs` classes exist yet — the queue listener is started for future use.
- **Scramble** (`dedoc/scramble`) auto-generates OpenAPI docs at `/docs/api` — no manual spec maintenance needed.
- **`role:admin` alias** registered in `bootstrap/app.php` → maps to `App\Http\Middleware\CheckRole`.
- **Global exception handler** in `bootstrap/app.php` — `api/*` routes always return JSON for 401/403/404/422.
