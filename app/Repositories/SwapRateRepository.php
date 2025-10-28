<?php

namespace App\Repositories;

use App\Models\CurrencyPair;
use App\Models\SwapRate;
use Illuminate\Support\Carbon;

class SwapRateRepository
{
    public function findPairIdBySymbol(string $symbol): ?int
    {
        return CurrencyPair::where('symbol', $symbol)->value('id');
    }

    public function latestEffective(int $pairId, ?int $profileId, Carbon|string $onDate): ?SwapRate
    {
        $d = $onDate instanceof Carbon ? $onDate->toDateString() : $onDate;

        return SwapRate::where('currency_pair_id', $pairId)
            ->when($profileId, fn($q) => $q->where('profile_id', $profileId))
            ->whereDate('effective_from', '<=', $d)
            ->where(function ($q) use ($d) {
                $q->whereNull('effective_to')->orWhereDate('effective_to', '>=', $d);
            })
            ->orderByDesc('effective_from')
            ->first();
    }
}
