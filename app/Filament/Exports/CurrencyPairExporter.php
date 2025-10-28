<?php

namespace App\Filament\Exports;

use App\Models\CurrencyPair;
use App\Support\Exports\HandlesExportPermissions;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Number;

class CurrencyPairExporter extends Exporter
{
    use HandlesExportPermissions;

    protected static ?string $model = CurrencyPair::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('symbol'),
            ExportColumn::make('base'),
            ExportColumn::make('quote'),
            ExportColumn::make('is_active'),
            ExportColumn::make('created_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
         static::fixExportPermissions($export);

        $body = 'Your currency pair export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
