<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SwapProfile extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'note',
        'wednesday_multiplier',
        'settings',
    ];

    protected $casts = [
        'wednesday_multiplier' => 'decimal:2',
        'settings'             => 'array',
    ];

    public function rates()
    {
        return $this->hasMany(SwapRate::class, 'profile_id');
    }

    public function calculations()
    {
        return $this->hasMany(SwapCalculation::class, 'profile_id');
    }
}
