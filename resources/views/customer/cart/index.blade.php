@extends('layouts.public')

@section('content')
    <section class="bg-[#f5f6fb] py-12">
        <div class="mx-auto max-w-4xl px-4 pb-24 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-6">
                <div class="rounded-[16px] border border-white/70 bg-white/90 px-5 py-4 shadow-[0_20px_35px_rgba(15,15,30,0.12)]">
                    <div class="text-center">
                        <p class="text-xs font-semibold uppercase tracking-[0.6em] text-[#b89a7a]">{{ __('Pala\'s Kopi') }}</p>
                        <h1 class="mt-3 text-3xl font-bold text-gray-900">{{ __('Keranjang Pesanan') }}</h1>
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
                            <p class="text-3xl font-semibold text-gray-900">Rp{{ number_format($cart->subtotal, 0, ',', '.') }}
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
