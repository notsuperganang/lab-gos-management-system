<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BorrowRequest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'request_id',
        'status',
        'members',
        'supervisor_name',
        'supervisor_nip',
        'supervisor_email',
        'supervisor_phone',
        'purpose',
        'borrow_date',
        'return_date',
        'start_time',
        'end_time',
        'submitted_at',
        'reviewed_at',
        'reviewed_by',
        'approval_notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'members' => 'array',
            'borrow_date' => 'date',
            'return_date' => 'date',
            'start_time' => 'datetime:H:i',
            'end_time' => 'datetime:H:i',
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
        ];
    }

    /**
     * Get the user who reviewed this request.
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get the borrow request items for this request.
     */
    public function borrowRequestItems()
    {
        return $this->hasMany(BorrowRequestItem::class);
    }

    /**
     * Get the equipment through borrow request items.
     */
    public function equipment()
    {
        return $this->belongsToMany(Equipment::class, 'borrow_request_items')
                    ->withPivot(['quantity_requested', 'quantity_approved', 'condition_before', 'condition_after', 'notes'])
                    ->withTimestamps();
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include pending requests.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include approved requests.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include active requests.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('borrow_date', [$startDate, $endDate]);
    }

    /**
     * Get available statuses.
     */
    public static function getStatuses()
    {
        return [
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'active' => 'Active',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];
    }

    /**
     * Get the status badge color.
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'yellow',
            'approved' => 'blue',
            'rejected' => 'red',
            'active' => 'green',
            'completed' => 'gray',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get the status label.
     */
    public function getStatusLabelAttribute()
    {
        $statuses = self::getStatuses();
        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Get the duration in days.
     */
    public function getDurationAttribute()
    {
        return $this->borrow_date->diffInDays($this->return_date) + 1;
    }

    /**
     * Get the total requested quantity.
     */
    public function getTotalRequestedQuantityAttribute()
    {
        return $this->borrowRequestItems->sum('quantity_requested');
    }

    /**
     * Get the total approved quantity.
     */
    public function getTotalApprovedQuantityAttribute()
    {
        return $this->borrowRequestItems->sum('quantity_approved');
    }

    /**
     * Check if request can be approved.
     */
    public function canBeApproved(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if request can be rejected.
     */
    public function canBeRejected(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if request can be activated.
     */
    public function canBeActivated(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if request can be completed.
     */
    public function canBeCompleted(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if request can be cancelled.
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'approved']);
    }

    /**
     * Generate unique request ID.
     */
    public static function generateRequestId()
    {
        $prefix = 'BR';
        $date = now()->format('Ymd');
        $sequence = str_pad(self::whereDate('created_at', today())->count() + 1, 3, '0', STR_PAD_LEFT);

        return $prefix . $date . $sequence;
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($request) {
            if (empty($request->request_id)) {
                $request->request_id = self::generateRequestId();
            }
        });
    }
}
