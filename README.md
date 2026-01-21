# Medic E-Commerce

## Deskripsi Proyek

Medic E-Commerce merupakan platform marketplace multi-vendor yang dirancang khusus untuk perdagangan alat dan perlengkapan kesehatan. Aplikasi ini dibangun menggunakan teknologi modern dengan arsitektur Laravel, Inertia.js, dan Vue.js untuk menghadirkan pengalaman Single Page Application (SPA) yang responsif dengan integrasi backend yang robust.

## Fitur Utama

### Modul Pelanggan (Customer)
- Eksplorasi katalog produk dengan fitur pencarian dan filter berdasarkan kategori
- Sistem keranjang belanja yang dinamis
- Proses checkout dan manajemen pesanan
- Unduh faktur dalam format PDF dan pengiriman otomatis melalui email
- Sistem review dan rating produk
- Fitur buku tamu untuk testimoni (tersedia untuk pengguna tanpa autentikasi, menunggu persetujuan administrator)

### Modul Vendor
- Sistem registrasi dan pengajuan akun vendor
- Manajemen produk lengkap (Create, Read, Update, Delete) dengan dukungan unggah gambar
- Manajemen pesanan vendor dengan pembaruan status real-time

### Modul Administrator
- Sistem persetujuan dan penolakan pendaftaran vendor
- Manajemen terpusat untuk produk, pesanan, pengguna, dan buku tamu
- Dashboard analitik dan monitoring aktivitas platform

### Sistem Pembayaran
- **Prepaid (Kartu/Transfer Bank)**: Pembayaran dengan status otomatis `completed`
- **PayPal**: Simulasi pembayaran dengan generasi `transaction_ref`
- **Cash on Delivery (COD)**: Verifikasi ketersediaan berdasarkan lokasi vendor dan tujuan pengiriman

### Keamanan dan Autentikasi
- Sistem auto-logout setelah 60 menit tidak ada aktivitas pengguna
- Implementasi CSRF protection dan session management yang aman

## Teknologi yang Digunakan

### Backend
- **Framework**: Laravel 12
- **Authentication**: Laravel Jetstream (Inertia Stack), Fortify, Sanctum
- **Database**: PostgreSQL
- **PDF Generator**: barryvdh/laravel-dompdf
- **Testing Framework**: Pest PHP
Instalasi dan Konfigurasi

### Persyaratan Sistem

- PHP 8.2 atau lebih tinggi
- Composer 2.x
- Node.js 18.x atau lebih tinggi dengan npm
- PostgreSQL 13.x atau lebih tinggi

### Langkah Instalasi

#### 1. Instalasi Dependencies

```bash
composer install
npm install
```

#### 2. Konfigurasi Environment

Salin file environment template dan generate application key:

```powershell
Copy-Item .env.example .env
php artisan key:generate
```

#### 3. Konfigurasi Database

