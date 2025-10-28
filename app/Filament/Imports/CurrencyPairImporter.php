<?php

namespace App\Filament\Imports;

use App\Models\CurrencyPair;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;
use Illuminate\Validation\ValidationException;

/**
 * Importer for CurrencyPair data.
 * - Handles duplicate detection based on unique symbol.
 * - Restores soft-deleted records when re-imported.
 * - Normalizes casing and ensures validation errors are caught per row.
 */
class CurrencyPairImporter extends Importer
{
    protected static ?string $model = CurrencyPair::class;

    /**
     * Define importable columns and their validation rules.
     */
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

    /**
     * Try to find an existing record by symbol (including soft-deleted ones).
     * - If found: update it.
     * - If found but soft-deleted: will be restored in beforeFill().
     * - If not found: a new model instance will be created.
     */
    public function resolveRecord(): CurrencyPair
    {
        $symbol = strtoupper(trim((string) ($this->data['symbol'] ?? '')));

        if ($symbol === '') {
            return new CurrencyPair();
        }

        $existing = CurrencyPair::withTrashed()
            ->where('symbol', $symbol)
            ->first();

        return $existing ?? new CurrencyPair();
    }

    /**
     * Hook executed before filling model data.
     * - Normalize input case (symbol/base/quote).
     * - Restore record if previously soft-deleted.
     * - Catch validation errors gracefully.
     */
    protected function beforeFill(): void
    {
        try {
            // Normalize inputs to uppercase for consistency
            foreach (['symbol', 'base', 'quote'] as $field) {
                if (isset($this->data[$field])) {
                    $this->data[$field] = strtoupper(trim((string) $this->data[$field]));
                }
            }

            // If record exists and is soft-deleted, restore it before update
            if ($this->record && method_exists($this->record, 'trashed') && $this->record->trashed()) {
                $this->record->restore();
            }
        } catch (ValidationException $e) {
            // Validation failure for this row → mark as failed and stop
            $this->import->fail(
                row: $this->originalData,
                message: "Row {$this->rowIndex()}: " . implode('; ', $e->errors())
            );
        } catch (\Throwable $e) {
            // Any other unexpected exception (DB, type, etc.)
            $this->import->fail(
                row: $this->originalData,
                message: "Row {$this->rowIndex()} error: " . $e->getMessage()
            );
        }
    }

    /**
     * Helper to get current row index for error reporting.
     */
    protected function rowIndex(): int
    {
        return (int) $this->import->successful_rows + (int) $this->import->getFailedRowsCount() + 1;
    }

    /**
     * Custom notification shown after import completes.
     */
    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your currency pair import has completed and '
              . Number::format($import->successful_rows) . ' '
              . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' '
                   . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
