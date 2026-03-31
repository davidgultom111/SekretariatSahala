@extends('layouts.app')

@section('title', 'Edit Data Jemaat')

@section('content')
<div>
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Edit Data Jemaat</h1>

    <div class="bg-white rounded-lg shadow-md p-8 max-w-2xl">
        <form method="POST" action="{{ route('member.update', $member) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="nama_lengkap" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                <input type="text" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap', $member->nama_lengkap) }}" required
                    class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                @error('nama_lengkap')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
                    <select id="jenis_kelamin" name="jenis_kelamin" required
                        class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">-- Pilih --</option>
                        <option value="Laki-laki" {{ old('jenis_kelamin', $member->jenis_kelamin) === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ old('jenis_kelamin', $member->jenis_kelamin) === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('jenis_kelamin')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                    <input type="date" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir', $member->tanggal_lahir?->format('Y-m-d')) }}" required
                        class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('tanggal_lahir')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="tempat_lahir" class="block text-sm font-medium text-gray-700">Tempat Lahir</label>
                <input type="text" id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir', $member->tempat_lahir) }}" required
                    class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                @error('tempat_lahir')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat</label>
                <textarea id="alamat" name="alamat" rows="3" required
                    class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('alamat', $member->alamat) }}</textarea>
                @error('alamat')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="no_telepon" class="block text-sm font-medium text-gray-700">No. Telepon</label>
                    <input type="text" id="no_telepon" name="no_telepon" value="{{ old('no_telepon', $member->no_telepon) }}" required
                        class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('no_telepon')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status_aktif" class="block text-sm font-medium text-gray-700">Status</label>
                    <select id="status_aktif" name="status_aktif" required
                        class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="Aktif" {{ old('status_aktif', $member->status_aktif) === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Tidak Aktif" {{ old('status_aktif', $member->status_aktif) === 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                        <option value="Dipindahkan" {{ old('status_aktif', $member->status_aktif) === 'Dipindahkan' ? 'selected' : '' }}>Dipindahkan</option>
                    </select>
                    @error('status_aktif')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="px-8 py-3 bg-blue-900 text-white rounded-lg hover:bg-blue-800 transition font-semibold">
                    Update
                </button>
                <a href="{{ route('member.index') }}" class="px-8 py-3 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition font-semibold">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
