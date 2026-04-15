<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Letter;
use App\Services\LetterTemplateService;
use Illuminate\Http\Request;

class LetterController extends Controller
{
    public function index()
    {
        $letters = Letter::with('member')->orderBy('created_at', 'desc')->paginate(10);
        $types = LetterTemplateService::getLetterTypes();
        return view('letter.index', compact('letters', 'types'));
    }

    public function types()
    {
        $types = LetterTemplateService::getLetterTypes();
        return view('letter.types', compact('types'));
    }

    public function create($type)
    {
        $allTypes = LetterTemplateService::getLetterTypes();
        
        if (!array_key_exists($type, $allTypes)) {
            return redirect()->route('letter.types')->with('error', 'Tipe surat tidak dikenal');
        }

        $members = Member::where('status_aktif', 'Aktif')->get();
        
        // Get preview nomor surat otomatis (without incrementing counter)
        $previewNomorSurat = LetterTemplateService::getLetterNumberPreview($type);

        return view('letter.create', compact('type', 'members', 'allTypes', 'previewNomorSurat'));
    }

    public function store(Request $request)
    {
        $allTypes = LetterTemplateService::getLetterTypes();

        $rules = [
            'member_id' => 'required|exists:members,id',
            'letter_type' => 'required|in:' . implode(',', array_keys($allTypes)),
            'tanggal_surat' => 'required|date',
            'keterangan' => 'nullable|string',
        ];

        // Validasi khusus untuk surat tugas pelayanan
        if ($request->input('letter_type') === 'surat_tugas_pelayanan') {
            $rules['tgl_mulai_tugas'] = 'required|date';
            $rules['tgl_akhir_tugas'] = 'required|date|after_or_equal:tgl_mulai_tugas';
            $rules['tujuan_tugas'] = 'required|string|min:10';
        }

        // Validasi khusus untuk surat pengantar - keterangan wajib
        if ($request->input('letter_type') === 'surat_pengantar') {
            $rules['keterangan'] = 'required|string|min:10';
        }

        // Validasi khusus untuk surat keterangan jemaat aktif
        if ($request->input('letter_type') === 'surat_keterangan_jemaat_aktif') {
            $rules['tahun_bergabung'] = 'required|integer|min:1900|max:' . date('Y');
        }

        // Validasi khusus untuk surat nilai sekolah
        if ($request->input('letter_type') === 'surat_nilai_sekolah') {
            $rules['asal_sekolah'] = 'required|string|min:3';
            $rules['kelas'] = 'required|string|min:1';
            $rules['semester'] = 'required|string|min:1';
            $rules['nilai'] = 'nullable|integer|min:0|max:100';
        }

        // Validasi khusus untuk surat penyerahan anak
        if ($request->input('letter_type') === 'surat_pengajuan_penyerahan_anak') {
            $rules['nama_ayah'] = 'required|string|min:3';
            $rules['nama_ibu'] = 'required|string|min:3';
            $rules['nama_anak'] = 'required|string|min:1';
            $rules['tempat_lahir_anak'] = 'required|string|min:3';
            $rules['tanggal_lahir_anak'] = 'required|date';
        }

        // Validasi khusus untuk surat pengajuan pernikahan
        if ($request->input('letter_type') === 'surat_pengajuan_pernikahan') {
            $rules['member_id'] = 'nullable'; // member_id tidak wajib untuk pernikahan
            $rules['member_pria_id'] = 'required|exists:members,id';
            $rules['member_wanita_id'] = 'required|exists:members,id';
            $rules['tanggal_pernikahan'] = 'required|date|after_or_equal:tanggal_surat';
        }

        $validated = $request->validate($rules);

        $typeName = $allTypes[$validated['letter_type']];
        
        // Generate nomor surat otomatis
        $nomorSurat = LetterTemplateService::generateLetterNumber($validated['letter_type']);
        
        // Untuk surat pernikahan, gunakan member_pria_id sebagai member_id utama
        $memberId = $validated['letter_type'] === 'surat_pengajuan_pernikahan' 
            ? $validated['member_pria_id'] 
            : $validated['member_id'];
        
        $letterData = [
            'member_id' => $memberId,
            'tipe_surat' => $typeName,
            'letter_type' => $validated['letter_type'],
            'nomor_surat' => $nomorSurat,
            'tanggal_surat' => $validated['tanggal_surat'],
            'keterangan' => $validated['keterangan'] ?? null,
        ];

        // Tambah data tugas pelayanan jika jenis suratnya adalah surat tugas pelayanan
        if ($validated['letter_type'] === 'surat_tugas_pelayanan') {
            $letterData['tgl_mulai_tugas'] = $validated['tgl_mulai_tugas'];
            $letterData['tgl_akhir_tugas'] = $validated['tgl_akhir_tugas'];
            $letterData['tujuan_tugas'] = $validated['tujuan_tugas'];
        }

        // Tambah data keterangan jemaat aktif jika jenis suratnya adalah surat keterangan jemaat aktif
        if ($validated['letter_type'] === 'surat_keterangan_jemaat_aktif') {
            $letterData['tahun_bergabung'] = $validated['tahun_bergabung'];
        }

        // Tambah data nilai sekolah jika jenis suratnya adalah surat nilai sekolah
        if ($validated['letter_type'] === 'surat_nilai_sekolah') {
            $letterData['asal_sekolah'] = $validated['asal_sekolah'];
            $letterData['kelas'] = $validated['kelas'];
            $letterData['semester'] = $validated['semester'];
            $letterData['nilai'] = $validated['nilai'] ?? 90;
        }

        // Tambah data penyerahan anak jika jenis suratnya adalah surat penyerahan anak
        if ($validated['letter_type'] === 'surat_pengajuan_penyerahan_anak') {
            $letterData['nama_ayah'] = $validated['nama_ayah'];
            $letterData['nama_ibu'] = $validated['nama_ibu'];
            $letterData['nama_anak'] = $validated['nama_anak'];
            $letterData['tempat_lahir_anak'] = $validated['tempat_lahir_anak'];
            $letterData['tanggal_lahir_anak'] = $validated['tanggal_lahir_anak'];
        }

        // Tambah data pernikahan jika jenis suratnya adalah surat pengajuan pernikahan
        if ($validated['letter_type'] === 'surat_pengajuan_pernikahan') {
            $letterData['member_pria_id'] = $validated['member_pria_id'];
            $letterData['member_wanita_id'] = $validated['member_wanita_id'];
            $letterData['tanggal_pernikahan'] = $validated['tanggal_pernikahan'];
        }

        $letter = Letter::create($letterData);

        return redirect()->route('letter.show', $letter)->with('success', 'Surat berhasil dibuat dengan nomor: ' . $nomorSurat);
    }

    public function show(Letter $letter)
    {
        return view('letter.show', compact('letter'));
    }

    public function print(Letter $letter)
    {
        return view('letter.print', compact('letter'));
    }

    public function pdf(Letter $letter)
    {
        // Replace "/" dengan "-" untuk membuat filename yang valid
        $number = str_replace('/', '-', $letter->nomor_surat);
        $filename = "surat-{$number}.pdf";
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('letter.print', compact('letter'));
        
        // Set paper size untuk match dengan cetak browser
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download($filename);
    }

    public function destroy(Letter $letter)
    {
        $letter->delete();
        return redirect()->route('letter.index')->with('success', 'Surat berhasil dihapus');
    }

    public function search(Request $request)
    {
        $query = Letter::with('member');

        if ($request->has('search') && $request->search) {
            $query->whereHas('member', function ($q) use ($request) {
                $q->where('nama_lengkap', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('letter_type') && $request->letter_type) {
            $query->where('letter_type', $request->letter_type);
        }

        $letters = $query->orderBy('created_at', 'desc')->paginate(10);
        $types = LetterTemplateService::getLetterTypes();

        return view('letter.index', compact('letters', 'types'));
    }
}

