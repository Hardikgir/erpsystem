<?php

namespace Database\Seeders;

use App\Models\Role;
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
        // Create Super Admin role
        $superAdminRole = Role::firstOrCreate(
            ['role_name' => 'Super Admin'],
            [
                'description' => 'Super Admin has full system access and can manage all roles, modules, and permissions.',
            ]
        );

        // Create Super Admin user
        User::firstOrCreate(
            ['email' => 'superadmin@erp.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('SuperAdmin@123'),
                'role_id' => $superAdminRole->id,
            ]
        );
    }
}
