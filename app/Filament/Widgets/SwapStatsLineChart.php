<?php

namespace App\Filament\Widgets;

use App\Models\CurrencyPair;
use App\Models\SwapCalculation;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class SwapStatsLineChart extends ChartWidget
{
    protected ?string $heading = 'Total Swap — last 7 days';
    protected ?string $maxHeight = '320px';

    protected function getData(): array
    {
        $start = Carbon::today()->subDays(6);
        $labels = collect(range(0, 6))
            ->map(fn(int $i) => $start->copy()->addDays($i)->toDateString())
            ->values();

        $rows = SwapCalculation::query()
            ->selectRaw('DATE(created_at) as d, currency_pair_id, SUM(total_swap) as total')
            ->where('created_at', '>=', $start->startOfDay())
            ->groupBy('d', 'currency_pair_id')
            ->get();

        $pairIds = $rows->pluck('currency_pair_id')->unique()->all();
        $symbols = CurrencyPair::query()
            ->whereIn('id', $pairIds)
            ->pluck('symbol', 'id');

        $datasets = $symbols->map(function (string $symbol, int $pairId) use ($labels, $rows) {
            $byPair = $rows->where('currency_pair_id', $pairId)->keyBy('d');

            $data = $labels->map(fn(string $d) => (float) ($byPair[$d]->total ?? 0.0))->all();

            return [
                'label' => $symbol,
                'data' => $data,
                'tension' => 0.3,
            ];
        })->values()->all();

        return [
            'labels' => $labels->all(),
            'datasets' => $datasets,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
