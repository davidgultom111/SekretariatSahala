<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Letter;
use Illuminate\Http\Request;

class LetterController extends Controller
{
    public function index()
    {
        $letters = Letter::with('member')->paginate(10);
        return view('letter.index', compact('letters'));
    }

    public function types()
    {
        $types = [
            'Surat Baptisan',
            'Surat Pernikahan',
            'Surat Serah Nikah',
            'Surat Kematian',
            'Surat Keluar Jemaat',
            'Surat Masuk Jemaat',
            'Surat Keterangan Jemaat',
            'Surat Rekomendasi'
        ];
        return view('letter.types', compact('types'));
    }

    public function create($type)
    {
        $members = Member::where('status_aktif', 'Aktif')->get();
        return view('letter.create', compact('type', 'members'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'tipe_surat' => 'required|in:Surat Baptisan,Surat Pernikahan,Surat Serah Nikah,Surat Kematian,Surat Keluar Jemaat,Surat Masuk Jemaat,Surat Keterangan Jemaat,Surat Rekomendasi',
            'nomor_surat' => 'required|unique:letters',
            'tanggal_surat' => 'required|date',
            'keterangan' => 'nullable|string',
            'isi_surat' => 'required|string',
        ]);

        Letter::create($validated);

        return redirect()->route('letter.index')->with('success', 'Surat berhasil dibuat');
    }

    public function show(Letter $letter)
    {
        return view('letter.show', compact('letter'));
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

        if ($request->has('tipe_surat') && $request->tipe_surat) {
            $query->where('tipe_surat', $request->tipe_surat);
        }

        $letters = $query->paginate(10);
        $types = [
            'Surat Baptisan',
            'Surat Pernikahan',
            'Surat Serah Nikah',
            'Surat Kematian',
            'Surat Keluar Jemaat',
            'Surat Masuk Jemaat',
            'Surat Keterangan Jemaat',
            'Surat Rekomendasi'
        ];

        return view('letter.index', compact('letters', 'types'));
    }
}
