<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EligibilityCriteria extends Model
{
    use HasFactory;

    protected $fillable = [
        'university_id',
        'program_id',
        'course_id',
        'semester_year',
        'category_id',
        'gender',
        'min_age',
        'max_age',
    ];

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(EligibilityItem::class, 'eligibility_id');
    }
}
