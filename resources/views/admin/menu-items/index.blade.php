<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Daftar Menu') }}
            </h2>
            <a href="{{ route('admin.menu-items.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
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
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Foto') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Menu') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Kategori') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Harga') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Stok') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Status') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Aksi') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($menuItems as $item)
                                    <tr>
                                        <td class="px-4 py-4">
                                            @if ($item->image_path)
                                                <img src="{{ asset('storage/'.$item->image_path) }}" alt="{{ $item->name }}" class="h-16 w-16 rounded-md object-cover">
                                            @else
                                                <div class="flex h-16 w-16 items-center justify-center rounded-md bg-gray-100 text-xs font-semibold text-gray-500 dark:bg-gray-700 dark:text-gray-300">
                                                    {{ strtoupper(mb_substr($item->name, 0, 2)) }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100">
                                            <p class="font-semibold">{{ $item->name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ \Illuminate\Support\Str::limit($item->description, 60) }}</p>
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $item->category?->name ?? '-' }}
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100">
                                            Rp{{ number_format($item->price, 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100">
                                            <form action="{{ route('admin.menu-items.update-stock', $item) }}" method="POST" class="flex flex-col gap-2">
                                                @csrf
                                                @method('PATCH')
                                                <div class="flex items-center gap-2">
                                                    <input type="number" name="stock" min="0" value="{{ $item->stock }}"
                                                           class="w-20 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                                    <button type="submit"
                                                            class="px-2 py-1 text-xs font-semibold text-white bg-indigo-600 rounded hover:bg-indigo-500">
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
                                        <td class="px-4 py-4 text-sm">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                @class([
                                                    'bg-green-100 text-green-800' => $item->is_active,
                                                    'bg-red-100 text-red-800' => ! $item->is_active,
                                                ])">
                                                {{ $item->is_active ? __('Aktif') : __('Nonaktif') }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-right text-sm">
                                            <div class="flex items-center justify-end gap-3">
                                                <a href="{{ route('admin.menu-items.edit', $item) }}"
                                                   class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                                    {{ __('Ubah') }}
                                                </a>
                                                <form action="{{ route('admin.menu-items.destroy', $item) }}" method="POST" onsubmit="return confirm('{{ __('Hapus menu ini?') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">
                                                        {{ __('Hapus') }}
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
</x-app-layout>
