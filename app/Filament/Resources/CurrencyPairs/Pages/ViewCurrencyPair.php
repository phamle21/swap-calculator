<?php

namespace App\Filament\Resources\CurrencyPairs\Pages;

use App\Filament\Resources\CurrencyPairs\CurrencyPairResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCurrencyPair extends ViewRecord
{
    protected static string $resource = CurrencyPairResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
