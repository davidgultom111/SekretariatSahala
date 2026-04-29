<?php

namespace App\Observers;

use App\Models\Member;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MemberObserver
{
    /**
     * Handle the Member "creating" event - Auto-generate id_jemaat before saving
     */
    public function creating(Member $member): void
    {
        // Auto-generate id_jemaat if not provided and tanggal_lahir exists
        if (!$member->id_jemaat && $member->tanggal_lahir) {
            try {
                // Convert to Carbon if it's a string
                $date = is_string($member->tanggal_lahir) 
                    ? Carbon::createFromFormat('Y-m-d', $member->tanggal_lahir)
                    : $member->tanggal_lahir;

                if ($date) {
                    $id_jemaat = $date->format('dmY'); // DDMMYYYY format

                    // Handle duplicates by appending counter
                    $counter = 1;
                    $originalId = $id_jemaat;
                    while (Member::where('id_jemaat', $id_jemaat)->exists()) {
                        $id_jemaat = $originalId . $counter;
                        $counter++;
                    }

                    $member->id_jemaat = $id_jemaat;
                }
            } catch (\Exception $e) {
                // If date parsing fails, skip id_jemaat generation
                Log::warning('Failed to generate id_jemaat: ' . $e->getMessage());
            }
        }
    }

    /**
     * Handle the Member "updating" event - Regenerate id_jemaat if tanggal_lahir changed
     */
    public function updating(Member $member): void
    {
        // Regenerate id_jemaat if tanggal_lahir changed and id_jemaat is null or not manually set
        if ($member->isDirty('tanggal_lahir') && (!$member->id_jemaat || !$member->isDirty('id_jemaat'))) {
            if ($member->tanggal_lahir) {
                try {
                    // Convert to Carbon if it's a string
                    $date = is_string($member->tanggal_lahir) 
                        ? Carbon::createFromFormat('Y-m-d', $member->tanggal_lahir)
                        : $member->tanggal_lahir;

                    if ($date) {
                        $id_jemaat = $date->format('dmY'); // DDMMYYYY format

                        // Handle duplicates by appending counter
                        $counter = 1;
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
                    // If date parsing fails, skip id_jemaat generation
                    Log::warning('Failed to update id_jemaat: ' . $e->getMessage());
                }
            }
        }
    }

    /**
     * Handle the Member "created" event.
     */
    public function created(Member $member): void
    {
        //
    }

    /**
     * Handle the Member "updated" event.
     */
    public function updated(Member $member): void
    {
        //
    }

    /**
     * Handle the Member "deleted" event.
     */
    public function deleted(Member $member): void
    {
        //
    }

    /**
     * Handle the Member "restored" event.
     */
    public function restored(Member $member): void
    {
        //
    }

    /**
     * Handle the Member "force deleted" event.
     */
    public function forceDeleted(Member $member): void
    {
        //
    }
}