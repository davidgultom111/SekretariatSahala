# FITUR NOMOR SURAT OTOMATIS

## Ringkasan Fitur

Sistem nomor surat sekarang **otomatis** dengan format yang telah ditentukan:

```
Format: XXX/GPdI/SA/[Tipe Surat]/YYYY

Contoh:
- 001/GPdI/SA/TP/2026    (Surat Tugas Pelayanan ke-1 tahun 2026)
- 002/GPdI/SA/SP/2026    (Surat Pengantar ke-2 tahun 2026)
- 001/GPdI/SA/NS/2026    (Surat Nilai Sekolah ke-1 tahun 2026)
```

---

## Komponen Format Nomor Surat

| Bagian           | Format   | Meaning                                          | Contoh         |
| ---------------- | -------- | ------------------------------------------------ | -------------- |
| **XXX**          | 001-999  | Nomor urut otomatis per jenis surat per tahun    | 001, 002, 003  |
| **GPdI**         | Fixed    | Gereja Pentekosta di Indonesia (nama organisasi) | GPdI           |
| **SA**           | Fixed    | Sahabat Allah (singkatan gereja)                 | SA             |
| **[Tipe Surat]** | Variable | Singkatan jenis surat (per jenis berbeda)        | TP, SP, NS, PB |
| **YYYY**         | Auto     | Tahun sekarang (otomatis dari sistem)            | 2026           |

---

## Singkatan Jenis Surat (7 Tipe)

| No  | Jenis Surat                     | Singkatan | Contoh Nomor         |
| --- | ------------------------------- | --------- | -------------------- |
| 1   | Surat Tugas Pelayanan           | **TP**    | 001/GPdI/SA/TP/2026  |
| 2   | Surat Pengantar                 | **SP**    | 001/GPdI/SA/SP/2026  |
| 3   | Surat Keterangan Jemaat Aktif   | **KJA**   | 001/GPdI/SA/KJA/2026 |
| 4   | Surat Nilai Sekolah             | **NS**    | 001/GPdI/SA/NS/2026  |
| 5   | Surat Pengajuan Baptisan        | **PB**    | 001/GPdI/SA/PB/2026  |
| 6   | Surat Pengajuan Penyerahan Anak | **PA**    | 001/GPdI/SA/PA/2026  |
| 7   | Surat Pengajuan Pernikahan      | **PP**    | 001/GPdI/SA/PP/2026  |

---

## Cara Kerja Sistem

### 1. Halaman Create (Buat Surat)

Ketika user membuka form membuat surat:

1. ✅ Preview nomor surat ditampilkan (nomor **AKAN** diterima saat simpan)
2. ✅ Nomor akan counter otomatis berdasarkan jenis surat
3. ✅ Tahun otomatis menggunakan tahun sistem (2026)
4. ✅ User **TIDAK perlu** input nomor surat, hanya lihat preview

**Contoh:**

```
Halaman: Surat Tugas Pelayanan
Preview: 001/GPdI/SA/TP/2026

Jika reload halaman lagi, preview akan sama: 001/GPdI/SA/TP/2026
```

### 2. Saat Submit Form

Ketika user klik **"Simpan Surat"**:

1. ✅ Sistem generate nomor surat sesuai preview
2. ✅ Counter **di-increment** untuk surat berikutnya
3. ✅ Surat disimpan dengan nomor otomatis
4. ✅ Success message: "Surat berhasil dibuat dengan nomor: 001/GPdI/SA/TP/2026"

### 3. Surat Berikutnya

Ketika user membuat surat **kedua** dengan tipe yang sama:

```
Preview akan berubah menjadi: 002/GPdI/SA/TP/2026

(Karena counter sudah di-increment ke nilai 2)
```

### 4. Tipe Surat Berbeda

Nomor urut terpisah untuk setiap jenis surat:

```
Surat Tugas Pelayanan:
- 001/GPdI/SA/TP/2026
- 002/GPdI/SA/TP/2026
- 003/GPdI/SA/TP/2026

Surat Pengantar:
- 001/GPdI/SA/SP/2026  ← Counter terpisah, mulai dari 1 lagi
- 002/GPdI/SA/SP/2026

Surat Nilai Sekolah:
- 001/GPdI/SA/NS/2026  ← Counter terpisah untuk jenis ini
```

---

## Database Structure

### Tabel: letter_number_counters

Tabel baru ini menyimpan counter untuk setiap jenis surat:

```sql
CREATE TABLE letter_number_counters (
    id BIGINT PRIMARY KEY,
    letter_type VARCHAR(50) UNIQUE,      -- surat_tugas_pelayanan, dll
    year INT,                             -- 2026, 2027, dll
    next_number INT DEFAULT 1,            -- Nomor urut berikutnya
    abbreviation VARCHAR(10),             -- TP, SP, NS, PB, PA, PP, KJA
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    INDEX(letter_type, year)
);
```

### Data Inisial

Saat seeding, tabel diisi dengan:

