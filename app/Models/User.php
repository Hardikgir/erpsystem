<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

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
        'university_id',
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

    /**
     * Boot method to auto-assign Spatie role when role_id is set
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($user) {
            // If user has role_id but doesn't have Spatie role assigned, sync it
            if ($user->role_id && $user->role) {
                $spatieRole = Role::find($user->role_id);
                if ($spatieRole && !$user->hasRole($spatieRole)) {
                    $user->assignRole($spatieRole);
                }
            }
        });
    }

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role && $this->role->role_name === 'Super Admin';
    }

    public function isUniversityAdmin(): bool
    {
        if (!$this->role) {
            return false;
        }
        
        $roleName = strtolower(trim($this->role->role_name));
        return in_array($roleName, [
            'university admin',
            'university_admin',
            'universityadmin'
        ]);
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
     * Now uses Spatie permissions with route-based permission names
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
        if (!$this->role_id || !$this->role) {
            return collect();
        }

        // Get all modules and submodules
        $allModules = Module::with('subModules')->where('status', true)->get();
        $allowedModules = collect();

        foreach ($allModules as $module) {
            $allowedSubModules = collect();

            foreach ($module->subModules->where('status', true) as $subModule) {
                // Use route name for permission check (route.view)
                $routeName = $subModule->route;
                $viewPermission = "{$routeName}.view";

                // Check if user has view permission for this submodule using Spatie
                if ($this->can($viewPermission)) {
                    $allowedSubModules->push($subModule);
                }
            }

            // Only include module if it has at least one allowed submodule
            if ($allowedSubModules->isNotEmpty()) {
                $module->setRelation('subModules', $allowedSubModules);
                $allowedModules->push($module);
            }
        }

        return $allowedModules;
    }
}
