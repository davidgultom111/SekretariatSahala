# PERUBAHAN FITUR SURAT PENYERAHAN ANAK

**Tanggal: 15 April 2026**

## RINGKASAN PERUBAHAN

Fitur Surat Penyerahan Anak telah dimodifikasi dengan menambahkan form khusus untuk data orang tua dan anak:

### 1. FORM FIELDS (create.blade.php)

**DIHAPUS:**

- ❌ Input field "Keterangan" untuk surat_pengajuan_penyerahan_anak

**DITAMBAHKAN:**

- ✅ **Nama Ayah** (text, required, readonly/auto-populated)
    - Otomatis terisi dari nama_lengkap jemaat yang dipilih
    - Tidak bisa diedit (readonly)
    - Validasi: String min 3 karakter
- ✅ **Nama Ibu** (text, required, readonly/auto-populated)
    - Otomatis terisi dari nama_ibu jemaat (atau nama_lengkap jika tidak ada)
    - Tidak bisa diedit (readonly)
    - Validasi: String min 3 karakter
- ✅ **Nama Anak** (text, required, user input)
    - Placeholder: "Contoh: Budi Santoso"
    - Validasi: String required
- ✅ **Tempat Lahir Anak** (text, required)
    - Placeholder: "Contoh: Palembang"
    - Validasi: String min 3 karakter
- ✅ **Tanggal Lahir Anak** (date, required)
    - Format: Date picker
    - Validasi: Date required

---

## 2. DATABASE SCHEMA (Migration)

**File:** `database/migrations/2026_04_15_070000_add_penyerahan_anak_fields_to_letters_table.php`

**Kolom Baru Ditambahkan ke Tabel `letters`:**

```
- nama_ayah (VARCHAR, nullable)
- nama_ibu (VARCHAR, nullable)
- nama_anak (VARCHAR, nullable)
- tempat_lahir_anak (VARCHAR, nullable)
- tanggal_lahir_anak (DATE, nullable)
```

**Migrasi Status:** ✅ BERHASIL (107.06ms)

---

## 3. MODEL (app/Models/Letter.php)

**Fillable Fields yang Ditambahkan:**

```php
'nama_ayah',
'nama_ibu',
'nama_anak',
'tempat_lahir_anak',
'tanggal_lahir_anak',
```

---

## 4. VALIDASI FORM (app/Http/Controllers/LetterController.php)

**Validasi untuk surat_pengajuan_penyerahan_anak:**

```php
if ($request->input('letter_type') === 'surat_pengajuan_penyerahan_anak') {
    $rules['nama_ayah'] = 'required|string|min:3';
    $rules['nama_ibu'] = 'required|string|min:3';
    $rules['nama_anak'] = 'required|string|min:1';
    $rules['tempat_lahir_anak'] = 'required|string|min:3';
    $rules['tanggal_lahir_anak'] = 'required|date';
}
```

**Data yang Disimpan:**

```php
if ($validated['letter_type'] === 'surat_pengajuan_penyerahan_anak') {
    $letterData['nama_ayah'] = $validated['nama_ayah'];
    $letterData['nama_ibu'] = $validated['nama_ibu'];
    $letterData['nama_anak'] = $validated['nama_anak'];
    $letterData['tempat_lahir_anak'] = $validated['tempat_lahir_anak'];
    $letterData['tanggal_lahir_anak'] = $validated['tanggal_lahir_anak'];
}
```

---

## 5. TEMPLATE GENERATOR (app/Services/LetterTemplateService.php)

**Fungsi: generateLetterBody() - Handling for surat_pengajuan_penyerahan_anak**

Ketika user membuat Surat Penyerahan Anak dan menyimpannya, isi surat akan otomatis di-generate dengan format:

```
Nama Ayah              : Santoso
Nama Ibu               : Dewi Lestari
Nama Anak              : Budi Santoso
Tempat/Tanggal Lahir   : Palembang / 15 Maret 2015

Orang tua dari anak tersebut ingin menyerahkan anak mereka kepada Tuhan dan
memohon doa restu dalam ibadah penyerahan anak.
```

**Fitur Khusus:**

- Semua data dinamis dari form ditampilkan otomatis saat print/PDF
- Data orang tua (ayah/ibu) diambil dari jemaat yang dipilih
- Tanggal lahir anak di-format ke format Indonesia

---

## 6. SMART FORM - AUTO-FILL PARENT NAMES (create.blade.php)

**JavaScript Implementation:**

Ketika user memilih jemaat di dropdown, nama ayah dan ibu akan otomatis terisi:

```javascript
// Event listener pada member_id dropdown
memberIdSelect.addEventListener("change", updateParentNames);

// Function updateParentNames akan:
// 1. Cari data jemaat berdasarkan ID yang dipilih
// 2. Isi field "Nama Ayah" dengan nama_lengkap jemaat
// 3. Isi field "Nama Ibu" dengan nama_ibu jemaat (atau nama_lengkap jika tidak ada)
```

**User Experience:**

- Fields "Nama Ayah" dan "Nama Ibu" adalah readonly (tidak bisa diubah manual)
- Otomatis terupdate saat member dipilih/diubah
- Jika member kosong, fields akan dikosongkan juga

---

## 7. TAMPILAN DETAIL (resources/views/letter/show.blade.php)

**Ditambahkan section untuk menampilkan data penyerahan anak:**

