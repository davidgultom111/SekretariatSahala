<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\API\BaseController;
use App\Models\JadwalPelayanan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JadwalController extends BaseController
{
    public function index(): JsonResponse
    {
        $jadwals = JadwalPelayanan::orderBy('urutan')->orderBy('id')->get();

        return $this->success($jadwals);
    }

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

    public function destroy(JadwalPelayanan $jadwal): JsonResponse
    {
        $jadwal->delete();

        return $this->noContent();
    }
}
