<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeatMatrixCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'seat_matrix_id',
        'category_name',
        'direct_seats',
        'counselling_seats',
        'merit_seats',
        'total_seats',
    ];

    public function seatMatrix(): BelongsTo
    {
        return $this->belongsTo(SeatMatrix::class);
    }
}


