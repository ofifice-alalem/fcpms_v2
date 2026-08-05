<?php

namespace Database\Seeders;

use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with Spatie roles and active/inactive test users.
     */
    public function run(): void
    {
        // 0. Ensure Roles Exist
        $hrRole = Role::firstOrCreate(['name' => 'hr', 'guard_name' => 'web']);
        $consultantRole = Role::firstOrCreate(['name' => 'consultant', 'guard_name' => 'web']);

        // 1. Active HR Admin
        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'مدير الموارد البشرية',
                'email' => 'admin@fcpms.test',
                'password' => Hash::make('password'),
                'status' => UserStatus::ACTIVE,
            ]
        );
        $admin->syncRoles([$hrRole]);

        // 2. Active Field Consultant
        $consultant = User::firstOrCreate(
            ['username' => 'consultant'],
            [
                'name' => 'أحمد السالم (استشاري ميداني)',
                'email' => 'ahmed@fcpms.test',
                'password' => Hash::make('password'),
                'status' => UserStatus::ACTIVE,
            ]
        );
        $consultant->syncRoles([$consultantRole]);

        // 3. Inactive User (for BR-002 testing)
        $inactive = User::firstOrCreate(
            ['username' => 'inactive_user'],
            [
                'name' => 'مستخدم موقوف',
                'email' => 'inactive@fcpms.test',
                'password' => Hash::make('password'),
                'status' => UserStatus::INACTIVE,
            ]
        );
        $inactive->syncRoles([$consultantRole]);
    }
}
