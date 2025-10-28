<?php

namespace App\Repositories;

use App\Models\CurrencyPair;
use App\Models\SwapRate;
use Illuminate\Support\Carbon;

/**
 * Repository for accessing swap rate data.
 *
 * This repository encapsulates logic related to currency pair lookups
 * and retrieving effective swap rates for a given date, pair, and profile.
 *
 * It separates data access from the service layer so the business logic
 * remains clean and testable.
 */
class SwapRateRepository
{
    /**
     * Find the CurrencyPair ID by its symbol (e.g. "EURUSD").
     *
     * @param  string  $symbol
     * @return int|null  Returns the pair ID or null if not found.
     */
    public function findPairIdBySymbol(string $symbol): ?int
    {
        return CurrencyPair::where('symbol', $symbol)->value('id');
    }

    /**
     * Retrieve the latest active SwapRate record that is effective for a given date.
     *
     * @param  int  $pairId      Currency pair ID.
     * @param  int|null  $profileId  Optional profile ID (for user-specific rates).
     * @param  Carbon|string  $onDate  Date used to determine which rate is effective.
     * @return SwapRate|null
     *
     * Example usage:
     * ```php
     * $rate = $repo->latestEffective($pairId, $profileId, now());
     * echo $rate?->swap_long;
     * ```
     */
    public function latestEffective(int $pairId, ?int $profileId, Carbon|string $onDate): ?SwapRate
    {
        // Normalize date to string for query
        $d = $onDate instanceof Carbon ? $onDate->toDateString() : $onDate;

        return SwapRate::where('currency_pair_id', $pairId)
            // If profileId is provided, filter rates for that profile
            ->when($profileId, fn($q) => $q->where('profile_id', $profileId))
            // Include only records that are active on the given date
            ->whereDate('effective_from', '<=', $d)
            ->where(function ($q) use ($d) {
                $q->whereNull('effective_to')
                    ->orWhereDate('effective_to', '>=', $d);
            })
            // Latest record by effective_from date wins
            ->orderByDesc('effective_from')
            ->first();
    }
}
