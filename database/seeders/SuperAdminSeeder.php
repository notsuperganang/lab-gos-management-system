<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'superadmin@labgos.ac.id'],
            [
                'name' => 'superadmin-gos',
                'email' => 'superadmin@labgos.ac.id',
                'password' => Hash::make('superadmin-gos-123'),
                'role' => 'super_admin',
                'phone' => '+628123456789',
                'position' => 'Super Administrator',
                'avatar_path' => null,
                'is_active' => true,
                'last_login_at' => null,
                'email_verified_at' => now(),
            ]
        );
    }
}