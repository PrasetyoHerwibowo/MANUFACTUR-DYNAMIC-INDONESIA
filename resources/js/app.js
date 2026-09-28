import './bootstrap';
import { initInputFilters } from './input-filter';

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

/**
 * Tolak karakter yang tidak diperbolehkan langsung saat diketik, sehingga
 * kolom hanya berisi karakter yang sah (lihat resources/js/input-filter.js).
 *
 * Fungsi juga dipasang di window agar baris formulir yang ditambahkan lewat
 * JavaScript (mis. item pesanan) ikut mendapat filter karakter.
 */
window.initInputFilters = initInputFilters;

initInputFilters();

