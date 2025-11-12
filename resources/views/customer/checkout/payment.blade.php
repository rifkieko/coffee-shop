@extends('layouts.public')

@section('content')
    <section class="py-10 sm:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-2xl p-6 sm:p-8 space-y-5">
                <div class="flex flex-col gap-2 text-center sm:text-left">
                    <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-gray-100">
                        {{ __('Pembayaran Pesanan') }} #{{ $order->order_number }}
                    </h1>
                    <p class="text-sm text-gray-600 dark:text-gray-300 max-w-2xl mx-auto sm:mx-0">
                        {{ __('Tekan tombol berikut untuk membuka halaman pembayaran Midtrans. Setelah selesai, status pesanan akan diperbarui otomatis.') }}
                    </p>
                </div>
                @if (session('status'))
                    <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm text-emerald-700 dark:border-emerald-500/40 dark:bg-emerald-500/10 dark:text-emerald-200">
                        {{ session('status') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-2 text-sm text-rose-700 dark:border-rose-500/40 dark:bg-rose-500/10 dark:text-rose-200">
                        {{ $errors->first() }}
                    </div>
                @endif
                <button id="pay-button"
                        class="inline-flex w-full sm:w-auto items-center justify-center gap-2 rounded-full bg-indigo-600 px-5 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 transition">
                    {{ __('Bayar Sekarang') }}
                </button>

                <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/60 p-4 sm:p-5 text-sm text-gray-600 dark:text-gray-300 space-y-2">
                    <p>{{ __('Nama Pemesan:') }} <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $order->customer_name ?? '-' }}</span></p>
                    <p>{{ __('Email:') }} <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $order->customer_email ?? '-' }}</span></p>
                    <p>{{ __('Nomor Telepon:') }} <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $order->customer_phone ?? '-' }}</span></p>
                    <p>{{ __('Total Pembayaran:') }} <span class="font-semibold text-gray-900 dark:text-gray-100">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</span></p>
                    <p>{{ __('Jika pembayaran gagal, Anda dapat mencoba lagi atau hubungi barista untuk bantuan.') }}</p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-2xl p-6 sm:p-7">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Rincian Menu') }}</h2>
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

@push('scripts')
    <script src="{{ config('midtrans.snap_url', 'https://app.sandbox.midtrans.com/snap/snap.js') }}" data-client-key="{{ $midtransClientKey }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const payButton = document.querySelector('#pay-button');

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
                        redirectWith('finish', result);
                    },
                    onPending: function (result) {
                        redirectWith('finish', result);
                    },
                    onError: function (result) {
                        redirectWith('error', result);
                    },
                    onClose: function () {
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
@endpush
