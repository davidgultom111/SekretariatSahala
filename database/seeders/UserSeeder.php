<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@gereja.com'],
            [
                'name' => 'Admin Gereja',
                'password' => Hash::make('password'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'staff@gereja.com'],
            [
                'name' => 'Staff Sekretariat',
                'password' => Hash::make('password'),
            ]
        );
    }
}
