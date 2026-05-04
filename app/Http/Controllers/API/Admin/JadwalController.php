<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\API\BaseController;
use App\Models\JadwalPelayanan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// JadwalController menangani CRUD jadwal pelayanan gereja oleh admin
class JadwalController extends BaseController
{
    // API menangani daftar semua jadwal pelayanan diurutkan berdasarkan field urutan
    public function index(): JsonResponse
    {
        $jadwals = JadwalPelayanan::orderBy('urutan')->orderBy('id')->get();

        return $this->success($jadwals);
    }

    // API menangani tambah jadwal pelayanan baru dengan validasi field wajib
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama_kegiatan' => 'required|string|max:100',
            'kategori'      => 'required|string|max:50',
            'deskripsi'     => 'nullable|string',
            'hari'          => 'required|string|max:20',
            'waktu'         => 'required|string|max:10',
            'urutan'        => 'nullable|integer|min:0',
            'aktif'         => 'nullable|boolean',
        ]);

        $jadwal = JadwalPelayanan::create($validated);

        return $this->created($jadwal);
    }

    // API menangani update jadwal pelayanan dengan partial update (semua field opsional)
    public function update(Request $request, JadwalPelayanan $jadwal): JsonResponse
    {
        $validated = $request->validate([
            'nama_kegiatan' => 'sometimes|required|string|max:100',
            'kategori'      => 'sometimes|required|string|max:50',
            'deskripsi'     => 'nullable|string',
            'hari'          => 'sometimes|required|string|max:20',
            'waktu'         => 'sometimes|required|string|max:10',
            'urutan'        => 'nullable|integer|min:0',
            'aktif'         => 'nullable|boolean',
        ]);

        $jadwal->update($validated);

        return $this->success($jadwal->fresh());
    }

    // API menangani hapus jadwal pelayanan dari database
    public function destroy(JadwalPelayanan $jadwal): JsonResponse
    {
        $jadwal->delete();

        return $this->noContent();
    }
}
