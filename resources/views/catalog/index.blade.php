@extends('layouts.public')

@section('content')
<section class="bg-gradient-to-b from-slate-100 via-white to-slate-100 dark:from-slate-900 dark:via-slate-950 dark:to-slate-900">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 py-10 sm:py-14 space-y-10" x-data="catalogScroll">
            <div class="flex items-center justify-between text-slate-500 dark:text-slate-300">
                <a href="{{ url()->previous() }}" class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-white shadow-sm transition hover:bg-slate-50 dark:bg-slate-800 dark:hover:bg-slate-700">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 19l-7-7m0 0 7-7m-7 7h18" />
                    </svg>
                </a>
                <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-white shadow-sm dark:bg-slate-800">
                    <x-application-logo class="h-8 w-8 text-indigo-600 dark:text-indigo-400" />
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('cart.index') }}" class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-white shadow-sm transition hover:bg-slate-50 dark:bg-slate-800 dark:hover:bg-slate-700">
                        <svg class="h-5 w-5 text-slate-500 dark:text-slate-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l3-7H6.4M7 13l-1.35 2.7A1 1 0 006.57 17h12.86M9 21a1 1 0 11-2 0 1 1 0 012 0zm10 0a1 1 0 11-2 0 1 1 0 012 0z" />
                        </svg>
                    </a>
                    <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-white shadow-sm transition hover:bg-slate-50 dark:bg-slate-800 dark:hover:bg-slate-700" onclick="document.getElementById('catalog-search').focus()">
                        <svg class="h-5 w-5 text-slate-500 dark:text-slate-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="space-y-4">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <p class="text-xs uppercase tracking-widest text-indigo-500 dark:text-indigo-300">
                        {{ __('Lokasi Kami') }}
                    </p>
                    <div class="mt-2 flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h1 class="text-xl font-semibold text-slate-900 dark:text-slate-100">
                                {{ config('app.name', 'Coffee Shop') }}
                            </h1>
                            <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('Buka setiap hari, 08:00 - 22:00') }}</p>
                        </div>
                        <a href="https://maps.google.com" target="_blank" rel="noopener" class="mt-3 inline-flex items-center gap-2 rounded-full border border-indigo-100 px-4 py-2 text-xs font-semibold text-indigo-600 transition hover:border-indigo-300 hover:text-indigo-700 dark:border-indigo-500/40 dark:text-indigo-200 dark:hover:border-indigo-300 dark:hover:text-indigo-100">
                            {{ __('Lihat Lokasi') }}
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </a>
                    </div>
                </div>

                <div class="rounded-3xl bg-amber-50 px-6 py-3 text-sm font-medium text-amber-700 shadow-sm dark:bg-amber-500/10 dark:text-amber-200">
                    {{ __('Scan QR di meja Anda untuk memasukkan nomor meja secara otomatis.') }}
                </div>
            </div>

            <form method="GET" action="{{ route('home') }}" class="relative">
                <label for="catalog-search" class="sr-only">{{ __('Cari menu') }}</label>
                <input
                    id="catalog-search"
                    name="q"
                    type="search"
                    value="{{ $search }}"
                    placeholder="{{ __('Cari menu favoritmu...') }}"
                    class="w-full rounded-full border-0 bg-white px-5 py-3 text-sm text-slate-700 shadow-sm ring-1 ring-slate-200 focus:ring-2 focus:ring-indigo-400 dark:bg-slate-900 dark:text-slate-100 dark:ring-1 dark:ring-slate-700 dark:focus:ring-indigo-500"
                >
                <button type="submit" class="absolute inset-y-0 right-2 my-1 inline-flex items-center justify-center rounded-full bg-indigo-600 px-4 py-2 text-xs font-semibold text-white shadow hover:bg-indigo-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                    {{ __('Cari') }}
                </button>
                @if ($search)
                    <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">
                        {{ __('Menampilkan hasil untuk ":term".', ['term' => $search]) }}
                    </p>
                @endif
            </form>

            <div class="flex items-center gap-3 overflow-x-auto pb-2 text-sm font-semibold text-slate-500 dark:text-slate-300">
                @foreach ($menuGroups as $group)
                    @php
                        $groupCategory = $group['category'];
                        $categoryName = $groupCategory?->name ?? __('Tanpa Kategori');
                        $anchor = \Illuminate\Support\Str::slug($categoryName) ?: 'uncategorized';
                    @endphp
                    <a href="#category-{{ $anchor }}" class="whitespace-nowrap rounded-full border border-transparent bg-white px-4 py-2 shadow-sm transition hover:border-indigo-300 hover:text-indigo-600 dark:bg-slate-900 dark:hover:border-indigo-500/40 dark:hover:text-indigo-200">
                        {{ $categoryName }}
                    </a>
                @endforeach
            </div>

            <div class="grid gap-12">
                @forelse ($menuGroups as $group)
                    @php
                        /** @var \App\Models\Category|null $groupCategory */
                        $groupCategory = $group['category'];
                        $items = $group['items'];
                        $categoryName = $groupCategory?->name ?? __('Tanpa Kategori');
                        $categoryDescription = $groupCategory?->description ?: __('Pilihan menu terbaik dari barista kami.');
                        $anchor = \Illuminate\Support\Str::slug($categoryName) ?: 'uncategorized';
                    @endphp
                    <div class="space-y-6 scroll-mt-24" id="category-{{ $anchor }}">
                        <div class="space-y-2">
                            <h2 class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-gray-100">
                                {{ $categoryName }}
                            </h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">
                                {{ $categoryDescription }}
                            </p>
                        </div>

                        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 xl:grid-cols-4">
                            @forelse ($items as $item)
                                @php
                                    $isLowStock = $item->stock <= $item->low_stock_threshold;
                                @endphp
                                <article class="group flex h-full flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg dark:border-gray-700 dark:bg-gray-800">
                                    <div class="relative aspect-[4/3] md:aspect-[3/4] w-full overflow-hidden bg-gray-100 dark:bg-gray-700">
                                        <img src="{{ $item->image_url ?? 'https://via.placeholder.com/400x520?text=Menu' }}" alt="{{ $item->name }}" class="h-full w-full object-cover object-center transition duration-700 group-hover:scale-105" loading="lazy">
                                        @if ($isLowStock)
                                            <span class="absolute top-3 left-3 rounded-full bg-amber-500/90 px-2 py-0.5 text-[11px] font-semibold text-white shadow-sm">
                                                {{ __('Terbatas') }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex flex-1 flex-col gap-4 p-4 sm:p-5">
                                        <div class="space-y-2">
                                            <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $item->name }}
                                            </h3>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $item->description ?: __('Belum ada deskripsi untuk menu ini.') }}
                                            </p>
                                        </div>
                                        <div class="mt-auto flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-sm">
                                            <div class="space-y-1">
                                                <p class="font-semibold text-gray-900 dark:text-gray-100 text-base">
                                                    Rp{{ number_format($item->price, 0, ',', '.') }}
                                                </p>
                                                <p class="text-xs {{ $isLowStock ? 'text-amber-600' : 'text-emerald-600' }}">
                                                    {{ $isLowStock ? __('Sisa stok: :qty', ['qty' => $item->stock]) : __('Stok tersedia: :qty', ['qty' => $item->stock]) }}
                                                </p>
                                            </div>
                                            @if (auth()->check() && ! auth()->user()->isCustomer())
                                                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 rounded-full bg-indigo-100 px-4 py-2 text-xs font-semibold text-indigo-600 shadow-sm hover:bg-indigo-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                                                    {{ __('Kelola di Dashboard') }}
                                                </a>
                                            @else
                                                <form method="POST" action="{{ route('cart.store') }}" data-cart-add>
                                                    @csrf
                                                    <input type="hidden" name="menu_item_id" value="{{ $item->id }}">
                                                    <input type="hidden" name="quantity" value="1">
                                                    <button type="submit" class="inline-flex items-center gap-2 rounded-full bg-indigo-500 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-indigo-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                                                        {{ __('Tambah') }}
                                                    </button>
                                                </form>
                                                @guest
                                                    <p class="mt-1 text-[11px] text-gray-400 dark:text-gray-500">
                                                        {{ __('Login disarankan agar pesanan mudah dilacak.') }}
                                                    </p>
                                                @endguest
                                            @endif
                                        </div>
                                    </div>
                                </article>
                            @empty
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Belum ada menu di kategori ini.') }}</p>
                            @endforelse
                        </div>
                    </div>
                @empty
                    <div class="rounded-3xl border border-dashed border-gray-300 bg-white px-6 py-12 text-center shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Belum ada menu yang ditampilkan.') }}</h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            {{ __('Silakan kembali lagi nanti atau hubungi barista kami untuk bantuan.') }}
                        </p>
                    </div>
                @endforelse
            </div>

            <div class="mt-6 sm:mt-12">
                {{ $menuItems->links() }}
            </div>
        </div>
    </section>
@endsection
