<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SeatMatrix extends Model
{
    use HasFactory;

    protected $fillable = [
        'university_id',
        'program_id',
        'course_id',
        'college_id',
        'academic_session_id',
        'admission_session_id',
        'mode',
        'start_date',
        'end_date',
        'publish_mode',
        'total_seats',
        'define_category',
    ];

    protected $casts = [
        'mode' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
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

    public function college(): BelongsTo
    {
        return $this->belongsTo(College::class);
    }

    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(Session::class, 'academic_session_id');
    }

    public function admissionSession(): BelongsTo
    {
        return $this->belongsTo(Session::class, 'admission_session_id');
    }

    public function categories(): HasMany
    {
        return $this->hasMany(SeatMatrixCategory::class);
    }
}


