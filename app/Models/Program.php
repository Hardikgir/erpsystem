<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
        'university_id',
        'program_code',
        'program_name',
    ];

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }
}
