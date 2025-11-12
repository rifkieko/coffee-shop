@extends('layouts.public')

@section('content')
@php
    $category = strtolower($item->category?->name ?? '');
    $isDrink = in_array($category, ['coffee','non coffee','non-coffee','noncoffee','kopi','minuman']);
@endphp

<section class="bg-[#eef0f5] py-6">
    <div class="mx-auto max-w-md px-4" x-data="detailForm()" x-init="init({ price: {{ (int) $item->price }} })">
        <article class="overflow-hidden rounded-[32px] bg-white shadow-[0_25px_50px_rgba(42,26,19,0.08)]">
            <div class="relative aspect-[4/3] w-full bg-gray-100" style="aspect-ratio: 4 / 3">
                <img src="{{ $item->image_url ?? 'https://via.placeholder.com/600x800?text=Menu' }}" alt="{{ $item->name }}" class="h-full w-full object-cover">
            </div>
            <div class="space-y-6 px-6 py-6">
                <div class="space-y-1">
                    <a href="{{ url()->previous() }}" class="inline-flex items-center gap-1 text-xs font-semibold uppercase tracking-[0.35em] text-[#b7aca2]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 6l-6 6 6 6" />
                        </svg>
                        {{ __('Kembali') }}
                    </a>
                    <h1 class="text-2xl font-semibold text-[#1f2a21]">{{ $item->name }}</h1>
                    <p class="text-sm text-[#6b5445]">{{ $item->description ?: __('Belum ada deskripsi untuk menu ini.') }}</p>
                    <p class="text-lg font-semibold text-[#1f2a21]">Rp{{ number_format($item->price, 0, ',', '.') }}</p>
                </div>

                <form id="detail-form" method="POST" action="{{ route('cart.store') }}" data-cart-add class="space-y-5">
            @csrf
            <input type="hidden" name="menu_item_id" value="{{ $item->id }}">
            <input type="hidden" name="quantity" :value="qty">
            <input type="hidden" name="redirect_to" value="{{ route('home') }}">
            <input type="hidden" name="temperature" :value="temp">
            <input type="hidden" name="sugar_level" :value="sugar">
            <input type="hidden" name="ice_level" :value="ice">

            @if($isDrink)
                @foreach ([
                    ['key' => 'temp', 'label' => __('Suhu'), 'options' => [
                        ['value' => 'cold', 'label' => __('Ice')],
                        ['value' => 'hot', 'label' => __('Hot')],
                    ]],
                    ['key' => 'sugar', 'label' => __('Level Gula'), 'options' => [
                        ['value' => 100, 'label' => __('Normal Sugar')],
                        ['value' => 50, 'label' => __('Less Sugar')],
                    ]],
                    ['key' => 'ice', 'label' => __('Level Es'), 'options' => [
                        ['value' => 100, 'label' => __('Normal Ice')],
                        ['value' => 50, 'label' => __('Less Ice')],
                    ]],
                ] as $section)
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <h2 class="text-sm font-semibold text-[#2a1205]">{{ $section['label'] }}</h2>
                            <span class="text-xs uppercase tracking-[0.35em] text-[#b7aca2]">{{ __('pilih salah satu') }}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach ($section['options'] as $option)
                                @php $optionValue = (string) $option['value']; @endphp
                                <button type="button"
                                    @click="{{ $section['key'] }}='{{ $optionValue }}'"
                                    :class="optionClass('{{ $section['key'] }}', '{{ $optionValue }}')"
                                    class="rounded-[16px] border px-3 py-3 text-sm font-semibold transition">
                                    {{ $option['label'] }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @else
                <div class="rounded-[20px] border border-[#ede6d8] bg-white p-4 text-sm text-[#6b5445]">
                    {{ __('Tidak ada opsi kustomisasi untuk menu ini.') }}
                </div>
            @endif

                <div class="flex items-center gap-3">
                    <div class="inline-flex items-center gap-4 rounded-full border border-[#dcd7d1] px-4 py-2">
                        <button type="button" @click="dec()" class="text-xl font-semibold text-[#a1784c]">-</button>
                        <span class="text-base font-semibold text-[#1f2a21]" x-text="qty"></span>
                        <button type="button" @click="inc()" class="text-xl font-semibold text-[#a1784c]">+</button>
                    </div>
                    <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 rounded-full bg-emerald-600 px-4 py-3 text-sm font-semibold text-white shadow hover:bg-emerald-500">
                        <span>+ {{ __('Keranjang') }}</span>
                        <span x-text="formatRupiah(total())"></span>
                    </button>
                </div>
            </form>
            </div>
        </article>
    </div>
</section>

<script>
    function detailForm(){
        return {
            price: 0,
            qty: 1,
            temp: 'cold',
            sugar: '100',
            ice: '100',
            init({ price }){ this.price = price; },
            inc(){ this.qty++; },
            dec(){ if(this.qty > 1) this.qty--; },
            total(){ return this.qty * this.price; },
            formatRupiah(n){
                try{
                    return 'Rp' + new Intl.NumberFormat('id-ID').format(n);
                } catch(_){
                    return 'Rp' + n;
                }
            },
            optionClass(state, value){
                const active = this[state] === value;
                return active
                    ? 'border-[#c58a53] bg-white text-[#2a1205]'
                    : 'border-[#eadfd3] bg-[#f7f2ec] text-[#b7aca2]';
            },
        }
    }
</script>
@endsection



