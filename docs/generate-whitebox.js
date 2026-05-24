'use strict';
const {
  Document, Packer, Paragraph, TextRun, Table, TableRow, TableCell,
  AlignmentType, HeadingLevel, BorderStyle, WidthType, ShadingType,
  PageBreak, PageNumber, Header, Footer, VerticalAlign
} = require('docx');
const fs = require('fs');
const path = require('path');

// ─────────────────────────────────────────────────────────────────────────────
// CONSTANTS
// ─────────────────────────────────────────────────────────────────────────────
const DXA_CM = 567;           // 1 cm ≈ 567 DXA
const PAGE_W  = 11906;        // A4 width  DXA
const PAGE_H  = 16838;        // A4 height DXA
const MAR_L   = 4 * DXA_CM;  // 4 cm left  (skripsi standard)
const MAR_R   = 3 * DXA_CM;  // 3 cm right
const MAR_T   = 3 * DXA_CM;  // 3 cm top
const MAR_B   = 3 * DXA_CM;  // 3 cm bottom
const CW      = PAGE_W - MAR_L - MAR_R;  // ≈ 7937 DXA content width

const FONT = 'Times New Roman';
const SZ   = 24;   // 12 pt in half-points
const SZ_S = 20;   // 10 pt (table body)
const SZ_H = 28;   // 14 pt heading

// ─────────────────────────────────────────────────────────────────────────────
// BORDER HELPERS
// ─────────────────────────────────────────────────────────────────────────────
const mkBord = (color, sz = 4) => ({ style: BorderStyle.SINGLE, size: sz, color });
const bAll   = (c)             => ({ top: mkBord(c), bottom: mkBord(c), left: mkBord(c), right: mkBord(c) });
const BHEAD  = bAll('2E4057');
const BCELL  = bAll('AAAAAA');

// ─────────────────────────────────────────────────────────────────────────────
// TEXT / PARAGRAPH HELPERS
// ─────────────────────────────────────────────────────────────────────────────
const R = (text, opts = {}) =>
  new TextRun({ text, font: FONT, size: SZ, ...opts });

const P = (children, opts = {}) =>
  new Paragraph({
    spacing: { after: 120 },
    children: Array.isArray(children) ? children : [children],
    ...opts,
  });

const Pc = (children, opts = {}) =>
  P(children, { alignment: AlignmentType.CENTER, ...opts });

const Pj = (children, opts = {}) =>
  P(children, { alignment: AlignmentType.JUSTIFIED, ...opts });

const space = (n = 120) => new Paragraph({ children: [], spacing: { after: n } });

// ─────────────────────────────────────────────────────────────────────────────
// CELL HELPERS
// ─────────────────────────────────────────────────────────────────────────────
const cell = (text, width, { fill='FFFFFF', color='000000', bold=false, align=AlignmentType.LEFT, span=1, isHead=false }={}) =>
  new TableCell({
    borders: isHead ? BHEAD : BCELL,
    shading:  { fill, type: ShadingType.CLEAR },
    margins:  { top: 60, bottom: 60, left: 100, right: 100 },
    width:    { size: width, type: WidthType.DXA },
    columnSpan: span,
    children: [new Paragraph({
      alignment: align,
      children: [new TextRun({ text, font: FONT, size: SZ_S, bold, color })]
    })],
  });

const TH  = (t, w, span=1) => cell(t, w, { fill:'2E4057', color:'FFFFFF', bold:true, align:AlignmentType.CENTER, span, isHead:true });
const TD  = (t, w)          => cell(t, w);
const TDc = (t, w)          => cell(t, w, { align: AlignmentType.CENTER });

const mkTable = (rows, colW) =>
  new Table({ width: { size: CW, type: WidthType.DXA }, columnWidths: colW, rows });

// ─────────────────────────────────────────────────────────────────────────────
// HEADING HELPERS
// ─────────────────────────────────────────────────────────────────────────────
const H1 = (txt) => new Paragraph({
  heading: HeadingLevel.HEADING_1,
  spacing: { before: 300, after: 160 },
  children: [new TextRun({ text: txt, font: FONT, size: 32, bold: true })]
});
const H2 = (txt) => new Paragraph({
  heading: HeadingLevel.HEADING_2,
  spacing: { before: 240, after: 120 },
  children: [new TextRun({ text: txt, font: FONT, size: SZ_H, bold: true })]
});
const H3 = (txt) => new Paragraph({
  heading: HeadingLevel.HEADING_3,
  spacing: { before: 180, after: 80 },
  children: [new TextRun({ text: txt, font: FONT, size: SZ, bold: true })]
});

const caption = (txt) => new Paragraph({
  alignment: AlignmentType.CENTER,
  spacing:   { before: 60, after: 200 },
  children:  [new TextRun({ text: txt, font: FONT, size: SZ_S, italics: true })]
});

const imgNote = (num) => new Paragraph({
  alignment: AlignmentType.CENTER,
  spacing:   { before: 160, after: 60 },
  border:    {
    top:    { style: BorderStyle.DASHED, size: 2, color: 'BBBBBB' },
    bottom: { style: BorderStyle.DASHED, size: 2, color: 'BBBBBB' },
  },
  children:  [new TextRun({
    text: `[ Gambar CFG ${num} disisipkan di sini ]`,
    font: FONT, size: SZ_S, italics: true, color: '888888'
  })]
});

