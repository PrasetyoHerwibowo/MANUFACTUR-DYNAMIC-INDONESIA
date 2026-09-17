<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Membuat satu akun administrator website.
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@manufacturdynamic.co.id'],
            [
                'name' => 'Administrator Website',
                'role' => User::ROLE_ADMIN,
                'phone' => '0341-123456',
                'password' => 'admin12345',
                'email_verified_at' => now(),
            ]
        );

        $this->command?->info('Akun admin siap digunakan: '.$admin->email.' / admin12345');
    }
}
