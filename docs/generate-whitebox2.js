'use strict';
const {
  Document, Packer, Paragraph, TextRun, Table, TableRow, TableCell,
  AlignmentType, HeadingLevel, BorderStyle, WidthType, ShadingType,
  PageBreak, PageNumber, Header, Footer
} = require('docx');
const fs   = require('fs');
const path = require('path');

// ─────────────────────────────────────────────────────────────────────────────
// PAGE / FONT CONSTANTS
// ─────────────────────────────────────────────────────────────────────────────
const CM   = 567;           // 1 cm in DXA
const CW   = 11906 - 4*CM - 3*CM;  // ≈ 7937 DXA content width (A4, margin 4-3-3-3)
const TNR  = 'Times New Roman';
const CODE = 'Courier New';

// ─────────────────────────────────────────────────────────────────────────────
// BORDER HELPERS
// ─────────────────────────────────────────────────────────────────────────────
const bord    = (c, s=4) => ({ style: BorderStyle.SINGLE, size: s, color: c });
const bAll    = c => ({ top: bord(c), bottom: bord(c), left: bord(c), right: bord(c) });
const B_HEAD  = bAll('2E4057');
const B_CELL  = bAll('AAAAAA');

// ─────────────────────────────────────────────────────────────────────────────
// PARAGRAPH / RUN HELPERS
// ─────────────────────────────────────────────────────────────────────────────
const R  = (t, o={}) => new TextRun({ text: t, font: TNR, size: 22, ...o });
const Rc = (t, o={}) => new TextRun({ text: t, font: CODE, size: 18, ...o });  // code run

const P  = (ch, o={}) => new Paragraph({ children: Array.isArray(ch)?ch:[ch], spacing:{after:80}, ...o });
const Pc = (ch, o={}) => P(ch, { alignment: AlignmentType.CENTER, ...o });
const Pj = (ch, o={}) => P(ch, { alignment: AlignmentType.JUSTIFIED, ...o });
const sp = (n=120) => new Paragraph({ children:[], spacing:{after:n} });

const H1 = t => new Paragraph({ heading:HeadingLevel.HEADING_1, spacing:{before:240,after:120},
  children:[new TextRun({ text:t, font:TNR, size:28, bold:true })] });
const H2 = t => new Paragraph({ heading:HeadingLevel.HEADING_2, spacing:{before:200,after:100},
  children:[new TextRun({ text:t, font:TNR, size:24, bold:true })] });
const H3 = t => new Paragraph({ heading:HeadingLevel.HEADING_3, spacing:{before:160,after:80},
  children:[new TextRun({ text:t, font:TNR, size:22, bold:true })] });

// ─────────────────────────────────────────────────────────────────────────────
// TABLE CELL HELPERS
// ─────────────────────────────────────────────────────────────────────────────
function mkCell(children, width, { fill='FFFFFF', isHead=false, align=AlignmentType.LEFT, span=1 }={}) {
  return new TableCell({
    borders:  isHead ? B_HEAD : B_CELL,
    shading:  { fill, type: ShadingType.CLEAR },
    margins:  { top:60, bottom:60, left:100, right:100 },
    width:    { size:width, type:WidthType.DXA },
    columnSpan: span,
    children: Array.isArray(children) ? children : [children],
  });
}
const TH  = (t,w,s=1) => mkCell(
  [new Paragraph({ alignment:AlignmentType.CENTER,
    children:[new TextRun({text:t, font:TNR, size:20, bold:true, color:'FFFFFF'})] })],
  w, { fill:'2E4057', isHead:true, span:s });

const TD  = (t,w,a=AlignmentType.LEFT) => mkCell(
  [new Paragraph({ alignment:a, children:[new TextRun({text:t, font:TNR, size:20})] })],
  w, { align:a });

const TDc = (t,w) => TD(t,w,AlignmentType.CENTER);

// Cell with mixed content (multiple paragraphs of code/text)
function codeCell(lines, width) {
  const paras = [];
  for (const ln of lines) {
    if (ln.startsWith('<<')) {
      // italic description
      paras.push(new Paragraph({ spacing:{after:40},
        children:[new TextRun({ text: ln.slice(2), font:TNR, size:18, italics:true, color:'555555' })] }));
    } else {
      paras.push(new Paragraph({ spacing:{after:40},
        children:[new TextRun({ text: ln, font:CODE, size:18 })] }));
    }
  }
  return mkCell(paras.length ? paras : [new Paragraph({ children:[] })], width);
}

// Component cell (bold top line + component filename below)
function kompCell(lines, width) {
  if (!lines || lines.length === 0 || lines[0] === '') {
    return mkCell([new Paragraph({ children:[] })], width);
  }
  const paras = lines.map((l,i) => new Paragraph({ spacing:{after:20},
    children:[new TextRun({ text:l, font:TNR, size:i===0?20:18, bold:i===0, italics:i>0 })] }));
  return mkCell(paras, width);
}

