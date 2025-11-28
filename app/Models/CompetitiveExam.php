<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetitiveExam extends Model
{
    use HasFactory;

    protected $fillable = [
        'university_id',
        'exam_name',
        'exam_code',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }
}
