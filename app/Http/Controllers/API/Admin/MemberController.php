<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\API\BaseController;
use App\Http\Requests\API\Admin\StoreMemberRequest;
use App\Http\Requests\API\Admin\UpdateMemberRequest;
use App\Http\Resources\MemberResource;
use App\Models\Member;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

// MemberController menangani CRUD data jemaat oleh admin
class MemberController extends BaseController
{
    // API menangani daftar semua jemaat dengan filter pencarian, status, dan pagination
    public function index(Request $request): JsonResponse
    {
        $members = Member::when($request->query('search'), fn ($q, $s) =>
                // Filter berdasarkan nama lengkap atau id_jemaat
                $q->where('nama_lengkap', 'LIKE', "%{$s}%")
                  ->orWhere('id_jemaat', 'LIKE', "%{$s}%")
            )
            // Filter berdasarkan status aktif jika parameter status dikirim
            ->when($request->query('status'), fn ($q, $s) => $q->where('status_aktif', $s))
            ->orderBy('nama_lengkap')
            ->paginate($request->query('per_page', 15));

        return $this->success(MemberResource::collection($members)->response()->getData(true));
    }

    // API menangani create jemaat baru dengan password default '12345'
    public function store(StoreMemberRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Set password default untuk jemaat baru
        $data['password'] = Hash::make('12345');

        $member = Member::create($data);

        return $this->created(MemberResource::make($member), 'Data jemaat berhasil ditambahkan');
    }

    // API menangani tampil detail satu jemaat berdasarkan ID
    public function show(Member $member): JsonResponse
    {
        return $this->success(MemberResource::make($member));
    }

    // API menangani update data jemaat (hash ulang password jika diubah)
    public function update(UpdateMemberRequest $request, Member $member): JsonResponse
    {
        $data = $request->validated();

        // Hash password baru jika password dikirim dalam request
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $member->update($data);

        // Reload data terbaru dari database setelah update
        return $this->success(MemberResource::make($member->fresh()), 'Data jemaat berhasil diperbarui');
    }

    // API menangani hapus permanen data jemaat (force delete, melewati soft delete)
    public function destroy(Member $member): JsonResponse
    {
        $member->forceDelete();
        return $this->noContent();
    }
}
