<?php

namespace App\Filament\Resources\SwapCalculations\Pages;

use App\Filament\Resources\SwapCalculations\SwapCalculationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSwapCalculation extends CreateRecord
{
    protected static string $resource = SwapCalculationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $positionType = $data['position_type'];
        if ($positionType === 'Long') {
            $swapRate = $data['swap_long'];
        } else {
            $swapRate = $data['swap_short'];
        }
        $data['swap_rate'] = $swapRate;
        $lotSize = $data['lot_size'];
        $days = $data['days'];

        $data['total_swap'] = $lotSize * $swapRate * $days;

        return $data;
    }
}
