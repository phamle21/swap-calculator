<?php

namespace App\Filament\Resources\SwapCalculations\Pages;

use App\Filament\Resources\SwapCalculations\SwapCalculationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSwapCalculations extends ListRecords
{
    protected static string $resource = SwapCalculationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
