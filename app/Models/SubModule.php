<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubModule extends Model
{
    protected $fillable = [
        'module_id',
        'sub_module_name',
        'route',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function permissions(): HasMany
    {
        return $this->hasMany(RoleModulePermission::class, 'sub_module_id');
    }

    /**
     * Check if the route is valid (exists and has no spaces)
     */
    public function hasValidRoute(): bool
    {
        $routeName = trim($this->route);
        
        // Route name cannot be empty or contain spaces
        if (empty($routeName) || strpos($routeName, ' ') !== false) {
            return false;
        }
        
        // Check if route exists
        return \Illuminate\Support\Facades\Route::has($routeName);
    }
}
