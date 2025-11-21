@extends('layouts.public')

@php
    use Illuminate\Support\Str;
@endphp

@section('content')
    <section class="py-10 bg-[#f5f6fb] min-h-screen">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.6em] text-[#b89a7a]">{{ __('Pala\'s Kopi') }}</p>
                    <h1 class="mt-3 text-3xl font-bold text-gray-900">{{ __('Riwayat Pembelian') }}</h1>
                    <p class="mt-2 text-sm text-gray-500">
                        {{ __('Lihat status pesanan yang sudah pernah dibuat, termasuk detail pembayaran.') }}
                    </p>
                </div>
                <a href="{{ route('catalog.index') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-full border border-[#1ec16b] bg-white px-4 py-2 text-xs font-semibold uppercase tracking-[0.3em] text-[#1ec16b] transition hover:bg-[#f6fff6]">
                    {{ __('Buat Pesanan Baru') }}
                </a>
            </div>

            <div class="space-y-4">
                @forelse ($orders as $order)
                    <div class="rounded-[24px] border border-white/70 bg-white/90 p-5 shadow-[0_20px_45px_rgba(15,15,30,0.1)]">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="text-xs uppercase tracking-[0.4em] text-[#8c6a4f]">{{ __('Nomor Pesanan') }}</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $order->order_number }}</p>
                            </div>
                            <span class="text-xs font-semibold uppercase tracking-[0.4em] text-[#2A1A13]">
                                {{ Str::title($order->status ?? 'pending') }}
                            </span>
                        </div>

                        <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="space-y-0.5">
                                <p class="text-sm font-semibold text-gray-800">{{ __('Total Pembayaran') }}</p>
                                <p class="text-2xl font-bold text-gray-900">Rp{{ number_format($order->total ?? $order->subtotal, 0, ',', '.') }}</p>
                            </div>
                            <div class="space-y-0.5">
                                <p class="text-xs uppercase tracking-[0.4em] text-gray-500">{{ __('Status Pembayaran') }}</p>
                                <p class="text-sm font-semibold text-[#2A1A13]">{{ ucfirst($order->payment_status ?? 'pending') }}</p>
                            </div>
                        </div>

                        <div class="mt-5 flex items-center justify-between">
                            <span class="text-xs text-gray-500">{{ $order->created_at?->format('d M Y H:i') }}</span>
                            <a href="{{ route('customer.orders.show', $order) }}"
                               class="inline-flex items-center gap-2 rounded-full border border-[#d4d4d4] px-4 py-1 text-xs font-semibold text-[#2A1A13] uppercase tracking-[0.4em] transition hover:bg-white">
                                {{ __('Detail Pesanan') }}
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 12h14M13 6l6 6-6 6" />
                                </svg>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="rounded-[24px] border border-white/70 bg-white/80 p-6 text-center text-sm text-gray-500 shadow-[0_20px_45px_rgba(15,15,30,0.05)]">
                        {{ __('Kamu belum memiliki riwayat pesanan. Buat order baru untuk mulai melihat statusnya.') }}
                    </div>
                @endforelse
            </div>

            @if ($orders instanceof \Illuminate\Contracts\Pagination\Paginator && $orders->hasPages())
                <div class="flex justify-center">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
