@php($items = $cart->items)

<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Ringkasan Keranjang') }}</h3>
    @if ($items->isNotEmpty())
        <form method="POST" action="{{ route('cart.clear') }}" data-cart-form class="sm:ml-auto">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-sm font-medium text-rose-600 transition hover:underline dark:text-rose-400">
                {{ __('Kosongkan') }}
            </button>
        </form>
    @endif
</div>

<div class="p-6 space-y-6">
    @if ($items->isEmpty())
        <div class="text-center space-y-3 text-sm text-gray-500 dark:text-gray-400">
            <p>{{ __('Keranjang masih kosong. Kembali ke katalog untuk menambahkan pesanan.') }}</p>
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 rounded-full bg-indigo-600 px-4 py-2 text-xs font-semibold text-white shadow hover:bg-indigo-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                {{ __('Lihat Menu') }}
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($items as $item)
                <div class="flex flex-col gap-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900 sm:flex-row sm:items-start sm:justify-between">
                    <div class="flex items-start gap-4">
                        <img src="{{ $item->menuItem->image_url ?? 'https://via.placeholder.com/120x140?text=Menu' }}"
                             alt="{{ $item->menuItem->name }}"
                             class="h-24 w-24 rounded-lg object-cover object-center">
                        <div class="space-y-1">
                            <h4 class="text-base font-semibold text-gray-900 dark:text-gray-100">
                                {{ $item->menuItem->name }}
                            </h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Rp{{ number_format($item->unit_price, 0, ',', '.') }}
                            </p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">
                                {{ $item->menuItem->category?->name }}
                            </p>
                            @if ($item->notes)
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ __('Catatan: :notes', ['notes' => $item->notes]) }}
                                </p>
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-1 flex-col items-start gap-3 sm:items-end">
                        <form method="POST" action="{{ route('cart.items.update', $item) }}" data-cart-form class="flex w-full flex-wrap items-center gap-2 sm:w-auto sm:flex-nowrap">
                            @csrf
                            @method('PUT')
                            <label for="quantity-{{ $item->id }}" class="sr-only">{{ __('Jumlah') }}</label>
                            <input
                                type="number"
                                min="1"
                                name="quantity"
                                id="quantity-{{ $item->id }}"
                                value="{{ old('quantity', $item->quantity) }}"
                                class="w-24 rounded-lg border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                            >
                            <button type="submit" class="inline-flex items-center justify-center rounded-full bg-indigo-500 px-3 py-2 text-xs font-semibold text-white hover:bg-indigo-400 w-full sm:w-auto">
                                {{ __('Perbarui') }}
                            </button>
                        </form>
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                            {{ __('Subtotal') }}: Rp{{ number_format($item->subtotal, 0, ',', '.') }}
                        </p>
                        <form method="POST" action="{{ route('cart.items.destroy', $item) }}" data-cart-form class="w-full text-right sm:w-auto">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs font-medium text-rose-600 transition hover:underline dark:text-rose-400">
                                {{ __('Hapus') }}
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="border-t border-gray-200 pt-4 dark:border-gray-700">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between text-sm text-gray-600 dark:text-gray-300">
                <span>{{ __('Total Keranjang') }}</span>
                <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    Rp{{ number_format($cart->subtotal, 0, ',', '.') }}
                </span>
            </div>
            <div class="mt-4 rounded-lg bg-indigo-50 px-4 py-3 text-sm text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-200">
                {{ __('Klik tombol "Lanjutkan ke Checkout" untuk memasukkan data kontak dan menyelesaikan pembayaran.') }}
            </div>
        </div>
    @endif
</div>
