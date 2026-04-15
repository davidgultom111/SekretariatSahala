@extends('layouts.app')

@section('title', 'Detail Surat')

@section('content')
<div>
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Detail Surat</h1>
        <div class="space-x-3">
            <a href="{{ route('letter.print', $letter) }}" target="_blank" class="inline-block px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold">
                🖨️ Cetak
            </a>
            <a href="{{ route('letter.pdf', $letter) }}" class="inline-block px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold">
                📄 Download PDF
            </a>
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
                @if ($letter->letter_type === 'surat_pengantar')
                    <h2 class="text-lg font-bold text-gray-900 mb-4">📝 Keperluan Surat Pengantar</h2>
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200 whitespace-pre-wrap font-serif text-gray-800">
                        {{ $letter->keterangan }}
                    </div>
                @else
                    <h2 class="text-lg font-bold text-gray-900 mb-2">Keterangan</h2>
                    <p class="text-gray-700">{{ $letter->keterangan }}</p>
                @endif
            </div>
        @endif

        {{-- Tampilkan data tugas pelayanan jika letter type-nya surat_tugas_pelayanan --}}
        @if ($letter->letter_type === 'surat_tugas_pelayanan' && $letter->tujuan_tugas)
            <div class="mb-8 pb-8 border-b border-gray-300">
                <h2 class="text-lg font-bold text-gray-900 mb-4">📋 Data Tugas Pelayanan</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-500">Tanggal Mulai Tugas</p>
                        <p class="text-lg font-semibold text-gray-900">
                            {{ $letter->tgl_mulai_tugas ? \Carbon\Carbon::parse($letter->tgl_mulai_tugas)->translatedFormat('d F Y') : '-' }}
                        </p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-500">Tanggal Akhir Tugas</p>
                        <p class="text-lg font-semibold text-gray-900">
                            {{ $letter->tgl_akhir_tugas ? \Carbon\Carbon::parse($letter->tgl_akhir_tugas)->translatedFormat('d F Y') : '-' }}
                        </p>
                    </div>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-2">Tujuan Tugas Pelayanan</p>
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-300 whitespace-pre-wrap font-serif text-gray-800">
                        {{ $letter->tujuan_tugas }}
                    </div>
                </div>
            </div>
        @endif

        {{-- Tampilkan data keterangan jemaat aktif jika letter type-nya surat_keterangan_jemaat_aktif --}}
        @if ($letter->letter_type === 'surat_keterangan_jemaat_aktif' && $letter->tahun_bergabung)
            <div class="mb-8 pb-8 border-b border-gray-300">
                <h2 class="text-lg font-bold text-gray-900 mb-4">✝️ Data Keterangan Jemaat Aktif</h2>
                <div class="bg-purple-50 p-4 rounded-lg border-2 border-purple-200">
                    <p class="text-sm text-gray-500 mb-1">Tahun Bergabung Jemaat</p>
                    <p class="text-2xl font-bold text-purple-900">{{ $letter->tahun_bergabung }}</p>
                    <p class="mt-2 text-sm text-gray-600">Jemaat telah aktif dan mengikuti kegiatan ibadah sejak tahun {{ $letter->tahun_bergabung }}</p>
                </div>
            </div>
        @endif

        {{-- Tampilkan data nilai sekolah jika letter type-nya surat_nilai_sekolah --}}
        @if ($letter->letter_type === 'surat_nilai_sekolah' && $letter->asal_sekolah)
            <div class="mb-8 pb-8 border-b border-gray-300">
                <h2 class="text-lg font-bold text-gray-900 mb-4">📚 Data Nilai Sekolah</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                        <p class="text-sm text-gray-500">Asal Sekolah/Tempat Belajar</p>
                        <p class="text-lg font-semibold text-green-900">{{ $letter->asal_sekolah }}</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                        <p class="text-sm text-gray-500">Kelas/Tingkat</p>
                        <p class="text-lg font-semibold text-green-900">{{ $letter->kelas }}</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                        <p class="text-sm text-gray-500">Semester</p>
                        <p class="text-lg font-semibold text-green-900">{{ $letter->semester }}</p>
                    </div>
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                        <p class="text-sm text-gray-500">Nilai Agama</p>
                        <p class="text-2xl font-bold text-blue-900">{{ $letter->nilai ?? 90 }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Tampilkan data penyerahan anak jika letter type-nya surat_pengajuan_penyerahan_anak --}}
        @if ($letter->letter_type === 'surat_pengajuan_penyerahan_anak' && $letter->nama_anak)
            <div class="mb-8 pb-8 border-b border-gray-300">
                <h2 class="text-lg font-bold text-gray-900 mb-4">👨‍👩‍👧 Data Penyerahan Anak</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-pink-50 p-4 rounded-lg border border-pink-200">
                        <p class="text-sm text-gray-500">Nama Ayah</p>
                        <p class="text-lg font-semibold text-pink-900">{{ $letter->nama_ayah }}</p>
                    </div>
                    <div class="bg-pink-50 p-4 rounded-lg border border-pink-200">
                        <p class="text-sm text-gray-500">Nama Ibu</p>
                        <p class="text-lg font-semibold text-pink-900">{{ $letter->nama_ibu }}</p>
                    </div>
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                        <p class="text-sm text-gray-500">Nama Anak</p>
                        <p class="text-lg font-semibold text-blue-900">{{ $letter->nama_anak }}</p>
                    </div>
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                        <p class="text-sm text-gray-500">Tempat Lahir</p>
                        <p class="text-lg font-semibold text-blue-900">{{ $letter->tempat_lahir_anak }}</p>
                    </div>
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200 md:col-span-2">
                        <p class="text-sm text-gray-500">Tanggal Lahir</p>
                        <p class="text-lg font-semibold text-blue-900">{{ $letter->tanggal_lahir_anak ? \Carbon\Carbon::parse($letter->tanggal_lahir_anak)->translatedFormat('d F Y') : '-' }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Tampilkan data pernikahan jika letter type-nya surat_pengajuan_pernikahan --}}
        @if ($letter->letter_type === 'surat_pengajuan_pernikahan' && $letter->member_pria_id && $letter->member_wanita_id)
            <div class="mb-8 pb-8 border-b border-gray-300">
                <h2 class="text-lg font-bold text-gray-900 mb-4">💍 Data Pengajuan Pernikahan</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="bg-red-50 p-4 rounded-lg border-2 border-red-300">
                        <h3 class="font-bold text-red-900 mb-3">👨 Mempelai Pria</h3>
                        <div class="space-y-2 text-sm">
                            <div>
                                <p class="text-gray-500">Nama</p>
                                <p class="font-semibold text-red-900">{{ $letter->memberPria->nama_lengkap }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">No. Identitas</p>
                                <p class="font-semibold text-red-900">{{ $letter->memberPria->no_identitas }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Tempat Lahir</p>
                                <p class="font-semibold text-red-900">{{ $letter->memberPria->tempat_lahir }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Tanggal Lahir</p>
                                <p class="font-semibold text-red-900">{{ $letter->memberPria->tanggal_lahir ? \Carbon\Carbon::parse($letter->memberPria->tanggal_lahir)->translatedFormat('d F Y') : '-' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Alamat</p>
                                <p class="font-semibold text-red-900">{{ $letter->memberPria->alamat }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-red-50 p-4 rounded-lg border-2 border-red-300">
                        <h3 class="font-bold text-red-900 mb-3">👩 Mempelai Wanita</h3>
                        <div class="space-y-2 text-sm">
                            <div>
                                <p class="text-gray-500">Nama</p>
                                <p class="font-semibold text-red-900">{{ $letter->memberWanita->nama_lengkap }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">No. Identitas</p>
                                <p class="font-semibold text-red-900">{{ $letter->memberWanita->no_identitas }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Tempat Lahir</p>
                                <p class="font-semibold text-red-900">{{ $letter->memberWanita->tempat_lahir }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Tanggal Lahir</p>
                                <p class="font-semibold text-red-900">{{ $letter->memberWanita->tanggal_lahir ? \Carbon\Carbon::parse($letter->memberWanita->tanggal_lahir)->translatedFormat('d F Y') : '-' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Alamat</p>
                                <p class="font-semibold text-red-900">{{ $letter->memberWanita->alamat }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-orange-50 p-4 rounded-lg border-2 border-orange-300">
                    <p class="text-sm text-gray-500 mb-1">Tanggal Pernikahan Rencana</p>
                    <p class="text-2xl font-bold text-orange-900">{{ $letter->tanggal_pernikahan ? \Carbon\Carbon::parse($letter->tanggal_pernikahan)->translatedFormat('d F Y') : '-' }}</p>
                </div>
            </div>
        @endif

        <div class="mt-8 p-4 bg-blue-50 rounded-lg border border-blue-200 text-sm text-blue-800">
            <p><strong>Catatan:</strong> Untuk melihat isi surat yang telah di-generate otomatis, silakan klik tombol <strong>"Cetak"</strong> atau <strong>"Download PDF"</strong> di atas.</p>
        </div>

        <div class="mt-8 p-4 bg-gray-100 rounded-lg text-sm text-gray-600">
            <p><strong>Dibuat:</strong> {{ $letter->created_at->format('d F Y H:i') }}</p>
            <p><strong>Diupdate:</strong> {{ $letter->updated_at->format('d F Y H:i') }}</p>
        </div>
    </div>
</div>
@endsection
