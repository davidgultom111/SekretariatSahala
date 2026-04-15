# PERUBAHAN FITUR SURAT NILAI SEKOLAH

**Tanggal: 15 April 2026**

## RINGKASAN PERUBAHAN

Fitur Surat Nilai Sekolah telah dimodifikasi dengan mengganti form "keterangan" menjadi form khusus untuk data nilai sekolah berikut:

### 1. FORM FIELDS (create.blade.php)

**DIHAPUS:**

- ❌ Input field "Keterangan" untuk surat_nilai_sekolah

**DITAMBAHKAN:**

- ✅ **Asal Sekolah/Tempat Belajar** (text, required, min 3 karakter)
    - Placeholder: "Contoh: SMA Negeri 1 Palembang"
    - Validasi: String min 3 karakter
- ✅ **Kelas/Tingkat** (text, required)
    - Placeholder: "Contoh: XII IPA 1"
    - Validasi: String required
- ✅ **Semester** (text, required)
    - Placeholder: "Contoh: Ganjil atau Genap"
    - Validasi: String required
- ✅ **Nilai Agama** (number, optional, default 90)
    - Range: 0-100
    - Default: 90
    - Validasi: Integer min 0 max 100

---

## 2. DATABASE SCHEMA (Migration)

**File:** `database/migrations/2026_04_15_060000_add_nilai_sekolah_fields_to_letters_table.php`

**Kolom Baru Ditambahkan ke Tabel `letters`:**

```
- asal_sekolah (VARCHAR, nullable)
- kelas (VARCHAR, nullable)
- semester (VARCHAR, nullable)
- nilai (INTEGER, default 90, nullable)
```

**Migrasi Status:** ✅ BERHASIL (188.78ms)

---

## 3. MODEL (app/Models/Letter.php)

**Fillable Fields yang Ditambahkan:**

```php
'asal_sekolah',
'kelas',
'semester',
'nilai',
```

---

## 4. VALIDASI FORM (app/Http/Controllers/LetterController.php)

**Validasi untuk surat_nilai_sekolah:**

```php
if ($request->input('letter_type') === 'surat_nilai_sekolah') {
    $rules['asal_sekolah'] = 'required|string|min:3';
    $rules['kelas'] = 'required|string|min:1';
    $rules['semester'] = 'required|string|min:1';
    $rules['nilai'] = 'nullable|integer|min:0|max:100';
}
```

**Data yang Disimpan:**

```php
if ($validated['letter_type'] === 'surat_nilai_sekolah') {
    $letterData['asal_sekolah'] = $validated['asal_sekolah'];
    $letterData['kelas'] = $validated['kelas'];
    $letterData['semester'] = $validated['semester'];
    $letterData['nilai'] = $validated['nilai'] ?? 90;  // Default 90 jika tidak diisi
}
```

---

## 5. TEMPLATE GENERATOR (app/Services/LetterTemplateService.php)

**Fungsi: generateLetterBody() - Handling for surat_nilai_sekolah**

Ketika user membuat Surat Nilai Sekolah dan menyimpannya, isi surat akan otomatis di-generate dengan format:

```
Asal Sekolah/Tempat Belajar : SMA Negeri 1 Palembang
Kelas/Tingkat              : XII IPA 1
Semester                   : Ganjil

Siswa/siswi tersebut telah mengikuti kegiatan ibadah dan pembelajaran agama
di Gereja Pantekosta Jemaat Sahabat Allah dengan nilai:

[ 90 ]
```

**Fitur Khusus:**

- Jika nilai tidak diisi saat create, otomatis default 90
- Isi surat langsung di-generate di LetterTemplateService, bukan form input
- Data dinamis dari form ditampilkan otomatis saat print/PDF

---

## 6. TAMPILAN DETAIL (resources/views/letter/show.blade.php)

**Ditambahkan section untuk menampilkan data nilai sekolah:**

```blade
@if ($letter->letter_type === 'surat_nilai_sekolah' && $letter->asal_sekolah)
    <div class="mb-8 pb-8 border-b border-gray-300">
        <h2>📚 Data Nilai Sekolah</h2>
        - Asal Sekolah: {asal_sekolah}
        - Kelas/Tingkat: {kelas}
        - Semester: {semester}
        - Nilai Agama: {nilai}
    </div>
@endif
```

