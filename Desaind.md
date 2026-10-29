# Spesifikasi Sistem Desain & Palet Warna Website (Hitam & Putih)
## PT Manufactur Dynamic Indonesia (Public Website)

Dokumen ini mendokumentasikan spesifikasi resmi sistem desain, palet warna monokrom (Black & White / Grayscale), gradien industrial, tipografi, dan komponen UI yang diterapkan pada website publik (Company Profile, Katalog Mesin, dan Halaman Informasi) yang bersumber dari `resources/css/public.css` dan komponen Blade publik.

---

## 1. Palet Warna Pokok (Black & White Industrial Palette)

Website publik menggunakan tema manufaktur industrial modern berestetika **Monochrome / Black & White** berdaya kontras tinggi (*high contrast*), memberikan kesan presisi mekanik teknik, bersih, kokoh, dan premium.

| Variabel CSS | Hex Code | Nama Warna | Peran & Penggunaan Utama |
|---|---|---|---|
| `--emerald` | `#09090b` | **Zinc 950 (Charcoal Deep Black)** | Warna primer elemen interaktif, tombol aksi utama, header dropdown, aksen fokus |
| `--forest` | `#000000` | **Pitch Pure Black** | Latar belakang hero section, footer, dan kontras tertinggi |
| `--navy` | `#09090b` | **Pitch Charcoal (Heading Text)** | Seluruh judul `h1`–`h6`, teks brand logo, teks tebal spesifikasi |
| `--steel` | `#27272a` | **Zinc 800 (Technical Charcoal)** | Warna aksen sekunder, tombol outline, border aktif |
| `--mint` | `#f4f4f5` | **Zinc 100 (Neutral Soft Surface)** | Latar alternatif section, latar chip badge, hover menu bar |
| `--ice` | `#fafafa` | **Zinc 50 (Off-White Surface)** | Latar kartu pendukung dan permukaan dengan kontras lembut |
| `--teal` | `#ffffff` | **Pure White** | Teks tombol kontras tinggi, badge teks pada latar gelap |
| `--orange` | `#18181b` | **Zinc 900 (Dark Zinc)** | Latar tombol aksi tegas, aksen penekanan |
| `--white` | `#ffffff` | **Pure Stark White** | Latar belakang utama halaman, permukaan kartu (`--card-bg`) |
| `--body` | `#27272a` | **Zinc 800 (Body Text)** | Teks paragraf utama (tingkat keterbacaan tajam & jelas) |
| `--muted` | `#71717a` | **Zinc 500 (Muted Technical Text)** | Deskripsi spesifikasi, label model code, subtitle |
| `--border-color` | `rgba(0, 0, 0, 0.08)` | **Monochrome Hairline Border** | Garis batas tipis presisi kartu, pembatas tabel, dropdown |

---

## 2. Palet Mode Gelap (Dark Mode System: `.dark`)

Ketika tema gelap aktif, variabel warna di `:root` dialihkan secara konsisten ke monokrom gelap:

| Variabel CSS | Nilai Hex / RGBA | Peran di Mode Gelap |
|---|---|---|
| `--white` | `#09090b` (Deep Pitch) | Latar belakang dasar halaman gelap |
| `--card-bg` | `#121215` (Zinc Surface) | Latar belakang kartu komponen, dropdown, dan accordion |
| `--navy` | `#f4f4f5` (Zinc 100) | Warna heading teks utama pada latar gelap |
| `--body` | `#d4d4d8` (Zinc 300) | Warna teks paragraf isi agar nyaman dibaca |
| `--muted` | `#a1a1aa` (Zinc 400) | Warna keterangan teks sekunder |
| `--mint` | `#18181b` (Zinc 900) | Latar aksen gelap |
| `--ice` | `#1f1f23` (Zinc 850) | Latar kartu penunjang kontras seimbang |
| `--border-color` | `rgba(255, 255, 255, 0.10)` | Garis batas kontras putih tipis (*hairline*) |

---

## 3. Sistem Gradien Monokrom (Design Gradients)

Website memanfaatkan komposisi gradien netral hitam-abu-putih:

