<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Detail Pesanan') }} #{{ $order->order_number }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Dibuat pada') }} {{ $order->created_at->format('d M Y H:i') }}
                </p>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                {{ __('Kembali ke daftar') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ __('Informasi Pesanan') }}</h3>
                            <dl class="space-y-2 text-sm text-gray-600 dark:text-gray-300">
                                <div class="flex items-center justify-between">
                                    <dt>{{ __('Meja') }}</dt>
                                    <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $order->table?->name ?? '-' }}</dd>
                                </div>
                                <div class="flex items-center justify-between">
                                    <dt>{{ __('Pelanggan') }}</dt>
                                    <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $order->user?->name ?? '-' }}</dd>
                                </div>
                                <div class="flex items-center justify-between">
                                    <dt>{{ __('Status Pesanan') }}</dt>
                                    <dd>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                            @class([
                                                'bg-yellow-100 text-yellow-800' => $order->status === \App\Enums\OrderStatus::Pending,
                                                'bg-blue-100 text-blue-800' => $order->status === \App\Enums\OrderStatus::Preparing,
                                                'bg-green-100 text-green-800' => $order->status === \App\Enums\OrderStatus::Completed,
                                                'bg-gray-100 text-gray-800' => $order->status === \App\Enums\OrderStatus::Served,
                                                'bg-red-100 text-red-800' => $order->status === \App\Enums\OrderStatus::Cancelled,
                                            ])">
                                            {{ $order->status->label() }}
                                        </span>
                                    </dd>
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
                                    <dt>{{ __('Total') }}</dt>
                                    <dd class="font-semibold text-gray-900 dark:text-gray-100">
                                        Rp{{ number_format($order->total_amount, 0, ',', '.') }}
                                    </dd>
                                </div>
                                @if ($order->paid_at)
                                    <div class="flex items-center justify-between">
                                        <dt>{{ __('Tanggal Bayar') }}</dt>
                                        <dd class="font-medium text-gray-900 dark:text-gray-100">
                                            {{ $order->paid_at->format('d M Y H:i') }}
                                        </dd>
                                    </div>
                                @endif
                            </dl>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ __('Perbarui Status Pesanan') }}</h3>
                                <form method="POST" action="{{ route('admin.orders.update-status', $order) }}" class="flex items-center gap-3">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status"
                                            class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                        @foreach (\App\Enums\OrderStatus::cases() as $case)
                                            <option value="{{ $case->value }}" @selected($order->status === $case)>
                                                {{ $case->label() }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-primary-button>
                                        {{ __('Simpan') }}
                                    </x-primary-button>
                                </form>
                            </div>

                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ __('Perbarui Status Pembayaran') }}</h3>
                                <form method="POST" action="{{ route('admin.orders.update-payment', $order) }}" class="flex items-center gap-3">
                                    @csrf
                                    @method('PATCH')
                                    <select name="payment_status"
                                            class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                        @foreach (\App\Enums\PaymentStatus::cases() as $case)
                                            <option value="{{ $case->value }}" @selected($order->payment_status === $case)>
                                                {{ $case->label() }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-primary-button>
                                        {{ __('Simpan') }}
                                    </x-primary-button>
                                </form>
                            </div>
                        </div>
                    </div>

                    @if ($order->notes)
                        <div class="bg-gray-50 dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700 rounded-md p-4">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ __('Catatan Pelanggan') }}</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-300">{{ $order->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Rincian Pesanan') }}</h3>
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
