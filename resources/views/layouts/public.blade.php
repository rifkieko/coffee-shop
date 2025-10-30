<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Coffee Shop') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        <style>
            [x-cloak]{display:none!important;}
            html{scroll-behavior:smooth;}
        </style>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased font-sans bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 min-h-screen flex flex-col">
        <header x-data="{ mobileOpen: false }" class="bg-white dark:bg-gray-800 shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <x-application-logo class="w-9 h-9 sm:w-10 sm:h-10 text-indigo-600" />
                    <a href="{{ route('home') }}" class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-gray-100">
                        {{ config('app.name', 'Coffee Shop') }}
                    </a>
                </div>
                <div class="flex items-center gap-3">
                    <nav class="hidden md:flex items-center gap-5 text-sm font-medium">
                        <a href="{{ route('home') }}" class="transition hover:text-indigo-600 dark:hover:text-indigo-300">{{ __('Menu') }}</a>
                        <a href="{{ route('cart.index') }}" class="transition hover:text-indigo-600 dark:hover:text-indigo-300">{{ __('Keranjang') }}</a>
                        @auth
                            @if (auth()->user()?->isAdmin())
                                <a href="{{ route('dashboard') }}" class="transition hover:text-indigo-600 dark:hover:text-indigo-300">{{ __('Dashboard') }}</a>
                            @else
                                <a href="{{ route('customer.orders.history') }}" class="transition hover:text-indigo-600 dark:hover:text-indigo-300">{{ __('Riwayat Pesanan') }}</a>
                            @endif
                        @endauth
                    </nav>
                    <div class="hidden md:block">
                        @auth
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button type="button" class="inline-flex items-center gap-2 rounded-full border border-gray-200 px-3 py-1.5 text-sm font-medium text-gray-600 transition hover:border-indigo-300 hover:text-indigo-600 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:border-indigo-500 dark:hover:text-indigo-300">
                                        <span class="max-w-[9rem] truncate">{{ auth()->user()->name }}</span>
                                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.177l3.71-3.945a.75.75 0 011.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <x-dropdown-link :href="route('profile.edit')">
                                        {{ __('Profil') }}
                                    </x-dropdown-link>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <x-dropdown-link :href="route('logout')"
                                                         onclick="event.preventDefault(); this.closest('form').submit();">
                                            {{ __('Keluar') }}
                                        </x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        @else
                            <div class="flex items-center gap-3 text-sm font-medium">
                                <a href="{{ route('login') }}" class="transition hover:text-indigo-600 dark:hover:text-indigo-300">{{ __('Masuk') }}</a>
                                <a href="{{ route('register') }}" class="inline-flex items-center rounded-full border border-indigo-200 px-3 py-1 text-indigo-600 transition hover:border-indigo-400 hover:text-indigo-700 dark:border-indigo-500/40 dark:text-indigo-200 dark:hover:border-indigo-300 dark:hover:text-indigo-100">
                                    {{ __('Daftar') }}
                                </a>
                            </div>
                        @endauth
                    </div>
                    <button type="button"
                            class="md:hidden inline-flex items-center justify-center rounded-lg border border-gray-200 p-2 text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-700"
                            @click="mobileOpen = !mobileOpen"
                            aria-controls="mobile-menu"
                            :aria-expanded="mobileOpen.toString()">
                        <svg x-show="!mobileOpen" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg x-show="mobileOpen" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            <nav id="mobile-menu"
                 x-cloak
                 x-show="mobileOpen"
                 x-transition
                 class="md:hidden border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                <div class="px-4 py-4 space-y-4 text-sm font-medium">
                    <a href="{{ route('home') }}" class="block rounded-lg px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">{{ __('Menu') }}</a>
                    <a href="{{ route('cart.index') }}" class="block rounded-lg px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">{{ __('Keranjang') }}</a>
                    @auth
                        @if (auth()->user()?->isAdmin())
                            <a href="{{ route('dashboard') }}" class="block rounded-lg px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">{{ __('Dashboard') }}</a>
                        @else
                            <a href="{{ route('customer.orders.history') }}" class="block rounded-lg px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">{{ __('Riwayat Pesanan') }}</a>
                        @endif
                        <hr class="border-gray-200 dark:border-gray-700" />
                        <a href="{{ route('profile.edit') }}" class="block rounded-lg px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">{{ __('Profil') }}</a>
                        <form method="POST" action="{{ route('logout') }}" class="mt-2">
                            @csrf
                            <button type="submit" class="w-full rounded-lg px-3 py-2 text-left text-rose-600 hover:bg-rose-50 dark:text-rose-300 dark:hover:bg-rose-500/10">
                                {{ __('Keluar') }}
                            </button>
                        </form>
                    @else
                        <div class="flex flex-col gap-2">
                            <a href="{{ route('login') }}" class="rounded-lg px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">{{ __('Masuk') }}</a>
                            <a href="{{ route('register') }}" class="rounded-lg px-3 py-2 bg-indigo-600 text-white text-center hover:bg-indigo-500">
                                {{ __('Daftar') }}
                            </a>
                        </div>
                    @endauth
                </div>
            </nav>
        </header>

        <main class="flex-1">
            @if (session('status'))
                <div class="bg-emerald-500/10 border border-emerald-200 text-emerald-700 dark:bg-emerald-500/20 dark:border-emerald-500/30 dark:text-emerald-200">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 text-sm">
                        {{ session('status') }}
                    </div>
                </div>
            @endif
            @if ($errors->any())
                <div class="bg-rose-100 border border-rose-200 text-rose-700 dark:bg-rose-500/20 dark:border-rose-500/30 dark:text-rose-200">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif
            @yield('content')
        </main>

        <footer class="mt-16 border-t border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-sm text-gray-500 dark:text-gray-400">
                &copy; {{ now()->year }} {{ config('app.name', 'Coffee Shop') }}. {{ __('Semua hak dilindungi.') }}
            </div>
        </footer>

        @stack('scripts')
    </body>
</html>
