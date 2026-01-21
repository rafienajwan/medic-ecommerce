# Medic E-Commerce

Medic E-Commerce adalah proyek marketplace sederhana (multi-vendor) untuk perdagangan alat dan perlengkapan kesehatan. Aplikasi ini dibangun menggunakan Laravel + Inertia + Vue, sehingga antarmuka terasa seperti SPA namun tetap terintegrasi dengan baik di sisi backend.

## Fitur

**Pelanggan (Customer)**
- Jelajah produk + detail produk (pencarian & filter kategori)
- Keranjang belanja
- Checkout + pembuatan pesanan
- Invoice PDF (unduh) + email invoice (PDF terlampir)
- Ulasan produk
- Buku tamu/testimoni (dapat dikirim tanpa login, ditampilkan setelah persetujuan)

**Penjual (Vendor)**
- Pengajuan menjadi vendor
- Kelola produk (CRUD + upload gambar)
- Kelola pesanan vendor (update status)

**Admin**
- Approve/reject vendor
- Kelola produk, pesanan, user, dan guest book

**Pembayaran (simulasi)**
- `prepaid` (kartu/bank) → status langsung `completed`
- `paypal` → simulasi (membuat `transaction_ref`)
- `cod` → ada pengecekan ketersediaan COD (hanya vendor satu kota dengan tujuan)

**Session timeout (penting)**
- Terdapat auto-logout saat tidak ada aktivitas (default 60 detik). Pengaturan ini dibuat agresif untuk kebutuhan demo/latihan.

## Tech stack

- Backend: Laravel 12, Jetstream (Inertia stack), Fortify, Sanctum
- Frontend: Vue 3, Inertia.js, Tailwind CSS, Vite, Axios
- Database default: PostgreSQL
- PDF: `barryvdh/laravel-dompdf`
- Test: Pest

## Menjalankan project (local)

### Prasyarat

- PHP 8.2+
- Composer
- Node.js + npm
- PostgreSQL

### Setup pertama kali

1) Instal dependensi
```bash
composer install
npm install
```

2) Buat file environment
```powershell
Copy-Item .env.example .env
php artisan key:generate
```

3) Konfigurasi database di `.env`
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=medic_ecommerce
DB_USERNAME=...
DB_PASSWORD=...
```

4) Migration + seed + storage link
```bash
php artisan migrate --seed
php artisan storage:link
```

### Menjalankan mode development

Disarankan (satu perintah, sudah termasuk Vite + queue + logs):
```bash
composer dev
```

Alternatif (manual, gunakan terminal terpisah):
```bash
php artisan serve
php artisan queue:listen --tries=1
npm run dev
```

Akses aplikasi: `http://localhost:8000`

## Akun contoh (hasil seeding)

Semua password default: `password`

- Admin: `admin@medic-ecommerce.com`
- Vendor: `vendor1@medic-ecommerce.com` s/d `vendor5@medic-ecommerce.com`
- Customer (contoh): `budi.santoso@hospital.co.id`, `siti.nurhaliza@gmail.com`, dll

## API (ringkas)

Endpoint tersedia di `/api` dan digunakan oleh halaman Inertia/Vue. Autentikasi API menggunakan Sanctum (stateful/session-based), sehingga pengujian paling praktis dilakukan melalui browser saat aplikasi berjalan.

Contoh endpoint yang sering dipakai:
- Public: `GET /api/products`, `GET /api/products/{id}`, `GET /api/guestbook`, `POST /api/guestbook`
- Auth: `POST /api/auth/register`, `POST /api/auth/login`, `POST /api/auth/logout`, `GET /api/auth/me`
- Cart: `GET /api/cart`, `POST /api/cart`, `PATCH /api/cart/{itemId}`, `DELETE /api/cart/{itemId}`
- Orders: `POST /api/orders`, `GET /api/orders`, `GET /api/orders/{id}/invoice`
- COD: `POST /api/cod/check`

## Email (invoice)

Secara default, `.env.example` menggunakan `MAIL_MAILER=log`, sehingga email akan tercatat pada log (tidak benar-benar terkirim). Untuk mengirim email sungguhan, sesuaikan konfigurasi `MAIL_*` sesuai SMTP yang digunakan.

## Testing

```bash
php artisan test
```

## Security note

Jangan melakukan commit file `.env` ke Git. Gunakan `.env.example` sebagai template.