// ─────────────────────────────────────────────────────────────────────────────
// CFG DATA
// ─────────────────────────────────────────────────────────────────────────────
const cfgs = [
  {
    num: 1, label: 'POST /api/auth/login', title: 'Fitur Login',
    N: 13, E: 14, Vg: 3, pred: 2,
    decNodes: 'n5, n7',
    desc: 'Fitur login memungkinkan anggota jemaat masuk ke sistem menggunakan id_jemaat dan password. Alur ini mencakup validasi sisi klien pada LoginForm.vue, pengiriman request ke server melalui komposabel useAuth.login(), serta pemrosesan autentikasi oleh MemberAuthController pada backend Laravel dengan Sanctum.',
    edges: '(n1,n2), (n2,n3), (n3,n4), (n4,n5), (n5,n6), (n5,n7), (n6,n12), (n7,n8), (n7,n9), (n8,n12), (n9,n10), (n10,n11), (n11,n13), (n12,n13)',
    nodes: [
      ['n1','START – Form submit login (user klik tombol Masuk)'],
      ['n2','Validasi input sisi klien (id_jemaat & password tidak kosong)'],
      ['n3','Kirim POST /api/auth/login ke server'],
      ['n4','Server menerima request'],
      ['n5','Cek id_jemaat terdaftar di database? [Decision]'],
      ['n6','Return 401 – Member tidak ditemukan'],
      ['n7','Cek password cocok & status_aktif = "Aktif"? [Decision]'],
      ['n8','Return 401 – Password salah atau akun tidak aktif'],
      ['n9','Buat Sanctum Bearer Token'],
      ['n10','Return 200 + { token, member }'],
      ['n11','Client simpan token, redirect ke dashboard'],
      ['n12','Client tampilkan pesan error'],
      ['n13','END'],
    ],
    paths: [
      ['P-1','1 – 2 – 3 – 4 – 5 – 6 – 12 – 13','id_jemaat tidak terdaftar di database'],
      ['P-2','1 – 2 – 3 – 4 – 5 – 7 – 8 – 12 – 13','Password salah atau status akun tidak aktif'],
      ['P-3','1 – 2 – 3 – 4 – 5 – 7 – 9 – 10 – 11 – 13','Login berhasil, token dibuat dan disimpan di client'],
    ],
    tc: [
      ['TC-1-1','P-1','id_jemaat: "99999999", password: "12345"','401 – {"status":"error","message":"Member tidak ditemukan"}'],
      ['TC-1-2','P-2','id_jemaat: "01011980", password: "salah123"','401 – {"status":"error","message":"Password salah atau akun tidak aktif"}'],
      ['TC-1-3','P-3','id_jemaat: "01011980", password: "12345" (status: Aktif)','200 – {"status":"success","data":{"token":"...","member":{...}}}'],
    ],
  },
  {
    num: 2, label: 'DELETE /api/me/logout', title: 'Fitur Logout',
    N: 11, E: 12, Vg: 3, pred: 2,
    decNodes: 'n1, n5',
    desc: 'Fitur logout menghapus token autentikasi aktif milik anggota yang sedang login. Alur mencakup pengecekan ketersediaan token di sisi klien, pengiriman request DELETE ke server, dan proses revoke token menggunakan mekanisme Sanctum. Setelah sukses, client menghapus state dan mengarahkan pengguna ke halaman login.',
    edges: '(n1,n2), (n1,n3), (n2,n11), (n3,n4), (n4,n5), (n5,n6), (n5,n7), (n6,n8), (n7,n8), (n8,n9), (n9,n10), (n10,n11)',
    nodes: [
      ['n1','START – Trigger logout [Decision: token tersedia di client?]'],
      ['n2','Token tidak ada → hapus sesi lokal langsung'],
      ['n3','Kirim DELETE /api/me/logout + Bearer token'],
      ['n4','Server menerima request (middleware auth:sanctum)'],
      ['n5','Cek token valid di server? [Decision]'],
      ['n6','Return 401 Unauthorized'],
      ['n7','Revoke token: currentAccessToken()->delete()'],
      ['n8','Return 204 No Content'],
      ['n9','Client hapus token dan data user dari state/storage'],
      ['n10','Redirect ke halaman login'],
      ['n11','END'],
    ],
    paths: [
      ['P-1','1 – 2 – 11','Tidak ada token di client, sesi lokal langsung dihapus'],
      ['P-2','1 – 3 – 4 – 5 – 6 – 8 – 9 – 10 – 11','Token tidak valid atau sudah kadaluarsa di server'],
      ['P-3','1 – 3 – 4 – 5 – 7 – 8 – 9 – 10 – 11','Logout berhasil, token direvoke, redirect ke login'],
    ],
    tc: [
      ['TC-2-1','P-1','Token: tidak ada (null/undefined di local storage)','Sesi lokal dihapus, redirect ke halaman login'],
      ['TC-2-2','P-2','Authorization: Bearer expired_or_revoked_token','401 – {"status":"error","message":"Unauthenticated"}'],
      ['TC-2-3','P-3','Authorization: Bearer <valid_token>','204 No Content, state terhapus, redirect ke login'],
    ],
  },
  {
    num: 3, label: 'GET /api/me', title: 'Lihat Profil Anggota',
    N: 8, E: 9, Vg: 3, pred: 2,
    decNodes: 'n1, n5',
    desc: 'Fitur lihat profil memungkinkan anggota yang telah login melihat data pribadi mereka. Endpoint GET /api/me dilindungi middleware auth:sanctum. Server mencari member berdasarkan token dan mengembalikan data dalam format MemberResource.',
    edges: '(n1,n2), (n1,n4), (n2,n3), (n3,n5), (n4,n8), (n5,n6), (n5,n7), (n6,n8), (n7,n8)',
    nodes: [
      ['n1','START – Request GET /api/me [Decision: token tersedia di client?]'],
      ['n2','Token tersedia → kirim request dengan Bearer token'],
      ['n3','Server mencari member berdasarkan token (auth:sanctum)'],
      ['n4','Token tidak ada → error autentikasi di client'],
      ['n5','Cek member ditemukan dan aktif? [Decision]'],
      ['n6','Return 401/404 – error'],
      ['n7','Return 200 + MemberResource (data profil lengkap)'],
      ['n8','END'],
    ],
    paths: [
      ['P-1','1 – 4 – 8','Tidak ada token, request ditolak sebelum dikirim ke server'],
      ['P-2','1 – 2 – 3 – 5 – 6 – 8','Token ada tetapi member tidak ditemukan atau tidak aktif'],
      ['P-3','1 – 2 – 3 – 5 – 7 – 8','Profil berhasil ditampilkan'],
    ],
    tc: [
      ['TC-3-1','P-1','Header Authorization: tidak ada','401 – {"status":"error","message":"Unauthenticated"}'],
      ['TC-3-2','P-2','Token valid, member di-soft-delete atau status Tidak Aktif','404 – {"status":"error","message":"Member tidak ditemukan"}'],
      ['TC-3-3','P-3','Authorization: Bearer <valid_token>, member aktif','200 – {"status":"success","data":{"id_jemaat":"01011980","nama_lengkap":"...",...}}'],
    ],
  },
  {
    num: 4, label: 'GET /api/me/letters', title: 'Daftar Surat Anggota',
    N: 15, E: 18, Vg: 5, pred: 4,
    decNodes: 'n3, n5, n8, n11',
    desc: 'Fitur daftar surat menampilkan seluruh surat milik anggota yang sedang login. Mendukung pencarian dengan parameter ?keyword= pada nama/tipe surat. Alur mencakup validasi autentikasi, eksekusi query database dengan filter opsional, serta pengembalian hasil dalam format JSON standar API.',
    edges: '(n1,n2), (n2,n3), (n3,n4), (n3,n5), (n4,n15), (n5,n6), (n5,n7), (n6,n15), (n7,n8), (n8,n9), (n8,n10), (n9,n15), (n10,n11), (n11,n12), (n11,n13), (n12,n14), (n13,n14), (n14,n15)',
    nodes: [
      ['n1','START – Request GET /api/me/letters'],
      ['n2','Kirim request + Bearer token (opsional: ?keyword=...)'],
      ['n3','Cek autentikasi token di server [Decision]'],
      ['n4','Return 401 Unauthorized'],
      ['n5','Cek parameter keyword ada dan valid? [Decision]'],
      ['n6','Return error – parameter tidak valid'],
      ['n7','Query letters berdasarkan member_id (base query)'],
      ['n8','Cek perlu filter keyword? [Decision]'],
      ['n9','Tidak ada data atau error awal'],
      ['n10','Terapkan filter WHERE LIKE pada tipe_surat'],
      ['n11','Cek ada hasil dari query? [Decision]'],
      ['n12','Siapkan data letters (array berisi surat)'],
      ['n13','Siapkan array kosong []'],
      ['n14','Format dan kembalikan response JSON 200'],
      ['n15','END'],
    ],
    paths: [
      ['P-1','1 – 2 – 3 – 4 – 15','Request tanpa token, server menolak dengan 401'],
      ['P-2','1 – 2 – 3 – 5 – 6 – 15','Token valid, parameter keyword tidak valid'],
      ['P-3','1 – 2 – 3 – 5 – 7 – 8 – 9 – 15','Token valid, query gagal atau tidak ada data awal'],
      ['P-4','1 – 2 – 3 – 5 – 7 – 8 – 10 – 11 – 12 – 14 – 15','Token valid, keyword ada, hasil ditemukan'],
      ['P-5','1 – 2 – 3 – 5 – 7 – 8 – 10 – 11 – 13 – 14 – 15','Token valid, keyword ada, tidak ada hasil'],
    ],
    tc: [
      ['TC-4-1','P-1','Header Authorization: tidak ada','401 – {"status":"error","message":"Unauthenticated"}'],
      ['TC-4-2','P-2','Token valid, ?keyword= (kosong/null)','422 – Error validasi parameter'],
      ['TC-4-3','P-3','Token valid, anggota baru (belum punya surat)','200 – {"status":"success","data":[]}'],
      ['TC-4-4','P-4','Token valid, ?keyword=pengantar','200 – {"status":"success","data":[{"tipe_surat":"Surat Pengantar",...}]}'],
      ['TC-4-5','P-5','Token valid, ?keyword=pernikahan (tidak ada surat)','200 – {"status":"success","data":[]}'],
    ],
  },
  {
    num: 5, label: 'GET /api/me/letters/{id}/download', title: 'Unduh PDF Surat',
    N: 13, E: 16, Vg: 5, pred: 4,
    decNodes: 'n1, n3, n6, n9',
    desc: 'Fitur unduh PDF memungkinkan anggota mengunduh file PDF surat yang telah tersimpan di storage. Berbeda dengan endpoint admin, endpoint ini hanya melayani file pre-stored (tidak generate on-the-fly). Alur mencakup validasi autentikasi, pencarian letter berdasarkan id dan member_id, pengecekan file di disk, serta streaming file ke client.',
    edges: '(n1,n2), (n1,n12), (n2,n3), (n3,n4), (n3,n5), (n4,n9), (n5,n6), (n6,n7), (n6,n8), (n7,n9), (n8,n9), (n9,n10), (n9,n11), (n10,n13), (n11,n13), (n12,n13)',
    nodes: [
      ['n1','START – GET /api/me/letters/{id}/download [Decision: token ada?]'],
      ['n2','Token tersedia → kirim request ke server'],
      ['n3','Server cari letter by id + member_id [Decision: letter ditemukan?]'],
      ['n4','Return 404 – Letter tidak ditemukan atau bukan milik anggota'],
      ['n5','Cek path file PDF tersimpan di kolom pdf_path'],
      ['n6','Cek file exists di storage disk "public" [Decision]'],
      ['n7','Return 404 – File PDF tidak ada di storage'],
      ['n8','Stream file PDF dari storage'],
      ['n9','Cek proses berhasil? [Decision]'],
      ['n10','Return 500 – Error saat akses/streaming file'],
      ['n11','Return 200 + PDF (Content-Disposition: attachment)'],
      ['n12','Token tidak ada → Return 401 Unauthorized'],
      ['n13','END'],
    ],
    paths: [
      ['P-1','1 – 12 – 13','Tidak ada token, 401 dikembalikan langsung'],
      ['P-2','1 – 2 – 3 – 4 – 9 – 10 – 13','Letter tidak ditemukan atau bukan milik anggota'],
      ['P-3','1 – 2 – 3 – 5 – 6 – 7 – 9 – 10 – 13','Letter ada tetapi file PDF tidak tersimpan di storage'],
      ['P-4','1 – 2 – 3 – 5 – 6 – 8 – 9 – 11 – 13','File ditemukan, PDF berhasil diunduh'],
      ['P-5','1 – 2 – 3 – 5 – 6 – 8 – 9 – 10 – 13','File ada tapi gagal di-stream (error I/O)'],
    ],
    tc: [
      ['TC-5-1','P-1','Header Authorization: tidak ada','401 – {"status":"error","message":"Unauthenticated"}'],
      ['TC-5-2','P-2','Token valid, id: 999 (tidak ada)','404 – {"status":"error","message":"Surat tidak ditemukan"}'],
      ['TC-5-3','P-3','Token valid, id valid, pdf_path: null','404 – {"status":"error","message":"File PDF tidak tersedia"}'],
      ['TC-5-4','P-4','Token valid, id valid, file PDF tersimpan di storage','200 – File PDF terunduh (Content-Disposition: attachment; filename="surat.pdf")'],
      ['TC-5-5','P-5','Token valid, file ada, terjadi error I/O','500 – {"status":"error","message":"Gagal mengunduh file"}'],
    ],
  },
  {
    num: 6, label: 'PUT /api/me', title: 'Perbarui Biodata Anggota',
    N: 10, E: 12, Vg: 4, pred: 3,
    decNodes: 'n2, n3, n7',
    desc: 'Fitur perbarui biodata memungkinkan anggota yang login memperbarui data profil pribadi. Server memvalidasi request body menggunakan UpdateBiodataRequest. Data yang diperbarui meliputi nama lengkap, alamat, nomor telepon, dan data personal lainnya sesuai model Member.',
    edges: '(n1,n2), (n2,n3), (n2,n7), (n3,n4), (n3,n5), (n4,n6), (n5,n7), (n6,n10), (n7,n8), (n7,n9), (n8,n10), (n9,n10)',
    nodes: [
      ['n1','START – User submit form ubah biodata'],
      ['n2','Cek token tersedia di client? [Decision]'],
      ['n3','Server validasi request body (UpdateBiodataRequest) [Decision]'],
      ['n4','Proses update data member di database'],
      ['n5','Return 422 Unprocessable Entity (validasi gagal)'],
      ['n6','Return 200 + MemberResource (data ter-update)'],
      ['n7','Client memproses respons [Decision: sukses/error?]'],
      ['n8','Tampilkan pesan error ke pengguna'],
      ['n9','Tampilkan data profil ter-update ke pengguna'],
      ['n10','END'],
    ],
    paths: [
      ['P-1','1 – 2 – 7 – 8 – 10','Token tidak ada, client tampilkan pesan error autentikasi'],
      ['P-2','1 – 2 – 3 – 4 – 6 – 10','Token valid, data valid, update berhasil'],
      ['P-3','1 – 2 – 3 – 5 – 7 – 8 – 10','Token valid, validasi server gagal (422), tampilkan error'],
      ['P-4','1 – 2 – 3 – 5 – 7 – 9 – 10','Token valid, 422 diterima, form dikembalikan ke nilai semula'],
    ],
    tc: [
      ['TC-6-1','P-1','Header Authorization: tidak ada','Client: pesan "Sesi telah berakhir, silakan login kembali"'],
      ['TC-6-2','P-2','Token valid, body: {nama_lengkap:"David Gultom", alamat:"Medan", no_telepon:"081234567890"}','200 – {"status":"success","data":{member ter-update}}'],
      ['TC-6-3','P-3','Token valid, body: {no_telepon:"abc-bukan-angka"}','422 – {"status":"error","errors":{"no_telepon":["Format nomor telepon tidak valid"]}}'],
      ['TC-6-4','P-4','Token valid, body: {nama_lengkap:""} (field wajib kosong)','422 – Form dikembalikan ke nilai sebelumnya (tidak ada perubahan)'],
    ],
  },
  {
    num: 7, label: 'POST /api/me/pengajuan', title: 'Pengajuan Surat',
    N: 16, E: 20, Vg: 6, pred: 5,
    decNodes: 'n3, n6, n7, n9, n13',
    desc: 'Fitur pengajuan surat memungkinkan anggota mengajukan permohonan pembuatan surat resmi gereja. Sistem mendukung 7 jenis surat dengan field wajib yang berbeda. Pengajuan disimpan ke tabel pengajuan_surats dengan status "Dalam Proses" dan menunggu persetujuan admin.',
    edges: '(n1,n2), (n2,n3), (n3,n4), (n3,n13), (n4,n5), (n5,n6), (n6,n7), (n6,n13), (n7,n8), (n7,n13), (n8,n9), (n9,n10), (n9,n11), (n10,n11), (n11,n12), (n12,n16), (n13,n14), (n13,n15), (n14,n16), (n15,n16)',
    nodes: [
      ['n1','START – User isi dan submit form pengajuan surat'],
      ['n2','Validasi form sisi klien (field wajib & tipe surat)'],
      ['n3','Kirim POST /api/me/pengajuan + Bearer token [Decision: auth valid?]'],
      ['n4','Server menerima request'],
      ['n5','Validasi letter_type dari daftar enum yang diizinkan'],
      ['n6','Cek letter_type valid? [Decision]'],
      ['n7','Cek semua required fields sesuai tipe surat tersedia? [Decision]'],
      ['n8','Simpan PengajuanSurat ke database (status: "Dalam Proses")'],
      ['n9','Cek hasil penyimpanan ke DB [Decision]'],
      ['n10','Tangani exception / rollback transaksi'],
      ['n11','Return 201 Created + data pengajuan'],
      ['n12','Client menerima respons sukses, tampilkan konfirmasi'],
      ['n13','Handler error response [Decision: jenis error?]'],
      ['n14','Return 422 Unprocessable Entity'],
      ['n15','Return 500 Internal Server Error'],
      ['n16','END'],
    ],
    paths: [
      ['P-1','1 – 2 – 3 – 13 – 14 – 16','Token tidak ada atau kadaluarsa (401 → 422)'],
      ['P-2','1 – 2 – 3 – 4 – 5 – 6 – 13 – 14 – 16','Tipe surat tidak dikenal, validasi gagal (422)'],
      ['P-3','1 – 2 – 3 – 4 – 5 – 6 – 7 – 13 – 14 – 16','Tipe surat valid, required fields kurang (422)'],
      ['P-4','1 – 2 – 3 – 4 – 5 – 6 – 7 – 8 – 9 – 10 – 11 – 12 – 16','Semua data valid, terjadi error DB (exception handler)'],
      ['P-5','1 – 2 – 3 – 4 – 5 – 6 – 7 – 8 – 9 – 11 – 12 – 16','Pengajuan berhasil disimpan, 201 Created'],
      ['P-6','1 – 2 – 3 – 13 – 15 – 16','Server mengalami kesalahan internal (500)'],
    ],
    tc: [
      ['TC-7-1','P-1','Header Authorization: tidak ada','401 – {"status":"error","message":"Unauthenticated"}'],
      ['TC-7-2','P-2','Token valid, letter_type: "surat_tidak_dikenal"','422 – {"status":"error","errors":{"letter_type":["Tipe surat tidak valid"]}}'],
      ['TC-7-3','P-3','Token valid, letter_type: "surat_pengantar", keterangan: (kosong)','422 – {"status":"error","errors":{"keterangan":["The keterangan field is required"]}}'],
      ['TC-7-4','P-4','Semua field valid, simulasi database timeout','500 – {"status":"error","message":"Terjadi kesalahan pada server"}'],
      ['TC-7-5','P-5','Token valid, letter_type: "surat_pengantar", keterangan: "Keperluan kerja"','201 – {"status":"success","data":{"id":1,"status":"Dalam Proses",...}}'],
      ['TC-7-6','P-6','Server mengalami fatal error (uncaught exception)','500 – Internal Server Error'],
    ],
  },
];

