@php
    $items = $cart->items;
@endphp

@if ($items->isEmpty())
    <div class="text-center text-sm text-gray-500">
        {{ __('Keranjang masih kosong. Tambahkan menu dulu.') }}
    </div>
@else
        <div class="space-y-4">
            @foreach ($items as $item)
            <article class="space-y-3 rounded-[24px] border border-[#e0dee3] bg-white/90 p-4 shadow-[0_10px_30px_rgba(15,15,40,0.05)]" data-note-row>
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-base font-semibold uppercase tracking-[0.35em] text-[#1b1b1b]">{{ $item->menuItem->name }}</h3>
                        <p class="mt-1 flex items-center gap-2 text-sm font-medium text-[#5a544a]">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M6 2h8l5 5v11a2 2 0 01-2 2H6a2 2 0 01-2-2V4a2 2 0 012-2z" fill="#000"/>
                            <path d="M13 2v5h5" fill="#000"/>
                            <path d="M8 11h5" stroke="#fff" stroke-width="1.5" stroke-linecap="round"/>
                            <path d="M8 14.5h5" stroke="#fff" stroke-width="1.5" stroke-linecap="round"/>
                            <path d="M8 18h5" stroke="#fff" stroke-width="1.5" stroke-linecap="round"/>
                            <path d="M17 17l3-3 1.5 1.5-3 3z" fill="#000"/>
                            <path d="M16 16l3-3" stroke="#fff" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                            {{ $item->notes ?: __('Belum menambah catatan') }}
                        </p>
                    </div>
                    <button type="button" data-note-toggle aria-label="{{ __('Ubah') }}" class="inline-flex items-center gap-2 rounded-full border border-[#e4e6f0] bg-white px-3 py-1 text-sm font-semibold text-[#2a3c4f] transition hover:border-[#cfd2df] hover:bg-[#f5f6fb]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24">
                            <path d="M6 2h8l5 5v11a2 2 0 01-2 2H6a2 2 0 01-2-2V4a2 2 0 012-2z" fill="#000"/>
                            <path d="M14 2v5h5" fill="#000"/>
                            <path d="M8 11h5" stroke="#fff" stroke-width="1.5" stroke-linecap="round"/>
                            <path d="M8 14.5h5" stroke="#fff" stroke-width="1.5" stroke-linecap="round"/>
                            <path d="M8 18h5" stroke="#fff" stroke-width="1.5" stroke-linecap="round"/>
                            <path d="M17 17l3-3 1.5 1.5-3 3z" fill="#000"/>
                            <path d="M16 16l3-3" stroke="#fff" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                        <span>{{ __('Edit') }}</span>
                    </button>
                </div>

                <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-lg font-semibold text-[#1b1b1b]">Rp{{ number_format($item->unit_price, 0, ',', '.') }}</p>
                    <div class="flex w-full flex-wrap items-center gap-2 sm:w-auto sm:justify-end sm:ml-auto">
                        <form method="POST" action="{{ route('cart.items.update', $item) }}" data-cart-form class="flex items-center justify-center gap-1 rounded-full border border-[#dededf] bg-white px-2 py-0.5 text-xs font-semibold text-gray-600 shadow-[0_2px_6px_rgba(15,15,30,0.07)]">
                            @csrf
                            @method('PUT')
                            <label for="quantity-{{ $item->id }}" class="sr-only">{{ __('Jumlah') }}</label>
                            <button type="button" data-quantity-adjust="-1" class="h-6 w-6 rounded-full border border-[#c79c65] text-base font-semibold text-[#c79c65] transition hover:bg-[#f4e7d8]">−</button>
                            <span class="min-w-[1.5rem] text-lg font-semibold text-[#2a1b14] text-center" data-quantity-display>{{ $item->quantity }}</span>
                            <button type="button" data-quantity-adjust="1" class="h-6 w-6 rounded-full border border-[#c79c65] text-base font-semibold text-[#c79c65] transition hover:bg-[#f4e7d8]">+</button>
                            <input
                                type="number"
                                min="1"
                                name="quantity"
                                id="quantity-{{ $item->id }}"
                                value="{{ old('quantity', $item->quantity) }}"
                                class="sr-only"
                            >
                        </form>
                        <form method="POST" action="{{ route('cart.items.destroy', $item) }}" data-cart-form>
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex h-[2.5rem] items-center gap-2 rounded-full border border-[#dc2626] bg-[#dc2626] px-4 text-xs font-semibold uppercase tracking-[0.3em] text-white transition hover:border-[#a01717] hover:bg-[#b91c1c]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 6h18" />
                                    <path d="M8 6v12a2 2 0 002 2h4a2 2 0 002-2V6" />
                                    <path d="M10 11v6" />
                                    <path d="M14 11v6" />
                                    <path d="M5 6l1-3h12l1 3" />
                                </svg>
                                {{ __('Hapus') }}
                            </button>
                        </form>
                    </div>
                </div>

                <form method="POST" action="{{ route('cart.items.update', $item) }}" data-cart-form class="hidden flex-col gap-2 rounded-[18px] border border-[#dfe2f0] bg-[#fdfbf9] p-3" data-note-panel>
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="quantity" value="{{ $item->quantity }}" data-note-quantity>
                    <label for="notes-{{ $item->id }}" class="sr-only">{{ __('Catatan') }}</label>
                    <textarea
                        id="notes-{{ $item->id }}"
                        name="notes"
                        rows="2"
                        class="w-full resize-none rounded-lg border border-[#d0c5b4] bg-white px-3 py-2 text-sm text-[#4a413a] focus:border-[#c79c65] focus:outline-none"
                        placeholder="{{ __('Berikan catatan khusus untuk menu ini...') }}"
                    >{{ old('notes', $item->notes) }}</textarea>
                    <button type="submit" class="inline-flex items-center justify-center rounded-full border border-[#c79c65] px-4 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-[#c79c65] transition hover:border-[#a26132]">
                        {{ __('Simpan Catatan') }}
                    </button>
                </form>
            </article>
        @endforeach
    </div>
@endif
