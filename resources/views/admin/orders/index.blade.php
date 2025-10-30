<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Daftar Pesanan') }}
            </h2>
            <form method="GET" action="{{ route('admin.orders.index') }}" class="flex items-center gap-3">
                <div>
                    <label for="status" class="sr-only">{{ __('Status') }}</label>
                    <select id="status" name="status"
                            class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">{{ __('Semua Status') }}</option>
                        @foreach (\App\Enums\OrderStatus::cases() as $case)
                            <option value="{{ $case->value }}" @selected(request('status') === $case->value)>
                                {{ $case->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="payment_status" class="sr-only">{{ __('Status Pembayaran') }}</label>
                    <select id="payment_status" name="payment_status"
                            class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">{{ __('Semua Pembayaran') }}</option>
                        @foreach (\App\Enums\PaymentStatus::cases() as $case)
                            <option value="{{ $case->value }}" @selected(request('payment_status') === $case->value)>
                                {{ $case->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <x-primary-button>
                    {{ __('Filter') }}
                </x-primary-button>
            </form>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Pesanan') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Pelanggan') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Meja') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Status') }}</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Total') }}</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Aksi') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($orders as $order)
                                <tr>
                                    <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100">
                                        <p class="font-semibold">{{ $order->order_number }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $order->created_at->format('d M Y H:i') }}</p>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $order->user?->name ?? '-' }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $order->table?->name ?? '-' }}
                                    </td>
                                    <td class="px-4 py-4 text-sm">
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
                                    <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100 text-right">
                                        Rp{{ number_format($order->total_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-right">
                                        <a href="{{ route('admin.orders.show', $order) }}"
                                           class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                            {{ __('Detail') }}
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
        </div>
    </div>
</x-app-layout>
