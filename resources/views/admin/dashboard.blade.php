<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-2xl text-[#2A1A13] dark:text-gray-100 leading-tight">
                    {{ __('Dashboard Admin') }}
                </h2>
                <span class="inline-flex items-center gap-2 rounded-full bg-[#F5E6D3] px-4 py-1 text-xs font-semibold text-[#8C5A3A] ring-1 ring-[#ECC9A8] mt-1">
                    <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                    {{ now()->translatedFormat('d M Y') }}
                </span>
            </div>
            <div>
                <x-dropdown align="right" width="64">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 rounded-full border border-[#ECC9A8] bg-[#F9F4EB] px-4 py-1.5 text-sm font-semibold text-[#4C2B1C] shadow-sm transition hover:bg-[#F5E6D3]">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-[#e3d0b4] font-semibold text-[#4C2B1C]">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </span>
                            <span>{{ __('Administrator') }}</span>
                            <svg class="h-4 w-4 text-[#4C2B1C]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 9l6 6 6-6" />
                            </svg>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <div class="px-4 py-3 space-y-3">
                            <div>
                                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-[#8C5A3A]">{{ __('Administrator') }}</p>
                                <p class="text-base font-semibold text-[#2A1A13]">{{ auth()->user()->name }}</p>
                                <p class="text-xs uppercase tracking-[0.4em] text-gray-400">{{ strtoupper(auth()->user()->role->value ?? 'ADMIN') }}</p>
                                <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                            </div>
                            <div class="space-y-2">
                                <x-dropdown-link :href="route('dashboard')" class="rounded-full border border-[#1ec16b]/40 bg-[#ebfff6] px-3 py-2 text-sm font-semibold text-[#1ec16b]">
                                    {{ __('Dashboard') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('home')" class="rounded-full border border-gray-200 px-3 py-2 text-sm font-semibold text-gray-700">
                                    {{ __('Kembali ke Toko') }}
                                </x-dropdown-link>
                            </div>
                            <x-dropdown-link :href="route('profile.edit')" class="text-sm text-gray-700">
                                {{ __('Profil') }}
                            </x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" class="text-sm text-red-600" onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Keluar') }}
                                </x-dropdown-link>
                            </form>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </x-slot>

    <div class="bg-[#f7f8fc] dark:bg-[#0f1117]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-8">
            <div class="rounded-3xl border border-gray-200 bg-gradient-to-br from-[#fffdf9] to-[#f1f2f7] px-6 py-8 shadow-lg shadow-amber-100 dark:border-gray-700 dark:from-gray-900/40 dark:to-gray-900/50">
                <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                    <div class="max-w-2xl space-y-3">
                        <p class="text-xs uppercase tracking-[0.5em] text-[#ad9e88]">{{ __('Overview Operasional') }}</p>
                        <h1 class="text-3xl font-bold text-[#1f1a17] dark:text-gray-100">{{ __('Selamat datang kembali, Admin!') }}</h1>
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            {{ __('Pantau menu, pesanan, dan stok secara real-time. Pastikan pengalaman pelanggan tetap optimal.') }}
                        </p>
                        <div class="flex flex-wrap gap-3">
                            <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-semibold text-[#7c6745] shadow-sm">Operational Pulse</span>
                            <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-semibold text-[#7c6745] shadow-sm">{{ now()->translatedFormat('l, d M Y') }}</span>
                        </div>
                    </div>
                    <div class="rounded-2xl border border-[#edd8bf] bg-white/80 p-5 text-right shadow-inner dark:bg-gray-900/60 dark:border-[#2a2c37]">
                        <p class="text-xs uppercase tracking-[0.4em] text-gray-500">{{ __('Total pendapatan hari ini') }}</p>
                        <p class="mt-2 text-3xl font-semibold text-[#1a281a] dark:text-white">
                            Rp{{ number_format($latestOrders->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])->sum('total_amount'), 0, ',', '.') }}
                        </p>
                        <p class="mt-1 text-xs text-gray-500">{{ __('Akumulasi pesanan yang selesai dibayar hari ini.') }}</p>
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap gap-3 overflow-x-auto rounded-[999px] bg-white/60 px-4 py-3 shadow-sm dark:bg-gray-900/40">
                <a href="{{ route('admin.menu-items.index') }}" class="whitespace-nowrap inline-flex items-center gap-2 rounded-full border border-[#ECC9A8] bg-white px-4 py-2 sm:py-2.5 text-sm font-semibold text-[#4C2B1C] shadow-sm hover:bg-[#F5E6D3]">
                    <x-icons.pencil class="w-4 h-4" />
                    {{ __('Kelola Menu') }}
                </a>
                <a href="{{ route('admin.categories.index') }}" class="whitespace-nowrap inline-flex items-center gap-2 rounded-full border border-[#ECC9A8] bg-white px-4 py-2 sm:py-2.5 text-sm font-semibold text-[#4C2B1C] shadow-sm hover:bg-[#F5E6D3]">
                    <x-icons.plus class="w-4 h-4" />
                    {{ __('Kategori') }}
                </a>
                <a href="{{ route('admin.tables.index') }}" class="whitespace-nowrap inline-flex items-center gap-2 rounded-full border border-[#ECC9A8] bg-white px-4 py-2 sm:py-2.5 text-sm font-semibold text-[#4C2B1C] shadow-sm hover:bg-[#F5E6D3]">
                    <x-icons.check class="w-4 h-4" />
                    {{ __('Meja') }}
                </a>
                <a href="{{ route('admin.orders.index') }}" class="whitespace-nowrap inline-flex items-center gap-2 rounded-full bg-emerald-600 px-4 py-2 sm:py-2.5 text-sm font-semibold text-white shadow hover:bg-emerald-500">
                    <x-icons.eye class="w-4 h-4" />
                    {{ __('Pesanan') }}
                </a>
            </div>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-2xl border border-indigo-100 bg-white p-6 shadow-sm dark:border-indigo-500/30 dark:bg-gray-800">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Total Menu') }}</p>
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-200">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3l3.75 7.5h-7.5L12 3zm0 0l3.75 7.5 3.75 7.5H4.5L8.25 10.5 12 3z" />
                            </svg>
                        </span>
                    </div>
                    <p class="mt-4 text-3xl font-semibold text-gray-900 dark:text-white">{{ $menuCount }}</p>
                    <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">{{ __('Menu aktif dalam sistem') }}</p>
                </div>
                <div class="rounded-2xl border border-purple-100 bg-white p-6 shadow-sm dark:border-purple-500/30 dark:bg-gray-800">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Kategori Aktif') }}</p>
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-purple-100 text-purple-600 dark:bg-purple-500/20 dark:text-purple-200">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7h16M4 12h16M4 17h16" />
                            </svg>
                        </span>
                    </div>
                    <p class="mt-4 text-3xl font-semibold text-gray-900 dark:text-white">{{ $categoryCount }}</p>
                    <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">{{ __('Kategori yang dapat dipilih customer') }}</p>
                </div>
                <div class="rounded-2xl border border-emerald-100 bg-white p-6 shadow-sm dark:border-emerald-500/30 dark:bg-gray-800">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Meja Aktif') }}</p>
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-200">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7h18M5 11h14M8 15h8M10 19h4" />
                            </svg>
                        </span>
                    </div>
                    <p class="mt-4 text-3xl font-semibold text-gray-900 dark:text-white">{{ $activeTableCount }}</p>
                    <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">{{ __('Meja yang sedang digunakan customer') }}</p>
                </div>
                <div class="rounded-2xl border border-amber-100 bg-white p-6 shadow-sm dark:border-amber-500/30 dark:bg-gray-800">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Pesanan Pending') }}</p>
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-amber-600 dark:bg-amber-500/20 dark:text-amber-200">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6l3 3" />
                            </svg>
                        </span>
                    </div>
                    <p class="mt-4 text-3xl font-semibold text-gray-900 dark:text-white">{{ $pendingOrdersCount }}</p>
                    <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">{{ __('Pesanan yang menunggu diproses') }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="lg:col-span-2 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-center justify-between border-b border-gray-200 pb-4 dark:border-gray-700">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Pesanan Terbaru') }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Pantau progres pesanan terbaru dari pelanggan.') }}</p>
                        </div>
                        <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-1 text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                            <x-icons.eye class="w-4 h-4" />
                            {{ __('Lihat Semua Pesanan') }}
                        </a>
                    </div>
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                            <thead>
                                <tr class="text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    <th class="px-4 py-3">{{ __('Nomor Pesanan') }}</th>
                                    <th class="px-4 py-3">{{ __('Customer') }}</th>
                                    <th class="px-4 py-3">{{ __('Meja') }}</th>
                                    <th class="px-4 py-3">{{ __('Status') }}</th>
                                    <th class="px-4 py-3 text-right">{{ __('Total') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($latestOrders as $order)
                                    <tr class="transition hover:bg-gray-50 dark:hover:bg-gray-900/50">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $order->code }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $order->user?->name ?? __('Tamu') }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $order->table?->name ?? __('Take Away') }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span @class([
                                                'inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold',
                                                'bg-yellow-100 text-yellow-800' => $order->status === \App\Enums\OrderStatus::Pending,
                                                'bg-blue-100 text-blue-800' => $order->status === \App\Enums\OrderStatus::Preparing,
                                                'bg-green-100 text-green-800' => $order->status === \App\Enums\OrderStatus::Completed,
                                                'bg-gray-100 text-gray-800' => $order->status === \App\Enums\OrderStatus::Served,
                                                'bg-red-100 text-red-800' => $order->status === \App\Enums\OrderStatus::Cancelled,
                                            ])">
                                            {{ $order->status->label() }}
                                        </span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm text-gray-900 dark:text-gray-100">
                                            Rp{{ number_format($order->total_amount, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                            {{ __('Belum ada pesanan terbaru.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-100 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            {{ __('Notifikasi Stok') }}
                            @if ($unreadNotificationCount > 0)
                                <span class="ml-2 inline-flex items-center rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-800">
                                    {{ $unreadNotificationCount }} {{ __('baru') }}
                                </span>
                            @endif
                        </h3>
                        <a href="{{ route('admin.menu-items.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                            {{ __('Kelola Stok') }}
                        </a>
                    </div>
                    <div class="p-6 space-y-4">
                        @forelse ($notifications as $notification)
                            <div @class([
                                'rounded-lg border p-4 text-sm',
                                'border-yellow-200 bg-yellow-50 text-yellow-900' => is_null($notification->read_at),
                                'border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-gray-700' => ! is_null($notification->read_at),
                            ])>
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="font-semibold">{{ data_get($notification->data, 'menu_name') }}</p>
                                        <p class="mt-1">
                                            {{ data_get($notification->data, 'message') }}
                                        </p>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                    <div class="flex flex-col items-end gap-2">
                                        <a href="{{ data_get($notification->data, 'url') }}" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">
                                            {{ __('Lihat Menu') }}
                                        </a>
                                        @if (is_null($notification->read_at))
                                            <form method="POST" action="{{ route('admin.notifications.read', $notification) }}">
                                                @csrf
                                                <button type="submit" class="text-xs text-green-600 dark:text-green-400 hover:underline">
                                                    {{ __('Tandai dibaca') }}
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Belum ada notifikasi stok.') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
