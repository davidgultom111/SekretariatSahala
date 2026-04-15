<?php

namespace Database\Seeders;

use App\Models\LetterNumberCounter;
use Illuminate\Database\Seeder;

class LetterNumberCounterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentYear = now()->year;

        $letterTypes = [
            [
                'letter_type' => 'surat_tugas_pelayanan',
                'abbreviation' => 'TP',
                'year' => $currentYear,
                'next_number' => 1,
            ],
            [
                'letter_type' => 'surat_pengantar',
                'abbreviation' => 'SP',
                'year' => $currentYear,
                'next_number' => 1,
            ],
            [
                'letter_type' => 'surat_keterangan_jemaat_aktif',
                'abbreviation' => 'KJA',
                'year' => $currentYear,
                'next_number' => 1,
            ],
            [
                'letter_type' => 'surat_nilai_sekolah',
                'abbreviation' => 'NS',
                'year' => $currentYear,
                'next_number' => 1,
            ],
            [
                'letter_type' => 'surat_pengajuan_baptisan',
                'abbreviation' => 'PB',
                'year' => $currentYear,
                'next_number' => 1,
            ],
            [
                'letter_type' => 'surat_pengajuan_penyerahan_anak',
                'abbreviation' => 'PA',
                'year' => $currentYear,
                'next_number' => 1,
            ],
            [
                'letter_type' => 'surat_pengajuan_pernikahan',
                'abbreviation' => 'PP',
                'year' => $currentYear,
                'next_number' => 1,
            ],
        ];

        foreach ($letterTypes as $type) {
            LetterNumberCounter::updateOrCreate(
                [
                    'letter_type' => $type['letter_type'],
                    'year' => $type['year'],
                ],
                $type
            );
        }
    }
}

