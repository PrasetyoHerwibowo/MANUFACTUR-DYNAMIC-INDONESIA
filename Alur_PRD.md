# Product Requirements Document (PRD) & Alur Sistem
## PT Manufactur Dynamic Indonesia

---

## 1. Ikhtisar Proyek (Project Overview)
**PT Manufactur Dynamic Indonesia** adalah platform digital manufaktur terintegrasi berbasis Laravel 12 yang melayani bisnis perancangan, fabrikasi, dan perakitan mesin pengolahan hasil perkebunan (spesialisasi kopi dan kakao/cokelat). Sistem mencakup Company Profile publik, Panel Administrasi Pabrik, serta REST API untuk Mobile E-Commerce & After-Sales Services.

- **Domain Bisnis**: Manufaktur Alat & Mesin Pengolahan Kopi (MD-Coffee Series) dan Kakao (MD-Cocoa Series).
- **Model Bisnis**: B2B (Business-to-Business) & B2G untuk perkebunan, koperasi, pabrik pengolahan, dan UMKM.
- **Sistem Transaksi**: Kontrak Purchase Order (PO) bertahap: Uang Muka (DP) 50% dan Pelunasan 50% sebelum pengiriman.

---

## 2. Aktor & Target Pengguna (User Persona)

| Peran (Role) | Platform | Akses & Wewenang |
|---|---|---|
| **Publik / Calon Klien B2B** | Web Browser (Desktop/Mobile) | Eksplorasi profil perusahaan, katalog mesin, spesifikasi teknis, unduh brosur/spesifikasi, kirim pesan kontak. |
| **Klien Terdaftar / Procurement** | Mobile App / REST API Client | Pembuatan Purchase Order (PO), pemantauan timeline 7 tahap produksi, pembayaran DP & Pelunasan, unduh invoice resmi, booking servis, klaim garansi. |
| **Administrator Pabrik** | Panel Admin Web (`/admin`) | Manajemen katalog (kategori & mesin), pemantauan pesanan, verifikasi bukti transfer pembayaran, manajemen pelanggan terdaftar, profil perusahaan, laporan transaksi. |

---

## 3. Arsitektur Teknologi (Tech Stack)

- **Backend Framework**: Laravel 12.x (PHP ^8.2)
- **Autentikasi & Keamanan**: Laravel Session (Web Admin), Laravel Sanctum (Mobile API Token & SSO support)
- **Database**: MySQL / PostgreSQL (Kompatibel dengan Supabase Postgres & Row Level Security)
- **Frontend Web**: Blade Templating + Tailwind CSS + Vite
- **Penyimpanan Berkas**: Local Disk / Public Storage (`public/uploads`) dengan sanitasi nama berkas & kompresi gambar
- **Caching**: Multi-layer database caching untuk navigasi produk, profil perusahaan (24 jam), dan statistik dashboard

---

## 4. Struktur Data & Entitas Utama

```
+------------------+         +-------------------+         +--------------------+
|    categories    | 1 --- * |      machines     | 1 --- * |   machine_images   |
+------------------+         +-------------------+         +--------------------+
                                       | 1
                                       |
                                       * *
+------------------+         +-------------------+         +--------------------+
|    customers     | 1 --- * |      orders       | 1 --- * |    order_items     |
+------------------+         +-------------------+         +--------------------+
         | 1                           | 1
         |                             |
         * *                           * *
+------------------+         +-------------------+
| service_bookings |         |     payments      |
+------------------+         +-------------------+
         |
+------------------+
| warranty_claims  |
+------------------+
```

### Kamus Entitas Pokok:
1. **categories**: Pengelompokan lini mesin berdasarkan serial (`coffee`, `cocoa`, `both`).
2. **machines**: Master unit mesin (kode model, nama, fungsi, kapasitas, daya listrik, dimensi, berat, spesifikasi teks terstruktur, foto utama).
3. **customers**: Data entitas pembeli/perusahaan klien B2B (nama, PIC, email, telepon, instansi, alamat).
4. **orders**: Purchase Order (PO) mesin, kalkulasi nilai kontrak, DP 50%, pelunasan 50%, status order, dan milestone tahap produksi manufaktur.
5. **order_items**: Rincian mesin yang dipesan per pesanan beserta harga satuan dan kuantitas.
6. **payments**: Log transaksi pembayaran per pesanan (kode pembayaran, metode transfer, termin DP/Pelunasan, bukti transfer, waktu jatuh tempo 12 jam, status).
7. **service_bookings**: Tiket penjadwalan kunjungan teknisi servis dan maintenance berkala unit di lokasi pabrik klien.
8. **warranty_claims**: Tiket klaim garansi unit mesin rusak menyertakan nomor seri pabrik dan foto kerusakan fisik.
9. **company_profiles**: Profil identitas pabrik, kontak resmi, alamat pabrik di Jawa Timur, legalitas, dan tautan sosial.

