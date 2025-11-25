<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeePackageItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_id',
        'element_id',
        'pattern',
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(FeePackage::class, 'package_id');
    }

    public function element(): BelongsTo
    {
        return $this->belongsTo(FeeElement::class, 'element_id');
    }
}
