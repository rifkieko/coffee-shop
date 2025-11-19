<script>
    (function () {
        const preview = document.getElementById('image-preview');
        if (!preview) {
            return;
        }

        const imageInput = document.getElementById('image');
        const originalSrc = preview.dataset.originalSrc;

        const updatePreviewSource = (src) => {
            preview.src = src || originalSrc || preview.src;
        };

        if (imageInput) {
            imageInput.addEventListener('change', () => {
                const file = imageInput.files && imageInput.files[0];
                if (!file) {
                    updatePreviewSource(originalSrc);
                    return;
                }

                const reader = new FileReader();
                reader.addEventListener('load', () => updatePreviewSource(reader.result));
                reader.readAsDataURL(file);
            });
        }
    })();
</script>
