<?php

namespace App\Observers;

use App\Models\Member;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

// MemberObserver menangani event lifecycle model Member secara otomatis
class MemberObserver
{
    // API menangani generate id_jemaat otomatis saat jemaat baru akan disimpan
    public function creating(Member $member): void
    {
        // Hanya generate jika id_jemaat belum ada dan tanggal_lahir tersedia
        if (!$member->id_jemaat && $member->tanggal_lahir) {
            try {
                // Konversi ke Carbon jika nilai masih string
                $date = is_string($member->tanggal_lahir)
                    ? Carbon::createFromFormat('Y-m-d', $member->tanggal_lahir)
                    : $member->tanggal_lahir;

                if ($date) {
                    // Format id_jemaat: DDMMYYYY dari tanggal lahir
                    $id_jemaat = $date->format('dmY');

                    // Jika sudah ada id yang sama, tambahkan angka counter di belakang
                    $counter    = 1;
                    $originalId = $id_jemaat;
                    while (Member::where('id_jemaat', $id_jemaat)->exists()) {
                        $id_jemaat = $originalId . $counter;
                        $counter++;
                    }

                    $member->id_jemaat = $id_jemaat;
                }
            } catch (\Exception $e) {
                // Gagal parse tanggal tidak perlu crash, cukup log peringatan
                Log::warning('Failed to generate id_jemaat: ' . $e->getMessage());
            }
        }
    }

    // API menangani regenerasi id_jemaat jika tanggal_lahir diubah saat update
    public function updating(Member $member): void
    {
        // Hanya regenerate jika tanggal_lahir berubah dan id_jemaat tidak diubah manual
        if ($member->isDirty('tanggal_lahir') && (!$member->id_jemaat || !$member->isDirty('id_jemaat'))) {
            if ($member->tanggal_lahir) {
                try {
                    // Konversi ke Carbon jika nilai masih string
                    $date = is_string($member->tanggal_lahir)
                        ? Carbon::createFromFormat('Y-m-d', $member->tanggal_lahir)
                        : $member->tanggal_lahir;

                    if ($date) {
                        // Format baru dari tanggal lahir yang diperbarui
                        $id_jemaat = $date->format('dmY');

                        // Cek duplikat, kecualikan id milik jemaat yang sedang diupdate
                        $counter    = 1;
                        $originalId = $id_jemaat;
                        while (Member::where('id_jemaat', $id_jemaat)
                            ->where('id', '!=', $member->id)
                            ->exists()) {
                            $id_jemaat = $originalId . $counter;
                            $counter++;
                        }

                        $member->id_jemaat = $id_jemaat;
                    }
                } catch (\Exception $e) {
                    // Gagal parse tanggal tidak perlu crash, cukup log peringatan
                    Log::warning('Failed to update id_jemaat: ' . $e->getMessage());
                }
            }
        }
    }

    // Event setelah jemaat berhasil dibuat (belum digunakan)
    public function created(Member $member): void {}

    // Event setelah jemaat berhasil diupdate (belum digunakan)
    public function updated(Member $member): void {}

    // Event setelah jemaat di-soft delete (belum digunakan)
    public function deleted(Member $member): void {}

    // Event setelah jemaat dipulihkan dari soft delete (belum digunakan)
    public function restored(Member $member): void {}

    // Event setelah jemaat dihapus permanen (belum digunakan)
    public function forceDeleted(Member $member): void {}
}
