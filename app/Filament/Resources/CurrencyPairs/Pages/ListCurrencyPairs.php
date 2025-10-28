<?php

namespace App\Filament\Resources\CurrencyPairs\Pages;

use App\Filament\Exports\CurrencyPairExporter;
use App\Filament\Imports\CurrencyPairImporter;
use App\Filament\Resources\CurrencyPairs\CurrencyPairResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Actions\Exports\Models\Export;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListCurrencyPairs extends ListRecords
{
    protected static string $resource = CurrencyPairResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
                ->importer(CurrencyPairImporter::class),
            ExportAction::make()
                ->exporter(CurrencyPairExporter::class),
            CreateAction::make(),
        ];
    }
}
