@php
    use SimpleSoftwareIO\QrCode\Facades\QrCode;
@endphp

@extends('layouts.public')

@section('content')
    <section class="py-10 sm:py-12 bg-white text-gray-900">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white text-gray-900 overflow-hidden shadow-lg rounded-2xl border border-gray-200 p-6 sm:p-8 space-y-5">
                <div class="text-center space-y-1">
                    <p class="text-lg font-semibold text-gray-900 mb-1">{{ __('Pembayaran Pesanan') }}</p>
                    <p class="text-xs text-gray-500 uppercase tracking-[0.3em]">{{ __('Nomor Pesanan') }} {{ $order->order_number }}</p>
                    <p class="text-sm text-gray-600">
                        {{ __('Scan QRIS di bawah ini. Total sudah termasuk 3 digit kode unik untuk verifikasi manual.') }}
                    </p>
                </div>

                @if ($qrisString)
                    <div class="flex flex-col items-center gap-3">
                        <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                            <div id="qr-image-wrapper">{!! QrCode::format('svg')->size(220)->margin(1)->generate($qrisString) !!}</div>
                        </div>
                        <div class="flex flex-wrap items-center justify-center gap-3 text-sm font-semibold text-gray-800">
                            @if ($uniqueCode)
                                <span class="rounded-full bg-indigo-50 px-3 py-1 text-indigo-700">{{ __('Kode Unik') }}: {{ $uniqueCode }}</span>
                            @endif
                            <span class="rounded-full bg-emerald-50 px-3 py-1 text-emerald-700">Rp{{ number_format($payableAmount, 0, ',', '.') }}</span>
                        </div>
                        <button type="button"
                                id="download-qr"
                                class="inline-flex items-center gap-2 rounded-full bg-slate-800 text-white px-4 py-2 text-sm font-semibold shadow hover:bg-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-600">
                            {{ __('Download QR') }}
                        </button>
                    </div>
                @else
                    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900 text-center">
                        {{ __('QRIS belum tersedia. Mohon hubungi barista untuk bantuan pembayaran.') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('checkout.confirm-payment', ['order' => $order->order_number]) }}" class="text-center">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center justify-center rounded-full bg-[#1ec16b] px-5 py-2 text-sm font-semibold text-white shadow-lg shadow-[#1ec16b]/40 transition hover:bg-[#14a75c]">
                        {{ __('Saya Sudah Bayar') }}
                    </button>
                </form>

                <p class="text-center text-xs text-gray-500 mt-2">
                    {{ __('Status Pembayaran:') }} <span class="font-semibold" data-payment-status>{{ $order->payment_status->label() }}</span>
                </p>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white text-gray-900 p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Rincian Pesanan') }}</h3>
                    <span class="text-xs text-gray-500">{{ __('Untuk meja') }} {{ $order->table_number ?? '-' }}</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left uppercase tracking-[0.3em] text-xs text-gray-500">{{ __('Menu') }}</th>
                                <th class="px-4 py-3 text-center uppercase tracking-[0.3em] text-xs text-gray-500">{{ __('Jumlah') }}</th>
                                <th class="px-4 py-3 text-right uppercase tracking-[0.3em] text-xs text-gray-500">{{ __('Harga Satuan') }}</th>
                                <th class="px-4 py-3 text-right uppercase tracking-[0.3em] text-xs text-gray-500">{{ __('Subtotal') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($order->items as $item)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $item->menu_name ?? $item->menuItem?->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-center text-gray-600">{{ $item->quantity }}</td>
                                    <td class="px-4 py-3 text-right text-gray-600">Rp{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right font-semibold text-gray-900">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        /* Paksa tema terang pada halaman pembayaran */
        :root { color-scheme: light; }
        body { background: #fff !important; color: #111827 !important; }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const statusUrl = @json(route('checkout.status', ['order' => $order->order_number]));
            const paidUrl = @json(route('checkout.paid', ['order' => $order->order_number]));
            const statusText = document.querySelector('[data-payment-status]');

            const updateStatus = (label) => {
                if (statusText) {
                    statusText.textContent = label;
                }
            };

            const pollStatus = async () => {
                try {
                    const res = await fetch(statusUrl, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        cache: 'no-store',
                    });
                    if (!res.ok) return;
                    const data = await res.json();
                    if (data.payment_status === 'paid') {
                        updateStatus(data.payment_status_label || 'Paid');
                        window.location.href = paidUrl;
                        return;
                    }
                    if (data.payment_status_label) {
                        updateStatus(data.payment_status_label);
                    }
                } catch (e) {
                    // ignore transient errors
                }
                setTimeout(pollStatus, 3500);
            };

            pollStatus();
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const downloadBtn = document.querySelector('#download-qr');
            const wrapper = document.querySelector('#qr-image-wrapper');
            if (!downloadBtn || !wrapper) return;

            const downloadJpeg = () => {
                const svg = wrapper.querySelector('svg');
                if (!svg) return;

                const serializer = new XMLSerializer();
                const svgString = serializer.serializeToString(svg);
                const svgBlob = new Blob([svgString], { type: 'image/svg+xml;charset=utf-8' });
                const url = URL.createObjectURL(svgBlob);
                const img = new Image();
                img.crossOrigin = 'anonymous';
                img.onload = () => {
                    const canvas = document.createElement('canvas');
                    canvas.width = img.width;
                    canvas.height = img.height;
                    const ctx = canvas.getContext('2d');
                    ctx.fillStyle = '#ffffff';
                    ctx.fillRect(0, 0, canvas.width, canvas.height);
                    ctx.drawImage(img, 0, 0);
                    URL.revokeObjectURL(url);
                    canvas.toBlob((blob) => {
                        if (!blob) return;
                        const link = document.createElement('a');
                        link.href = URL.createObjectURL(blob);
                        link.download = 'qris-{{ $order->order_number }}.jpeg';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                        URL.revokeObjectURL(link.href);
                    }, 'image/jpeg', 0.95);
                };
                img.src = url;
            };

            downloadBtn.addEventListener('click', downloadJpeg);
        });
    </script>
@endpush
