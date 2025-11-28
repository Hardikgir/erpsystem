<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Role;
use App\Models\SubModule;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;

class SeatMatrixModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find University Admin role
        $universityAdminRole = Role::where(function($query) {
            $query->whereRaw('LOWER(role_name) = ?', ['university admin'])
                  ->orWhereRaw('LOWER(role_name) = ?', ['university_admin'])
                  ->orWhere('role_name', 'University Admin');
        })->first();

        // Create Seat Matrix Module
        $seatMatrixModule = Module::firstOrCreate(
            ['module_name' => 'Seat Matrix'],
            [
                'module_code' => 'SEAT_MATRIX',
                'icon' => 'fas fa-th',
                'status' => true,
            ]
        );

        // Create Seat Matrix Entry Submodule
        $seatMatrixSubModule = SubModule::firstOrCreate(
            [
                'module_id' => $seatMatrixModule->id,
                'route' => 'university.admin.seat.matrix',
            ],
            [
                'sub_module_name' => 'Seat Matrix Entry',
                'status' => true,
            ]
        );

        // Create Spatie permissions for Seat Matrix
        $permissions = [
            'university.admin.seat.matrix.view',
            'university.admin.seat.matrix.create',
            'university.admin.seat.matrix.edit',
            'university.admin.seat.matrix.delete',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(
                ['name' => $permissionName, 'guard_name' => 'web']
            );
        }

        // Assign all permissions to University Admin role
        if ($universityAdminRole) {
            foreach ($permissions as $permissionName) {
                $permission = Permission::where('name', $permissionName)->first();
                if ($permission && !$universityAdminRole->hasPermissionTo($permission)) {
                    $universityAdminRole->givePermissionTo($permission);
                }
            }
            $this->command->info('✓ Seat Matrix module created and assigned to University Admin role with full permissions.');
        } else {
            $this->command->warn('⚠ University Admin role not found. Please assign permissions manually.');
        }

        $this->command->info('Seat Matrix module and permissions created successfully!');
    }
}


