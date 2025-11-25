<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeeElement extends Model
{
    use HasFactory;

    protected $fillable = [
        'university_id',
        'element_name',
        'pattern',
    ];

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function packageItems(): HasMany
    {
        return $this->hasMany(FeePackageItem::class, 'element_id');
    }

    public function planItems(): HasMany
    {
        return $this->hasMany(FeePlanItem::class, 'element_id');
    }
}
