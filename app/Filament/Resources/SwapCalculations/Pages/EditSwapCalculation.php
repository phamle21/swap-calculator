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
}
