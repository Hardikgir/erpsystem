<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Role;
use App\Models\RoleModulePermission;
use App\Models\SubModule;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PermissionController extends Controller
{
    /**
     * Display the module assignment form.
     */
    public function index(): View
    {
        $roles = Role::all();
        $modules = Module::with('subModules')->where('status', true)->get();
        return view('admin.permissions.index', compact('roles', 'modules'));
    }

    /**
     * Show permissions for a specific role.
     */
    public function show(Role $role): View
    {
        $modules = Module::with('subModules')->where('status', true)->get();
        $permissions = RoleModulePermission::where('role_id', $role->id)->get()
            ->keyBy(function ($item) {
                return $item->module_id . '_' . $item->sub_module_id;
            });
        
        return view('admin.permissions.show', compact('role', 'modules', 'permissions'));
    }

    /**
     * Store or update permissions for a role.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $roleId = $request->role_id;
        $permissionsData = $request->permissions;

        // Delete existing permissions for this role
        RoleModulePermission::where('role_id', $roleId)->delete();

        // Handle nested array format from form: permissions[module_id][sub_module_id][can_view]
        if (is_array($permissionsData) && !empty($permissionsData)) {
            foreach ($permissionsData as $moduleId => $subModules) {
                if (is_array($subModules) && !empty($subModules)) {
                    foreach ($subModules as $subModuleId => $permissions) {
                        if (is_array($permissions)) {
                            // Get permission values (checkboxes send "1" when checked, nothing when unchecked)
                            $canView = isset($permissions['can_view']) && ($permissions['can_view'] == 1 || $permissions['can_view'] === '1');
                            $canAdd = isset($permissions['can_add']) && ($permissions['can_add'] == 1 || $permissions['can_add'] === '1');
                            $canEdit = isset($permissions['can_edit']) && ($permissions['can_edit'] == 1 || $permissions['can_edit'] === '1');
                            $canDelete = isset($permissions['can_delete']) && ($permissions['can_delete'] == 1 || $permissions['can_delete'] === '1');
                            
                            // Only create permission record if at least one permission is checked
                            // This is important - if can_view is checked, create the record so it shows in sidebar
                            if ($canView || $canAdd || $canEdit || $canDelete) {
                                RoleModulePermission::updateOrCreate(
                                    [
                                        'role_id' => $roleId,
                                        'module_id' => $moduleId,
                                        'sub_module_id' => $subModuleId,
                                    ],
                                    [
                                        'can_view' => $canView,
                                        'can_add' => $canAdd,
                                        'can_edit' => $canEdit,
                                        'can_delete' => $canDelete,
                                    ]
                                );
                            }
                        }
                    }
                }
            }
        }

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permissions assigned successfully.')
            ->with('selected_role_id', $roleId);
    }

    /**
     * Get permissions for a role (AJAX).
     */
    public function getPermissions(Role $role)
    {
        $permissions = RoleModulePermission::where('role_id', $role->id)->get()
            ->mapWithKeys(function ($item) {
                return [$item->module_id . '_' . $item->sub_module_id => [
                    'can_view' => $item->can_view,
                    'can_add' => $item->can_add,
                    'can_edit' => $item->can_edit,
                    'can_delete' => $item->can_delete,
                ]];
            });

        return response()->json($permissions);
    }
}
