<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SwapCalculation extends Model
{
    protected $fillable = [
        'currency_pair_id',
        'profile_id',
        'lot_size',
        'position_type',
        'swap_rate',
        'days',
        'cross_wednesday',
        'total_swap',
        'note',
        'inputs',
    ];

    protected $casts = [
        'lot_size'        => 'decimal:2',
        'swap_rate'       => 'decimal:4',
        'days'            => 'integer',
        'cross_wednesday' => 'boolean',
        'total_swap'      => 'decimal:4',
        'inputs'          => 'array',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
    ];

    public function pair()
    {
        return $this->belongsTo(CurrencyPair::class, 'currency_pair_id');
    }

    public function profile()
    {
        return $this->belongsTo(SwapProfile::class, 'profile_id');
    }

    public function scopeRecent($q, int $limit = 10)
    {
        return $q->orderByDesc('created_at')->limit($limit);
    }
}
