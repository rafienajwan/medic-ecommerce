# 🏥 Medic E-Commerce Platform

Platform marketplace multi-vendor untuk peralatan dan perlengkapan medis.

[![Laravel](https://img.shields.io/badge/Laravel-11-red)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3-green)](https://vuejs.org)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-16-blue)](https://postgresql.org)

> **⚠️ SECURITY NOTICE**: This project contains a `.env` file with sensitive credentials. Never commit `.env` to git. Use `.env.example` for templates only.

---

## 📋 Daftar Isi

- [Fitur Utama](#-fitur-utama)
- [Instalasi](#-instalasi)
- [Konfigurasi Email](#-konfigurasi-email)
- [Penggunaan](#-penggunaan)
- [API Reference](#-api-reference)
- [Tech Stack](#-tech-stack)

---

## 🌟 Fitur Utama

### 🛒 Untuk Customer
- **Belanja Produk**: Browse, search, filter berdasarkan kategori
- **Multiple Payment**: Prepaid (Card), Cash on Delivery (COD), PayPal
- **Shopping Cart**: Add/remove items, update quantity
- **Order Management**: Tracking status, download invoice PDF
- **Product Reviews**: Rating dan review produk yang sudah dibeli
- **Guest Book**: Testimonial (bisa diakses tanpa login/anonymous)
- **Email Notifications**: Invoice otomatis dikirim setelah checkout

### 🏪 Untuk Vendor
- **Vendor Registration**: Apply menjadi vendor dengan approval admin
- **Product Management**: CRUD produk dengan upload gambar
- **Order Management**: Kelola pesanan, update status pengiriman
- **Dashboard**: Statistik produk, pesanan, revenue, low stock alert
- **Profile Management**: Update info bisnis, kontak, legal docs
- **Payment Confirmation**: Terima pembayaran setelah customer konfirmasi delivery

### 👨‍💼 Untuk Admin
- **Vendor Approval**: Review dan approve/reject aplikasi vendor
- **User Management**: Kelola user dan assign role
- **Product Moderation**: Monitor semua produk dari vendor
- **Order Monitoring**: Track semua pesanan di sistem
- **Testimonial Moderation**: Approve/reject testimonial customer
- **Analytics**: Reports dan statistik sistem

---

## ⚡ Instalasi

### Prerequisites
- PHP 8.2+
- Composer
- Node.js & NPM
- PostgreSQL 16+
- Git

### Langkah Instalasi

```bash
# 1. Clone repository
git clone <repository-url>
cd medic-ecommerce

# 2. Install dependencies
composer install
npm install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Konfigurasi database di .env
# DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_PORT=5432
# DB_DATABASE=medic_ecommerce
# DB_USERNAME=your_username
# DB_PASSWORD=your_password

# 5. Jalankan migrations & seeder
php artisan migrate --seed

# 6. Setup storage link
php artisan storage:link

# 7. Build frontend assets
npm run build

# 8. Start development server
php artisan serve
```

Akses aplikasi di: **http://localhost:8000**

### Default Login

```
Admin: admin@example.com / password
Customer: customer@example.com / password
Vendor: vendor@example.com / password
```

---

## 📧 Konfigurasi Email

> **⚠️ SECURITY WARNING**: Never commit your `.env` file or expose real credentials in code/docs. Always use `.env.example` with placeholder values.

### Setup Gmail SMTP

1. **Aktifkan 2-Step Verification** di Google Account
2. **Buat App Password**:
   - Buka: https://myaccount.google.com/security
   - Pilih "App passwords"
   - Generate password untuk "Mail" → "Other device"
   - Copy App Password (16 karakter)

3. **Update .env** (gunakan kredensial ASLI Anda):
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-actual-email@gmail.com
MAIL_PASSWORD=xxxxxxxxxxxxxxxx  # 16 char App Password dari step 2
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@medic-ecommerce.com"
MAIL_FROM_NAME="Medic E-Commerce"
QUEUE_CONNECTION=sync  # Email langsung terkirim
```

4. **Test Email**:
```bash
php artisan tinker
Mail::raw('Test', function($m) { $m->to('your-test@example.com')->subject('Test'); });
```

> **🔐 Important**: If you previously committed real credentials, rotate them immediately:
> 1. Revoke the Gmail App Password at https://myaccount.google.com/apppasswords
> 2. Generate a new App Password
> 3. Update your `.env` with the new password

### Fitur Email yang Aktif
- ✅ **Invoice Email**: Auto-send setelah checkout dengan PDF attachment
- ✅ **Password Reset**: Email reset password dengan template Indonesia
- ✅ **Team Invitation**: Email undangan team (Jetstream)

---

## 🎯 Penggunaan

### Customer Flow
1. **Register** dengan alamat lengkap (nama, email, password, phone, alamat, kota, provinsi, kode pos)
2. **Browse Products** → Filter kategori, search
3. **Add to Cart** → Update quantity
4. **Checkout** → Pilih metode pembayaran
5. **Track Order** → Lihat status, download invoice
6. **Review Product** → Beri rating setelah produk diterima
7. **Testimonial** → Tulis di Guest Book (opsional)

### Vendor Flow
1. **Login** sebagai customer
2. **Apply Vendor** → Isi form bisnis, tunggu approval
3. **Add Products** → Upload gambar, set harga & stock
4. **Manage Orders** → Update status: pending → processing → shipped → delivered
5. **Confirm Delivery** → Customer konfirmasi → Payment received
6. **Dashboard** → Monitor statistik & low stock

### Admin Flow
1. **Login** sebagai admin
2. **Approve Vendors** → Review aplikasi vendor
3. **Monitor Products** → Moderasi produk listing
4. **Track Orders** → Monitor semua pesanan
5. **Moderate Testimonials** → Approve guest book entries

---

## � API Reference

Base URL: `http://localhost:8000/api`

### Authentication

```bash
# Register
POST /api/auth/register
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "phone": "08123456789",
  "address": "Jl. Example No. 123",
  "city": "Jakarta",
  "province": "DKI Jakarta",
  "postal_code": "12345"
}

# Login
POST /api/auth/login
{ "email": "john@example.com", "password": "password123" }
```

### Products (Public)

```bash
# List products
GET /api/products?q=search&category=slug

# Product details
GET /api/products/{id}

# Product reviews
GET /api/products/{productId}/reviews
```

### Cart (Authenticated)

```bash
# View cart
GET /api/cart

# Add to cart
POST /api/cart
{ "product_id": 1, "quantity": 2 }

# Update quantity
PATCH /api/cart/{itemId}
{ "quantity": 3 }

# Remove item
DELETE /api/cart/{itemId}
```

### Orders (Authenticated)

```bash
# Create order
POST /api/orders
{
  "payment_method": "prepaid|cod|paypal",
  "shipping_address": "Full address",
  "shipping_city": "Jakarta",
  "shipping_province": "DKI Jakarta",
  "shipping_postal_code": "12345",
  "shipping_phone": "08123456789"
}

# List orders
GET /api/orders

# Download invoice
GET /api/orders/{id}/invoice

# Confirm delivery (Customer)
POST /api/orders/{id}/confirm-delivery
```

### Vendor (Vendor Only)

```bash
# Apply to become vendor
POST /api/vendor/apply
{
  "business_name": "Medical Store",
  "business_type": "Toko",
  "description": "Medical equipment supplier"
}

# Create product
POST /api/vendor/products
{
  "name": "Product Name",
  "category_id": 1,
  "sku": "PROD-001",
  "price": 100000,
  "stock": 50,
  "image": (file)
}

# Update order status
PATCH /api/vendor/orders/{id}/status
{ "status": "processing|shipped|delivered" }

# Confirm delivery received
POST /api/vendor/orders/{id}/confirm-delivery
```

### Guest Book (Public)

```bash
# View testimonials (approved only)
GET /api/guestbook

# Submit testimonial (Guest or Logged-in)
POST /api/guestbook
{
  "name": "John Doe",        # Optional for guests
  "email": "john@example.com", # Optional for guests
  "message": "Great service!"  # Required
}
```

### Admin (Admin Only)

```bash
# Approve vendor
POST /api/admin/vendors/{id}/approve

# Reject vendor
POST /api/admin/vendors/{id}/reject
{ "reason": "Incomplete documents" }

# Approve testimonial
POST /api/admin/guestbook/{id}/approve

# Delete testimonial
DELETE /api/admin/guestbook/{id}
```

---

## 🛠️ Tech Stack

### Backend
- **Laravel 11**: PHP Framework
- **PostgreSQL 16**: Database
- **Laravel Jetstream**: Authentication scaffolding
- **Laravel Fortify**: Auth backend
- **DomPDF**: PDF invoice generation
- **Laravel Mail**: Email notifications

### Frontend
- **Vue 3**: Progressive JavaScript framework
- **Inertia.js**: Modern monolith architecture
- **Tailwind CSS**: Utility-first CSS
- **Vite**: Fast build tool
- **Axios**: HTTP client

### Features
- **Multi-role System**: Customer, Vendor, Admin
- **Payment Gateway**: Prepaid, COD, PayPal
- **Image Upload**: Local storage (`storage/app/public`)
- **Session Management**: Auto-logout 30 min inactivity
- **Responsive Design**: Mobile-first approach

---

## 🔒 Security

- ✅ Laravel Jetstream authentication
- ✅ Two-Factor Authentication (Coming Soon)
- ✅ Session-based auth with CSRF protection
- ✅ Role-based access control (RBAC)
- ✅ Auto-logout on inactivity (30 minutes)
- ✅ Password hashing (bcrypt)
- ✅ SQL injection protection (Eloquent ORM)

---

## 📱 Mobile Responsive

100% responsive di semua device:
- Desktop (1920px+)
- Laptop (1024px+)
- Tablet (768px+)
- Mobile (320px+)

---

## � Testing

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=ProductTest

# Clear cache before testing
php artisan optimize:clear
php artisan test
```

---

## 🚀 Production Deployment

```bash
# 1. Set production environment
APP_ENV=production
APP_DEBUG=false

# 2. Build optimized assets
npm run build

# 3. Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4. Set permissions
chmod -R 755 storage bootstrap/cache

# 5. Setup queue worker (for emails)
php artisan queue:work --daemon
```

---

## 🐛 Troubleshooting

### White Screen / 500 Error
```bash
php artisan optimize:clear
npm run build
```

### Storage Link Not Working
```bash
php artisan storage:link
```

### Migration Errors
```bash
php artisan migrate:fresh --seed
```

### Email Not Sending
- Pastikan `MAIL_PASSWORD` adalah App Password (bukan password Gmail biasa)
- Pastikan tidak ada spasi di App Password
- Cek log: `storage/logs/laravel.log`
- Test manual: `php artisan tinker`
- **Jika kredensial pernah ter-commit ke git**: Rotate segera di https://myaccount.google.com/apppasswords

---

## 📄 License

Open-source software licensed under [MIT License](https://opensource.org/licenses/MIT).

---

## 👤 Author

Developed by Jasmine - Full-stack Developer

**Tech Stack Expertise:**
- Backend: Laravel, PHP, PostgreSQL
- Frontend: Vue.js, Inertia.js, Tailwind CSS
- DevOps: Git, Docker, CI/CD

---

## 🙏 Acknowledgments

- Laravel Framework Team
- Vue.js Community
- Tailwind CSS
- Inertia.js Team
- PostgreSQL Community
