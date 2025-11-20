<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Module extends Model
{
    protected $fillable = [
        'module_code',
        'module_name',
        'icon',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function subModules(): HasMany
    {
        return $this->hasMany(SubModule::class);
    }

    public function permissions(): HasMany
    {
        return $this->hasMany(RoleModulePermission::class, 'module_id');
    }
}
