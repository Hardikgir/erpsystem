<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Role;
use App\Models\RoleModulePermission;
use App\Models\SubModule;
use Illuminate\Database\Seeder;

class UniversityAdminMasterModulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find University Admin role (check multiple possible names)
        $universityAdminRole = Role::where(function($query) {
            $query->whereRaw('LOWER(role_name) = ?', ['university admin'])
                  ->orWhereRaw('LOWER(role_name) = ?', ['university_admin'])
                  ->orWhere('role_name', 'University Admin');
        })->first();

        // 1. Program Master Module
        $programModule = Module::firstOrCreate(
            ['module_name' => 'Program Master'],
            [
                'module_code' => 'PROGRAM_MASTER',
                'icon' => 'fas fa-graduation-cap',
                'status' => true,
            ]
        );

        // Program Master Submodules
        $programSubModule = SubModule::firstOrCreate(
            [
                'module_id' => $programModule->id,
                'route' => 'university.admin.program.master',
            ],
            [
                'sub_module_name' => 'Program Master',
                'status' => true,
            ]
        );

        // 2. Course Master Module
        $courseModule = Module::firstOrCreate(
            ['module_name' => 'Course Master'],
            [
                'module_code' => 'COURSE_MASTER',
                'icon' => 'fas fa-book-open',
                'status' => true,
            ]
        );

        // Course Master Submodules
        $courseSubModule = SubModule::firstOrCreate(
            [
                'module_id' => $courseModule->id,
                'route' => 'university.admin.course.master',
            ],
            [
                'sub_module_name' => 'Course Master',
                'status' => true,
            ]
        );

        // 3. College Master Module
        $collegeModule = Module::firstOrCreate(
            ['module_name' => 'College Master'],
            [
                'module_code' => 'COLLEGE_MASTER',
                'icon' => 'fas fa-building',
                'status' => true,
            ]
        );

        // College Master Submodules
        $collegeSubModule = SubModule::firstOrCreate(
            [
                'module_id' => $collegeModule->id,
                'route' => 'university.admin.college.master',
            ],
            [
                'sub_module_name' => 'College Master',
                'status' => true,
            ]
        );

        // 4. University Role Master Module
        $roleModule = Module::firstOrCreate(
            ['module_name' => 'University Role Master'],
            [
                'module_code' => 'UNIVERSITY_ROLE_MASTER',
                'icon' => 'fas fa-user-shield',
                'status' => true,
            ]
        );

        // University Role Master Submodules
        $roleSubModule = SubModule::firstOrCreate(
            [
                'module_id' => $roleModule->id,
                'route' => 'university.admin.role.master',
            ],
            [
                'sub_module_name' => 'University Role Master',
                'status' => true,
            ]
        );

        // 5. Session Master Module
        $sessionModule = Module::firstOrCreate(
            ['module_name' => 'Session Master'],
            [
                'module_code' => 'SESSION_MASTER',
                'icon' => 'fas fa-calendar-alt',
                'status' => true,
            ]
        );

        // Session Master Submodules
        $sessionSubModule = SubModule::firstOrCreate(
            [
                'module_id' => $sessionModule->id,
                'route' => 'university.admin.session.master',
            ],
            [
                'sub_module_name' => 'Session Master',
                'status' => true,
            ]
        );

        // Automatically assign all modules to University Admin role with full permissions
        if ($universityAdminRole) {
            $subModules = [
                $programSubModule,
                $courseSubModule,
                $collegeSubModule,
                $roleSubModule,
                $sessionSubModule,
            ];

            foreach ($subModules as $subModule) {
                RoleModulePermission::firstOrCreate(
                    [
                        'role_id' => $universityAdminRole->id,
                        'module_id' => $subModule->module_id,
                        'sub_module_id' => $subModule->id,
                    ],
                    [
                        'can_view' => true,
                        'can_add' => true,
                        'can_edit' => true,
                        'can_delete' => true,
                    ]
                );
            }

            $this->command->info('University Admin Master Modules and Submodules created successfully!');
            $this->command->info('Created modules: Program Master, Course Master, College Master, University Role Master, Session Master');
            $this->command->info('✓ All modules automatically assigned to University Admin role with full permissions.');
        } else {
            $this->command->info('University Admin Master Modules and Submodules created successfully!');
            $this->command->info('Created modules: Program Master, Course Master, College Master, University Role Master, Session Master');
            $this->command->warn('⚠ University Admin role not found. Please assign permissions manually via Permission Assignment page.');
        }
    }
}

