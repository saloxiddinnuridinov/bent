<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'BentCrypt') — Ilmiy tadqiqot vositasi</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900">

{{-- Navigatsiya --}}
<nav class="bg-white border-b border-gray-200">
    <div class="max-w-5xl mx-auto px-4 py-3 flex items-center justify-between">
        <a href="{{ route('bent.index') }}"
           class="text-base font-medium text-gray-900">
            BentCrypt
        </a>
        <div class="flex gap-2 text-sm">
            <a href="{{ route('bent.index') }}"
               class="px-3 py-1.5 rounded-lg border border-gray-200
                          hover:bg-gray-50 text-gray-600
                          {{ request()->routeIs('bent.index') ? 'bg-blue-50 text-blue-700 border-blue-200' : '' }}">
                Hisoblash
            </a>
            <a href="{{ route('bent.library') }}"
               class="px-3 py-1.5 rounded-lg border border-gray-200
                          hover:bg-gray-50 text-gray-600
                          {{ request()->routeIs('bent.library') ? 'bg-blue-50 text-blue-700 border-blue-200' : '' }}">
                Kutubxona
            </a>
            <a href="{{ route('sbox.index') }}"
               class="px-3 py-1.5 rounded-lg border border-gray-200
                          hover:bg-gray-50 text-gray-600
                          {{ request()->routeIs('sbox.*') ? 'bg-blue-50 text-blue-700 border-blue-200' : '' }}">
                S-qutilar
            </a>
        </div>
    </div>
</nav>

{{-- Flash xabar --}}
@if(session('success'))
    <div class="max-w-5xl mx-auto px-4 mt-4">
        <div class="bg-green-50 text-green-700 border border-green-200
                        rounded-lg px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    </div>
@endif

{{-- Asosiy kontent --}}
<main class="max-w-5xl mx-auto px-4 py-8">
    @yield('content')
</main>

</body>
</html>
