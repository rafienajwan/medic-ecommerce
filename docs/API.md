# API Reference

Quick API endpoints reference for testing and development.

## Base URL
```
http://localhost:8000/api
```

---

## Authentication

```bash
# Register
POST /api/auth/register
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}

# Login
POST /api/auth/login
{
  "email": "john@example.com",
  "password": "password123"
}

# Logout (Authenticated)
POST /api/auth/logout
```

---

## Products (Public)

```bash
# List products
GET /api/products
GET /api/products?q=search&category=category-slug

# Product details
GET /api/products/{id}

# Product reviews
GET /api/products/{productId}/reviews
```

---

## Cart (Authenticated)

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

# Clear cart
DELETE /api/cart
```

---

## Orders (Authenticated)

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

# List my orders
GET /api/orders

# Order details
GET /api/orders/{id}

# Download invoice (PDF)
GET /api/orders/{id}/invoice

# Cancel order
POST /api/orders/{id}/cancel
```

---

## Vendor Registration (Authenticated)

```bash
# Check vendor status
GET /api/vendor/status

# Apply to become vendor
POST /api/vendor/apply
{
  "business_name": "Medical Store",
  "business_type": "Toko",
  "description": "Medical equipment supplier",
  "business_address": "Jl. Example No. 123",
  "business_phone": "021-1234567",
  "business_email": "info@store.com"
}

# Update vendor profile
PUT /api/vendor/profile
{ "business_name": "New Name", "description": "New desc" }
```

---

## Vendor Products (Vendor Only)

```bash
# List my products
GET /api/vendor/products

# Get categories
GET /api/vendor/products/categories

# Create product (multipart/form-data)
POST /api/vendor/products
{
  "name": "Product Name",
  "category_id": 1,
  "sku": "PROD-001",
  "description": "Product description",
  "price": 100000,
  "stock": 50,
  "image": (file upload)
}

# Update product
PUT /api/vendor/products/{id}

# Activate/Deactivate
PATCH /api/vendor/products/{id}
{ "is_active": true }

# Delete product
DELETE /api/vendor/products/{id}
```

---

## Vendor Orders (Vendor Only)

```bash
# List orders containing my products
GET /api/vendor/orders

# Update order status
PATCH /api/vendor/orders/{orderId}/status
{ "status": "pending|processing|shipped|delivered|cancelled" }

# Dashboard statistics
GET /api/vendor/dashboard/stats
```

---

## Reviews (Authenticated)

```bash
# Submit review
POST /api/reviews
{
  "product_id": 1,
  "rating": 5,
  "review": "Great product!"
}

# My reviews
GET /api/reviews/my

# Delete my review
DELETE /api/reviews/{id}
```

---

## Guest Book

```bash
# View approved testimonials (Public)
GET /api/guestbook

# Submit testimonial (Authenticated)
POST /api/guestbook
{
  "name": "John Doe",
  "email": "john@example.com",
  "message": "Great service!"
}
```

---

## Admin Endpoints (Admin Only)

All admin endpoints are prefixed with `/api/admin` and require admin role.

- **Users**: `/api/admin/users`
- **Vendors**: `/api/admin/vendors`
- **Products**: `/api/admin/products`
- **Orders**: `/api/admin/orders`
- **Guest Book**: `/api/admin/guestbook`

See controller files in `app/Http/Controllers/Api/Admin/` for detailed endpoints.

---

## Notes

- All authenticated endpoints require `Authorization: Bearer {token}` header (for API) or valid session (for web)
- File uploads use `multipart/form-data`
- Responses are in JSON format
- Dates are in ISO 8601 format
- Pagination available on list endpoints