---

## 5. Alur Bisnis & Siklus Hidup Sistem (System Lifecycle)

### 5.1 Alur Pengunjung Publik (Website Company Profile)
1. **Landing Page**: Pengunjung melihat portofolio manufaktur, kategori unggulan (MD-Coffee & MD-Cocoa), dan spesifikasi ringkas.
2. **Katalog Produk**: Filter berdasarkan seri (Kopi/Kakao), pencarian model mesin, dan inspeksi detail spesifikasi teknis (kapasitas/jam, kW daya, dimensi, berat).
3. **Inquiry & Kontak**: Pengunjung dapat mengontak admin via WhatsApp gateway resmi atau mengisi formulir pesan kontak.

### 5.2 Alur Pemesanan Mesin (Purchase Order & Pembayaran Bertahap)
1. **Pembuatan PO**: Klien memilih unit mesin, menentukan kuantitas, alamat pabrik/pengiriman, dan catatan custom engineering.
2. **Penerbitan Kontrak**:
   - Sistem menetapkan `order_code` unik (`PO-YYYYMMDD-XXXXXX`).
   - Sistem menghitung `total_amount`, `dp_amount` (50%), dan `pelunasan_amount` (50%).
   - Terbit invoice dengan payment window 12 jam.
3. **Pembayaran DP (Uang Muka 50%)**:
   - Klien mentransfer via rekening resmi pabrik dan mengunggah bukti transfer.
   - Status pesanan berpindah dari `verifikasi_dp` ke `persiapan_bahan`.

### 5.3 Timeline 7 Tahap Produksi Manufaktur (Manufacturing Pipeline)
Setiap pesanan mesin melalui 7 tahapan resmi manufaktur yang dapat dipantau real-time:
```
[1. Verifikasi DP] ➔ [2. Persiapan Bahan Baku] ➔ [3. Proses Produksi/Assembly]
                                                         │
                                                         ▼
[6. Pengiriman & Instalasi] ◄─ [5. Menunggu Pelunasan] ◄─ [4. Quality Control (QC)]
         │
         ▼
[7. After-Sales Service]
```
1. **Tahap 1 - Verifikasi DP**: Validasi dana masuk 50% oleh bagian finance.
2. **Tahap 2 - Persiapan Bahan Baku**: Pengadaan pelat baja food grade (SS304/SS316), motor penggerak, dan komponen mekanikal.
3. **Tahap 3 - Proses Produksi / Assembly**: Pengerjaan fabrikasi, pengelasan, perakitan sasis, dan instalasi kelistrikan.
4. **Tahap 4 - Quality Control (QC)**: Pengujian beban, kalibrasi putaran, uji fungsi nir-beban, dan checklist keamanan operator.
5. **Tahap 5 - Menunggu Pelunasan**: Notifikasi kesiapan mesin kepada klien untuk melakukan transfer pelunasan sisa 50%.
6. **Tahap 6 - Pengiriman & Instalasi**: Pengiriman unit menggunakan ekspedisi alat berat, unboxing, penempatan layout di pabrik klien, dan comissioning.
7. **Tahap 7 - After-Sales Service**: Pelatihan operator pabrik klien, aktivasi garansi mesin, dan integrasi modul purna jual.

### 5.4 Alur Layanan Purna Jual (After-Sales Service)
- **Booking Kunjungan Teknisi (`service_bookings`)**:
  Klien memasukkan nomor seri mesin, model mesin, tanggal yang diinginkan, jenis kendala, dan alamat lokasi. Sistem menerbitkan nomor tiket servis (`SRV-YYYYMMDD-XXXXXX`) dan menugaskan teknisi resmi.
- **Klaim Garansi Kerusakan (`warranty_claims`)**:
  Klien memasukkan serial number unit, tanggal pembelian, deskripsi kerusakan, dan foto bukti fisik komponen rusak. Tim engineering pabrik memverifikasi validitas garansi dan memproses pengiriman sparepart pengganti atau kunjungan inspeksi.

