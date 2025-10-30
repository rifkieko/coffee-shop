<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Daftar Meja') }}
            </h2>
            <a href="{{ route('admin.tables.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
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
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Nama') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Kode') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Kapasitas') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Status') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Link QR') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Aksi') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($tables as $table)
                                    <tr>
                                        <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100 font-semibold">
                                            {{ $table->name }}
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $table->code }}
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $table->capacity }} {{ __('orang') }}
                                        </td>
                                        <td class="px-4 py-4 text-sm">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                @class([
                                                    'bg-green-100 text-green-800' => $table->is_active,
                                                    'bg-red-100 text-red-800' => ! $table->is_active,
                                                ])">
                                                {{ $table->is_active ? __('Aktif') : __('Nonaktif') }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-500 dark:text-gray-400">
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
                                        <td class="px-4 py-4 text-right text-sm">
                                            <div class="flex items-center justify-end gap-3">
                                                <a href="{{ route('admin.tables.edit', $table) }}"
                                                   class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                                    {{ __('Ubah') }}
                                                </a>
                                                <form action="{{ route('admin.tables.regenerate-token', $table) }}" method="POST"
                                                      onsubmit="return confirm('{{ __('Ganti token QR untuk meja ini?') }}')">
                                                    @csrf
                                                    <button type="submit" class="text-yellow-600 dark:text-yellow-400 hover:underline">
                                                        {{ __('Token Baru') }}
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.tables.destroy', $table) }}" method="POST"
                                                      onsubmit="return confirm('{{ __('Hapus meja ini?') }}')">
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
</x-app-layout>
