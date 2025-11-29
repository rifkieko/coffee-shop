<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs uppercase tracking-[0.4em] text-[#b07b57]">{{ __('Laporan') }}</p>
            <h1 class="text-2xl font-semibold text-[#2A1A13]">{{ __('Rekap Penjualan & Penghasilan') }}</h1>
            <p class="text-sm text-[#6b5445]">{{ __('Hitung total pesanan yang sudah dibayar dan unduh datanya dalam CSV.') }}</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto space-y-6 px-4 sm:px-6 lg:px-8">
            <div></div>
            <div class="rounded-3xl border border-[#f1d8c3] bg-gradient-to-br from-white to-[#fff9f2] p-6 shadow-sm">
                <div class="space-y-1">
                    <span class="inline-flex items-center rounded-full bg-[#F5E6D3] px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.3em] text-[#8C5A3A]">
                        {{ __('Rekap Penjualan') }}
                    </span>
                    <p class="text-sm text-[#6b5445]">{{ __('Atur rentang tanggal untuk menghitung rekap penjualan.') }}</p>
                </div>
                <form method="GET" action="{{ route('admin.reports.sales') }}" class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-4 md:items-end">
                    <div>
                        <label for="start_date" class="block text-xs font-semibold uppercase tracking-[0.2em] text-[#8C5A3A]">{{ __('Mulai') }}</label>
                        <input type="date" id="start_date" name="start_date" value="{{ $filters['start_date'] }}"
                               class="mt-1 block w-full rounded-2xl border border-[#f1d8c3] bg-white text-sm shadow-sm focus:border-amber-500 focus:ring-amber-500">
                    </div>
                    <div>
                        <label for="end_date" class="block text-xs font-semibold uppercase tracking-[0.2em] text-[#8C5A3A]">{{ __('Sampai') }}</label>
                        <input type="date" id="end_date" name="end_date" value="{{ $filters['end_date'] }}"
                               class="mt-1 block w-full rounded-2xl border border-[#f1d8c3] bg-white text-sm shadow-sm focus:border-amber-500 focus:ring-amber-500">
                    </div>
                    <div class="flex flex-nowrap items-center gap-2 md:col-span-2 md:justify-end">
                        <button type="submit"
                                class="inline-flex items-center justify-center whitespace-nowrap rounded-2xl border border-[#ECC9A8] bg-[#F5E6D3] px-5 py-2 text-sm font-semibold text-[#4C2B1C] shadow-sm hover:bg-[#F0DABD]">
                            {{ __('Terapkan Filter') }}
                        </button>
                        <a href="{{ route('admin.reports.sales.export', ['start_date' => $filters['start_date'], 'end_date' => $filters['end_date']]) }}"
                           class="inline-flex items-center justify-center whitespace-nowrap rounded-2xl border border-indigo-200 bg-indigo-50 px-5 py-2 text-sm font-semibold text-indigo-700 shadow-sm hover:bg-indigo-100">
                            {{ __('Download CSV') }}
                        </a>
                    </div>
                </form>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="rounded-2xl border border-indigo-100 bg-white p-5 shadow-sm">
                    <p class="text-xs uppercase tracking-[0.3em] text-gray-500">{{ __('Total Pesanan Dibayar') }}</p>
                    <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $summary['paid_orders'] }}</p>
                    <p class="text-xs text-gray-500">{{ __('Dalam rentang tanggal terpilih') }}</p>
                </div>
                <div class="rounded-2xl border border-emerald-100 bg-white p-5 shadow-sm">
                    <p class="text-xs uppercase tracking-[0.3em] text-gray-500">{{ __('Total Penghasilan') }}</p>
                    <p class="mt-2 text-3xl font-semibold text-emerald-700">Rp{{ number_format($summary['revenue'], 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-500">{{ __('Hanya menghitung pesanan dengan status Paid') }}</p>
                </div>
                <div class="rounded-2xl border border-amber-100 bg-white p-5 shadow-sm">
                    <p class="text-xs uppercase tracking-[0.3em] text-gray-500">{{ __('Rata-rata per Pesanan') }}</p>
                    <p class="mt-2 text-3xl font-semibold text-amber-700">Rp{{ number_format($summary['average'], 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-500">{{ __('Penghasilan / jumlah pesanan') }}</p>
                </div>
            </div>

            <div class="rounded-3xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h2 class="text-lg font-semibold text-[#2A1A13]">{{ __('Detail Pesanan') }}</h2>
                    <p class="text-sm text-[#6b5445]">{{ __('Daftar pesanan yang sudah dibayar sesuai rentang waktu.') }}</p>
                </div>

                <div class="hidden md:block overflow-x-auto px-4">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Tanggal') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Pesanan') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Customer') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Meja') }}</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Total') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($orders as $order)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                        {{ $order->created_at->timezone('Asia/Jakarta')->format('d M Y H:i') }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-gray-900 font-semibold">
                                        {{ $order->order_number }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-gray-600">
                                        {{ $order->customer_name ?? $order->user?->name ?? __('Tamu') }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-gray-600">
                                        {{ $order->table_number ?? __('Take Away') }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right text-gray-900 font-semibold">
                                        Rp{{ number_format($order->total_amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">
                                        {{ __('Belum ada pesanan dibayar di rentang ini.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="block md:hidden space-y-4 px-4 py-4">
                    @forelse ($orders as $order)
                        <div class="rounded-2xl border border-gray-200 p-4 shadow-sm">
                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <span>{{ $order->created_at->timezone('Asia/Jakarta')->format('d M Y H:i') }}</span>
                                <span class="font-semibold text-[#2A1A13]">{{ $order->order_number }}</span>
                            </div>
                            <div class="mt-2 space-y-1 text-sm text-gray-700">
                                <p><span class="font-semibold text-[#2A1A13]">{{ __('Customer:') }}</span> {{ $order->customer_name ?? $order->user?->name ?? __('Tamu') }}</p>
                                <p><span class="font-semibold text-[#2A1A13]">{{ __('Meja:') }}</span> {{ $order->table_number ?? __('Take Away') }}</p>
                            </div>
                            <div class="mt-3 flex items-center justify-between">
                                <p class="text-xs text-gray-500">{{ __('Total') }}</p>
                                <p class="text-lg font-semibold text-[#2A1A13]">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-sm text-gray-500">{{ __('Belum ada pesanan dibayar di rentang ini.') }}</p>
                    @endforelse
                </div>

                <div class="border-t border-gray-200 px-6 py-4">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

@push('scripts')
<script>
(function () {
    let refreshTimer;
    const startAutoRefresh = () => {
        if (refreshTimer) return;
        refreshTimer = setInterval(() => {
            if (document.visibilityState === 'visible') {
                window.location.reload();
            }
        }, 30000); // refresh every 30s while tab is visible
    };

    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'hidden' && refreshTimer) {
            clearInterval(refreshTimer);
            refreshTimer = null;
        } else if (document.visibilityState === 'visible') {
            startAutoRefresh();
        }
    });

    startAutoRefresh();
})();
</script>
@endpush
