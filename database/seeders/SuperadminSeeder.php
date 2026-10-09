<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SuperadminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Runs in ALL environments to ensure the superadmin account is always available.
     */
    public function run(): void
    {
        $name = env('SUPERADMIN_NAME', 'Super Administrator');
        $email = env('SUPERADMIN_EMAIL', 'admin@preminaja.id');
        $password = env('SUPERADMIN_PASSWORD', 'password123');

        $superadmin = User::where('email', $email)->first();

        if (! $superadmin) {
            $superadmin = new User([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'status' => UserStatus::ACTIVE,
                'phone' => '081234567890',
                'last_login_at' => now(),
            ]);
            $superadmin->role = UserRole::SUPERADMIN;
            $superadmin->email_verified_at = now();
            $superadmin->save();
        } else {
            // Ensure email_verified_at is set if null, but do NOT overwrite password
            if (! $superadmin->email_verified_at) {
                $superadmin->email_verified_at = now();
                $superadmin->save();
            }
        }

        // Ensure Spatie role is assigned
        $superadminRole = Role::firstOrCreate(['name' => UserRole::SUPERADMIN->value, 'guard_name' => 'web']);
        $superadmin->assignRole($superadminRole);
    }
}
