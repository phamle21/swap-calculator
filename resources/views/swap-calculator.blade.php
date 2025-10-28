<!-- resources/views/swap-calculator.blade.php -->
<!DOCTYPE html>
<html lang="vi">

    <head>
        <meta charset="UTF-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Swap Calculator - Forex Overnight Fee Calculator</title>
        @vite(["resources/css/app.css"])
    </head>

    <body class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 p-6">
        <div class="mx-auto max-w-7xl">
            <!-- Header -->
            <div class="mb-12 flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-600 text-2xl font-bold text-white">₹</div>
                <div>
                    <h1 class="text-4xl font-bold text-white">Swap Calculator</h1>
                </div>
            </div>

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
        </div>
        @vite(["resources/js/swap-calculator.js"])
    </body>

</html>
