@extends('layouts.app')

@section('title', 'Buat Surat')

@section('content')
<div>
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Buat {{ $allTypes[$type] ?? $type }}</h1>

    <div class="bg-white rounded-lg shadow-md p-8 max-w-4xl">
        <form method="POST" action="{{ route('letter.store') }}" class="space-y-6">
            @csrf

            <input type="hidden" name="letter_type" value="{{ $type }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="member_id" class="block text-sm font-medium text-gray-700">Pilih Jemaat</label>
                    <select id="member_id" name="member_id" required
                        class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">-- Pilih Jemaat --</option>
                        @foreach ($members as $member)
                            <option value="{{ $member->id }}" {{ old('member_id') == $member->id ? 'selected' : '' }}>
                                {{ $member->nama_lengkap }} ({{ $member->no_identitas }})
                            </option>
                        @endforeach
                    </select>
                    @error('member_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nomor_surat_preview" class="block text-sm font-medium text-gray-700">Nomor Surat (Otomatis)</label>
                    <div class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 font-mono text-lg font-bold text-blue-900">
                        {{ $previewNomorSurat ?? 'Loading...' }}
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Nomor surat akan otomatis terisi saat simpan</p>
                </div>

                <div>
                    <label for="tanggal_surat" class="block text-sm font-medium text-gray-700">Tanggal Surat</label>
                    <input type="date" id="tanggal_surat" name="tanggal_surat" value="{{ old('tanggal_surat', date('Y-m-d')) }}" required
                        class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('tanggal_surat')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    @if($type === 'surat_pengantar')
                        <label for="keterangan" class="block text-sm font-medium text-gray-700">Keperluan Surat Pengantar <span class="text-red-600">*</span></label>
                        <textarea id="keterangan" name="keterangan" rows="4" required
                            placeholder="Contoh: Jemaat pindah gereja, Butuh referensi kerja Kristen, dll"
                            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('keterangan') }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">Jelaskan keperluan/tujuan surat pengantar ini</p>
                    @else
                        <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan (Opsional)</label>
                        <input type="text" id="keterangan" name="keterangan" value="{{ old('keterangan') }}"
                            placeholder="Catatan tambahan"
                            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @endif
                    @error('keterangan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Kolom khusus untuk Surat Tugas Pelayanan --}}
            @if($type === 'surat_tugas_pelayanan')
            <div class="bg-yellow-50 p-6 rounded-lg border-2 border-yellow-300">
                <h3 class="text-lg font-bold text-yellow-900 mb-4">📋 Data Tugas Pelayanan</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="tgl_mulai_tugas" class="block text-sm font-medium text-gray-700">Tanggal Mulai Tugas <span class="text-red-600">*</span></label>
                        <input type="date" id="tgl_mulai_tugas" name="tgl_mulai_tugas" 
                            value="{{ old('tgl_mulai_tugas') }}" required
                            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('tgl_mulai_tugas')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tgl_akhir_tugas" class="block text-sm font-medium text-gray-700">Tanggal Akhir Tugas <span class="text-red-600">*</span></label>
                        <input type="date" id="tgl_akhir_tugas" name="tgl_akhir_tugas" 
                            value="{{ old('tgl_akhir_tugas') }}" required
                            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('tgl_akhir_tugas')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6">
                    <label for="tujuan_tugas" class="block text-sm font-medium text-gray-700">Tujuan Tugas Pelayanan <span class="text-red-600">*</span></label>
                    <textarea id="tujuan_tugas" name="tujuan_tugas" rows="6" required
                        placeholder="Jelaskan tujuan dan uraian tugas pelayanan..."
                        class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent font-serif text-sm">{{ old('tujuan_tugas') }}</textarea>
                    @error('tujuan_tugas')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            @endif

            {{-- Kolom khusus untuk Surat Keterangan Jemaat Aktif --}}
            @if($type === 'surat_keterangan_jemaat_aktif')
            <div class="bg-purple-50 p-6 rounded-lg border-2 border-purple-300">
                <h3 class="text-lg font-bold text-purple-900 mb-4">✝️ Data Keterangan Jemaat Aktif</h3>
                
                <div>
                    <label for="tahun_bergabung" class="block text-sm font-medium text-gray-700">Tahun Bergabung Jemaat <span class="text-red-600">*</span></label>
                    <input type="number" id="tahun_bergabung" name="tahun_bergabung" 
                        min="1900" max="{{ date('Y') }}" 
                        value="{{ old('tahun_bergabung') }}" required
                        placeholder="Contoh: 2020"
                        class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <p class="mt-1 text-xs text-gray-500">Masukkan tahun saat jemaat pertama kali bergabung</p>
                    @error('tahun_bergabung')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            @endif

            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                <p class="text-sm text-blue-800">
                    <strong>Info:</strong> Nomor surat akan otomatis terbentuk dengan format XXX/GPdI/SA/[Tipe Surat]/{{ date('Y') }}. Data jemaat (Nama, No. Telepon, Alamat, dll) dan isi surat akan otomatis ditampilkan saat pencetakan berdasarkan jenis surat yang dipilih dan data yang terdaftar.
                </p>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="px-8 py-3 bg-blue-900 text-white rounded-lg hover:bg-blue-800 transition font-semibold">
                    Simpan Surat
                </button>
                <a href="{{ route('letter.types') }}" class="px-8 py-3 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition font-semibold">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
