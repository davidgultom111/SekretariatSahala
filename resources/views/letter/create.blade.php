@extends('layouts.app')

@section('title', 'Buat Surat')

@section('content')
<div>
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Buat {{ $type }}</h1>

    <div class="bg-white rounded-lg shadow-md p-8 max-w-4xl">
        <form method="POST" action="{{ route('letter.store') }}" class="space-y-6">
            @csrf

            <input type="hidden" name="tipe_surat" value="{{ $type }}">

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
                    <label for="nomor_surat" class="block text-sm font-medium text-gray-700">Nomor Surat</label>
                    <input type="text" id="nomor_surat" name="nomor_surat" value="{{ old('nomor_surat') }}" required
                        placeholder="Contoh: 001/SK/2026"
                        class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('nomor_surat')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
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
                    <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan (Opsional)</label>
                    <input type="text" id="keterangan" name="keterangan" value="{{ old('keterangan') }}"
                        placeholder="Catatan tambahan"
                        class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('keterangan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="isi_surat" class="block text-sm font-medium text-gray-700">Isi Surat</label>
                <textarea id="isi_surat" name="isi_surat" rows="10" required
                    placeholder="Tuliskan isi surat di sini..."
                    class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono text-sm">{{ old('isi_surat') }}</textarea>
                @error('isi_surat')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                <p class="text-sm text-blue-800">
                    <strong>Tip:</strong> Anda dapat menggunakan format standar untuk surat {{ $type }}. Pastikan semua informasi jemaat sudah terdaftar dengan benar.
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
