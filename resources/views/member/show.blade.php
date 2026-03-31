@extends('layouts.app')

@section('title', $member->nama_lengkap)

@section('content')
<div>
    <div class="mb-8 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Detail Data Jemaat</h1>
        <div class="flex gap-3">
            <a href="{{ route('member.edit', $member) }}" class="px-6 py-2 bg-yellow-400 text-gray-900 rounded-lg hover:bg-yellow-500 transition font-semibold">
                Edit
            </a>
            <form method="POST" action="{{ route('member.destroy', $member) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold">
                    Hapus
                </button>
            </form>
            <a href="{{ route('member.index') }}" class="px-6 py-2 bg-gray-400 text-gray-900 rounded-lg hover:bg-gray-500 transition font-semibold">
                Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Informasi Jemaat -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow-md p-8">
            <div class="mb-6 pb-6 border-b-2 border-blue-900">
                <h2 class="text-xl font-bold text-blue-900 mb-6">📋 Informasi Pribadi</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-500">Nama Lengkap</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $member->nama_lengkap }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Jenis Kelamin</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $member->jenis_kelamin }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Tempat Lahir</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $member->tempat_lahir }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Tanggal Lahir</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $member->tanggal_lahir?->format('d/m/Y') ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="mb-6 pb-6 border-b-2 border-blue-900">
                <h2 class="text-xl font-bold text-blue-900 mb-6">🏠 Alamat & Kontak</h2>
                
                <div class="mb-4">
                    <p class="text-sm text-gray-500 mb-2">Alamat Lengkap</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $member->alamat }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-500">No. Telepon</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $member->no_telepon }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Status</p>
                        <p class="text-lg font-semibold">
                            @if($member->status_aktif === 'Aktif')
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-bold">✓ Aktif</span>
                            @elseif($member->status_aktif === 'Tidak Aktif')
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-bold">⊘ Tidak Aktif</span>
                            @else
                                <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-bold">✕ Dipindahkan</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Letters Generated -->
        <div>
            <div class="bg-white rounded-lg shadow-md p-8 border-l-4 border-yellow-400">
                <h2 class="text-lg font-bold text-gray-900 mb-6">📄 Surat Jemaat</h2>
                
                @if($member->letters->count() > 0)
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        @foreach($member->letters as $letter)
                            <div class="p-4 bg-yellow-50 rounded-lg border-l-4 border-yellow-400 hover:bg-yellow-100 transition">
                                <p class="font-semibold text-gray-900">{{ $letter->jenis_surat }}</p>
                                <p class="text-sm text-gray-600">{{ $letter->created_at->format('d/m/Y') }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-gray-500 py-6">Belum ada surat yang dibuat</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
