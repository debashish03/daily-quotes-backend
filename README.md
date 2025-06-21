<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# Daily Quotes Backend API Documentation

This document provides a detailed overview of the Daily Quotes Backend API.

---

## 1. Authentication

### Admin Authentication
Admin access is handled through a session-based login system.

#### **Admin Login**
- **Endpoint:** `/admin/login`
- **Method:** `POST`
- **Description:** Authenticates an admin user.
- **Body:**
  ```json
  {
    "email": "admin@dailyquotes.com",
    "password": "admin123"
  }
  ```
- **Response:**
  - On success, it creates a session and redirects to the `/admin/dashboard`.
  - On failure, it redirects back with validation errors.

#### **Admin Logout**
- **Endpoint:** `/admin/logout`
- **Method:** `POST`
- **Description:** Logs out the authenticated admin user.
- **Response:**
  - Redirects to the `/admin/login` page.

---

## 2. Admin API Endpoints

All admin API endpoints are protected by the `auth:admin` middleware and are prefixed with `/admin/api`.

### **Dashboard & Analytics**
- **Endpoint:** `/admin/api/dashboard` - **GET** - Main dashboard stats.
- **Endpoint:** `/admin/api/analytics` - **GET** - Detailed analytics.
- **Endpoint:** `/admin/api/user/analytics` - **GET** - User-specific analytics.
- **Endpoint:** `/admin/api/quote/analytics` - **GET** - Quote-specific analytics.

### **Users**
- **Endpoint:** `/admin/users` - **GET** - List all users.
- **Endpoint:** `/admin/users/{id}` - **GET** - Get a specific user.
- **Endpoint:** `/admin/users/{id}` - **PUT** - Update a user.
- **Endpoint:** `/admin/users/{id}` - **DELETE** - Delete a user.

### **Quotes**
- **Endpoint:** `/admin/quotes` - **GET** - List all quotes.
- **Endpoint:** `/admin/quotes` - **POST** - Create a new quote.
- **Endpoint:** `/admin/quotes/{id}` - **GET** - Get a specific quote.
- **Endpoint:** `/admin/quotes/{id}` - **PUT** - Update a quote.
- **Endpoint:** `/admin/quotes/{id}` - **DELETE** - Delete a quote.

### **Categories**
- **Endpoint:** `/admin/categories` - **GET** - List all categories.
- **Endpoint:** `/admin/categories` - **POST** - Create a new category.
- **Endpoint:** `/admin/categories/{id}` - **GET** - Get a specific category.
- **Endpoint:** `/admin/categories/{id}` - **PUT** - Update a category.
- **Endpoint:** `/admin/categories/{id}` - **DELETE** - Delete a category.

---

## 3. Public API Endpoints

All public API endpoints are prefixed with `/api/v1`.

### **Quotes**
- **Endpoint:** `/api/v1/quotes/today` - **GET** - Get today's featured quote.
- **Endpoint:** `/api/v1/quotes/random` - **GET** - Get a random quote.
- **Endpoint:** `/api/v1/quotes/by-category?category_id={id}` - **GET** - Get quotes by category.
- **Endpoint:** `/api/v1/quotes/{id}` - **GET** - Get a specific quote.

### **Categories**
- **Endpoint:** `/api/v1/categories` - **GET** - List all active categories.
- **Endpoint:** `/api/v1/categories/{id}` - **GET** - Get a specific category.

### **Shares**
- **Endpoint:** `/api/v1/shares` - **POST** - Share a quote.
- **Endpoint:** `/api/v1/shares/statistics/{quoteId}` - **GET** - Get share statistics.

### **User Preferences (Requires Sanctum Authentication)**
- **Endpoint:** `/api/v1/preferences` - **GET** - Get user preferences.
- **Endpoint:** `/api/v1/preferences` - **PUT** - Update user preferences.
- **Endpoint:** `/api/v1/preferences/device-token` - **POST** - Update device token.
- **Endpoint:** `/api/v1/shares/history` - **GET** - Get user's share history.

---

## 4. Example Usage

### **Get All Categories (Public)**
**Request:**
```http
GET /api/v1/categories
```
**Response (200 OK):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Motivational",
      "slug": "motivational",
      "description": "Quotes that inspire and motivate...",
      "color": "#667eea",
      "image": "categories/image.jpg",
      "image_url": "http://localhost/storage/categories/image.jpg",
      "is_active": true,
      "created_at": "...",
      "updated_at": "...",
      "quotes_count": 200
    }
  ]
}
```

### **Create a Category (Admin)**
**Request:**
```http
POST /admin/categories
Content-Type: multipart/form-data
Accept: application/json

// Form Data
name=New Category
description=A new category for testing.
color=#ff0000
image=@/path/to/your/image.png
```
**Response (201 Created):**
```json
{
  "success": true,
  "data": {
    "id": 9,
    "name": "New Category",
    "slug": "new-category",
    "description": "A new category for testing.",
    "color": "#ff0000",
    "image": "categories/your-image.png",
    "is_active": true,
    "created_at": "...",
    "updated_at": "..."
  },
  "message": "Category created successfully"
}
```

---

## 5. Error Responses

#### **Validation Error (422)**
```json
{
  "success": false,
  "message": "The given data was invalid.",
  "errors": {
    "name": [
      "The name field is required."
    ]
  }
}
```

#### **Not Found (404)**
```json
{
  "success": false,
  "message": "Resource not found."
}
```

#### **Unauthorized (401/403)**
```json
{
  "success": false,
  "message": "Unauthenticated."
}
```
---

This documentation should provide a clear guide for any developer working with the API. Let me know if you'd like more detailed examples or explanations for specific endpoints.
