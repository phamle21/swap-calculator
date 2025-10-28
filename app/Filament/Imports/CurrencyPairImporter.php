<?php

namespace App\Filament\Imports;

use App\Models\CurrencyPair;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class CurrencyPairImporter extends Importer
{
    protected static ?string $model = CurrencyPair::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('symbol')
                ->requiredMapping()
                ->rules(['required', 'max:10']),
            ImportColumn::make('base')
                ->requiredMapping()
                ->rules(['required', 'max:6']),
            ImportColumn::make('quote')
                ->requiredMapping()
                ->rules(['required', 'max:6']),
        ];
    }

    public function resolveRecord(): CurrencyPair
    {
        return new CurrencyPair();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your currency pair import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
