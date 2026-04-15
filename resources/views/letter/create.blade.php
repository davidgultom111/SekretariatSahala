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
                    @elseif($type !== 'surat_nilai_sekolah' && $type !== 'surat_pengajuan_penyerahan_anak' && $type !== 'surat_pengajuan_pernikahan')
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

            {{-- Kolom khusus untuk Surat Nilai Sekolah --}}
            @if($type === 'surat_nilai_sekolah')
            <div class="bg-green-50 p-6 rounded-lg border-2 border-green-300">
                <h3 class="text-lg font-bold text-green-900 mb-4">📚 Data Nilai Sekolah</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="asal_sekolah" class="block text-sm font-medium text-gray-700">Asal Sekolah/Tempat Belajar <span class="text-red-600">*</span></label>
                        <input type="text" id="asal_sekolah" name="asal_sekolah" 
                            value="{{ old('asal_sekolah') }}" required
                            placeholder="Contoh: SMA Negeri 1 Palembang"
                            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        @error('asal_sekolah')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="kelas" class="block text-sm font-medium text-gray-700">Kelas/Tingkat <span class="text-red-600">*</span></label>
                        <input type="text" id="kelas" name="kelas" 
                            value="{{ old('kelas') }}" required
                            placeholder="Contoh: XII IPA 1"
                            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        @error('kelas')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="semester" class="block text-sm font-medium text-gray-700">Semester <span class="text-red-600">*</span></label>
                        <input type="text" id="semester" name="semester" 
                            value="{{ old('semester') }}" required
                            placeholder="Contoh: Ganjil atau Genap"
                            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        @error('semester')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="nilai" class="block text-sm font-medium text-gray-700">Nilai Agama</label>
                        <input type="number" id="nilai" name="nilai" 
                            value="{{ old('nilai', 90) }}" 
                            min="0" max="100"
                            placeholder="Contoh: 90"
                            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <p class="mt-1 text-xs text-gray-500">Default: 90. Masukkan nilai antara 0-100</p>
                        @error('nilai')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            @endif

            {{-- Kolom khusus untuk Surat Penyerahan Anak --}}
            @if($type === 'surat_pengajuan_penyerahan_anak')
            <div class="bg-pink-50 p-6 rounded-lg border-2 border-pink-300">
                <h3 class="text-lg font-bold text-pink-900 mb-4">👨‍👩‍👧 Data Penyerahan Anak</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nama_ayah" class="block text-sm font-medium text-gray-700">Nama Ayah <span class="text-red-600">*</span></label>
                        <input type="text" id="nama_ayah" name="nama_ayah" 
                            value="{{ old('nama_ayah') }}" required
                            placeholder="Contoh: Santoso"
                            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                        <p class="mt-1 text-xs text-gray-500">Nama lengkap ayah (orang pertama)</p>
                        @error('nama_ayah')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="nama_ibu" class="block text-sm font-medium text-gray-700">Nama Ibu <span class="text-red-600">*</span></label>
                        <input type="text" id="nama_ibu" name="nama_ibu" 
                            value="{{ old('nama_ibu') }}" required
                            placeholder="Contoh: Dewi Lestari"
                            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                        <p class="mt-1 text-xs text-gray-500">Nama lengkap ibu (orang kedua)</p>
                        @error('nama_ibu')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="nama_anak" class="block text-sm font-medium text-gray-700">Nama Anak <span class="text-red-600">*</span></label>
                        <input type="text" id="nama_anak" name="nama_anak" 
                            value="{{ old('nama_anak') }}" required
                            placeholder="Contoh: Budi Santoso"
                            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                        @error('nama_anak')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tempat_lahir_anak" class="block text-sm font-medium text-gray-700">Tempat Lahir Anak <span class="text-red-600">*</span></label>
                        <input type="text" id="tempat_lahir_anak" name="tempat_lahir_anak" 
                            value="{{ old('tempat_lahir_anak') }}" required
                            placeholder="Contoh: Palembang"
                            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                        @error('tempat_lahir_anak')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tanggal_lahir_anak" class="block text-sm font-medium text-gray-700">Tanggal Lahir Anak <span class="text-red-600">*</span></label>
                        <input type="date" id="tanggal_lahir_anak" name="tanggal_lahir_anak" 
                            value="{{ old('tanggal_lahir_anak') }}" required
                            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                        @error('tanggal_lahir_anak')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-4 p-3 bg-blue-50 rounded border border-blue-200">
                    <p class="text-xs text-blue-800">
                        <strong>Catatan:</strong> Isi nama ayah dan ibu sesuai data orang tua yang akan diserahkan (bisa berbeda dengan jemaat yang dipilih).
                    </p>
                </div>
            </div>
            @endif

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

            {{-- Kolom khusus untuk Surat Pengajuan Pernikahan --}}
            @if($type === 'surat_pengajuan_pernikahan')
            <div class="bg-red-50 p-6 rounded-lg border-2 border-red-300">
                <h3 class="text-lg font-bold text-red-900 mb-4">💍 Data Pengajuan Pernikahan</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="member_pria_id" class="block text-sm font-medium text-gray-700">Mempelai Pria <span class="text-red-600">*</span></label>
                        <select id="member_pria_id" name="member_pria_id" required
                            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <option value="">-- Pilih Mempelai Pria --</option>
                            @foreach ($members as $member)
                                <option value="{{ $member->id }}" data-nama="{{ $member->nama_lengkap }}" data-identitas="{{ $member->no_identitas }}" data-tempat="{{ $member->tempat_lahir }}" data-tgl="{{ $member->tanggal_lahir ?? '' }}" data-alamat="{{ $member->alamat }}" {{ old('member_pria_id') == $member->id ? 'selected' : '' }}>
                                    {{ $member->nama_lengkap }} ({{ $member->no_identitas }})
                                </option>
                            @endforeach
                        </select>
                        @error('member_pria_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="member_wanita_id" class="block text-sm font-medium text-gray-700">Mempelai Wanita <span class="text-red-600">*</span></label>
                        <select id="member_wanita_id" name="member_wanita_id" required
                            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <option value="">-- Pilih Mempelai Wanita --</option>
                            @foreach ($members as $member)
                                <option value="{{ $member->id }}" data-nama="{{ $member->nama_lengkap }}" data-identitas="{{ $member->no_identitas }}" data-tempat="{{ $member->tempat_lahir }}" data-tgl="{{ $member->tanggal_lahir ?? '' }}" data-alamat="{{ $member->alamat }}" {{ old('member_wanita_id') == $member->id ? 'selected' : '' }}>
                                    {{ $member->nama_lengkap }} ({{ $member->no_identitas }})
                                </option>
                            @endforeach
                        </select>
                        @error('member_wanita_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6">
                    <label for="tanggal_pernikahan" class="block text-sm font-medium text-gray-700">Tanggal Pernikahan <span class="text-red-600">*</span></label>
                    <input type="date" id="tanggal_pernikahan" name="tanggal_pernikahan" 
                        value="{{ old('tanggal_pernikahan') }}" required
                        class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    <p class="mt-1 text-xs text-gray-500">Tanggal rencananya upacara pernikahan akan dilaksanakan</p>
                    @error('tanggal_pernikahan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white p-4 rounded-lg border border-red-200">
                        <h4 class="font-semibold text-red-900 mb-2">📋 Data Mempelai Pria</h4>
                        <div class="text-sm text-gray-700 space-y-1">
                            <p><strong>Nama:</strong> <span id="pria_nama">-</span></p>
                            <p><strong>No. Identitas:</strong> <span id="pria_identitas">-</span></p>
                            <p><strong>Tempat Lahir:</strong> <span id="pria_tempat">-</span></p>
                            <p><strong>Tanggal Lahir:</strong> <span id="pria_tgl">-</span></p>
                            <p><strong>Alamat:</strong> <span id="pria_alamat">-</span></p>
                        </div>
                    </div>

                    <div class="bg-white p-4 rounded-lg border border-red-200">
                        <h4 class="font-semibold text-red-900 mb-2">📋 Data Mempelai Wanita</h4>
                        <div class="text-sm text-gray-700 space-y-1">
                            <p><strong>Nama:</strong> <span id="wanita_nama">-</span></p>
                            <p><strong>No. Identitas:</strong> <span id="wanita_identitas">-</span></p>
                            <p><strong>Tempat Lahir:</strong> <span id="wanita_tempat">-</span></p>
                            <p><strong>Tanggal Lahir:</strong> <span id="wanita_tgl">-</span></p>
                            <p><strong>Alamat:</strong> <span id="wanita_alamat">-</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                const memberPriaSelect = document.getElementById('member_pria_id');
                const memberWanitaSelect = document.getElementById('member_wanita_id');

                // Fungsi untuk update tampilan data
                function updateMemberDisplay(selectElement, prefix) {
                    const selected = selectElement.options[selectElement.selectedIndex];
                    document.getElementById(prefix + '_nama').textContent = selected.dataset.nama || '-';
                    document.getElementById(prefix + '_identitas').textContent = selected.dataset.identitas || '-';
                    document.getElementById(prefix + '_tempat').textContent = selected.dataset.tempat || '-';
                    document.getElementById(prefix + '_tgl').textContent = selected.dataset.tgl || '-';
                    document.getElementById(prefix + '_alamat').textContent = selected.dataset.alamat || '-';
                }

                // Event listeners
                memberPriaSelect.addEventListener('change', function() {
                    updateMemberDisplay(this, 'pria');
                });

                memberWanitaSelect.addEventListener('change', function() {
                    updateMemberDisplay(this, 'wanita');
                });

                // Initial display load (untuk old values)
                if (memberPriaSelect.value) updateMemberDisplay(memberPriaSelect, 'pria');
                if (memberWanitaSelect.value) updateMemberDisplay(memberWanitaSelect, 'wanita');
            </script>
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
