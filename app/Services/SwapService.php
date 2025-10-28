<?php

namespace App\Services;

use App\DTOs\SwapInputDTO;
use App\Repositories\SwapCalculationRepository;
use App\Repositories\SwapRateRepository;
use Illuminate\Support\Carbon;
use RuntimeException;

class SwapService
{
    public function __construct(
        private SwapCalculationRepository $calcRepo,
        private SwapRateRepository $rateRepo,
    ) {}

    public function resolveRateFromDB(string $symbol, ?int $profileId, string $posType, Carbon|string $onDate = null): float
    {
        $pairId = $this->rateRepo->findPairIdBySymbol($symbol);
        if (!$pairId) throw new RuntimeException('Currency pair not found');

        $onDate ??= now()->toDateString();
        $rate = $this->rateRepo->latestEffective($pairId, $profileId, $onDate);
        if (!$rate) throw new RuntimeException('No effective rate found');

        return $posType === 'Long' ? (float)$rate->swap_long : (float)$rate->swap_short;
    }

    public function calcTotal(float $lot, float $rate, int $days, bool $crossWed = false, ?float $wedMult = null): float
    {
        $daysEff = $crossWed && $wedMult ? ($days - 1) + $wedMult : $days;
        return $lot * $rate * $daysEff;
    }

    public function storeSnapshot(SwapInputDTO $dto, int $pairId, ?int $profileId, float $resolvedRate, float $total)
    {
        return $this->calcRepo->create([
            'currency_pair_id' => $pairId,
            'profile_id'       => $profileId,
            'lot_size'         => $dto->lotSize,
            'position_type'    => $dto->positionType,
            'swap_rate'        => $resolvedRate,
            'days'             => $dto->days,
            'cross_wednesday'  => $dto->crossWednesday,
            'total_swap'       => $total,
            'inputs'           => [
                'pair' => $dto->pair,
                'swap_long' => $dto->swapLong,
                'swap_short' => $dto->swapShort,
            ],
        ]);
    }
}
