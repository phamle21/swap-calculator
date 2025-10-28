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

    public function create(array $data): SwapCalculation
    {
        return SwapCalculation::create($data);
    }

    public function delete(int $id): bool
    {
        return (bool) SwapCalculation::whereKey($id)->delete();
    }
}
