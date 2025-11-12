<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Ubah Kategori') }}
            </h2>
            <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center gap-1 text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                <x-icons.arrow-left class="w-4 h-4" />
                {{ __('Kembali ke daftar') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="space-y-6" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="name" :value="__('Nama Kategori')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                          :value="old('name', $category->name)" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('Deskripsi')" />
                            <textarea id="description" name="description" rows="4"
                                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $category->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="space-y-3">
                            <x-input-label for="image" :value="__('Foto Kategori')" />
                            @if ($category->image_path)
                                <img src="{{ asset('storage/'.$category->image_path) }}" alt="{{ $category->name }}" class="h-32 w-full rounded-lg object-contain bg-gray-100">
                            @endif
                            <input id="image" name="image" type="file" accept="image/*"
                                   class="mt-1 block w-full text-sm text-gray-600 dark:text-gray-300 file:mr-4 file:rounded-md file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-500/20 dark:file:text-indigo-200" />
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Unggah gambar baru (opsional). Maks 2MB.') }}</p>
                            <x-input-error :messages="$errors->get('image')" class="mt-2" />
                        </div>

                        <div class="flex items-center">
                            <input id="is_active" name="is_active" type="checkbox" value="1"
                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                   {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                            <label for="is_active" class="ms-2 text-sm text-gray-600 dark:text-gray-400">
                                {{ __('Aktifkan kategori ini') }}
                            </label>
                        </div>

                        <div class="flex justify-end gap-3">
                            <a href="{{ route('admin.categories.index') }}"
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
</x-app-layout>
