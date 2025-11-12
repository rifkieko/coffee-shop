<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Daftar Meja') }}
            </h2>
            <a href="{{ route('admin.tables.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 border border-indigo-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <x-icons.plus class="w-4 h-4" />
                {{ __('Tambah Meja') }}
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
                                    <th class="px-2 py-2 sm:px-4 sm:py-3 text-left text-[11px] sm:text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Nama') }}</th>
                                    <th class="px-2 py-2 sm:px-4 sm:py-3 text-left text-[11px] sm:text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Kode') }}</th>
                                    <th class="px-2 py-2 sm:px-4 sm:py-3 text-left text-[11px] sm:text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hidden md:table-cell">{{ __('Kapasitas') }}</th>
                                    <th class="px-2 py-2 sm:px-4 sm:py-3 text-left text-[11px] sm:text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Status') }}</th>
                                    <th class="px-2 py-2 sm:px-4 sm:py-3 text-left text-[11px] sm:text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hidden lg:table-cell">{{ __('Link QR') }}</th>
                                    <th class="px-2 py-2 sm:px-4 sm:py-3 text-right text-[11px] sm:text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">{{ __('Aksi') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($tables as $table)
                                    <tr>
                                        <td class="px-2 py-3 sm:px-4 sm:py-4 text-gray-900 dark:text-gray-100 font-semibold">
                                            {{ $table->name }}
                                        </td>
                                        <td class="px-2 py-3 sm:px-4 sm:py-4 text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                            {{ $table->code }}
                                        </td>
                                        <td class="px-2 py-3 sm:px-4 sm:py-4 text-gray-500 dark:text-gray-400 hidden md:table-cell">
                                            {{ $table->capacity }} {{ __('orang') }}
                                        </td>
                                        <td class="px-2 py-3 sm:px-4 sm:py-4">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                @class([
                                                    'bg-green-100 text-green-800' => $table->is_active,
                                                    'bg-red-100 text-red-800' => ! $table->is_active,
                                                ])">
                                                {{ $table->is_active ? __('Aktif') : __('Nonaktif') }}
                                            </span>
                                        </td>
                                        <td class="px-2 py-3 sm:px-4 sm:py-4 text-gray-500 dark:text-gray-400 hidden lg:table-cell">
                                            <div class="space-y-3">
                                                <div>
                                                    <p class="text-xs font-semibold uppercase text-gray-600 dark:text-gray-300 tracking-wide">
                                                        {{ __('QR Utama') }}
                                                    </p>
                                                    <div class="mt-1 flex items-center gap-2">
                                                        <input type="text" readonly value="{{ $table->order_url }}"
                                                               class="flex-1 text-xs rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                                        <button type="button" onclick="navigator.clipboard.writeText('{{ $table->order_url }}')"
                                                                class="px-2 py-1 text-xs text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-600 rounded hover:bg-indigo-50 dark:hover:bg-indigo-600/10">
                                                            {{ __('Salin') }}
                                                        </button>
                                                    </div>
                                                </div>
                                                <div>
                                                    <p class="text-xs font-semibold uppercase text-gray-600 dark:text-gray-300 tracking-wide">
                                                        {{ __('QR Meja Ini (opsional)') }}
                                                    </p>
                                                    <div class="mt-1 flex items-center gap-2">
                                                        <input type="text" readonly value="{{ $table->table_order_url }}"
                                                               class="flex-1 text-xs rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                                        <button type="button" onclick="navigator.clipboard.writeText('{{ $table->table_order_url }}')"
                                                                class="px-2 py-1 text-xs text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-600 rounded hover:bg-indigo-50 dark:hover:bg-indigo-600/10">
                                                            {{ __('Salin') }}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-2 py-3 sm:px-4 sm:py-4 text-right whitespace-nowrap">
                                            <div class="flex items-center justify-end gap-3">
                                                <a href="{{ route('admin.tables.edit', $table) }}"
                                                   class="inline-flex items-center gap-1 px-2 py-1 border border-indigo-200 dark:border-indigo-600 rounded text-indigo-600 dark:text-indigo-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/20" title="{{ __('Ubah') }}">
                                                    <x-icons.pencil class="w-4 h-4" />
                                                    <span class="hidden md:inline">{{ __('Ubah') }}</span>
                                                </a>
                                                <form action="{{ route('admin.tables.regenerate-token', $table) }}" method="POST"
                                                      onsubmit="return confirm('{{ __('Ganti token QR untuk meja ini?') }}')">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center gap-1 text-yellow-700 dark:text-yellow-400 border border-yellow-300 dark:border-yellow-500 rounded px-2 py-1 hover:bg-yellow-50 dark:hover:bg-yellow-900/20" title="{{ __('Token Baru') }}">
                                                        <x-icons.refresh class="w-4 h-4" />
                                                        <span class="hidden md:inline">{{ __('Token Baru') }}</span>
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.tables.destroy', $table) }}" method="POST"
                                                      onsubmit="return confirm('{{ __('Hapus meja ini?') }}')">
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
                                        <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                            {{ __('Belum ada data meja.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $tables->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <a href="{{ route('admin.tables.create') }}"
       class="sm:hidden fixed bottom-20 right-4 inline-flex items-center justify-center w-12 h-12 rounded-full bg-indigo-600 text-white shadow-lg hover:bg-indigo-500">
        <x-icons.plus class="w-5 h-5" />
        <span class="sr-only">{{ __('Tambah Meja') }}</span>
    </a>
</x-app-layout>
