<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CurrencyPair extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'symbol',
        'base',
        'quote',
        'digits',
        'is_active',
        'meta',
    ];

    protected $casts = [
        'digits'    => 'integer',
        'is_active' => 'boolean',
        'meta'      => 'array',
    ];

    public function rates()
    {
        return $this->hasMany(SwapRate::class);
    }

    public function calculations()
    {
        return $this->hasMany(SwapCalculation::class);
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }
}
