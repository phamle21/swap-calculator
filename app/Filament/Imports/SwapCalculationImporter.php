<?php

namespace App\Filament\Imports;

use App\Models\CurrencyPair;
use App\Models\SwapCalculation;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Illuminate\Support\Arr;
use Illuminate\Support\Number;

class SwapCalculationImporter extends Importer
{
    protected static ?string $model = SwapCalculation::class;

    public static function getColumns(): array
    {
        return [
            // Either currency_pair_id or pair (symbol) must be present
            ImportColumn::make('currency_pair_id')
                ->label('Currency Pair ID')
                ->numeric()
                ->rules(['required_without:pair', 'integer', 'exists:currency_pairs,id']),

            // Lookup-only; will not be saved to DB
            ImportColumn::make('pair')
                ->label('Currency Pair (symbol)')
                ->rules(['required_without:currency_pair_id', 'string', 'max:10']),

            ImportColumn::make('lot_size')
                ->numeric()
                ->rules(['required', 'numeric', 'gt:0']),

            ImportColumn::make('position_type')
                ->rules(['required', 'in:Long,Short,long,short']),

            // Optional; auto-derived from CurrencyPair if empty
            ImportColumn::make('swap_rate')
                ->numeric()
                ->rules(['nullable', 'numeric']),

            ImportColumn::make('days')
                ->numeric()
                ->rules(['required', 'integer', 'gt:0']),

            // Optional boolean
            ImportColumn::make('cross_wednesday')
                ->rules(['nullable']),
        ];
    }

    public function resolveRecord(): SwapCalculation
    {
        // Always insert new record per row
        return new SwapCalculation();
    }

    protected function beforeFill(): void
    {
        // Normalize inputs
        $id     = $this->data['currency_pair_id'] ?? null;
        $symbol = isset($this->data['pair']) ? strtoupper(trim((string)$this->data['pair'])) : '';

        if (isset($this->data['position_type'])) {
            // Normalize "Long"/"Short"
            $this->data['position_type'] = ucfirst(strtolower((string)$this->data['position_type']));
        }

        // --- CRITICAL: Resolve pair by ID first, else by symbol. If neither, fail this row ---
        if ($id) {
            $pair = CurrencyPair::find($id);
            if (! $pair) {
                throw new RowImportFailedException("Row {$this->rowIndex()}: currency_pair_id={$id} does not exist.");
            }
        } elseif ($symbol !== '') {
            $pair = CurrencyPair::where('symbol', $symbol)->first();
            if (! $pair) {
                throw new RowImportFailedException("Row {$this->rowIndex()}: symbol '{$symbol}' not found.");
            }
            // Persist resolved ID for saving
            $this->data['currency_pair_id'] = $pair->id;
        } else {
            throw new RowImportFailedException("Row {$this->rowIndex()}: missing currency_pair_id or pair (symbol).");
        }

        // Validate position_type
        $pos = $this->data['position_type'] ?? 'Long';
        if (!in_array($pos, ['Long', 'Short'], true)) {
            throw new RowImportFailedException("Row {$this->rowIndex()}: position_type must be Long or Short.");
        }

        // Derive swap_rate from pair if missing
        if (($this->data['swap_rate'] ?? '') === '' || $this->data['swap_rate'] === null) {
            $this->data['swap_rate'] = $pos === 'Short' ? $pair->swap_short : $pair->swap_long;
        }

        // Compute total_swap
        $lot  = (float) ($this->data['lot_size'] ?? 0);
        $days = (int)   ($this->data['days'] ?? 0);
        $rate = (float) ($this->data['swap_rate'] ?? 0);
        $this->data['total_swap'] = round($lot * $rate * $days, 2);

        // Do not save lookup column
        unset($this->data['pair']);

        // Normalize optional boolean
        if (array_key_exists('cross_wednesday', $this->data)) {
            $this->data['cross_wednesday'] = (bool) $this->data['cross_wednesday'];
        }
    }

    protected function beforeSave(): void
    {
        // --- CRITICAL: Ensure currency_pair_id is present after resolution ---
        $finalId = $this->data['currency_pair_id'] ?? $this->record->currency_pair_id ?? null;
        if (empty($finalId)) {
            throw new RowImportFailedException(
                "Row {$this->rowIndex()}: missing currency_pair_id after resolution."
            );
        }
        $this->record->currency_pair_id = (int) $finalId;
        // Recompute total to be safe before persisting
        $lot  = (float) Arr::get($this->data, 'lot_size', $this->record->lot_size ?? 0);
        $days = (int)   Arr::get($this->data, 'days', $this->record->days ?? 0);
        $rate = (float) Arr::get($this->data, 'swap_rate', $this->record->swap_rate ?? 0);
        $this->record->total_swap = round($lot * $rate * $days, 2);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        // Summary notification
        $body = 'Your swap calculation import has completed and '
            . Number::format($import->successful_rows) . ' '
            . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failed = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failed) . ' '
                . str('row')->plural($failed) . ' failed to import.';
        }

        return $body;
    }

    /** Helpers */

    protected function rowIndex(): int
    {
        // Approximate 1-based row index for user-friendly error messages
        return (int) $this->import->successful_rows
            + (int) $this->import->getFailedRowsCount()
            + 1;
    }
}
