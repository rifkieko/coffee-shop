@extends('layouts.public')

@section('content')
    <section class="py-6 sm:py-12 font-['Figtree']">
        <div class="max-w-3xl mx-auto px-4">
            <div class="overflow-hidden rounded-[12px] bg-white shadow-[0_25px_45px_rgba(15,23,42,0.08)] border border-gray-200">
                <div class="flex items-center gap-2 bg-white px-4 py-3 text-sm font-semibold text-[#2A1A13]">
                    <a href="{{ route('cart.index') }}"
                       class="inline-flex items-center justify-center rounded-[12px] border border-[#d1d1d1] bg-white p-2 text-[#2A1A13] transition hover:bg-[#f5f5f5]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 6l-6 6 6 6" />
                        </svg>
                    </a>
                    <span class="text-base tracking-[0.3em] uppercase">{{ __('Checkout') }}</span>
                </div>
                <div class="space-y-6 px-6 py-8">
                    <div class="space-y-2">
                        <h2 class="text-2xl font-semibold text-[#4C5823]">{{ __('Informasi Pengguna') }}</h2>
                        <p class="text-sm text-[#c92f2f] leading-tight">
                            {{ __('Data digunakan untuk memproses pesanan. Pastikan Anda memasukkan data yang valid.') }}
                        </p>
                    </div>
                    <form method="POST" action="{{ route('checkout.store') }}" class="space-y-5" id="checkout-form">
                        @csrf
                        <div class="space-y-3">
                            <div>
                                <label for="name" class="sr-only">{{ __('Nama Lengkap') }}</label>
                                <div class="flex items-center gap-3 rounded-[12px] border border-[#d4d4d4] bg-white px-3 py-2 text-[#2A1A13] focus-within:border-[#b9b9b9]">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4z" />
                                        <path d="M6 20v-1c0-2.21 1.79-4 4-4h4c2.21 0 4 1.79 4 4v1" />
                                    </svg>
                                    <input id="name" name="name" type="text" required
                                           value="{{ old('name', $user?->name) }}"
                                           placeholder="{{ __('Name') }}"
                                           class="w-full bg-transparent text-sm font-semibold text-[#2A1A13] placeholder:text-[#b1b1b1] focus:outline-none focus:ring-0 border-0" />
                            </div>
                                @error('name')
                                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="phone" class="sr-only">{{ __('Nomor Telepon') }}</label>
                                <div class="flex items-center gap-3 rounded-[12px] border border-[#d4d4d4] bg-white px-3 py-2 text-[#2A1A13] focus-within:border-[#b9b9b9]">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2A19.86 19.86 0 0 1 3 5.18 2 2 0 0 1 5 3h3a2 2 0 0 1 2 1.72c.12 1.21.35 2.44.69 3.62a2 2 0 0 1-.45 2.11L9 11a16 16 0 0 0 6 6l1.55-1.55a2 2 0 0 1 2.11-.45c1.18.34 2.41.57 3.62.69A2 2 0 0 1 22 16.92z" />
                                    </svg>
                                    <input id="phone" name="phone" type="tel" required
                                           value="{{ old('phone', $user?->phone) }}"
                                           placeholder="{{ __('No Handphone') }}"
                                           class="w-full bg-transparent text-sm font-semibold text-[#2A1A13] placeholder:text-[#b1b1b1] focus:outline-none focus:ring-0 border-0" />
                                </div>
                                @error('phone')
                                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="email" class="sr-only">{{ __('Email') }}</label>
                                <div class="flex items-center gap-3 rounded-[12px] border border-[#d4d4d4] bg-white px-3 py-2 text-[#2A1A13] focus-within:border-[#b9b9b9]">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 4h16v16H4z" />
                                        <path d="m22 6-10 7-10-7" />
                                    </svg>
                                    <input id="email" name="email" type="email" required
                                           value="{{ old('email', $user?->email) }}"
                                           placeholder="{{ __('Email') }}"
                                           class="w-full bg-transparent text-sm font-semibold text-[#2A1A13] placeholder:text-[#b1b1b1] focus:outline-none focus:ring-0 border-0" />
                                </div>
                                @error('email')
                                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="table_number" class="sr-only">{{ __('Nomor Meja') }}</label>
                                <div class="flex items-center gap-3 rounded-[12px] border border-[#d4d4d4] bg-white px-3 py-2 text-[#2A1A13] focus-within:border-[#b9b9b9]">
                                    <span class="text-sm font-semibold tracking-[0.2em] text-[#2A1A13]">{{ __('Table') }}</span>
                                    <input id="table_number" name="table_number" type="number" inputmode="numeric" min="1" required
                                           value="{{ old('table_number') }}"
                                           class="w-full bg-transparent text-sm font-semibold text-[#34401d] placeholder:text-[#aabb81] focus:outline-none focus:ring-0 border-0" />
                                </div>
                                @error('table_number')
                                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <p class="text-xs text-[#c0391a]">
                            *{{ __('Struk pembayaran akan dikirim melalui email yang Anda daftarkan.') }}
                        </p>
                        <div data-checkout-error class="hidden rounded-[12px] border border-rose-200 bg-rose-50 px-4 py-3 text-xs text-rose-700 dark:border-rose-500/40 dark:bg-rose-500/10 dark:text-rose-200"></div>
                        <div class="border-t border-gray-200 pt-5">
                            <h3 class="text-base font-semibold text-[#5c783f]">{{ __('Metode Pembayaran') }}</h3>
                            <p class="text-xs text-[#7d8c4b]">{{ __('Pilih metode pembayaran yang Anda gunakan.') }}</p>
                            <label class="mt-3 flex cursor-pointer items-center justify-between rounded-[12px] border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-[#2A1A13] transition hover:border-[#b9b9b9] focus-within:border-[#7c8c48]">
                                <input type="checkbox" name="payment_method" value="qris" class="sr-only peer" checked />
                                <div class="flex items-center gap-3">
                                    <span class="hidden h-6 w-6 items-center justify-center rounded-full border border-[#d1d1d1] bg-white text-[#2A1A13] md:inline-flex">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-4 w-4">
                                            <path d="M3 3h7v7H3V3zm1 1v5h5V4H4zM14 3h7v7h-7V3zm1 1v5h5V4h-5zM3 14h7v7H3v-7zm1 1v5h5v-5H4zm9 0h7v7h-7v-7zm1 1v5h5v-5h-5z" fill="currentColor"/>
                                            <path d="M10 10h4v4h-4z" fill="#fff"/>
                                        </svg>
                                    </span>
                                    <div>
                                        <p class="text-sm font-semibold">{{ __('QRIS') }}</p>
                                        <p class="text-xs text-[#5c5c5c]">{{ __('Pembayaran otomatis melalui QRIS') }}</p>
                                    </div>
                                </div>
                                <span class="flex h-4 w-4 items-center justify-center rounded-full border-2 border-[#d1d1d1] bg-white transition peer-checked:border-[#5c783f] peer-checked:bg-[#5c783f]">
                                    <span class="h-2 w-2 rounded-full bg-transparent transition peer-checked:bg-white"></span>
                                </span>
                            </label>
                        </div>
                        <div class="mt-6">
                            @include('customer.cart.partials.summary', ['cart' => $cart])
                        </div>
                        <p class="text-sm font-semibold text-[#c92f2f]">
                            {{ __('Nama, No Handphone, Email, dan Table wajib diisi untuk memproses pesanan.') }}
                        </p>
                        <div class="mt-6 space-y-2">
                            <div class="flex items-center justify-between text-sm font-semibold text-[#4c5a35]">
                                <span>{{ __('Amount') }}</span>
                                <span>Rp{{ number_format($cart->subtotal, 0, ',', '.') }}</span>
                            </div>
                            <button type="submit" data-checkout-submit disabled
                                    class="w-full rounded-[20px] border border-[#1ec16b] bg-[#1ec16b] px-4 py-3 text-center text-sm font-semibold text-white transition hover:bg-[#14a75c] opacity-70 cursor-not-allowed">
                                <span class="inline-flex items-center justify-center gap-2">
                                    <span class="h-3 w-3 rounded-full border border-white text-[10px] leading-none">&#10003;</span>
                                    {{ __('Lanjutkan ke Pembayaran') }}
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('#checkout-form');
            if (!form) {
                return;
            }

            const submitButton = form.querySelector('[data-checkout-submit]');
            const errorBox = form.querySelector('[data-checkout-error]');
            const defaultErrorMessage = '{{ __('Tidak dapat memproses pembayaran. Silakan coba kembali.') }}';
            const paymentCheckbox = form.querySelector('input[name=\"payment_method\"]');

            const disableSubmit = () => {
                if (!submitButton) return;
                submitButton.disabled = true;
                submitButton.classList.add('opacity-70', 'cursor-not-allowed');
            };

            const enableSubmit = () => {
                if (!submitButton) return;
                submitButton.disabled = false;
                submitButton.classList.remove('opacity-70', 'cursor-not-allowed');
            };

            if (paymentCheckbox && submitButton) {
                if (paymentCheckbox.checked) {
                    enableSubmit();
                } else {
                    disableSubmit();
                }
                paymentCheckbox.addEventListener('change', () => {
                    if (paymentCheckbox.checked) {
                        enableSubmit();
                    } else {
                        disableSubmit();
                    }
                });
            }

            form.addEventListener('submit', async (event) => {
                event.preventDefault();

                if (errorBox) {
                    errorBox.classList.add('hidden');
                    errorBox.textContent = '';
                }

                disableSubmit();

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
                        return;
                    }

                    const data = rawData && typeof rawData === 'object' ? rawData : {};
                    const xenditData = data.xendit && typeof data.xendit === 'object' ? data.xendit : {};

                    if (xenditData.invoice_url) {
                        window.location.href = xenditData.invoice_url;
                        return;
                    }

                    if (data.checkout_payment_url) {
                        window.location.href = data.checkout_payment_url;
                        return;
                    }

                    const message = data.message ? data.message : '{{ __('Pesanan berhasil dibuat, namun pembayaran belum dapat diproses. Silakan hubungi kasir.') }}';
                    if (errorBox) {
                        errorBox.textContent = message;
                        errorBox.classList.remove('hidden');
                    } else {
                        alert(message);
                    }
                } catch (error) {
                    if (errorBox) {
                        errorBox.textContent = defaultErrorMessage;
                        errorBox.classList.remove('hidden');
                    } else {
                        alert(defaultErrorMessage);
                    }
                } finally {
                    enableSubmit();
                }
            });
        });
    </script>
@endpush
