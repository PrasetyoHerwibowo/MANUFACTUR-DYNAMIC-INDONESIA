/**
 * Filter karakter pada kolom formulir admin.
 *
 * Kolom yang diberi atribut data-input-filter hanya menerima karakter yang
 * diperbolehkan: karakter lain tidak pernah muncul di kolom tersebut, baik
 * saat diketik maupun saat ditempel (paste). Aturan di bawah mengikuti
 * validasi sisi server (lihat App\Http\Controllers\Admin\CategoryController),
 * sedangkan validasi server tetap berlaku sebagai pengaman terakhir.
 *
 * Aturan dipakai lewat atribut pada kolom, mis. data-input-filter="letter".
 */

/** Lama tampil peringatan "karakter tidak diperbolehkan" (milidetik). */
const WARNING_DURATION = 2500;

/**
 * Aturan karakter per kolom.
 *
 * - disallowed: regex karakter yang tidak boleh masuk kolom.
 * - note: ringkasan karakter yang diperbolehkan, dipakai pada pesan peringatan.
 *
 * Catatan: aturan `letter` dan `alnum` harus tetap selaras dengan
 * NAME_PATTERN dan TAGLINE_PATTERN di CategoryController.
 */
export const INPUT_FILTERS = {
    // Nama jenis mesin: huruf (termasuk huruf beraksen), spasi, tanda hubung,
    // dan tanda &. Angka serta tanda baca lain ditolak.
    letter: {
        disallowed: /[^\p{L}\s&-]/u,
        note: 'huruf, spasi, tanda hubung (-), dan tanda &',
    },

    // Nama orang: hanya huruf dan spasi. Dipakai pada nama pelanggan.
    name: {
        disallowed: /[^\p{L}\s]/u,
        note: 'huruf dan spasi',
    },

    // Tagline singkat: huruf dan angka, dengan spasi sebagai pemisah kata.
    alnum: {
        disallowed: /[^\p{L}\p{N}\s]/u,
        note: 'huruf dan angka',
    },

    // Deskripsi: huruf, angka, dan tanda baca. Karakter kendali, karakter tak
    // terlihat (zero width), spasi tak biasa, serta tanda < dan > yang dipakai
    // untuk menyusun tag HTML tidak diperbolehkan.
    text: {
        disallowed: /[\u0000-\u0008\u000B\u000C\u000E-\u001F\u007F<>\u00A0\u200B-\u200F\u202A-\u202E\u2066-\u2069\uFEFF]/u,
        note: 'huruf, angka, dan tanda baca',
    },

    // Teks berketerangan resmi (deskripsi jenis mesin & catatan pesanan):
    // huruf, angka, spasi, dan tanda baca. Simbol @ # $ % ^ * serta tanda <
    // dan > tidak diperbolehkan. Selaras dengan TEXT_PATTERN pada
    // CategoryController dan OrderController.
    punctuation: {
        disallowed: /[\u0000-\u0008\u000B\u000C\u000E-\u001F\u007F<>@#$%^*\u00A0\u200B-\u200F\u202A-\u202E\u2066-\u2069\uFEFF]/u,
        note: 'huruf, angka, dan tanda baca',
    },

    // Hanya angka: dipakai pada kode pos, harga satuan, dan jumlah pesanan.
    digits: {
        disallowed: /[^0-9]/u,
        note: 'angka',
    },
};

/** Regex global milik sebuah aturan (dibuat sekali saja, lalu dipakai ulang). */
function globalPattern(rule) {
    rule.global ??= new RegExp(rule.disallowed.source, 'gu');

    return rule.global;
}

/** Buang semua karakter yang tidak diperbolehkan dari sebuah teks. */
export function filterValue(value, rule) {
    return value.replace(globalPattern(rule), '');
}

/** Kolom satu baris tidak boleh berisi baris baru: rapikan menjadi spasi. */
function normalizedForField(field, text) {
    return field instanceof HTMLTextAreaElement ? text : text.replace(/\s+/gu, ' ');
}

/** Sisa karakter yang masih boleh masuk kolom (mengikuti maxlength). */
function availableRoom(field, replacedLength) {
    if (field.maxLength < 0) {
        return Number.MAX_SAFE_INTEGER;
    }

    return Math.max(0, field.maxLength - (field.value.length - replacedLength));
}

/** Sisipkan teks pada posisi kursor/ seleksi aktif. */
function insertAtCaret(field, text) {
    const start = field.selectionStart ?? field.value.length;
    const end = field.selectionEnd ?? start;

    if (typeof field.setRangeText === 'function') {
        field.setRangeText(text, start, end);
    } else {
        field.value = field.value.slice(0, start) + text + field.value.slice(end);
    }

    field.setSelectionRange(start + text.length, start + text.length);
    field.dispatchEvent(new Event('input', { bubbles: true }));
}

/** Tampilkan peringatan singkat bahwa ada karakter yang ditolak. */
function showWarning(field, rule) {
    const wrapper = field.parentElement;

    if (!wrapper) {
        return;
    }

    let warning = wrapper.querySelector('[data-input-filter-warning]');

    if (!(warning instanceof HTMLElement)) {
        warning = document.createElement('p');
        warning.dataset.inputFilterWarning = '';
        warning.className = 'mt-1.5 text-xs font-semibold text-red-600 dark:text-red-400';
        warning.setAttribute('role', 'status');
        wrapper.append(warning);
    }

    warning.textContent = `Karakter itu tidak diperbolehkan. Kolom ini hanya menerima ${rule.note}.`;

    window.clearTimeout(Number(warning.dataset.timer) || 0);
    warning.dataset.timer = String(window.setTimeout(() => warning.remove(), WARNING_DURATION));
}

/** Bersihkan isi kolom dari karakter yang tidak diperbolehkan. */
function cleanField(field, rule) {
    const value = field.value;

    if (value === '') {
        return;
    }

    const cleaned = filterValue(value, rule);

    if (cleaned === value) {
        return;
    }

    // Kursor digeser sebanyak karakter yang dibuang sebelum posisinya agar
    // posisi mengetik admin tidak meloncat.
    const caret = field.selectionStart ?? value.length;
    const removed = caret - filterValue(value.slice(0, caret), rule).length;

    field.value = cleaned;
    field.setSelectionRange(caret - removed, caret - removed);
    showWarning(field, rule);
}

/** Pasang filter karakter pada sebuah kolom formulir. */
function attachFilter(field, rule) {
    // Karakter salah ketik ditahan sebelum masuk ke kolom sehingga tidak pernah
    // tampil (tanpa kedip). Bagian teks yang masih sah, mis. hasil tempel,
    // tetap dimasukkan.
    field.addEventListener('beforeinput', (event) => {
        if (event.isComposing || typeof event.data !== 'string' || event.data === '') {
            return;
        }

        if (!rule.disallowed.test(event.data)) {
            return;
        }

        event.preventDefault();

        const replaced = (field.selectionEnd ?? 0) - (field.selectionStart ?? 0);
        const allowed = filterValue(normalizedForField(field, event.data), rule)
            .slice(0, availableRoom(field, replaced));

        if (allowed === '') {
            showWarning(field, rule);

            return;
        }

        insertAtCaret(field, allowed);
    });

    // Jaring pengaman untuk perubahan yang tidak melewati beforeinput: isian
    // otomatis browser, tarik-lepas teks, atau browser lama.
    field.addEventListener('input', (event) => {
        if (event.isComposing) {
            return;
        }

        cleanField(field, rule);
    });
}

/** Pasang filter pada semua kolom dengan atribut data-input-filter. */
export function initInputFilters(root = document) {
    root.querySelectorAll('[data-input-filter]').forEach((field) => {
        const rule = INPUT_FILTERS[field.dataset.inputFilter];

        if (rule && (field instanceof HTMLInputElement || field instanceof HTMLTextAreaElement)) {
            attachFilter(field, rule);
        }
    });
}
