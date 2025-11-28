<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseDocumentMapping extends Model
{
    use HasFactory;

    protected $fillable = [
        'university_id',
        'program_id',
        'course_id',
        'session_id',
        'domicile',
        'document_id',
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

    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class);
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(CourseDocument::class, 'document_id');
    }
}
