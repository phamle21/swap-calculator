@extends("layouts.app")

@section("title", __("swap_calculator.title"))

@section("content")
    <!-- Main Grid -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Left Column: Form -->
        <div class="lg:col-span-1">
            @include("components.swap-form", ["pairs" => $pairs ?? []])
        </div>

        <!-- Right Column: Results & History -->
        <div class="space-y-6 lg:col-span-2">
            <!-- Results Card (always present so JS can update without reload) -->
            @include("components.swap-results")

            <!-- History Card -->
            <div class="rounded-lg border border-slate-700 bg-slate-800 p-6">
                @include("components.swap-history", ["history" => $history ?? [], 'pairs' => $pairs ?? []])
            </div>
        </div>
    </div>
@endsection

@push("scripts")
    <script>
        window.SWAP_I18N = {
            confirmDelete: {!! json_encode(__('swap_history.confirm_delete')) !!},
            confirmClear: {!! json_encode(__('swap_history.confirm_clear')) !!},
            confirmYes: {!! json_encode(__('swap_history.confirm_yes')) !!},
            confirmNo: {!! json_encode(__('swap_history.confirm_no')) !!},
            deletedSuccess: {!! json_encode(__('swap_history.deleted_success')) !!},
            deletedCanceled: {!! json_encode(__('swap_history.deleted_canceled')) !!},
            deleteLabel: {!! json_encode(__('swap_history.delete_label')) !!},
            pagePrev: {!! json_encode(__('swap_history.page_prev')) !!},
            pageNext: {!! json_encode(__('swap_history.page_next')) !!},
            allLabel: {!! json_encode(__('swap_history.all_label')) !!},
            // form validation messages (from swap_form.php)
            swapFormValidation: {!! json_encode(__('swap_form.validation')) !!},
            // labels used by client-side validator
            pairLabel: {!! json_encode(__('swap_form.currency_pair')) !!},
            lotLabel: {!! json_encode(__('swap_form.lot_size')) !!},
            swapLongLabel: {!! json_encode(__('swap_form.swap_long')) !!},
            swapShortLabel: {!! json_encode(__('swap_form.swap_short')) !!},
            daysLabel: {!! json_encode(__('swap_form.holding_days')) !!},
            positionLabel: {!! json_encode(__('swap_form.position_type')) !!}
        };
    </script>
    @vite(["resources/js/swap-calculator.js"])
@endpush
