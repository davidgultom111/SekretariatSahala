# This script generates XML for member pages blackbox testing tables

def make_rpr(bold=False):
    b = '<w:b/><w:bCs/>' if bold else '<w:b w:val="false"/><w:bCs w:val="false"/>'
    return f'''<w:rPr>
                <w:rFonts w:ascii="Times New Roman" w:cs="Times New Roman" w:eastAsia="Times New Roman" w:hAnsi="Times New Roman"/>
                {b}
                <w:sz w:val="24"/>
                <w:szCs w:val="24"/>
              </w:rPr>'''

def make_tc(width, text, align='left', bold=False, shaded=False):
    shd = '<w:shd w:fill="D9D9D9" w:val="clear"/>' if shaded else ''
    return f'''        <w:tc>
          <w:tcPr>
            <w:tcW w:type="dxa" w:w="{width}"/>
            <w:tcBorders>
              <w:top w:val="single" w:color="000000" w:sz="4"/>
              <w:left w:val="single" w:color="000000" w:sz="4"/>
              <w:bottom w:val="single" w:color="000000" w:sz="4"/>
              <w:right w:val="single" w:color="000000" w:sz="4"/>
            </w:tcBorders>
            {shd}
            <w:tcMar>
              <w:top w:type="dxa" w:w="60"/>
              <w:left w:type="dxa" w:w="120"/>
              <w:bottom w:type="dxa" w:w="60"/>
              <w:right w:type="dxa" w:w="120"/>
            </w:tcMar>
            <w:vAlign w:val="center"/>
          </w:tcPr>
          <w:p>
            <w:pPr>
              <w:jc w:val="{align}"/>
            </w:pPr>
            <w:r>
              {make_rpr(bold)}
              <w:t xml:space="preserve">{text}</w:t>
            </w:r>
          </w:p>
        </w:tc>'''

def make_header_row():
    return f'''      <w:tr>
        <w:trPr>
          <w:tblHeader/>
        </w:trPr>
{make_tc(2000, 'Unit Testing', 'center', True, True)}
{make_tc(3500, 'Case Testing', 'center', True, True)}
{make_tc(2572, 'Hasil yang Diharapkan', 'center', True, True)}
{make_tc(1000, 'Hasil Percobaan', 'center', True, True)}
      </w:tr>'''

def make_data_row(unit, case, expected, result='Sesuai', unit_span=False, is_first_in_group=False):
    if unit_span:
        # Empty unit cell for continuation
        unit_cell = f'''        <w:tc>
          <w:tcPr>
            <w:tcW w:type="dxa" w:w="2000"/>
            <w:tcBorders>
              <w:top w:val="single" w:color="000000" w:sz="4"/>
              <w:left w:val="single" w:color="000000" w:sz="4"/>
              <w:bottom w:val="single" w:color="000000" w:sz="4"/>
              <w:right w:val="single" w:color="000000" w:sz="4"/>
            </w:tcBorders>
            <w:tcMar>
              <w:top w:type="dxa" w:w="60"/>
              <w:left w:type="dxa" w:w="120"/>
              <w:bottom w:type="dxa" w:w="60"/>
              <w:right w:type="dxa" w:w="120"/>
            </w:tcMar>
            <w:vAlign w:val="center"/>
          </w:tcPr>
          <w:p>
            <w:pPr>
              <w:jc w:val="left"/>
            </w:pPr>
            <w:r>
              {make_rpr(False)}
              <w:t xml:space="preserve">{unit}</w:t>
            </w:r>
          </w:p>
        </w:tc>'''
    else:
        unit_cell = make_tc(2000, unit)
    return f'''      <w:tr>
{unit_cell}
{make_tc(3500, case)}
{make_tc(2572, expected)}
{make_tc(1000, result, 'center')}
      </w:tr>'''

