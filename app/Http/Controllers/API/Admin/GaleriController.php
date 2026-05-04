<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\API\BaseController;
use App\Models\GaleriFoto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

// GaleriController menangani upload dan hapus foto galeri gereja oleh admin
class GaleriController extends BaseController
{
    // API menangani daftar semua foto galeri diurutkan berdasarkan field urutan
    public function index(): JsonResponse
    {
        $galeri = GaleriFoto::orderBy('urutan')->orderBy('id')->get();

        return $this->success($galeri);
    }

    // API menangani upload foto baru ke storage publik dengan validasi format dan ukuran file
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'judul'    => 'required|string|max:150',
            'deskripsi' => 'nullable|string',
            'foto'     => 'required|image|mimes:jpeg,jpg,png,webp|max:5120',
            'urutan'   => 'nullable|integer|min:0',
        ]);

        // Simpan file foto ke disk public dan catat path-nya di database
        $path = $request->file('foto')->store('galeri', 'public');

        $galeri = GaleriFoto::create([
            'judul'     => $request->judul,
            'deskripsi' => $request->deskripsi,
            'foto'      => $path,
            'urutan'    => $request->integer('urutan', 0),
        ]);

        return $this->created($galeri);
    }

    // API menangani hapus foto galeri beserta file fisiknya dari storage publik
    public function destroy(GaleriFoto $galeri): JsonResponse
    {
        Storage::disk('public')->delete($galeri->foto);
        $galeri->delete();

        return $this->noContent();
    }
}
