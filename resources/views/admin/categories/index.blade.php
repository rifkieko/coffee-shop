<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Daftar Kategori Menu') }}
            </h2>
            <a href="{{ route('admin.categories.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('Tambah Kategori') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <x-auth-session-status class="mb-4" :status="session('status')" />
            <x-auth-validation-errors class="mb-4" :errors="$errors" />

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Foto') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Nama') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Deskripsi') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Status') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Aksi') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($categories as $category)
                                    <tr>
                                        <td class="px-4 py-3">
                                            @if ($category->image_path)
                                                <img src="{{ asset('storage/'.$category->image_path) }}" alt="{{ $category->name }}" class="h-12 w-12 rounded-md object-cover">
                                            @else
                                                <div class="flex h-12 w-12 items-center justify-center rounded-md bg-gray-100 text-xs font-semibold text-gray-500 dark:bg-gray-700 dark:text-gray-300">
                                                    {{ strtoupper(mb_substr($category->name, 0, 2)) }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100 font-medium">
                                            {{ $category->name }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $category->description ?: '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                @class([
                                                    'bg-green-100 text-green-800' => $category->is_active,
                                                    'bg-red-100 text-red-800' => ! $category->is_active,
                                                ])">
                                                {{ $category->is_active ? __('Aktif') : __('Tidak Aktif') }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right text-sm">
                                            <a href="{{ route('admin.categories.edit', $category) }}"
                                               class="text-indigo-600 dark:text-indigo-400 hover:underline mr-3">
                                                {{ __('Ubah') }}
                                            </a>
                                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 dark:text-red-400 hover:underline"
                                                    onclick="return confirm('{{ __('Hapus kategori ini?') }}')">
                                                    {{ __('Hapus') }}
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                            {{ __('Belum ada kategori.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $categories->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
