@extends('layouts.app')

@section('title', 'Jenis Surat')

@section('content')
<div>
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Pilih Jenis Surat</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @php
            $icons = [
                'surat_tugas_pelayanan' => '☩',
                'surat_pengantar' => '📮',
                'surat_keterangan_jemaat_aktif' => '✅',
                'surat_nilai_sekolah' => '📚',
                'surat_pengajuan_baptisan' => '⛪',
                'surat_pengajuan_penyerahan_anak' => '👶',
                'surat_pengajuan_pernikahan' => '💍',
            ];
        @endphp

        @foreach ($types as $typeKey => $typeName)
            <a href="{{ route('letter.create', $typeKey) }}" 
               class="block p-6 bg-white rounded-lg shadow-md hover:shadow-xl transition transform hover:scale-105 border-2 border-transparent hover:border-blue-900">
                <div class="text-5xl mb-4 text-center">
                    {{ $icons[$typeKey] ?? '📄' }}
                </div>
                <h3 class="text-center font-bold text-gray-900 text-lg">{{ $typeName }}</h3>
                <p class="text-center text-gray-500 text-sm mt-2">Klik untuk membuat surat</p>
            </a>
        @endforeach
    </div>

    <div class="mt-10">
        <a href="{{ route('letter.index') }}" class="inline-block px-6 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500 transition font-semibold">
            Lihat Semua Surat
        </a>
    </div>
</div>
@endsection
