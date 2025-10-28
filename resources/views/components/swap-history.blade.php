<div class="mb-6">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-white">{{ __('swap_history.history_heading') }}</h2>
        <div class="flex items-center gap-3">
                <button id="reloadHistory" class="cursor-pointer text-sm text-slate-400 hover:text-white">{{ __('swap_history.reload') }}</button>
                <button id="clearHistory" class="cursor-pointer text-sm text-red-400 hover:text-red-300">{{ __('swap_history.delete') }} All</button>
                <button id="toggleFilterBtn" class="ml-2 inline-flex items-center gap-2 rounded bg-slate-700/30 px-2 py-1 text-sm text-slate-200 hover:bg-slate-700" title="{{ __('swap_history.filter') ?? 'Filter' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L15 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 019 21v-7.586L3.293 6.707A1 1 0 013 6V4z" />
                    </svg>
                    <span class="hidden sm:inline">{{ __('swap_history.filter') ?? 'Filter' }}</span>
                </button>
            </div>
    </div>

    <div id="filterTags" class="mt-3 flex gap-2 flex-wrap"></div>

    <!-- SlideOver filter (hidden) -->
    <div id="filterSlideOver" class="hidden fixed inset-0 z-50">
        <div id="filterBackdrop" class="absolute inset-0 bg-black/50"></div>
        <div id="slidePanel" class="absolute right-0 top-0 h-full w-full max-w-md bg-slate-800 p-6 transform translate-x-full transition-transform">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-white">{{ __('swap_history.filter') }}</h3>
                <button id="closeFilterSlide" class="text-slate-400 hover:text-white">&times;</button>
            </div>

            <div id="filterPanel" class="grid grid-cols-1 gap-3 md:grid-cols-12">
                <div class="md:col-span-12">
                    <label class="block text-xs text-slate-400">{{ __('swap_form.currency_pair') }}</label>
                    <select id="filterPair" class="mt-1 w-full rounded bg-slate-700 px-2 py-2 text-white">
                        <option value="">{{ __('swap_history.all_label') }}</option>
                        @if(isset($pairs) && count($pairs))
                            @foreach($pairs as $p)
                                <option value="{{ $p->symbol }}">{{ $p->symbol }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="md:col-span-6">
                    <label class="block text-xs text-slate-400">{{ __('swap_history.filter_type') }}</label>
                    <select id="filterType" class="mt-1 w-full rounded bg-slate-700 px-2 py-2 text-white">
                        <option value="">{{ __('swap_history.all_label') }}</option>
                        <option value="Long">Long</option>
                        <option value="Short">Short</option>
                    </select>
                </div>

                <div class="md:col-span-6">
                    <label class="block text-xs text-slate-400">{{ __('swap_history.per_page') }}</label>
                    <select id="perPage" class="mt-1 w-full rounded bg-slate-700 px-2 py-2 text-white">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>

                <div class="md:col-span-6">
                    <label class="block text-xs text-slate-400">{{ __('swap_history.filter_from') }}</label>
                    <input id="filterFrom" type="date" class="mt-1 w-full rounded bg-slate-700 px-2 py-2 text-white" />
                </div>

                <div class="md:col-span-6">
                    <label class="block text-xs text-slate-400">{{ __('swap_history.filter_to') }}</label>
                    <input id="filterTo" type="date" class="mt-1 w-full rounded bg-slate-700 px-2 py-2 text-white" />
                </div>

                <div class="md:col-span-6">
                    <label class="block text-xs text-slate-400">{{ __('swap_history.filter_min') }}</label>
                    <input id="filterMin" type="number" step="0.01" class="mt-1 w-full rounded bg-slate-700 px-2 py-2 text-white" placeholder="-" />
                </div>

                <div class="md:col-span-6">
                    <label class="block text-xs text-slate-400">{{ __('swap_history.filter_max') }}</label>
                    <input id="filterMax" type="number" step="0.01" class="mt-1 w-full rounded bg-slate-700 px-2 py-2 text-white" placeholder="-" />
                </div>

                <div class="md:col-span-12 flex items-center justify-end gap-2">
                    <button id="resetFilters" class="px-3 py-2 rounded bg-slate-700 text-sm text-slate-200 hover:bg-slate-600">{{ __('swap_history.reset') ?? 'Reset' }}</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
                <tr class="border-b border-slate-700 text-slate-400">
                <th class="px-4 py-3 text-center text-xs font-semibold uppercase">{{ __('swap_history.th_pair') }}</th>
                <th class="px-4 py-3 text-center text-xs font-semibold uppercase">{{ __('swap_history.th_lot') }}</th>
                <th class="px-4 py-3 text-center text-xs font-semibold uppercase">{{ __('swap_history.th_type') }}</th>
                <th class="px-4 py-3 text-center text-xs font-semibold uppercase">{{ __('swap_history.th_swap_rate') }}</th>
                <th class="px-4 py-3 text-center text-xs font-semibold uppercase">{{ __('swap_history.th_days') }}</th>
                <th class="px-4 py-3 text-center text-xs font-semibold uppercase">{{ __('swap_history.th_total_swap') }}</th>
                <th class="px-4 py-3 text-center text-xs font-semibold uppercase">{{ __('swap_history.th_time') }}</th>
                <th class="px-4 py-3 text-center text-xs font-semibold uppercase">{{ __('swap_history.th_action') }}</th>
            </tr>
        </thead>
        <tbody id="historyBody" class="text-white">
            {{-- @if(isset($history) && count($history))
                @foreach($history as $item)
                    <tr class="border-b border-slate-700 hover:bg-slate-700/50">
                        <td class="text-center px-4 py-3">{{ $item['pair'] ?? $item['currency_pair'] ?? '-' }}</td>
                        <td class="text-center px-4 py-3">{{ $item['lot_size'] ?? '-' }}</td>
                        <td class="text-center px-4 py-3 "><span class="px-2 py-1 rounded text-xs font-semibold bg-green-900/30 text-green-400">{{ $item['position_type'] ?? '-' }}</span></td>
                        <td class="text-center px-4 py-3">{{ $item['swap_rate'] ?? '-' }}</td>
                        <td class="text-center px-4 py-3">{{ $item['days'] ?? $item['holding_days'] ?? '-' }}</td>
                        <td class="text-center px-4 py-3 text-right">{{ $item['total_swap'] ?? '-' }}</td>
                        <td class="text-center px-4 py-3">{{ $item['created_at'] ?? '-' }}</td>
                        <td class="text-center px-4 py-3"><button data-id="{{ $item['id'] ?? '' }}" class="cursor-pointer del-btn text-red-400 text-xs">{{ __('swap_history.delete') }}</button></td>
                    </tr>
                @endforeach
                @else
                <tr>
                    <td class="py-8 text-center text-slate-500" colspan="8">{{ __('swap_history.no_history') }}</td>
                </tr>
            @endif --}}
        </tbody>
    </table>
</div>

<div id="historyPager"></div>
