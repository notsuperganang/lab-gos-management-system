<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitRequest extends Model
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
        'full_name',
        'email',
        'phone',
        'institution',
        'purpose',
        'visit_date',
        'visit_time',
        'participants',
        'additional_notes',
        'request_letter_path',
        'approval_letter_path',
        'submitted_at',
        'reviewed_at',
        'reviewed_by',
        'approval_notes',
        'agreement_accepted',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'visit_date' => 'date',
            'participants' => 'integer',
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'agreement_accepted' => 'boolean',
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
     * Scope a query to filter by visit date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('visit_date', [$startDate, $endDate]);
    }

    /**
     * Scope a query to filter by purpose.
     */
    public function scopeByPurpose($query, $purpose)
    {
        return $query->where('purpose', $purpose);
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
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];
    }

    /**
     * Get available purposes.
     */
    public static function getPurposes()
    {
        return [
            'study-visit' => 'Study Visit',
            'research' => 'Research',
            'learning' => 'Learning',
            'internship' => 'Internship',
            'others' => 'Others',
        ];
    }

    /**
     * Get available visit times.
     */
    public static function getVisitTimes()
    {
        return [
            'morning' => 'Morning (08:00 - 12:00)',
            'afternoon' => 'Afternoon (13:00 - 17:00)',
        ];
    }

    /**
     * Get the status badge color.
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
            'completed' => 'blue',
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
     * Get the purpose label.
     */
    public function getPurposeLabelAttribute()
    {
        $purposes = self::getPurposes();
        return $purposes[$this->purpose] ?? $this->purpose;
    }

    /**
     * Get the visit time label.
     */
    public function getVisitTimeLabelAttribute()
    {
        $visitTimes = self::getVisitTimes();
        return $visitTimes[$this->visit_time] ?? $this->visit_time;
    }

    /**
     * Get the request letter URL.
     */
    public function getRequestLetterUrlAttribute()
    {
        if ($this->request_letter_path) {
            return asset('storage/' . $this->request_letter_path);
        }

        return null;
    }

    /**
     * Get the approval letter URL.
     */
    public function getApprovalLetterUrlAttribute()
    {
        if ($this->approval_letter_path) {
            return asset('storage/' . $this->approval_letter_path);
        }

        return null;
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
     * Check if request can be completed.
     */
    public function canBeCompleted(): bool
    {
        return $this->status === 'approved' && $this->visit_date <= now()->toDateString();
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
        $prefix = 'VR';
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
