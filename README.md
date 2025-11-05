# 🏥 Medic E-Commerce Platform

A comprehensive e-commerce platform for medical equipment and supplies built with Laravel 11, Inertia.js, and Vue 3.

**BNSP Certification Project - 100% Use Case Coverage**

## 🎯 Project Status

- ✅ **Backend API**: 16 endpoints implemented
- ✅ **Frontend UI**: 5 new pages implemented  
- ✅ **Use Cases**: 21/21 (100% coverage)
- ✅ **Database**: 15 tables with seeded data
- ✅ **Documentation**: Complete with testing guides

[![Laravel](https://img.shields.io/badge/Laravel-11-red)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3-green)](https://vuejs.org)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-16-blue)](https://postgresql.org)

## 📋 Deskripsi

Sistem e-commerce marketplace alat kesehatan dengan fitur lengkap termasuk manajemen produk, keranjang belanja, pembayaran multi-metode, generate invoice PDF, dan admin panel. **Aplikasi dilengkapi dengan sistem keamanan auto-logout setelah 60 detik tidak ada aktivitas.**

## ✨ Features

### 🛍️ Customer Features
- **Browse Products** - Lihat katalog produk (tanpa perlu login)
- **Product Search & Filter** - Cari dan filter produk berdasarkan kategori
- **Shopping Cart** - Keranjang belanja (requires login)
- **Checkout** - Proses pembelian dengan 4 metode pembayaran:
  - Prepaid (Credit/Debit Card)
  - Postpaid (Pay on Delivery)
  - Cash on Delivery (COD)
  - PayPal
- **Order History** - Lihat riwayat pesanan
- **PDF Invoice** - Download invoice otomatis setelah checkout
- **Auto-Logout** - Otomatis logout setelah 60 detik tidak aktif
- **Become a Vendor** - Customer bisa mendaftar jadi vendor (perlu approval admin)

### 🏪 Vendor Features (Customer yang sudah approved)
- **Apply to become vendor** - Submit application dengan data usaha
- **Business Information** - NPWP, SIUP/NIB, alamat usaha
- **Vendor Dashboard** - Kelola toko sendiri
- **Approval Status** - Lihat status permohonan (pending/approved/rejected)
- **Rejection Reason** - Jika ditolak, lihat alasan penolakan dari admin

### 👨‍💼 Admin Features
- ✅ **Vendor Management** - Approve/reject aplikasi vendor dengan alasan
- ✅ **Pending Applications** - Lihat daftar vendor yang menunggu approval
- ✅ **Vendor Details** - Review data lengkap vendor (NPWP, SIUP, dll)
- ✅ **Product Management** (CRUD produk)
- ✅ **Order Management** (update status pesanan)
- ✅ **Monthly Reports** (laporan transaksi bulanan)
- ✅ **Audit Logs** (tracking semua aktivitas)

### 🔒 Keamanan
- ✅ Password hashing (Bcrypt)
- ✅ Session TTL 60 detik dengan sliding window
- ✅ HttpOnly & Secure cookies
- ✅ CSRF protection
- ✅ Data encryption untuk informasi sensitif
- ✅ Client & server-side idle detection

## 🛠️ Tech Stack

| Layer | Technology |
|-------|------------|
| Backend | Laravel 11 (PHP 8.3) |
| Frontend | Vue 3 + Inertia.js + Tailwind CSS |
| Database | PostgreSQL 16 |
| PDF | Laravel DomPDF |
| Auth | Laravel Jetstream + Sanctum |
| Build | Vite |

## 📦 Instalasi

### Prerequisites
- PHP 8.3+, PostgreSQL 16+, Composer, Node.js 18+, Git

### Quick Start

```bash
# 1. Clone & navigate
git clone <repo-url>
cd medic-ecommerce

# 2. Install dependencies
composer install
npm install --legacy-peer-deps

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Configure database (.env)
DB_CONNECTION=pgsql
DB_DATABASE=medic_ecommerce
DB_USERNAME=postgres
DB_PASSWORD=your_password

# 5. Create database
createdb medic_ecommerce

# 6. Migrate & seed
php artisan migrate
php artisan db:seed

# 7. Build assets
npm run build

# 8. Start server
php artisan serve
```

Visit: http://localhost:8000

## 👥 Demo Accounts

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@medic-ecommerce.com | password |
| Vendor | vendor1@medic-ecommerce.com | password |
| Customer | customer1@example.com | password |

## 🔐 Auto-Logout (60 Detik)

**Server-Side:**
- Middleware `SessionExpiry` checks `last_activity`
- Session expires if idle > 60 seconds
- Sliding window extends session on each request

**Client-Side:**
- `useIdleDetector` composable tracks user activity
- Events: mouse, keyboard, touch, scroll, visibility
- Heartbeat ping every 30 seconds
- Warning 20 seconds before timeout

## 📊 Database Schema

![Database ERD](docs/database-erd.png) *(coming soon)*

**Core Tables:**
- `users`, `vendors`, `categories`, `products`
- `carts`, `cart_items`, `orders`, `order_items`
- `payments`, `sessions`, `audit_logs`

## 🧪 Testing

```bash
php artisan test
php artisan test --coverage
```

## 📝 API Documentation

### Authentication
```http
POST /api/auth/register
POST /api/auth/login
POST /api/auth/logout
GET  /api/auth/me
```

### Products
```http
GET  /api/products?q=search&category=slug
GET  /api/products/{id}
```

### Cart (requires login)
```http
GET    /api/cart
POST   /api/cart
PATCH  /api/cart/{itemId}
DELETE /api/cart/{itemId}
```

### Orders (requires login)
```http
GET  /api/orders
POST /api/checkout
GET  /api/orders/{id}/invoice
```

### Vendor Application (requires login)
```http
GET  /api/vendor/status          # Check vendor status
POST /api/vendor/apply           # Apply to become vendor
PUT  /api/vendor/update          # Update vendor info (if pending/rejected)
GET  /api/vendor                 # Get vendor details
```

### Admin (requires admin role)
```http
GET  /api/admin/vendors/pending       # Pending vendor applications
POST /api/admin/vendors/{id}/approve  # Approve vendor
POST /api/admin/vendors/{id}/reject   # Reject with reason
GET  /api/admin/products
GET  /api/admin/reports/monthly
```

## 🐳 Docker (Optional)

```bash
docker-compose up -d
```

## 📧 Support

Proyek ini dibuat untuk sertifikasi BNSP. Untuk pertanyaan, hubungi developer.

---

**© 2025 - BNSP Junior Web Programmer Certification Project**
