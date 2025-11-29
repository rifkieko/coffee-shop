<nav x-data="{ open: false }" class="bg-white/90 dark:bg-slate-900/90 backdrop-blur border-b border-slate-200 dark:border-slate-800">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-indigo-600 dark:text-indigo-400" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-6 sm:-my-px sm:ms-10 sm:flex">
                    @auth
                        <x-nav-link :href="auth()->user()->isAdmin() ? route('dashboard') : route('home')"
                                    :active="auth()->user()->isAdmin() ? request()->routeIs('dashboard') : request()->routeIs('home')">
                            {{ auth()->user()->isAdmin() ? __('Admin Dashboard') : __('Beranda') }}
                        </x-nav-link>
                    @else
                        <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                            {{ __('Beranda') }}
                        </x-nav-link>
                    @endauth
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">
                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center gap-2 rounded-full border border-indigo-100 px-3 py-1.5 text-sm font-medium text-gray-600 transition hover:border-indigo-300 hover:text-indigo-600 dark:border-indigo-500/50 dark:text-gray-200 dark:hover:border-indigo-300 dark:hover:text-indigo-200">
                                <div class="max-w-[10rem] truncate">{{ auth()->user()->name }}</div>

                                <div>
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            @php
                                $initial = strtoupper(mb_substr(auth()->user()->name, 0, 1));
                                $roleLabel = auth()->user()->isAdmin() ? 'ADMIN' : 'CUSTOMER';
                            @endphp

                            <div class="rounded-[8px] border border-[#ECC9A8]/60 bg-white p-5 text-[#2A1A13] shadow-[0_30px_60px_rgba(47,33,31,0.15)] dark:bg-slate-900">
                                <div class="flex items-center gap-3">
                                    <div class="h-12 w-12 flex items-center justify-center rounded-full bg-[#ECC9A8] text-[#4C2B1C] font-semibold">
                                        {{ $initial }}
                                    </div>
                                    <div>
                                        <div class="text-lg font-semibold">{{ auth()->user()->name }}</div>
                                        <div class="text-[11px] uppercase tracking-[0.4em] text-[#C6956D]">{{ $roleLabel }}</div>
                                    </div>
                                </div>
                                <div class="mt-2 text-sm text-[#4C2B1C]/70">{{ auth()->user()->email }}</div>

                                <div class="mt-4 h-px bg-slate-100"></div>

                                <div class="mt-3 space-y-2">
                                    <x-dropdown-link :href="route('dashboard')"
                                        class="rounded-[12px] border border-[#1ec16b]/40 bg-[#ebfff6] px-4 py-2 text-sm font-semibold text-[#1ec16b] transition hover:bg-[#d9f9e9]">
                                        {{ __('Dashboard') }}
                                    </x-dropdown-link>

                                    <x-dropdown-link :href="route('home')"
                                        class="rounded-[12px] border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-100">
                                        {{ __('Kembali ke Toko') }}
                                    </x-dropdown-link>

                                    <a href="{{ route('profile.edit') }}"
                                       class="block w-full rounded-[12px] border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-800 transition hover:bg-gray-100 text-center">
                                        {{ __('Profil') }}
                                    </a>

                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="w-full rounded-[12px] border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-800 transition hover:bg-gray-100 text-center">
                                            {{ __('Keluar') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </x-slot>
                    </x-dropdown>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-full bg-[#ececec] px-5 py-2 text-sm font-semibold text-[#4C2B1C] transition hover:bg-[#dbdbdb]">
                        {{ __('Masuk') }}
                    </a>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-300 hover:bg-gray-100 dark:hover:bg-slate-800 focus:outline-none focus:bg-gray-100 dark:focus:bg-slate-800 focus:text-indigo-600 dark:focus:text-indigo-300 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white/95 dark:bg-slate-900/95 border-t border-slate-200 dark:border-slate-800" x-cloak>
        <div class="pt-2 pb-3 space-y-1 px-4">
            @auth
                <x-responsive-nav-link :href="auth()->user()->isAdmin() ? route('dashboard') : route('home')"
                                       :active="auth()->user()->isAdmin() ? request()->routeIs('dashboard') : request()->routeIs('home')"
                                       class="rounded-lg">
                    {{ auth()->user()->isAdmin() ? __('Admin Dashboard') : __('Beranda') }}
                </x-responsive-nav-link>
            @else
                <x-responsive-nav-link :href="route('home')" class="rounded-lg">
                    {{ __('Beranda') }}
                </x-responsive-nav-link>
            @endauth
            <x-responsive-nav-link :href="route('home')" class="rounded-lg">
                {{ __('Kembali ke Toko') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-4 border-t border-slate-200 dark:border-slate-800">
            @auth
                @php
                    $initial = strtoupper(mb_substr(auth()->user()->name, 0, 1));
                    $roleLabel = auth()->user()->isAdmin() ? 'ADMIN' : 'CUSTOMER';
                @endphp

                <div class="px-4">
                    <div class="rounded-3xl border border-[#ECC9A8]/70 bg-white dark:bg-slate-900 shadow-[0_25px_60px_rgba(47,33,31,0.15)] p-4">
                        <div class="flex items-center gap-3">
                            <div class="h-11 w-11 rounded-full bg-[#ECC9A8] dark:bg-[#906144]/80 text-[#4C2B1C] dark:text-[#2A1A13] font-semibold flex items-center justify-center text-lg">
                                {{ $initial }}
                            </div>
                            <div>
                                <div class="text-lg font-semibold text-[#2A1A13] dark:text-slate-50 leading-tight">{{ auth()->user()->name }}</div>
                                <div class="text-[11px] font-medium tracking-[0.4em] uppercase text-[#C6956D] dark:text-[#F5D6B9] mt-1">
                                    {{ $roleLabel }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 text-sm text-[#4C2B1C]/80 dark:text-slate-300">{{ auth()->user()->email }}</div>

                        <div class="mt-3 h-px bg-slate-200 dark:bg-slate-700"></div>

                        <div class="mt-3 space-y-1">
                            <x-responsive-nav-link :href="route('dashboard')" class="rounded-[12px] px-3 py-2 text-sm font-semibold text-[#2A1A13] dark:text-slate-100 hover:bg-[#F5E6D3] dark:hover:bg-slate-800">
                                {{ __('Dashboard') }}
                            </x-responsive-nav-link>

                            <x-responsive-nav-link :href="route('home')" class="rounded-[12px] px-3 py-2 text-sm font-semibold text-[#2A1A13] dark:text-slate-100 hover:bg-[#F5E6D3] dark:hover:bg-slate-800">
                                {{ __('Kembali ke Toko') }}
                            </x-responsive-nav-link>

                            <a href="{{ route('profile.edit') }}" class="block rounded-[12px] border border-gray-200 px-3 py-2.5 text-sm font-semibold text-[#2A1A13] dark:text-slate-100 hover:bg-[#F5E6D3] dark:hover:bg-slate-800">
                                {{ __('Profil') }}
                            </a>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <button type="submit" class="w-full rounded-[12px] border border-gray-200 px-3 py-2.5 text-sm font-semibold text-[#2A1A13] dark:text-slate-100 hover:bg-[#F5E6D3] dark:hover:bg-slate-800">
                                    {{ __('Keluar') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <div class="px-4 flex flex-col gap-2">
                    <x-responsive-nav-link :href="route('login')" class="rounded-lg">
                        {{ __('Masuk') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('register')" class="rounded-lg">
                        {{ __('Daftar') }}
                    </x-responsive-nav-link>
                </div>
            @endauth
        </div>
    </div>
</nav>
