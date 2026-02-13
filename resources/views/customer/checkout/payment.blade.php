@php
    use SimpleSoftwareIO\QrCode\Facades\QrCode;
@endphp

@extends('layouts.public')

@section('content')
    <section class="py-10 sm:py-12 bg-white text-gray-900">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white text-gray-900 overflow-hidden shadow-lg rounded-2xl border border-gray-200 p-6 sm:p-8 space-y-6">
                <div class="flex flex-col gap-2 text-center sm:text-left">
                    <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900">
                        {{ __('Pembayaran Pesanan') }} #{{ $order->order_number }}
                    </h1>
                    <p class="text-sm text-gray-600 max-w-2xl mx-auto sm:mx-0 leading-relaxed">
                        {{ __('Scan QRIS di bawah ini. Total pembayaran') }} <span class="font-semibold text-gray-900">Rp{{ number_format($payableAmount, 0, ',', '.') }}</span> {{ __('(termasuk 3 digit kode unik). Mohon transfer sesuai nominal hingga digit terakhir.') }}
                    </p>
                </div>

                @if (session('status'))
                    <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm text-emerald-700">
                        {{ session('status') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-2 text-sm text-rose-700">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
                    <div class="bg-white rounded-2xl border border-gray-200 p-6 flex flex-col items-center gap-4">
                        @if ($qrisString)
                            <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                                <div id="qr-image-wrapper">{!! QrCode::format('svg')->size(240)->margin(1)->generate($qrisString) !!}</div>
                            </div>
                            <div class="flex justify-center">
                                <button type="button"
                                        id="download-qr"
                                        class="inline-flex items-center gap-2 rounded-full bg-slate-800 text-white px-4 py-2 text-sm font-semibold shadow hover:bg-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-600">
                                    {{ __('Download QR') }}
                                </button>
                            </div>
                        @else
                            <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 text-center">
                                {{ __('QRIS belum dapat dibuat otomatis. Mohon hubungi kasir untuk menyelesaikan pembayaran.') }}
                            </div>
                        @endif

                        <div class="w-full flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-center text-sm text-gray-700">
                            @if ($uniqueCode)
                                <div class="rounded-full bg-indigo-50 text-indigo-700 px-4 py-2 font-semibold">
                                    {{ __('Kode Unik') }}: {{ $uniqueCode }}
                                </div>
                            @endif
                            <div class="rounded-full bg-emerald-50 text-emerald-700 px-4 py-2 font-semibold">
                                {{ __('Total') }}: Rp{{ number_format($payableAmount, 0, ',', '.') }}
                            </div>
                        </div>

                        <form method="POST" action="{{ route('checkout.confirm-payment', ['order' => $order->order_number]) }}" class="w-full sm:w-auto">
                            @csrf
                            <button type="submit"
                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-full bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow hover:bg-indigo-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 transition">
                                {{ __('Saya Sudah Bayar') }}
                            </button>
                        </form>
                    </div>

                    <div class="space-y-4 rounded-2xl border border-gray-200 bg-white p-5 text-sm text-gray-700">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">{{ __('Nama Pemesan') }}</span>
                            <span class="font-semibold text-gray-900">{{ $order->customer_name ?? '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">{{ __('Nomor Telepon') }}</span>
                            <span class="font-semibold text-gray-900">{{ $order->customer_phone ?? '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">{{ __('Email') }}</span>
                            <span class="font-semibold text-gray-900">{{ $order->customer_email ?? '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">{{ __('Nomor Meja') }}</span>
                            <span class="font-semibold text-gray-900">{{ $order->table_number ?? '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">{{ __('Total Pembayaran') }}</span>
                            <span class="font-semibold text-gray-900">Rp{{ number_format($payableAmount, 0, ',', '.') }}</span>
                        </div>
                        <p class="text-xs text-gray-500">
                            {{ __('Status Pembayaran:') }} <span class="font-semibold" data-payment-status>{{ $order->payment_status->label() }}</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-2xl p-6 sm:p-7">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Rincian Menu') }}</h2>
                <div class="space-y-3 divide-y divide-gray-100 dark:divide-gray-700/60">
                    @foreach ($order->items as $item)
                        <div class="pt-0 first:pt-0 sm:pt-3 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                            <div class="space-y-1">
                                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $item->menu_name ?? $item->menuItem?->name ?? '-' }}</p>
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

@push('styles')
    <style>
        /* Paksa tema terang pada halaman pembayaran */
        :root { color-scheme: light; }
        body { background: #fff !important; color: #111827 !important; }
    </style>
@endpush

@push('scripts')
    <script>
        (() => {
            const statusUrl = @json(route('checkout.status', ['order' => $order->order_number]));
            const paidUrl = @json(route('checkout.paid', ['order' => $order->order_number]));
            const statusText = document.querySelector('[data-payment-status]');
            const refreshMs = 1000;
            let timer = null;

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
                        credentials: 'same-origin',
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
            };

            const start = () => {
                if (timer) return;
                timer = setInterval(pollStatus, refreshMs);
                pollStatus();
            };

            const stop = () => {
                if (!timer) return;
                clearInterval(timer);
                timer = null;
            };

            const boot = () => {
                start();
                document.addEventListener('visibilitychange', () => {
                    if (document.hidden) {
                        stop();
                    } else {
                        start();
                    }
                });
            };

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', boot);
            } else {
                boot();
            }
        })();
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
