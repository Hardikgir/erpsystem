<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UniversityRole extends Model
{
    use HasFactory;

    protected $fillable = [
        'university_id',
        'role_code',
        'role_name',
    ];

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }
}
