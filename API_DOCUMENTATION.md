# Daily Quotes API Documentation

Welcome to the official API documentation for the Daily Quotes backend.

---

## Base URLs

- **Admin Panel:** `http://localhost/live/daily-quotes-backend/public/admin`
- **Public API:** `http://localhost/live/daily-quotes-backend/public/api/v1`
- **Admin API:** `http://localhost/live/daily-quotes-backend/public/admin/api`

**Production URLs:**
- **Admin Panel:** `https://yourdomain.com/admin`
- **Public API:** `https://api.yourdomain.com/v1`
- **Admin API:** `https://yourdomain.com/admin/api`

---

## Authentication

### 1. Admin Authentication (Session-based)

The admin panel uses a standard web session for authentication. You must log in through the admin panel to receive a session cookie.

#### **POST** `/admin/login`
Authenticates an admin user.

**Request Body (`application/x-www-form-urlencoded`):**
| Parameter | Type   | Description               |
| --------- | ------ | ------------------------- |
| `email`   | string | The admin's email address. |
| `password`| string | The admin's password.     |

**Response:**
- On success, redirects to the admin dashboard with a session cookie.
- On failure, redirects back to the login page with errors.

### 2. Public API Authentication (Sanctum)

For user-specific actions on the public API, authentication is handled via Laravel Sanctum. The client must send a Bearer Token in the `Authorization` header.

**Header:**
```
Authorization: Bearer <YOUR_API_TOKEN>
```

---

## Public API Endpoints (`/v1`)

### Authentication

#### **POST** `/auth/register`
Register a new user account.

**Request Body (`application/json`):**
| Parameter | Type   | Description                    | Required |
| --------- | ------ | ------------------------------ | -------- |
| `name`    | string | User's full name               | Yes      |
| `email`   | string | User's email address           | Yes      |
| `password`| string | User's password                | Yes      |
| `password_confirmation` | string | Password confirmation | Yes      |

**Response:**
```json
{
  "success": true,
  "message": "User registered successfully",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "created_at": "2025-06-21T14:30:00.000000Z"
    },
    "token": "1|abc123...",
    "token_type": "Bearer"
  }
}
```

#### **POST** `/auth/login`
Authenticate a user and get access token.

**Request Body (`application/json`):**
| Parameter | Type   | Description          | Required |
| --------- | ------ | -------------------- | -------- |
| `email`   | string | User's email address | Yes      |
| `password`| string | User's password      | Yes      |

**Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "created_at": "2025-06-21T14:30:00.000000Z"
    },
    "token": "1|abc123...",
    "token_type": "Bearer"
  }
}
```

#### **POST** `/auth/forgot-password`
Send password reset link to user's email.

**Request Body (`application/json`):**
| Parameter | Type   | Description          | Required |
| --------- | ------ | -------------------- | -------- |
| `email`   | string | User's email address | Yes      |

**Response:**
```json
{
  "success": true,
  "message": "Password reset link sent to your email"
}
```

#### **POST** `/auth/reset-password`
Reset user password using token from email.

**Request Body (`application/json`):**
| Parameter | Type   | Description                    | Required |
| --------- | ------ | ------------------------------ | -------- |
| `token`   | string | Reset token from email         | Yes      |
| `email`   | string | User's email address           | Yes      |
| `password`| string | New password                   | Yes      |
| `password_confirmation` | string | Password confirmation | Yes      |

**Response:**
```json
{
  "success": true,
  "message": "Password reset successfully"
}
```

### User Profile (Protected Routes)

#### **GET** `/auth/profile`
Get current user's profile information.

**Headers:**
```
Authorization: Bearer <YOUR_API_TOKEN>
```

**Response:**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "created_at": "2025-06-21T14:30:00.000000Z",
      "updated_at": "2025-06-21T14:30:00.000000Z"
    }
  }
}
```

#### **PUT** `/auth/profile`
Update current user's profile information.

**Headers:**
```
Authorization: Bearer <YOUR_API_TOKEN>
```

**Request Body (`application/json`):**
| Parameter | Type   | Description          | Required |
| --------- | ------ | -------------------- | -------- |
| `name`    | string | User's full name     | No       |
| `email`   | string | User's email address | No       |