function mkTable(rows, colW) {
  return new Table({ width:{ size:CW, type:WidthType.DXA }, columnWidths:colW, rows });
}

// ─────────────────────────────────────────────────────────────────────────────
// SCRIPT TABLE  (col widths: 2100 + 5137 + 700 = 7937)
// ─────────────────────────────────────────────────────────────────────────────
const SW = [2100, 5137, 700];

function scriptTable(rows) {
  const header = new TableRow({ children:[
    TH('Komponen', SW[0]),
    TH('Script',   SW[1]),
    TH('Node',     SW[2]),
  ]});
  const dataRows = rows.map(([komp, scripts, node]) =>
    new TableRow({ children:[
      kompCell(Array.isArray(komp) ? komp : (komp ? [komp] : []), SW[0]),
      codeCell(scripts, SW[1]),
      mkCell([new Paragraph({ alignment:AlignmentType.CENTER,
        children:[new TextRun({ text:node||'', font:TNR, size:20, bold:!!node })] })], SW[2]),
    ]})
  );
  return mkTable([header, ...dataRows], SW);
}

// ─────────────────────────────────────────────────────────────────────────────
// SUMMARY TABLE
// ─────────────────────────────────────────────────────────────────────────────
// Cols: No(500) | Nama(2500) | N(700) | E(700) | P(700) | V(G)(700) | Path(700) = 6500 + 1437 padding → 7937
const SUMW = [500, 2837, 800, 800, 800, 800, 400];

function summaryTable(cfgs) {
  const header = new TableRow({ children:[
    TH('No', SUMW[0]), TH('Nama Script', SUMW[1]),
    TH('N', SUMW[2]),  TH('E', SUMW[3]),
    TH('P', SUMW[4]),  TH('V(G)', SUMW[5]), TH('Path', SUMW[6]),
  ]});
  const rows = cfgs.map((c,i) => new TableRow({ children:[
    TDc(String(i+1), SUMW[0]),
    TD(c.scriptName, SUMW[1]),
    TDc(String(c.vg.n), SUMW[2]),
    TDc(String(c.vg.e), SUMW[3]),
    TDc(String(c.vg.p), SUMW[4]),
    TDc(String(c.vg.result), SUMW[5]),
    TDc(String(c.paths.length), SUMW[6]),
  ]}));
  return mkTable([header, ...rows], SUMW);
}