// ─────────────────────────────────────────────────────────────────────────────
// NODE TABLE
// ─────────────────────────────────────────────────────────────────────────────
// CW = 7937 → col widths: 500 + 800 + 6637
function nodeTable(nodes) {
  const W = [600, 900, 6437]; // = 7937
  const header = new TableRow({ children: [TH('No', W[0]), TH('Node', W[1]), TH('Deskripsi Node', W[2])] });
  const rows = nodes.map(([nid, desc], i) =>
    new TableRow({ children: [TDc(String(i + 1), W[0]), TDc(nid, W[1]), TD(desc, W[2])] })
  );
  return mkTable([header, ...rows], W);
}

// ─────────────────────────────────────────────────────────────────────────────
// PATH TABLE
// ─────────────────────────────────────────────────────────────────────────────
// CW: 700 + 3100 + 4137 = 7937
function pathTable(paths) {
  const W = [700, 3100, 4137];
  const header = new TableRow({ children: [TH('Jalur', W[0]), TH('Urutan Node', W[1]), TH('Deskripsi Skenario', W[2])] });
  const rows = paths.map(([pid, seq, desc]) =>
    new TableRow({ children: [TDc(pid, W[0]), TD(seq, W[1]), TD(desc, W[2])] })
  );
  return mkTable([header, ...rows], W);
}

