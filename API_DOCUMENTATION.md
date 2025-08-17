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
GET /api/equipment?category_id=1&condition=excellent&available_only=true&search=microscope
```

**Query Parameters:**
- `category_id` - Filter by category ID
- `condition` - Filter by condition (excellent, good, fair, poor)
- `available_only` - boolean, show only available equipment
- `search` - Search in name, model, manufacturer, or category name

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
  "equipment_items": [
    {
      "equipment_id": 1,
      "quantity_requested": 1,
      "notes": "Handle with care"
    }
  ]
}
```

**📝 Validation Notes:**
- `members` array: min 1, max 10 members per request
- `equipment_items` array: min 1, max 20 items per request  
- `borrow_date`: must be after today, within 6 months
- `return_date`: must be after or equal to borrow_date
- `supervisor_nip`: required, max 50 characters
- `start_time` and `end_time` fields are **optional (nullable)**
- Phone numbers should be in Indonesian format (+62...)

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
  "client_organization": "Research Institute",
  "client_email": "analyst@company.com",
  "client_phone": "+6281234567890",
  "client_address": "Jl. Research No. 123, Jakarta 12345",
  "sample_name": "Polymer Sample A",
  "sample_description": "Polymer composite material for structural analysis",
  "sample_quantity": "2 pieces (5g each)",
  "testing_type": "ftir_spectroscopy",
  "testing_parameters": {
    "wavenumber_range": "4000-400 cm⁻¹",
    "sample_preparation": "KBr pellet method"
  },
  "urgent_request": false,
  "sample_delivery_schedule": "2025-08-25"
}
```

**📝 Field Requirements:**
- **client_name**: Required, 2-255 characters
- **client_organization**: Required, 3-255 characters
- **client_email**: Required, valid email format
- **client_phone**: Required, 10-20 characters
- **client_address**: Required, 10-500 characters
- **sample_name**: Required, 2-255 characters
- **sample_description**: Required, 10-1000 characters
- **sample_quantity**: Required, 1-100 characters (description format)
- **testing_type**: Required, valid testing type
- **testing_parameters**: Optional array, max 20 parameters
- **urgent_request**: Optional boolean, default false
- **sample_delivery_schedule**: Required date, 3 days to 3 months advance

**Available Testing Types:**
- `uv_vis_spectroscopy` - UV-Vis Spectroscopy
- `ftir_spectroscopy` - FTIR Spectroscopy  
- `optical_microscopy` - Optical Microscopy
- `custom` - Custom Testing

**Testing Parameter Requirements:**
- **uv_vis_spectroscopy**: Requires `wavelength_range`, `solvent`
- **ftir_spectroscopy**: Requires `wavenumber_range`, `sample_preparation`
- **optical_microscopy**: Requires `magnification`, `illumination_type`
- **custom**: No specific parameter requirements

**Response:**
```json
{
  "success": true,
  "message": "Sample testing request submitted successfully",
  "data": {
    "request_id": "TR20250825001",
    "status": "pending",
    "submitted_at": "2025-08-15 10:30:00",
    "tracking_url": "http://localhost:8000/api/tracking/testing/TR20250825001"
  }
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
  "message": "Testing request details retrieved successfully",
  "data": {
    "request_id": "TR20250825001",
    "status": "approved",
    "status_label": "Disetujui",
    "status_color": "success",
    "progress_percentage": 60,
    "client": {
      "name": "Dr. Sample Analyst",
      "organization": "Research Institute",
      "email": "analyst@company.com",
      "phone": "+6281234567890",
      "address": "Jl. Research No. 123, Jakarta 12345"
    },
    "sample": {
      "name": "Polymer Sample A",
      "description": "Polymer composite material for structural analysis",
      "quantity": "2 pieces (5g each)"
    },
    "testing": {
      "type": "ftir_spectroscopy",
      "type_label": "FTIR Spectroscopy",
      "parameters": {
        "wavenumber_range": "4000-400 cm⁻¹",
        "sample_preparation": "KBr pellet method"
      },
      "urgent_request": false
    },
    "schedule": {
      "sample_delivery_schedule": "2025-08-25",
      "estimated_duration": 5,
      "estimated_completion_date": "2025-08-30",
      "completion_date": null
    },
    "cost": {
      "cost": 200000
    },
    "results": {
      "summary": null,
      "files": null
    },
    "submitted_at": "2025-08-15 10:30:00",
    "reviewed_at": "2025-08-15 14:20:00",
    "reviewer": {
      "name": "Dr. Lab Admin"
    },
    "assigned_to": {
      "name": "Lab Technician"
    },
    "approval_notes": "Approved for standard testing procedure",
    "is_overdue": false,
    "timeline": [
      {
        "status": "submitted",
        "label": "Request Submitted",
        "date": "2025-08-15 10:30:00",
        "active": true
      },
      {
        "status": "approved",
        "label": "Approved",
        "date": "2025-08-15 14:20:00",
        "active": true
      }
    ]
  }
}
```

#### Cancel Testing Request
```http
DELETE /api/tracking/testing/{request_id}/cancel
```

#### Cancel Borrow Request
```http
DELETE /api/tracking/borrow/{request_id}/cancel
```

**Cancel Response:**
```json
{
  "success": true,
  "message": "Request cancelled successfully",
  "data": {
    "request_id": "TR20250825001",
    "status": "cancelled"
  }
}
```

---

## 🔒 Admin API Endpoints

> **Authentication required:** Bearer token + admin role

### 📊 Dashboard

#### Get Dashboard Statistics
```http
GET /api/admin/dashboard/stats?date_from=2025-08-01&date_to=2025-08-31
Authorization: Bearer {token}
```

**Query Parameters:**
- `date_from` - Filter from date (default: 30 days ago)
- `date_to` - Filter to date (default: today)

**Response:**
```json
{
  "success": true,
  "data": {
    "overview": {
      "total_requests": 45,
      "pending_requests": 10,
      "active_requests": 26,
      "completed_requests": 120
    },
    "requests_by_type": {
      "borrow": {
        "total": 25,
        "pending": 5,
        "approved": 8,
        "active": 7,
        "completed": 5
      },
      "visit": {
        "total": 12,
        "pending": 2,
        "approved": 4,
        "active": 3,
        "completed": 3
      },
      "testing": {
        "total": 8,
        "pending": 3,
        "approved": 2,
        "active": 2,
        "completed": 1
      }
    },
    "equipment_stats": {
      "total": 45,
      "available": 38,
      "in_use": 7,
      "maintenance": 0,
      "categories_count": 8
    },
    "recent_trends": {
      "requests_this_month": 25,
      "requests_last_month": 18,
      "growth_percentage": 38.9
    },
    "peak_usage_times": [
      { "hour": "09:00", "count": 12 },
      { "hour": "14:00", "count": 8 }
    ]
  }
}
```

#### Get Activity Logs
```http
GET /api/admin/activity-logs?page=1&per_page=20
Authorization: Bearer {token}
```

#### Get Notifications
```http
GET /api/admin/notifications
Authorization: Bearer {token}
```

### 📋 Request Management

#### Visit Request Management
```http
# List visit requests
GET /api/admin/requests/visit?status=pending&search=research
Authorization: Bearer {token}

