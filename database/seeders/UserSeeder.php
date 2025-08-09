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
        // Super Admin
        User::create([
            'name' => 'Ganang Setyo Hadi S.Kom',
            'email' => 'ganangsetyohadi@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'phone' => '+62 812-3456-7890',
            'position' => 'Kepala Laboratorium',
            'is_active' => true,
            'last_login_at' => now()->subDays(1),
            'email_verified_at' => now(),
        ]);

        // Admin Users
        $admins = [
            [
                'name' => 'Dr. Siti Nurhaliza, M.Sc',
                'email' => 'admin1@labgos.ac.id',
                'phone' => '+62 813-4567-8901',
                'position' => 'Lab Manager - Spektroskopi',
            ],
            [
                'name' => 'Drs. Budi Santoso, M.Si',
                'email' => 'admin2@labgos.ac.id',
                'phone' => '+62 814-5678-9012',
                'position' => 'Lab Manager - Optik',
            ],
            [
                'name' => 'Dr. Maya Sari, M.T',
                'email' => 'admin3@labgos.ac.id',
                'phone' => '+62 815-6789-0123',
                'position' => 'Lab Manager - Elektronik',
            ],
            [
                'name' => 'Prof. Dr. Andi Rahman, M.Sc',
                'email' => 'admin4@labgos.ac.id',
                'phone' => '+62 816-7890-1234',
                'position' => 'Senior Research Associate',
            ],
            [
                'name' => 'Ir. Dewi Kusuma, M.T',
                'email' => 'admin5@labgos.ac.id',
                'phone' => '+62 817-8901-2345',
                'position' => 'Technical Manager',
            ],
        ];

        foreach ($admins as $admin) {
            User::create(array_merge($admin, [
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
                'last_login_at' => now()->subDays(rand(1, 30)),
                'email_verified_at' => now(),
            ]));
        }

        $this->command->info('✅ Created ' . (count($admins) + 1) . ' users (1 super admin, ' . count($admins) . ' admins)');
    }
}
