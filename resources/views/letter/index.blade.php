@extends('layouts.app')

@section('title', 'Fitur Tersimpan - Surat')

@section('content')
<div>
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Fitur Tersimpan</h1>
        <a href="{{ route('letter.types') }}" class="px-6 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition font-semibold">
            + Buat Surat Baru
        </a>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form method="GET" action="{{ route('letter.search') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Cari Nama Jemaat</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}"
                    placeholder="Ketik nama jemaat..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div>
                <label for="letter_type" class="block text-sm font-medium text-gray-700 mb-2">Filter Jenis Surat</label>
                <select id="letter_type" name="letter_type"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">-- Semua Jenis --</option>
                    @if(isset($types))
                        @foreach($types as $typeKey => $typeName)
                            <option value="{{ $typeKey }}" {{ request('letter_type') === $typeKey ? 'selected' : '' }}>
                                {{ $typeName }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="flex items-end gap-3">
                <button type="submit" class="px-6 py-2 bg-blue-900 text-white rounded-lg hover:bg-blue-800 transition font-semibold flex-1">
                    Cari
                </button>
                <a href="{{ route('letter.index') }}" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition font-semibold">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Letters Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="w-full">
            <thead class="bg-blue-900 text-white">
                <tr>
                    <th class="px-6 py-3 text-left">Nama Jemaat</th>
                    <th class="px-6 py-3 text-left">Jenis Surat</th>
                    <th class="px-6 py-3 text-left">Nomor Surat</th>
                    <th class="px-6 py-3 text-left">Tanggal</th>
                    <th class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($letters as $letter)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium">{{ $letter->member->nama_lengkap }}</td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold">
                                {{ $letter->tipe_surat }}
                            </span>
                        </td>
                        <td class="px-6 py-4">{{ $letter->nomor_surat }}</td>
                        <td class="px-6 py-4">{{ $letter->tanggal_surat->format('d F Y') }}</td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('letter.show', $letter) }}" class="text-blue-600 hover:text-blue-800 mx-2">Lihat</a>
                            <form method="POST" action="{{ route('letter.destroy', $letter) }}" class="inline-block"
                                  onsubmit="return confirm('Yakin ingin menghapus surat ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 mx-2">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada surat</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $letters->links() }}
    </div>
</div>
@endsection
