<?php

namespace App\Repositories;

use App\Models\SwapCalculation;

class SwapCalculationRepository
{
    public function recent(int $limit = 10)
    {
        return SwapCalculation::with('pair:id,symbol')
            ->orderByDesc('created_at')->limit($limit)->get();
    }

    /**
     * Search swap calculations by filters and return paginated result.
     * Supported filters: pair (symbol), position_type, date_from, date_to, min_total, max_total
     */
    public function search(array $filters = [], int $perPage = 10)
    {
        $q = SwapCalculation::with('pair:id,symbol');

        if (!empty($filters['pair'])) {
            $q->whereHas('pair', function ($qq) use ($filters) {
                $qq->where('symbol', $filters['pair']);
            });
        }

        if (!empty($filters['position_type'])) {
            $q->where('position_type', $filters['position_type']);
        }

        if (!empty($filters['date_from'])) {
            $q->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $q->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (isset($filters['min_total']) && $filters['min_total'] !== '') {
            $q->where('total_swap', '>=', (float)$filters['min_total']);
        }

        if (isset($filters['max_total']) && $filters['max_total'] !== '') {
            $q->where('total_swap', '<=', (float)$filters['max_total']);
        }

        return $q->orderByDesc('created_at')->paginate($perPage)->appends($filters);
    }

    public function create(array $data): SwapCalculation
    {
        return SwapCalculation::create($data);
    }

    public function delete(int $id): bool
    {
        return (bool) SwapCalculation::whereKey($id)->delete();
    }

    public function deleteAll(): bool
    {
        return (bool) SwapCalculation::query()->delete();
    }
}
