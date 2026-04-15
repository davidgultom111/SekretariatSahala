# Panduan Sistem Surat Baru

## Fitur-Fitur Baru

### 1. Tujuh Jenis Surat yang Didukung

Sistem sekarang mendukung tujuh jenis surat dengan template otomatis:

1. **Surat Tugas Pelayanan** - Untuk penugasan pelayanan jemaat
2. **Surat Pengantar** - Surat rekomendasi umum
3. **Surat Keterangan Jemaat Aktif** - Keterangan status keanggotaan
4. **Surat Nilai Sekolah** - Untuk keperluan sekolah
5. **Surat Pengajuan Baptisan** - Permohonan baptisan
6. **Surat Pengajuan Penyerahan Anak** - Permohonan penyerahan anak
7. **Surat Pengajuan Pernikahan** - Permohonan upacara pernikahan

### 2. Template Otomatis

Setiap jenis surat memiliki template standar yang otomatis diisi dengan data jemaat. Anda dapat:

- Membiarkan template standar apa adanya
- Mengedit isi surat sesuai kebutuhan

### 3. Cetak dan Unduh PDF

Setiap surat dapat:

- **Dicetak** melalui browser (Cetak/Print)
- **Diunduh** sebagai file PDF

### 4. Kop Surat Otomatis

Sistem akan menampilkan kop surat "Gereja Pentekosta di Indonesia - Jemaat Sahabat Allah" di setiap dokumen yang dicetak.

---

## Cara Penggunaan

### Membuat Surat Baru

1. Klik **"+ Buat Surat Baru"** di halaman Surat
2. Pilih jenis surat yang ingin dibuat
3. Isi form dengan informasi:
    - **Pilih Jemaat**: Nama jemaat (otomatis tersimpan di database)
    - **Nomor Surat**: Sesuai format organisasi (contoh: 001/TP/2026)
    - **Tanggal Surat**: Tanggal pembuatan surat
    - **Keterangan**: Catatan tambahan (opsional)
    - **Isi Surat**: Biarkan kosong untuk template standar, atau edit sesuai kebutuhan
4. Klik **"Simpan Surat"**

### Mencetak Surat

1. Buka detail surat yang sudah dibuat
2. Klik tombol **"Cetak"** (🖨️) untuk membuka preview cetak
3. Gunakan fitur print browser untuk mencetak ke kertas atau PDF

### Mengunduh PDF

1. Buka detail surat yang sudah dibuat
2. Klik tombol **"Download PDF"** (📄)
3. File PDF akan otomatis diunduh

### Mencari dan Filter

1. Di halaman Surat, gunakan kolom pencarian untuk:
    - **Cari Nama Jemaat**: Ketik nama jemaat
    - **Filter Jenis Surat**: Pilih jenis surat dari dropdown
2. Klik **Cari** untuk filter hasil

---

## Penambahan Kop Surat (Opsional)

Untuk menggunakan kop surat asli dari gereja:

1. Siapkan file gambar kop surat (JPG/PNG)
2. Pindahkan file ke folder: `public/images/kop-surat.jpg`
3. Sistem akan otomatis menggunakan gambar tersebut di setiap surat

Jika gambar tidak ditemukan, sistem akan menggunakan teks header standar.

---

## Catatan Teknis

- **Database**: Surat disimpan dengan template content terpisah untuk referensi
- **Format Nomor Surat**: Unik di database (tidak boleh duplikat)
- **Tanggal**: Format otomatis mengikuti system date
- **PDF**: Menggunakan DOMPDF, kompatibel dengan semua browser
- **Print**: Responsive, dapat disesuaikan dengan ukuran kertas A4

---

## Troubleshooting

**Q: Surat tidak muncul atau error?**

- Pastikan jemaat sudah terdaftar dengan status "Aktif" di data jemaat
- Periksa nomor surat tidak duplikat di database

**Q: Cetak/PDF tidak berfungsi?**

- Buka dengan browser yang compatible (Chrome, Firefox, Safari)
- Coba refresh halaman

**Q: Kop surat tidak muncul di cetak?**

- Pastikan file `kop-surat.jpg` ada di folder `public/images/`
- Periksa ukuran file tidak terlalu besar
- Gunakan format JPG atau PNG

---

## Fitur Masa Depan (Opsional)

Jika diperlukan, fitur berikut dapat ditambahkan:

- Template yang dapat dikustomisasi per jenis surat
- Tanda tangan digital
- Barcode/QR code untuk verifikasi
- Arsip surat dengan tanggal
- Email langsung dari sistem
