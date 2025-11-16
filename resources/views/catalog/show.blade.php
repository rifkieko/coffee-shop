@extends('layouts.public')

@section('content')
<section class="bg-[#eef0f5] py-6">
    <div class="mx-auto max-w-md px-4" x-data="detailForm()" x-init="init({ price: {{ (int) $item->price }} })">
        <article class="overflow-hidden rounded-[12px] bg-white shadow-[0_25px_50px_rgba(42,26,19,0.08)]">
            <div class="relative aspect-[4/3] w-full overflow-hidden rounded-[12px] bg-gray-100" style="aspect-ratio: 4 / 3">
                <a href="{{ url()->previous() }}" class="absolute right-3 top-3 flex h-8 w-8 items-center justify-center rounded-full bg-white/90 text-[#2a1205] shadow transition hover:bg-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="6" y1="18" x2="18" y2="6" />
                        <line x1="6" y1="6" x2="18" y2="18" />
                    </svg>
                </a>
                <img src="{{ $item->image_url ?? 'https://via.placeholder.com/600x800?text=Menu' }}" alt="{{ $item->name }}" class="h-full w-full object-cover">
            </div>
            <div class="space-y-6 px-6 py-6">
                <div class="space-y-1">
                    <h1 class="text-2xl font-semibold text-[#1f2a21]">{{ $item->name }}</h1>
                    <p class="text-sm text-[#6b5445]">{{ $item->description ?: __('Belum ada deskripsi untuk menu ini.') }}</p>
                    <p class="text-lg font-semibold text-[#1f2a21]">Rp{{ number_format($item->price, 0, ',', '.') }}</p>
                </div>

                <form id="detail-form" method="POST" action="{{ route('cart.store') }}" data-cart-add class="space-y-5">
            @csrf
            <input type="hidden" name="menu_item_id" value="{{ $item->id }}">
            <input type="hidden" name="quantity" :value="qty">
            <input type="hidden" name="redirect_to" value="{{ route('home') }}">

                <div class="space-y-2">
                    <label for="notes" class="block text-sm font-semibold text-[#2a1205]">{{ __('Catatan untuk Barista') }}</label>
                    <textarea id="notes" name="notes" rows="3" class="w-full rounded-[16px] border border-[#dcd7d1] bg-[#fdfdfd] px-3 py-2 text-sm text-[#4c2b1c] focus:border-[#c58a53] focus:outline-none focus:ring-2 focus:ring-[#fdecd9]">{{ old('notes') }}</textarea>
                </div>

                <div class="flex items-center gap-3">
                    <div class="inline-flex items-center gap-4 rounded-full border border-[#dcd7d1] px-4 py-2">
                        <button type="button" @click="dec()" class="text-xl font-semibold text-[#a1784c]">-</button>
                        <span class="text-base font-semibold text-[#1f2a21]" x-text="qty"></span>
                        <button type="button" @click="inc()" class="text-xl font-semibold text-[#a1784c]">+</button>
                    </div>
                    <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 rounded-full bg-[#1ec16b] px-4 py-3 text-sm font-semibold text-white shadow hover:bg-[#14a75c]">
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
        }
    }
</script>
@endsection
