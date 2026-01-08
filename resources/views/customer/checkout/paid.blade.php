@extends('layouts.public')

@section('content')
    <section class="py-12">
        <div class="max-w-3xl mx-auto px-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-8 space-y-6 text-center">
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ __('Pembayaran Sudah Lunas') }}</h1>
                <p class="text-sm text-gray-600 dark:text-gray-300">{{ __('Terima kasih! Pesanan kamu sedang diproses.') }}</p>
                <div class="inline-flex items-center gap-2 rounded-full bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-200 px-4 py-2 text-sm font-semibold">
                    {{ __('Nomor Pesanan:') }} {{ $order->order_number }}
                </div>
                <div class="space-y-2 text-sm text-gray-600 dark:text-gray-300">
                    <p>{{ __('Mohon tunggu. Barista akan memproses pesananmu.') }}</p>
                    @if ($order->table_number)
                        <p>{{ __('Nomor Meja:') }} <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $order->table_number }}</span></p>
                    @endif
                </div>

                <div class="text-left space-y-3">
                    <div class="flex items-center justify-between text-sm text-gray-700 dark:text-gray-200">
                        <span>{{ __('Total Pembayaran') }}</span>
                        <span class="font-semibold text-gray-900 dark:text-gray-100">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 p-4">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3">{{ __('Rincian Menu') }}</h3>
                        <div class="space-y-2 text-sm text-gray-700 dark:text-gray-200">
                            @foreach ($order->items as $item)
                                <div class="flex items-center justify-between">
                                    <span>{{ $item->menu_name ?? $item->menuItem?->name ?? '-' }} ({{ $item->quantity }}x)</span>
                                    <span class="font-semibold">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="flex justify-center pt-4">
                    <a href="{{ route('home') }}"
                       class="inline-flex items-center justify-center rounded-full bg-indigo-600 px-5 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-500">
                        {{ __('Kembali ke Beranda') }}
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
