<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeePackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'university_id',
        'package_name',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(FeePackageItem::class, 'package_id');
    }

    public function plans(): HasMany
    {
        return $this->hasMany(FeePlan::class, 'package_id');
    }
}
