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

    <body class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 px-12 py-6">
        <!-- Header -->
        <div class="mb-12 flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-600 text-2xl font-bold text-white">₹</div>
            <div>
                <h1 class="text-2xl font-semibold text-white">{{ __("layout.brand_title") }}</h1>
                <p class="text-sm text-slate-400">{{ __("layout.brand_subtitle") }}</p>
            </div>
            <div class="ms-auto flex items-center gap-3">
                <div class="flex items-center gap-3">
                    <a class="{{ app()->getLocale() === "en" ? "underline" : "" }} text-sm font-medium text-slate-400 hover:text-slate-600" href="{{ route("lang.switch", "en") }}">EN</a>
                    <a class="{{ app()->getLocale() === "vi" ? "underline" : "" }} text-sm font-medium text-slate-400 hover:text-slate-600" href="{{ route("lang.switch", "vi") }}">VI</a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <main>
            @yield("content")
        </main>

        @stack("scripts")
    </body>

</html>
