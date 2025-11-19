<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Ubah Menu') }}
            </h2>
            <a href="{{ route('admin.menu-items.index') }}" class="inline-flex items-center gap-1 text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                <x-icons.arrow-left class="w-4 h-4" />
                {{ __('Kembali ke daftar') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.menu-items.update', $menuItem) }}" class="space-y-6" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="name" :value="__('Nama Menu')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                          :value="old('name', $menuItem->name)" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="category_id" :value="__('Kategori')" />
                            <select id="category_id" name="category_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">{{ __('Pilih kategori') }}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected(old('category_id', $menuItem->category_id) == $category->id)>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="price" :value="__('Harga (Rp)')" />
                                <x-text-input id="price" name="price" type="number" step="100" min="0"
                                              class="mt-1 block w-full" :value="old('price', $menuItem->price)" required />
                                <x-input-error :messages="$errors->get('price')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="stock" :value="__('Stok')" />
                                <x-text-input id="stock" name="stock" type="number" min="0"
                                              class="mt-1 block w-full" :value="old('stock', $menuItem->stock)" required />
                                <x-input-error :messages="$errors->get('stock')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="low_stock_threshold" :value="__('Batas Notifikasi Stok')" />
                                <x-text-input id="low_stock_threshold" name="low_stock_threshold" type="number" min="0"
                                              class="mt-1 block w-full" :value="old('low_stock_threshold', $menuItem->low_stock_threshold)" />
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    {{ __('Notifikasi dikirim saat stok berada di bawah atau sama dengan angka ini.') }}
                                </p>
                                <x-input-error :messages="$errors->get('low_stock_threshold')" class="mt-2" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('Deskripsi')" />
                            <textarea id="description" name="description" rows="4"
                                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $menuItem->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        @php
                            $initialImage = $menuItem->image_path
                                ? asset('storage/'.$menuItem->image_path)
                                : 'https://via.placeholder.com/400x400?text=Menu';
                        @endphp
                        <div class="space-y-3">
                            <x-input-label for="image" :value="__('Foto Menu')" />
                            <div class="relative h-32 overflow-hidden rounded-lg border border-dashed border-gray-200 bg-gray-50">
                                <img
                                    id="image-preview"
                                    data-original-src="{{ $initialImage }}"
                                    src="{{ $initialImage }}"
                                    alt="{{ $menuItem->name }}"
                                    class="h-full w-full object-contain"
                                >
                            </div>
                            <input id="image" name="image" type="file" accept="image/*"
                                   class="mt-1 block w-full text-sm text-gray-600 dark:text-gray-300 file:mr-4 file:rounded-md file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-500/20 dark:file:text-indigo-200" />
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Unggah gambar baru (opsional). Maks 4MB.') }}</p>
                            <x-input-error :messages="$errors->get('image')" class="mt-2" />
                        </div>

                        <div class="flex items-center">
                            <input id="is_active" name="is_active" type="checkbox" value="1"
                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                   {{ old('is_active', $menuItem->is_active) ? 'checked' : '' }}>
                            <label for="is_active" class="ms-2 text-sm text-gray-600 dark:text-gray-400">
                                {{ __('Aktifkan menu ini') }}
                            </label>
                        </div>

                        <div class="flex justify-end gap-3">
                            <a href="{{ route('admin.menu-items.index') }}"
                               class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <x-icons.x-mark class="w-4 h-4" />
                                {{ __('Batal') }}
                            </a>
                            <x-primary-button>
                                <x-icons.check class="w-4 h-4 mr-2" />
                                {{ __('Simpan Perubahan') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        @include('admin.menu-items.partials.image-preview-script')
    @endpush
</x-app-layout>
