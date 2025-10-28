<?php

namespace App\Filament\Resources\SwapCalculations\Pages;

use App\Filament\Exports\SwapCalculationExporter;
use App\Filament\Imports\SwapCalculationImporter;
use App\Filament\Resources\SwapCalculations\SwapCalculationResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListSwapCalculations extends ListRecords
{
    protected static string $resource = SwapCalculationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
                ->importer(SwapCalculationImporter::class),
            ExportAction::make()
                ->exporter(SwapCalculationExporter::class),
            CreateAction::make(),
        ];
    }
}
