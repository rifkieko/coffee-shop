<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Detail Pesanan') }} #{{ $order->order_number }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Status Pesanan') }}:
                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $order->status->label() }}</span>
                </p>
            </div>
            <a href="{{ route('customer.orders.history') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                {{ __('Kembali ke riwayat') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">
                                {{ __('Informasi Pesanan') }}
                            </h3>
                            <dl class="space-y-2 text-sm text-gray-600 dark:text-gray-300">
                                <div class="flex items-center justify-between">
                                    <dt>{{ __('Meja') }}</dt>
                                    <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $order->table?->name ?? '-' }}</dd>
                                </div>
                                <div class="flex items-center justify-between">
                                    <dt>{{ __('Waktu') }}</dt>
                                    <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $order->created_at->format('d M Y H:i') }}</dd>
                                </div>
                                <div class="flex items-center justify-between">
                                    <dt>{{ __('Status Pembayaran') }}</dt>
                                    <dd>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                            @class([
                                                'bg-green-100 text-green-800' => $order->payment_status === \App\Enums\PaymentStatus::Paid,
                                                'bg-yellow-100 text-yellow-800' => $order->payment_status === \App\Enums\PaymentStatus::Pending,
                                                'bg-red-100 text-red-800' => $order->payment_status === \App\Enums\PaymentStatus::Failed,
                                                'bg-gray-100 text-gray-800' => $order->payment_status === \App\Enums\PaymentStatus::Unpaid,
                                                'bg-orange-100 text-orange-800' => $order->payment_status === \App\Enums\PaymentStatus::Expired,
                                            ])">
                                            {{ $order->payment_status->label() }}
                                        </span>
                                    </dd>
                                </div>
                                <div class="flex items-center justify-between">
                                    <dt>{{ __('Total Pembayaran') }}</dt>
                                    <dd class="font-semibold text-gray-900 dark:text-gray-100">
                                        Rp{{ number_format($order->total_amount, 0, ',', '.') }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                        <div class="space-y-4">
                            @if ($order->payment_status !== \App\Enums\PaymentStatus::Paid && $order->midtrans_token)
                                <div class="bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-200 dark:border-indigo-700 rounded-md p-4">
                                    <h4 class="text-sm font-semibold text-indigo-700 dark:text-indigo-300 mb-2">{{ __('Status Pembayaran') }}</h4>
                                    <p class="text-xs text-indigo-600 dark:text-indigo-200 mb-3">
                                        {{ __('Jika pembayaran belum selesai, Anda dapat melanjutkan proses melalui tombol di bawah ini.') }}
                                    </p>
                                    <a href="{{ route('customer.orders.payment', $order) }}"
                                       class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-xs font-semibold rounded-md hover:bg-indigo-500">
                                        {{ __('Bayar Sekarang') }}
                                    </a>
                                </div>
                            @endif

                            @if ($order->notes)
                                <div class="bg-gray-50 dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700 rounded-md p-4">
                                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ __('Catatan') }}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ $order->notes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
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
</x-app-layout>