# Get specific visit request
GET /api/admin/requests/visit/{id}
Authorization: Bearer {token}

# Update visit request
PUT /api/admin/requests/visit/{id}
Authorization: Bearer {token}

# Approve visit request
PUT /api/admin/requests/visit/{id}/approve
Authorization: Bearer {token}

# Reject visit request
PUT /api/admin/requests/visit/{id}/reject
Authorization: Bearer {token}
```

#### Testing Request Management
```http
# List testing requests
GET /api/admin/requests/testing?urgency=high&date_from=2025-08-01
Authorization: Bearer {token}

# Get specific testing request
GET /api/admin/requests/testing/{id}
Authorization: Bearer {token}

# Update testing request
PUT /api/admin/requests/testing/{id}
Authorization: Bearer {token}

# Approve testing request
PUT /api/admin/requests/testing/{id}/approve
Authorization: Bearer {token}

# Reject testing request
PUT /api/admin/requests/testing/{id}/reject
Authorization: Bearer {token}
```

#### Borrow Request Management
```http
GET /api/admin/requests/borrow?status=pending&date_from=2025-08-01&date_to=2025-08-31&search=jane
Authorization: Bearer {token}
```

**Query Parameters:**
- `status` - Filter by status (pending, approved, active, completed, rejected, cancelled)
- `date_from` - Filter from borrow date (Y-m-d format)
- `date_to` - Filter to borrow date (Y-m-d format)
- `search` - Search in request ID, supervisor name, email, purpose
- `per_page` - Items per page (default: 15, max: 100)

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

#### Equipment Categories Management
```http
# List equipment categories
GET /api/admin/equipment/categories
Authorization: Bearer {token}

# Create new category
POST /api/admin/equipment/categories
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "New Category",
  "description": "Category description"
}

# Update category
PUT /api/admin/equipment/categories/{id}
Authorization: Bearer {token}

# Delete category
DELETE /api/admin/equipment/categories/{id}
Authorization: Bearer {token}
```

#### Equipment CRUD Operations
```http
# List all equipment (admin view)
GET /api/admin/equipment
Authorization: Bearer {token}

# Create new equipment
POST /api/admin/equipment
Authorization: Bearer {token}
Content-Type: multipart/form-data

# Get specific equipment details
GET /api/admin/equipment/{id}
Authorization: Bearer {token}

