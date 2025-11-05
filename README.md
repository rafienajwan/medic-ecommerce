# 🏥 Medical Equipment E-Commerce Platform

A comprehensive multi-vendor marketplace for medical equipment and supplies.

Built with Laravel 11, Vue 3, Inertia.js, and Tailwind CSS.

[![Laravel](https://img.shields.io/badge/Laravel-11-red)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3-green)](https://vuejs.org)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-16-blue)](https://postgresql.org)

---

## 🌟 Key Features

### For Customers
- Browse and search medical products
- Multiple payment methods (Card, COD, PayPal)
- Shopping cart & checkout
- Order tracking & history
- Product reviews & ratings
- PDF invoice generation
- Testimonials (Guest Book)

### For Vendors
- Apply to become a vendor
- Manage products (CRUD with images)
- Track and manage orders
- Update order status
- Business profile management
- Dashboard with statistics

### For Admins
- Vendor approval workflow
- User management
- Product moderation
- Order monitoring
- Testimonial moderation
- System analytics

---

## 🚀 Quick Start

```bash
# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure database in .env
# DB_CONNECTION=pgsql
# DB_DATABASE=medic_ecommerce

# Run migrations
php artisan migrate --seed

# Build assets
npm run build

# Start server
php artisan serve
```

Visit http://localhost:8000

**Default Admin**: admin@example.com / password

---

## 📚 Documentation

- [Features Overview](docs/FEATURES.md) - Complete feature list
- [Installation Guide](docs/INSTALLATION.md) - Detailed setup instructions
- [API Reference](docs/API.md) - API endpoints documentation

---

## 🛠️ Tech Stack

- **Backend**: Laravel 11 (PHP 8.2)
- **Frontend**: Vue 3, Inertia.js
- **Styling**: Tailwind CSS
- **Database**: PostgreSQL
- **Payment**: Stripe, PayPal, COD
- **PDF**: DomPDF
- **Email**: Laravel Mail

---

## 📸 Screenshots

### Customer Interface
- Product browsing with search & filter
- Shopping cart management
- Multi-payment checkout
- Order tracking

### Vendor Dashboard
- Product management
- Order inbox with status updates
- Business statistics
- Profile settings

### Admin Panel
- Vendor approvals
- User & order management
- Product moderation
- Analytics dashboard

---

## 🔒 Security Features

- Laravel Jetstream authentication
- Two-factor authentication support
- Session-based authentication
- Auto-logout on inactivity (30 min)
- Role-based access control
- CSRF protection

---

## 📱 Mobile Responsive

100% responsive design optimized for:
- Desktop (1920px+)
- Laptop (1024px+)
- Tablet (768px+)
- Mobile (320px+)

---

## 🤝 Contributing

This is an open-source project. Contributions are welcome!

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Open a Pull Request

---

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## 👤 Author

Created as a demonstration of full-stack development capabilities with modern web technologies.

---

## 🙏 Acknowledgments

- Laravel Framework
- Vue.js Community
- Tailwind CSS
- Inertia.js Team