Perbarui konfigurasi database pada file `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=medic_ecommerce
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

#### 4. Migrasi Database dan Seeding

Jalankan perintah berikut untuk membuat struktur database dan mengisi data awal:

```bash
php artisan migrate --seed
php artisan storage:link
```

### Menjalankan Aplikasi

#### Mode Development (Disarankan)

Gunakan perintah berikut untuk menjalankan seluruh service dalam satu terminal:

```bash
composer dev
```

Perintah ini akan menjalankan:
- Laravel Development Server
- Vite Development Server
- Queue Worker
- LKredensial Akun Default

Database seeding akan membuat beberapa akun pengguna untuk keperluan testing dan development.

**Password Default**: `password` (untuk semua akun)

### Akun Administrator
- Email: `admin@medic-ecommerce.com`
- Role: Administrator dengan akses penuh ke seluruh sistem

### Akun Vendor
- `vendor1@medic-ecommerce.com` (Alat Medis Sejahtera - Bandung)
- `vendor2@medic-ecommerce.com` (Prima Tech Solution - Surabaya)
- `vendor3@medic-ecommerce.com` (Jakarta Medical Store - Jakarta)
- `vendor4@medic-ecommerce.com` (Medan Health Equipment - Medan)
- `vendor5@medic-ecommerce.com` (Semarang Medical Center - Semarang)

### Akun Customer
- `budi.santoso@hospital.co.id`
- `siti.nurhaliza@gmail.com`
- `ahmad.yani@clinic.com`
- `dewi.lestari@yahoo.com`
- `rizki.pratama@outlook.com`
```bash
php artisan serve
```

**Terminal 2 - Queue Worker:**
```bash
php artisan queue:listen --tries=1
```

**Terminal 3 - Vite Development Server:**
```bash
npm run dev
```

#### Akses Aplikasi

Setelah menjalankan server, akses aplikasi melalui browser:
```
http://127.0.0.1:8000
``
Alternatif (manual, gunakan terminal terpisah):
```Dokumentasi API

Aplikasi menyediakan RESTful API yang diakses melalui prefix `/api`. Autentikasi menggunakan Laravel Sanctum dengan metode stateful (session-based), sehingga pengujian dapat dilakukan langsung melalui browser atau tools seperti Postman.

### Endpoint Publik
- `GET /api/products` - Daftar produk dengan pagination
- `GET /api/products/{id}` - Detail produk spesifik
- `GET /api/guestbook` - Daftar testimoni yang telah disetujui
- `POST /api/guestbook` - Submit testimoni baru

### Endpoint Autentikasi
- `POST /api/auth/register` - Registrasi pengguna baru
- `POST /api/auth/login` - Login pengguna
- `POST /api/auth/logout` - Logout pengguna
- `GET /api/auth/me` - Informasi pengguna yang sedang login

### Endpoint Keranjang Belanja (Authenticated)
- `GET /api/cart` - Lihat isi keranjang
- `POST /api/cart` - Tambah item ke keranjang
- `PATCH /api/cart/{itemId}` - Update kuantitas item
- `DELETE /api/cart/{itemId}` - Hapus item dari keranjang

### Endpoint Pesanan (Authenticated)
- `POST /api/orders` - Buat pesanan baru
- `GET /api/orders` - Daftar pesanan pengguna
- `GET /api/orders/{id}` - Detail pesanan
- `GET /api/orders/{id}/invoice` - Unduh invoice PDF
Konfigurasi Email

Aplikasi menggunakan sistem email untuk mengirim invoice dan notifikasi. Konfigurasi default pada `.env.example` menggunakan driver `log`, sehingga email tidak terkirim secara aktual melainkan disimpan pada file log di `storage/logs/laravel.log`.

### Menggunakan SMTP Real

Untuk mengirim email melalui SMTP server, perbarui konfigurasi berikut pada file `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@medic-ecommerce.com
MAIL_FROM_NAME="${APP_NAME}"
```

## Testing

Menjalankan automated tests menggunakan Pest PHP:

```bash
php artisan test
```

Menjalankan test dengan coverage report:

```bash
php artisan test --coverage
```

## Catatan Keamanan

### Penting untuk Diperhatikan

1. **File Environment**: Jangan pernah melakukan commit file `.env` ke version control (Git). Gunakan `.env.example` sebagai template.

2. **Kredensial Default**: Segera ubah seluruh password default setelah deployment ke production environment.

3. **Application Key**: Pastikan `APP_KEY` telah di-generate dengan `php artisan key:generate`.

4. **Database Credentials**: Lindungi informasi database dengan password yang kuat dan jangan ekspos ke publik.

5. **Session Security**: Konfigurasi `SESSION_IDLE_TIMEOUT` pada file `.env` untuk mengatur durasi auto-logout.

## Kontribusi

Untuk berkontribusi pada proyek ini, silakan:
1. Fork repository
2. Buat branch fitur baru (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

## Lisensi

Proyek ini dibuat untuk keperluan pendidikan dan pembelajaran.

## Kontak

Untuk pertanyaan atau dukungan, silakan hubungi:
- Email: admin@medic-ecommerce.com
- GitHub: [@rafienajwan](https://github.com/rafienajwan)
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

