@extends('layouts.public')

@section('content')
    <section class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="flex flex-col items-start justify-between gap-3 text-center sm:text-left sm:flex-row sm:items-center">
                <div class="space-y-1">
                    <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ __('Keranjang Pesanan') }}</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('Silakan tinjau kembali pesanan Anda sebelum melanjutkan ke checkout.') }}
                    </p>
                </div>
                <a href="{{ route('home') }}"
                   class="inline-flex items-center gap-2 rounded-full border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-600 transition hover:bg-gray-100 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 19l-7-7 7-7m11 14V5" />
                    </svg>
                    {{ __('Kembali ke Menu') }}
                </a>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-2xl">
                <div id="cart-content" class="w-full">
                    @include('customer.cart.partials.items', ['cart' => $cart])
                </div>
            </div>

            <!-- Desktop/Tablet CTA -->
            <div class="hidden sm:flex justify-end">
                @if ($cart->items->isNotEmpty())
                    <a href="{{ route('checkout.show') }}"
                       class="inline-flex items-center gap-2 rounded-full bg-indigo-600 px-5 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                        {{ __('Lanjutkan ke Checkout') }}
                    </a>
                @else
                    <a href="{{ route('home') }}"
                       class="inline-flex items-center gap-2 rounded-full bg-indigo-100 px-5 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                        {{ __('Mulai Belanja') }}
                    </a>
                @endif
            </div>

            <!-- Mobile sticky CTA -->
            <div class="sm:hidden">
                @if ($cart->items->isNotEmpty())
                    <div class="fixed inset-x-0 bottom-0 z-40 border-t border-gray-200 bg-white/95 backdrop-blur dark:border-gray-700 dark:bg-gray-900/95">
                        <div class="mx-auto max-w-4xl px-4 py-3 flex items-center justify-between gap-3">
                            <div class="text-sm">
                                <div class="text-gray-500 dark:text-gray-400">{{ __('Total Keranjang') }}</div>
                                <div class="text-base font-semibold text-gray-900 dark:text-gray-100" id="cart-mobile-total">
                                    Rp{{ number_format($cart->subtotal, 0, ',', '.') }}
                                </div>
                            </div>
                            <a href="{{ route('checkout.show') }}" class="inline-flex items-center gap-2 rounded-full bg-emerald-600 px-5 py-2 text-sm font-semibold text-white shadow hover:bg-emerald-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">
                                {{ __('Checkout') }}
                            </a>
                        </div>
                    </div>
                    <div class="h-16"></div>
                @else
                    <div class="text-center">
                        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 rounded-full bg-indigo-600 px-5 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                            {{ __('Mulai Belanja') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
