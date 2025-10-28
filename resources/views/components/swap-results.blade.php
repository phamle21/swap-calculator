<div class="rounded-lg border border-slate-700 bg-slate-800 p-6">
    <h2 class="mb-6 text-xl font-semibold text-white">{{ __('swap_results.results_heading') }}</h2>

    <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="rounded-lg border border-slate-600 bg-slate-700 p-4">
            <p class="mb-1 text-xs uppercase text-slate-400">{{ __('swap_results.pair_label') }}</p>
            <p id="r-pair" class="text-lg font-semibold text-white">-</p>
        </div>
        <div class="rounded-lg border border-slate-600 bg-slate-700 p-4">
            <p class="mb-1 text-xs uppercase text-slate-400">{{ __('swap_results.position_label') }}</p>
            <p id="r-position_type" class="text-lg font-semibold text-white">-</p>
        </div>
        <div class="rounded-lg border border-slate-600 bg-slate-700 p-4">
            <p class="mb-1 text-xs uppercase text-slate-400">{{ __('swap_results.lot_label') }}</p>
            <p id="r-lot_size" class="text-lg font-semibold text-white">-</p>
        </div>

        <div class="rounded-lg border border-slate-600 bg-slate-700 p-4">
            <p class="mb-1 text-xs uppercase text-slate-400">{{ __('swap_results.rate_label') }}</p>
            <p id="r-swap_rate" class="text-lg font-semibold text-white">-</p>
        </div>
        <div class="rounded-lg border border-slate-600 bg-slate-700 p-4">
            <p class="mb-1 text-xs uppercase text-slate-400">{{ __('swap_results.days_label') }}</p>
            <p id="r-days" class="text-lg font-semibold text-white">-</p>
        </div>
        <div class="rounded-lg border border-slate-600 bg-slate-700 p-4">
            <p class="mb-1 text-xs uppercase text-slate-400">{{ __('swap_results.total_label') }}</p>
            <p id="r-total_swap" class="text-lg font-semibold text-white">-</p>
        </div>
    </div>

    <div id="advise" class="hidden rounded-lg border p-4">
        <span class="text-sm"></span>
    </div>
</div>
