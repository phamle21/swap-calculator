<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SwapCalculationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'id'            => $this->id,
            'pair'          => $this->whenLoaded('pair', fn() => $this->pair->symbol, $this->pair),
            'lot_size'      => (float) $this->lot_size,
            'position_type' => $this->position_type,
            'swap_rate'     => (float) $this->swap_rate,
            'days'          => (int) $this->days,
            'total_swap'    => (float) $this->total_swap,
            'created_at'    => $this->created_at?->format('Y-m-d H:i'),
        ];
    }
}
