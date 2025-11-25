<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeePlanItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_id',
        'element_id',
        'amount',
        'semester_no',
        'installment_no',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(FeePlan::class, 'plan_id');
    }

    public function element(): BelongsTo
    {
        return $this->belongsTo(FeeElement::class, 'element_id');
    }
}
