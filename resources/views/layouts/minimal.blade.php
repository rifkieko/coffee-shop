<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', "Pala's Kopi") }}</title>
        @vite(['resources/css/app.css'])
    </head>
    <body class="font-sans text-[#2A1A13] bg-[#f5f6fb]">
        <main class="min-h-screen flex items-start justify-center py-10 px-4 sm:px-6 lg:px-8">
            <div class="w-full max-w-4xl space-y-6">
                @yield('content')
            </div>
        </main>
        @stack('scripts')
    </body>
</html>