def make_table(title, rows):
    rows_xml = make_header_row() + '\n'
    for r in rows:
        rows_xml += make_data_row(*r) + '\n'
    return f'''    <w:p>
      <w:pPr>
        <w:spacing w:after="120" w:before="240"/>
        <w:jc w:val="center"/>
      </w:pPr>
      <w:r>
        {make_rpr(True)}
        <w:t xml:space="preserve">{title}</w:t>
      </w:r>
    </w:p>
    <w:tbl>
      <w:tblPr>
        <w:tblW w:type="dxa" w:w="9072"/>
        <w:tblBorders>
          <w:top w:val="single" w:color="auto" w:sz="4"/>
          <w:left w:val="single" w:color="auto" w:sz="4"/>
          <w:bottom w:val="single" w:color="auto" w:sz="4"/>
          <w:right w:val="single" w:color="auto" w:sz="4"/>
          <w:insideH w:val="single" w:color="auto" w:sz="4"/>
          <w:insideV w:val="single" w:color="auto" w:sz="4"/>
        </w:tblBorders>
      </w:tblPr>
      <w:tblGrid>
        <w:gridCol w:w="2000"/>
        <w:gridCol w:w="3500"/>
        <w:gridCol w:w="2572"/>
        <w:gridCol w:w="1000"/>
      </w:tblGrid>
{rows_xml}    </w:tbl>'''

# Define all tables for member pages
tables = []

# Table 4.21 - Halaman Profil Saya (Member)
tables.append(make_table('Tabel 4.21 Pengujian Halaman Profil Saya', [
    ('Tampilan Profil', 'Jemaat membuka halaman profil saya', 'Menampilkan data profil pribadi (nama lengkap, ID jemaat, tanggal lahir, tempat lahir, alamat, no telepon, status aktif)', 'Sesuai'),
    ('Tombol Edit Profil', 'Jemaat menekan tombol edit profil', 'Menampilkan form edit data profil dengan data yang sudah terisi', 'Sesuai'),
    ('Tombol Simpan (data lengkap)', 'Jemaat mengubah data profil secara lengkap dan benar lalu klik simpan', 'Menampilkan pesan data profil berhasil diperbarui', 'Sesuai'),
    ('Tombol Simpan (data tidak lengkap)', 'Jemaat mengosongkan field Nama Lengkap lalu klik simpan', 'Menampilkan pesan validasi field Nama Lengkap wajib diisi', 'Sesuai'),
    ('', 'Jemaat mengosongkan field Alamat lalu klik simpan', 'Menampilkan pesan validasi field Alamat wajib diisi', 'Sesuai'),
    ('', 'Jemaat mengosongkan semua field wajib lalu klik simpan', 'Menampilkan pesan validasi semua field wajib diisi', 'Sesuai'),
    ('Tombol Batal', 'Jemaat menekan tombol batal pada form edit', 'Membatalkan perubahan dan menampilkan data profil semula', 'Sesuai'),
]))

# Table 4.22 - Halaman Surat Saya (Member)
tables.append(make_table('Tabel 4.22 Pengujian Halaman Surat Saya', [
    ('Tampilan Halaman', 'Jemaat membuka halaman surat saya', 'Menampilkan daftar semua surat milik jemaat yang sedang login', 'Sesuai'),
    ('Tombol Search', 'Jemaat mengetikkan kata kunci pada field pencarian', 'Menampilkan hasil pencarian surat sesuai kata kunci', 'Sesuai'),
    ('', 'Jemaat mengosongkan field pencarian', 'Menampilkan semua data surat milik jemaat', 'Sesuai'),
    ('Tombol Download PDF', 'Jemaat menekan tombol download PDF pada salah satu surat', 'Mengunduh atau menampilkan file PDF surat di browser', 'Sesuai'),
    ('', 'Jemaat menekan tombol download PDF pada surat yang belum memiliki file PDF', 'Menampilkan pesan file PDF belum tersedia', 'Sesuai'),
    ('Tampilan Kosong', 'Jemaat membuka halaman surat saya saat belum ada surat', 'Menampilkan pesan belum ada surat yang diterbitkan', 'Sesuai'),
]))

