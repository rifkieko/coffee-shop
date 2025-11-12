@extends('layouts.public')

@section('content')
    <section class="py-10 sm:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-2xl p-6 sm:p-8 space-y-6">
                <div class="text-center space-y-3">
                    <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-gray-100">
                        {{ __('Status Pembayaran Pesanan') }}
                    </h1>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        {{ __('Nomor Pesanan:') }} <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $order->order_number }}</span>
                    </p>
                </div>

                @php
                    $alertClasses = [
                        'success' => 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-500/40 dark:bg-emerald-500/10 dark:text-emerald-200',
                        'warning' => 'border-amber-200 bg-amber-50 text-amber-700 dark:border-amber-500/40 dark:bg-amber-500/10 dark:text-amber-200',
                        'danger' => 'border-rose-200 bg-rose-50 text-rose-700 dark:border-rose-500/40 dark:bg-rose-500/10 dark:text-rose-200',
                    ];
                    $paymentStatusLabel = $order->payment_status
                        ? $order->payment_status->label()
                        : __('Tidak diketahui');
                @endphp

                <div class="rounded-xl border px-4 py-3 sm:px-5 sm:py-4 text-sm {{ $alertClasses[$alert['tone']] ?? $alertClasses['warning'] }}">
                    <p class="text-base font-semibold">{{ $alert['title'] }}</p>
                    <p class="mt-1">{{ $alert['message'] }}</p>
                    @if ($statusMessage)
                        <p class="mt-2 text-xs opacity-80">{{ $statusMessage }}</p>
                    @endif
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/60 p-4 sm:p-5 space-y-2">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Status Pembayaran') }}</p>
                        <p class="text-base font-semibold text-gray-900 dark:text-gray-100">
                            {{ $paymentStatusLabel }}
                        </p>
                        @if ($transactionStatus)
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ __('Status Transaksi Midtrans: :status', ['status' => ucfirst($transactionStatus)]) }}
                            </p>
                        @endif
                    </div>
                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/60 p-4 sm:p-5 space-y-2">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Total Pembayaran') }}</p>
                        <p class="text-base font-semibold text-gray-900 dark:text-gray-100">
                            Rp{{ number_format($order->total_amount, 0, ',', '.') }}
                        </p>
                        @if ($order->paid_at)
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ __('Dibayar pada: :date', ['date' => $order->paid_at->translatedFormat('d F Y H:i')]) }}
                            </p>
                        @endif
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-center gap-3">
                    <a href="{{ route('home') }}"
                       class="inline-flex items-center gap-2 rounded-full border border-gray-200 px-5 py-2 text-sm font-medium text-gray-700 transition hover:border-indigo-300 hover:text-indigo-600 dark:border-gray-700 dark:text-gray-200 dark:hover:border-indigo-500 dark:hover:text-indigo-300">
                        {{ __('Kembali ke Beranda') }}
                    </a>
                    @auth
                        @if (auth()->user()->isCustomer())
                            <a href="{{ route('customer.orders.show', $order) }}"
                               class="inline-flex items-center gap-2 rounded-full bg-indigo-600 px-5 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 transition">
                                {{ __('Lihat Detail Pesanan') }}
                            </a>
                        @endif
                    @endauth
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-2xl p-6 sm:p-7 space-y-4">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-gray-100">{{ __('Rincian Pesanan') }}</h2>
                <div class="space-y-3 divide-y divide-gray-100 dark:divide-gray-700/60">
                    @foreach ($order->items as $item)
                        <div class="pt-0 first:pt-0 sm:pt-3 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                            <div class="space-y-1">
                                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $item->menuItem?->name ?? '-' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $item->quantity }} x Rp{{ number_format($item->unit_price, 0, ',', '.') }}
                                </p>
                            </div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 sm:text-right">
                                Rp{{ number_format($item->subtotal, 0, ',', '.') }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endsection
