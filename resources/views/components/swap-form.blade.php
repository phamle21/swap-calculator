<div class="rounded-lg border border-slate-700 bg-slate-800 p-6">
    <h2 class="mb-6 text-xl font-semibold text-white">{{ __('swap_form.calculate_heading') }}</h2>

    <form id="calcForm" class="space-y-4" action="{{ route("swap.calculate") }}" method="POST">
        @csrf

        <!-- Currency Pair -->
        <div>
            <label class="mb-2 block text-sm font-medium text-slate-300">{{ __('swap_form.currency_pair') }}</label>
            <select id="pair" name="pair" class="w-full rounded-lg border border-slate-600 bg-slate-700 px-4 py-2 text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" required>
                <option value="">{{ __('swap_form.select_pair') }}</option>
                <option value="EURUSD" {{ old("pair") == "EURUSD" ? "selected" : "" }}>EURUSD</option>
                <option value="GBPUSD" {{ old("pair") == "GBPUSD" ? "selected" : "" }}>GBPUSD</option>
                <option value="USDJPY" {{ old("pair") == "USDJPY" ? "selected" : "" }}>USDJPY</option>
                <option value="XAUUSD" {{ old("pair") == "XAUUSD" ? "selected" : "" }}>XAUUSD</option>
                <option value="GBPJPY" {{ old("pair") == "GBPJPY" ? "selected" : "" }}>GBPJPY</option>
                <option value="AUDUSD" {{ old("pair") == "AUDUSD" ? "selected" : "" }}>AUDUSD</option>
            </select>
        </div>

        <!-- Lot Size -->
        <div>
            <label class="mb-2 block text-sm font-medium text-slate-300">{{ __('swap_form.lot_size') }}</label>
            <input id="lot_size"
                class="w-full rounded-lg border border-slate-600 bg-slate-700 px-4 py-2 text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                name="lot_size"
                type="number"
                value="{{ old("lot_size", "1") }}"
                step="0.1"
                min="0.1"
                placeholder="1"
                required
            >
        </div>

        <!-- Swap Long -->
        <div>
            <label class="mb-2 block text-sm font-medium text-slate-300">{{ __('swap_form.swap_long') }}</label>
            <input id="swap_long"
                class="w-full rounded-lg border border-slate-600 bg-slate-700 px-4 py-2 text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                name="swap_long"
                type="number"
                value="{{ old("swap_long", "-3.5") }}"
                step="0.1"
                placeholder="-3.5"
                required
            >
        </div>

        <!-- Swap Short -->
        <div>
            <label class="mb-2 block text-sm font-medium text-slate-300">{{ __('swap_form.swap_short') }}</label>
            <input id="swap_short"
                class="w-full rounded-lg border border-slate-600 bg-slate-700 px-4 py-2 text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                name="swap_short"
                type="number"
                value="{{ old("swap_short", "-1.5") }}"
                step="0.1"
                placeholder="-1.5"
                required
            >
        </div>

        <!-- Position Type -->
        <div>
            <label class="mb-2 block text-sm font-medium text-slate-300">{{ __('swap_form.position_type') }}</label>
            <div class="grid grid-cols-2 gap-3">
                <label class="flex items-center">
                        <input class="peer hidden" name="position_type" id="pos_long" type="radio" value="Long" {{ old("position_type", "Long") == "Long" ? "checked" : "" }} required>
                        <span for="pos_long" class="w-full cursor-pointer rounded-lg border border-slate-600 bg-slate-700 px-4 py-2 text-center font-semibold text-slate-300 transition peer-checked:border-blue-600 peer-checked:bg-blue-600 peer-checked:text-white">{{ __('swap_form.long') }}</span>
                </label>
                <label class="flex items-center">
                        <input class="peer hidden" name="position_type" id="pos_short" type="radio" value="Short" {{ old("position_type") == "Short" ? "checked" : "" }} required>
                        <span for="pos_short" class="w-full cursor-pointer rounded-lg border border-slate-600 bg-slate-700 px-4 py-2 text-center font-semibold text-slate-300 transition peer-checked:border-blue-600 peer-checked:bg-blue-600 peer-checked:text-white">{{ __('swap_form.short') }}</span>
                </label>
            </div>
        </div>

        <!-- Holding Days -->
        <div>
            <label class="mb-2 block text-sm font-medium text-slate-300">{{ __('swap_form.holding_days') }}</label>
            <input id="days"
                class="w-full rounded-lg border border-slate-600 bg-slate-700 px-4 py-2 text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                name="days"
                type="number"
                value="{{ old("holding_days", "1") }}"
                min="1"
                placeholder="1"
                required
            >
        </div>

        <!-- Calculate Button -->
    <button class="cursor-pointer mt-6 w-full rounded-lg bg-blue-600 px-4 py-2 font-semibold text-white transition duration-200 hover:bg-blue-700" type="submit">{{ __('swap_form.calculate_button') }}</button>
    </form>
</div>
