import './bootstrap';

/**
 * Pratinjau foto sebelum diunggah admin.
 * Input file dengan atribut data-preview="#id-gambar".
 */
document.addEventListener('change', (event) => {
    const input = event.target;

    if (!(input instanceof HTMLInputElement) || input.type !== 'file' || !input.dataset.preview) {
        return;
    }

    const preview = document.querySelector(input.dataset.preview);
    const file = input.files?.[0];

    if (preview instanceof HTMLImageElement && file) {
        preview.src = URL.createObjectURL(file);
    }
});

/**
 * Buka/ tutup menu navigasi versi mobile.
 */
document.querySelectorAll('[data-menu-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
        const target = document.querySelector(button.dataset.menuToggle);

        if (target) {
            target.classList.toggle('hidden');
        }
    });
});

/**
 * Konfirmasi sebelum mengirim form (mis. hapus data).
 */
document.addEventListener('submit', (event) => {
    const form = event.target;

    if (form instanceof HTMLFormElement && form.dataset.confirm && !window.confirm(form.dataset.confirm)) {
        event.preventDefault();
    }
});

