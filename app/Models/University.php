<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class University extends Model
{
    use HasFactory;

    protected $fillable = [
        'university_code',
        'university_name',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}
