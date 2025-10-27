<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class SwapRate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'currency_pair_id',
        'profile_id',
        'swap_long',
        'swap_short',
        'effective_from',
        'effective_to',
        'is_active',
    ];

    protected $casts = [
        'swap_long'      => 'decimal:4',
        'swap_short'     => 'decimal:4',
        'effective_from' => 'date',
        'effective_to'   => 'date',
        'is_active'      => 'boolean',
    ];

    public function pair()
    {
        return $this->belongsTo(CurrencyPair::class, 'currency_pair_id');
    }

    public function profile()
    {
        return $this->belongsTo(SwapProfile::class, 'profile_id');
    }

    public function scopeForDate($q, Carbon|string $date)
    {
        $d = $date instanceof Carbon ? $date->toDateString() : $date;
        return $q->whereDate('effective_from', '<=', $d)
            ->where(function ($s) use ($d) {
                $s->whereNull('effective_to')->orWhereDate('effective_to', '>=', $d);
            });
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }
}
