# Installation & Setup Guide

## Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js & NPM
- PostgreSQL
- Git

---

## Installation Steps

### 1. Clone Repository

```bash
git clone <repository-url>
cd medic-ecommerce
```

### 2. Install Dependencies

```bash
# PHP dependencies
composer install

# JavaScript dependencies
npm install
```

### 3. Environment Setup

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Database Configuration

Edit `.env` file:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=medic_ecommerce
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 5. Run Migrations

```bash
# Create database tables
php artisan migrate

# (Optional) Seed sample data
php artisan db:seed
```

### 6. Storage Setup

```bash
# Create storage link for product images
php artisan storage:link
```

### 7. Build Frontend Assets

```bash
# Development
npm run dev

# Production
npm run build
```

### 8. Start Server

```bash
# Development server
php artisan serve

# Access at http://localhost:8000
```

---

## Default Admin Account

If you ran the seeder:

```
Email: admin@example.com
Password: password
```

---

## Testing the Application

### Customer Flow
1. Register → http://localhost:8000/register
2. Browse products → http://localhost:8000/products
3. Add to cart
4. Checkout → Choose payment method
5. View orders → http://localhost:8000/orders

### Vendor Flow
1. Login as customer
2. Apply to become vendor → http://localhost:8000/vendor/apply
3. Wait for admin approval (or approve via admin panel)
4. Access vendor dashboard → http://localhost:8000/vendor/dashboard
5. Add products → http://localhost:8000/vendor/products/create
6. Manage orders → http://localhost:8000/vendor/orders

### Admin Flow
1. Login as admin
2. Access admin panel → http://localhost:8000/admin
3. Approve vendors → http://localhost:8000/admin/vendors
4. Monitor orders → http://localhost:8000/admin/orders
5. Manage users → http://localhost:8000/admin/users

---

## Common Issues

### Migration Errors
```bash
# Reset database
php artisan migrate:fresh --seed
```

### Storage Permission Issues
```bash
# Fix permissions (Linux/Mac)
chmod -R 775 storage bootstrap/cache

# Fix permissions (Windows - run as administrator)
icacls storage /grant Users:F /T
icacls bootstrap\cache /grant Users:F /T
```

### Frontend Not Loading
```bash
# Clear cache
npm run build
php artisan optimize:clear
```

### Images Not Displaying
```bash
# Recreate storage link
php artisan storage:link
```

---

## Development Commands

```bash
# Watch for changes (Hot reload)
npm run dev

# Run tests
php artisan test

# Clear all caches
php artisan optimize:clear

# Queue worker (for emails)
php artisan queue:work
```

---

## Production Deployment

1. Set environment to production in `.env`:
   ```env
   APP_ENV=production
   APP_DEBUG=false
   ```

2. Build optimized assets:
   ```bash
   npm run build
   ```

3. Optimize Laravel:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

4. Set proper permissions:
   ```bash
   chmod -R 755 storage bootstrap/cache
   ```

5. Use process manager (PM2, Supervisor) for queue worker

---

## Support

For issues or questions, check:
- Laravel Documentation: https://laravel.com/docs
- Vue.js Guide: https://vuejs.org/guide
- Inertia.js Docs: https://inertiajs.com
