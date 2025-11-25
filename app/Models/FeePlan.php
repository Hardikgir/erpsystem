<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeePlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'university_id',
        'course_id',
        'category',
        'package_id',
        'total_amount',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(FeePackage::class, 'package_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(FeePlanItem::class, 'plan_id');
    }
}
