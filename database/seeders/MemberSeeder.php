<?php

namespace Database\Seeders;

use App\Models\Member;
use Illuminate\Database\Seeder;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $members = [
            [
                'nama_lengkap' => 'Budi Santoso',
                'jenis_kelamin' => 'Laki-laki',
                'tanggal_lahir' => '1980-05-15',
                'tempat_lahir' => 'Jakarta',
                'alamat' => 'Jl. Merdeka No. 123',
                'no_telepon' => '081234567890',
                'status_aktif' => 'Aktif',
            ],
            [
                'nama_lengkap' => 'Siti Nurhaliza',
                'jenis_kelamin' => 'Perempuan',
                'tanggal_lahir' => '1992-08-22',
                'tempat_lahir' => 'Bandung',
                'alamat' => 'Jl. Ahmad Yani No. 45',
                'no_telepon' => '082345678901',
                'status_aktif' => 'Aktif',
            ],
            [
                'nama_lengkap' => 'Hendra Wijaya',
                'jenis_kelamin' => 'Laki-laki',
                'tanggal_lahir' => '1985-03-10',
                'tempat_lahir' => 'Surabaya',
                'alamat' => 'Jl. Diponegoro No. 78',
                'no_telepon' => '083456789012',
                'status_aktif' => 'Aktif',
            ],
        ];

        foreach ($members as $member) {
            Member::create($member);
        }
    }
}
