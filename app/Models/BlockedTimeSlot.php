<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Carbon\Carbon;

class BlockedTimeSlot extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'date',
        'start_time',
        'end_time',
        'reason',
        'created_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'start_time' => 'string',
            'end_time' => 'string',
        ];
    }

    /**
     * Get the admin user who blocked this slot.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope a query to only include slots for a specific date.
     */
    public function scopeForDate($query, string $date)
    {
        return $query->where('date', $date);
    }

    /**
     * Scope a query to include slots for a date range.
     */
    public function scopeForDateRange($query, string $startDate, string $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Scope a query to include slots for a specific month.
     */
    public function scopeForMonth($query, int $year, int $month)
    {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        
        return $query->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
    }

    /**
     * Scope a query to include future blocked slots only.
     */
    public function scopeFuture($query)
    {
        return $query->where('date', '>=', now()->toDateString());
    }

    /**
     * Check if this blocked slot overlaps with a given time range.
     */
    public function isOverlappingWith(string $startTime, string $endTime): bool
    {
        $blockedStart = $this->timeToMinutes($this->start_time);
        $blockedEnd = $this->timeToMinutes($this->end_time);
        $checkStart = $this->timeToMinutes($startTime);
        $checkEnd = $this->timeToMinutes($endTime);

        // Two time slots overlap if:
        // - Check slot starts before blocked slot ends AND
        // - Check slot ends after blocked slot starts
        return ($checkStart < $blockedEnd) && ($checkEnd > $blockedStart);
    }

    /**
     * Get the duration in hours.
     */
    public function getDurationAttribute(): float
    {
        $startMinutes = $this->timeToMinutes($this->start_time);
        $endMinutes = $this->timeToMinutes($this->end_time);
        
        return ($endMinutes - $startMinutes) / 60;
    }

    /**
     * Get a formatted time range display.
     */
    public function getTimeRangeAttribute(): string
    {
        return $this->start_time . ' - ' . $this->end_time . ' WIB';
    }

    /**
     * Get formatted date display.
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->date->format('d M Y');
    }

    /**
     * Check if this blocked slot is in the past.
     */
    public function isPast(): bool
    {
        return $this->date->lt(now()->toDateString()) || 
               ($this->date->isToday() && $this->timeToMinutes($this->end_time) < $this->timeToMinutes(now()->format('H:i')));
    }

    /**
     * Check if this blocked slot can be removed.
     */
    public function canBeRemoved(): bool
    {
        // Can remove if not in the past
        return !$this->isPast();
    }

    /**
     * Convert time string (HH:MM) to minutes since midnight.
     */
    protected function timeToMinutes(string $time): int
    {
        $parts = explode(':', $time);
        $hours = (int) $parts[0];
        $minutes = isset($parts[1]) ? (int) $parts[1] : 0;
        
        return ($hours * 60) + $minutes;
    }

    /**
     * Validate that the time slot is within operating hours.
     */
    public function isWithinOperatingHours(): bool
    {
        $startMinutes = $this->timeToMinutes($this->start_time);
        $endMinutes = $this->timeToMinutes($this->end_time);
        
        // Operating hours: 08:00-16:00 (480-960 minutes), excluding lunch 12:00-13:00 (720-780 minutes)
        $operatingStart = 8 * 60; // 08:00 = 480 minutes
        $operatingEnd = 16 * 60;  // 16:00 = 960 minutes
        $lunchStart = 12 * 60;    // 12:00 = 720 minutes
        $lunchEnd = 13 * 60;      // 13:00 = 780 minutes

        // Check if within operating hours
        if ($startMinutes < $operatingStart || $endMinutes > $operatingEnd) {
            return false;
        }

        // Check if overlaps with lunch break
        if ($startMinutes < $lunchEnd && $endMinutes > $lunchStart) {
            return false;
        }

        return true;
    }

    /**
     * Get blocked slots that would conflict with existing visit requests.
     */
    public static function getConflictingWithVisitRequests(string $date, string $startTime, string $endTime): array
    {
        return VisitRequest::where('visit_date', $date)
            ->whereIn('status', ['approved', 'ready', 'active'])
            ->whereNotNull('start_time')
            ->whereNotNull('end_time')
            ->get()
            ->filter(function ($visitRequest) use ($startTime, $endTime) {
                $visitStart = (new static)->timeToMinutes($visitRequest->start_time);
                $visitEnd = (new static)->timeToMinutes($visitRequest->end_time);
                $blockStart = (new static)->timeToMinutes($startTime);
                $blockEnd = (new static)->timeToMinutes($endTime);
                
                // Check if visit request overlaps with the blocked slot
                return ($blockStart < $visitEnd) && ($blockEnd > $visitStart);
            })
            ->values()
            ->toArray();
    }

    /**
     * Configure activity log options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'date',
                'start_time',
                'end_time',
                'reason',
                'created_by'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'Time slot blocked by admin',
                'updated' => 'Blocked time slot updated',
                'deleted' => 'Time slot unblocked by admin',
                default => "Blocked time slot {$eventName}",
            });
    }
}