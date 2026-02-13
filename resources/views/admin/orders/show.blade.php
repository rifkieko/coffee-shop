<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Detail Pesanan') }} #{{ $order->order_number }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Dibuat pada') }} {{ $order->created_at->timezone('Asia/Jakarta')->format('d M Y H:i') }}
                </p>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-1 text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                <x-icons.arrow-left class="w-4 h-4" />
                {{ __('Kembali ke daftar') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(0,320px)]">
                        <div class="space-y-5">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Informasi Pesanan') }}</h3>
                            <div class="grid gap-4 text-sm text-gray-700 dark:text-gray-200 sm:grid-cols-2">
                                <div>
                                    <dt class="text-[11px] uppercase tracking-[0.22em] text-gray-500 dark:text-gray-400">{{ __('Nomor Meja') }}</dt>
                                    <dd class="mt-1 text-base font-semibold text-gray-900 dark:text-gray-100">{{ $order->table_number ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-[11px] uppercase tracking-[0.22em] text-gray-500 dark:text-gray-400">{{ __('Pelanggan') }}</dt>
                                    <dd class="mt-1 text-base font-semibold text-gray-900 dark:text-gray-100">{{ $order->customer_name ?? $order->user?->name ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-[11px] uppercase tracking-[0.22em] text-gray-500 dark:text-gray-400">{{ __('Status Pesanan') }}</dt>
                                    <dd>
                                        <span @class([
                                            'inline-flex items-center rounded-full px-3 py-1 text-sm font-semibold',
                                            'bg-yellow-100 text-yellow-800' => $order->status === \App\Enums\OrderStatus::Pending,
                                            'bg-blue-100 text-blue-800' => $order->status === \App\Enums\OrderStatus::Preparing,
                                            'bg-green-100 text-green-800' => $order->status === \App\Enums\OrderStatus::Completed,
                                            'bg-gray-100 text-gray-800' => $order->status === \App\Enums\OrderStatus::Served,
                                            'bg-red-100 text-red-800' => $order->status === \App\Enums\OrderStatus::Cancelled,
                                        ])>
                                            {{ $order->status->label() }}
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-[11px] uppercase tracking-[0.22em] text-gray-500 dark:text-gray-400">{{ __('Status Pembayaran') }}</dt>
                                    <dd>
                                        <span @class([
                                            'inline-flex items-center rounded-full px-3 py-1 text-sm font-semibold',
                                            'bg-green-100 text-green-800' => $order->payment_status === \App\Enums\PaymentStatus::Paid,
                                            'bg-yellow-100 text-yellow-800' => $order->payment_status === \App\Enums\PaymentStatus::Pending,
                                            'bg-red-100 text-red-800' => $order->payment_status === \App\Enums\PaymentStatus::Failed,
                                            'bg-gray-100 text-gray-800' => $order->payment_status === \App\Enums\PaymentStatus::Unpaid,
                                            'bg-orange-100 text-orange-800' => $order->payment_status === \App\Enums\PaymentStatus::Expired,
                                        ])>
                                            {{ $order->payment_status->label() }}
                                        </span>
                                    </dd>
                                </div>
                                <div class="sm:col-span-2">
                                    <dt class="text-[11px] uppercase tracking-[0.22em] text-gray-500 dark:text-gray-400">{{ __('Total') }}</dt>
                                    <dd class="mt-1 text-base font-semibold text-gray-900 dark:text-gray-100">
                                        Rp{{ number_format($order->total_amount, 0, ',', '.') }}
                                    </dd>
                                </div>
                                @if ($order->paid_at)
                                    <div class="sm:col-span-2">
                                        <dt class="text-[11px] uppercase tracking-[0.22em] text-gray-500 dark:text-gray-400">{{ __('Tanggal Bayar') }}</dt>
                                        <dd class="mt-1 text-base font-semibold text-gray-900 dark:text-gray-100">
                                            {{ $order->paid_at?->timezone('Asia/Jakarta')->format('d M Y H:i') }}
                                        </dd>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="space-y-6 border-t border-gray-100 pt-4 dark:border-gray-700 lg:border-l lg:border-t-0 lg:pl-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">{{ __('Perbarui Status Pesanan') }}</h3>
                                <form method="POST" action="{{ route('admin.orders.update-status', $order) }}" class="flex flex-col gap-3">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" @class([
                                        'block w-full cursor-pointer rounded-full border px-4 py-2.5 text-sm font-semibold shadow-sm transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-900' => true,
                                        'border-blue-600 bg-blue-600 text-white hover:border-blue-700 hover:bg-blue-700 focus:border-blue-600 focus:ring-blue-500/30' => $order->status === \App\Enums\OrderStatus::Preparing,
                                        'border-emerald-600 bg-emerald-600 text-white hover:border-emerald-700 hover:bg-emerald-700 focus:border-emerald-600 focus:ring-emerald-500/30' => $order->status === \App\Enums\OrderStatus::Completed,
                                        'border-[#1ec16b]/40 bg-white text-[#1f1a17] hover:border-[#1ec16b]/70 focus:border-[#1ec16b] focus:ring-[#1ec16b]/30 dark:border-emerald-500/40 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-emerald-400 dark:focus:ring-emerald-500/30' => ! in_array($order->status, [\App\Enums\OrderStatus::Preparing, \App\Enums\OrderStatus::Completed], true),
                                    ]) onchange="this.form.submit()">
                                        @foreach (\App\Enums\OrderStatus::cases() as $case)
                                            @if (in_array($case, [\App\Enums\OrderStatus::Preparing, \App\Enums\OrderStatus::Completed], true))
                                                <option value="{{ $case->value }}" @selected($order->status === $case)>
                                                    {{ $case->label() }}
                                                </option>
                                            @endif
                                    @endforeach
                                    </select>
                                </form>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">{{ __('Perbarui Status Pembayaran') }}</h3>
                                <form method="POST" action="{{ route('admin.orders.update-payment', $order) }}" class="flex flex-col gap-3">
                                    @csrf
                                    @method('PATCH')
                                    <select name="payment_status" @class([
                                        'block w-full cursor-pointer rounded-full border px-4 py-2.5 text-sm font-semibold shadow-sm transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-900' => true,
                                        'border-amber-500 bg-amber-500 text-white hover:border-amber-600 hover:bg-amber-600 focus:border-amber-500 focus:ring-amber-400/30' => $order->payment_status === \App\Enums\PaymentStatus::Pending,
                                        'border-emerald-600 bg-emerald-600 text-white hover:border-emerald-700 hover:bg-emerald-700 focus:border-emerald-600 focus:ring-emerald-500/30' => $order->payment_status === \App\Enums\PaymentStatus::Paid,
                                        'border-[#1ec16b]/40 bg-white text-[#1f1a17] hover:border-[#1ec16b]/70 focus:border-[#1ec16b] focus:ring-[#1ec16b]/30 dark:border-emerald-500/40 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-emerald-400 dark:focus:ring-emerald-500/30' => ! in_array($order->payment_status, [\App\Enums\PaymentStatus::Pending, \App\Enums\PaymentStatus::Paid], true),
                                    ]) onchange="this.form.submit()">
                                        @foreach (\App\Enums\PaymentStatus::cases() as $case)
                                            @if (in_array($case, [\App\Enums\PaymentStatus::Pending, \App\Enums\PaymentStatus::Paid], true))
                                                <option value="{{ $case->value }}" @selected($order->payment_status === $case)>
                                                    {{ $case->label() }}
                                                </option>
                                            @endif
                                    @endforeach
                                    </select>
                                </form>
                            </div>
                        </div>
                    </div>
                    @if ($order->notes)
                        <div class="mt-6 bg-gray-50 dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700 rounded-md p-4">
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
                                            {{ $item->menu_name ?? $item->menuItem?->name ?? '-' }}
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
