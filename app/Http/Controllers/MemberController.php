<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        
        $query = Member::orderBy('nama_lengkap', 'asc');
        
        if ($search) {
            $query->where('nama_lengkap', 'like', '%' . $search . '%');
        }
        
        $members = $query->paginate(10);
        
        return view('member.index', compact('members', 'search'));
    }

    public function create()
    {
        return view('member.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'tanggal_lahir' => 'required|date',
            'tempat_lahir' => 'required|string|max:255',
            'alamat' => 'required|string|max:500',
            'no_telepon' => 'required|string|max:20',
            'status_aktif' => 'required|in:Aktif,Tidak Aktif,Dipindahkan',
        ]);

        Member::create($validated);

        return redirect()->route('member.index')->with('success', 'Data jemaat berhasil ditambahkan');
    }

    public function show(Member $member)
    {
        return view('member.show', compact('member'));
    }

    public function edit(Member $member)
    {
        return view('member.edit', compact('member'));
    }

    public function update(Request $request, Member $member)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'tanggal_lahir' => 'required|date',
            'tempat_lahir' => 'required|string|max:255',
            'alamat' => 'required|string|max:500',
            'no_telepon' => 'required|string|max:20',
            'status_aktif' => 'required|in:Aktif,Tidak Aktif,Dipindahkan',
        ]);

        $member->update($validated);

        return redirect()->route('member.index')->with('success', 'Data jemaat berhasil diperbarui');
    }

    public function destroy(Member $member)
    {
        // Check for related letters BEFORE deletion to inform user
        $letterCount = $member->letters()->count();
        $marriageLettersCount = $member->marriageLettersAsPria()->count() + $member->marriageLettersAsWanita()->count();
        $totalRelated = $letterCount + $marriageLettersCount;
        
        // Delete member (cascade will delete related letters automatically)
        $member->delete();
        
        $message = 'Data jemaat berhasil dihapus';
        if ($totalRelated > 0) {
            $message .= " (beserta {$totalRelated} surat terkait)";
        }
        
        return redirect()->route('member.index')->with('success', $message);
    }
}
