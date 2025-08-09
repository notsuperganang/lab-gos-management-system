<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BorrowRequestItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'borrow_request_id',
        'equipment_id',
        'quantity_requested',
        'quantity_approved',
        'condition_before',
        'condition_after',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantity_requested' => 'integer',
            'quantity_approved' => 'integer',
        ];
    }

    /**
     * Get the borrow request that owns this item.
     */
    public function borrowRequest()
    {
        return $this->belongsTo(BorrowRequest::class);
    }

    /**
     * Get the equipment for this item.
     */
    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    /**
     * Scope a query to only include approved items.
     */
    public function scopeApproved($query)
    {
        return $query->whereNotNull('quantity_approved')
                    ->where('quantity_approved', '>', 0);
    }

    /**
     * Scope a query to filter by equipment.
     */
    public function scopeForEquipment($query, $equipmentId)
    {
        return $query->where('equipment_id', $equipmentId);
    }

    /**
     * Get available condition statuses.
     */
    public static function getConditionStatuses()
    {
        return [
            'excellent' => 'Excellent',
            'good' => 'Good',
            'fair' => 'Fair',
            'poor' => 'Poor',
        ];
    }

    /**
     * Get the condition before label.
     */
    public function getConditionBeforeLabelAttribute()
    {
        $conditions = self::getConditionStatuses();
        return $conditions[$this->condition_before] ?? $this->condition_before;
    }

    /**
     * Get the condition after label.
     */
    public function getConditionAfterLabelAttribute()
    {
        $conditions = self::getConditionStatuses();
        return $conditions[$this->condition_after] ?? $this->condition_after;
    }

    /**
     * Get the condition before badge color.
     */
    public function getConditionBeforeColorAttribute()
    {
        return match($this->condition_before) {
            'excellent' => 'green',
            'good' => 'blue',
            'fair' => 'yellow',
            'poor' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get the condition after badge color.
     */
    public function getConditionAfterColorAttribute()
    {
        return match($this->condition_after) {
            'excellent' => 'green',
            'good' => 'blue',
            'fair' => 'yellow',
            'poor' => 'red',
            default => 'gray',
        };
    }

    /**
     * Check if the quantity is fully approved.
     */
    public function isFullyApproved(): bool
    {
        return $this->quantity_approved && $this->quantity_approved >= $this->quantity_requested;
    }

    /**
     * Check if the quantity is partially approved.
     */
    public function isPartiallyApproved(): bool
    {
        return $this->quantity_approved && $this->quantity_approved < $this->quantity_requested;
    }

    /**
     * Check if condition degraded after use.
     */
    public function hasConditionDegraded(): bool
    {
        if (!$this->condition_before || !$this->condition_after) {
            return false;
        }

        $conditionLevels = [
            'poor' => 1,
            'fair' => 2,
            'good' => 3,
            'excellent' => 4,
        ];

        $beforeLevel = $conditionLevels[$this->condition_before] ?? 0;
        $afterLevel = $conditionLevels[$this->condition_after] ?? 0;

        return $afterLevel < $beforeLevel;
    }

    /**
     * Get the approval percentage.
     */
    public function getApprovalPercentageAttribute()
    {
        if (!$this->quantity_approved || $this->quantity_requested == 0) {
            return 0;
        }

        return round(($this->quantity_approved / $this->quantity_requested) * 100, 2);
    }

    /**
     * Get the remaining quantity not approved.
     */
    public function getRemainingQuantityAttribute()
    {
        if (!$this->quantity_approved) {
            return $this->quantity_requested;
        }

        return max(0, $this->quantity_requested - $this->quantity_approved);
    }
}