**Response:**
```json
{
  "success": true,
  "message": "Profile updated successfully",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "created_at": "2025-06-21T14:30:00.000000Z",
      "updated_at": "2025-06-21T14:35:00.000000Z"
    }
  }
}
```

#### **POST** `/auth/change-password`
Change current user's password.

**Headers:**
```
Authorization: Bearer <YOUR_API_TOKEN>
```

**Request Body (`application/json`):**
| Parameter | Type   | Description                    | Required |
| --------- | ------ | ------------------------------ | -------- |
| `current_password` | string | Current password               | Yes      |
| `password`| string | New password                   | Yes      |
| `password_confirmation` | string | Password confirmation | Yes      |

**Response:**
```json
{
  "success": true,
  "message": "Password changed successfully"
}
```

#### **POST** `/auth/logout`
Logout current user and invalidate token.

**Headers:**
```
Authorization: Bearer <YOUR_API_TOKEN>
```

**Response:**
```json
{
  "success": true,
  "message": "Logged out successfully"
}
```

#### **POST** `/auth/refresh`
Refresh current user's access token.

**Headers:**
```
Authorization: Bearer <YOUR_API_TOKEN>
```

**Response:**
```json
{
  "success": true,
  "message": "Token refreshed successfully",
  "data": {
    "token": "2|def456...",
    "token_type": "Bearer"
  }
}
```

### Quotes

#### **GET** `/quotes/today`
Retrieves the featured quote for the current day.

#### **GET** `/quotes/random`
Retrieves a random quote from the database.

#### **GET** `/quotes/{id}`
Retrieves a specific quote by its ID.
- **`id`** (integer, required): The ID of the quote.

#### **GET** `/quotes/by-category`
Retrieves quotes filtered by category.
- **`category_id`** (integer, query parameter): The category ID to filter by.

### Categories

#### **GET** `/categories`
Retrieves a list of all active categories.

#### **GET** `/categories/{id}`
Retrieves a specific category by its ID.
- **`id`** (integer, required): The ID of the category.

### User Preferences (Protected Routes)

#### **GET** `/preferences`
Get current user's preferences.

**Headers:**
```
Authorization: Bearer <YOUR_API_TOKEN>
```

#### **PUT** `/preferences`
Update current user's preferences.

**Headers:**
```
Authorization: Bearer <YOUR_API_TOKEN>
```

**Request Body (`application/json`):**
| Parameter | Type   | Description                    | Required |
| --------- | ------ | ------------------------------ | -------- |
| `notification_time` | string | Preferred notification time (HH:MM) | No |
| `notifications_enabled` | boolean | Enable/disable notifications | No |
| `preferred_categories` | array | Array of category IDs | No |

#### **POST** `/preferences/device-token`
Update user's device token for push notifications.

**Headers:**
```
Authorization: Bearer <YOUR_API_TOKEN>
```

**Request Body (`application/json`):**
| Parameter | Type   | Description          | Required |
| --------- | ------ | -------------------- | -------- |
| `device_token` | string | Device token for push notifications | Yes |

### Sharing

#### **POST** `/shares`
Share a quote on social media.

**Request Body (`application/json`):**
| Parameter | Type   | Description          | Required |
| --------- | ------ | -------------------- | -------- |
| `quote_id` | integer | ID of the quote to share | Yes |
| `platform` | string | Platform to share on (facebook, twitter, etc.) | Yes |

#### **GET** `/shares/statistics/{quoteId}`
Get sharing statistics for a specific quote.
- **`quoteId`** (integer, required): The ID of the quote.

#### **GET** `/shares/history`
Get current user's share history (Protected Route).

**Headers:**
```
Authorization: Bearer <YOUR_API_TOKEN>
```

---

## Admin API Endpoints

These endpoints require an active admin session cookie.

### Categories

#### **GET** `/admin/api/categories`
Retrieves a list of all categories.

#### **POST** `/admin/api/categories`
Creates a new category.

**Request Body (`multipart/form-data`):**
| Parameter   | Type   | Description                         |
| ----------- | ------ | ----------------------------------- |
| `name`      | string | The name of the category. (required) |
| `description`| string| A short description. (optional)      |
| `color`     | string | A hex color code (e.g., `#667eea`).  |
| `image`     | file   | An image file (jpg, png, etc.).      |

