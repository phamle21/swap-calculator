<?php

namespace App\Filament\Imports;

use App\Models\CurrencyPair;
use App\Models\SwapCalculation;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Arr;
use Illuminate\Support\Number;
use Illuminate\Validation\ValidationException;

class SwapCalculationImporter extends Importer
{
    protected static ?string $model = SwapCalculation::class;

    /**
     * Cấu hình cột cho màn hình map cột khi import.
     * - Có thể map bằng 'currency_pair_id' hoặc 'pair' (EURUSD…)
     * - 'swap_rate' có thể bỏ trống nếu đã có pair + position_type (sẽ suy ra)
     * - 'total_swap' luôn được tính lại nên KHÔNG cần map
     */
    public static function getColumns(): array
    {
        return [
            ImportColumn::make('currency_pair_id')
                ->label('Currency Pair ID')
                ->numeric()
                ->rules(['nullable', 'integer', 'exists:currency_pairs,id']),

            // Or map by symbol
            ImportColumn::make('pair')
                ->label('Currency Pair (symbol)')
                ->rules(['nullable', 'string', 'max:10']),

            ImportColumn::make('lot_size')
                ->numeric()
                ->rules(['required', 'numeric', 'gt:0']),

            ImportColumn::make('position_type')
                ->rules(['required', 'in:Long,Short,long,short']),

            ImportColumn::make('swap_rate')
                ->numeric()
                ->rules(['nullable', 'numeric']),

            ImportColumn::make('days')
                ->numeric()
                ->rules(['required', 'integer', 'gt:0']),
        ];
    }

    /**
     * Always create a new record (can be changed to firstOrNew by a key if you want to update by key).
     */
    public function resolveRecord(): SwapCalculation
    {
        return new SwapCalculation();
    }

    /**
     * Recompute total_swap and relation map before filling.
     * Called by Importer routines in the order you gave them.
     */
    protected function beforeFill(): void
    {
        // Normalize input data
        $data = $this->data;

        // 1) Resolve currency_pair_id from either currency_pair_id or pair (symbol)
        $currencyPairId = Arr::get($data, 'currency_pair_id');
        $symbol = strtoupper(trim((string) (Arr::get($data, 'pair') ?? '')));

        $pairModel = null;

        if ($currencyPairId) {
            $pairModel = CurrencyPair::query()->find($currencyPairId);
        } elseif ($symbol !== '') {
            $pairModel = CurrencyPair::query()->where('symbol', $symbol)->first();
            if ($pairModel) {
                $currencyPairId = $pairModel->id;
                $this->data['currency_pair_id'] = $currencyPairId;
            }
        }

        if (! $currencyPairId || ! $pairModel) {
            throw ValidationException::withMessages([
                'currency_pair_id' => 'Invalid currency_pair_id or pair symbol.',
            ]);
        }

        // 2) Normalize position_type
        $position = Arr::get($data, 'position_type');
        $position = is_string($position) ? ucfirst(strtolower($position)) : 'Long';
        if (! in_array($position, ['Long', 'Short'], true)) {
            throw ValidationException::withMessages([
                'position_type' => 'position_type must be Long or Short.',
            ]);
        }
        $this->data['position_type'] = $position;

        // 3) Determine swap_rate:
        // - If the file already has a swap_rate, use it
        // - If not, deduce it by position_type from currency_pairs: swap_long / swap_short
        $swapRate = Arr::get($data, 'swap_rate');
        if ($swapRate === null || $swapRate === '') {
            $swapRate = $position === 'Short'
                ? $pairModel->swap_short
                : $pairModel->swap_long;
            $this->data['swap_rate'] = $swapRate;
        }

        // 4) Calculate total_swap = lot_size * swap_rate * days
        $lot   = (float) Arr::get($this->data, 'lot_size', 0);
        $days  = (int)   Arr::get($this->data, 'days', 0);
        $rate  = (float) $swapRate;

        $this->data['total_swap'] = round($lot * $rate * $days, 2);
    }

    /**
     * Recalculate one last time right before save to ensure correct calculation if there are changes after beforeFill.
     */
    protected function beforeSave(): void
    {
        if (! $this->record) {
            return;
        }

        $lot  = (float) Arr::get($this->data, 'lot_size', $this->record->lot_size ?? 0);
        $days = (int)   Arr::get($this->data, 'days', $this->record->days ?? 0);
        $rate = (float) Arr::get($this->data, 'swap_rate', $this->record->swap_rate ?? 0);

        $this->record->total_swap = round($lot * $rate * $days, 2);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your swap calculation import has completed and '
            . Number::format($import->successful_rows) . ' '
            . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failed = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failed) . ' '
                . str('row')->plural($failed) . ' failed to import.';
        }

        return $body;
    }
}