// ─────────────────────────────────────────────────────────────────────────────
// CFG DATA  (exact content from the chat session)
// Lines starting with << are rendered as italic description, others as code.
// ─────────────────────────────────────────────────────────────────────────────
const cfgs = [
  // ── CFG 1 ────────────────────────────────────────────────────────────────
  {
    title: '1. Pengujian White Box Script Post_Login',
    scriptName: 'Post_Login',
    tableRows: [
      [['Rest Client (Nuxt.js)', 'LoginForm.vue + useAuth.ts'],
        ['const handleLogin = async () => {'], '1'],
      ['', ['loading.value = true', 'errorMsg.value = null'], ''],
      ['', ['try {', 'await auth.login(form.value.id_jemaat, form.value.password)'], '2'],
      ['', ['const login = async (idJemaat: string, password: string) => {',
            "const res = await request('/auth/login', { method: 'POST', body: { id_jemaat, password } })"], '3'],
      [['Rest Server (PHP)', 'MemberAuthController.php'],
        ['public function login(MemberLoginRequest $request): JsonResponse {',
         "$member = Member::where('id_jemaat', $request->id_jemaat)->first()"], '4'],
      ['', ["if (!$member || !Hash::check($request->password, $member->password)) {"], '5'],
      ['', ["return \$this->error('ID Jemaat atau password salah', 401)"], '6'],
      ['', ["if (\$member->status_aktif !== 'Aktif') {"], '7'],
      ['', ["return \$this->error('Akun jemaat tidak aktif', 403)"], '8'],
      ['', ["$token = $member->createToken('api-token')->plainTextToken",
            "return \$this->success(['member' => ..., 'token' => \$token], 'Login berhasil')"], '9'],
      [['Rest Client (Nuxt.js)', 'useAuth.ts'],
        ['token.value = res.data.token', 'member.value = res.data.member'], '10'],
      [['Rest Client (Nuxt.js)', 'LoginForm.vue'],
        ["await router.push('/profile')"], '11'],
      ['', ["} catch (err: any) {", "errorMsg.value = err.data?.message ?? 'ID atau password salah.'"], '12'],
      ['', ['} finally {', 'loading.value = false', '}'], '13'],
    ],
    flowPaths: [
      'Jalur utama (login berhasil): 1 → 2 → 3 → 4 → 5 → 7 → 9 → 10 → 11 → 13',
      'Jalur percabangan (credential salah, 401): 5 → 6 → 12',
      'Jalur percabangan (akun tidak aktif, 403): 7 → 8 → 12',
      'Jalur catch ke finally: 12 → 13',
    ],
    vg: { e:14, n:13, p:2, result:3, pNodes:'node 5 dan node 7' },
    paths: [
      ['Path 1', '1 - 2 - 3 - 4 - 5 - 7 - 9 - 10 - 11 - 13', 'login berhasil, token disimpan, navigasi ke profil'],
      ['Path 2', '1 - 2 - 3 - 4 - 5 - 6 - 12 - 13', 'ID jemaat atau password salah, respons 401'],
      ['Path 3', '1 - 2 - 3 - 4 - 5 - 7 - 8 - 12 - 13', 'akun tidak aktif, respons 403'],
    ],
  },

  // ── CFG 2 ────────────────────────────────────────────────────────────────
  {
    title: '2. Pengujian White Box Script Post_Logout',
    scriptName: 'Post_Logout',
    tableRows: [
      [['Rest Client (Nuxt.js)', 'NavbarApp.vue'],
        ['const handleLogout = async () => {',
         "if (!confirm('Apakah Anda yakin ingin keluar?')) return"], '1'],
      ['', ['<<pengguna membatalkan konfirmasi'], '2'],
      ['', ['loggingOut.value = true', 'await auth.logout()'], '3'],
      [['Rest Client (Nuxt.js)', 'useAuth.ts'],
        ['const logout = async () => {'], '4'],
      ['', ["try { await request('/me/logout', { method: 'DELETE' }) }"], '5'],
      [['Rest Server (PHP)', 'MemberAuthController.php'],
        ['public function logout(Request $request): JsonResponse {',
         '$request->user()->currentAccessToken()->delete()'], '6'],
      ['', ['return $this->noContent()', '<<204 No Content'], '7'],
      ['', ['} catch {}', '<<error diabaikan — bypass dari Node 5 jika API gagal'], '8'],
      ['', ['token.value = null', 'member.value = null'], '9'],
      ['', ["navigateTo('/login')"], '10'],
      ['', ['<<akhir handleLogout'], '11'],
    ],
    flowPaths: [
      'Jalur utama (logout berhasil): 1 → 3 → 4 → 5 → 6 → 7 → 9 → 10 → 11',
      'Jalur percabangan (pengguna membatalkan): 1 → 2 → 11',
      'Jalur percabangan (API error, diabaikan): 5 → 8 → 9',
    ],
    vg: { e:12, n:11, p:2, result:3, pNodes:'node 1 dan node 5' },
    paths: [
      ['Path 1', '1 - 3 - 4 - 5 - 6 - 7 - 9 - 10 - 11', 'pengguna konfirmasi, token dicabut di server, redirect ke login'],
      ['Path 2', '1 - 2 - 11', 'pengguna membatalkan konfirmasi, tidak ada aksi'],
      ['Path 3', '1 - 3 - 4 - 5 - 8 - 9 - 10 - 11', 'pengguna konfirmasi, API error, token tetap dihapus lokal dan redirect'],
    ],
  },

  // ── CFG 3 ────────────────────────────────────────────────────────────────
  {
    title: '3. Pengujian White Box Script Get_Profil',
    scriptName: 'Get_Profil',
    tableRows: [
      [['Rest Client (Nuxt.js)', 'Profile.vue'],
        ["const { request } = useApi()",
         "const { data, pending, error } = await useAsyncData('member-profile', async () => {",
         "const res = await request('/me')"], '1'],
      [['Rest Server (PHP)', 'MemberApiController.php'],
        ['public function show(Request $request): JsonResponse {',
         'return $this->success(MemberResource::make($request->user()))'], '2'],
      [['Rest Client (Nuxt.js)', 'Profile.vue'],
        ['return res.data', '<<data state terisi, useAsyncData selesai'], '3'],
      ['', ['<<Error state diset oleh useAsyncData jika request gagal',
            '<<bypass dari Node 1 langsung ke Node 4'], '4'],
      ['', ["const formatTanggalLahir = (tempat: string, tanggal: string) => {",
            "if (!tanggal) return '-'"], '5'],
      ['', ["return '-'", '<<tanggal bernilai null atau kosong'], '6'],
      ['', ["const d = new Date(tanggal)",
            "const bulan = ['Januari', ..., 'Desember']",
            "return `${tempat}, ${d.getDate()} ${bulan[d.getMonth()]} ${d.getFullYear()}`"], '7'],
      ['', ['<<akhir formatTanggalLahir'], '8'],
    ],
    flowPaths: [
      'Jalur utama (fetch sukses, tanggal valid): 1 → 2 → 3 → 5 → 7 → 8',
      'Jalur percabangan (fetch gagal/error): 1 → 4 → 8',
      'Jalur percabangan (fetch sukses, tanggal null): 5 → 6 → 8',
    ],
    vg: { e:9, n:8, p:2, result:3, pNodes:'node 1 dan node 5' },
    paths: [
      ['Path 1', '1 - 2 - 3 - 5 - 7 - 8', 'fetch berhasil, tanggal valid, format ditampilkan'],
      ['Path 2', '1 - 4 - 8', 'fetch gagal, error state diset, profil tidak ditampilkan'],
      ['Path 3', '1 - 2 - 3 - 5 - 6 - 8', "fetch berhasil, tanggal kosong/null, tampilkan tanda '-'"],
    ],
  },

  // ── CFG 4 ────────────────────────────────────────────────────────────────
  {
    title: '4. Pengujian White Box Script Get_Surat',
    scriptName: 'Get_Surat',
    tableRows: [
      [['Rest Client (Nuxt.js)', 'Surat.vue'],
        ['const handleSearch = async () => {',
         'errorMsg.value = null',
         'const input = searchInput.value.trim().toLowerCase()'], '1'],
      ['', ['if (!input)'], '2'],
      ['', ["errorMsg.value = 'Masukkan ID Jemaat atau Nama Jemaat terlebih dahulu.'",
            'return'], '3'],
      ['', ['const member = auth.member.value'], '4'],
      ['', ['if (!member)'], '5'],
      ['', ["errorMsg.value = 'Gagal memuat data akun. Coba muat ulang halaman.'",
            'return'], '6'],
      ['', ['const cocokId  = member.id_jemaat.toLowerCase() === input',
            'const cocokNama = member.nama_lengkap.toLowerCase().includes(input)'], '7'],
      ['', ['if (!cocokId && !cocokNama)'], '8'],
      ['', ["errorMsg.value = 'Data tidak ditemukan...'",
            'letters.value = []', 'searched.value = false', 'return'], '9'],
      ['', ['loading.value = true', 'try {',
            "const res = await request('/me/letters')"], '10'],
      [['Rest Server (PHP)', 'MemberApiController.php'],
        ['public function letters(Request $request): JsonResponse {',
         '$member = $request->user()',
         "$keyword = $request->query('keyword')"], '11'],
      ['', ["$letters = Letter::where('member_id', $member->id)",
            '->when($keyword, fn($q) => ...)',
            '->orderByDesc(\'created_at\')->get()',
            'return $this->success(LetterResource::collection($letters))'], '12'],
      [['Rest Client (Nuxt.js)', 'Surat.vue'],
        ['letters.value = res.data', 'searched.value = true'], '13'],
      ['', ['} catch {', "errorMsg.value = 'Gagal memuat daftar surat.'"], '14'],
      ['', ['} finally {', 'loading.value = false', '}'], '15'],
    ],
    flowPaths: [
      'Jalur utama (pencarian berhasil): 1 → 2 → 4 → 5 → 7 → 8 → 10 → 11 → 12 → 13 → 15',
      'Jalur percabangan (input kosong): 2 → 3 → 15',
      'Jalur percabangan (data member tidak ada): 5 → 6 → 15',
      'Jalur percabangan (input tidak cocok): 8 → 9 → 15',
      'Jalur percabangan (API error): 10 → 14 → 15',
    ],
    vg: { e:18, n:15, p:4, result:5, pNodes:'node 2, 5, 8, dan 10' },
    paths: [
      ['Path 1', '1 - 2 - 4 - 5 - 7 - 8 - 10 - 11 - 12 - 13 - 15', 'input valid, cocok, daftar surat berhasil dimuat'],
      ['Path 2', '1 - 2 - 3 - 15', 'kolom pencarian kosong, pesan validasi ditampilkan'],
      ['Path 3', '1 - 2 - 4 - 5 - 6 - 15', 'data member belum tersedia di state, pesan error ditampilkan'],
      ['Path 4', '1 - 2 - 4 - 5 - 7 - 8 - 9 - 15', 'input tidak cocok dengan ID atau nama jemaat'],
      ['Path 5', '1 - 2 - 4 - 5 - 7 - 8 - 10 - 14 - 15', 'request ke server gagal, pesan error API ditampilkan'],
    ],
  },

  // ── CFG 5 ────────────────────────────────────────────────────────────────
  {
    title: '5. Pengujian White Box Script Download_PDF',
    scriptName: 'Download_PDF',
    tableRows: [
      [['Rest Client (Nuxt.js)', 'Surat.vue'],
        ['const downloadPdf = async (letter: Letter) => {',
         'downloadingId.value = letter.id', 'try {',
         'const token = useCookie(\'api_token\')',
         'const res = await fetch(`${apiBase}/me/letters/${letter.id}/download`,',
         '  { headers: { Authorization: `Bearer ${token.value}` } })'], '1'],
      [['Rest Server (PHP)', 'MemberApiController.php'],
        ['public function downloadLetter(Request $request, int $letterId) {',
         "$letter = Letter::where('id', \$letterId)->where('member_id', \$request->user()->id)->first()"], '2'],
      ['', ['if (!$letter)'], '3'],
      ['', ["return \$this->error('Surat tidak ditemukan', 404)"], '4'],
      ['', ["$path = $letter->pdf_path ? storage_path('app/' . $letter->pdf_path) : null"], '5'],
      ['', ['if (!$path || !file_exists($path))'], '6'],
      ['', ["return \$this->error('File PDF belum tersedia', 404)"], '7'],
      ['', ["$filename = 'surat_' . str_replace('/', '-', \$letter->nomor_surat) . '.pdf'",
            'return response()->download($path, $filename)'], '8'],
      [['Rest Client (Nuxt.js)', 'Surat.vue'],
        ['if (!res.ok)'], '9'],
      ['', ["alert('PDF belum tersedia. Hubungi sekretariat gereja.')",
            'return'], '10'],
      ['', ['const blob = await res.blob()',
            'const url  = URL.createObjectURL(blob)',
            "const a    = document.createElement('a')",
            'a.href = url',
            'a.download = `surat-${letter.nomor_surat.replace(/\\//g, \'-\')}.pdf`',
            'a.click()', 'URL.revokeObjectURL(url)'], '11'],
      ['', ["} catch {", "alert('Gagal mengunduh PDF.')"], '12'],
      ['', ['} finally {', 'downloadingId.value = null', '}'], '13'],
    ],
    flowPaths: [
      'Jalur utama (unduh berhasil): 1 → 2 → 3 → 5 → 6 → 8 → 9 → 11 → 13',
      'Jalur percabangan (error jaringan, catch): 1 → 12 → 13',
      'Jalur percabangan (surat tidak ditemukan, 404): 3 → 4 → 9 → 10 → 13',
      'Jalur percabangan (file PDF tidak tersedia, 404): 6 → 7 → 9 → 10 → 13',
      'Jalur percabangan (respons HTTP selain 200, misal 500): 8 → 9 → 10 → 13',
    ],
    vg: { e:16, n:13, p:4, result:5, pNodes:'node 1, 3, 6, dan 9' },
    paths: [
      ['Path 1', '1 - 2 - 3 - 5 - 6 - 8 - 9 - 11 - 13', 'surat dan file ditemukan, PDF berhasil diunduh'],
      ['Path 2', '1 - 12 - 13', 'terjadi error jaringan saat fetch, pesan gagal ditampilkan'],
      ['Path 3', '1 - 2 - 3 - 4 - 9 - 10 - 13', 'surat tidak ditemukan atau bukan milik pengguna, 404'],
      ['Path 4', '1 - 2 - 3 - 5 - 6 - 7 - 9 - 10 - 13', 'surat ditemukan, file PDF belum tersimpan di server'],
      ['Path 5', '1 - 2 - 3 - 5 - 6 - 8 - 9 - 10 - 13', 'server mengembalikan status HTTP selain 200 (mis. 5xx)'],
    ],
  },

  // ── CFG 6 ────────────────────────────────────────────────────────────────
  {
    title: '6. Pengujian White Box Script Put_UpdateBiodata',
    scriptName: 'Put_UpdateBiodata',
    tableRows: [
      [['Rest Client (Nuxt.js)', 'EditProfile.vue'],
        ['const saveProfile = async () => {',
         'loading.value = true', 'apiError.value = null', 'fieldErrors.value = {}'], '1'],
      ['', ["try {",
            "await request('/me', { method: 'PUT', body: {",
            "  nama_lengkap: form.value.nama_lengkap,",
            "  alamat:       form.value.alamat,",
            "  no_telepon:   form.value.no_telepon } })"], '2'],
      [['Rest Server (PHP)', 'MemberApiController.php'],
        ['public function update(UpdateMemberBiodataRequest $request): JsonResponse {',
         '<<UpdateMemberBiodataRequest memvalidasi input secara otomatis sebelum method dijalankan'], '3'],
      ['', ['<<Jika validasi gagal: return 422 — diteruskan ke catch klien'], '4'],
      ['', ["$request->user()->update(\$request->validated())",
            "return \$this->success(MemberResource::make(\$request->user()->fresh()),",
            "  'Biodata berhasil diperbarui')"], '5'],
      [['Rest Client (Nuxt.js)', 'EditProfile.vue'],
        ['saved.value = true', "setTimeout(() => router.push('/profile'), 1200)"], '6'],
      ['', ["} catch (err: any) {", 'if (err.data?.errors)'], '7'],
      ['', ["fieldErrors.value = err.data.errors",
            '<<error validasi per field, 422'], '8'],
      ['', ['} else {', "apiError.value = err.data?.message ?? 'Gagal menyimpan perubahan.'"], '9'],
      ['', ['} finally {', 'loading.value = false', '}'], '10'],
    ],
    flowPaths: [
      'Jalur utama (update berhasil): 1 → 2 → 3 → 5 → 6 → 10',
      'Jalur percabangan (validasi gagal, 422): 3 → 4 → 7 → 8 → 10',
      'Jalur percabangan (error jaringan atau server): 2 → 7 → 9 → 10',
      'Jalur percabangan (validasi gagal, format error tak standar): 3 → 4 → 7 → 9 → 10',
    ],
    vg: { e:12, n:10, p:3, result:4, pNodes:'node 2, 3, dan 7' },
    paths: [
      ['Path 1', '1 - 2 - 3 - 5 - 6 - 10', 'validasi lulus, biodata diperbarui, redirect ke profil'],
      ['Path 2', '1 - 2 - 3 - 4 - 7 - 8 - 10', 'validasi field gagal, error per field 422 ditampilkan'],
      ['Path 3', '1 - 2 - 7 - 9 - 10', 'error jaringan atau server non-422, pesan error umum ditampilkan'],
      ['Path 4', '1 - 2 - 3 - 4 - 7 - 9 - 10', 'validasi gagal, respons server tidak dalam format field errors standar'],
    ],
  },

  // ── CFG 7 ────────────────────────────────────────────────────────────────
  {
    title: '7. Pengujian White Box Script Post_Pengajuan',
    scriptName: 'Post_Pengajuan',
    tableRows: [
      [['Rest Client (Nuxt.js)', 'pages/pengajuan.vue'],
        ['async function submit() {',
         'formError.value  = null', 'fieldErrors.value = {}', 'submitting.value  = true'], '1'],
      ['', ['const payload: Record<string, string | number> = { letter_type: selectedType.value }',
            "if (selectedType === 'surat_tugas_pelayanan') { ... }",
            "else if (selectedType === 'surat_pengantar') { ... }",
            'else if (...) { ... }',
            '<<membangun payload sesuai jenis surat yang dipilih'], '2'],
      ['', ["try {",
            "await request('/me/pengajuan', { method: 'POST', body: payload })"], '3'],
      [['Rest Server (PHP)', 'PengajuanController.php'],
        ['public function store(Request $request): JsonResponse {',
         '$allTypes = LetterTemplateService::getLetterTypes()',
         '$member   = $request->user()',
         "$request->validate(['letter_type' => 'required|in:...'])"], '4'],
      ['', ["<<Jika letter_type tidak valid: return 422 — diteruskan ke catch klien"], '5'],
      ['', ['$typeRules = match ($type) { ... }',
            '$request->validate($typeRules)'], '6'],
      ['', ['<<Jika validasi field khusus gagal: return 422 — diteruskan ke catch klien'], '7'],
      ['', ["$data = [ 'member_id'  => \$member->id,",
            "           'letter_type' => \$type,",
            "           'tipe_surat'  => \$allTypes[\$type],",
            "           'status'      => 'Dalam Proses', ... ]"], '8'],
      ['', ["if (\$type === 'surat_pengajuan_pernikahan')"], '9'],
      ['', ["$pria   = Member::where('id_jemaat', \$request->id_jemaat_pria)->firstOrFail()",
            "$wanita = Member::where('id_jemaat', \$request->id_jemaat_wanita)->firstOrFail()",
            "$data['member_pria_id']   = \$pria->id",
            "$data['member_wanita_id'] = \$wanita->id"], '10'],
      ['', ['$pengajuan = PengajuanSurat::create($data)',
            "return \$this->created(\$pengajuan, 'Pengajuan surat berhasil dikirim dan sedang diproses.')"], '11'],
      [['Rest Client (Nuxt.js)', 'pages/pengajuan.vue'],
        ['submitted.value = true', 'resetForm()', 'await refreshHistory()'], '12'],
      ['', ["} catch (err: unknown) {",
            'const e = err as { status?: number; data?: { errors?: ...; message?: string } }',
            'if (e?.status === 422 && e.data?.errors)'], '13'],
      ['', ['fieldErrors.value = e.data.errors',
            '<<pesan validasi per field'], '14'],
      ['', ['} else {',
            "formError.value = e?.data?.message ?? 'Terjadi kesalahan. Coba lagi.'"], '15'],
      ['', ['} finally {', 'submitting.value = false', '}'], '16'],
    ],
    flowPaths: [
      'Jalur utama (berhasil, bukan pernikahan): 1 → 2 → 3 → 4 → 6 → 8 → 9 → 11 → 12 → 16',
      'Jalur percabangan (berhasil, tipe pernikahan): 9 → 10 → 11',
      'Jalur percabangan (letter_type tidak valid, 422): 4 → 5 → 13 → 14',
      'Jalur percabangan (validasi field khusus gagal, 422): 6 → 7 → 13 → 14',
      'Jalur percabangan (error jaringan / server non-422): 3 → 13 → 15 → 16',
      'Jalur percabangan (validasi gagal, format error tidak standar): 7 → 13 → 15',
    ],
    vg: { e:20, n:16, p:5, result:6, pNodes:'node 3, 4, 6, 9, dan 13' },
    paths: [
      ['Path 1', '1 - 2 - 3 - 4 - 6 - 8 - 9 - 11 - 12 - 16', 'pengajuan berhasil, tipe bukan pernikahan'],
      ['Path 2', '1 - 2 - 3 - 4 - 6 - 8 - 9 - 10 - 11 - 12 - 16', 'pengajuan berhasil, tipe pernikahan, lookup member pria & wanita'],
      ['Path 3', '1 - 2 - 3 - 4 - 5 - 13 - 14 - 16', 'letter_type tidak valid, 422 dengan pesan validasi per field'],
      ['Path 4', '1 - 2 - 3 - 4 - 6 - 7 - 13 - 14 - 16', 'validasi field khusus gagal, 422 dengan pesan validasi per field'],
      ['Path 5', '1 - 2 - 3 - 13 - 15 - 16', 'error jaringan atau server error non-422, formError umum ditampilkan'],
      ['Path 6', '1 - 2 - 3 - 4 - 6 - 7 - 13 - 15 - 16', 'validasi field gagal, respons server tidak dalam format field errors standar'],
    ],
  },
];