# Update equipment
PUT /api/admin/equipment/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Updated Equipment Name",
  "status": "active",
  "condition_status": "good",
  "location": "Room 202"
}

# Delete equipment
DELETE /api/admin/equipment/{id}
Authorization: Bearer {token}
```

### 📂 Content Management

#### Articles Management
```http
# List articles for admin management
GET /api/admin/content/articles
Authorization: Bearer {token}

# Create new article
POST /api/admin/content/articles
Authorization: Bearer {token}
Content-Type: multipart/form-data

# Get article for editing
GET /api/admin/content/articles/{id}
Authorization: Bearer {token}

# Update article
PUT /api/admin/content/articles/{id}
Authorization: Bearer {token}

# Delete article
DELETE /api/admin/content/articles/{id}
Authorization: Bearer {token}
```

#### Staff Management
```http
# List staff members for management
GET /api/admin/content/staff
Authorization: Bearer {token}

# Create new staff member
POST /api/admin/content/staff
Authorization: Bearer {token}
Content-Type: multipart/form-data

# Get staff member details
GET /api/admin/content/staff/{id}
Authorization: Bearer {token}

# Update staff member
PUT /api/admin/content/staff/{id}
Authorization: Bearer {token}

# Delete staff member
DELETE /api/admin/content/staff/{id}
Authorization: Bearer {token}
```

#### Gallery Management
```http
# List gallery items for management
GET /api/admin/content/gallery
Authorization: Bearer {token}

# Create gallery item
POST /api/admin/content/gallery
Authorization: Bearer {token}
Content-Type: multipart/form-data

# Get gallery item
GET /api/admin/content/gallery/{id}
Authorization: Bearer {token}

# Update gallery item
PUT /api/admin/content/gallery/{id}
Authorization: Bearer {token}

# Delete gallery item
DELETE /api/admin/content/gallery/{id}
Authorization: Bearer {token}
```

#### Site Settings
```http
# Get site settings
GET /api/admin/content/site-settings
Authorization: Bearer {token}

# Update site settings
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

  async submitTestingRequest(data) {
    return this.request('/requests/testing', {
      method: 'POST',
      body: JSON.stringify(data),
    });
  }

  async trackRequest(type, requestId) {
    return this.request(`/tracking/${type}/${requestId}`);
  }

  async cancelTestingRequest(requestId) {
    return this.request(`/tracking/testing/${requestId}/cancel`, {
      method: 'DELETE',
    });
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
const borrowRequestData = {
  members: [{ name: "John", nim: "123", study_program: "Physics" }],
  supervisor_name: "Dr. Smith",
  // ... other fields
};
const borrowResult = await api.submitBorrowRequest(borrowRequestData);

// Submit testing request
const testingRequestData = {
  client_name: "Dr. Research",
  client_organization: "University Lab",
  client_email: "research@university.ac.id",
  client_phone: "+6281234567890",
  client_address: "University Campus, Room 301",
  sample_name: "Polymer Sample",
  sample_description: "Thermoplastic polymer for analysis",
  sample_quantity: "3 pieces",
  testing_type: "ftir_spectroscopy",
  testing_parameters: {
    wavenumber_range: "4000-400 cm⁻¹",
    sample_preparation: "KBr pellet"
  },
  urgent_request: false,
  sample_delivery_schedule: "2025-08-25"
};
const testingResult = await api.submitTestingRequest(testingRequestData);

// Track request
const tracking = await api.trackRequest('testing', 'TR20250825001');

// Cancel testing request
const cancelResult = await api.cancelTestingRequest('TR20250825001');
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

---

## 📊 API Endpoints Summary

### Total Endpoints: 65+

#### Public API (22 endpoints)
- **Site Information**: 1 endpoint
- **Staff Directory**: 2 endpoints
- **Articles & News**: 2 endpoints  
- **Equipment Catalog**: 3 endpoints
- **Gallery**: 2 endpoints
- **Request Submissions**: 3 endpoints
- **Request Tracking**: 4 endpoints (including cancel)

#### Admin API (35+ endpoints)
- **Dashboard & Statistics**: 3 endpoints
- **Borrow Request Management**: 5 endpoints
- **Visit Request Management**: 5 endpoints
- **Testing Request Management**: 5 endpoints
- **Equipment Categories**: 4 endpoints
- **Equipment Management**: 5 endpoints
- **Content Management**: 
  - Articles: 5 endpoints
  - Staff: 5 endpoints
  - Gallery: 5 endpoints
  - Site Settings: 2 endpoints

#### Super Admin API (6 endpoints)
- **User Management**: 6 endpoints (CRUD + status update)

### Authentication Requirements
- **Public API**: No authentication required
- **Admin API**: `auth:sanctum` + `role:admin`
- **Super Admin API**: `auth:sanctum` + `role:superadmin`

This comprehensive documentation provides everything needed for seamless frontend integration with the Lab GOS backend API.
