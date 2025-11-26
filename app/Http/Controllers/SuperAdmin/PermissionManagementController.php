<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;

class PermissionManagementController extends Controller
{
    /**
     * Display the permission management page
     */
    public function manage(): View
    {
        $roles = Role::all();
        $modules = Module::with('subModules')->where('status', true)->get();
        
        // Get all existing permissions for reference
        $existingPermissions = Permission::pluck('name')->toArray();
        
        return view('superadmin.permissions.manage', compact('roles', 'modules', 'existingPermissions'));
    }

    /**
     * Get permissions for a specific role (AJAX)
     */
    public function getRolePermissions(Role $role)
    {
        $permissions = $role->permissions->pluck('name')->toArray();
        
        return response()->json($permissions);
    }

    /**
     * Update permissions for a role
     */
    public function update(Request $request, Role $role): RedirectResponse
    {
        \Log::info('Permission Update Request', [
            'role_id' => $role->id,
            'role_name' => $role->role_name ?? $role->name,
            'permissions_received' => $request->permissions ?? [],
        ]);

        $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
        ]);

        // Get selected permission names
        $selectedPermissions = $request->permissions ?? [];
        
        // Filter out empty values
        $selectedPermissions = array_filter($selectedPermissions, function($permission) {
            return !empty($permission) && trim($permission) !== '';
        });
        
        // Re-index array after filtering
        $selectedPermissions = array_values($selectedPermissions);
        
        \Log::info('Filtered Permissions', ['permissions' => $selectedPermissions]);
        
        // Ensure every permission exists, create them if they don't
        $permissionsToAssign = [];
        $createdPermissions = [];
        
        foreach ($selectedPermissions as $permissionName) {
            $permission = Permission::firstOrCreate(
                ['name' => $permissionName, 'guard_name' => 'web'],
                ['name' => $permissionName, 'guard_name' => 'web']
            );
            
            $permissionsToAssign[] = $permission->name;
            
            if ($permission->wasRecentlyCreated) {
                $createdPermissions[] = $permissionName;
            }
        }
        
        \Log::info('Permissions to Assign', [
            'count' => count($permissionsToAssign),
            'permissions' => $permissionsToAssign,
            'newly_created' => $createdPermissions
        ]);

        // Sync all selected permissions to this role (overwrites old permissions)
        $role->syncPermissions($permissionsToAssign);

        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $message = 'Permissions updated successfully for role: ' . ($role->role_name ?? $role->name);
        if (!empty($createdPermissions)) {
            $message .= ' (' . count($createdPermissions) . ' new permissions created)';
        }

        return redirect()->route('superadmin.permissions.manage')
            ->with('success', $message)
            ->with('selected_role_id', $role->id);
    }

    /**
     * Helper method to generate permission name from module and submodule
     */
    private function generatePermissionName($module, $subModule, $action): string
    {
        $moduleSlug = $this->slugify($module->module_code ?? $module->module_name);
        $subModuleSlug = $this->slugify($subModule->sub_module_name);
        
        return "{$moduleSlug}.{$subModuleSlug}.{$action}";
    }

    /**
     * Convert string to slug format
     */
    private function slugify(string $string): string
    {
        $string = strtolower(trim($string));
        $string = preg_replace('/[^a-z0-9]+/', '_', $string);
        $string = preg_replace('/_+/', '_', $string);
        $string = trim($string, '_');
        
        return $string;
    }
}