// ─────────────────────────────────────────────────────────────────────────────
// BUILD SECTION per CFG
// ─────────────────────────────────────────────────────────────────────────────
function buildSection(c, idx) {
  const elems = [];

  elems.push(H1(c.title));
  elems.push(sp(60));

  // ── Script & Node table ──
  elems.push(H2(`Tabel Script & Node — ${c.scriptName}`));
  elems.push(scriptTable(c.tableRows));
  elems.push(sp(120));

  // ── Logika CFG ──
  elems.push(H2(`Logika Control Flow Graph — ${c.scriptName}`));
  elems.push(Pj([R('Representasi Logika Graf:', {bold:true})]));
  for (const fp of c.flowPaths) {
    const arrow = '→';
    // Bold the node numbers (digits connected by arrows) part
    elems.push(new Paragraph({ spacing:{after:60}, indent:{left:360},
      children:[
        new TextRun({ text:'•  ', font:TNR, size:22 }),
        new TextRun({ text:fp, font:TNR, size:22 }),
      ]}));
  }
  elems.push(sp(100));

  // ── Cyclomatic Complexity ──
  elems.push(H2(`Cyclomatic Complexity & Independent Paths — ${c.scriptName}`));
  elems.push(Pj([R(`Berikut merupakan perhitungan cyclomatic complexity untuk script ${c.scriptName}:`)]));
  elems.push(sp(40));

  // Formula block
  for (const line of [
    'V(G) = E - N + 2',
    `V(G) = ${c.vg.e} - ${c.vg.n} + 2`,
    `V(G) = ${c.vg.result}`,
  ]) {
    elems.push(Pc([new TextRun({ text: line, font: CODE, size: 22, bold: line.includes(`${c.vg.result}`) })]));
  }
  elems.push(sp(60));

  elems.push(Pj([
    R('Jumlah edge (E) = ', {bold:false}), R(String(c.vg.e), {bold:true}),
    R('  |  Jumlah node (N) = ', {bold:false}), R(String(c.vg.n), {bold:true}),
    R('  |  Jumlah predicate node (P) = ', {bold:false}), R(String(c.vg.p), {bold:true}),
    R(`  (${c.vg.pNodes})`),
  ]));
  elems.push(sp(80));

  // Also show P+1 formula
  elems.push(Pj([R('Verifikasi dengan metode predikat:', {bold:true})]));
  for (const line of [
    'V(G) = P + 1',
    `V(G) = ${c.vg.p} + 1`,
    `V(G) = ${c.vg.result}`,
  ]) {
    elems.push(Pc([new TextRun({ text: line, font: CODE, size: 22, bold: line.includes(`${c.vg.result}`) })]));
  }
  elems.push(sp(80));

  elems.push(Pj([R(`Hasil independent path pada flowgraph script ${c.scriptName} dijabarkan sebagai berikut:`)]));
  elems.push(sp(40));

  // Path table: Path | Urutan Node | Keterangan
  const PW = [700, 3200, 4037]; // = 7937
  const pathHeader = new TableRow({ children:[
    TH('Jalur', PW[0]), TH('Urutan Node', PW[1]), TH('Keterangan', PW[2])
  ]});
  const pathRows = c.paths.map(([pid, seq, keterangan]) =>
    new TableRow({ children:[
      TDc(pid, PW[0]),
      TD(seq, PW[1]),
      TD(keterangan, PW[2]),
    ]})
  );
  elems.push(mkTable([pathHeader, ...pathRows], PW));
  elems.push(sp(80));

  // Page break after each section except last
  if (idx < cfgs.length - 1) {
    elems.push(new Paragraph({ children:[new PageBreak()] }));
  }

  return elems;
}

