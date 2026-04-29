<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\API\BaseController;
use App\Http\Requests\API\Admin\StoreLetterRequest;
use App\Http\Resources\LetterResource;
use App\Models\Letter;
use App\Services\LetterTemplateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LetterController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        $letters = Letter::with('member')
            ->when($request->query('search'), fn ($q, $s) =>
                $q->whereHas('member', fn ($mq) =>
                    $mq->where('nama_lengkap', 'LIKE', "%{$s}%")
                )->orWhere('nomor_surat', 'LIKE', "%{$s}%")
            )
            ->when($request->query('letter_type'), fn ($q, $t) => $q->where('letter_type', $t))
            ->orderByDesc('created_at')
            ->paginate($request->query('per_page', 15));

        return $this->success(LetterResource::collection($letters)->response()->getData(true));
    }

    public function store(StoreLetterRequest $request): JsonResponse
    {
        $validated   = $request->validated();
        $allTypes    = LetterTemplateService::getLetterTypes();
        $nomorSurat  = LetterTemplateService::generateLetterNumber($validated['letter_type']);

        $memberId = $validated['letter_type'] === 'surat_pengajuan_pernikahan'
            ? $validated['member_pria_id']
            : $validated['member_id'];

        $letterData = [
            'member_id'     => $memberId,
            'tipe_surat'    => $allTypes[$validated['letter_type']],
            'letter_type'   => $validated['letter_type'],
            'nomor_surat'   => $nomorSurat,
            'tanggal_surat' => $validated['tanggal_surat'],
            'keterangan'    => $validated['keterangan'] ?? null,
        ];

        $typeFields = [
            'surat_tugas_pelayanan'           => ['tgl_mulai_tugas', 'tgl_akhir_tugas', 'tujuan_tugas'],
            'surat_keterangan_jemaat_aktif'   => ['tahun_bergabung'],
            'surat_nilai_sekolah'             => ['asal_sekolah', 'kelas', 'semester', 'nilai'],
            'surat_pengajuan_penyerahan_anak' => ['nama_ayah', 'nama_ibu', 'nama_anak', 'tempat_lahir_anak', 'tanggal_lahir_anak'],
            'surat_pengajuan_pernikahan'      => ['member_pria_id', 'member_wanita_id', 'tanggal_pernikahan'],
        ];

        foreach ($typeFields[$validated['letter_type']] ?? [] as $field) {
            if (isset($validated[$field])) {
                $letterData[$field] = $validated[$field];
            }
        }

        $letter = Letter::create($letterData);

        return $this->created(LetterResource::make($letter->load('member')), 'Surat berhasil dibuat');
    }

    public function show(Letter $letter): JsonResponse
    {
        return $this->success(LetterResource::make($letter->load(['member', 'memberPria', 'memberWanita'])));
    }

    public function destroy(Letter $letter): JsonResponse
    {
        if ($letter->pdf_path) {
            $path = storage_path('app/' . $letter->pdf_path);
            if (file_exists($path)) {
                unlink($path);
            }
        }

        $letter->delete();
        return $this->noContent();
    }

    public function downloadPdf(Letter $letter): BinaryFileResponse|JsonResponse|HttpResponse
    {
        $path = $letter->pdf_path ? storage_path('app/' . $letter->pdf_path) : null;

        if ($path && file_exists($path)) {
            $filename = 'surat_' . str_replace('/', '-', $letter->nomor_surat) . '.pdf';
            return response()->download($path, $filename);
        }

        // Generate, simpan ke disk, update pdf_path agar has_pdf = true
        $filename    = 'surat_' . str_replace('/', '-', $letter->nomor_surat) . '.pdf';
        $storagePath = 'letters/' . $filename;
        $fullPath    = storage_path('app/' . $storagePath);

        if (!is_dir(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'letter.print',
            ['letter' => $letter->load(['member', 'memberPria', 'memberWanita'])]
        )->setPaper('A4', 'portrait');

        $pdf->save($fullPath);
        $letter->update(['pdf_path' => $storagePath]);

        return response()->download($fullPath, $filename);
    }
}
