<?php

namespace App\Console\Commands;

use App\Models\SubModule;
use App\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Console\Command;

class CheckFeeElementPermission extends Command
{
    protected $signature = 'permissions:check-fee-element';
    protected $description = 'Check Fee Element permission setup';

    public function handle()
    {
        $subModule = SubModule::where('sub_module_name', 'Fee Element')->first();
        
        if (!$subModule) {
            $this->error('Fee Element submodule not found');
            return;
        }

        $this->info("Submodule: {$subModule->sub_module_name}");
        $this->info("Route: {$subModule->route}");
        
        $expectedPermission = "{$subModule->route}.view";
        $this->info("Expected Permission: {$expectedPermission}");

        $permission = Permission::where('name', $expectedPermission)->first();
        if ($permission) {
            $this->info("✓ Permission exists: {$permission->name}");
        } else {
            $this->error("✗ Permission does NOT exist: {$expectedPermission}");
            $this->info("Creating permission...");
            Permission::create(['name' => $expectedPermission, 'guard_name' => 'web']);
            $this->info("✓ Permission created");
        }

        $role = Role::where('role_name', 'university_admin')
            ->orWhere('name', 'university_admin')
            ->first();

        if ($role) {
            $this->info("\nRole: {$role->name}");
            $hasPermission = $role->hasPermissionTo($expectedPermission);
            if ($hasPermission) {
                $this->info("✓ Role HAS permission");
            } else {
                $this->warn("✗ Role does NOT have permission");
                $this->info("Assigning permission to role...");
                $role->givePermissionTo($expectedPermission);
                $this->info("✓ Permission assigned");
            }
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $this->info("\n✓ Cache cleared");
    }
}

