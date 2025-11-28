<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\SubModule;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;

class RegisterMasterModulesForSpatieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * This ensures all 5 master modules are registered for Spatie permission management
     */
    public function run(): void
    {
        $this->command->info('Registering Master Modules for Spatie Permission Management...');

        // Define modules with their details
        $modules = [
            [
                'module_name' => 'Program Master',
                'module_code' => 'PROGRAM_MASTER',
                'icon' => 'fas fa-graduation-cap',
                'submodules' => [
                    [
                        'sub_module_name' => 'Program Master',
                        'route' => 'university.admin.program.master',
                    ],
                ],
            ],
            [
                'module_name' => 'Course Master',
                'module_code' => 'COURSE_MASTER',
                'icon' => 'fas fa-book-open',
                'submodules' => [
                    [
                        'sub_module_name' => 'Course Master',
                        'route' => 'university.admin.course.master',
                    ],
                ],
            ],
            [
                'module_name' => 'College Master',
                'module_code' => 'COLLEGE_MASTER',
                'icon' => 'fas fa-building',
                'submodules' => [
                    [
                        'sub_module_name' => 'College Master',
                        'route' => 'university.admin.college.master',
                    ],
                ],
            ],
            [
                'module_name' => 'Role Master',
                'module_code' => 'ROLE_MASTER',
                'icon' => 'fas fa-user-shield',
                'submodules' => [
                    [
                        'sub_module_name' => 'University Role Master',
                        'route' => 'university.admin.role.master',
                    ],
                ],
            ],
            [
                'module_name' => 'Session Master',
                'module_code' => 'SESSION_MASTER',
                'icon' => 'fas fa-calendar-alt',
                'submodules' => [
                    [
                        'sub_module_name' => 'Session Master',
                        'route' => 'university.admin.session.master',
                    ],
                ],
            ],
        ];

        $modulesCreated = 0;
        $submodulesCreated = 0;
        $permissionsCreated = 0;

        foreach ($modules as $moduleData) {
            // Check if module already exists
            $module = Module::where('module_name', $moduleData['module_name'])->first();

            if (!$module) {
                // Create module if it doesn't exist
                $module = Module::create([
                    'module_name' => $moduleData['module_name'],
                    'module_code' => $moduleData['module_code'],
                    'icon' => $moduleData['icon'],
                    'status' => true,
                ]);
                $modulesCreated++;
                $this->command->info("✓ Created module: {$moduleData['module_name']}");
            } else {
                $this->command->comment("⊘ Module already exists: {$moduleData['module_name']}");
            }

            // Process submodules
            foreach ($moduleData['submodules'] as $submoduleData) {
                // Check if submodule with this route already exists
                $submodule = SubModule::where('module_id', $module->id)
                    ->where('route', $submoduleData['route'])
                    ->first();

                if (!$submodule) {
                    // Create submodule if it doesn't exist
                    $submodule = SubModule::create([
                        'module_id' => $module->id,
                        'sub_module_name' => $submoduleData['sub_module_name'],
                        'route' => $submoduleData['route'],
                        'status' => true,
                    ]);
                    $submodulesCreated++;
                    $this->command->info("  ✓ Created submodule: {$submoduleData['sub_module_name']} ({$submoduleData['route']})");
                } else {
                    $this->command->comment("  ⊘ Submodule already exists: {$submoduleData['sub_module_name']} ({$submoduleData['route']})");
                }

                // Generate Spatie permissions for this submodule
                $routeName = trim($submoduleData['route']);
                $actions = ['view', 'create', 'edit', 'delete'];

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
            }
        }

        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info("\n=== Summary ===");
        $this->command->info("Modules created: {$modulesCreated}");
        $this->command->info("Submodules created: {$submodulesCreated}");
        $this->command->info("Permissions created: {$permissionsCreated}");
        $this->command->info("\n✓ All master modules registered for Spatie Permission Management!");
        $this->command->info("✓ Modules will now appear in Super Admin → Permission Management (Spatie)");
    }
}




