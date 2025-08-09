<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'color_code',
        'icon_class',
        'sort_order',
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
            'sort_order' => 'integer',
        ];
    }

    /**
     * Get the equipment for this category.
     */
    public function equipment()
    {
        return $this->hasMany(Equipment::class);
    }

    /**
     * Get the active equipment for this category.
     */
    public function activeEquipment()
    {
        return $this->hasMany(Equipment::class)->where('status', 'active');
    }

    /**
     * Scope a query to only include active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get the equipment count for this category.
     */
    public function getEquipmentCountAttribute()
    {
        return $this->equipment()->count();
    }

    /**
     * Get the active equipment count for this category.
     */
    public function getActiveEquipmentCountAttribute()
    {
        return $this->activeEquipment()->count();
    }
}
