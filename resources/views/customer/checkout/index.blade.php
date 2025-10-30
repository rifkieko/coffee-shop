@extends('layouts.public')

@section('content')
    <section class="py-10 sm:py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <div class="flex flex-col gap-2 text-center sm:text-left">
                <h1 class="text-3xl sm:text-4xl font-semibold text-gray-900 dark:text-gray-100">{{ __('Checkout') }}</h1>
                <p class="text-sm sm:text-base text-gray-500 dark:text-gray-400 max-w-3xl mx-auto sm:mx-0">
                    {{ __('Masukkan data kontak Anda untuk melanjutkan pembayaran. Login tidak wajib, namun disarankan agar pesanan mudah dipantau.') }}
                </p>
                @guest
                    <p class="text-xs text-indigo-600 dark:text-indigo-300">
                        {{ __('Sudah punya akun?') }}
                        <a href="{{ route('login') }}" class="font-semibold hover:underline">{{ __('Masuk sekarang') }}</a>
                        {{ __('agar alamat email dan nomor telepon terisi otomatis.') }}
                    </p>
                @endguest
            </div>

            <div class="grid gap-8 lg:grid-cols-3 lg:items-start">
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-6 sm:p-8 space-y-6">
                    <div class="space-y-1">
                        <h2 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-gray-100">{{ __('Informasi Kontak') }}</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Isi data sesuai yang dapat dihubungi oleh barista saat pesanan siap.') }}</p>
                    </div>
                    <form method="POST" action="{{ route('checkout.store') }}" class="space-y-5">
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
                            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Catatan untuk Barista') }}</label>
                            <textarea id="notes" name="notes" rows="4"
                                      class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                            <p class="text-xs text-gray-500 dark:text-gray-400 sm:max-w-sm">
                                {{ __('Dengan melanjutkan, Anda menyetujui pemrosesan data untuk kebutuhan pemesanan dan pembayaran.') }}
                            </p>
                            <button type="submit"
                                    class="inline-flex w-full sm:w-auto items-center justify-center gap-2 rounded-full bg-indigo-600 px-5 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                                {{ __('Lanjutkan ke Pembayaran') }}
                            </button>
                        </div>
                    </form>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-6 sm:p-7 space-y-4">
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
            </div>
        </div>
    </section>
@endsection
