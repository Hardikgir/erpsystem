<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Role;
use App\Models\RoleModulePermission;
use App\Models\SubModule;
use Illuminate\Database\Seeder;

class ExamDetailsModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Exam Details Module
        $examModule = Module::firstOrCreate(
            ['module_name' => 'Exam Details'],
            [
                'icon' => 'fas fa-book',
                'status' => true,
            ]
        );

        // Create View Exam Details Submodule
        $examSubModule = SubModule::firstOrCreate(
            [
                'module_id' => $examModule->id,
                'route' => 'student.exam.details',
            ],
            [
                'sub_module_name' => 'View Exam Details',
                'status' => true,
            ]
        );

        // Assign permission to Student role
        $studentRole = Role::where('role_name', 'student')->first();
        
        if ($studentRole) {
            RoleModulePermission::firstOrCreate(
                [
                    'role_id' => $studentRole->id,
                    'module_id' => $examModule->id,
                    'sub_module_id' => $examSubModule->id,
                ],
                [
                    'can_view' => true,
                    'can_add' => false,
                    'can_edit' => false,
                    'can_delete' => false,
                ]
            );
        }

        $this->command->info('Exam Details module and submodule created successfully!');
        if ($studentRole) {
            $this->command->info('Permissions assigned to Student role.');
        } else {
            $this->command->warn('Student role not found. Please assign permissions manually.');
        }
    }
}
