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
                @include("components.swap-history", ["history" => $history ?? []])
            </div>
        </div>
    </div>
@endsection

@push("scripts")
    <script src="{{ asset("js/swap-calculator.js") }}" defer></script>
    @vite(["resources/js/swap-calculator.js"])
@endpush
