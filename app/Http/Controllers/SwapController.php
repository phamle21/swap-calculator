<?php

namespace App\Http\Controllers;

use App\DTOs\SwapInputDTO;
use App\Http\Requests\SwapCalculateRequest;
use App\Http\Resources\SwapCalculationResource;
use App\Models\CurrencyPair;
use App\Repositories\SwapCalculationRepository;
use App\Services\SwapService;

class SwapController extends Controller
{
    public function __construct(
        private SwapService $svc,
        private SwapCalculationRepository $calcRepo,
    ) {}

    public function index()
    {
        $pairs = CurrencyPair::active()->orderBy('symbol')->get(['id', 'symbol']);
        return view('swap-calculator', compact('pairs'));
    }

    public function calculate(SwapCalculateRequest $req)
    {
        $dto = new SwapInputDTO(
            pair: $req->pair,
            lotSize: (float)$req->lot_size,
            positionType: $req->position_type,
            swapLong: (float)$req->swap_long,
            swapShort: (float)$req->swap_short,
            days: (int)$req->days,
            crossWednesday: (bool)$req->cross_wednesday,
            profileId: $req->profile_id
        );

        $pairId = CurrencyPair::where('symbol', $dto->pair)->value('id');
        if (!$pairId) {
            return response()->json(['errors' => ['pair' => ['Pair not found']]], 422);
        }

        $swapRate = $dto->chosenRate();
        $total    = $this->svc->calcTotal($dto->lotSize, $swapRate, $dto->days, $dto->crossWednesday, 3.0);

        $row = $this->svc->storeSnapshot($dto, $pairId, $dto->profileId, $swapRate, $total);

        return response()->json([
            'result' => [
                'pair'          => $dto->pair,
                'lot_size'      => $dto->lotSize,
                'position_type' => $dto->positionType,
                'swap_rate'     => $swapRate,
                'days'          => $dto->days,
                'total_swap'    => $total,
                'message'       => $total < 0
                    ? 'Swap âm, cân nhắc không nên giữ lệnh lâu.'
                    : 'Swap dương, có thể xem xét giữ lệnh.',
            ],
            'row' => new SwapCalculationResource($row->load('pair:id,symbol')),
        ]);
    }

    public function history()
    {
        $items = $this->calcRepo->recent(20);
        return response()->json(['items' => SwapCalculationResource::collection($items)]);
    }

    public function destroy(int $id)
    {
        $ok = $this->calcRepo->delete($id);
        return response()->json(['ok' => $ok]);
    }
}
