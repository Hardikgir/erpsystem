<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\SubModule;
use App\Models\Role;
use App\Models\RoleModulePermission;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;

class CourseDocumentModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Registering Course Document Mapping Module...');

        // Find University Admin role
        $universityAdminRole = Role::where(function($query) {
            $query->whereRaw('LOWER(role_name) = ?', ['university admin'])
                  ->orWhereRaw('LOWER(role_name) = ?', ['university_admin'])
                  ->orWhere('role_name', 'University Admin');
        })->first();

        // Create Course Document Mapping Module
        $module = Module::firstOrCreate(
            ['module_name' => 'Course Document Mapping'],
            [
                'module_code' => 'COURSE_DOCUMENT_MAPPING',
                'icon' => 'fas fa-file-alt',
                'status' => true,
            ]
        );

        $this->command->info("✓ Module: Course Document Mapping");

        // Create Submodule
        $subModule = SubModule::firstOrCreate(
            [
                'module_id' => $module->id,
                'route' => 'university.admin.course.document',
            ],
            [
                'sub_module_name' => 'Course Document',
                'status' => true,
            ]
        );

        $this->command->info("  ✓ Submodule: Course Document ({$subModule->route})");

        // Generate Spatie permissions
        $routeName = trim($subModule->route);
        $actions = ['view', 'create', 'delete', 'map'];
        $permissionsCreated = 0;

        foreach ($actions as $action) {
            $permissionName = "{$routeName}.{$action}";
            
            $permission = Permission::firstOrCreate(
                ['name' => $permissionName, 'guard_name' => 'web'],
                ['name' => $permissionName, 'guard_name' => 'web']
            );

            if ($permission->wasRecentlyCreated) {
                $permissionsCreated++;
                $this->command->line("    ✓ Created permission: {$permissionName}");
            }
        }

        // Assign to University Admin role if found
        if ($universityAdminRole) {
            RoleModulePermission::firstOrCreate(
                [
                    'role_id' => $universityAdminRole->id,
                    'module_id' => $module->id,
                    'sub_module_id' => $subModule->id,
                ],
                [
                    'can_view' => true,
                    'can_add' => true,
                    'can_edit' => false,
                    'can_delete' => true,
                ]
            );

            // Assign Spatie permissions to role
            foreach ($actions as $action) {
                $permissionName = "{$routeName}.{$action}";
                if (!$universityAdminRole->hasPermissionTo($permissionName)) {
                    $universityAdminRole->givePermissionTo($permissionName);
                }
            }

            $this->command->info("  ✓ Assigned to University Admin role with permissions");
        } else {
            $this->command->warn("  ⚠ University Admin role not found. Please assign permissions manually.");
        }

        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info("\n=== Summary ===");
        $this->command->info("Permissions created: {$permissionsCreated}");
        $this->command->info("✓ Course Document Mapping module registered successfully!");
        $this->command->info("✓ Module will appear in Super Admin → Permission Management (Spatie)");
    }
}