// ─────────────────────────────────────────────────────────────────────────────
// TEST CASE TABLE
// ─────────────────────────────────────────────────────────────────────────────
// CW: 900 + 700 + 2837 + 3500 = 7937
function tcTable(tcs) {
  const W = [900, 700, 2837, 3500];
  const header = new TableRow({
    children: [TH('ID Test Case', W[0]), TH('Jalur', W[1]), TH('Input', W[2]), TH('Expected Output', W[3])]
  });
  const rows = tcs.map(([id, path, input, expected]) =>
    new TableRow({ children: [TDc(id, W[0]), TDc(path, W[1]), TD(input, W[2]), TD(expected, W[3])] })
  );
  return mkTable([header, ...rows], W);
}

// ─────────────────────────────────────────────────────────────────────────────
// SECTION GENERATOR (per CFG)
// ─────────────────────────────────────────────────────────────────────────────
function cfgSection(c, tableNum) {
  const { num, label, title, N, E, Vg, pred, decNodes, desc, edges, nodes, paths, tc } = c;
  let t = tableNum;
  const elems = [];

  // ── Section heading ──
  elems.push(H2(`${num}. CFG – ${title} (${label})`));

  // ── Description ──
  elems.push(Pj([R(desc)]));
  elems.push(space(80));

  // ── Node table ──
  elems.push(H3(`${num}.1 Deskripsi Node`));
  elems.push(nodeTable(nodes));
  elems.push(caption(`Tabel ${t++}. Deskripsi Node CFG ${num} – ${title}`));

  // ── CFG image placeholder ──
  elems.push(imgNote(num));
  elems.push(new Paragraph({
    alignment: AlignmentType.CENTER,
    spacing: { before: 40, after: 200 },
    children: [new TextRun({ text: `Gambar ${num}. Control Flow Graph (CFG) ${num} – ${title}`, font: FONT, size: SZ_S, italics: true })]
  }));

  // ── Cyclomatic complexity ──
  elems.push(H3(`${num}.2 Perhitungan Cyclomatic Complexity`));
  elems.push(Pj([
    R('Berikut adalah perhitungan Cyclomatic Complexity menggunakan dua metode, yaitu metode edge-node dan metode predikat.')
  ]));
  elems.push(space(60));

  // Method 1
  elems.push(P([R('Metode 1 (berdasarkan jumlah edge dan node):', { bold: true })]));
  elems.push(Pc([R(`V(G)  =  E – N + 2  =  ${E} – ${N} + 2  =  ${Vg}`, { bold: true, size: SZ })]));
  elems.push(space(60));

  // Method 2
  elems.push(P([R('Metode 2 (berdasarkan jumlah node predikat/decision):', { bold: true })]));
  elems.push(Pc([R(`V(G)  =  P + 1  =  ${pred} + 1  =  ${Vg}`, { bold: true, size: SZ })]));
  elems.push(space(60));

  elems.push(Pj([
    R('Keterangan: '),
    R(`E = ${E}`, { bold: true }), R(' (jumlah edge/busur), '),
    R(`N = ${N}`, { bold: true }), R(' (jumlah node), '),
    R(`P = ${pred}`, { bold: true }), R(` (jumlah node predikat: ${decNodes}). `),
    R(`Nilai Cyclomatic Complexity V(G) = ${Vg} menunjukkan terdapat `, ),
    R(`${Vg} jalur independen`, { bold: true }),
    R(' yang harus diuji.'),
  ]));

  elems.push(Pj([
    R('Busur (edge) pada grafik: '),
    R(edges, { italics: true }),
    R('.'),
  ]));
  elems.push(space(80));

  // ── Independent paths ──
  elems.push(H3(`${num}.3 Jalur Independen (Independent Paths)`));
  elems.push(pathTable(paths));
  elems.push(caption(`Tabel ${t++}. Jalur Independen CFG ${num} – ${title}`));

  // ── Test cases ──
  elems.push(H3(`${num}.4 Tabel Test Case`));
  elems.push(tcTable(tc));
  elems.push(caption(`Tabel ${t++}. Test Case CFG ${num} – ${title}`));

  // Page break between sections (except last)
  if (num < 7) elems.push(new Paragraph({ children: [new PageBreak()] }));

  return { elems, nextTable: t };
}