**Styling:** Green-themed box dengan border-2 border-green-200

---

## 7. TAMPILAN CETAK (resources/views/letter/print.blade.php)

**Tidak perlu diubah!**

Sudah menggunakan dynamic template dari LetterTemplateService::generateLetterBody(),
sehingga otomatis menampilkan data nilai sekolah yang telah disimpan.

---

## 8. FITUR YANG TETAP SAMA

- ✅ Nomor Surat Otomatis (Format: XXX/GPdI/SA/NS/YYYY)
- ✅ Print & Export PDF
- ✅ Validasi Data Sekolah (Wajib Diisi)
- ✅ Default Nilai 90 (Jika Tidak Diisi)
- ✅ Tampilan pada Cetak Surat

---

## USER JOURNEY (SURAT NILAI SEKOLAH)

### Step 1: Pilih Jenis Surat

User masuk ke halaman Tipe Surat → Klik "Surat Nilai Sekolah"

### Step 2: Isi Form

Halaman `create` menampilkan form dengan fields:

- Pilih Jemaat (required)
- Tanggal Surat (required)
- **Asal Sekolah** (required)
- **Kelas** (required)
- **Semester** (required)
- **Nilai Agama** (optional, default 90)

_Field "Keterangan" TIDAK ditampilkan untuk surat_nilai_sekolah_

### Step 3: Simpan

```
POST /letter (store method)
↓
Auto-generate nomor surat: 001/GPdI/SA/NS/2026
↓
Simpan ke database dengan nilai default 90 jika kosong
↓
Redirect ke halaman detail surat
```

### Step 4: Preview Detail

Halaman `show` menampilkan:

- Informasi Surat (jenis, nomor, tanggal)
- Data Jemaat (nama, identitas, telepon)
- **📚 Data Nilai Sekolah** (green box dengan 4 field)
- Tombol Cetak & Download PDF

### Step 5: Cetak/PDF

Print view secara otomatis menampilkan:

```
SURAT NILAI SEKOLAH
No. 001/GPdI/SA/NS/2026

Yang bertanda tangan di bawah ini adalah Gembala Sidang...

Nama               : [nama jemaat]
No. Telepon        : [nomor]
Tempat/Tgl Lahir   : [data]
Alamat             : [data]

Asal Sekolah/Tempat Belajar : SMA Negeri 1 Palembang
Kelas/Tingkat              : XII IPA 1
Semester                   : Ganjil

Siswa/siswi tersebut telah mengikuti kegiatan ibadah...
[ 90 ]

Palembang, 15 April 2026

Gembala Sidang
(Tamrin Gultom, S.Th.)
```

---

## FILES YANG DIUBAH

| File                                                                                  | Perubahan                             |
| ------------------------------------------------------------------------------------- | ------------------------------------- |
| `database/migrations/2026_04_15_060000_add_nilai_sekolah_fields_to_letters_table.php` | ✅ BARU - Tambah kolom                |
| `app/Models/Letter.php`                                                               | ✅ Update fillable fields             |
| `app/Http/Controllers/LetterController.php`                                           | ✅ Add validation & store logic       |
| `app/Services/LetterTemplateService.php`                                              | ✅ Add generateLetterBody() handling  |
| `resources/views/letter/create.blade.php`                                             | ✅ Add form fields, remove keterangan |
| `resources/views/letter/show.blade.php`                                               | ✅ Add display section                |
| `resources/views/letter/print.blade.php`                                              | ❌ Tidak diubah (auto-inline)         |

---

## NOTES PENTING

1. **Tidak ada migrasi reset** - Data yang ada di tabel letters tetap aman
2. **Backward compatible** - Surat lain (pengantar, tugas, dll) tidak terpengaruh
3. **Default value 90** - Jika user tidak mengisi nilai, otomatis jadi 90
4. **Isi surat auto-generated** - Tidak diminta dari user, generated dari service
5. **Print langsung jalan** - Tidak perlu edit print.blade.php, semua dynamic

---

## STATUS: ✅ SIAP DIGUNAKAN

Semua perubahan telah di-implement dan database telah di-migrate.
User dapat langsung membuat Surat Nilai Sekolah dengan field-field baru.
