<?php

namespace App\Filament\Resources\SwapCalculations\Pages;

use App\Filament\Resources\SwapCalculations\SwapCalculationResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditSwapCalculation extends EditRecord
{
    protected static string $resource = SwapCalculationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $positionType = $data['position_type'];
        if ($positionType === 'Long') {
            $swapRate = $data['inputs']['swap_long'];
        } else {
            $swapRate = $data['inputs']['swap_short'];
        }

        $data['swap_rate'] = $swapRate;
        $lotSize = $data['lot_size'];
        $days = $data['days'];

        $data['total_swap'] = $lotSize * $swapRate * $days;

        return $data;
    }
}