#### **GET** `/admin/api/categories/{id}`
Retrieves a specific category.
- **`id`** (integer, required): The ID of the category.

#### **POST** `/admin/api/categories/{id}`
Updates an existing category. You must include `_method: PUT` in the form data.

**Request Body (`multipart/form-data`):**
| Parameter | Type   | Description                         |
| --------- | ------ | ----------------------------------- |
| `_method` | string | **PUT** (required for updates)       |
| `name`    | string | The new name of the category.       |
| `image`   | file   | A new image file to replace the old one. |

#### **DELETE** `/admin/api/categories/{id}`
Deletes a category.
- **`id`** (integer, required): The ID of the category.

### Users

#### **GET** `/admin/api/users`
Retrieves a list of all users with pagination.

#### **GET** `/admin/api/users/{id}`
Retrieves a specific user's details.
- **`id`** (integer, required): The ID of the user.

#### **PUT** `/admin/api/users/{id}`
Updates a user's information and preferences.
- **`id`** (integer, required): The ID of the user.

**Request Body (`application/json`):**
| Parameter | Type   | Description                    | Required |
| --------- | ------ | ------------------------------ | -------- |
| `name`    | string | User's full name               | Yes      |
| `email`   | string | User's email address           | Yes      |
| `is_admin`| boolean | Whether user is admin          | No       |
| `notification_time` | string | Preferred notification time | No |
| `notifications_enabled` | boolean | Enable notifications | No |
| `preferred_categories` | array | Array of category IDs | No |

#### **GET** `/admin/api/users/export`
Exports all users to CSV format.

### Analytics

#### **GET** `/admin/api/dashboard`
Get dashboard overview statistics.

#### **GET** `/admin/api/analytics`
Get comprehensive analytics data.

#### **GET** `/admin/api/dashboard/user-analytics`
Get user-specific analytics.

#### **GET** `/admin/api/dashboard/quote-analytics`
Get quote-specific analytics.

---

## Data Models

### User Object
```json
{
  "id": 1,
  "name": "John Doe",
  "email": "john@example.com",
  "created_at": "2025-06-21T14:30:00.000000Z",
  "updated_at": "2025-06-21T14:30:00.000000Z"
}
```

### User Preferences Object
```json
{
  "id": 1,
  "user_id": 1,
  "notification_time": "09:00:00",
  "notifications_enabled": true,
  "preferred_categories": [1, 2, 3],
  "device_token": "fcm_token_here",
  "created_at": "2025-06-21T14:30:00.000000Z",
  "updated_at": "2025-06-21T14:30:00.000000Z"
}
```

### Category Object
```json
{
  "id": 1,
  "name": "Motivational",
  "slug": "motivational",
  "description": "Quotes that inspire and motivate people to achieve their goals",
  "color": "#667eea",
  "image": "categories/your_image.jpg",
  "image_url": "https://yourdomain.com/storage/categories/your_image.jpg",
  "is_active": true,
  "quotes_count": 200
}
```

### Quote Object
```json
{
  "id": 1,
  "content": "The only way to do great work is to love what you do.",
  "author": "Steve Jobs",
  "category_id": 1,
  "is_published": true,
  "view_count": 543,
  "share_count": 123,
  "category": {
    // Category Object
  }
}
```

### Share Object
```json
{
  "id": 1,
  "user_id": 1,
  "quote_id": 1,
  "platform": "facebook",
  "created_at": "2025-06-21T14:30:00.000000Z",
  "user": {
    // User Object
  },
  "quote": {
    // Quote Object
  }
}
```

---

## Error Responses

All API endpoints return consistent error responses:

### Validation Error (422)
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password field is required."]
  }
}
```

### Authentication Error (401)
```json
{
  "success": false,
  "message": "Invalid credentials"
}
```

### Not Found Error (404)
```json
{
  "success": false,
  "message": "Resource not found"
}
```

### Server Error (500)
```json
{
  "success": false,
  "message": "Internal server error"
}
```

---

## Rate Limiting

The API implements rate limiting to prevent abuse:
- **Public endpoints:** 60 requests per minute
- **Authenticated endpoints:** 120 requests per minute
- **Admin endpoints:** 300 requests per minute

---

## Support

For API support and questions, please contact:
- **Email:** support@yourdomain.com
- **Documentation:** https://yourdomain.com/docs/api 
 