// ─────────────────────────────────────────────────────────────────────────────
// SUMMARY TABLE
// ─────────────────────────────────────────────────────────────────────────────
// Columns: No | Fitur | N | E | V(G) | Jalur | TC
// widths:  500 | 3137 | 800 | 800 | 800 | 900 | 1000 = 7937
function summaryTable() {
  const W = [500, 3137, 800, 800, 800, 900, 1000];
  const header = new TableRow({ children: [
    TH('No', W[0]), TH('Fitur / Endpoint', W[1]), TH('N', W[2]), TH('E', W[3]),
    TH('V(G)', W[4]), TH('Jalur', W[5]), TH('Test Case', W[6])
  ]});
  const rows = cfgs.map((c, i) =>
    new TableRow({ children: [
      TDc(String(i + 1), W[0]),
      TD(`${c.title} (${c.label})`, W[1]),
      TDc(String(c.N), W[2]), TDc(String(c.E), W[3]),
      TDc(String(c.Vg), W[4]),
      TDc(String(c.paths.length), W[5]),
      TDc(String(c.tc.length), W[6]),
    ]})
  );

  const totalTC = cfgs.reduce((s, c) => s + c.tc.length, 0);
  const totalJalur = cfgs.reduce((s, c) => s + c.paths.length, 0);
  const totalRow = new TableRow({ children: [
    cell('TOTAL', W[0] + W[1], { fill:'F0F0F0', bold:true, align:AlignmentType.RIGHT, span:2, isHead:false }),
    TDc('', W[2]), TDc('', W[3]), TDc('', W[4]),
    cell(String(totalJalur), W[5], { fill:'F0F0F0', bold:true, align:AlignmentType.CENTER }),
    cell(String(totalTC),    W[6], { fill:'F0F0F0', bold:true, align:AlignmentType.CENTER }),
  ]});

  return mkTable([header, ...rows, totalRow], W);
}

