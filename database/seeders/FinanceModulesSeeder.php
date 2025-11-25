<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Role;
use App\Models\RoleModulePermission;
use App\Models\SubModule;
use Illuminate\Database\Seeder;

class FinanceModulesSeeder extends Seeder
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

        // 1. Fee Element Module
        $feeElementModule = Module::firstOrCreate(
            ['module_name' => 'Fee Element'],
            [
                'module_code' => 'FEE_ELEMENT',
                'icon' => 'fas fa-tag',
                'status' => true,
            ]
        );

        // Fee Element Submodules
        $feeElementSubModule = SubModule::firstOrCreate(
            [
                'module_id' => $feeElementModule->id,
                'route' => 'university.admin.fee.element',
            ],
            [
                'sub_module_name' => 'Fee Element',
                'status' => true,
            ]
        );

        // 2. Fee Package Module
        $feePackageModule = Module::firstOrCreate(
            ['module_name' => 'Fee Package'],
            [
                'module_code' => 'FEE_PACKAGE',
                'icon' => 'fas fa-box',
                'status' => true,
            ]
        );

        // Fee Package Submodules
        $feePackageSubModule = SubModule::firstOrCreate(
            [
                'module_id' => $feePackageModule->id,
                'route' => 'university.admin.fee.package',
            ],
            [
                'sub_module_name' => 'Fee Package',
                'status' => true,
            ]
        );

        // 3. Fee Plan Module
        $feePlanModule = Module::firstOrCreate(
            ['module_name' => 'Fee Plan'],
            [
                'module_code' => 'FEE_PLAN',
                'icon' => 'fas fa-file-invoice-dollar',
                'status' => true,
            ]
        );

        // Fee Plan Submodules
        $feePlanSubModule = SubModule::firstOrCreate(
            [
                'module_id' => $feePlanModule->id,
                'route' => 'university.admin.fee.plan',
            ],
            [
                'sub_module_name' => 'Fee Plan',
                'status' => true,
            ]
        );

        // 4. Bank Master Module
        $bankModule = Module::firstOrCreate(
            ['module_name' => 'Bank Master'],
            [
                'module_code' => 'BANK_MASTER',
                'icon' => 'fas fa-university',
                'status' => true,
            ]
        );

        // Bank Master Submodules
        $bankSubModule = SubModule::firstOrCreate(
            [
                'module_id' => $bankModule->id,
                'route' => 'university.admin.bank.master',
            ],
            [
                'sub_module_name' => 'Bank Master',
                'status' => true,
            ]
        );

        // Automatically assign all modules to University Admin role with full permissions
        if ($universityAdminRole) {
            $subModules = [
                $feeElementSubModule,
                $feePackageSubModule,
                $feePlanSubModule,
                $bankSubModule,
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

            $this->command->info('Finance Modules and Submodules created successfully!');
            $this->command->info('Created modules: Fee Element, Fee Package, Fee Plan, Bank Master');
            $this->command->info('✓ All modules automatically assigned to University Admin role with full permissions.');
        } else {
            $this->command->info('Finance Modules and Submodules created successfully!');
            $this->command->info('Created modules: Fee Element, Fee Package, Fee Plan, Bank Master');
            $this->command->warn('⚠ University Admin role not found. Please assign permissions manually via Permission Assignment page.');
        }
    }
}
