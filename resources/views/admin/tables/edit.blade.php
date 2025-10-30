<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Ubah Meja') }}
            </h2>
            <a href="{{ route('admin.tables.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                {{ __('Kembali ke daftar') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6 space-y-6">
                    <form method="POST" action="{{ route('admin.tables.update', $table) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="name" :value="__('Nama Meja')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                          :value="old('name', $table->name)" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="capacity" :value="__('Kapasitas (orang)')" />
                            <x-text-input id="capacity" name="capacity" type="number" min="1" max="20"
                                          class="mt-1 block w-full" :value="old('capacity', $table->capacity)" required />
                            <x-input-error :messages="$errors->get('capacity')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="notes" :value="__('Catatan')" />
                            <textarea id="notes" name="notes" rows="4"
                                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes', $table->notes) }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>

                        <div class="flex items-center">
                            <input id="is_active" name="is_active" type="checkbox" value="1"
                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                   {{ old('is_active', $table->is_active) ? 'checked' : '' }}>
                            <label for="is_active" class="ms-2 text-sm text-gray-600 dark:text-gray-400">
                                {{ __('Aktifkan meja ini') }}
                            </label>
                        </div>

                        <div class="flex justify-end gap-3">
                            <a href="{{ route('admin.tables.index') }}"
                               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                {{ __('Batal') }}
                            </a>
                            <x-primary-button>
                                {{ __('Simpan Perubahan') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6 space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Informasi QR Code') }}</h3>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">{{ __('Token QR Saat Ini') }}</p>
                        <code class="block text-sm text-gray-900 dark:text-gray-100 bg-gray-100 dark:bg-gray-900 px-3 py-2 rounded">{{ $table->qr_token }}</code>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">{{ __('QR Utama') }}</p>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                {{ __('Gunakan tautan ini untuk QR standar yang mengarah ke katalog menu.') }}
                            </p>
                            <div class="mt-2 flex items-center gap-2">
                                <input type="text" readonly value="{{ $table->order_url }}"
                                       class="flex-1 text-xs rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                <button type="button" onclick="navigator.clipboard.writeText('{{ $table->order_url }}')"
                                        class="px-2 py-1 text-xs text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-600 rounded hover:bg-indigo-50 dark:hover:bg-indigo-600/10">
                                    {{ __('Salin') }}
                                </button>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">{{ __('QR Meja Ini (opsional)') }}</p>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                {{ __('Pakailah jika ingin melacak pesanan per meja dengan slug unik.') }}
                            </p>
                            <div class="mt-2 flex items-center gap-2">
                                <input type="text" readonly value="{{ $table->table_order_url }}"
                                       class="flex-1 text-xs rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                <button type="button" onclick="navigator.clipboard.writeText('{{ $table->table_order_url }}')"
                                        class="px-2 py-1 text-xs text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-600 rounded hover:bg-indigo-50 dark:hover:bg-indigo-600/10">
                                    {{ __('Salin') }}
                                </button>
                            </div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('admin.tables.regenerate-token', $table) }}" onsubmit="return confirm('{{ __('Ganti token QR untuk meja ini? Token lama tidak dapat digunakan.') }}')">
                        @csrf
                        <x-primary-button>
                            {{ __('Generate Token Baru') }}
                        </x-primary-button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
