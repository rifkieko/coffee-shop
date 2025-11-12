<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Daftar Menu') }}
            </h2>
            <a href="{{ route('admin.menu-items.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 border border-indigo-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <x-icons.plus class="w-4 h-4" />
                {{ __('Tambah Menu') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <x-auth-session-status class="mb-4" :status="session('status')" />
            <x-auth-validation-errors class="mb-4" :errors="$errors" />

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-xs sm:text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-2 py-2 sm:px-4 sm:py-3 text-left text-[11px] sm:text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Foto') }}</th>
                                    <th class="px-2 py-2 sm:px-4 sm:py-3 text-left text-[11px] sm:text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Menu') }}</th>
                                    <th class="px-2 py-2 sm:px-4 sm:py-3 text-left text-[11px] sm:text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hidden md:table-cell">{{ __('Kategori') }}</th>
                                    <th class="px-2 py-2 sm:px-4 sm:py-3 text-left text-[11px] sm:text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Harga') }}</th>
                                    <th class="px-2 py-2 sm:px-4 sm:py-3 text-left text-[11px] sm:text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Stok') }}</th>
                                    <th class="px-2 py-2 sm:px-4 sm:py-3 text-left text-[11px] sm:text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Status') }}</th>
                                    <th class="px-2 py-2 sm:px-4 sm:py-3 text-right text-[11px] sm:text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">{{ __('Aksi') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($menuItems as $item)
                                    <tr>
                                        <td class="px-4 py-4">
                                            @if ($item->image_path)
                                                <div class="relative w-20 aspect-[4/3] overflow-hidden rounded-md bg-gray-100">
                                                    <img src="{{ asset('storage/'.$item->image_path) }}" alt="{{ $item->name }}" class="absolute inset-0 h-full w-full object-cover">
                                                </div>
                                            @else
                                                <div class="flex items-center justify-center rounded-md bg-gray-100 text-xs font-semibold text-gray-500 dark:bg-gray-700 dark:text-gray-300 w-20 aspect-[4/3]">
                                                    {{ strtoupper(mb_substr($item->name, 0, 2)) }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-2 py-3 sm:px-4 sm:py-4 text-gray-900 dark:text-gray-100">
                                            <p class="font-semibold">{{ $item->name }}</p>
                                            <p class="text-[11px] sm:text-xs text-gray-500 dark:text-gray-400">{{ \Illuminate\Support\Str::limit($item->description, 60) }}</p>
                                        </td>
                                        <td class="px-2 py-3 sm:px-4 sm:py-4 text-gray-500 dark:text-gray-400 hidden md:table-cell">
                                            {{ $item->category?->name ?? '-' }}
                                        </td>
                                        <td class="px-2 py-3 sm:px-4 sm:py-4 text-gray-900 dark:text-gray-100 whitespace-nowrap">
                                            Rp{{ number_format($item->price, 0, ',', '.') }}
                                        </td>
                                        <td class="px-2 py-3 sm:px-4 sm:py-4 text-gray-900 dark:text-gray-100">
                                            <form action="{{ route('admin.menu-items.update-stock', $item) }}" method="POST" class="flex flex-col gap-2">
                                                @csrf
                                                @method('PATCH')
                                                <div class="flex items-center gap-2">
                                                    <input type="number" name="stock" min="0" value="{{ $item->stock }}"
                                                           class="w-20 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                                    <button type="submit"
                                                            class="inline-flex items-center gap-1 px-2 py-1 text-[11px] sm:text-xs font-semibold text-white bg-indigo-600 rounded hover:bg-indigo-500">
                                                        <x-icons.check class="w-3.5 h-3.5" />
                                                        {{ __('Simpan') }}
                                                    </button>
                                                </div>
                                                <div class="text-xs">
                                                    <span @class([
                                                        'text-red-600 dark:text-red-400 font-semibold' => $item->stock <= $item->low_stock_threshold,
                                                        'text-gray-500 dark:text-gray-400' => $item->stock > $item->low_stock_threshold,
                                                    ])>
                                                        {{ __('Stok: :current / Batas: :threshold', ['current' => $item->stock, 'threshold' => $item->low_stock_threshold]) }}
                                                    </span>
                                                    @if ($item->low_stock_notified_at)
                                                        <span class="ml-1 inline-flex items-center rounded-full bg-yellow-100 px-2 py-0.5 text-[10px] font-semibold text-yellow-800">
                                                            {{ __('Sudah diberi tahu') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </form>
                                        </td>
                                        <td class="px-2 py-3 sm:px-4 sm:py-4">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                @class([
                                                    'bg-green-100 text-green-800' => $item->is_active,
                                                    'bg-red-100 text-red-800' => ! $item->is_active,
                                                ])">
                                                {{ $item->is_active ? __('Aktif') : __('Nonaktif') }}
                                            </span>
                                        </td>
                                        <td class="px-2 py-3 sm:px-4 sm:py-4 text-right whitespace-nowrap">
                                            <div class="flex items-center justify-end gap-3">
                                                <a href="{{ route('admin.menu-items.edit', $item) }}"
                                                   class="inline-flex items-center gap-1 px-2 py-1 border border-indigo-200 dark:border-indigo-600 rounded text-indigo-600 dark:text-indigo-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/20" title="{{ __('Ubah') }}">
                                                    <x-icons.pencil class="w-4 h-4" />
                                                    <span class="hidden md:inline">{{ __('Ubah') }}</span>
                                                </a>
                                                <form action="{{ route('admin.menu-items.destroy', $item) }}" method="POST" onsubmit="return confirm('{{ __('Hapus menu ini?') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center gap-1 px-2 py-1 border border-red-200 dark:border-red-600 rounded text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20" title="{{ __('Hapus') }}">
                                                        <x-icons.trash class="w-4 h-4" />
                                                        <span class="hidden md:inline">{{ __('Hapus') }}</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                            {{ __('Belum ada menu.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $menuItems->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <a href="{{ route('admin.menu-items.create') }}"
       class="sm:hidden fixed bottom-20 right-4 inline-flex items-center justify-center w-12 h-12 rounded-full bg-indigo-600 text-white shadow-lg hover:bg-indigo-500">
        <x-icons.plus class="w-5 h-5" />
        <span class="sr-only">{{ __('Tambah Menu') }}</span>
    </a>
</x-app-layout>
