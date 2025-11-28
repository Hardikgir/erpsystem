<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EligibilityItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'eligibility_id',
        'min_qualification_id',
        'min_marks',
        'board_id',
        'exam_id',
        'min_percentile',
    ];

    public function eligibilityCriteria(): BelongsTo
    {
        return $this->belongsTo(EligibilityCriteria::class, 'eligibility_id');
    }

    public function minimumQualification(): BelongsTo
    {
        return $this->belongsTo(MinimumQualification::class, 'min_qualification_id');
    }

    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }

    public function competitiveExam(): BelongsTo
    {
        return $this->belongsTo(CompetitiveExam::class, 'exam_id');
    }
}
