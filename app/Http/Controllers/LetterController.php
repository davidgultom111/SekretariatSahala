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
        $letters = Letter::with('member')->paginate(10);
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

        $validated = $request->validate($rules);

        $typeName = $allTypes[$validated['letter_type']];
        
        // Generate nomor surat otomatis
        $nomorSurat = LetterTemplateService::generateLetterNumber($validated['letter_type']);
        
        $letterData = [
            'member_id' => $validated['member_id'],
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
        // This will generate PDF - you'll need to install dompdf
        $number = $letter->nomor_surat;
        $filename = "surat-{$number}.pdf";
        
        return \Barryvdh\DomPDF\Facade\Pdf::loadView('letter.print', compact('letter'))
            ->download($filename);
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

        $letters = $query->paginate(10);
        $types = LetterTemplateService::getLetterTypes();

        return view('letter.index', compact('letters', 'types'));
    }
}

