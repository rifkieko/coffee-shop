@extends('layouts.public')

@section('content')
    <section class="bg-[#f5f6fb] py-12 min-h-screen">
        <div class="mx-auto max-w-4xl px-4 pb-24 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-6">
                <div class="rounded-[24px] border border-white/70 bg-white/90 shadow-[0_25px_60px_rgba(15,15,30,0.12)]">
                    <div class="flex items-center gap-2 border-b border-white/70 px-4 py-3 text-sm font-semibold text-[#2A1A13] tracking-[0.3em] uppercase">
                        <a href="{{ route('home') }}"
                           class="inline-flex h-8 w-8 items-center justify-center rounded-[12px] border border-[#d4d4d4] bg-white text-[#2A1A13] transition hover:bg-[#f5f5f5]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M15 6l-6 6 6 6" />
                            </svg>
                        </a>
                        <span class="text-xs tracking-[0.4em]">KERANJANG</span>
                    </div>
                    <div class="px-4 py-3 text-center">
                        <p class="text-sm font-semibold uppercase tracking-[0.6em] text-[#b89a7a]">{{ __('Pala\'s Kopi') }}</p>
                    </div>
                </div>
                <div class="mt-6">
                    <div class="flex items-center justify-between gap-3 pb-4 font-medium text-gray-800">
                        <div>
                            <p class="text-sm uppercase tracking-[0.4em] text-[#8c6a4f]">{{ __('Item yang dipesan') }}</p>
                            <p class="text-lg font-semibold text-gray-900">({{ $cart->items->count() }} {{ __('menu') }})</p>
                        </div>
                        <a href="{{ route('catalog.index') }}" class="cart-add-btn whitespace-nowrap">
                            + {{ __('Tambah Item') }}
                        </a>
                    </div>
                    <div id="cart-content" class="space-y-5">
                        @include('customer.cart.partials.items', ['cart' => $cart])
                    </div>
                </div>
                <div class="rounded-[24px] border border-transparent bg-gradient-to-r from-[#fefefe] via-[#fef6ec] to-[#f1f3ff] p-5 shadow-[0_25px_60px_rgba(15,15,30,0.12)]">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-[0.4em] text-gray-500">{{ __('Total Pembayaran') }}</p>
                            <p class="text-3xl font-semibold text-gray-900"><span data-cart-total>Rp{{ number_format($cart->subtotal, 0, ',', '.') }}</span>
                            </p>
                        </div>
                        <a href="{{ route('checkout.show') }}" class="ml-4 inline-flex items-center justify-center rounded-full bg-[#1ec16b] px-8 py-3 text-sm font-semibold text-white shadow-lg transition hover:bg-[#14a75c] whitespace-nowrap">
                            {{ __('Lanjut Pembayaran') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