1. **`--grad-primary` (Gradien Monokrom Utama)**:
   - **CSS**: `linear-gradient(135deg, #09090b 0%, #27272a 100%)` (Mode Terang)
   - **CSS**: `linear-gradient(135deg, #27272a 0%, #09090b 100%)` (Mode Gelap)
   - **Penerapan**: Tombol CTA Navbar, ikon logo brand, badge aktif, tombol detail accordion.

2. **`--grad-hero` (Gradien Hero & Showcase Banner)**:
   - **CSS**: `linear-gradient(180deg, rgba(9, 9, 11, 0.94) 0%, rgba(24, 24, 27, 0.88) 50%, rgba(9, 9, 11, 0.96) 100%)`
   - **Penerapan**: Overlay visual latar belakang Hero Header dan bagian Sustainability.

3. **`--grad-soft` (Gradien Permukaan Halus)**:
   - **CSS**: `linear-gradient(135deg, #f4f4f5 0%, #e4e4e7 100%)`
   - **Penerapan**: Placeholder gambar mesin sebelum termuat, latar kartu fitur halus.

---

## 4. Visual Language & Komponen Khusus

### A. Grayscale Image Treatment
- Foto latar hero (`.hero-bg`, `.sus-bg`) diberikan filter desaturasi monokromatis: `filter: grayscale(100%) contrast(115%)` dengan lapisan overlay radial dan linear gelap untuk menjamin keterbacaan teks headline putih.

### B. Glassmorphism Badge Pills
- Komponen badge hero menggunakan *frosted glass*: `background: rgba(255, 255, 255, 0.10); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.20);`

### C. Tombol CTA Berdaya Kontras Tinggi (*Inverted CTA*)
- **Primary CTA (`.btn-learn-more`)**: Latar putih solid (`#ffffff`), teks hitam pekat (`#09090b`), font-weight 700, sudut melingkar kapsul (*pill shape* `999px`), tanpa stroke (`border: none`), bayangan tegas `box-shadow: 0 4px 16px rgba(0, 0, 0, 0.35)`.
- **Secondary CTA (`.btn-hero-outline`)**: Latar transparan kaca (`rgba(255, 255, 255, 0.12)`), tanpa stroke (`border: none`), teks putih solid, `backdrop-filter: blur(10px)`.

---

## 5. Elevasi Bayangan & Radius Sudut

### Bayangan (Box Shadows)
- `--shadow-sm`: `0 1px 3px rgba(0, 0, 0, 0.06)` (Kartu datar / micro interaction)
- `--shadow-md`: `0 4px 16px rgba(0, 0, 0, 0.08)` (Kartu produk & header melayang)
- `--shadow-lg`: `0 12px 32px rgba(0, 0, 0, 0.12)` (Gambar zigzag section & hero float)
- `--shadow-xl`: `0 20px 48px rgba(0, 0, 0, 0.16)` (Mega dropdown menu & modal)

### Radius Sudut (Border Radius)
- `--radius-sm`: `8px` (Tombol standar, input formulir, badge model mesin)
- `--radius-md`: `12px` (Kartu spesifikasi, dropdown menu, accordion)
- `--radius-lg`: `16px` (Mega dropdown container, dialog)
- `--radius-xl`: `24px` (Frame foto produk zigzag, showcase banner)
- **Pill Shape**: `999px` (Tombol hero, badge status kapsul)

---

## 6. Tipografi Website Publik

### A. Font Kombinasi
- **Body & Paragraf**: `'Inter', system-ui, sans-serif`
  - Karakter: Bersih, geometris, tingkat legibilitas tinggi untuk membaca spesifikasi teknis mesin.
  - Warna default: `--body` (`#27272a`).
  - Line height: `1.65`.
- **Headings (`h1` s/d `h6`)**: `'Plus Jakarta Sans', sans-serif`
  - Karakter: Tegas, industrial, arsitektural.
  - Warna default: `--navy` (`#09090b`).
  - Font weight: `700` (Bold) & `800` (Extra Bold).
  - Letter spacing: `-0.03em` hingga `-0.05em` pada display titles.

### B. Ikonografi
- **Library**: `Font Awesome 6 Free` & `Tabler Icons`
- **Penerapan**: Ikon spesifikasi mesin (daya listrik, kapasitas, dimensi, berat), tombol navigasi, switch tema gelap/terang, dan tautan WhatsApp resmi.