```blade
@if ($letter->letter_type === 'surat_pengajuan_penyerahan_anak' && $letter->nama_anak)
    <h2>👨‍👩‍👧 Data Penyerahan Anak</h2>
    - Nama Ayah: {nama_ayah}
    - Nama Ibu: {nama_ibu}
    - Nama Anak: {nama_anak}
    - Tempat Lahir: {tempat_lahir_anak}
    - Tanggal Lahir: {tanggal_lahir_anak (format Indonesia)}
@endif
```

**Styling:** Pink-themed box dengan border-2 border-pink-200

---

## 8. TAMPILAN CETAK (resources/views/letter/print.blade.php)

**Tidak perlu diubah!**

Sudah menggunakan dynamic template dari LetterTemplateService::generateLetterBody(),
sehingga otomatis menampilkan data penyerahan anak yang telah disimpan.

---

## USER JOURNEY (SURAT PENYERAHAN ANAK)

### Step 1: Pilih Jenis Surat

User masuk ke halaman Tipe Surat → Klik "Surat Penyerahan Anak"

### Step 2: Isi Form Dasar

Halaman `create` menampilkan form dengan fields:

- Pilih Jemaat (required)
- Tanggal Surat (required)

_Field "Keterangan" TIDAK ditampilkan untuk surat_pengajuan_penyerahan_anak_

### Step 3: Pilih Jemaat (Orang Tua)

User memilih jemaat dari dropdown → **Nama Ayah dan Nama Ibu otomatis terisi**

### Step 4: Isi Data Anak

- Nama Anak (required, manual input)
- Tempat Lahir Anak (required, manual input)
- Tanggal Lahir Anak (required, date picker)

### Step 5: Simpan

```
POST /letter (store method)
↓
Auto-generate nomor surat: 001/GPdI/SA/PA/2026
↓
Simpan ke database dengan semua data
↓
Redirect ke halaman detail surat
```

### Step 6: Preview Detail

Halaman `show` menampilkan:

- Informasi Surat (jenis, nomor, tanggal)
- Data Jemaat (nama, identitas, telepon)
- **👨‍👩‍👧 Data Penyerahan Anak** (pink box dengan 5 field)
- Tombol Cetak & Download PDF

### Step 7: Cetak/PDF

Print view secara otomatis menampilkan:

```
SURAT PENGAJUAN PENYERAHAN ANAK
No. 001/GPdI/SA/PA/2026

Kepada Yth. Pemimpin Ibadah terkait di tempat,

Nama               : [nama jemaat - orang tua]
No. Telepon        : [nomor]
Tempat/Tgl Lahir   : [data]
Alamat             : [data]

Nama Ayah              : Santoso
Nama Ibu               : Dewi Lestari
Nama Anak              : Budi Santoso
Tempat/Tanggal Lahir   : Palembang / 15 Maret 2015

Orang tua dari anak tersebut ingin menyerahkan anak mereka kepada Tuhan dan
memohon doa restu dalam ibadah penyerahan anak.

Palembang, 15 April 2026

Gembala Sidang
(Tamrin Gultom, S.Th.)
```

---

## FILES YANG DIUBAH

| File                                                                                    | Perubahan                                             |
| --------------------------------------------------------------------------------------- | ----------------------------------------------------- |
| `database/migrations/2026_04_15_070000_add_penyerahan_anak_fields_to_letters_table.php` | ✅ BARU - Tambah kolom                                |
| `app/Models/Letter.php`                                                                 | ✅ Update fillable fields                             |
| `app/Http/Controllers/LetterController.php`                                             | ✅ Add validation & store logic                       |
| `app/Services/LetterTemplateService.php`                                                | ✅ Add generateLetterBody() handling                  |
| `resources/views/letter/create.blade.php`                                               | ✅ Add form fields, remove keterangan, add JavaScript |
| `resources/views/letter/show.blade.php`                                                 | ✅ Add display section                                |
| `resources/views/letter/print.blade.php`                                                | ❌ Tidak diubah (auto-inline)                         |

---

## NOTES PENTING

1. **Field Rename/Clarity:**
    - Form menggunakan nama_ayah & nama_ibu, bukan orang_tua_1 & orang_tua_2
    - Lebih intuitif dan mudah dipahami

2. **Auto-Fill Smart:**
    - Nama Ayah = nama_lengkap dari jemaat
    - Nama Ibu = nama_ibu jemaat (atau nama_lengkap jika tidak ada data ibu)
    - Readonly untuk mencegah typo manual

3. **Data Anak Terpisah:**
    - Nama, tempat lahir, dan tanggal lahir anak adalah input manual
    - Tidak diambil dari database, karena anak belum tentu terdaftar

4. **Backward Compatible:**
    - Surat lain tidak terpengaruh
    - Sistem pengajuan penyerahan anak terpisah dari sistem jemaat

5. **Print Otomatis:**
    - Template sudah dynamic, semua data ditampilkan otomatis
    - Tidak perlu edit print.blade.php

---

## TROUBLESHOOTING

### Nama Ayah/Ibu Tidak Terisi

- Pastikan Browser sudah load JavaScript dengan benar
- Buka Developer Console (F12) dan check ada error
- Pastikan @json($members) berfungsi dengan benar

### Tanggal Lahir Anak Format Salah

- Sistem akan auto-format ke "dd MMMM yyyy" (Indonesia)
- Contoh: 15 Maret 2015

### Field Readonly Tidak Berfungsi

- Buka browser DevTools dan verify readonly attribute ada
- Jika ada JavaScript error, check console

---

## STATUS: ✅ SIAP DIGUNAKAN

Semua perubahan telah di-implement dan database telah di-migrate.
User dapat langsung membuat Surat Penyerahan Anak dengan field-field baru.
Nama ayah dan ibu akan otomatis terisi dari data jemaat yang dipilih.
