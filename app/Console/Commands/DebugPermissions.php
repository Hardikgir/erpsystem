<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Role;
use App\Models\SubModule;
use Illuminate\Console\Command;

class DebugPermissions extends Command
{
    protected $signature = 'permissions:debug {email?}';
    protected $description = 'Debug user permissions and sidebar visibility';

    public function handle()
    {
        $email = $this->argument('email') ?? 'tarun@test.com';
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User not found: {$email}");
            return;
        }

        $this->info("=== User: {$user->email} ===");
        $this->info("Role ID: {$user->role_id}");
        
        if ($user->role) {
            $this->info("Role Name: {$user->role->role_name}");
            $this->info("Spatie Role Name: " . ($user->role->name ?? 'NULL'));
        }

        $this->info("\n=== Spatie Roles ===");
        $spatieRoles = $user->roles;
        foreach ($spatieRoles as $role) {
            $this->info("  - {$role->name}");
        }

        $this->info("\n=== Spatie Permissions ===");
        $permissions = $user->getAllPermissions();
        $this->info("Total Permissions: " . $permissions->count());
        foreach ($permissions->take(20) as $perm) {
            $this->info("  - {$perm->name}");
        }

        $this->info("\n=== Submodules & Route Permissions ===");
        $subModules = SubModule::where('status', true)->whereNotNull('route')->get();
        foreach ($subModules as $subModule) {
            $routeName = trim($subModule->route);
            $viewPermission = "{$routeName}.view";
            $hasPermission = $user->can($viewPermission);
            $status = $hasPermission ? '✓' : '✗';
            $this->info("  {$status} {$subModule->sub_module_name} -> {$routeName} -> {$viewPermission}");
        }

        $this->info("\n=== Sidebar Visibility Check ===");
        $allModules = \App\Models\Module::with('subModules')->where('status', true)->get();
        $visibleCount = 0;
        foreach ($allModules as $module) {
            foreach ($module->subModules->where('status', true) as $subModule) {
                $routeName = trim($subModule->route);
                if (!empty($routeName)) {
                    $viewPermission = "{$routeName}.view";
                    if ($user->can($viewPermission)) {
                        $this->info("  ✓ {$module->module_name} -> {$subModule->sub_module_name}");
                        $visibleCount++;
                    }
                }
            }
        }
        $this->info("Total visible modules: {$visibleCount}");
    }
}




