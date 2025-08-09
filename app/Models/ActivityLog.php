<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'activity_logs';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'log_name',
        'description',
        'subject_type',
        'subject_id',
        'causer_type',
        'causer_id',
        'properties',
        'batch_uuid',
        'event',
        'created_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'properties' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Get the subject model (polymorphic relation).
     */
    public function subject()
    {
        return $this->morphTo();
    }

    /**
     * Get the causer model (polymorphic relation).
     */
    public function causer()
    {
        return $this->morphTo();
    }

    /**
     * Scope a query to filter by log name.
     */
    public function scopeInLog($query, $logName)
    {
        return $query->where('log_name', $logName);
    }

    /**
     * Scope a query to filter by causer.
     */
    public function scopeCausedBy($query, $causer)
    {
        return $query->where('causer_type', get_class($causer))
                    ->where('causer_id', $causer->id);
    }

    /**
     * Scope a query to filter by subject.
     */
    public function scopeForSubject($query, $subject)
    {
        return $query->where('subject_type', get_class($subject))
                    ->where('subject_id', $subject->id);
    }

    /**
     * Scope a query to filter by event.
     */
    public function scopeWithEvent($query, $event)
    {
        return $query->where('event', $event);
    }

    /**
     * Scope a query to filter by batch UUID.
     */
    public function scopeInBatch($query, $batchUuid)
    {
        return $query->where('batch_uuid', $batchUuid);
    }

    /**
     * Scope a query to order by latest.
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Get the human readable description.
     */
    public function getDescriptionAttribute($value)
    {
        return $value;
    }

    /**
     * Get a specific property value.
     */
    public function getProperty($key, $default = null)
    {
        return data_get($this->properties, $key, $default);
    }

    /**
     * Check if log has a specific property.
     */
    public function hasProperty($key): bool
    {
        return data_get($this->properties, $key) !== null;
    }

    /**
     * Get the causer's name if available.
     */
    public function getCauserNameAttribute()
    {
        if ($this->causer) {
            return $this->causer->name ?? $this->causer->title ?? 'System';
        }

        return 'System';
    }

    /**
     * Get the subject's name if available.
     */
    public function getSubjectNameAttribute()
    {
        if ($this->subject) {
            return $this->subject->name ?? $this->subject->title ?? $this->subject->id;
        }

        return 'Unknown';
    }

    /**
     * Get the event color for UI display.
     */
    public function getEventColorAttribute()
    {
        return match($this->event) {
            'created' => 'green',
            'updated' => 'blue',
            'deleted' => 'red',
            'restored' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
            'cancelled' => 'gray',
            'completed' => 'purple',
            default => 'blue',
        };
    }

    /**
     * Create a log entry.
     */
    public static function createLog($description, $subject = null, $causer = null, $properties = [], $logName = 'default', $event = null)
    {
        return static::create([
            'log_name' => $logName,
            'description' => $description,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject?->id,
            'causer_type' => $causer ? get_class($causer) : null,
            'causer_id' => $causer?->id,
            'properties' => $properties,
            'event' => $event,
            'created_at' => now(),
        ]);
    }
}
