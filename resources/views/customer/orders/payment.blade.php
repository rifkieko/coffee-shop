<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Pembayaran Pesanan') }} #{{ $order->order_number }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6 space-y-4">
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        {{ __('Tekan tombol di bawah ini untuk membuka halaman pembayaran Midtrans. Setelah selesai, status akan diperbarui secara otomatis.') }}
                    </p>
                    <button id="pay-button"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-md hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        {{ __('Bayar Sekarang') }}
                    </button>

                    <div class="bg-gray-50 dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700 rounded-md p-4 text-sm text-gray-600 dark:text-gray-300 space-y-2">
                        <p>{{ __('Total Pembayaran:') }} <span class="font-semibold text-gray-900 dark:text-gray-100">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</span></p>
                        <p>{{ __('Pesanan untuk meja:') }} <span class="font-medium text-gray-900 dark:text-gray-100">{{ $order->table?->name ?? '-' }}</span></p>
                        <p>{{ __('Jika pembayaran gagal, Anda dapat mencoba lagi atau hubungi kasir.') }}</p>
                    </div>

                    <div id="payment-log" class="text-xs text-gray-400"></div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Rincian Menu') }}</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Menu') }}</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Jumlah') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Harga Satuan') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Subtotal') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($order->items as $item)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                            {{ $item->menuItem?->name ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-center text-gray-900 dark:text-gray-100">
                                            {{ $item->quantity }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-gray-100">
                                            Rp{{ number_format($item->unit_price, 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-gray-100">
                                            Rp{{ number_format($item->subtotal, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ config('midtrans.snap_url', 'https://app.sandbox.midtrans.com/snap/snap.js') }}" data-client-key="{{ $midtransClientKey }}"></script>
    <script>
        const payButton = document.querySelector('#pay-button');
        if (payButton) {
            payButton.addEventListener('click', function () {
                window.snap.pay('{{ $order->midtrans_token }}', {
                    onSuccess: function (result) {
                        logResult('success', result);
                        window.location.href = '{{ route('customer.orders.show', $order) }}';
                    },
                    onPending: function (result) {
                        logResult('pending', result);
                        window.location.href = '{{ route('customer.orders.show', $order) }}';
                    },
                    onError: function (result) {
                        logResult('error', result);
                        alert('Pembayaran gagal. Silakan coba lagi atau hubungi kasir.');
                    },
                    onClose: function () {
                        logResult('closed', {});
                    }
                });
            });
        }

        function logResult(status, data) {
            const logEl = document.querySelector('#payment-log');
            if (!logEl) {
                return;
            }

            const item = document.createElement('div');
            item.textContent = `[${new Date().toLocaleTimeString()}] ${status.toUpperCase()}`;
            logEl.appendChild(item);
        }
    </script>
</x-app-layout>
