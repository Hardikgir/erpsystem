<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class College extends Model
{
    use HasFactory;

    protected $fillable = [
        'university_id',
        'college_code',
        'college_name',
        'college_type',
        'establish_date',
    ];

    protected $casts = [
        'establish_date' => 'date',
    ];

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }
}
