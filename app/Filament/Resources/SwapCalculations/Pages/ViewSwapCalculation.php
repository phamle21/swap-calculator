<?php

namespace App\Filament\Resources\SwapCalculations\Pages;

use App\Filament\Resources\SwapCalculations\SwapCalculationResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSwapCalculation extends ViewRecord
{
    protected static string $resource = SwapCalculationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
