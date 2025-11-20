<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role && $this->role->role_name === 'Super Admin';
    }

    public function hasPermission(string $route, string $permission = 'can_view'): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if (!$this->role) {
            return false;
        }

        $subModule = SubModule::where('route', $route)->first();
        
        if (!$subModule) {
            return false;
        }

        $permissionRecord = RoleModulePermission::where('role_id', $this->role_id)
            ->where('module_id', $subModule->module_id)
            ->where('sub_module_id', $subModule->id)
            ->first();

        return $permissionRecord && $permissionRecord->$permission === true;
    }

    /**
     * Get modules and submodules accessible to the user based on role permissions
     */
    public function getAccessibleModules()
    {
        // Ensure role is loaded
        if (!$this->relationLoaded('role')) {
            $this->load('role');
        }

        // Super Admin sees all modules
        if ($this->isSuperAdmin()) {
            return Module::with(['subModules' => function($query) {
                $query->where('status', true);
            }])->where('status', true)->get();
        }

        // If user has no role, return empty collection
        if (!$this->role_id) {
            return collect();
        }

        $roleId = $this->role_id;

        // Get all permissions for this role where can_view is true
        // Query directly without boolean casting issues
        $permissions = RoleModulePermission::where('role_id', $roleId)
            ->get()
            ->filter(function($permission) {
                // Filter in PHP to avoid boolean casting issues
                return $permission->can_view === true || $permission->can_view === 1 || $permission->can_view === '1';
            });

        if ($permissions->isEmpty()) {
            return collect();
        }

        // Get unique submodule IDs that have can_view permission
        $allowedSubModuleIds = $permissions->pluck('sub_module_id')->unique()->filter()->values()->toArray();

        if (empty($allowedSubModuleIds)) {
            return collect();
        }

        // Get module IDs from the allowed submodules
        $moduleIds = SubModule::whereIn('id', $allowedSubModuleIds)
            ->where('status', true)
            ->pluck('module_id')
            ->unique()
            ->filter()
            ->values()
            ->toArray();

        if (empty($moduleIds)) {
            return collect();
        }

        // Load modules with only the allowed submodules
        $modules = Module::whereIn('id', $moduleIds)
            ->where('status', true)
            ->with(['subModules' => function($query) use ($allowedSubModuleIds) {
                $query->whereIn('id', $allowedSubModuleIds)
                      ->where('status', true);
            }])
            ->get();

        return $modules;
    }
}
