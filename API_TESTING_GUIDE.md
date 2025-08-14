# Laboratory Management System - API Testing Guide

## 🚀 **Quick Start**

The Laravel development server is running at: `http://localhost:8000`

## 📋 **Prerequisites**

1. **Database seeded with test data**:
   ```bash
   php artisan migrate:fresh --seed
   ```

2. **Server running**:
   ```bash
   php artisan serve
   ```

## 🧪 **Testing Methods**

### Method 1: Using cURL (Command Line)

### Method 2: Using Thunder Client/Postman (VS Code/Desktop)

### Method 3: Using PHP artisan tinker

---

## 🔓 **Public API Endpoints (No Authentication)**

### 1. **Site Settings**
```bash
# Get laboratory information and configuration
curl -X GET http://localhost:8000/api/site/settings
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Site settings retrieved successfully",
  "data": {
    "lab": { "name": "...", "code": "GOS", ... },
    "contact": { "email": "lab-gos@usk.ac.id", ... },
    "operational_hours": { "monday": "08:00-16:00", ... }
  }
}
```

### 2. **Staff Directory**
```bash
# Get all staff members (paginated)
curl -X GET http://localhost:8000/api/staff

# Get specific staff member
curl -X GET http://localhost:8000/api/staff/1

# Search staff by position
curl -X GET "http://localhost:8000/api/staff?position=professor&per_page=5"
```

### 3. **Articles/News**
```bash
# Get published articles
curl -X GET http://localhost:8000/api/articles

# Get specific article by slug
curl -X GET http://localhost:8000/api/articles/sample-article-slug

# Filter by category
curl -X GET "http://localhost:8000/api/articles?category=research"
```

### 4. **Equipment Catalog**
```bash
# Get all available equipment
curl -X GET http://localhost:8000/api/equipment

# Get specific equipment details
curl -X GET http://localhost:8000/api/equipment/1

# Filter available equipment only
curl -X GET "http://localhost:8000/api/equipment?available_only=1"

# Search equipment
curl -X GET "http://localhost:8000/api/equipment?search=microscope"

# Get equipment categories
curl -X GET http://localhost:8000/api/equipment/categories
```

### 5. **Submit Requests (No Auth Required)**

#### **Equipment Borrow Request**
```bash
curl -X POST http://localhost:8000/api/requests/borrow \
  -H "Content-Type: application/json" \
  -d '{
    "members": [
      {
        "name": "John Doe",
        "nim": "1234567890",
        "program": "Physics"
      }
    ],
    "supervisor_name": "Dr. Jane Smith",
    "supervisor_nip": "123456789",
    "supervisor_email": "supervisor@usk.ac.id",
    "supervisor_phone": "081234567890",
    "purpose": "Laboratory experiment for optics course",
    "borrow_date": "2025-08-20",
    "return_date": "2025-08-22",
    "start_time": "09:00",
    "end_time": "15:00",
    "equipment_items": [
      {
        "equipment_id": 1,
        "quantity_requested": 2,
        "notes": "Need for microscopy analysis"
      }
    ]
  }'
```

#### **Lab Visit Request**
```bash
curl -X POST http://localhost:8000/api/requests/visit \
  -H "Content-Type: application/json" \
  -d '{
    "full_name": "John Doe",
    "email": "john@example.com",
    "phone": "081234567890",
    "institution": "University XYZ",
    "purpose": "study-visit",
    "visit_date": "2025-08-25",
    "visit_time": "morning",
    "participants": 15,
    "additional_notes": "Student group visit for laboratory introduction",
    "agreement_accepted": true
  }'
```

#### **Testing Request**
```bash
curl -X POST http://localhost:8000/api/requests/testing \
  -H "Content-Type: application/json" \
  -d '{
    "client_name": "Dr. John Researcher",
    "client_organization": "Research Institute ABC",
    "client_email": "researcher@example.com",
    "client_phone": "081234567890",
    "client_address": "123 Research Street, City",
    "sample_name": "Optical Material Sample",
    "sample_description": "Thin film sample for optical analysis",
    "sample_quantity": "5 pieces, 2x2 cm",
    "testing_type": "uv_vis_spectroscopy",
    "testing_parameters": {
      "wavelength_range": "200-800 nm",
      "solvent": "ethanol"
    },
    "urgent_request": false,
    "preferred_date": "2025-08-30"
  }'
```

### 6. **Track Requests**
```bash
# Track borrow request (replace with actual request ID from submission)
curl -X GET http://localhost:8000/api/tracking/borrow/BR20250811001

# Track visit request
curl -X GET http://localhost:8000/api/tracking/visit/VR20250811001

# Track testing request
curl -X GET http://localhost:8000/api/tracking/testing/TR20250811001
```

---