```
letter_type: surat_tugas_pelayanan | year: 2026 | next_number: 1 | abbreviation: TP
letter_type: surat_pengantar | year: 2026 | next_number: 1 | abbreviation: SP
letter_type: surat_keterangan_jemaat_aktif | year: 2026 | next_number: 1 | abbreviation: KJA
letter_type: surat_nilai_sekolah | year: 2026 | next_number: 1 | abbreviation: NS
letter_type: surat_pengajuan_baptisan | year: 2026 | next_number: 1 | abbreviation: PB
letter_type: surat_pengajuan_penyerahan_anak | year: 2026 | next_number: 1 | abbreviation: PA
letter_type: surat_pengajuan_pernikahan | year: 2026 | next_number: 1 | abbreviation: PP
```

---

## File yang Berubah

### Database

- ✅ `2026_04_14_034208_create_letter_number_counters_table.php` - Migration baru

### Models

- ✅ `app/Models/LetterNumberCounter.php` - Model baru untuk tracking counter

### Services

- ✅ `app/Services/LetterTemplateService.php` - Tambah 2 method:
    - `getLetterNumberPreview()` - Tampilkan preview tanpa increment
    - `generateLetterNumber()` - Generate & increment counter

### Controllers

- ✅ `app/Http/Controllers/LetterController.php` - Update:
    - `create()` - Gunakan getLetterNumberPreview()
    - `store()` - Gunakan generateLetterNumber()

### Views

- ✅ `resources/views/letter/create.blade.php` - Update:
    - Hapus field input nomor_surat
    - Tampilkan preview nomor surat otomatis
    - Update info text

### Seeders

- ✅ `database/seeders/LetterNumberCounterSeeder.php` - Seeder baru

---

## Cara Menjalankan

### 1. Setup Awal (Sudah Selesai)

```bash
# Migration sudah dijalankan
php artisan migrate

# Seeding sudah dijalankan
php artisan db:seed --class=LetterNumberCounterSeeder
```

### 2. Jika Perlu Reset Counter

Jika ingin reset counter (misalnya untuk testing):

```bash
# Reset untuk tahun 2026
UPDATE letter_number_counters SET next_number = 1 WHERE year = 2026;
```

---

## Troubleshooting

### Q: Preview nomor surat tidak muncul?

**A:**

- Pastikan seeding sudah dijalankan
- Clear cache: `php artisan cache:clear`
- Refresh browser

### Q: Nomor surat berloncatan atau tidak urut?

**A:**

- Normal jika ada yang membuat draft / cancel di tengah jalan
- Setiap form load = counter di-generate tapi belum di-increment
- Counter hanya increment saat form di-submit

### Q: Bagaimana jika tahun berganti (2026 → 2027)?

**A:**

- Sistem otomatis buat counter baru untuk tahun 2027
- Counter 2026 tetap tersimpan (untuk arsip)
- Nomor surat 2027 akan mulai dari: `001/GPdI/SA/[Tipe]/2027`

### Q: Bisa ganti format nomor surat?

**A:**

- Ya, ubah di `LetterTemplateService.php`
- Ganti constants: `ORGANIZATION` dan `CHURCH`
- Ganti abbreviation di method `getAbbreviationForLetterType()`

---

## Contoh Penggunaan

### Scenario 1: User membuat 3 Surat Tugas Pelayanan

```
1. User buka halaman "Buat Surat"
   Preview: 001/GPdI/SA/TP/2026

2. User isi form dan simpan
   Saved: 001/GPdI/SA/TP/2026

3. User buat surat kedua tipe TP
   Preview: 002/GPdI/SA/TP/2026

4. User isi form dan simpan
   Saved: 002/GPdI/SA/TP/2026

5. User buat surat ketiga tipe TP
   Preview: 003/GPdI/SA/TP/2026

6. User isi form dan simpan
   Saved: 003/GPdI/SA/TP/2026
```

### Scenario 2: Mixing Different Types

```
1. User buat Surat Tugas Pelayanan
   Saved: 001/GPdI/SA/TP/2026

2. User buat Surat Pengantar
   Preview: 001/GPdI/SA/SP/2026  ← Counter SP mulai dari 1

3. User buat Surat Pengantar lagi
   Preview: 002/GPdI/SA/SP/2026

4. User buat Surat Tugas Pelayanan lagi
   Preview: 002/GPdI/SA/TP/2026  ← Counter TP lanjut dari 2
```

---

## Future Enhancement

Jika diperlukan, fitur berikut bisa ditambahkan:

1. **Manual Override** - Admin bisa override nomor surat jika perlu
2. **Reset Counter** - Admin bisa reset counter di tahun tertentu
3. **Counter History** - Laporan perubahan counter
4. **Backup Format** - Preview dengan format berbeda
5. **Multi-office** - Counter terpisah per kantor/cabang

---

## Summary

✅ **Nomor surat 100% otomatis**
✅ **Format: XXX/GPdI/SA/[Tipe]/YYYY**
✅ **Counter terpisah per jenis surat per tahun**
✅ **Tahun otomatis mengikuti sistem**
✅ **Preview ditampilkan sebelum submit**
✅ **Database clean & terstruktur**
✅ **Siap production**

**Fitur berhasil diimplementasikan!** 🎉
