# GitHub Copilot Instructions - Lab GOS Management System

This Laravel-based laboratory management system serves Laboratorium Gelombang, Optik dan Spektroskopi (GOS) at Universitas Syiah Kuala, handling equipment rentals, lab visits, testing services, and content management.

## Core Architecture

### Three-Tier API Structure
- **Public API** (`/api/`) - Equipment catalogs, request submissions, tracking (no auth)
- **Admin API** (`/api/admin/`) - Request management, approvals, equipment CRUD (Sanctum auth)
- **Super Admin API** (`/api/superadmin/`) - User management, system administration (role-based)

### Key Technology Stack
- **Laravel 12.x** with PHP 8.2+, **Sanctum** for API auth, **Spatie** packages for permissions & activity logging
- **Frontend**: Alpine.js 3.14.9 + Tailwind CSS with Vite hot reloading
- **Manual WhatsApp Contact**: User-initiated messaging via tracking pages (no automated services)

## Development Commands

### Primary Development Workflow
```bash
composer dev    # Main command: starts Laravel + queue + logs + Vite concurrently
composer test   # Full PHPUnit suite with config cache clearing
php artisan pint # Laravel Pint code formatting
```

### Essential Setup Commands
```bash
cp .env.example .env && php artisan key:generate
php artisan storage:link
php artisan migrate:fresh --seed
```

## Critical File Patterns

### Request Lifecycle Models
All request models (`BorrowRequest`, `VisitRequest`, `TestingRequest`) include:
- Status workflow validation: `canTransitionTo()` and `getValidNextStatuses()` methods
- Equipment stock management: `reserveQuantity()` and `releaseQuantity()` for real-time availability
- Activity logging via Spatie traits for complete audit trails

### Service Classes Architecture
- `DashboardService`: Cached analytics with 5-minute TTL, complex aggregations
- `BorrowLetterService`/`TestingLetterService`/`VisitLetterService`: PDF generation via DomPDF
- `VisitSlotsService`: Time slot management with capacity validation

### File Storage Convention
- **Structure**: `storage/app/public/{category}/` (equipment/, articles/, gallery/, staff/)
- **Access**: Models use accessors like `getFeaturedImageUrlAttribute()` returning `asset('storage/path')`
- **Cleanup**: Automatic file deletion in model observers

### Configuration Pattern
- Lab-specific config in `config/lab.php` for operational hours, services, SOP compliance
- Environment variables: `WHATSAPP_LAB_PHONE` for manual contact, CORS settings for frontend integration

## Data Model Conventions

### Status Workflow System
```php
// All request models follow this pattern
public function canTransitionTo(string $newStatus): bool
public function getValidNextStatuses(): array
```

### Activity Logging Pattern
```php
use Spatie\Activitylog\Traits\LogsActivity;

// Automatic logging with model observers
public function getActivitylogOptions(): LogOptions
{
    return LogOptions::defaults()
        ->logOnly(['field1', 'field2'])
        ->logOnlyDirty();
}
```

### Equipment Stock Management
```php
// Real-time availability tracking
public function reserveQuantity(int $quantity): bool
public function releaseQuantity(int $quantity): void
public function getAvailableQuantityAttribute(): int
```

## Frontend Integration Patterns

### Alpine.js Store Pattern
Complex state management uses Alpine stores (see `resources/js/app.js`):
```javascript
Alpine.store('scrollAnimations', {
    // Throttled scroll handling with RAF optimization
});
```

### API Response Format
All API endpoints use consistent `ApiResponse` helper:
```json
{
  "success": true,
  "message": "Operation completed",
  "data": {},
  "meta": {} // pagination, filters
}
```

## Testing & Quality Assurance

### Test Structure
- **Feature Tests**: End-to-end API workflows in `tests/Feature/`
- **Unit Tests**: Service class logic in `tests/Unit/`
- Run with `composer test` (includes config cache clearing)

### Code Quality
- **Laravel Pint**: `php artisan pint` for consistent formatting
- **Activity Logging**: All model changes automatically tracked
- **Validation**: Request classes in `app/Http/Requests/` with custom rules

## Critical Environment Variables

```bash
# Manual WhatsApp contact (user-initiated only)
WHATSAPP_LAB_PHONE=+62651755555

# Frontend CORS integration
FRONTEND_URL=http://localhost:3000
SANCTUM_STATEFUL_DOMAINS=localhost:3000,localhost:8080

# Lab configuration
LAB_NAME="Laboratorium Gelombang, Optik dan Spektroskopi"
LAB_CODE="GOS"
```

## Common Pitfalls to Avoid

1. **File Storage**: Always use `storage/app/public/` with symbolic links, never `public/` directly
2. **Status Transitions**: Use model validation methods before changing request statuses
3. **Queue Processing**: Equipment reservations use queued jobs - ensure `queue:listen` is running
4. **Activity Logging**: Don't manually log activities - use Spatie traits and model observers
5. **WhatsApp Integration**: System uses manual contact only - no automated messaging services

When modifying request workflows, always update the corresponding status validation methods and ensure proper activity logging is maintained.