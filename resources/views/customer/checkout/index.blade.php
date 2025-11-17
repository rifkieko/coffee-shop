@extends('layouts.public')

@section('content')
    <section class="py-6 sm:py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <div class="flex flex-col gap-2 text-center sm:text-left">
                <h1 class="text-3xl sm:text-4xl font-semibold text-gray-900 dark:text-gray-100">{{ __('Checkout') }}</h1>
            </div>

            <div class="space-y-8">
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-[24px] border border-gray-200 dark:border-gray-700 p-6 sm:p-7 space-y-4">
                    <h2 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-gray-100">{{ __('Ringkasan Pesanan') }}</h2>
                    <div class="space-y-4 divide-y divide-gray-100 dark:divide-gray-700/60">
                        @foreach ($cart->items as $item)
                            <div class="pt-0 first:pt-0 sm:pt-4 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                                <div class="space-y-1">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 leading-tight">
                                        {{ $item->menuItem->name }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $item->quantity }} x Rp{{ number_format($item->unit_price, 0, ',', '.') }}
                                    </p>
                                    @if ($item->notes)
                                        <p class="text-xs text-gray-400 dark:text-gray-500">{{ __('Catatan: :notes', ['notes' => $item->notes]) }}</p>
                                    @endif
                                </div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 sm:text-right">
                                    Rp{{ number_format($item->subtotal, 0, ',', '.') }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        <div class="flex items-center justify-between text-sm font-semibold text-gray-600 dark:text-gray-300">
                            <span>{{ __('Subtotal') }}</span>
                            <span class="text-base text-gray-900 dark:text-gray-100">Rp{{ number_format($cart->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                            {{ __('Harga belum termasuk biaya layanan pembayaran dari Midtrans (jika ada).') }}
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl border border-gray-200 dark:border-gray-700 p-6 sm:p-8">
                    <div class="space-y-1">
                        <h2 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-gray-100">{{ __('Informasi Kontak') }}</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Isi data sesuai yang dapat dihubungi oleh barista saat pesanan siap.') }}</p>
                    </div>
                    <form method="POST" action="{{ route('checkout.store') }}" class="space-y-5" id="checkout-form">
                        @csrf
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Nama Lengkap') }}</label>
                            <input id="name" name="name" type="text" required
                                   value="{{ old('name', $user?->name) }}"
                                   class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100" />
                            @error('name')
                                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Email') }}</label>
                                <input id="email" name="email" type="email" required
                                       value="{{ old('email', $user?->email) }}"
                                       class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100" />
                                @error('email')
                                    <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Nomor Telepon') }}</label>
                                <input id="phone" name="phone" type="tel" required
                                       value="{{ old('phone', $user?->phone) }}"
                                       class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100" />
                                @error('phone')
                                    <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div>
                            <label for="table_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Nomor Meja') }}</label>
                            <input id="table_number" name="table_number" type="number" inputmode="numeric" min="1" required
                                   value="{{ old('table_number') }}"
                                   class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100" />
                            @error('table_number')
                                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div data-checkout-error class="hidden rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs text-rose-700 dark:border-rose-500/40 dark:bg-rose-500/10 dark:text-rose-200"></div>
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                            <p class="text-xs text-gray-500 dark:text-gray-400 sm:max-w-sm">
                                {{ __('Dengan melanjutkan, Anda menyetujui pemrosesan data untuk kebutuhan pemesanan dan pembayaran.') }}
                            </p>
                            <button type="submit" data-checkout-submit
                                    class="inline-flex w-full sm:w-auto items-center justify-center gap-2 rounded-full bg-[#1ec16b] px-5 py-2 text-sm font-semibold text-white shadow hover:bg-[#14a75c] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#1ec16b]">
                                {{ __('Lanjutkan ke Pembayaran') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ config('midtrans.snap_url', 'https://app.sandbox.midtrans.com/snap/snap.js') }}" data-client-key="{{ $midtransClientKey }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('#checkout-form');
            if (!form) {
                return;
            }

            const submitButton = form.querySelector('[data-checkout-submit]');
            const errorBox = form.querySelector('[data-checkout-error]');
            const defaultErrorMessage = '{{ __('Tidak dapat memproses pembayaran. Silakan coba kembali.') }}';
            const routes = @json($midtransRoutes);

            form.addEventListener('submit', async (event) => {
                if (!window.snap || typeof fetch === 'undefined') {
                    return;
                }

                event.preventDefault();

                if (errorBox) {
                    errorBox.classList.add('hidden');
                    errorBox.textContent = '';
                }

                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.classList.add('opacity-70', 'cursor-not-allowed');
                }

                let shouldReenable = true;

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: new FormData(form),
                    });

                    const rawData = await response.json().catch(() => ({}));
                    if (!response.ok) {
                        let message = defaultErrorMessage;
                        if (rawData && typeof rawData === 'object') {
                            if (rawData.errors && typeof rawData.errors === 'object') {
                                const firstKey = Object.keys(rawData.errors)[0] ?? null;
                                if (firstKey && Array.isArray(rawData.errors[firstKey]) && rawData.errors[firstKey].length > 0) {
                                    message = rawData.errors[firstKey][0];
                                }
                            } else if (rawData.message) {
                                message = rawData.message;
                            }
                        }
                        if (errorBox) {
                            errorBox.textContent = message;
                            errorBox.classList.remove('hidden');
                        } else {
                            alert(message);
                        }
                        if (response.headers.get('Content-Type')?.includes('text/html')) {
                            window.location.reload();
                        }
                        return;
                    }

                    const data = rawData && typeof rawData === 'object' ? rawData : {};
                    const midtransData = data.midtrans && typeof data.midtrans === 'object' ? data.midtrans : {};
                    if (!midtransData.token) {
                        const message = data.message ? data.message : '{{ __('Pesanan berhasil dibuat, namun pembayaran belum dapat diproses. Silakan hubungi kasir.') }}';
                        if (errorBox) {
                            errorBox.textContent = message;
                            errorBox.classList.remove('hidden');
                        } else {
                            alert(message);
                        }
                        return;
                    }

                    const order = data.order && typeof data.order === 'object' ? data.order : {};
                    const resultRoutes = midtransData.result_urls && typeof midtransData.result_urls === 'object' ? midtransData.result_urls : routes;
                    const redirectWith = (key, result = {}) => {
                        const base = (resultRoutes && resultRoutes[key]) ? resultRoutes[key] : ((resultRoutes && resultRoutes.finish) ? resultRoutes.finish : '{{ route('home') }}');
                        try {
                            const url = new URL(base, window.location.origin);
                            if (order.number) {
                                url.searchParams.set('order_id', order.number);
                            }
                            if (result && result.transaction_status) {
                                url.searchParams.set('transaction_status', result.transaction_status);
                            }
                            if (result && result.status_message) {
                                url.searchParams.set('status_message', result.status_message);
                            }
                            window.location.href = url.toString();
                        } catch (error) {
                            window.location.href = base;
                        }
                    };

                    window.snap.pay(midtransData.token, {
                        onSuccess: function (result) {
                            redirectWith('finish', result);
                        },
                        onPending: function (result) {
                            redirectWith('finish', result);
                        },
                        onError: function (result) {
                            redirectWith('error', result);
                        },
                        onClose: function () {
                            redirectWith('unfinish', {
                                transaction_status: 'pending',
                                status_message: 'Payment window closed by user',
                            });
                        }
                    });
                    shouldReenable = false;
                } catch (error) {
                    if (errorBox) {
                        errorBox.textContent = defaultErrorMessage;
                        errorBox.classList.remove('hidden');
                    } else {
                        alert(defaultErrorMessage);
                    }
                } finally {
                    if (shouldReenable && submitButton) {
                        submitButton.disabled = false;
                        submitButton.classList.remove('opacity-70', 'cursor-not-allowed');
                    }
                }
            });
        });
    </script>
@endpush
