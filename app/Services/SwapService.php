<?php

namespace App\Services;

use App\DTOs\SwapInputDTO;
use App\Repositories\SwapCalculationRepository;
use App\Repositories\SwapRateRepository;
use Illuminate\Support\Carbon;
use RuntimeException;

/**
 * Application service for swap calculation workflows.
 * - Resolves effective swap rate (optionally profile-scoped) from DB.
 * - Computes total swap with optional Wednesday multiplier.
 * - Persists an immutable snapshot of inputs + derived outputs.
 */
class SwapService
{
    public function __construct(
        private SwapCalculationRepository $calcRepo,
        private SwapRateRepository $rateRepo,
    ) {}

    /**
     * Resolve the effective swap rate for a symbol on a given date.
     * Honors profile-specific rates when provided.
     *
     * @param  string             $symbol     Pair symbol, e.g. "EURUSD"
     * @param  int|null           $profileId  Optional profile scope
     * @param  "Long"|"Short"     $posType    Position side
     * @param  Carbon|string|null $onDate     Valuation date (defaults to today)
     * @return float                           The rate to apply (USD/lot/day)
     *
     * @throws RuntimeException if pair or rate cannot be found
     */
    public function resolveRateFromDB(string $symbol, ?int $profileId, string $posType, Carbon|string $onDate = null): float
    {
        // Map symbol -> currency_pairs.id
        $pairId = $this->rateRepo->findPairIdBySymbol($symbol);
        if (! $pairId) {
            throw new RuntimeException('Currency pair not found');
        }

        // Pick the latest effective rate record for the date window
        $onDate ??= now()->toDateString();
        $rate = $this->rateRepo->latestEffective($pairId, $profileId, $onDate);
        if (! $rate) {
            throw new RuntimeException('No effective rate found');
        }

        // Choose the side-specific leg
        return $posType === 'Long'
            ? (float) $rate->swap_long
            : (float) $rate->swap_short;
    }

    /**
     * Compute total swap with optional Wednesday (triple) adjustment.
     *
     * Formula:
     *   total = lot * rate * daysEff
     * where:
     *   daysEff = days            (default)
     *           = (days - 1) + M  if crossWed=true and M provided (e.g. 3.0)
     *
     * @param  float      $lot
     * @param  float      $rate
     * @param  int        $days
     * @param  bool       $crossWed  If the holding crosses Wednesday
     * @param  float|null $wedMult   Wednesday multiplier (e.g. 3.0)
     * @return float
     */
    public function calcTotal(float $lot, float $rate, int $days, bool $crossWed = false, ?float $wedMult = null): float
    {
        $daysEff = ($crossWed && $wedMult)
            ? ($days - 1) + $wedMult // replace 1 day with multiplier
            : $days;

        return $lot * $rate * $daysEff;
    }

    /**
     * Persist a calculation snapshot.
     * Saves normalized inputs and derived outputs in one place for auditing.
     *
     * @param  SwapInputDTO $dto
     * @param  int          $pairId
     * @param  int|null     $profileId
     * @param  float        $resolvedRate
     * @param  float        $total
     * @return \App\Models\SwapCalculation
     */
    public function storeSnapshot(SwapInputDTO $dto, int $pairId, ?int $profileId, float $resolvedRate, float $total)
    {
        return $this->calcRepo->create([
            'currency_pair_id' => $pairId,
            'profile_id'       => $profileId,
            'lot_size'         => $dto->lotSize,
            'position_type'    => $dto->positionType,
            'swap_rate'        => $resolvedRate,     // the actual side-specific rate used
            'days'             => $dto->days,
            'cross_wednesday'  => $dto->crossWednesday,
            'total_swap'       => $total,            // final computed result
            // Raw inputs stored for traceability and reproducibility
            'inputs'           => [
                'pair'        => $dto->pair,         // may be id or symbol per your DTO contract
                'swap_long'   => $dto->swapLong,
                'swap_short'  => $dto->swapShort,
            ],
        ]);
    }
}
