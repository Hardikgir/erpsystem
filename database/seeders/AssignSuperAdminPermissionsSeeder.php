<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class AssignSuperAdminPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find Super Admin role
        $superAdminRole = Role::where('role_name', 'Super Admin')->first();

        if (!$superAdminRole) {
            $this->command->warn('Super Admin role not found. Please create it first.');
            return;
        }

        // Get all permissions
        $allPermissions = Permission::all();

        if ($allPermissions->isEmpty()) {
            $this->command->warn('No permissions found. Please run: php artisan permissions:sync-modules first.');
            return;
        }

        // Assign all permissions to Super Admin role
        $superAdminRole->syncPermissions($allPermissions);

        $this->command->info('Successfully assigned ' . $allPermissions->count() . ' permissions to Super Admin role.');
    }
}
