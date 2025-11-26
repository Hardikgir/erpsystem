<?php

namespace App\Console\Commands;

use App\Models\Module;
use App\Models\SubModule;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;

class SyncModulePermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:sync-modules';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync permissions from modules and submodules. Creates permissions in format: module.submodule.action (view, create, edit, delete)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting permission sync from modules...');
        
        $modules = Module::with('subModules')->where('status', true)->get();
        $permissionsCreated = 0;
        $permissionsSkipped = 0;

        foreach ($modules as $module) {
            foreach ($module->subModules as $subModule) {
                if (!$subModule->status) {
                    continue;
                }

                // Use route name for permissions (route.view, route.create, etc.)
                $routeName = trim($subModule->route);
                
                // Skip if route is empty or invalid
                if (empty($routeName) || $routeName === '') {
                    $this->warn("Skipping submodule '{$subModule->sub_module_name}' - no route defined");
                    continue;
                }
                
                // Clean route name - remove spaces and ensure it's valid
                $routeName = preg_replace('/\s+/', '.', $routeName);
                
                // Create permissions for each action based on route
                $actions = ['view', 'create', 'edit', 'delete'];
                
                foreach ($actions as $action) {
                    $permissionName = "{$routeName}.{$action}";
                    
                    try {
                        Permission::firstOrCreate(
                            ['name' => $permissionName, 'guard_name' => 'web'],
                            ['name' => $permissionName, 'guard_name' => 'web']
                        );
                        $permissionsCreated++;
                        $this->line("Created: {$permissionName}");
                    } catch (\Exception $e) {
                        $permissionsSkipped++;
                        $this->warn("Skipped: {$permissionName} - {$e->getMessage()}");
                    }
                }
            }
        }

        $this->info("\nSync completed!");
        $this->info("Permissions created: {$permissionsCreated}");
        $this->info("Permissions skipped: {$permissionsSkipped}");
        
        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $this->info("Permission cache cleared.");
        
        return Command::SUCCESS;
    }

    // Note: Permission names now use route names directly (route.view, route.create, etc.)
    // No need for slugify method anymore
}
