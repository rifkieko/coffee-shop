<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Status Pembayaran Pesanan') }}</h2>
            <div class="text-xs text-gray-500 uppercase tracking-[0.3em]">{{ __('Nomor Pesanan') }} {{ $order->order_number }}</div>
        </div>
    </x-slot>

    <div class="min-h-screen bg-[#f5f6fb] py-10 px-4 sm:px-6 lg:px-8">
        <div class="mx-auto flex w-full max-w-5xl flex-col gap-6">
            <div class="rounded-[32px] border border-white/70 bg-white p-6 shadow-[0_35px_100px_rgba(17,17,19,0.08)]">
                <div class="space-y-5">
                    <div class="rounded-[24px] border border-[#F3ECCC] bg-[#FFF8E0] p-5 text-sm font-semibold text-[#845F23]">
                        <p>{{ $alert['title'] }}</p>
                        <p class="text-xs font-normal text-gray-600">{{ $alert['message'] }}</p>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="rounded-[20px] border border-gray-100 bg-white p-4 shadow-sm">
                            <p class="text-xs uppercase tracking-[0.3em] text-gray-400">{{ __('Status Pembayaran') }}</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $transactionStatus ? ucfirst($transactionStatus) : $order->payment_status->label() }}</p>
                            <p class="text-xs text-gray-500">{{ __('Status Transaksi Midtrans') }}</p>
                        </div>
                        <div class="rounded-[20px] border border-gray-100 bg-white p-4 shadow-sm">
                            <p class="text-xs uppercase tracking-[0.3em] text-gray-400">{{ __('Total Pembayaran') }}</p>
                            <p class="text-xl font-bold text-gray-900">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</p>
                            <p class="text-xs text-gray-500">{{ __('Dibayar pada') }} {{ $order->paid_at?->timezone('Asia/Jakarta')->format('d M Y H:i') ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="grid gap-4 rounded-[20px] border border-gray-100 bg-white p-4 shadow-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-xs uppercase tracking-[0.3em] text-gray-400">{{ __('Status Pesanan') }}</span>
                            <span class="text-xs text-gray-500">{{ __('Diperbarui pada WIB') }}</span>
                        </div>
                        <div class="flex flex-wrap items-center gap-3 text-sm font-semibold text-gray-900">
                            <span>{{ $order->status->label() }}</span>
                            <span>{{ $order->payment_status->label() }}</span>
                            <span class="text-xs text-gray-500">{{ $order->created_at->timezone('Asia/Jakarta')->format('d M Y H:i') }}</span>
                        </div>
                    </div>
                    <div class="flex justify-center">
                        <a href="{{ route('home') }}" class="inline-flex items-center justify-center gap-2 rounded-full border border-[#1ec16b] px-4 py-2 text-sm font-semibold uppercase tracking-[0.3em] text-[#1ec16b] transition hover:bg-[#f6fff6]">
                            {{ __('Kembali ke Beranda') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="rounded-[32px] border border-white/70 bg-white p-6 shadow-[0_35px_80px_rgba(17,17,19,0.06)]">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Rincian Pesanan') }}</h3>
                    <span class="text-xs text-gray-500">{{ __('Meja') }} {{ $order->table_number ?? $order->table?->name ?? '-' }}</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Menu') }}</th>
                                <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Jumlah') }}</th>
                                <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Harga Satuan') }}</th>
                                <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Subtotal') }}</th>
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

            <div id="payment-log" class="text-xs text-gray-400"></div>
        </div>
    </div>
</x-app-layout>
