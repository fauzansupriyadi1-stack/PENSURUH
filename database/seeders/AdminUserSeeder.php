<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'pensuruh@gmail.com'],
            [
                'name'              => 'Admin PenSuruh',
                'phone'             => '081234567890',
                'password'          => Hash::make('FZ3434AW'),
                'role'              => 'admin',
                'email_verified_at' => now(),
            ]
        );
    }
}
