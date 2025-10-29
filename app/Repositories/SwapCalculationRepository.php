<?php

namespace App\Repositories;

use App\Models\SwapCalculation;

/**
 * Repository for reading/writing swap calculation records.
 * Keeps data-access concerns out of controllers/services.
 */
class SwapCalculationRepository
{
    /**
     * Get latest N calculations for homepage/history widget.
     *
     * @param  int  $limit
     * @return \Illuminate\Support\Collection<SwapCalculation>
     */
    public function recent(int $limit = 10)
    {
        return SwapCalculation::with('pair:id,symbol')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Search with optional filters and return a paginator.
     * Supported filters:
     * - pair:       string symbol, e.g. "EURUSD"
     * - position_type: "Long" | "Short"
     * - date_from:  Y-m-d (inclusive)
     * - date_to:    Y-m-d (inclusive)
     * - min_total:  float (>=)
     * - max_total:  float (<=)
     *
     * @param  array  $filters
     * @param  int    $perPage 1..100 suggested
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function search(array $filters = [], int $perPage = 10)
    {
        $q = SwapCalculation::with('pair:id,symbol');

        // Free-text query (search across related pair symbol and position_type)
        if (! empty($filters['q'])) {
            $term = trim($filters['q']);
            $q->where(function ($qq) use ($term) {
                // match pair symbol partially
                $qq->whereHas('pair', function ($qqq) use ($term) {
                    $qqq->where('symbol', 'like', "%{$term}%");
                });

                // also allow searching position type (Long/Short)
                $qq->orWhere('position_type', 'like', "%{$term}%");
            });
        }

        // Filter by pair symbol through relation to avoid exposing FK in API
        if (! empty($filters['pair'])) {
            $q->whereHas('pair', function ($qq) use ($filters) {
                $qq->where('symbol', $filters['pair']);
            });
        }

        // Filter by position type
        if (! empty($filters['position_type'])) {
            $q->where('position_type', $filters['position_type']);
        }

        // Date range (created_at)
        if (! empty($filters['date_from'])) {
            $q->whereDate('created_at', '>=', $filters['date_from']);
        }
        if (! empty($filters['date_to'])) {
            $q->whereDate('created_at', '<=', $filters['date_to']);
        }

        // Total swap range
        if (isset($filters['min_total']) && $filters['min_total'] !== '') {
            $q->where('total_swap', '>=', (float) $filters['min_total']);
        }
        if (isset($filters['max_total']) && $filters['max_total'] !== '') {
            $q->where('total_swap', '<=', (float) $filters['max_total']);
        }

        // Newest first; append filters for stable pagination links
        return $q->orderByDesc('created_at')
            ->paginate($perPage)
            ->appends($filters);
    }

    /**
     * Create a calculation row (service computes totals beforehand).
     *
     * @param  array  $data
     * @return SwapCalculation
     */
    public function create(array $data): SwapCalculation
    {
        return SwapCalculation::create($data);
    }

    /**
     * Soft/hard delete single row by PK depending on model config.
     *
     * @param  int  $id
     * @return bool  true if at least one row deleted
     */
    public function delete(int $id): bool
    {
        return (bool) SwapCalculation::whereKey($id)->delete();
    }

    /**
     * Bulk delete all rows (admin-only operation).
     *
     * @return bool
     */
    public function deleteAll(): bool
    {
        return (bool) SwapCalculation::query()->delete();
    }
}
