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
    /**
     * Service layer and repository are injected for testability and SoC.
     */
    public function __construct(
        private SwapService $svc,
        private SwapCalculationRepository $calcRepo,
    ) {}

    /**
     * Render the calculator screen.
     * - Applies locale (server-rendered translations).
     * - Loads active pairs for the select.
     * - Loads latest 10 calculations as history.
     */
    public function index()
    {
        // Localize server strings from session or fallback config
        $locale = session('locale', config('app.locale'));
        App::setLocale($locale);

        // Only id/symbol are needed for the dropdown
        $pairs = CurrencyPair::active()
            ->orderBy('symbol')
            ->get(['id', 'symbol']);

        // Recent history (repository abstracts the query)
        $history = $this->calcRepo->recent(10);

        return view('swap-calculator', compact('pairs', 'history'));
    }

    /**
     * Handle calculation via POST.
     * - Validates with FormRequest.
     * - Maps request to DTO (immutable input contract).
     * - Calculates total using service (business logic).
     * - Persists a snapshot via service + repository.
     * - Returns both a human-friendly result and a Resource row for UI.
     */
    public function calculate(SwapCalculateRequest $req)
    {
        // Localize messages returned in JSON
        $locale = session('locale', config('app.locale'));
        App::setLocale($locale);

        // Build the input DTO to decouple controller from raw request
        $dto = new SwapInputDTO(
            pair: $req->pair,
            lotSize: (float) $req->lot_size,
            positionType: $req->position_type,
            swapLong: (float) $req->swap_long,
            swapShort: (float) $req->swap_short,
            days: (int) $req->days,
            crossWednesday: (bool) $req->cross_wednesday, // triple-swap flag if needed
            profileId: $req->profile_id
        );

        // Resolve pair by primary key (DTO->pair holds id here)
        $pair = CurrencyPair::find($dto->pair, ['id', 'symbol']);
        if (! $pair) {
            // 422 so the client can surface field-level error
            return response()->json(['errors' => ['pair' => ['Pair not found']]], 422);
        }

        $pairId = $pair->id;
        $pairSymbol = $pair->symbol;

        // Choose swap rate based on position (encapsulated in DTO)
        $swapRate = $dto->chosenRate();

        // Core formula: total = lot * rate * days (+ optional Wednesday factor)
        // The 3.0 is the default triple factor used when crossing Wednesday
        $total = $this->svc->calcTotal(
            $dto->lotSize,
            $swapRate,
            $dto->days,
            $dto->crossWednesday,
            3.0
        );

        // Persist snapshot (atomic save of inputs + derived outputs)
        $row = $this->svc->storeSnapshot(
            $dto,
            $pairId,
            $dto->profileId,
            $swapRate,
            $total
        );

        // API response:
        // - 'result' is a flat payload for quick rendering in the UI
        // - 'row' is a full Resource for history table consumption
        return response()->json([
            'result' => [
                'pair'          => $pairSymbol,         // show symbol, not id
                'lot_size'      => $dto->lotSize,
                'position_type' => $dto->positionType,
                'swap_rate'     => $swapRate,
                'days'          => $dto->days,
                'total_swap'    => $total,
                'message'       => $total < 0
                    ? __('swap_results.advice_negative') // warning text
                    : __('swap_results.advice_positive'), // positive hint
            ],
            'row' => new SwapCalculationResource(
                $row->load('pair:id,symbol') // avoid N+1 when serializing
            ),
        ]);
    }

    /**
     * Query history with optional filters and pagination.
     * - Accepts filters via query string.
     * - Delegates search logic to repository.
     * - Returns a Resource collection with pagination meta.
     */
    public function history(\Illuminate\Http\Request $request)
    {
        $filters = [
            'q'             => $request->query('q'),
            'pair'          => $request->query('pair'),
            'position_type' => $request->query('position_type'),
            'date_from'     => $request->query('date_from'),
            'date_to'       => $request->query('date_to'),
            'min_total'     => $request->query('min_total'),
            'max_total'     => $request->query('max_total'),
        ];

        // Clamp per-page to protect the API and UI
        $perPage = (int) max(1, min(100, $request->query('per_page', 10)));

        // Remove empty filters to keep queries clean
        $activeFilters = array_filter($filters, fn($v) => $v !== null && $v !== '');

        $paginator = $this->calcRepo->search($activeFilters, $perPage);

        return SwapCalculationResource::collection($paginator);
    }

    /**
     * Delete all history rows.
     * Useful for demos or resetting state.
     */
    public function clear()
    {
        $ok = $this->calcRepo->deleteAll();

        return response()->json(['ok' => $ok]);
    }

    /**
     * Delete a single history row by id.
     * Returns a simple JSON flag for UI handling.
     */
    public function destroy(int $id)
    {
        $ok = $this->calcRepo->delete($id);

        return response()->json(['ok' => $ok]);
    }
}
