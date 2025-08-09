<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'notifiable_type',
        'notifiable_id',
        'data',
        'read_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'data' => 'array',
            'read_at' => 'datetime',
        ];
    }

    /**
     * Get the notifiable entity that the notification belongs to.
     */
    public function notifiable()
    {
        return $this->morphTo();
    }

    /**
     * Scope a query to only include unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope a query to only include read notifications.
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Scope a query to filter by notification type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to filter by notifiable.
     */
    public function scopeForNotifiable($query, $notifiable)
    {
        return $query->where('notifiable_type', get_class($notifiable))
                    ->where('notifiable_id', $notifiable->id);
    }

    /**
     * Scope a query to order by latest.
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Mark the notification as read.
     */
    public function markAsRead()
    {
        if (is_null($this->read_at)) {
            $this->forceFill(['read_at' => now()])->save();
        }
    }

    /**
     * Mark the notification as unread.
     */
    public function markAsUnread()
    {
        if (!is_null($this->read_at)) {
            $this->forceFill(['read_at' => null])->save();
        }
    }



    /**
     * Get a specific data attribute.
     */
    public function getData($key, $default = null)
    {
        return data_get($this->data, $key, $default);
    }

    /**
     * Check if notification has specific data.
     */
    public function hasData($key): bool
    {
        return data_get($this->data, $key) !== null;
    }

    /**
     * Get the notification title.
     */
    public function getTitleAttribute()
    {
        return $this->getData('title', 'Notification');
    }

    /**
     * Get the notification message.
     */
    public function getMessageAttribute()
    {
        return $this->getData('message', 'You have a new notification.');
    }

    /**
     * Get the notification action URL.
     */
    public function getActionUrlAttribute()
    {
        return $this->getData('action_url');
    }

    /**
     * Get the notification action text.
     */
    public function getActionTextAttribute()
    {
        return $this->getData('action_text', 'View');
    }

    /**
     * Get the notification icon.
     */
    public function getIconAttribute()
    {
        return $this->getData('icon', 'bell');
    }

    /**
     * Get the notification color.
     */
    public function getColorAttribute()
    {
        return $this->getData('color', 'blue');
    }

    /**
     * Get the time ago format.
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Create a notification for a specific notifiable.
     */
    public static function createFor($notifiable, $type, $data = [])
    {
        return static::create([
            'type' => $type,
            'notifiable_type' => get_class($notifiable),
            'notifiable_id' => $notifiable->id,
            'data' => $data,
        ]);
    }

    /**
     * Create notifications for multiple notifiables.
     */
    public static function createForMany($notifiables, $type, $data = []): bool
    {
        $notifications = [];

        foreach ($notifiables as $notifiable) {
            $notifications[] = [
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'type' => $type,
                'notifiable_type' => get_class($notifiable),
                'notifiable_id' => $notifiable->id,
                'data' => json_encode($data),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        return static::insert($notifications);
    }

    /**
     * Mark all notifications as read for a notifiable.
     */
    public static function markAllAsReadFor($notifiable)
    {
        return static::forNotifiable($notifiable)
                    ->unread()
                    ->update(['read_at' => now()]);
    }

    /**
     * Determine if a notification has been read.
     */
    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    /**
     * Determine if a notification has not been read.
     */
    public function isUnread(): bool
    {
        return $this->read_at === null;
    }

    /**
     * Delete old read notifications.
     */
    public static function cleanupOldNotifications($days = 30): bool
    {
        return static::read()
            ->where('created_at', '<', now()->subDays($days))
            ->delete() > 0;
    }
}