// ─────────────────────────────────────────────────────────────────────────────
// ASSEMBLE DOCUMENT
// ─────────────────────────────────────────────────────────────────────────────
const children = [];

// ── Title page ──
children.push(
  sp(200),
  Pc([R('PENGUJIAN WHITE BOX', {bold:true, size:36})]),
  Pc([R('Basis Path Testing', {bold:true, size:28})]),
  Pc([R('Aplikasi JemaatSahala — Nuxt.js + Laravel API', {size:24})]),
  sp(80),
  Pc([R('Sistem Sekretariat Jemaat GPdI', {size:22, italics:true})]),
  sp(200),
  new Paragraph({ children:[new PageBreak()] }),
);

// ── Ringkasan awal ──
children.push(H1('Ringkasan Cyclomatic Complexity'));
children.push(summaryTable(cfgs));
children.push(sp(100));
children.push(Pj([
  R('Keterangan: '), R('N', {bold:true}), R(' = jumlah node, '),
  R('E', {bold:true}), R(' = jumlah edge/busur, '),
  R('P', {bold:true}), R(' = jumlah predicate node (decision node), '),
  R('V(G)', {bold:true}), R(' = Cyclomatic Complexity, '),
  R('Path', {bold:true}), R(' = jumlah jalur independen.'),
]));
children.push(new Paragraph({ children:[new PageBreak()] }));

