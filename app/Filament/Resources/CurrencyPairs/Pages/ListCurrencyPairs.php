<?php

namespace App\Filament\Resources\CurrencyPairs\Pages;

use App\Filament\Resources\CurrencyPairs\CurrencyPairResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCurrencyPairs extends ListRecords
{
    protected static string $resource = CurrencyPairResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
