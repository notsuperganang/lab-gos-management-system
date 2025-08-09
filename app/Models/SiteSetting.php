<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'title',
        'content',
        'type',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'content' => 'string', // Will be cast to JSON when type is 'json'
        ];
    }

    /**
     * Scope a query to only include active settings.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by setting type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get a setting value by key.
     */
    public static function getValue($key, $default = null)
    {
        $setting = static::where('key', $key)->where('is_active', true)->first();

        if (!$setting) {
            return $default;
        }

        return $setting->type === 'json' ? json_decode($setting->content, true) : $setting->content;
    }

    /**
     * Set a setting value by key.
     */
    public static function setValue($key, $value, $type = 'text', $title = null)
    {
        $content = $type === 'json' ? json_encode($value) : $value;

        return static::updateOrCreate(
            ['key' => $key],
            [
                'content' => $content,
                'type' => $type,
                'title' => $title,
                'is_active' => true,
            ]
        );
    }

    /**
     * Get the parsed content based on type.
     */
    public function getParsedContentAttribute()
    {
        if ($this->type === 'json') {
            return json_decode($this->content, true);
        }

        return $this->content;
    }
}