// ─────────────────────────────────────────────────────────────────────────────
// BUILD DOCUMENT
// ─────────────────────────────────────────────────────────────────────────────
const allChildren = [];
let tableCounter = 1;

// ── Title block ──
allChildren.push(
  new Paragraph({ spacing: { before: 0, after: 60 }, alignment: AlignmentType.CENTER,
    children: [new TextRun({ text: 'PENGUJIAN WHITE BOX', font: FONT, size: 36, bold: true })] }),
  new Paragraph({ spacing: { before: 0, after: 60 }, alignment: AlignmentType.CENTER,
    children: [new TextRun({ text: '(Basis Path Testing)', font: FONT, size: 28, bold: true })] }),
  new Paragraph({ spacing: { before: 0, after: 60 }, alignment: AlignmentType.CENTER,
    children: [new TextRun({ text: 'Sistem Sekretariat Jemaat – GPdI', font: FONT, size: 26 })] }),
  space(160),
);

// ── Intro paragraph ──
allChildren.push(H1('A. Pendahuluan'));
allChildren.push(Pj([R(
  'Pengujian white box dilakukan dengan menggunakan metode Basis Path Testing yang dikemukakan oleh Tom McCabe. ' +
  'Metode ini menggunakan Control Flow Graph (CFG) untuk merepresentasikan alur logika program, kemudian menghitung ' +
  'Cyclomatic Complexity V(G) untuk menentukan jumlah jalur independen minimum yang harus diuji. ' +
  'Setiap jalur independen menghasilkan setidaknya satu test case.'
)]));
allChildren.push(Pj([R(
  'Pengujian mencakup 7 fitur utama pada aplikasi Sekretariat Jemaat GPdI, yaitu: (1) Login, ' +
  '(2) Logout, (3) Lihat Profil, (4) Daftar Surat, (5) Unduh PDF Surat, (6) Perbarui Biodata, ' +
  'dan (7) Pengajuan Surat. Masing-masing fitur dianalisis menggunakan CFG yang menggabungkan alur ' +
  'sisi klien (Nuxt.js/Vue 3) dan sisi server (Laravel 11 REST API).'
)]));

