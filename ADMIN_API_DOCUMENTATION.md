# Admin API Documentation

This document provides comprehensive documentation for all Admin API endpoints in the Laboratorium GOS system.

## Table of Contents

- [Authentication](#authentication)
- [Base URL](#base-url)
- [Response Format](#response-format)
- [Error Handling](#error-handling)
- [Dashboard API](#dashboard-api)
- [Authentication API](#authentication-api)

## Authentication

All Admin API endpoints require Sanctum Bearer token authentication with admin or super_admin role.

### Requirements
- Valid Sanctum Bearer token obtained from `/api/admin/login`
- Admin or Super Admin role
- Token included in Authorization header

### Login Process
1. Login via `/api/admin/login` with email and password
2. Receive Bearer token in response
3. Include token in all subsequent API calls

### Headers
```http
Accept: application/json
Content-Type: application/json
Authorization: Bearer {bearer_token}
```

### Authentication Errors
```json
{
  "success": false,
  "message": "Unauthenticated"
}
```

## Base URL

```
/api/admin
```

All admin endpoints are prefixed with `/api/admin` and use Sanctum Bearer token authentication.

## Response Format

All API responses follow a consistent format:

### Success Response
```json
{
  "success": true,
  "message": "Operation completed successfully",
  "data": {
    // Response data
  },
  "meta": {
    // Optional metadata
    "timestamp": "2025-08-19T13:36:02.000000Z",
    "timezone": "UTC",
    "cache_duration": 900
  }
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error description",
  "errors": {
    // Validation errors (if applicable)
  }
}
```

## Error Handling

### HTTP Status Codes

| Code | Description |
|------|-------------|
| `200` | Success |
| `302` | Redirect to login (unauthenticated) |
| `401` | Unauthorized |
| `403` | Forbidden (insufficient permissions) |
| `422` | Validation Error |
| `500` | Internal Server Error |

### Validation Errors
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field_name": [
      "Validation error message"
    ]
  }
}
```

---

# Dashboard API

The Dashboard API provides comprehensive statistics and analytics for the admin dashboard.

## Endpoints Overview

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/admin/dashboard/stats` | Get dashboard statistics |
| `GET` | `/api/admin/activity-logs` | Get activity logs |
| `GET` | `/api/admin/notifications` | Get admin notifications |

---

## Get Dashboard Statistics

Retrieves comprehensive dashboard statistics including request summaries, equipment analytics, trends, and system alerts.

### Request

```http
GET /api/admin/dashboard/stats
```

### Query Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `date_from` | string | No | 30 days ago | Start date (YYYY-MM-DD format) |
| `date_to` | string | Required if `date_from` provided | Today | End date (YYYY-MM-DD format) |
| `refresh_cache` | boolean | No | `false` | Force cache refresh (accepts `1`/`0`, `true`/`false`) |

### Validation Rules

- `date_from` must be a valid date
- `date_to` must be a valid date
- `date_from` must be before or equal to `date_to`
- `refresh_cache` must be a boolean value

### Example Request

```bash
curl -X GET "http://127.0.0.1:8000/api/admin/dashboard/stats?date_from=2025-08-01&date_to=2025-08-19&refresh_cache=1" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {bearer_token}"
```

### Response

```json
{
  "success": true,
  "message": "Dashboard statistics retrieved successfully",
  "data": {
    "summary": {
      "total_pending_requests": 46,
      "total_equipment": 11,
      "available_equipment": 72,
      "equipment_utilization_rate": 18.3,
      "recent_activity_count": 0,
      "pending_trend": 475,
      "total_active_requests": 9,
      "pending_borrow_requests": 16,
      "active_borrow_requests": 3,
      "pending_visit_requests": 16,
      "active_visit_requests": 4,
      "pending_testing_requests": 14,
      "active_testing_requests": 2
    },
    "equipment_analytics": {
      "total_count": 12,
      "availability": {
        "available": 10,
        "fully_utilized": 1,
        "low_stock": 0
      },
      "status_distribution": {
        "active": 11,
        "maintenance": 1,
        "retired": 0
      }
    },
    "request_analytics": {
      "period_summary": {
        "total_requests": 83,
        "borrow_requests": 27,
        "visit_requests": 25,
        "testing_requests": 31
      }
    },
    "trend_data": {
      "daily_trends": [
        {
          "date": "2025-08-10",
          "borrow_requests": 8,
          "visit_requests": 10,
          "testing_requests": 10
        },
        {
          "date": "2025-08-11",
          "borrow_requests": 1,
          "visit_requests": 1,
          "testing_requests": 1
        }
      ]
    },
    "quick_insights": {
      "most_requested_equipment": [
        {
          "id": 10,
          "name": "Digital Caliper",
          "category": "Peralatan Umum",
          "request_count": 10,
          "total_quantity": 11,
          "avg_quantity": 1.1
        },
        {
          "id": 1,
          "name": "UV-Vis Spectrophotometer",
          "category": "Spektroskopi",
          "request_count": 7,
          "total_quantity": 7,
          "avg_quantity": 1
        }
      ]
    },
    "alerts": [
      {
        "type": "warning",
        "title": "Overdue Requests",
        "message": "16 requests have been pending for more than 3 days",
        "count": 16,
        "category": "requests",
        "created_at": "2025-08-19 13:36:02"
      },
      {
        "type": "error",
        "title": "Maintenance Overdue",
        "message": "4 equipment items require immediate maintenance",
        "count": 4,
        "category": "equipment",
        "created_at": "2025-08-19 13:36:02"
      }
    ]
  },
  "meta": {
    "timestamp": "2025-08-19T13:36:02.000000Z",
    "timezone": "UTC",
    "cache_duration": 900
  }
}
```

### Response Fields

#### Summary Object
| Field | Type | Description |
|-------|------|-------------|
| `total_pending_requests` | integer | Total pending requests across all types |
| `total_equipment` | integer | Total equipment items in system |
| `available_equipment` | integer | Equipment items available for use |
| `equipment_utilization_rate` | float | Percentage of equipment currently in use |
| `recent_activity_count` | integer | Number of recent activities |
| `pending_trend` | integer | Trend in pending requests (positive = increase) |
| `total_active_requests` | integer | Currently active requests |
| `pending_borrow_requests` | integer | Pending equipment borrowing requests |
| `active_borrow_requests` | integer | Active equipment borrowing requests |
| `pending_visit_requests` | integer | Pending lab visit requests |
| `active_visit_requests` | integer | Active lab visit requests |
| `pending_testing_requests` | integer | Pending testing service requests |
| `active_testing_requests` | integer | Active testing service requests |

#### Equipment Analytics
| Field | Type | Description |
|-------|------|-------------|
| `total_count` | integer | Total number of equipment items |
| `availability.available` | integer | Equipment items available |
| `availability.fully_utilized` | integer | Equipment items fully booked |
| `availability.low_stock` | integer | Equipment items with low stock |
| `status_distribution.active` | integer | Active equipment items |
| `status_distribution.maintenance` | integer | Equipment items under maintenance |
| `status_distribution.retired` | integer | Retired equipment items |

#### Quick Insights
| Field | Type | Description |
|-------|------|-------------|
| `most_requested_equipment` | array | Top 5 most requested equipment items with usage statistics |

#### Alerts
| Field | Type | Description |
|-------|------|-------------|
| `type` | string | Alert type (`info`, `warning`, `error`) |
| `title` | string | Alert title |
| `message` | string | Alert description |
| `count` | integer | Number of items related to alert |
| `category` | string | Alert category (`requests`, `equipment`, `system`) |
| `created_at` | string | Alert timestamp |

---

## Get Activity Logs

Retrieves paginated activity logs with optional filtering.

### Request

```http
GET /api/admin/activity-logs
```

### Query Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `per_page` | integer | No | `10` | Items per page (1-100) |
| `page` | integer | No | `1` | Page number |
| `type` | string | No | - | Filter by activity type (`created`, `updated`, `deleted`) |
| `user_id` | integer | No | - | Filter by user ID |
| `search` | string | No | - | Search in activity descriptions |
| `date_from` | string | No | - | Start date filter |
| `date_to` | string | No | - | End date filter |

### Example Request

```bash
curl -X GET "http://127.0.0.1:8000/api/admin/activity-logs?per_page=5&type=created" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {bearer_token}"
```

### Response

```json
{
  "success": true,
  "message": "Activity logs retrieved successfully",
  "data": {
    "data": [
      {
        "id": 1,
        "log_name": "default",
        "description": "Equipment created",
        "event": "created",
        "subject_type": "App\\Models\\Equipment",
        "subject_id": 1,
        "causer_type": "App\\Models\\User",
        "causer_id": 1,
        "causer": {
          "id": 1,
          "name": "Admin User",
          "email": "admin@labgos.ac.id"
        },
        "properties": {
          "attributes": {
            "name": "UV-Vis Spectrophotometer",
            "category_id": 1
          }
        },
        "created_at": "2025-08-19T13:36:02.000000Z",
        "created_at_human": "2 minutes ago",
        "created_at_iso": "2025-08-19T13:36:02+00:00",
        "category": "equipment",
        "importance": "medium",
        "icon": "plus-circle",
        "color": "green"
      }
    ]
  },
  "meta": {
    "total": 1,
    "timestamp": "2025-08-19T13:38:15.000000Z",
    "filters_applied": ["type"]
  }
}
```

---

## Get Notifications

Retrieves admin notifications (placeholder endpoint for future implementation).

### Request

```http
GET /api/admin/notifications
```

### Example Request

```bash
curl -X GET "http://127.0.0.1:8000/api/admin/notifications" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {bearer_token}"
```

### Response

```json
{
  "success": true,
  "message": "Notifications retrieved successfully",
  "data": []
}
```

---

## Frontend Integration

### JavaScript Usage

The dashboard endpoints are designed to work seamlessly with the existing AdminAPI client in `resources/js/app.js`:

```javascript
// Get dashboard stats
const stats = await AdminAPI.getDashboardStats('2025-08-01', '2025-08-19');
console.log(stats.data.summary.total_pending_requests);

// Get activity logs
const activities = await AdminAPI.getActivityLogs(1, 10);

// Get notifications
const notifications = await AdminAPI.getNotifications();
```

### Alpine.js Integration

The response structure is optimized for Alpine.js components:

```javascript
// Dashboard stats structure matches Alpine component expectations
this.stats.summary.total_pending_requests
this.stats.equipment_analytics.availability.available
this.stats.quick_insights.most_requested_equipment
```

## Performance Notes

- **Caching**: Dashboard stats are cached for 15 minutes by default
- **Optimization**: Uses single optimized database queries with subqueries
- **Response Size**: Dashboard stats typically ~4-5KB
- **Rate Limiting**: Session-based, follows Laravel's built-in protections

## Error Examples

### Validation Error
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "date_from": ["The date from field must be a valid date."],
    "date_to": ["The date to field must be after or equal to date from."]
  }
}
```

### Authentication Error
```json
{
  "message": "Unauthenticated."
}
```

### Authorization Error
## Articles Management

### Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/admin/content/articles | List articles with filters & pagination |
| POST | /api/admin/content/articles | Create new article |
| GET | /api/admin/content/articles/{id} | Retrieve single article |
| PUT | /api/admin/content/articles/{id} | Update article |
| DELETE | /api/admin/content/articles/{id} | Delete article |

All endpoints require Authorization: Bearer <admin_token> and role admin or super_admin.

### Query Parameters (GET index)
- search: string (search in title, excerpt, content)
- category: research|news|announcement|publication
- is_published: 1|0
- author: partial author_name match
- page: integer
- per_page: integer (max 100)

### Request: Create Article (multipart/form-data)
Field | Type | Required | Notes
----- | ---- | -------- | -----
title | string | yes | max 255
excerpt | string | no | max 500
content | string | yes | HTML/Markdown
category | enum | yes | research, news, announcement, publication
tags[] | string[] | no | up to 10 items, each max 50 chars
is_published | boolean | no | default false
is_featured | boolean | no | only one featured globally (DB constraint)
published_at | datetime | no | default now if is_published=true and omitted
featured_image | file(image) | no | jpeg/png/jpg/gif/webp <=5MB

### Request: Update Article (multipart/form-data)
Same as create; additionally support:
- _method=PUT when using POST override
- remove_featured_image=1 to delete existing image

### Response: Paginated List
{
  "success": true,
  "message": "Articles retrieved successfully",
  "data": [ ArticleResource, ... ],
  "pagination": {
    "total": int,
    "per_page": int,
    "current_page": int,
    "last_page": int,
    "from": int,
    "to": int
  },
  "meta": {
    "filters": { search, category, is_published, author },
    "categories": { "research": "Riset", ... }
  }
}

### ArticleResource Structure
Field | Type | Description
----- | ---- | -----------
id | int | Article ID
title | string | Title
slug | string | Unique slug
excerpt | string | Excerpt or generated summary
content | string | Raw stored content
featured_image_path | string|null | Storage path
featured_image_url | string | Public URL or placeholder
author_name | string | Stored author name
category | string | Category key
category_label | string | Localized label
tags | string[] | Tags array
is_published | boolean | Publication flag
published_at | ISO datetime|null | Publication timestamp
published_at_formatted | string|null | Y-m-d H:i:s
published_by | int|null | Publisher user ID
publisher | object|null | { id, name, email }
views_count | int | View counter
reading_time | int | Estimated minutes
status | string | Published|Draft
status_badge | object | { text, color }
created_at / updated_at | ISO datetime | Timestamps
created_at_formatted / updated_at_formatted | string | Formatted timestamps
created_at_human / updated_at_human | string | Human diff
word_count | int | Word count of content
character_count | int | Character length
has_featured_image | boolean | Derived
tag_count | int | Tag length
is_recent | boolean | Created < 7 days
can_edit | boolean | Permission flag
can_delete | boolean | Permission flag
can_publish | boolean | Permission flag

### Errors
Validation errors return 422 with structure:
{
  "success": false,
  "message": "Validation failed",
  "errors": { field: [messages...] }
}

Generic failures return 500 with message and optional debug info.

### Notes
- Only one article can be featured at a time (enforced via virtual column + unique index + model saving logic).
- Slug auto-generated server-side; UI shows preview but server authoritative.
- Removing featured image sets path to null and deletes old file.
```json
{
  "message": "This action is unauthorized."
}
```

# Authentication API

The Authentication API provides login and logout functionality for admin users using Sanctum Bearer tokens.

## Admin Login

Login endpoint for admin and super admin users.

### Request
```http
POST /api/admin/login
```

### Request Body
```json
{
  "email": "admin@example.com",
  "password": "password"
}
```

### Example Request
```bash
curl -X POST "http://127.0.0.1:8000/api/admin/login" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "ganangsetyohadi@gmail.com",
    "password": "password"
  }'
```

### Success Response
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "Ganang Setyo Hadi S.Kom",
      "email": "ganangsetyohadi@gmail.com",
      "role": "super_admin"
    },
    "token": "1|abc123...",
    "token_type": "Bearer"
  }
}
```

## Admin Logout

Logout endpoint that invalidates the current Bearer token.

### Request
```http
POST /api/admin/logout
```

### Headers
```http
Authorization: Bearer {bearer_token}
```

### Example Request
```bash
curl -X POST "http://127.0.0.1:8000/api/admin/logout" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer {bearer_token}"
```

### Success Response
```json
{
  "success": true,
  "message": "Logged out successfully"
}
```

---

## Changelog

### Version 1.1.0 - 2025-08-20
- **BREAKING**: Migrated from session-based to Sanctum Bearer token authentication
- Updated all endpoints to use `/api/admin` prefix instead of `/admin-api`
- Added login/logout endpoints for token management
- Improved security with stateless token authentication
- Updated documentation examples and headers

### Version 1.0.0 - 2025-08-19
- Initial dashboard API implementation
- Session-based authentication (deprecated)
- Comprehensive dashboard statistics
- Activity logs with filtering
- Basic notifications endpoint
- Full test coverage and validation