### 5.5 Alur Verifikasi Admin Pabrik
- Admin mengakses `/admin` dengan kredensial terproteksi middleware `admin`.
- Memantau metriks keuangan: total pesanan, pesanan menunggu verifikasi, omzet lunas, dan nilai pembayaran menunggu.
- Memverifikasi bukti pembayaran: klik "Lunas" untuk mengesahkan atau "Tandai Gagal" disertai alasan.
- Otomasi pembatalan/kadaluwarsa: Sistem otomatis mengecek dan menandai pembayaran yang melewati batas waktu 12 jam sebagai `gagal` (`Payment::expireOverdue()`).

---

## 6. Spesifikasi REST API

### 6.1 Autentikasi
- `POST /api/register` & `POST /api/auth/register` : Registrasi akun klien baru + otomatis membuat profil customer.
- `POST /api/login` & `POST /api/auth/login` : Autentikasi klien, menghasilkan Sanctum Bearer Token.
- `POST /api/auth/sso` : Endpoint Single Sign-On untuk integrasi eksternal.
- `GET /api/me` : Profil pengguna yang sedang terautentikasi (Protected: `auth:sanctum`).
- `POST /api/logout` : Revokasi token sesi aktif (Protected: `auth:sanctum`).

### 6.2 Katalog Produk
- `GET /api/machines` : Daftar seluruh mesin aktif beserta kategori, foto, dan spesifikasi (opsional filter query).
- `GET /api/machines/{id}` : Detail komprehensif satu unit mesin berdasarkan ID atau slug.
- `GET /api/categories` : Daftar kategori mesin (lini kopi dan kakao).

### 6.3 Purchase Order & Pembayaran
- `GET /api/orders` : Riwayat seluruh Purchase Order milik pengguna.
- `POST /api/orders` : Pembuatan PO baru (DP 50%, kalkulasi otomatis, penerbitan invoice awal).
- `GET /api/orders/{id}` : Detail PO, timeline status produksi, dan riwayat pembayaran terkait.
- `POST /api/orders/{id}/pay-dp` : Unggah bukti transfer DP 50% (otomatis memajukan milestone produksi).
- `POST /api/orders/{id}/pay-pelunasan` : Unggah bukti transfer pelunasan 50% (status order menjadi lunas, maju ke tahap pengiriman).
- `GET /api/orders/{id}/invoice` : Struktur data invoice resmi B2B (nomor invoice, data rekening pabrik, item, rincian termin).
- `GET /api/payments` : Log seluruh transaksi pembayaran.

### 6.4 Layanan Purna Jual (After-Sales)
- `GET /api/services/bookings` : Daftar pengajuan booking teknisi servis.
- `POST /api/services/bookings` : Formulir pengajuan jadwal kunjungan teknisi.
- `GET /api/services/warranty-claims` : Riwayat klaim garansi yang diajukan.
- `POST /api/services/warranty-claims` : Pengajuan klaim garansi disertai serial number dan foto kerusakan.

---

## 7. Kebutuhan Non-Fungsional (Non-Functional Requirements)

1. **Keamanan (Security)**:
   - Sanitasi input ketat terhadap form input dan pencegahan XSS.
   - Proteksi upload berkas gambar (validasi MIME, batas ukuran 4MB, penamaan acak terisolasi).
   - Penggunaan prepared statements dan Eloquent ORM untuk mencegah SQL Injection.
2. **Kinerja & Skalabilitas (Performance)**:
   - Eager loading relasi (`with(['items.machine', 'customer'])`) untuk mencegah problem N+1 Query.
   - Cache tagging / key caching pada data statis (profil perusahaan, navigasi produk, statistik dashboard).
   - Indeks database pada kolom pencarian (`order_code`, `payment_code`, `status`, `serial_number`).
3. **Integritas Transaksi (Data Integrity)**:
   - Pembungkusan transaksi krusial (pembuatan order, pembayaran, booking tiket) dalam `DB::transaction()` untuk menjamin rollback otomatis bila terjadi error.

---

## 8. Rencana Pengembangan Selanjutnya (Next Steps / Roadmap)

1. **Payment Gateway Otomatis**: Integrasi Midtrans / Xendit Virtual Account & QRIS B2B untuk auto-reconciliation pembayaran tanpa upload manual.
2. **Push Notification / WhatsApp Bot**: Pengiriman notifikasi otomatis via WhatsApp Gateway saat status produksi mesin berganti tahapan (misal: mesin selesai QC, mesin dalam perjalanan ekspedisi).
3. **Export Laporan PDF & Excel**: Cetak surat jalan, invoice faktur pajak, dan rekap keuangan bulanan di panel admin.
4. **Portal Pelacakan Ekspedisi**: Integrasi nomor resi truk tronton / ekspedisi kontainer logistik alat berat pada tahap 6 (Pengiriman & Instalasi).
