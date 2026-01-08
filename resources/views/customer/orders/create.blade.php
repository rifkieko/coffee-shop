<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Pesan Menu') }} - {{ $table->name }}
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('Silakan pilih menu dan tentukan jumlah. Stok akan otomatis berkurang setelah pemesanan.') }}
            </p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if ($errors->any())
                <div class="bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded mb-4">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <x-auth-session-status class="mb-4" :status="session('status')" />

            @php
                $hasLowStock = $categories
                    ->pluck('menuItems')
                    ->flatten()
                    ->contains(fn ($item) => $item->stock < 10);
            @endphp
            @if ($hasLowStock)
                <div class="flex items-start gap-3 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-amber-900">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l6.518 11.6c.75 1.335-.213 3.001-1.743 3.001H3.482c-1.53 0-2.493-1.666-1.743-3.001l6.518-11.6zM11 14a1 1 0 10-2 0 1 1 0 002 0zm-1-2a.75.75 0 01-.75-.75v-3.5a.75.75 0 011.5 0v3.5A.75.75 0 0110 12z" clip-rule="evenodd" />
                    </svg>
                    <div>
                        <p class="text-sm font-semibold">Perhatian stok menipis</p>
                        <p class="text-sm">Beberapa menu stoknya di bawah 10. Pertimbangkan untuk restok sebelum kehabisan.</p>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('customer.orders.store', $table) }}">
                @csrf
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 space-y-6">
                        @foreach ($categories as $category)
                            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $category->name }}</h3>
                                    @if ($category->description)
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $category->description }}</p>
                                    @endif
                                </div>
                                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse ($category->menuItems as $item)
                                        <div class="px-6 py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <p class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ $item->name }}</p>
                                                    @if ($item->stock < 10)
                                                        <span class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-[11px] font-semibold uppercase tracking-wide text-amber-800">
                                                            {{ __('Stok menipis') }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $item->description }}</p>
                                                <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400 mt-1">
                                                    Rp{{ number_format($item->price, 0, ',', '.') }}
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Stok tersisa') }}: {{ $item->stock }}</p>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <label for="item-{{ $item->id }}" class="text-sm text-gray-700 dark:text-gray-300">{{ __('Jumlah') }}</label>
                                                <input id="item-{{ $item->id }}" name="items[{{ $item->id }}]" type="number" min="0" max="{{ $item->stock }}"
                                                       class="w-24 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500"
                                                       value="{{ old("items.{$item->id}", 0) }}">
                                            </div>
                                        </div>
                                    @empty
                                        <p class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ __('Belum ada menu di kategori ini.') }}</p>
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="space-y-6">
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                            <div class="p-6 space-y-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Ringkasan') }}</h3>
                                <div>
                                    <x-input-label for="notes" :value="__('Catatan untuk Barista')" />
                                    <textarea id="notes" name="notes" rows="4"
                                              class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                                    <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                                </div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('Setelah menekan tombol pesan, Anda akan diarahkan ke halaman pembayaran Xendit.') }}
                                </p>
                                <x-primary-button class="w-full justify-center">
                                    {{ __('Buat Pesanan & Bayar') }}
                                </x-primary-button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