## 🔐 **Admin API Endpoints (Authentication Required)**

### **Step 1: Create Admin User & Get Token**

First, create an admin user using tinker:
```bash
php artisan tinker
```

In tinker console:
```php
// Create super admin user
$user = \App\Models\User::create([
    'name' => 'Super Admin',
    'email' => 'superadmin@labgos.ac.id',
    'password' => 'password',
    'role' => 'super_admin',
    'is_active' => true
]);

// Create API token
$token = $user->createToken('API Token')->plainTextToken;
echo "Token: " . $token;
exit;
```

### **Step 2: Test Admin Endpoints**

#### **Get Borrow Requests (Admin)**
```bash
curl -X GET http://localhost:8000/api/admin/requests/borrow \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

#### **Approve Borrow Request**
```bash
curl -X PUT http://localhost:8000/api/admin/requests/borrow/1/approve \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "approval_notes": "Request approved for educational purposes"
  }'
```

#### **Reject Request**
```bash
curl -X PUT http://localhost:8000/api/admin/requests/borrow/1/reject \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "approval_notes": "Equipment not available on requested dates"
  }'
```

---

## 🧪 **Testing with Thunder Client (VS Code)**

1. **Install Thunder Client extension** in VS Code
2. **Create New Request**
3. **Import this collection**:

```json
{
  "clientName": "Thunder Client",
  "collectionName": "Lab API Tests",
  "requests": [
    {
      "name": "Get Site Settings",
      "method": "GET",
      "url": "http://localhost:8000/api/site/settings"
    },
    {
      "name": "Submit Borrow Request",
      "method": "POST",
      "url": "http://localhost:8000/api/requests/borrow",
      "headers": [{"name": "Content-Type", "value": "application/json"}],
      "body": {
        "type": "json",
        "raw": "{\n  \"members\": [{\n    \"name\": \"John Doe\",\n    \"nim\": \"1234567890\",\n    \"program\": \"Physics\"\n  }],\n  \"supervisor_name\": \"Dr. Jane Smith\",\n  \"supervisor_nip\": \"123456789\",\n  \"supervisor_email\": \"supervisor@usk.ac.id\",\n  \"supervisor_phone\": \"081234567890\",\n  \"purpose\": \"Laboratory experiment\",\n  \"borrow_date\": \"2025-08-20\",\n  \"return_date\": \"2025-08-22\",\n  \"start_time\": \"09:00\",\n  \"end_time\": \"15:00\",\n  \"equipment_items\": [{\n    \"equipment_id\": 1,\n    \"quantity_requested\": 2\n  }]\n}"
      }
    }
  ]
}
```

---

## 📋 **Test Scenarios to Validate**

### **Public API Tests:**
- [ ] ✅ Get site settings
- [ ] ✅ Browse equipment catalog
- [ ] ✅ View staff directory  
- [ ] ✅ Submit borrow request
- [ ] ✅ Submit visit request
- [ ] ✅ Track request status
- [ ] ⚠️ Test validation errors (invalid data)

### **Admin API Tests:**
- [ ] 🔐 Authentication required
- [ ] 🔐 Admin can view requests
- [ ] 🔐 Admin can approve requests  
- [ ] 🔐 Admin can reject requests
- [ ] 🔐 Access control works

### **Error Handling Tests:**
- [ ] ❌ Invalid endpoints return 404
- [ ] ❌ Missing required fields return validation errors
- [ ] ❌ Unauthorized access returns 401
- [ ] ❌ Invalid tokens return proper errors

---

## 🐛 **Common Testing Issues & Solutions**

### **Issue 1: "Route not found"**
```bash
# Clear route cache
php artisan route:clear
php artisan route:cache
```

### **Issue 2: "Validation failed"**
- Check required fields in API documentation above
- Ensure date formats are YYYY-MM-DD
- Verify equipment IDs exist in database

### **Issue 3: "Authentication failed"**
- Verify token is correct and not expired
- Include `Authorization: Bearer TOKEN` header
- Check user has correct role

### **Issue 4: "Database not seeded"**
```bash
php artisan migrate:fresh --seed
```

---

## 🔄 **Next Steps After Basic Testing**

1. **Load Testing**: Test with multiple concurrent requests
2. **Integration Testing**: Test complete workflows end-to-end
3. **Frontend Integration**: Connect your frontend to these APIs
4. **Production Testing**: Test with real data and proper authentication
5. **API Documentation**: Generate Swagger/OpenAPI documentation

---

## 📞 **Need Help?**

If you encounter any issues:
1. Check Laravel logs: `tail -f storage/logs/laravel.log`
2. Verify database connections: `php artisan tinker` → `DB::connection()->getPdo()`
3. Test routes: `php artisan route:list --path=api`
4. Clear caches: `php artisan optimize:clear`