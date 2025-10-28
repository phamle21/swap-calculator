<div class="rounded-lg border border-slate-700 bg-slate-800 p-6">
    <h2 class="mb-6 text-xl font-semibold text-white">{{ __("swap_form.calculate_heading") }}</h2>

    <form class="space-y-4" id="calcForm" action="{{ route("swap.calculate") }}" method="POST">
        @csrf

        <!-- Currency Pair -->
        <div>
            <label class="mb-2 block text-sm font-medium text-slate-300">{{ __("swap_form.currency_pair") }}</label>
            <select class="w-full rounded-lg border border-slate-600 bg-slate-700 px-4 py-2 text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" id="pair" name="pair">
                <option value="">{{ __("swap_form.select_pair") }}</option>
                @foreach ($pairs as $pair)
                    <option value="{{ $pair->id }}" {{ old("pair") == $pair->id ? "selected" : "" }}>{{ $pair->symbol }}</option>
                @endforeach
            </select>
        </div>

        <!-- Lot Size -->
        <div>
            <label class="mb-2 block text-sm font-medium text-slate-300">{{ __("swap_form.lot_size") }}</label>
            <input
                class="w-full rounded-lg border border-slate-600 bg-slate-700 px-4 py-2 text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                id="lot_size"
                name="lot_size"
                type="number"
                value="{{ old("lot_size", "1") }}"
                step="0.1"
                placeholder="1"
                required
            >
        </div>

        <!-- Swap Long -->
        <div>
            <label class="mb-2 block text-sm font-medium text-slate-300">{{ __("swap_form.swap_long") }}</label>
            <input
                class="w-full rounded-lg border border-slate-600 bg-slate-700 px-4 py-2 text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                id="swap_long"
                name="swap_long"
                type="number"
                value="{{ old("swap_long", "-3.5") }}"
                step="0.1"
                placeholder="-3.5"
            >
        </div>

        <!-- Swap Short -->
        <div>
            <label class="mb-2 block text-sm font-medium text-slate-300">{{ __("swap_form.swap_short") }}</label>
            <input
                class="w-full rounded-lg border border-slate-600 bg-slate-700 px-4 py-2 text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                id="swap_short"
                name="swap_short"
                type="number"
                value="{{ old("swap_short", "-1.5") }}"
                step="0.1"
                placeholder="-1.5"
            >
        </div>

        <!-- Position Type -->
        <div>
            <label class="mb-2 block text-sm font-medium text-slate-300">{{ __("swap_form.position_type") }}</label>
            <div class="grid grid-cols-2 gap-3">
                <label class="flex items-center">
                    <input
                        class="peer hidden"
                        id="pos_long"
                        name="position_type"
                        type="radio"
                        value="Long"
                        {{ old("position_type", "Long") == "Long" ? "checked" : "" }}
                        required
                    >
                    <span class="w-full cursor-pointer rounded-lg border border-slate-600 bg-slate-700 px-4 py-2 text-center font-semibold text-slate-300 transition peer-checked:border-blue-600 peer-checked:bg-blue-600 peer-checked:text-white" for="pos_long">{{ __("swap_form.long") }}</span>
                </label>
                <label class="flex items-center">
                    <input
                        class="peer hidden"
                        id="pos_short"
                        name="position_type"
                        type="radio"
                        value="Short"
                        {{ old("position_type") == "Short" ? "checked" : "" }}
                        required
                    >
                    <span class="w-full cursor-pointer rounded-lg border border-slate-600 bg-slate-700 px-4 py-2 text-center font-semibold text-slate-300 transition peer-checked:border-blue-600 peer-checked:bg-blue-600 peer-checked:text-white" for="pos_short">{{ __("swap_form.short") }}</span>
                </label>
            </div>
        </div>

        <!-- Holding Days -->
        <div>
            <label class="mb-2 block text-sm font-medium text-slate-300">{{ __("swap_form.holding_days") }}</label>
            <input
                class="w-full rounded-lg border border-slate-600 bg-slate-700 px-4 py-2 text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                id="days"
                name="days"
                type="number"
                value="{{ old("holding_days", "1") }}"
                placeholder="1"
                required
            >
        </div>

        <!-- Calculate Button -->
        <button class="mt-6 w-full cursor-pointer rounded-lg bg-blue-600 px-4 py-2 font-semibold text-white transition duration-200 hover:bg-blue-700" type="submit">{{ __("swap_form.calculate_button") }}</button>
    </form>
</div>
