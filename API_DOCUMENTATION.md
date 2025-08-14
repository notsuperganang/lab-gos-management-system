# 🔗 Lab GOS API Documentation

**Comprehensive Frontend Integration Guide**

> Complete API documentation for the Laboratory Gelombang, Optik dan Spektroskopi (GOS) Management System

---

## 📋 Table of Contents

- [🚀 Quick Start](#-quick-start)
- [🔐 Authentication](#-authentication)
- [📊 API Response Format](#-api-response-format)
- [🌐 Public API Endpoints](#-public-api-endpoints)
- [🔒 Admin API Endpoints](#-admin-api-endpoints)
- [👑 Super Admin API Endpoints](#-super-admin-api-endpoints)
- [🚫 Error Handling](#-error-handling)
- [📝 Request Examples](#-request-examples)

---

## 🚀 Quick Start

### Base URL
```
Production: https://your-domain.com/api
Development: http://localhost:8000/api
```

### API Architecture
The system follows a **three-tier structure**:
- **Public API** (`/api/`) - No authentication required
- **Admin API** (`/api/admin/`) - Requires authentication + admin role
- **Super Admin API** (`/api/superadmin/`) - Requires authentication + superadmin role

---

## 🔐 Authentication

The API uses **Laravel Sanctum** for token-based authentication.

### Login (Web-based)
```http
POST /login
Content-Type: application/json

{
  "email": "admin@labgos.ac.id",
  "password": "password"
}
```

### Get User Token (After Login)
```http
GET /api/user
Authorization: Bearer {token}
```

### Headers for Authenticated Requests
```http
Authorization: Bearer {your-sanctum-token}
Content-Type: application/json
Accept: application/json
```

---

## 📊 API Response Format

All API responses follow a consistent format using the `ApiResponse` helper:

### Success Response
```json
{
  "success": true,
  "message": "Operation completed successfully",
  "data": {
    // Response data here
  },
  "meta": {
    // Additional metadata (pagination, filters, etc.)
  }
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    // Validation errors or error details
  }
}
```

### Paginated Response
```json
{
  "success": true,
  "message": "Data retrieved successfully",
  "data": [
    // Array of resources
  ],
  "meta": {
    "pagination": {
      "current_page": 1,
      "last_page": 5,
      "per_page": 15,
      "total": 75,
      "from": 1,
      "to": 15,
      "has_more_pages": true
    }
  }
}
```

---

## 🌐 Public API Endpoints

> **No authentication required** - Accessible by frontend users

### 🏛️ Site Information

#### Get Site Settings
```http
GET /api/site/settings
```

**Response:**
```json
{
  "success": true,
  "data": {
    "lab_name": "Laboratorium Gelombang, Optik dan Spektroskopi",
    "lab_code": "GOS",
    "department": "Departemen Fisika FMIPA USK",
    "vision": "Lab vision statement...",
    "contact": {
      "phone": "+62651755555",
      "email": "lab-gos@usk.ac.id"
    },
    "operational_hours": {
      "monday": "08:00-16:00",
      "friday": "08:00-11:30"
    }
  }
}
```

### 👥 Staff Members

#### Get All Staff
```http
GET /api/staff
```

#### Get Staff Detail
```http
GET /api/staff/{id}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Dr. John Doe",
    "position": "Lab Manager",
    "specialization": "Optical Physics",
    "email": "john.doe@usk.ac.id",
    "phone": "+6281234567890",
    "avatar_url": "https://domain.com/storage/staff/avatar.jpg"
  }
}
```

### 📚 Articles

#### Get Articles List
```http
GET /api/articles
```

**Query Parameters:**
- `page` - Page number (default: 1)
- `per_page` - Items per page (max: 100)
- `search` - Search in title/content

#### Get Article Detail
```http
GET /api/articles/{slug}
```

### 🔬 Equipment

#### Get Equipment Categories
```http
GET /api/equipment/categories
```

#### Get Equipment List
```http
GET /api/equipment?category_id=1&available=true
```

**Query Parameters:**
- `category_id` - Filter by category
- `available` - Show only available equipment
- `search` - Search in name/model

#### Get Equipment Detail
```http
GET /api/equipment/{id}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "FTIR Spectrometer",
    "model": "Frontier MIR/NIR",
    "manufacturer": "PerkinElmer",
    "specifications": "Resolution: 0.5 cm⁻¹, Range: 7800-350 cm⁻¹",
    "total_quantity": 1,
    "available_quantity": 1,
    "status": "active",
    "condition_status": "excellent",
    "location": "Room 201",
    "image_url": "https://domain.com/storage/equipment/ftir.jpg",
    "category": {
      "id": 1,
      "name": "Spectroscopy"
    },
    "is_available": true,
    "status_color": "success",
    "condition_color": "success"
  }
}
```

### 📷 Gallery

#### Get Gallery Images
```http
GET /api/gallery
```

#### Get Gallery Detail
```http
GET /api/gallery/{id}
```

### 📝 Request Submissions

#### Submit Equipment Borrow Request
```http
POST /api/requests/borrow
Content-Type: application/json

{
  "members": [
    {
      "name": "John Student",
      "nim": "1234567890",
      "study_program": "Physics"
    }
  ],
  "supervisor_name": "Dr. Jane Smith",
  "supervisor_nip": "197001011998021001",
  "supervisor_email": "jane.smith@usk.ac.id",
  "supervisor_phone": "+6281234567890",
  "purpose": "Research experiment for thesis",
  "borrow_date": "2025-08-20",
  "return_date": "2025-08-21",
  "start_time": "08:30",
  "end_time": "15:00",
  "equipment_items": [
    {
      "equipment_id": 1,
      "quantity_requested": 1,
      "notes": "Handle with care"
    }
  ]
}
```

**Response:**
```json
{
  "success": true,
  "message": "Equipment borrow request submitted successfully",
  "data": {
    "request_id": "BR20250820001",
    "status": "pending",
    "submitted_at": "2025-08-15 10:30:00",
    "tracking_url": "http://localhost:8000/api/tracking/borrow/BR20250820001"
  }
}
```

#### Submit Lab Visit Request
```http
POST /api/requests/visit
Content-Type: application/json

{
  "visitor_name": "Dr. Research Team",
  "visitor_email": "research@university.ac.id",
  "visitor_phone": "+6281234567890",
  "institution": "Universitas Indonesia",
  "group_size": 5,
  "visit_date": "2025-08-25",
  "start_time": "09:00",
  "end_time": "12:00",
  "visit_purpose": "research",
  "purpose_description": "Collaborative research on optical materials",
  "equipment_needed": ["FTIR", "UV-Vis"],
  "special_requirements": "Need clean room access"
}
```

#### Submit Testing Service Request
```http
POST /api/requests/testing
Content-Type: application/json

{
  "client_name": "Dr. Sample Analyst",
  "client_email": "analyst@company.com",
  "client_phone": "+6281234567890",
  "institution": "Research Institute",
  "testing_type": "ftir",
  "sample_description": "Polymer composite material",
  "sample_quantity": 2,
  "parameters": {
    "wavenumber_range": "4000-400 cm⁻¹",
    "resolution": "4 cm⁻¹",
    "sample_preparation": "KBr pellet"
  },
  "expected_results": "Identification of functional groups",
  "urgency": "normal",
  "additional_notes": "Samples are temperature sensitive"
}
```

### 🔍 Request Tracking

#### Track Borrow Request
```http
GET /api/tracking/borrow/{request_id}
```

#### Track Visit Request
```http
GET /api/tracking/visit/{request_id}
```

#### Track Testing Request
```http
GET /api/tracking/testing/{request_id}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "request_id": "BR20250820001",
    "status": "approved",
    "current_step": 2,
    "total_steps": 5,
    "timeline": [
      {
        "step": 1,
        "title": "Request Submitted",
        "status": "completed",
        "date": "2025-08-15 10:30:00"
      },
      {
        "step": 2,
        "title": "Under Review",
        "status": "completed",
        "date": "2025-08-15 14:20:00"
      },
      {
        "step": 3,
        "title": "Approved",
        "status": "current",
        "date": "2025-08-15 16:45:00"
      }
    ],
    "details": {
      "supervisor_name": "Dr. Jane Smith",
      "borrow_date": "2025-08-20",
      "equipment_count": 1
    }
  }
}
```

---

## 🔒 Admin API Endpoints

> **Authentication required:** Bearer token + admin role

### 📊 Dashboard

#### Get Dashboard Statistics
```http
GET /api/admin/dashboard/stats
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "pending_requests": {
      "borrow": 5,
      "visit": 2,
      "testing": 3
    },
    "active_requests": {
      "borrow": 12,
      "visit": 8,
      "testing": 6
    },
    "equipment_stats": {
      "total": 45,
      "available": 38,
      "in_use": 7,
      "maintenance": 0
    },
    "recent_activity": 25
  }
}
```

#### Get Activity Logs
```http
GET /api/admin/activity-logs?page=1&per_page=20
Authorization: Bearer {token}
```

### 📋 Request Management

#### Get Borrow Requests
```http
GET /api/admin/requests/borrow
Authorization: Bearer {token}
```

**Query Parameters:**
- `status` - Filter by status (pending, approved, active, completed, rejected, cancelled)
- `date_from` - Filter from date (Y-m-d)
- `date_to` - Filter to date (Y-m-d)
- `search` - Search in request ID, supervisor name, email, purpose
- `per_page` - Items per page (max: 100)

#### Get Borrow Request Detail
```http
GET /api/admin/requests/borrow/{id}
Authorization: Bearer {token}
```

#### Approve Borrow Request
```http
PUT /api/admin/requests/borrow/{id}/approve
Authorization: Bearer {token}
Content-Type: application/json

{
  "approval_notes": "Approved for research purposes",
  "special_instructions": "Equipment must be returned by 5 PM"
}
```

#### Reject Borrow Request
```http
PUT /api/admin/requests/borrow/{id}/reject
Authorization: Bearer {token}
Content-Type: application/json

{
  "rejection_reason": "Equipment not available on requested date",
  "alternative_suggestions": "Available next week"
}
```

#### Update Borrow Request Status
```http
PUT /api/admin/requests/borrow/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "status": "active",
  "admin_notes": "Equipment picked up successfully"
}
```

### 🔧 Equipment Management

#### Get Equipment List (Admin)
```http
GET /api/admin/equipment
Authorization: Bearer {token}
```

#### Create Equipment
```http
POST /api/admin/equipment
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
  "name": "New Equipment",
  "model": "Model X",
  "manufacturer": "Manufacturer ABC",
  "category_id": 1,
  "specifications": "Technical specifications...",
  "total_quantity": 1,
  "purchase_date": "2025-01-15",
  "location": "Room 201",
  "image": "[file upload]",
  "manual": "[file upload]",
  "notes": "Special handling required"
}
```

#### Update Equipment
```http
PUT /api/admin/equipment/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Updated Equipment Name",
  "status": "active",
  "condition_status": "good",
  "location": "Room 202"
}
```

#### Delete Equipment
```http
DELETE /api/admin/equipment/{id}
Authorization: Bearer {token}
```

### 📂 Content Management

#### Get Articles (Admin)
```http
GET /api/admin/content/articles
Authorization: Bearer {token}
```

#### Create Article
```http
POST /api/admin/content/articles
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
  "title": "New Article Title",
  "slug": "new-article-slug",
  "excerpt": "Article excerpt...",
  "content": "Full article content...",
  "featured_image": "[file upload]",
  "status": "published",
  "published_at": "2025-08-15 10:00:00"
}
```

#### Update Site Settings
```http
PUT /api/admin/content/site-settings
Authorization: Bearer {token}
Content-Type: application/json

{
  "lab_name": "Updated Lab Name",
  "contact_phone": "+62651755555",
  "operational_hours": {
    "monday": "08:00-16:00",
    "friday": "08:00-11:30"
  }
}
```

---

## 👑 Super Admin API Endpoints

> **Authentication required:** Bearer token + superadmin role

### 👥 User Management

#### Get Users List
```http
GET /api/superadmin/users
Authorization: Bearer {token}
```

#### Create User
```http
POST /api/superadmin/users
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "New Admin User",
  "email": "newadmin@labgos.ac.id",
  "password": "securepassword",
  "password_confirmation": "securepassword",
  "role": "admin"
}
```

#### Update User
```http
PUT /api/superadmin/users/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Updated Name",
  "email": "updated@labgos.ac.id",
  "role": "admin"
}
```

#### Update User Status
```http
PUT /api/superadmin/users/{id}/status
Authorization: Bearer {token}
Content-Type: application/json

{
  "status": "active"
}
```

#### Delete User
```http
DELETE /api/superadmin/users/{id}
Authorization: Bearer {token}
```

---

## 🚫 Error Handling

### HTTP Status Codes
- `200` - Success
- `201` - Created
- `204` - No Content
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Internal Server Error

### Common Error Responses

#### Validation Error (422)
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

#### Unauthorized (401)
```json
{
  "success": false,
  "message": "Unauthorized access"
}
```

#### Not Found (404)
```json
{
  "success": false,
  "message": "Resource not found"
}
```

#### Server Error (500)
```json
{
  "success": false,
  "message": "Internal server error"
}
```

---

## 📝 Request Examples

### Frontend Integration Example (JavaScript)

```javascript
// Configure base API client
const API_BASE_URL = 'http://localhost:8000/api';

class LabGOSAPI {
  constructor() {
    this.token = localStorage.getItem('auth_token');
  }

  async request(endpoint, options = {}) {
    const url = `${API_BASE_URL}${endpoint}`;
    const config = {
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        ...options.headers,
      },
      ...options,
    };

    if (this.token) {
      config.headers.Authorization = `Bearer ${this.token}`;
    }

    const response = await fetch(url, config);
    const data = await response.json();

    if (!response.ok) {
      throw new Error(data.message || 'API request failed');
    }

    return data;
  }

  // Equipment methods
  async getEquipment(params = {}) {
    const query = new URLSearchParams(params).toString();
    return this.request(`/equipment?${query}`);
  }

  async getEquipmentDetail(id) {
    return this.request(`/equipment/${id}`);
  }

  // Request submission methods
  async submitBorrowRequest(data) {
    return this.request('/requests/borrow', {
      method: 'POST',
      body: JSON.stringify(data),
    });
  }

  async trackRequest(type, requestId) {
    return this.request(`/tracking/${type}/${requestId}`);
  }

  // Admin methods (require authentication)
  async getAdminRequests(type, params = {}) {
    const query = new URLSearchParams(params).toString();
    return this.request(`/admin/requests/${type}?${query}`);
  }

  async approveRequest(type, id, data = {}) {
    return this.request(`/admin/requests/${type}/${id}/approve`, {
      method: 'PUT',
      body: JSON.stringify(data),
    });
  }
}

// Usage example
const api = new LabGOSAPI();

// Get equipment list
const equipment = await api.getEquipment({ 
  category_id: 1, 
  available: true 
});

// Submit borrow request
const requestData = {
  members: [{ name: "John", nim: "123", study_program: "Physics" }],
  supervisor_name: "Dr. Smith",
  // ... other fields
};
const result = await api.submitBorrowRequest(requestData);

// Track request
const tracking = await api.trackRequest('borrow', 'BR20250820001');
```

### React Hook Example

```javascript
// useLabGOS.js - Custom React hook
import { useState, useEffect } from 'react';

export function useEquipment(params = {}) {
  const [equipment, setEquipment] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    async function fetchEquipment() {
      try {
        setLoading(true);
        const api = new LabGOSAPI();
        const response = await api.getEquipment(params);
        setEquipment(response.data);
      } catch (err) {
        setError(err.message);
      } finally {
        setLoading(false);
      }
    }

    fetchEquipment();
  }, [JSON.stringify(params)]);

  return { equipment, loading, error };
}

// Component usage
function EquipmentList() {
  const { equipment, loading, error } = useEquipment({ 
    available: true 
  });

  if (loading) return <div>Loading...</div>;
  if (error) return <div>Error: {error}</div>;

  return (
    <div>
      {equipment.map(item => (
        <div key={item.id}>
          <h3>{item.name}</h3>
          <p>Available: {item.available_quantity}</p>
        </div>
      ))}
    </div>
  );
}
```

---

## 🎯 Key Notes for Frontend Integration

1. **Authentication Flow**: Use web-based login, then access `/api/user` to get user details
2. **File Uploads**: Use `multipart/form-data` for equipment images, manuals, article images
3. **Pagination**: All list endpoints support pagination with `page` and `per_page` parameters
4. **Real-time Updates**: Consider implementing WebSocket or polling for request status updates
5. **Error Handling**: Always check the `success` field in responses
6. **Validation**: Frontend should mirror API validation rules for better UX
7. **Status Colors**: Use provided `status_color` and `condition_color` for consistent UI

This documentation provides everything needed for seamless frontend integration with the Lab GOS backend API.