<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Equipment extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'category_id',
        'model',
        'manufacturer',
        'specifications',
        'total_quantity',
        'available_quantity',
        'status',
        'condition_status',
        'purchase_date',
        'purchase_price',
        'location',
        'image_path',
        'manual_file_path',
        'notes',
        'last_maintenance_date',
        'next_maintenance_date',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'specifications' => 'array',
            'total_quantity' => 'integer',
            'available_quantity' => 'integer',
            'purchase_date' => 'date',
            'purchase_price' => 'decimal:2',
            'last_maintenance_date' => 'date',
            'next_maintenance_date' => 'date',
        ];
    }

    /**
     * Get the category that owns the equipment.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the borrow request items for this equipment.
     */
    public function borrowRequestItems()
    {
        return $this->hasMany(BorrowRequestItem::class);
    }

    /**
     * Get the active borrow requests for this equipment.
     */
    public function activeBorrowRequests()
    {
        return $this->borrowRequestItems()
            ->whereHas('borrowRequest', function ($query) {
                $query->whereIn('status', ['approved', 'active']);
            });
    }

    /**
     * Scope a query to only include active equipment.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include available equipment.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'active')
                    ->where('available_quantity', '>', 0);
    }

    /**
     * Scope a query to filter by category.
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope a query to filter by condition.
     */
    public function scopeByCondition($query, $condition)
    {
        return $query->where('condition_status', $condition);
    }

    /**
     * Get the equipment image URL.
     */
    public function getImageUrlAttribute()
    {
        if ($this->image_path) {
            $fullPath = storage_path('app/public/' . $this->image_path);
            if (file_exists($fullPath)) {
                return asset('storage/' . $this->image_path);
            }
        }

        return asset('assets/images/placeholder.svg');
    }

    /**
     * Get the manual file URL.
     */
    public function getManualUrlAttribute()
    {
        if ($this->manual_file_path) {
            return asset('storage/' . $this->manual_file_path);
        }

        return null;
    }

    /**
     * Check if equipment is available for borrowing.
     */
    public function isAvailable($quantity = 1): bool
    {
        return $this->status === 'active' && $this->available_quantity >= $quantity;
    }

    /**
     * Get the current quantity of equipment that is borrowed (approved or active borrow requests).
     */
    public function getCurrentBorrowedQuantity(): int
    {
        return $this->borrowRequestItems()
            ->whereHas('borrowRequest', function ($query) {
                $query->whereIn('status', ['approved', 'active']);
            })
            ->sum('quantity_approved');
    }

    /**
     * Get the maximum available quantity considering currently borrowed equipment.
     */
    public function getMaxAvailableQuantity(): int
    {
        $currentlyBorrowed = $this->getCurrentBorrowedQuantity();
        return max(0, $this->total_quantity - $currentlyBorrowed);
    }

    /**
     * Check if the requested available quantity is valid considering borrowed equipment.
     */
    public function isAvailableQuantityValid(int $requestedAvailable): bool
    {
        return $requestedAvailable <= $this->getMaxAvailableQuantity();
    }

    /**
     * Check if equipment needs maintenance.
     */
    public function needsMaintenance(): bool
    {
        if (!$this->next_maintenance_date) {
            return false;
        }

        return $this->next_maintenance_date <= now()->addDays(30);
    }

    /**
     * Get the status badge color.
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'active' => 'green',
            'maintenance' => 'yellow',
            'retired' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get the condition badge color.
     */
    public function getConditionColorAttribute()
    {
        return match($this->condition_status) {
            'excellent' => 'green',
            'good' => 'blue',
            'fair' => 'yellow',
            'poor' => 'red',
            default => 'gray',
        };
    }

    /**
     * Reserve equipment quantity for borrowing.
     */
    public function reserveQuantity(int $quantity): bool
    {
        if (!$this->isAvailable($quantity)) {
            return false;
        }

        $this->decrement('available_quantity', $quantity);
        return true;
    }

    /**
     * Release reserved equipment quantity.
     */
    public function releaseQuantity(int $quantity): void
    {
        $this->increment('available_quantity', $quantity);
        
        // Ensure available quantity doesn't exceed total quantity
        if ($this->available_quantity > $this->total_quantity) {
            $this->update(['available_quantity' => $this->total_quantity]);
        }
    }

    /**
     * Get currently borrowed quantity.
     */
    public function getBorrowedQuantity(): int
    {
        return $this->activeBorrowRequests()
            ->sum('quantity_approved') ?: 0;
    }

    /**
     * Get availability percentage.
     */
    public function getAvailabilityPercentage(): float
    {
        if ($this->total_quantity <= 0) {
            return 0;
        }

        return round(($this->available_quantity / $this->total_quantity) * 100, 1);
    }

    /**
     * Check if equipment has low availability.
     */
    public function hasLowAvailability(float $threshold = 20.0): bool
    {
        return $this->getAvailabilityPercentage() <= $threshold;
    }

    /**
     * Get available statuses.
     */
    public static function getStatuses()
    {
        return [
            'active' => 'Active',
            'maintenance' => 'Under Maintenance',
            'retired' => 'Retired',
        ];
    }

    /**
     * Get available conditions.
     */
    public static function getConditions()
    {
        return [
            'excellent' => 'Excellent',
            'good' => 'Good',
            'fair' => 'Fair',
            'poor' => 'Poor',
        ];
    }

    /**
     * Configure activity log options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'name',
                'category_id',
                'status',
                'condition_status',
                'total_quantity',
                'available_quantity',
                'location',
                'notes',
                'last_maintenance_date',
                'next_maintenance_date'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'Equipment added',
                'updated' => 'Equipment updated',
                'deleted' => 'Equipment deleted',
                default => "Equipment {$eventName}",
            });
    }
}
