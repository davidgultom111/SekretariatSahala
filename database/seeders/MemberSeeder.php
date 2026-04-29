<?php

namespace Database\Seeders;

use App\Models\Member;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $members = [
            // Regular members
            [
                'id_jemaat' => '15051980',
                'nama_lengkap' => 'Budi Santoso',
                'jenis_kelamin' => 'Laki-laki',
                'tanggal_lahir' => '1980-05-15',
                'tempat_lahir' => 'Jakarta',
                'alamat' => 'Jl. Merdeka No. 123',
                'no_telepon' => '081234567890',
                'status_aktif' => true,
                'password' => Hash::make('12345'),
                'role' => 'member',
            ],
            [
                'id_jemaat' => '22081992',
                'nama_lengkap' => 'Siti Nurhaliza',
                'jenis_kelamin' => 'Perempuan',
                'tanggal_lahir' => '1992-08-22',
                'tempat_lahir' => 'Bandung',
                'alamat' => 'Jl. Ahmad Yani No. 45',
                'no_telepon' => '082345678901',
                'status_aktif' => true,
                'password' => Hash::make('12345'),
                'role' => 'member',
            ],
            [
                'id_jemaat' => '10031985',
                'nama_lengkap' => 'Hendra Wijaya',
                'jenis_kelamin' => 'Laki-laki',
                'tanggal_lahir' => '1985-03-10',
                'tempat_lahir' => 'Surabaya',
                'alamat' => 'Jl. Diponegoro No. 78',
                'no_telepon' => '083456789012',
                'status_aktif' => true,
                'password' => Hash::make('12345'),
                'role' => 'member',
            ],
            
            // Admin account
            [
                'id_jemaat' => '01011980',
                'nama_lengkap' => 'Admin Gereja',
                'jenis_kelamin' => 'Laki-laki',
                'tanggal_lahir' => '1980-01-01',
                'tempat_lahir' => 'Medan',
                'alamat' => 'Jl. Gereja No. 1',
                'no_telepon' => '081111111111',
                'status_aktif' => 'true',
                'password' => Hash::make('12345'),
                'role' => 'admin',
            ],
        ];

        foreach ($members as $member) {
            Member::create($member);
        }
    }
}
