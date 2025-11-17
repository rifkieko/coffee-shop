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
    <body class="antialiased font-sans bg-white dark:bg-gray-900 text-[#2A1A13] dark:text-gray-100 min-h-screen flex flex-col">
                <header x-data="{ mobileOpen: false, q: @js(request('q')) }" class="sticky top-0 z-40 border-b border-black/20 bg-white/90 backdrop-blur">
            @php
                // Navigation links intentionally disabled per request.
                $primaryLinks = [];
            @endphp

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 sm:h-20 items-center justify-between gap-4 sm:gap-6">
                    <a href="{{ route('home') }}" class="flex items-center gap-3">
                        <img src="{{ asset('images/palas-logo.png') }}" alt="Palas Kopi" class="h-10 w-10 rounded-full bg-white object-contain ring-1 ring-black/20">
                        <span class="text-lg sm:text-xl font-semibold tracking-tight text-gray-900 dark:text-gray-100">
                            {{ config('app.name', "Pala's Kopi") }}
                        </span>
                    </a>

                    <nav class="hidden lg:flex items-center gap-6 text-sm font-medium text-[#8C5A3A]">
                        @foreach ($primaryLinks as $link)
                            <a href="{{ $link['route'] }}"
                               class="group relative inline-flex flex-col items-center gap-1 transition hover:text-[#4C2B1C] {{ $link['active'] ? 'text-[#4C2B1C]' : '' }}">
                                <span>{{ $link['label'] }}</span>
                                <span class="h-[2px] rounded-full transition-all duration-200 {{ $link['active'] ? 'w-8 bg-[#C58A53]' : 'w-0 bg-transparent group-hover:w-6 group-hover:bg-[#C58A53]' }}"></span>
                            </a>
                        @endforeach
                    </nav>

                    <div class="hidden lg:flex items-center gap-4 text-[#8C5A3A]">
                        <div class="relative" x-data="liveSearch('{{ route('catalog.search') }}')" @click.outside="open=false">
                            <input x-model="q" @input="type()" @focus="open = (results.length>0)" @keydown.down.prevent="move(1)" @keydown.up.prevent="move(-1)" @keydown.enter.prevent="enter()"
                                   type="text" placeholder="{{ __('Cari menu, kategori, atau deskripsi...') }}"
                                   class="w-72 rounded-full border border-black/20 bg-white/95 pl-9 pr-4 py-2 text-sm text-black placeholder-black/60 focus:border-black/60 focus:ring-2 focus:ring-black/30" />
                            <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-[#8C5A3A]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" />
                            </svg>
                            <div x-cloak x-show="open" class="absolute z-50 mt-2 w-[28rem] max-h-80 overflow-auto rounded-2xl border border-black/20 bg-white shadow-lg">
                                <template x-if="loading">
                                    <div class="p-3 text-sm text-[#8C5A3A]">{{ __('Mencari...') }}</div>
                                </template>
                                <template x-for="(item, idx) in results" :key="item.id">
                                    <a :href="item.url" class="flex items-center gap-3 p-2 hover:bg-[#F5E6D3]/40" :class="{ 'bg-[#F5E6D3]/30' : idx === index }" @mouseenter="index = idx">
                                        <div class="relative w-12 aspect-[4/3] overflow-hidden rounded-md bg-gray-100">
                                            <img :src="item.image_url || '{{ asset('images/palas-logo.png') }}'" alt="" class="absolute inset-0 h-full w-full object-cover">
                                        </div>
                                        <div class="flex-1">
                                            <div class="text-sm font-semibold text-[#2A1A13]" x-text="item.name"></div>
                                            <div class="text-xs text-gray-500"><span x-text="item.category || '-' "></span> • <span x-text="format(item.price)"></span></div>
                                        </div>
                                    </a>
                                </template>
                                <div x-show="!loading && results.length === 0 && q.length >= 2" class="p-3 text-sm text-gray-500">{{ __('Tidak ada hasil') }}</div>
                            </div>
                        </div>

                        <a href="{{ route('cart.index') }}" data-cart-indicator class="rounded-full p-2 transition hover:bg-[#F5E6D3] hover:text-[#4C2B1C]" title="{{ __('Cart') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2m0 0h13.2l1.2 6.5a1 1 0 01-1 1.17H7.53m-2.13-7.67L7.53 17h9.94m0 0a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 103 0m-9.94 0a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0" />
                            </svg>
                        </a>
                        @auth
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button type="button" class="inline-flex items-center gap-2 rounded-full border border-[#C58A53]/40 bg-white px-4 py-2 text-sm font-semibold text-[#4C2B1C] shadow-sm transition hover:border-[#4C2B1C]/40 hover:bg-[#F5E6D3]">
                                        <span class="flex h-7 w-7 items-center justify-center rounded-full bg-[#4C2B1C]/10 text-[#4C2B1C]">
                                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                        </span>
                                        <span class="max-w-[10rem] truncate">
                                            {{ auth()->user()->name }}
                                        </span>
                                        <svg class="h-4 w-4 text-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
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
                                        <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                            {{ __('Keluar') }}
                                        </x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        @else
                            <div class="flex items-center gap-2">
                                <a href="{{ route('register') }}" class="inline-flex items-center gap-2 rounded-full border border-[#8C5A3A] px-5 py-2 text-sm font-semibold text-[#8C5A3A] hover:bg-[#8C5A3A] hover:text-white transition">
                                    {{ __('Register') }}
                                </a>
                                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-full bg-[#8C5A3A] px-5 py-2 text-sm font-semibold text-white shadow-sm shadow-[#8C5A3A]/20 transition hover:bg-[#4C2B1C]">
                                    {{ __('Sign in') }}
                                </a>
                            </div>
                        @endauth
                    </div>

                    <!-- Mobile quick actions: Search + Cart + Hamburger (auth in mobile menu) -->
                    <div class="flex items-center gap-2 lg:hidden">
                        <a href="{{ route('cart.index') }}" data-cart-indicator class="rounded-full p-2 text-[#4C2B1C] hover:bg-[#F5E6D3]" title="{{ __('Cart') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2m0 0h13.2l1.2 6.5a1 1 0 01-1 1.17H7.53m-2.13-7.67L7.53 17h9.94m0 0a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 103 0m-9.94 0a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0" />
                            </svg>
                        </a>
                        <div class="relative" x-data @click.outside="$root.mobileOpen = false" @keydown.escape.window="$root.mobileOpen = false">
                            <button type="button"
                                class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-transparent p-2 text-[#4C2B1C] transition hover:bg-[#F5E6D3]"
                                @click="mobileOpen = !mobileOpen"
                                aria-controls="mobile-auth-overlay"
                                :aria-expanded="mobileOpen.toString()"
                                aria-label="{{ __('Tampilkan menu akun') }}">
                                <svg x-show="!mobileOpen" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                                <svg x-show="mobileOpen" x-cloak class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                            <div x-cloak x-show="mobileOpen" x-transition.origin.top.right
                                 id="mobile-auth-overlay"
                                 class="absolute right-0 top-full z-40 mt-2 w-48 min-w-[12rem] rounded-[18px] border border-black/20 bg-white/95 px-4 py-3 shadow-[0_16px_30px_rgba(15,15,30,0.1)] text-sm text-[#4C2B1C]">
                                @auth
                                    <div class="space-y-3">
                                        <div class="text-sm font-semibold">{{ auth()->user()->name }}</div>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit"
                                                class="w-full rounded-full border border-[#8C5A3A] px-4 py-2 text-xs font-semibold uppercase tracking-[0.3em] text-[#8C5A3A] transition hover:bg-[#8C5A3A] hover:text-white">
                                                {{ __('Keluar') }}
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <div class="space-y-2">
                                        <a href="{{ route('register') }}"
                                           class="inline-flex items-center justify-center rounded-full border border-[#8C5A3A] px-4 py-2 text-xs font-semibold uppercase tracking-[0.3em] text-[#8C5A3A] transition hover:bg-[#8C5A3A] hover:text-white"
                                           @click="mobileOpen = false">
                                            {{ __('Register') }}
                                        </a>
                                        <a href="{{ route('login') }}"
                                           class="inline-flex items-center justify-center rounded-full bg-[#8C5A3A] px-4 py-2 text-xs font-semibold uppercase tracking-[0.3em] text-white transition hover:bg-[#4C2B1C]"
                                           @click="mobileOpen = false">
                                            {{ __('Sign in') }}
                                        </a>
                                    </div>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile search bar under header (always visible on mobile) -->
            <div class="lg:hidden border-t border-black/20 bg-white/98">
                <div class="max-w-7xl mx-auto px-4 py-3">
                    <div class="relative" x-data="liveSearch('/menu/search')" @click.outside="open=false">
                        <input x-model="q" @input="type()" @focus="open = (results.length>0)" @keydown.down.prevent="move(1)" @keydown.up.prevent="move(-1)" @keydown.enter.prevent="enter()"
                               type="text" placeholder="Cari menu..."
                               class="flex-1 w-full rounded-full border border-black/20 bg-white/95 pl-9 pr-4 py-2 text-sm text-black placeholder-black/60 focus:border-black/60 focus:ring-2 focus:ring-black/30" />
                        <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-[#8C5A3A]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" />
                        </svg>
                        <div x-cloak x-show="open" class="absolute z-50 mt-2 w-full max-h-80 overflow-auto rounded-2xl border border-black/20 bg-white shadow-lg">
                            <template x-if="loading">
                                <div class="p-3 text-sm text-[#8C5A3A]">Mencari...</div>
                            </template>
                                <template x-for="(item, idx) in results" :key="item.id">
                                <a :href="item.url" class="flex items-center gap-3 p-2 hover:bg-[#F5E6D3]/40" :class="{ 'bg-[#F5E6D3]/30' : idx === index }" @mouseenter="index = idx">
                                    <div class="relative w-12 aspect-[4/3] overflow-hidden rounded-md bg-gray-100">
                                        <img :src="item.image_url || '/images/palas-logo.png'" alt="" class="absolute inset-0 h-full w-full object-cover">
                                    </div>
                                    <div class="flex-1">
                                        <div class="text-sm font-semibold text-[#2A1A13]" x-text="item.name"></div>
                                        <div class="text-xs text-gray-500"><span x-text="item.category || '-' "></span> • <span x-text="format(item.price)"></span></div>
                                    </div>
                                </a>
                                </template>
                            <div x-show="!loading && results.length === 0 && q.length >= 2" class="p-3 text-sm text-gray-500">Tidak ada hasil</div>
                        </div>
                    </div>
                </div>
            </div>

        </header>

        <main class="flex-1">
            @if (session('status'))
                <div id="global-status-alert" class="bg-emerald-500/10 border border-emerald-200 text-emerald-700 dark:bg-emerald-500/20 dark:border-emerald-500/30 dark:text-emerald-200">
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

        <script>
            window.liveSearch = function(endpoint){
                return {
                    q: '',
                    open: false,
                    loading: false,
                    results: [],
                    index: 0,
                    seq: 0,
                    timer: null,
                    type(){
                        clearTimeout(this.timer);
                        const term = (this.q || '').trim();
                        if (term.length < 2){ this.results = []; this.open = false; return; }
                        this.open = true;
                        this.timer = setTimeout(() => this.search(), 250);
                    },
                    async search(){
                        const term = (this.q || '').trim();
                        this.index = 0;
                        if (term.length < 2){ this.results = []; this.open = false; return; }
                        this.loading = true; this.open = true;
                        const mySeq = ++this.seq;
                        try{
                            const res = await fetch(endpoint + '?q=' + encodeURIComponent(term), { headers: { 'Accept': 'application/json' }});
                            const data = await res.json().catch(() => ({ items: [] }));
                            if (mySeq !== this.seq) return;
                            this.results = data.items || [];
                            this.open = this.results.length > 0;
                        } catch(e){
                            if (mySeq !== this.seq) return;
                            this.results = []; this.open = false;
                        } finally { this.loading = false; }
                    },
                    move(dir){ const len = this.results.length; if (!len) return; this.index = Math.max(0, Math.min(this.index + dir, len - 1)); },
                    enter(){ if (this.results[this.index]) window.location.href = this.results[this.index].url; },
                    format(n){ try { return 'Rp' + new Intl.NumberFormat('id-ID').format(n || 0); } catch(_) { return 'Rp' + (n || 0); } }
                };
            }
        </script>
        @include('customer.cart.partials.scripts')
        @if (! request()->routeIs(['cart.*', 'checkout.*']))
            <!-- Global mini cart bar -->
            <div id="mini-cart-bar" class="fixed inset-x-0 bottom-4 z-40 hidden px-4">
                <div class="mx-auto w-full max-w-md sm:max-w-xl">
                    <div class="flex items-center justify-between gap-4 rounded-[28px] border border-[#e5e1dc] bg-white px-4 py-3 shadow-[0_20px_45px_rgba(34,36,38,0.12)]">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600 ring-1 ring-emerald-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M3 3h2l.4 2h13.2l1.2 6.5a1 1 0 01-1 1.17H7.53m0 0L6 19h11.47m-9.94 0a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0m9.94 0a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0" />
                                </svg>
                            </span>
                            <div class="text-sm">
                                <p class="text-[#8C5A3A]">{{ __('Total') }}</p>
                                <p id="mini-cart-total" class="text-base font-semibold text-[#2A1A13]">Rp0</p>
                            </div>
                        </div>
                        <a href="{{ route('cart.index') }}" class="inline-flex items-center gap-2 rounded-full bg-[#1ec16b] px-5 py-2 text-sm font-semibold text-white shadow hover:bg-[#14a75c] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#1ec16b]">
                            {{ __('Lihat Keranjang') }}
                        </a>
                    </div>
                </div>
            </div>
        @endif
        @stack('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const alert = document.getElementById('global-status-alert');
                if (!alert) {
                    return;
                }
                setTimeout(() => {
                    alert.remove();
                }, 2000);
            });
        </script>
    </body>
</html>


