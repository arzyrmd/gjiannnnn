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
        // Admin Utama
        User::updateOrCreate(
            ['email' => 'admin@gajianarmn.com'],
            [
                'name' => 'Admin Utama',
                'email' => 'admin@gajianarmn.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );
    }
}
