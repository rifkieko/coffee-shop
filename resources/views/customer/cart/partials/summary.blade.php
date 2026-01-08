@php
    $items = $cart->items;
    $groupedItems = $items->groupBy(fn($item) => $item->menu_item_id);
@endphp

<div class="rounded-[24px] border border-[#e0dee3] bg-white/90 p-5 shadow-[0_18px_30px_rgba(15,15,30,0.08)]">
    <h2 class="text-lg font-semibold text-[#1b1b1b]">{{ __('Ringkasan Pesanan') }}</h2>
    @if ($items->isEmpty())
        <p class="mt-3 text-sm text-gray-500">{{ __('Keranjang masih kosong.') }}</p>
    @else
        <div class="mt-4 space-y-3">
            @foreach ($groupedItems as $group)
                @php
                    $quantity = $group->sum('quantity');
                    $unitPrice = $group->first()->unit_price;
                    $lineTotal = $group->sum('subtotal');
                @endphp
                <div class="space-y-1 border-b border-gray-200 pb-3 last:border-b-0 last:pb-0">
                    <p class="text-sm font-semibold text-[#1b1b1b]">{{ $group->first()->menuItem->name }}</p>
                    <p class="text-xs font-semibold text-[#5a544a]">
                        {{ $quantity }} × Rp{{ number_format($unitPrice, 0, ',', '.') }}
                    </p>
                    <p class="text-sm font-semibold text-[#1b1b1b]">
                        Rp{{ number_format($lineTotal, 0, ',', '.') }}
                    </p>
                </div>
            @endforeach
        </div>
        <div class="mt-4 border-t border-gray-200 pt-3">
            <div class="flex items-center justify-between text-sm font-semibold text-[#1b1b1b]">
                <span>{{ __('Subtotal') }}</span>
                <span>Rp{{ number_format($cart->subtotal, 0, ',', '.') }}</span>
            </div>
            <p class="mt-1 text-xs text-gray-500">
                {{ __('Harga belum termasuk biaya layanan pembayaran dari Xendit (jika ada).') }}
            </p>
        </div>
    @endif
</div>
