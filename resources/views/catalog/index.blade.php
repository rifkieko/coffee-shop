@extends('layouts.public')

@section('content')
<section class="bg-white pb-16">
    <div class="mx-auto max-w-6xl px-4 pt-8 sm:px-6 lg:px-8">
        <div class="space-y-10">
            <div class="ui-hero">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="space-y-2">
                        <span class="ui-hero-badge">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-6-4.35-6-9a6 6 0 1112 0c0 4.65-6 9-6 9z" />
                                <circle cx="12" cy="12" r="2.5" />
                            </svg>
                            {{ __('Lokasi Kami') }}
                        </span>
                        <h1 class="text-2xl font-semibold text-[#2A1A13]">{{ config('app.name', "Pala's Kopi") }}</h1>
                        <p class="text-sm text-[#6b5445]">{{ __('Buka setiap hari, 08:00 - 22:00') }}</p>
                    </div>
                </div>
            </div>

            <form method="GET" action="{{ route('home') }}" class="ml-auto max-w-md">
                <label for="catalog-search" class="sr-only">Cari menu</label>
                <div class="flex items-center gap-2 rounded-full border border-[#f1d8c3] bg-white px-3 py-1 shadow-sm">
                    <input
                        id="catalog-search"
                        name="q"
                        type="search"
                        value="{{ $search }}"
                        placeholder="Cari menu favoritmu..."
                        class="flex-1 border-none bg-transparent px-2 py-2 text-sm text-[#4C2B1C] focus:ring-0"
                    >
                    <button type="submit" class="inline-flex items-center rounded-full bg-[#b07b57] px-4 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-[#8c5a3a]">
                        {{ __('Cari') }}
                    </button>
                </div>
            </form>

            <div class="flex flex-wrap gap-3 text-sm font-medium text-[#6b5445]">
                @foreach ($menuGroups as $group)
                    @php
                        $groupCategory = $group['category'];
                        $categoryName = $groupCategory?->name ?? 'Tanpa Kategori';
                        $anchor = \Illuminate\Support\Str::slug($categoryName) ?: 'uncategorized';
                    @endphp
                    <a href="#category-{{ $anchor }}" class="ui-chip">
                        {{ $categoryName }}
                    </a>
                @endforeach
            </div>

            <div class="space-y-12">
                @forelse ($menuGroups as $group)
                    @php
                        $groupCategory = $group['category'];
                        $items = $group['items'];
                        $categoryName = $groupCategory?->name ?? __('Tanpa Kategori');
                        $categoryDescription = $groupCategory?->description ?: __('Pilihan menu terbaik dari barista kami.');
                        $anchor = \Illuminate\Support\Str::slug($categoryName) ?: 'uncategorized';
                    @endphp
                    <div id="category-{{ $anchor }}" class="space-y-6">
                        <div class="space-y-1">
                            <p class="text-xs font-semibold uppercase tracking-[0.4em] text-[#b07b57]">Kategori</p>
                            <div class="flex items-center justify-between">
                                <h2 class="text-2xl font-semibold text-[#2A1A13]">{{ $categoryName }}</h2>
                                <span class="hidden text-xs text-[#8c5a3a] sm:block">{{ $items->count() }} pilihan</span>
                            </div>
                            <p class="text-sm text-[#6b5445]">{{ $categoryDescription }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            @forelse ($items as $item)
                                @php
                                    $isLowStock = $item->stock <= $item->low_stock_threshold;
                                    $category = strtolower($item->category?->name ?? '');
                                    $isDrink = in_array($category, ['coffee','non coffee','non-coffee','noncoffee','kopi','minuman']);
                                @endphp
                                <article class="ui-card">
                                    <a href="{{ route('catalog.show', $item) }}" class="relative block focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#c58a53]">
                                        <img src="{{ $item->image_url ?? 'https://via.placeholder.com/400x400?text=Menu' }}" alt="{{ $item->name }}">
                                        @if ($isLowStock)
                                            <span class="absolute top-4 left-4 rounded-full bg-[#f3b26d]/90 px-3 py-0.5 text-xs font-semibold text-white">Terbatas</span>
                                        @endif
                                    </a>
                                    <div class="ui-card-body">
                                        <h3 class="text-base font-semibold text-[#1e3d2f]">{{ $item->name }}</h3>
                                        <p class="text-sm text-[#6b5445] line-clamp-2">{{ $item->description ?: __('Belum ada deskripsi untuk menu ini.') }}</p>
                                        <p class="text-base font-semibold text-[#1e3d2f]">Rp{{ number_format($item->price, 0, ',', '.') }}</p>
                                    </div>
                                    <div class="px-4 pb-4">
                                        <form method="POST" action="{{ route('cart.store') }}" data-cart-add class="w-full">
                                            @csrf
                                            <input type="hidden" name="menu_item_id" value="{{ $item->id }}">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="ui-btn-ghost w-full text-sm">
                                                {{ __('Tambah') }}
                                            </button>
                                        </form>
                                    </div>
                                </article>
                            @empty
                                <p class="text-sm text-[#6b5445]">{{ __('Belum ada menu di kategori ini.') }}</p>
                            @endforelse
                        </div>
                    </div>
                @empty
                    <div class="rounded-3xl border border-dashed border-[#f1d8c3] bg-white px-6 py-12 text-center shadow-sm">
                        <h3 class="text-lg font-semibold text-[#2A1A13]">{{ __('Belum ada menu yang ditampilkan.') }}</h3>
                        <p class="mt-2 text-sm text-[#6b5445]">
                            {{ __('Silakan kembali lagi nanti atau hubungi barista kami untuk bantuan.') }}
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</section>
@endsection







