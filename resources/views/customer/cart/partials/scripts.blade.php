@push('scripts')
<script>
    document.addEventListener('submit', async (event) => {
        const form = event.target;
        if (!form.closest('body')) {
            return;
        }
        if (!form.matches('[data-cart-add], [data-cart-form]')) {
            return;
        }

        event.preventDefault();

        const formData = new FormData(form);
        const requestInit = {
            method: form.method || 'POST',
            body: formData,
            headers: {'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest'},
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

                alert(data.message || 'Terjadi kesalahan.');
                return;
            }

            if (data.html) {
                const container = document.querySelector('#cart-content');
                if (container) {
                    container.innerHTML = data.html;
                }
            }

            if (data.message) {
                alert(data.message);
            }
        } catch (error) {
            console.error(error);
            alert('Tidak dapat memproses permintaan.');
        }
    });
</script>
@endpush
