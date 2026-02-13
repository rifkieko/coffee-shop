<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Daftar Pesanan') }}
            </h2>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:flex-wrap">
                <form method="GET" action="{{ route('admin.orders.index') }}" class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center sm:gap-3">
                    <div class="w-full sm:w-auto">
                        <label for="status" class="sr-only">{{ __('Status') }}</label>
                        <select id="status" name="status"
                                class="w-full rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 sm:w-auto">
                            <option value="">{{ __('Semua Status') }}</option>
                            @foreach (\App\Enums\OrderStatus::cases() as $case)
                                <option value="{{ $case->value }}" @selected(request('status') === $case->value)>
                                    {{ $case->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-full sm:w-auto">
                        <label for="payment_status" class="sr-only">{{ __('Status Pembayaran') }}</label>
                        <select id="payment_status" name="payment_status"
                                class="w-full rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 sm:w-auto">
                            <option value="">{{ __('Semua Pembayaran') }}</option>
                            @foreach (\App\Enums\PaymentStatus::cases() as $case)
                                <option value="{{ $case->value }}" @selected(request('payment_status') === $case->value)>
                                    {{ $case->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <x-primary-button class="w-full justify-center sm:w-auto">
                        <x-icons.funnel class="w-4 h-4 mr-2" />
                        {{ __('Filter') }}
                    </x-primary-button>
                </form>
                <a href="{{ route('admin.reports.sales') }}" class="inline-flex w-full items-center justify-center rounded-md border border-indigo-200 bg-indigo-50 px-4 py-2 text-sm font-semibold text-indigo-700 shadow-sm hover:bg-indigo-100 sm:w-auto">
                    {{ __('Rekap & Unduh') }}
                </a>
                <button type="button" id="order-sound-toggle" class="inline-flex w-full items-center justify-center gap-2 rounded-md border px-4 py-2 text-xs font-semibold shadow-sm transition sm:w-auto" aria-pressed="false">
                    <span id="order-sound-indicator" class="h-2 w-2 rounded-full bg-gray-300"></span>
                    <span id="order-sound-label">{{ __('Suara: Mati') }}</span>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                @include('admin.orders.partials.list', ['orders' => $orders])
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            (() => {
                const refreshMs = 1000;
                let timer = null;
                const selector = '[data-orders-partial]';
                const soundToggle = document.getElementById('order-sound-toggle');
                const soundLabel = document.getElementById('order-sound-label');
                const soundIndicator = document.getElementById('order-sound-indicator');
                const soundOnClasses = ['border-emerald-200', 'bg-emerald-50', 'text-emerald-700'];
                const soundOffClasses = ['border-gray-200', 'bg-white', 'text-gray-500'];
                const soundStorageKey = 'admin-orders-sound-enabled';
                let soundEnabled = localStorage.getItem(soundStorageKey) === '1';
                const notificationAudio = new Audio('/order.mp3');
                notificationAudio.preload = 'auto';
                let needsGesture = false;

                const setSoundUI = (enabled) => {
                    if (!soundToggle || !soundLabel || !soundIndicator) return;
                    soundToggle.setAttribute('aria-pressed', enabled ? 'true' : 'false');
                    soundLabel.textContent = enabled ? 'Suara: Aktif' : 'Suara: Mati';
                    soundIndicator.classList.toggle('bg-emerald-500', enabled);
                    soundIndicator.classList.toggle('bg-gray-300', !enabled);
                    soundOnClasses.forEach((cls) => soundToggle.classList.toggle(cls, enabled));
                    soundOffClasses.forEach((cls) => soundToggle.classList.toggle(cls, !enabled));
                };

                const parseOrderIds = (value) => {
                    if (!value) return new Set();
                    return new Set(
                        value
                            .split(',')
                            .map((item) => Number.parseInt(item, 10))
                            .filter((item) => Number.isFinite(item))
                    );
                };

                const initialDataset = document.querySelector(selector)?.dataset;
                let previousOrderIds = parseOrderIds(initialDataset?.orderIds);
                let previousUnpaidIds = parseOrderIds(initialDataset?.unpaidOrderIds);
                const alertingIds = new Set();
                let alertPlaying = false;

                const requestGestureUnlock = () => {
                    if (needsGesture || !soundEnabled) return;
                    needsGesture = true;
                    document.addEventListener('click', () => {
                        needsGesture = false;
                        if (!soundEnabled) return;
                        if (alertingIds.size > 0) {
                            startAlert();
                        } else {
                            playOnce();
                        }
                    }, { once: true, capture: true });
                };

                const playOnce = () => {
                    if (!soundEnabled || alertPlaying) return;
                    notificationAudio.loop = false;
                    notificationAudio.currentTime = 0;
                    notificationAudio.play().catch(() => {
                        // Ignore autoplay restriction errors.
                        requestGestureUnlock();
                    });
                };

                const startAlert = () => {
                    if (!soundEnabled || alertPlaying) return;
                    alertPlaying = true;
                    notificationAudio.loop = true;
                    notificationAudio.currentTime = 0;
                    notificationAudio.play().catch(() => {
                        alertPlaying = false;
                        notificationAudio.loop = false;
                        requestGestureUnlock();
                    });
                };

                const stopAlert = () => {
                    if (!alertPlaying) return;
                    notificationAudio.pause();
                    notificationAudio.currentTime = 0;
                    notificationAudio.loop = false;
                    alertPlaying = false;
                };

                const syncAlerting = (nextOrderIds, nextUnpaidIds) => {
                    const newOrderIds = Array.from(nextOrderIds).filter((id) => !previousOrderIds.has(id));
                    let shouldPlayOnce = false;
                    newOrderIds.forEach((id) => {
                        if (nextUnpaidIds.has(id)) {
                            alertingIds.add(id);
                        } else {
                            shouldPlayOnce = true;
                        }
                    });

                    for (const id of Array.from(alertingIds)) {
                        if (!nextUnpaidIds.has(id)) {
                            alertingIds.delete(id);
                        }
                    }

                    if (alertingIds.size > 0) {
                        startAlert();
                    } else {
                        stopAlert();
                        if (shouldPlayOnce) {
                            playOnce();
                        }
                    }
                };

                const enableSound = async () => {
                    soundEnabled = true;
                    localStorage.setItem(soundStorageKey, '1');
                    setSoundUI(true);
                    if (alertingIds.size > 0) {
                        startAlert();
                    } else {
                        playOnce();
                    }
                };

                const disableSound = () => {
                    soundEnabled = false;
                    localStorage.removeItem(soundStorageKey);
                    setSoundUI(false);
                    stopAlert();
                };

                setSoundUI(soundEnabled);
                if (soundEnabled) {
                    requestGestureUnlock();
                }
                if (soundToggle) {
                    soundToggle.addEventListener('click', async () => {
                        if (soundEnabled) {
                            disableSound();
                            return;
                        }
                        await enableSound();
                    });
                }

                const buildUrl = () => {
                    const url = new URL(window.location.href);
                    url.searchParams.set('partial', '1');
                    return url.toString();
                };

                const refresh = async () => {
                    if (document.hidden) return;
                    const current = document.querySelector(selector);
                    if (!current) return;
                    try {
                        const response = await fetch(buildUrl(), {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' },
                            cache: 'no-store',
                        });
                        if (!response.ok) return;
                        const html = await response.text();
                        const doc = new DOMParser().parseFromString(html, 'text/html');
                        const next = doc.querySelector(selector);
                        if (!next) return;
                        const nextOrderIds = parseOrderIds(next.dataset.orderIds);
                        const nextUnpaidIds = parseOrderIds(next.dataset.unpaidOrderIds);
                        syncAlerting(nextOrderIds, nextUnpaidIds);
                        previousOrderIds = nextOrderIds;
                        previousUnpaidIds = nextUnpaidIds;
                        if (next.innerHTML.trim() === current.innerHTML.trim()) return;
                        current.replaceWith(next);
                    } catch (error) {
                        // Ignore polling errors to avoid breaking the page.
                    }
                };

                const start = () => {
                    if (timer) return;
                    timer = setInterval(refresh, refreshMs);
                };

                const stop = () => {
                    if (!timer) return;
                    clearInterval(timer);
                    timer = null;
                };

                document.addEventListener('visibilitychange', () => {
                    if (document.hidden) {
                        stop();
                    } else {
                        start();
                    }
                });

                refresh();
                start();
            })();
        </script>
    @endpush
</x-app-layout>
