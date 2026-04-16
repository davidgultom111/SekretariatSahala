@extends('layouts.app')

@section('title', 'Data Diri Jemaat')

@section('content')
<div>
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Data Diri Jemaat</h1>
        <a href="{{ route('member.create') }}" class="px-6 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition font-semibold">
            + Tambah Jemaat
        </a>
    </div>

    <!-- Search Bar -->
    <div class="mb-6">
        <form method="GET" action="{{ route('member.index') }}" class="flex gap-2">
            <input type="text" name="search" placeholder="Cari nama jemaat..." 
                   value="{{ $search ?? '' }}"
                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold">
                Cari
            </button>
            @if($search)
                <a href="{{ route('member.index') }}" class="px-6 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500 transition font-semibold">
                    Reset
                </a>
            @endif
        </form>
    </div>
        <table class="w-full">
            <thead class="bg-blue-900 text-white">
                <tr>
                    <th class="px-6 py-3 text-left">Nama</th>
                    <th class="px-6 py-3 text-left">Jenis Kelamin</th>
                    <th class="px-6 py-3 text-left">No. Telepon</th>
                    <th class="px-6 py-3 text-left">Status</th>
                    <th class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($members as $member)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">{{ $member->nama_lengkap }}</td>
                        <td class="px-6 py-4">{{ $member->jenis_kelamin }}</td>
                        <td class="px-6 py-4">{{ $member->no_telepon }}</td>
                        <td class="px-6 py-4">
                            @if($member->status_aktif === 'Aktif')
                                <span class="px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800">✓ Aktif</span>
                            @elseif($member->status_aktif === 'Tidak Aktif')
                                <span class="px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800">⊘ Tidak Aktif</span>
                            @else
                                <span class="px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-800">✕ Dipindahkan</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('member.show', $member) }}" class="text-blue-600 hover:text-blue-800 mx-2">Lihat</a>
                            <a href="{{ route('member.edit', $member) }}" class="text-yellow-600 hover:text-yellow-800 mx-2">Edit</a>
                            <form method="POST" action="{{ route('member.destroy', $member) }}" class="inline-block"
                                  onsubmit="return confirm('Yakin ingin menghapus?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 mx-2">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            @if($search)
                                Tidak ada hasil pencarian untuk "<strong>{{ $search }}</strong>"
                            @else
                                Belum ada data jemaat
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $members->links() }}
    </div>
</div>
@endsection