// ── Summary overview ──
allChildren.push(H1('B. Rekapitulasi Control Flow Graph'));
allChildren.push(summaryTable());
allChildren.push(caption(`Tabel ${tableCounter++}. Rekapitulasi Parameter CFG Seluruh Fitur`));
allChildren.push(space(120));

allChildren.push(Pj([
  R('Keterangan: '),
  R('N', { bold: true }), R(' = jumlah node, '),
  R('E', { bold: true }), R(' = jumlah edge/busur, '),
  R('V(G)', { bold: true }), R(' = Cyclomatic Complexity, '),
  R('Jalur', { bold: true }), R(' = jumlah jalur independen, '),
  R('Test Case', { bold: true }), R(' = jumlah kasus uji yang dirancang.'),
]));
allChildren.push(space(120));

// ── Per-CFG sections ──
allChildren.push(H1('C. Analisis dan Test Case per Fitur'));
allChildren.push(new Paragraph({ children: [new PageBreak()] }));

for (const cfg of cfgs) {
  const { elems, nextTable } = cfgSection(cfg, tableCounter);
  allChildren.push(...elems);
  tableCounter = nextTable;
}

// ── Final summary of test results ──
allChildren.push(H1('D. Rekapitulasi Hasil Pengujian'));
allChildren.push(Pj([R(
  'Berdasarkan analisis basis path testing terhadap 7 fitur sistem, seluruh jalur independen ' +
  'telah diidentifikasi dan test case telah dirancang sesuai dengan nilai Cyclomatic Complexity ' +
  'masing-masing CFG. Berikut adalah ringkasan hasil pengujian white box:'
)]));
allChildren.push(space(80));