# Table 4.23 - Halaman Form Pengajuan Surat (Member)
tables.append(make_table('Tabel 4.23 Pengujian Halaman Form Pengajuan Surat', [
    ('Pilih Tipe Surat', 'Jemaat memilih tipe surat dari dropdown', 'Menampilkan field tambahan sesuai tipe surat yang dipilih', 'Sesuai'),
    ('Tombol Ajukan (data lengkap)', 'Jemaat mengisi semua field secara lengkap dan benar lalu klik ajukan', 'Menampilkan pesan pengajuan surat berhasil dikirim dan menampilkan halaman history pengajuan', 'Sesuai'),
    ('Tombol Ajukan (data tidak lengkap)', 'Jemaat tidak memilih tipe surat lalu klik ajukan', 'Menampilkan pesan validasi tipe surat wajib dipilih', 'Sesuai'),
    ('', 'Jemaat mengosongkan field wajib pada tipe surat yang dipilih lalu klik ajukan', 'Menampilkan pesan validasi field wajib diisi', 'Sesuai'),
    ('', 'Jemaat mengosongkan semua field wajib lalu klik ajukan', 'Menampilkan pesan validasi semua field wajib diisi', 'Sesuai'),
    ('Tombol Reset', 'Jemaat menekan tombol reset', 'Menghapus semua isian dan menampilkan form pengajuan kosong', 'Sesuai'),
    ('Tombol Kembali', 'Jemaat menekan tombol kembali', 'Menampilkan halaman history pengajuan surat', 'Sesuai'),
]))

# Table 4.24 - Halaman History Pengajuan Saya (Member)
tables.append(make_table('Tabel 4.24 Pengujian Halaman History Pengajuan Saya', [
    ('Tampilan Halaman', 'Jemaat membuka halaman history pengajuan saya', 'Menampilkan daftar semua pengajuan surat beserta statusnya (Dalam Proses, Disetujui, Ditolak)', 'Sesuai'),
    ('Tombol Filter Status', 'Jemaat memilih filter status Dalam Proses', 'Menampilkan daftar pengajuan dengan status Dalam Proses', 'Sesuai'),
    ('', 'Jemaat memilih filter status Disetujui', 'Menampilkan daftar pengajuan dengan status Disetujui', 'Sesuai'),
    ('', 'Jemaat memilih filter status Ditolak', 'Menampilkan daftar pengajuan dengan status Ditolak', 'Sesuai'),
    ('Tombol Detail', 'Jemaat menekan tombol detail pada salah satu pengajuan', 'Menampilkan detail pengajuan surat beserta status dan catatan dari admin', 'Sesuai'),
    ('', 'Jemaat melihat detail pengajuan yang disetujui', 'Menampilkan informasi surat yang telah dibuat beserta tombol download PDF', 'Sesuai'),
    ('', 'Jemaat melihat detail pengajuan yang ditolak', 'Menampilkan status Ditolak beserta catatan alasan penolakan dari admin', 'Sesuai'),
    ('Tombol Ajukan Surat Baru', 'Jemaat menekan tombol ajukan surat baru', 'Menampilkan halaman form pengajuan surat baru', 'Sesuai'),
    ('Tampilan Kosong', 'Jemaat membuka halaman history saat belum pernah mengajukan surat', 'Menampilkan pesan belum ada pengajuan surat', 'Sesuai'),
]))

new_xml = '\n'.join(tables)

with open('docs/blackbox-testing-unpacked/word/document.xml', 'r', encoding='utf-8') as f:
    content = f.read()

insert_before = '    <w:sectPr>'
new_content = content.replace(insert_before, new_xml + '\n    ' + insert_before.strip())

with open('docs/blackbox-testing-unpacked/word/document.xml', 'w', encoding='utf-8') as f:
    f.write(new_content)

print('Done! Tables 4.21-4.24 added.')
print('New table count:', new_content.count('<w:tbl>'))
