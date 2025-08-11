#!/bin/bash

# Laboratory Management System API Test Script
# Usage: ./test-api.sh

BASE_URL="http://localhost:8000/api"

echo "🧪 Laboratory Management System - API Testing"
echo "============================================="
echo ""

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to test endpoint
test_endpoint() {
    local method=$1
    local endpoint=$2
    local description=$3
    local data=$4
    local token=$5
    
    echo -e "${BLUE}Testing:${NC} $description"
    echo "Endpoint: $method $endpoint"
    
    if [ "$method" = "GET" ]; then
        if [ -n "$token" ]; then
            response=$(curl -s -w "HTTPSTATUS:%{http_code}" -H "Authorization: Bearer $token" "$BASE_URL$endpoint")
        else
            response=$(curl -s -w "HTTPSTATUS:%{http_code}" "$BASE_URL$endpoint")
        fi
    elif [ "$method" = "POST" ]; then
        if [ -n "$token" ]; then
            response=$(curl -s -w "HTTPSTATUS:%{http_code}" -X POST -H "Content-Type: application/json" -H "Authorization: Bearer $token" -d "$data" "$BASE_URL$endpoint")
        else
            response=$(curl -s -w "HTTPSTATUS:%{http_code}" -X POST -H "Content-Type: application/json" -d "$data" "$BASE_URL$endpoint")
        fi
    fi
    
    http_code=$(echo $response | tr -d '\n' | sed -e 's/.*HTTPSTATUS://')
    body=$(echo $response | sed -e 's/HTTPSTATUS\:.*//g')
    
    if [ "$http_code" -eq 200 ] || [ "$http_code" -eq 201 ]; then
        echo -e "${GREEN}✅ SUCCESS${NC} (HTTP $http_code)"
        echo "Response: $(echo $body | head -c 100)..."
    else
        echo -e "${RED}❌ FAILED${NC} (HTTP $http_code)"
        echo "Response: $body"
    fi
    echo ""
}

echo "1. Testing Public Endpoints (No Authentication)"
echo "------------------------------------------------"

# Test site settings
test_endpoint "GET" "/site/settings" "Site Settings"

# Test staff directory
test_endpoint "GET" "/staff" "Staff Directory"

# Test equipment catalog
test_endpoint "GET" "/equipment" "Equipment Catalog"

# Test articles
test_endpoint "GET" "/articles" "Published Articles"

# Test equipment categories
test_endpoint "GET" "/equipment/categories" "Equipment Categories"

echo "2. Testing Request Submission"
echo "-----------------------------"

# Test borrow request submission
borrow_data='{
  "members": [{"name": "Test Student", "nim": "1234567890", "program": "Physics"}],
  "supervisor_name": "Dr. Test Supervisor",
  "supervisor_nip": "123456789",
  "supervisor_email": "supervisor@test.com",
  "supervisor_phone": "081234567890",
  "purpose": "Testing API endpoint",
  "borrow_date": "2025-08-20",
  "return_date": "2025-08-22",
  "start_time": "09:00",
  "end_time": "15:00",
  "equipment_items": [{"equipment_id": 1, "quantity_requested": 1}]
}'

test_endpoint "POST" "/requests/borrow" "Equipment Borrow Request" "$borrow_data"

echo "3. Testing Authentication Required Endpoints"
echo "--------------------------------------------"
echo "Note: These will fail without valid admin token"
echo "Run: php artisan tinker"
echo "Then: \$user = App\\Models\\User::where('role', 'super_admin')->first();"
echo "      \$token = \$user->createToken('API Token')->plainTextToken;"
echo ""

# Test admin endpoint (will fail without token)
test_endpoint "GET" "/admin/dashboard/stats" "Admin Dashboard (No Token - Should Fail)"

echo "🏁 API Testing Complete!"
echo ""
echo "For detailed testing instructions, see: API_TESTING_GUIDE.md"
echo "To test admin endpoints, create a token first using Laravel Tinker"