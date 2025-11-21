@extends('layouts.minimal')

@section('content')
    <div class="space-y-6">
        <div class="rounded-[28px] border border-white/70 bg-white p-6 shadow-[0_30px_70px_rgba(17,17,19,0.08)]">
            <p class="text-center text-lg font-semibold text-[#2A1A13] mb-1">{{ __('Status Pembayaran Pesanan') }}</p>
            <p class="text-center text-xs text-gray-500 uppercase tracking-[0.3em]">{{ __('Nomor Pesanan') }} {{ $order->order_number }}</p>
            <div class="mt-4 space-y-4">
                <div class="rounded-2xl border border-[#F0E7D7] bg-[#FEF5DF]/80 p-4 text-sm text-[#4C2B1C]">
                    <p class="font-semibold">{{ __('Pembayaran sedang diproses') }}</p>
                    <p class="text-xs text-[#845F23]">
                        {{ __('Kami sedang menunggu konfirmasi dari Midtrans. Pesanan akan otomatis diperbarui setelah pembayaran selesai.') }}
                    </p>
                </div>
                <div class="grid gap-3 md:grid-cols-2">
                    <div class="rounded-2xl border border-gray-200 p-4">
                        <p class="text-xs uppercase tracking-[0.3em] text-gray-500">{{ __('Status Pembayaran') }}</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $order->payment_status->label() }}</p>
                        <p class="text-xs text-gray-500">{{ __('Status Transaksi Midtrans:') }} {{ ucfirst($order->payment_status->value ?? 'pending') }}</p>
                    </div>
                    <div class="rounded-2xl border border-gray-200 p-4">
                        <p class="text-xs uppercase tracking-[0.3em] text-gray-500">{{ __('Total Pembayaran') }}</p>
                        <p class="text-2xl font-bold text-gray-900">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</p>
                    </div>
                </div>
                <div>
                    <button id="pay-button" class="w-full rounded-full bg-[#1ec16b] px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-[#1ec16b]/40 transition hover:bg-[#14a75c]">
                        {{ __('Bayar Sekarang') }}
                    </button>
                </div>
            </div>
        </div>

        <div class="rounded-[28px] border border-white/70 bg-white p-6 shadow-[0_15px_40px_rgba(17,17,19,0.05)]">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('Rincian Pesanan') }}</h3>
                <span class="text-xs text-gray-500">{{ __('Untuk meja') }} {{ $order->table_number ?? $order->table?->name ?? '-' }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left uppercase tracking-[0.3em] text-xs text-gray-500">{{ __('Menu') }}</th>
                            <th class="px-4 py-3 text-center uppercase tracking-[0.3em] text-xs text-gray-500">{{ __('Jumlah') }}</th>
                            <th class="px-4 py-3 text-right uppercase tracking-[0.3em] text-xs text-gray-500">{{ __('Harga Satuan') }}</th>
                            <th class="px-4 py-3 text-right uppercase tracking-[0.3em] text-xs text-gray-500">{{ __('Subtotal') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($order->items as $item)
                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $item->menuItem?->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-center text-gray-600">{{ $item->quantity }}</td>
                                <td class="px-4 py-3 text-right text-gray-600">Rp{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-900">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ config('midtrans.snap_url', 'https://app.sandbox.midtrans.com/snap/snap.js') }}" data-client-key="{{ $midtransClientKey }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const payButton = document.querySelector('#pay-button');
                const routes = {
                    finish: @json(route('midtrans.finish')),
                    unfinish: @json(route('midtrans.unfinish')),
                    error: @json(route('midtrans.error')),
                };
                const orderNumber = @json($order->order_number);
                if (!payButton) return;

                const redirectWith = (key, result = {}) => {
                    trackStatus(key);
                    const base = routes[key] ?? routes.finish;
                    const url = new URL(base, window.location.origin);
                    if (orderNumber) {
                        url.searchParams.set('order_id', orderNumber);
                    }
                    if (result.transaction_status) {
                        url.searchParams.set('transaction_status', result.transaction_status);
                    }
                    if (result.status_message) {
                        url.searchParams.set('status_message', result.status_message);
                    }
                    window.location.href = url.toString();
                };

                const trackStatus = (status) => {
                    const log = document.getElementById('payment-log');
                    if (!log) return;
                    const entry = document.createElement('div');
                    entry.className = 'text-[11px] text-gray-400';
                    entry.textContent = `[${new Date().toLocaleTimeString()}] ${status}`;
                    log.appendChild(entry);
                };

                payButton.addEventListener('click', function () {
                    window.snap.pay('{{ $order->midtrans_token }}', {
                        onSuccess(result) {
                            redirectWith('finish', result);
                        },
                        onPending(result) {
                            redirectWith('finish', result);
                        },
                        onError(result) {
                            redirectWith('error', result);
                        },
                        onClose() {
                            redirectWith('unfinish', { transaction_status: 'pending', status_message: 'Payment window closed by user' });
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection

    <script src="{{ config('midtrans.snap_url', 'https://app.sandbox.midtrans.com/snap/snap.js') }}" data-client-key="{{ $midtransClientKey }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const payButton = document.querySelector('#pay-button');
            const logEl = document.querySelector('#payment-log');

            if (!payButton) {
                return;
            }

            const routes = {
                finish: @json(route('midtrans.finish')),
                unfinish: @json(route('midtrans.unfinish')),
                error: @json(route('midtrans.error')),
            };
            const orderNumber = @json($order->order_number);
            const shouldAutoLaunch = @json(request()->boolean('auto'));

            const logResult = (status) => {
                if (!logEl) {
                    return;
                }

                const item = document.createElement('div');
                item.textContent = `[${new Date().toLocaleTimeString()}] ${status.toUpperCase()}`;
                logEl.appendChild(item);
            };

            const redirectWith = (key, result = {}) => {
                const base = routes[key] ?? routes.finish;
                const url = new URL(base, window.location.origin);

                if (orderNumber) {
                    url.searchParams.set('order_id', orderNumber);
                }

                if (result.transaction_status) {
                    url.searchParams.set('transaction_status', result.transaction_status);
                }

                if (result.status_message) {
                    url.searchParams.set('status_message', result.status_message);
                }

                window.location.href = url.toString();
            };

            payButton.addEventListener('click', function () {
                window.snap.pay('{{ $order->midtrans_token }}', {
                    onSuccess: function (result) {
                        logResult('success');
                        redirectWith('finish', result);
                    },
                    onPending: function (result) {
                        logResult('pending');
                        redirectWith('finish', result);
                    },
                    onError: function (result) {
                        logResult('error');
                        redirectWith('error', result);
                    },
                    onClose: function () {
                        logResult('closed');
                        redirectWith('unfinish', { transaction_status: 'pending', status_message: 'Payment window closed by user' });
                    }
                });
            });

            if (shouldAutoLaunch) {
                setTimeout(() => {
                    payButton.click();

                    try {
                        const currentUrl = new URL(window.location.href);
                        currentUrl.searchParams.delete('auto');
                        const newSearch = currentUrl.searchParams.toString();
                        window.history.replaceState({}, document.title, currentUrl.pathname + (newSearch ? `?${newSearch}` : '') + currentUrl.hash);
                    } catch (error) {
                        // ignore history failures
                    }
                }, 300);
            }
        });
    </script>
</x-app-layout>
