@extends('layouts.public')

@section('content')
<section class="bg-white">
    <div class="mx-auto max-w-6xl px-4 pt-4 pb-12 sm:px-6 lg:px-8 sm:pt-8 sm:pb-16">
        <div class="space-y-6 sm:space-y-10">
            <div class="ui-hero">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="space-y-1.5 sm:space-y-2">
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

            <div class="overflow-x-auto -mx-2 px-2 pb-1">
                <div class="inline-flex min-w-max gap-3 whitespace-nowrap text-sm font-medium text-[#6b5445]">
                    @foreach ($menuGroups as $group)
                        @php
                            $groupCategory = $group['category'];
                            $categoryName = $groupCategory?->name ?? 'Tanpa Kategori';
                            $anchor = \Illuminate\Support\Str::slug($categoryName) ?: 'uncategorized';
                        @endphp
                        <a href="#category-{{ $anchor }}" class="ui-chip shrink-0">
                            {{ $categoryName }}
                        </a>
                    @endforeach
                </div>
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
                            </div>
                            <p class="text-sm text-[#6b5445]">{{ $categoryDescription }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            @forelse ($items as $item)
                                @php
                                    $isLowStock = $item->stock < 10;
                                    $category = strtolower($item->category?->name ?? '');
                                    $isDrink = in_array($category, ['coffee','non coffee','non-coffee','noncoffee','kopi','minuman']);
                        @endphp
                                <article class="ui-card">
                                    <a href="{{ route('catalog.show', $item) }}" class="relative block focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#c58a53]">
                                        <img src="{{ $item->image_url ?? 'https://via.placeholder.com/400x400?text=Menu' }}" alt="{{ $item->name }}">
                                        @if ($isLowStock)
                                            <span class="absolute top-4 left-4 rounded-full border border-red-500 bg-red-100 px-3 py-0.5 text-xs font-semibold text-red-800 shadow-sm">Terbatas</span>
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