// ── final result table: reuse summaryTable ──
allChildren.push(summaryTable());
allChildren.push(caption(`Tabel ${tableCounter++}. Rekapitulasi Hasil Pengujian White Box`));
allChildren.push(space(120));

const totalTC = cfgs.reduce((s, c) => s + c.tc.length, 0);
const totalJalur = cfgs.reduce((s, c) => s + c.paths.length, 0);
allChildren.push(Pj([
  R(`Total keseluruhan terdapat `),
  R(String(totalJalur), { bold: true }),
  R(` jalur independen dari 7 fitur yang diuji, menghasilkan `),
  R(String(totalTC), { bold: true }),
  R(` test case. Seluruh test case dirancang berdasarkan jalur independen yang diperoleh dari perhitungan ` +
     `Cyclomatic Complexity menggunakan metode Basis Path Testing (McCabe, 1976). ` +
     `Pengujian ini memastikan setiap jalur logika yang mungkin terjadi pada kode program telah tercakup.`),
]));

// ─────────────────────────────────────────────────────────────────────────────
// DOCUMENT OBJECT
// ─────────────────────────────────────────────────────────────────────────────
const doc = new Document({
  styles: {
    default: {
      document: { run: { font: FONT, size: SZ } }
    },
    paragraphStyles: [
      {
        id: 'Heading1', name: 'Heading 1', basedOn: 'Normal', next: 'Normal', quickFormat: true,
        run: { size: 32, bold: true, font: FONT },
        paragraph: { spacing: { before: 300, after: 160 }, outlineLevel: 0 }
      },
      {
        id: 'Heading2', name: 'Heading 2', basedOn: 'Normal', next: 'Normal', quickFormat: true,
        run: { size: SZ_H, bold: true, font: FONT },
        paragraph: { spacing: { before: 240, after: 120 }, outlineLevel: 1 }
      },
      {
        id: 'Heading3', name: 'Heading 3', basedOn: 'Normal', next: 'Normal', quickFormat: true,
        run: { size: SZ, bold: true, font: FONT },
        paragraph: { spacing: { before: 180, after: 80 }, outlineLevel: 2 }
      },
    ]
  },
  sections: [{
    properties: {
      page: {
        size:   { width: PAGE_W, height: PAGE_H },
        margin: { top: MAR_T, right: MAR_R, bottom: MAR_B, left: MAR_L }
      }
    },
    headers: {
      default: new Header({
        children: [new Paragraph({
          alignment: AlignmentType.RIGHT,
          border: { bottom: { style: BorderStyle.SINGLE, size: 4, color: '2E4057' } },
          children: [new TextRun({ text: 'Pengujian White Box – Basis Path Testing | Sekretariat Jemaat GPdI', font: FONT, size: 18, color: '666666' })]
        })]
      })
    },
    footers: {
      default: new Footer({
        children: [new Paragraph({
          alignment: AlignmentType.CENTER,
          children: [
            new TextRun({ text: 'Halaman ', font: FONT, size: 18, color: '666666' }),
            new TextRun({ children: [PageNumber.CURRENT], font: FONT, size: 18, color: '666666' }),
          ]
        })]
      })
    },
    children: allChildren,
  }]
});

// ─────────────────────────────────────────────────────────────────────────────
// WRITE FILE
// ─────────────────────────────────────────────────────────────────────────────
const OUT = path.join(__dirname, 'whitebox-testing.docx');
Packer.toBuffer(doc).then(buf => {
  fs.writeFileSync(OUT, buf);
  console.log(`✓ File berhasil dibuat: ${OUT}`);
}).catch(err => {
  console.error('✗ Gagal membuat file:', err.message);
  process.exit(1);
});
