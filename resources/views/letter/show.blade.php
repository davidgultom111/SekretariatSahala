@extends('layouts.app')

@section('title', 'Detail Surat')

@section('content')
<div>
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Detail Surat</h1>
        <div class="space-x-3">
            <a href="{{ route('letter.index') }}" class="inline-block px-6 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500 transition font-semibold">
                Kembali
            </a>
            <form method="POST" action="{{ route('letter.destroy', $letter) }}" class="inline-block"
                  onsubmit="return confirm('Yakin ingin menghapus surat ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold">
                    Hapus
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-8 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <div>
                <h2 class="text-lg font-bold text-gray-900 mb-4">Informasi Surat</h2>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-500">Jenis Surat</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $letter->tipe_surat }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Nomor Surat</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $letter->nomor_surat }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Tanggal Surat</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $letter->tanggal_surat->format('d F Y') }}</p>
                    </div>
                </div>
            </div>

            <div>
                <h2 class="text-lg font-bold text-gray-900 mb-4">Data Jemaat</h2>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-500">Nama</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $letter->member->nama_lengkap }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">No. Identitas</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $letter->member->no_identitas }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">No. Telepon</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $letter->member->no_telepon }}</p>
                    </div>
                </div>
            </div>
        </div>

        @if ($letter->keterangan)
            <div class="mb-8 pb-8 border-b border-gray-300">
                <h2 class="text-lg font-bold text-gray-900 mb-2">Keterangan</h2>
                <p class="text-gray-700">{{ $letter->keterangan }}</p>
            </div>
        @endif

        <div class="mb-4">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Isi Surat</h2>
            <div class="bg-gray-50 p-6 rounded-lg border border-gray-300 whitespace-pre-wrap font-serif text-gray-800">
                {{ $letter->isi_surat }}
            </div>
        </div>

        <div class="mt-8 p-4 bg-gray-100 rounded-lg text-sm text-gray-600">
            <p><strong>Dibuat:</strong> {{ $letter->created_at->format('d F Y H:i') }}</p>
            <p><strong>Diupdate:</strong> {{ $letter->updated_at->format('d F Y H:i') }}</p>
        </div>
    </div>
</div>
@endsection
