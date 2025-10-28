<?php

namespace App\Http\Controllers;

use App\DTOs\SwapInputDTO;
use App\Http\Requests\SwapCalculateRequest;
use App\Http\Resources\SwapCalculationResource;
use App\Models\CurrencyPair;
use App\Repositories\SwapCalculationRepository;
use App\Services\SwapService;
use Illuminate\Support\Facades\App;

class SwapController extends Controller
{
    public function __construct(
        private SwapService $svc,
        private SwapCalculationRepository $calcRepo,
    ) {}

    public function index()
    {
        // Apply locale from session if present so server-rendered strings are localized
        $locale = session('locale', config('app.locale'));
        App::setLocale($locale);

        $pairs = CurrencyPair::active()->orderBy('symbol')->get(['id', 'symbol']);
        $history = $this->calcRepo->recent(10);

        return view('swap-calculator', compact('pairs', 'history'));
    }

    public function calculate(SwapCalculateRequest $req)
    {
        // Apply locale from session if present so server-rendered strings are localized
        $locale = session('locale', config('app.locale'));
        App::setLocale($locale);

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

        $pair = CurrencyPair::find($dto->pair, ['id', 'symbol']);
        if (!$pair) {
            return response()->json(['errors' => ['pair' => ['Pair not found']]], 422);
        }

        $pairId = $pair->id;
        $pairSymbol = $pair->symbol;

        $swapRate = $dto->chosenRate();
        $total    = $this->svc->calcTotal($dto->lotSize, $swapRate, $dto->days, $dto->crossWednesday, 3.0);

        $row = $this->svc->storeSnapshot($dto, $pairId, $dto->profileId, $swapRate, $total);

        return response()->json([
            'result' => [
                // return symbol (human-friendly) instead of numeric id
                'pair'          => $pairSymbol,
                'lot_size'      => $dto->lotSize,
                'position_type' => $dto->positionType,
                'swap_rate'     => $swapRate,
                'days'          => $dto->days,
                'total_swap'    => $total,
                'message'       => $total < 0
                    ? __('swap_results.advice_negative')
                    : __('swap_results.advice_positive'),
            ],
            'row' => new SwapCalculationResource($row->load('pair:id,symbol')),
        ]);
    }

    public function history(\Illuminate\Http\Request $request)
    {
        $filters = [
            'pair' => $request->query('pair'),
            'position_type' => $request->query('position_type'),
            'date_from' => $request->query('date_from'),
            'date_to' => $request->query('date_to'),
            'min_total' => $request->query('min_total'),
            'max_total' => $request->query('max_total'),
        ];

        $perPage = (int) max(1, min(100, $request->query('per_page', 10)));

        $paginator = $this->calcRepo->search(array_filter($filters, function ($v) { return $v !== null && $v !== ''; }), $perPage);

        // Return a resource collection which includes pagination meta
        return SwapCalculationResource::collection($paginator);
    }

    public function clear()
    {
        $ok = $this->calcRepo->deleteAll();
        return response()->json(['ok' => $ok]);
    }

    public function destroy(int $id)
    {
        $ok = $this->calcRepo->delete($id);
        return response()->json(['ok' => $ok]);
    }
}
