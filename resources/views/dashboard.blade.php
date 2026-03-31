@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div>
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Dashboard</h1>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-900">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Jemaat</p>
                    <p class="text-4xl font-bold text-blue-900">{{ $totalMembers }}</p>
                </div>
                <svg class="w-12 h-12 text-blue-900 opacity-20" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM9 12a6 6 0 11-12 0 6 6 0 0112 0z"></path>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Jemaat Aktif</p>
                    <p class="text-4xl font-bold text-green-600">{{ $activeMembbers }}</p>
                </div>
                <svg class="w-12 h-12 text-green-500 opacity-20" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Surat</p>
                    <p class="text-4xl font-bold text-yellow-600">{{ $totalLetters }}</p>
                </div>
                <svg class="w-12 h-12 text-yellow-500 opacity-20" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.657 6.243A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Quick Actions</h2>
            <div class="space-y-2">
                <a href="{{ route('member.create') }}" class="block w-full text-left px-4 py-3 bg-blue-900 text-white rounded-lg hover:bg-blue-800 transition font-semibold">
                    + Tambah Data Jemaat
                </a>
                <a href="{{ route('letter.types') }}" class="block w-full text-left px-4 py-3 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition font-semibold">
                    + Buat Surat Baru
                </a>
            </div>
        </div>

        <!-- Recent Letters -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Surat Terbaru</h2>
            <div class="space-y-3 max-h-56 overflow-y-auto">
                @forelse ($recentLetters as $letter)
                    <div class="flex items-between pb-3 border-b border-gray-200">
                        <div class="flex-1">
                            <p class="font-medium text-gray-800">{{ $letter->tipe_surat }}</p>
                            <p class="text-sm text-gray-500">{{ $letter->member->nama_lengkap }}</p>
                            <p class="text-xs text-gray-400">{{ $letter->tanggal_surat->format('d M Y') }}</p>
                        </div>
                        <a href="{{ route('letter.show', $letter) }}" class="text-blue-600 hover:text-blue-800">Lihat</a>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">Belum ada surat</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
