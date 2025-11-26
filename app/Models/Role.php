<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    protected $fillable = [
        'name',
        'guard_name',
        'role_name',
        'description',
        'role_color',
        'role_hover_color',
    ];

    /**
     * Boot the model to sync role_name with name
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($role) {
            // If role_name is set but name is not, sync them
            if (isset($role->role_name) && empty($role->name)) {
                $role->name = $role->role_name;
            }
            // If name is set but role_name is not, sync them
            if (isset($role->name) && empty($role->role_name)) {
                $role->role_name = $role->name;
            }
            // Ensure guard_name is set
            if (empty($role->guard_name)) {
                $role->guard_name = 'web';
            }
        });
    }

    /**
     * Legacy users relationship (one-to-many via role_id)
     * Note: Spatie's Role model uses BelongsToMany for users() through model_has_roles pivot,
     * so we use a different method name for the legacy relationship
     */
    public function assignedUsers(): HasMany
    {
        return $this->hasMany(User::class, 'role_id');
    }

    /**
     * Legacy module permissions relationship
     */
    public function modulePermissions(): HasMany
    {
        return $this->hasMany(RoleModulePermission::class);
    }
    
    // Note: Spatie's Role model already provides users() method as BelongsToMany
    // If you need the legacy HasMany relationship, use assignedUsers() instead
}