// ── Per-CFG sections ──
for (let i = 0; i < cfgs.length; i++) {
  children.push(...buildSection(cfgs[i], i));
}

// ── Tabel ringkasan ulang di akhir ──
children.push(new Paragraph({ children:[new PageBreak()] }));
children.push(H1('Ringkasan Cyclomatic Complexity'));
children.push(summaryTable(cfgs));
children.push(sp(100));
children.push(Pj([R('Total seluruh pengujian: '), R(String(cfgs.reduce((s,c)=>s+c.paths.length,0)), {bold:true}),
  R(' jalur independen dari '), R(String(cfgs.length), {bold:true}), R(' fitur yang diuji.')]));

// ─────────────────────────────────────────────────────────────────────────────
// DOCUMENT CONFIG
// ─────────────────────────────────────────────────────────────────────────────
const doc = new Document({
  styles: {
    default: { document: { run: { font: TNR, size: 22 } } },
    paragraphStyles: [
      { id:'Heading1', name:'Heading 1', basedOn:'Normal', next:'Normal', quickFormat:true,
        run:{ size:28, bold:true, font:TNR },
        paragraph:{ spacing:{before:240,after:120}, outlineLevel:0 } },
      { id:'Heading2', name:'Heading 2', basedOn:'Normal', next:'Normal', quickFormat:true,
        run:{ size:24, bold:true, font:TNR },
        paragraph:{ spacing:{before:180,after:80}, outlineLevel:1 } },
      { id:'Heading3', name:'Heading 3', basedOn:'Normal', next:'Normal', quickFormat:true,
        run:{ size:22, bold:true, font:TNR },
        paragraph:{ spacing:{before:140,after:60}, outlineLevel:2 } },
    ],
  },
  sections: [{
    properties: {
      page: {
        size:   { width:11906, height:16838 },
        margin: { top:3*CM, right:3*CM, bottom:3*CM, left:4*CM },
      },
    },
    headers: {
      default: new Header({ children:[
        new Paragraph({ alignment:AlignmentType.RIGHT,
          border:{ bottom:{ style:BorderStyle.SINGLE, size:4, color:'2E4057' } },
          children:[new TextRun({ text:'Pengujian White Box – Basis Path Testing | JemaatSahala', font:TNR, size:18, color:'666666' })] })
      ]}),
    },
    footers: {
      default: new Footer({ children:[
        new Paragraph({ alignment:AlignmentType.CENTER,
          children:[
            new TextRun({ text:'Halaman ', font:TNR, size:18, color:'666666' }),
            new TextRun({ children:[PageNumber.CURRENT], font:TNR, size:18, color:'666666' }),
          ] })
      ]}),
    },
    children,
  }],
});

// ─────────────────────────────────────────────────────────────────────────────
// WRITE
// ─────────────────────────────────────────────────────────────────────────────
const OUT = path.join(__dirname, 'whitebox-testing.docx');
Packer.toBuffer(doc).then(buf => {
  fs.writeFileSync(OUT, buf);
  console.log('OK: ' + OUT);
}).catch(err => { console.error('FAIL:', err.message); process.exit(1); });
