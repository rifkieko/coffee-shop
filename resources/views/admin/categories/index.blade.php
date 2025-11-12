<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Daftar Kategori Menu') }}
            </h2>
            <a href="{{ route('admin.categories.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 border border-indigo-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <x-icons.plus class="w-4 h-4" />
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
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-xs sm:text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-2 py-2 sm:px-4 sm:py-3 text-left text-[11px] sm:text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Foto') }}</th>
                                    <th class="px-2 py-2 sm:px-4 sm:py-3 text-left text-[11px] sm:text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Nama') }}</th>
                                    <th class="px-2 py-2 sm:px-4 sm:py-3 text-left text-[11px] sm:text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hidden md:table-cell">{{ __('Deskripsi') }}</th>
                                    <th class="px-2 py-2 sm:px-4 sm:py-3 text-left text-[11px] sm:text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Status') }}</th>
                                    <th class="px-2 py-2 sm:px-4 sm:py-3 text-right text-[11px] sm:text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">{{ __('Aksi') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($categories as $category)
                                    <tr>
                                        <td class="px-2 py-2 sm:px-4 sm:py-3">
                                            @if ($category->image_path)
                                                <div class="relative w-16 aspect-[4/3] overflow-hidden rounded-md bg-gray-100">
                                                    <img src="{{ asset('storage/'.$category->image_path) }}" alt="{{ $category->name }}" class="absolute inset-0 h-full w-full object-cover">
                                                </div>
                                            @else
                                                <div class="flex items-center justify-center rounded-md bg-gray-100 text-xs font-semibold text-gray-500 dark:bg-gray-700 dark:text-gray-300 w-16 aspect-[4/3]">
                                                    {{ strtoupper(mb_substr($category->name, 0, 2)) }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-2 py-2 sm:px-4 sm:py-3 text-gray-900 dark:text-gray-100 font-medium">
                                            {{ $category->name }}
                                        </td>
                                        <td class="px-2 py-2 sm:px-4 sm:py-3 text-gray-500 dark:text-gray-400 hidden md:table-cell">
                                            {{ $category->description ?: '-' }}
                                        </td>
                                        <td class="px-2 py-2 sm:px-4 sm:py-3">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                @class([
                                                    'bg-green-100 text-green-800' => $category->is_active,
                                                    'bg-red-100 text-red-800' => ! $category->is_active,
                                                ])">
                                                {{ $category->is_active ? __('Aktif') : __('Tidak Aktif') }}
                                            </span>
                                        </td>
                                        <td class="px-2 py-2 sm:px-4 sm:py-3 text-right whitespace-nowrap">
                                            <a href="{{ route('admin.categories.edit', $category) }}"
                                               class="inline-flex items-center gap-1 mr-2 sm:mr-3 px-2 py-1 border border-indigo-200 dark:border-indigo-600 rounded text-indigo-600 dark:text-indigo-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/20"
                                               title="{{ __('Ubah') }}">
                                                <x-icons.pencil class="w-4 h-4" />
                                                <span class="hidden md:inline">{{ __('Ubah') }}</span>
                                            </a>
                                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center gap-1 px-2 py-1 border border-red-200 dark:border-red-600 rounded text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20" title="{{ __('Hapus') }}"
                                                    onclick="return confirm('{{ __('Hapus kategori ini?') }}')">
                                                    <x-icons.trash class="w-4 h-4" />
                                                    <span class="hidden md:inline">{{ __('Hapus') }}</span>
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
    <a href="{{ route('admin.categories.create') }}"
       class="sm:hidden fixed bottom-20 right-4 inline-flex items-center justify-center w-12 h-12 rounded-full bg-indigo-600 text-white shadow-lg hover:bg-indigo-500">
        <x-icons.plus class="w-5 h-5" />
        <span class="sr-only">{{ __('Tambah Kategori') }}</span>
    </a>
</x-app-layout>
