<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class TestingRequest extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'request_id',
        'status',
        'client_name',
        'client_organization',
        'client_email',
        'client_phone',
        'client_address',
        'sample_name',
        'sample_description',
        'sample_quantity',
        'testing_type',
        'testing_parameters',
        'urgent_request',
        'sample_delivery_schedule',
        'estimated_duration',
        'completion_date',
        'result_files_path',
        'result_summary',
        'cost',
        'submitted_at',
        'reviewed_at',
        'reviewed_by',
        'assigned_to',
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
            'testing_parameters' => 'array',
            'urgent_request' => 'boolean',
            'sample_delivery_schedule' => 'date',
            'estimated_duration' => 'integer',
            'completion_date' => 'date',
            'result_files_path' => 'array',
            'cost' => 'decimal:2',
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
     * Get the user assigned to this request.
     */
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
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
     * Scope a query to only include in progress requests.
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope a query to only include urgent requests.
     */
    public function scopeUrgent($query)
    {
        return $query->where('urgent_request', true);
    }

    /**
     * Scope a query to filter by testing type.
     */
    public function scopeByTestingType($query, $type)
    {
        return $query->where('testing_type', $type);
    }

    /**
     * Scope a query to filter by assigned user.
     */
    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
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
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];
    }

    /**
     * Get available testing types.
     */
    public static function getTestingTypes()
    {
        return [
            'uv_vis_spectroscopy' => 'UV-Vis Spectroscopy',
            'ftir_spectroscopy' => 'FTIR Spectroscopy',
            'optical_microscopy' => 'Optical Microscopy',
            'custom' => 'Custom Testing',
        ];
    }

    /**
     * Get testing type configuration (duration and cost).
     */
    public static function getTestingTypeConfig()
    {
        return [
            'uv_vis_spectroscopy' => [
                'duration_days' => 3,
                'cost' => 150000.00,
            ],
            'ftir_spectroscopy' => [
                'duration_days' => 5,
                'cost' => 200000.00,
            ],
            'optical_microscopy' => [
                'duration_days' => 2,
                'cost' => 100000.00,
            ],
            'custom' => [
                'duration_days' => 7,
                'cost' => 300000.00,
            ],
        ];
    }

    /**
     * Get estimated completion date (sample delivery + duration).
     */
    public function getEstimatedCompletionDateAttribute()
    {
        if (!$this->sample_delivery_schedule || !$this->estimated_duration) {
            return null;
        }

        return $this->sample_delivery_schedule->addDays($this->estimated_duration);
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
            'in_progress' => 'purple',
            'completed' => 'green',
            'cancelled' => 'gray',
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
     * Get the testing type label.
     */
    public function getTestingTypeLabelAttribute()
    {
        $types = self::getTestingTypes();
        return $types[$this->testing_type] ?? $this->testing_type;
    }

    /**
     * Get the actual duration in hours.
     */
    public function getActualDurationHoursAttribute()
    {
        if ($this->actual_start_date && $this->actual_completion_date) {
            return $this->actual_start_date->diffInHours($this->actual_completion_date);
        }

        return null;
    }

    /**
     * Get the progress percentage.
     */
    public function getProgressPercentageAttribute()
    {
        return match($this->status) {
            'pending' => 0,
            'approved' => 25,
            'in_progress' => 50,
            'completed' => 100,
            'rejected', 'cancelled' => 0,
            default => 0,
        };
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
     * Check if request can be started.
     */
    public function canBeStarted(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if request can be completed.
     */
    public function canBeCompleted(): bool
    {
        return $this->status === 'in_progress';
    }

    /**
     * Check if request can be cancelled.
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'approved', 'in_progress']);
    }

    /**
     * Validate status transition.
     */
    public function canTransitionTo(string $newStatus): bool
    {
        $validTransitions = [
            'pending' => ['approved', 'rejected', 'cancelled'],
            'approved' => ['in_progress', 'cancelled'],
            'in_progress' => ['completed', 'cancelled'],
            'rejected' => [], // Final state
            'completed' => [], // Final state
            'cancelled' => [], // Final state
        ];

        return in_array($newStatus, $validTransitions[$this->status] ?? []);
    }

    /**
     * Get valid next statuses.
     */
    public function getValidNextStatuses(): array
    {
        $validTransitions = [
            'pending' => [
                'approved' => 'Approve & Quote',
                'rejected' => 'Reject',
                'cancelled' => 'Cancel'
            ],
            'approved' => [
                'in_progress' => 'Start Testing',
                'cancelled' => 'Cancel'
            ],
            'in_progress' => [
                'completed' => 'Mark as Completed',
                'cancelled' => 'Cancel'
            ],
            'rejected' => [],
            'completed' => [],
            'cancelled' => [],
        ];

        return $validTransitions[$this->status] ?? [];
    }

    /**
     * Check if request is overdue.
     */
    public function isOverdue(): bool
    {
        if (!$this->sample_delivery_schedule || in_array($this->status, ['completed', 'cancelled', 'rejected'])) {
            return false;
        }

        return $this->sample_delivery_schedule < now()->toDateString();
    }

    /**
     * Generate unique request ID.
     */
    public static function generateRequestId()
    {
        $prefix = 'TR';
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

    /**
     * Configure activity log options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'status', 
                'client_name',
                'client_organization',
                'client_email',
                'client_phone',
                'sample_name',
                'testing_type',
                'urgent_request',
                'sample_delivery_schedule',
                'reviewed_by',
                'approval_notes',
                'total_cost',
                'scheduled_date'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'Testing request submitted',
                'updated' => 'Testing request updated',
                'deleted' => 'Testing request deleted',
                default => "Testing request {$eventName}",
            });
    }
}
