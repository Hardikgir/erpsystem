<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bank extends Model
{
    use HasFactory;

    protected $fillable = [
        'university_id',
        'bank_name',
        'account_no',
    ];

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }
}
