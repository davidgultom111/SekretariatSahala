<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\API\BaseController;
use App\Models\Pengumuman;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PengumumanController extends BaseController
{
    public function index(): JsonResponse
    {
        $pengumuman = Pengumuman::orderByDesc('tanggal_mulai')->orderByDesc('id')->get();

        return $this->success($pengumuman);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'judul'         => 'required|string|max:200',
            'isi'           => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_mulai',
            'aktif'         => 'nullable|boolean',
        ]);

        $pengumuman = Pengumuman::create($validated);

        return $this->created($pengumuman);
    }

    public function update(Request $request, Pengumuman $pengumuman): JsonResponse
    {
        $validated = $request->validate([
            'judul'         => 'sometimes|required|string|max:200',
            'isi'           => 'sometimes|required|string',
            'tanggal_mulai' => 'sometimes|required|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_mulai',
            'aktif'         => 'nullable|boolean',
        ]);

        $pengumuman->update($validated);

        return $this->success($pengumuman->fresh());
    }

    public function destroy(Pengumuman $pengumuman): JsonResponse
    {
        $pengumuman->delete();

        return $this->noContent();
    }
}
