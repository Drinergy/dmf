<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed the primary administrator account.
     *
     * This account is the sole owner of the admin panel and the only user
     * permitted to manage assistant accounts and assign roles.
     *
     * @author CKD
     *
     * @created 2026-04-24
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@dmfdental.com'],
            [
                'name' => 'DMF Dental Administrator',
                'password' => Hash::make('admin12345'),
                'role' => UserRole::Admin->value,
            ]
        );

        // Ensure the admin role is always set, even if the record already existed.
        if ($user->role !== UserRole::Admin->value) {
            $user->update(['role' => UserRole::Admin->value]);
        }
    }
}
