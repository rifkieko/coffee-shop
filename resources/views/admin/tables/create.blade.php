<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Tambah Meja') }}
            </h2>
            <a href="{{ route('admin.tables.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                {{ __('Kembali ke daftar') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.tables.store') }}" class="space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="name" :value="__('Nama Meja')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                          :value="old('name')" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="capacity" :value="__('Kapasitas (orang)')" />
                            <x-text-input id="capacity" name="capacity" type="number" min="1" max="20"
                                          class="mt-1 block w-full" :value="old('capacity', 2)" required />
                            <x-input-error :messages="$errors->get('capacity')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="notes" :value="__('Catatan')" />
                            <textarea id="notes" name="notes" rows="4"
                                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>

                        <div class="flex items-center">
                            <input id="is_active" name="is_active" type="checkbox" value="1"
                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                   {{ old('is_active', true) ? 'checked' : '' }}>
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
                                {{ __('Simpan') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
