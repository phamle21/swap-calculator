<div class="mb-6 flex items-center justify-between">
    <h2 class="text-xl font-semibold text-white">{{ __('swap_history.history_heading') }}</h2>
    <div class="flex items-center gap-3">
    <button id="reloadHistory" class="cursor-pointer text-sm text-slate-400 hover:text-white">{{ __('swap_history.reload') }}</button>
    </div>
</div>

<div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
                <tr class="border-b border-slate-700 text-slate-400">
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase">{{ __('swap_history.th_pair') }}</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase">{{ __('swap_history.th_lot') }}</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase">{{ __('swap_history.th_type') }}</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase">{{ __('swap_history.th_swap_rate') }}</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase">{{ __('swap_history.th_days') }}</th>
                <th class="px-4 py-3 text-right text-xs font-semibold uppercase">{{ __('swap_history.th_total_swap') }}</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase">{{ __('swap_history.th_time') }}</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase">{{ __('swap_history.th_action') }}</th>
            </tr>
        </thead>
        <tbody id="historyBody" class="text-white">
            @if(isset($history) && count($history))
                @foreach($history as $item)
                    <tr class="border-b border-slate-700 hover:bg-slate-700/50">
                        <td class="text-center px-4 py-3">{{ $item['pair'] ?? $item['currency_pair'] ?? '-' }}</td>
                        <td class="text-center px-4 py-3">{{ $item['lot_size'] ?? '-' }}</td>
                        <td class="text-center px-4 py-3">{{ $item['position_type'] ?? '-' }}</td>
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
            @endif
        </tbody>
    </table>
</div>
