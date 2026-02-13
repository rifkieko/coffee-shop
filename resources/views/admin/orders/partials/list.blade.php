@php
    $ordersCollection = method_exists($orders, 'getCollection') ? $orders->getCollection() : collect($orders);
    $orderIds = $ordersCollection->pluck('id')->all();
    $unpaidOrderIds = $ordersCollection
        ->filter(fn ($order) => $order->payment_status !== \App\Enums\PaymentStatus::Paid)
        ->pluck('id')
        ->all();
@endphp

<div id="orders-list"
     data-orders-partial
     data-latest-order-id="{{ $orders->first()?->id ?? 0 }}"
     data-order-ids="{{ implode(',', $orderIds) }}"
     data-unpaid-order-ids="{{ implode(',', $unpaidOrderIds) }}">
    <div class="p-4 sm:p-6 overflow-x-auto">
        <div class="space-y-4 md:hidden">
            @forelse ($orders as $order)
                <article class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 break-words">{{ $order->order_number }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $order->created_at->timezone('Asia/Jakarta')->format('d M Y H:i') }}</p>
                        </div>
                        <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex items-center gap-1 rounded-full border border-[#1ec16b]/60 bg-[#f0fbf7] px-3 py-1 text-[11px] font-bold uppercase tracking-[0.25em] text-[#1ec16b] shadow-sm transition hover:bg-[#1ec16b] hover:text-white shrink-0 self-start sm:self-auto">
                            {{ __('Update') }}
                        </a>
                    </div>
                    <div class="mt-3 flex flex-wrap gap-2">
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
                    </div>
                    <div class="mt-2 text-sm text-gray-700 dark:text-gray-200">
                        <p class="font-semibold">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $order->customer_name ?? $order->user?->name ?? '-' }}</p>
                    </div>
                </article>
            @empty
                <p class="text-center text-sm text-gray-500 dark:text-gray-400">{{ __('Belum ada pesanan.') }}</p>
            @endforelse
        </div>
        <table class="hidden md:table min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-xs sm:text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th class="px-2 py-2 sm:px-4 sm:py-3 text-left text-[11px] sm:text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Pesanan') }}</th>
                    <th class="px-2 py-2 sm:px-4 sm:py-3 text-left text-[11px] sm:text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hidden md:table-cell">{{ __('Pelanggan') }}</th>
                    <th class="px-2 py-2 sm:px-4 sm:py-3 text-left text-[11px] sm:text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hidden lg:table-cell">{{ __('Meja') }}</th>
                    <th class="px-2 py-2 sm:px-4 sm:py-3 text-left text-[11px] sm:text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Status') }}</th>
                    <th class="px-2 py-2 sm:px-4 sm:py-3 text-right text-[11px] sm:text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Total') }}</th>
                    <th class="px-2 py-2 sm:px-4 sm:py-3 text-right text-[11px] sm:text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">{{ __('Update Status') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($orders as $order)
                    <tr>
                        <td class="px-2 py-3 sm:px-4 sm:py-4 text-gray-900 dark:text-gray-100">
                            <p class="font-semibold">{{ $order->order_number }}</p>
                            <p class="text-[11px] sm:text-xs text-gray-500 dark:text-gray-400">{{ $order->created_at->timezone('Asia/Jakarta')->format('d M Y H:i') }}</p>
                        </td>
                        <td class="px-2 py-3 sm:px-4 sm:py-4 text-gray-500 dark:text-gray-400 hidden md:table-cell">
                            {{ $order->customer_name ?? $order->user?->name ?? '-' }}
                        </td>
                        <td class="px-2 py-3 sm:px-4 sm:py-4 text-gray-500 dark:text-gray-400 hidden lg:table-cell">
                            {{ $order->table_number ?? '-' }}
                        </td>
                        <td class="px-2 py-3 sm:px-4 sm:py-4">
                            <div class="flex flex-col gap-1">
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
                            </div>
                        </td>
                        <td class="px-2 py-3 sm:px-4 sm:py-4 text-gray-900 dark:text-gray-100 text-right whitespace-nowrap">
                            Rp{{ number_format($order->total_amount, 0, ',', '.') }}
                        </td>
                        <td class="px-2 py-3 sm:px-4 sm:py-4 text-right whitespace-nowrap">
                        <a href="{{ route('admin.orders.show', $order) }}"
                           class="inline-flex items-center gap-1 rounded-full border border-[#1ec16b]/60 bg-[#f0fbf7] px-3 py-1 text-xs font-bold uppercase tracking-[0.3em] text-[#1ec16b] shadow-sm transition hover:bg-[#1ec16b] hover:text-white" title="{{ __('Detail') }}">
                            <x-icons.eye class="hidden md:inline-block w-4 h-4" />
                            <span class="md:hidden">{{ __('Update') }}</span>
                            <span class="hidden md:inline">{{ __('Update Status') }}</span>
                        </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                            {{ __('Belum ada pesanan.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 pb-6">
        {{ $orders->links() }}
    </div>
</div>
