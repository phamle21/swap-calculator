<!DOCTYPE html>
<html lang="{{ str_replace("_", "-", app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield("title", "Forex Swap Calculator")</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com" rel="preconnect">
        <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Styles -->
        @vite(["resources/css/app.css", "resources/js/app.js"])

        @stack("styles")
    </head>

    <body class="min-h-screen bg-gray-50 antialiased">
        <header class="border-b border-white/10">
            <div class="mx-auto flex max-w-6xl items-center gap-3 px-4 py-6">
                <div class="grid h-10 w-10 place-items-center rounded-xl bg-blue-500/20 ring-1 ring-white/10">
                    <span class="text-xl">₹</span>
                </div>
                <div>
                    <h1 class="text-2xl font-semibold">Swap Calculator</h1>
                    <p class="text-sm text-slate-400">Forex Overnight Fee Calculator</p>
                </div>
                <div class="ms-auto text-sm text-slate-400">
                    {{ now()->format("Y-m-d H:i") }}
                </div>
            </div>
        </header>
        
        <!-- Main Content -->
        <main>
            @yield("content")
        </main>

        @stack("scripts")
    </body>

</html>
