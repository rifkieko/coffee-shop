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
                        {{ __('Scan QRIS berikut menggunakan aplikasi pembayaran favorit Anda. Halaman ini akan otomatis memperbarui status pesanan setelah pembayaran berhasil.') }}
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
                @if (! $order->midtrans_token)
                    <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-2 text-sm text-rose-700 dark:border-rose-500/40 dark:bg-rose-500/10 dark:text-rose-200">
                        {{ __('Token pembayaran tidak tersedia. Silakan hubungi kasir untuk menyelesaikan pembayaran.') }}
                    </div>
                @else
                    <div
                        id="snap-container"
                        class="rounded-2xl border border-dashed border-indigo-300 dark:border-indigo-500/60 bg-indigo-50/60 dark:bg-indigo-500/10 p-4 sm:p-6 min-h-[320px] flex items-center justify-center">
                        <div class="text-center text-sm text-indigo-700 dark:text-indigo-200 space-y-2" id="snap-loading">
                            <span class="font-semibold">{{ __('Memuat QRIS ...') }}</span>
                            <p>{{ __('Jika tidak muncul dalam beberapa detik, periksa koneksi internet Anda lalu muat ulang halaman.') }}</p>
                        </div>
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 text-center sm:text-left">
                        {{ __('Kesulitan memindai?') }}
                        @if ($order->midtrans_redirect_url)
                            <a href="{{ $order->midtrans_redirect_url }}" target="_blank" rel="noopener" class="font-semibold text-indigo-600 hover:underline dark:text-indigo-300">
                                {{ __('Buka halaman pembayaran di tab baru.') }}
                            </a>
                        @endif
                        {{ __('Atau minta bantuan kasir kami.') }}
                    </div>
                @endif

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
            const snapContainer = document.querySelector('#snap-container');

            if (!snapContainer || typeof window.snap === 'undefined') {
                return;
            }

            window.snap.embed('{{ $order->midtrans_token }}', {
                embedId: 'snap-container',
                onSuccess: function () {
                    window.location.href = '{{ route('home') }}';
                },
                onPending: function () {
                    window.location.href = '{{ route('home') }}';
                },
                onError: function () {
                    alert('Pembayaran gagal. Silakan coba lagi atau hubungi kasir.');
                },
                onClose: function () {
                    // no-op
                }
            });

            const loadingState = document.querySelector('#snap-loading');
            if (loadingState) {
                const observer = new MutationObserver(() => {
                    const hasChildPayment = snapContainer.querySelector('iframe, img, canvas');
                    if (hasChildPayment) {
                        loadingState.remove();
                        observer.disconnect();
                    }
                });

                observer.observe(snapContainer, { childList: true, subtree: true });
            }
        });
    </script>
@endpush
