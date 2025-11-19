@push('scripts')
<script>
    (function () {
        // Debounce util for auto-submit
            const debounce = (fn, wait = 400) => {
                let t;
                return (...args) => {
                    clearTimeout(t);
                    t = setTimeout(() => fn.apply(null, args), wait);
                };
            };
            const notifyCartUpdate = (subtotal) => {
                try {
                    localStorage.setItem('palas_cart_subtotal', subtotal.toString());
                    window.dispatchEvent(new CustomEvent('cart:changed', { detail: { subtotal } }));
                } catch (_) { /* ignore */ }
            };
            const formatRupiah = (n) => {
                try { return 'Rp' + new Intl.NumberFormat('id-ID').format(n || 0); }
                catch (_) { return 'Rp' + (n || 0); }
            };

        const resolveCartTarget = () => {
            const candidates = document.querySelectorAll('[data-cart-indicator]');
            for (const element of candidates) {
                const rect = element.getBoundingClientRect();
                if (rect.width > 0 && rect.height > 0) {
                    return { element, rect };
                }
            }
            return null;
        };

        const getToastStack = () => {
            let stack = document.querySelector('.cart-toast-stack');
            if (!stack) {
                stack = document.createElement('div');
                stack.className = 'cart-toast-stack';
                document.body.appendChild(stack);
            }
            return stack;
        };

        const showCartToast = (message, variant = 'success') => {
            if (!message) {
                return;
            }

            const stack = getToastStack();
            const toast = document.createElement('div');
            toast.className = 'cart-toast';
            if (variant === 'error') {
                toast.classList.add('cart-toast--error');
            }
            toast.textContent = message;
            stack.appendChild(toast);

            window.requestAnimationFrame(() => {
                toast.classList.add('is-visible');
            });

            const toastTimeout = 3000;
            window.setTimeout(() => {
                toast.classList.remove('is-visible');
                toast.addEventListener('transitionend', () => toast.remove(), { once: true });
            }, toastTimeout);
            // Ensure mini cart bar is visible after add
            if (variant !== 'error') {
                const bar = document.getElementById('mini-cart-bar');
                if (bar) bar.classList.remove('hidden');
            }
        };

        const triggerCartAnimation = (button) => {
            const target = resolveCartTarget();
            if (!target) {
                return;
            }

            const { element: cartTarget, rect: targetRect } = target;
            const sourceRect = (button || cartTarget).getBoundingClientRect();

            const startX = sourceRect.left + sourceRect.width / 2;
            const startY = sourceRect.top + sourceRect.height / 2;
            const endX = targetRect.left + targetRect.width / 2;
            const endY = targetRect.top + targetRect.height / 2;

            const bubble = document.createElement('div');
            bubble.className = 'cart-flyout';
            bubble.innerHTML = '<svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M7.667 14.5a.75.75 0 01-.53-.22l-3.417-3.417a.75.75 0 011.06-1.06l2.887 2.886 6.553-6.553a.75.75 0 011.06 1.06l-7.083 7.084a.75.75 0 01-.53.22z"/></svg>';
            bubble.style.left = startX + 'px';
            bubble.style.top = startY + 'px';
            document.body.appendChild(bubble);

            const animation = bubble.animate(
                [
                    { left: startX + 'px', top: startY + 'px', transform: 'translate(-50%, -50%) scale(0.85)', opacity: 1 },
                    { left: endX + 'px', top: endY + 'px', transform: 'translate(-50%, -50%) scale(0.2)', opacity: 0 }
                ],
                { duration: 600, easing: 'cubic-bezier(0.22, 1, 0.36, 1)' }
            );

            animation.onfinish = () => bubble.remove();
            animation.oncancel = () => bubble.remove();

            cartTarget.classList.add('cart-indicator-flash');
            window.setTimeout(() => cartTarget.classList.remove('cart-indicator-flash'), 700);
        };

        document.addEventListener('submit', async (event) => {
            const form = event.target;
            if (!form.closest('body')) {
                return;
            }
            if (!form.matches('[data-cart-add], [data-cart-form]')) {
                return;
            }

            event.preventDefault();

            const submitButton = form.querySelector('[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.classList.add('cart-button-busy');
            }

            const formData = new FormData(form);
            const requestInit = {
                method: form.method || 'POST',
                body: formData,
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            };

            try {
                const response = await fetch(form.action, requestInit);
                const data = await response.json();

                if (!response.ok) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                        return;
                    }

                    showCartToast(data.message || 'Terjadi kesalahan.', 'error');
                    return;
                }

                // If server instructs to redirect after success
                if (data.redirect) {
                    try {
                        const payload = {
                            message: data.message || 'Menu ditambahkan ke keranjang.',
                            variant: 'success'
                        };
                        sessionStorage.setItem('cart_flash', JSON.stringify(payload));
                    } catch (_) { /* ignore */ }
                    window.location.href = data.redirect;
                    return;
                }

                if (data.html) {
                    const container = document.querySelector('#cart-content');
                    if (container) {
                        container.innerHTML = data.html;
                    }
                }
                if (data.summary_html) {
                    const summary = document.getElementById('cart-summary');
                    if (summary) {
                        summary.innerHTML = data.summary_html;
                    }
                }
                // Update mobile sticky total if present
                if (typeof data.subtotal !== 'undefined') {
                    const totalEl = document.getElementById('cart-mobile-total');
                    if (totalEl) totalEl.textContent = formatRupiah(data.subtotal);
                }

                if (form.matches('[data-cart-add]')) {
                    triggerCartAnimation(submitButton);
                }

                if (data.message) {
                    showCartToast(data.message, data.variant ?? 'success');
                }
                if (typeof data.subtotal !== 'undefined') {
                    const subtotal = Number(data.subtotal ?? 0);
                    const bar = document.getElementById('mini-cart-bar');
                    const totalEl = document.getElementById('mini-cart-total');
                    if (bar) bar.classList.toggle('hidden', !(subtotal > 0));
                    if (totalEl) totalEl.textContent = formatRupiah(subtotal);
                    const summaryTotal = document.querySelector('[data-cart-total]');
                    if (summaryTotal) {
                        summaryTotal.textContent = formatRupiah(subtotal);
                    }
                    notifyCartUpdate(subtotal);
                }
            } catch (error) {
                console.error(error);
                showCartToast('Tidak dapat memproses permintaan.', 'error');
            } finally {
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.classList.remove('cart-button-busy');
                    submitButton.blur();
                }
            }
        });

        // Auto update quantity on input/change without clicking "Perbarui"
        const autoSubmitQuantity = debounce((input) => {
            const form = input.closest('[data-cart-form]');
            if (!form) return;
            const submit = form.querySelector('[type="submit"]');
            // Trigger our submit handler path
            if (submit) {
                submit.click();
            } else {
                form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
            }
        }, 500);

        document.addEventListener('input', (e) => {
            const target = e.target;
            if (target && target.matches('[data-cart-form] input[name="quantity"], form[data-cart-form] input[name="quantity"]')) {
                autoSubmitQuantity(target);
            }
        });

        document.addEventListener('change', (e) => {
            const target = e.target;
            if (target && target.matches('[data-cart-form] input[name="quantity"], form[data-cart-form] input[name="quantity"]')) {
                // Immediate submit on change (e.g., stepper clicks)
                const form = target.closest('[data-cart-form]');
                if (!form) return;
                form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
            }
        });

        document.addEventListener('click', (event) => {
            const button = event.target.closest('[data-quantity-adjust]');
            if (!button) {
                return;
            }

            const form = button.closest('form[data-cart-form]');
            if (!form) {
                return;
            }

            const input = form.querySelector('input[name="quantity"]');
            if (!input) {
                return;
            }

            const delta = parseInt(button.dataset.quantityAdjust, 10);
            const current = parseInt(input.value, 10) || 1;
            const next = Math.max(1, current + delta);

            input.value = next;
            const display = form.querySelector('[data-quantity-display]');
            if (display) {
                display.textContent = next;
            }
            const row = button.closest('[data-note-row]');
            if (row) {
                const noteQty = row.querySelector('[data-note-quantity]');
                if (noteQty) {
                    noteQty.value = next;
                }
            }
            autoSubmitQuantity(input);
            input.dispatchEvent(new Event('input', { bubbles: true }));
            input.dispatchEvent(new Event('change', { bubbles: true }));
        });

        document.addEventListener('click', (event) => {
            const toggle = event.target.closest('[data-note-toggle]');
            if (!toggle) {
                return;
            }

            const row = toggle.closest('[data-note-row]');
            if (!row) {
                return;
            }

            const panel = row.querySelector('[data-note-panel]');
            if (!panel) {
                return;
            }

            panel.classList.toggle('hidden');
            if (!panel.classList.contains('hidden')) {
                const textarea = panel.querySelector('textarea');
                textarea?.focus();
            }
        });

        // Initial mini cart summary on page load (mobile)
        const refreshMiniCartSummary = async () => {
            try {
                const res = await fetch('{{ route('cart.summary') }}', { headers: { 'Accept': 'application/json' } });
                const data = await res.json().catch(() => ({}));
                const bar = document.getElementById('mini-cart-bar');
                const totalEl = document.getElementById('mini-cart-total');
                if (typeof data.subtotal !== 'undefined') {
                    const subtotal = Number(data.subtotal ?? 0);
                    if (bar) bar.classList.toggle('hidden', !(subtotal > 0));
                    if (totalEl) totalEl.textContent = formatRupiah(subtotal);
                }
            } catch (_) { /* ignore */ }
        };

        const onReady = () => {
            refreshMiniCartSummary();
            // Show flash toast if present (after redirect from add-to-cart)
            try {
                const raw = sessionStorage.getItem('cart_flash');
                if (raw) {
                    const data = JSON.parse(raw);
                    showCartToast(data?.message || 'Menu ditambahkan ke keranjang.', data?.variant || 'success');
                    sessionStorage.removeItem('cart_flash');
                }
            } catch (_) { /* ignore */ }
        };

        if (document.readyState === 'complete' || document.readyState === 'interactive') {
            onReady();
        } else {
            document.addEventListener('DOMContentLoaded', onReady);
        }
    })();
</script>
@endpush
