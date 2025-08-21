<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'image_path',
        'alt_text',
        'category',
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
     * Scope a query to only include active gallery items.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include published gallery items.
     */
    public function scopePublished($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at', 'desc');
    }

    /**
     * Scope a query to filter by category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope a query to search by title or description.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /**
     * Get available gallery categories.
     */
    public static function getCategories()
    {
        return [
            'lab_facilities' => 'Fasilitas Lab',
            'equipment' => 'Peralatan',
            'activities' => 'Kegiatan',
            'events' => 'Acara',
        ];
    }

    /**
     * Get the image URL.
     */
    public function getImageUrlAttribute()
    {
        if ($this->image_path) {
            // Check if file exists in storage
            $fullPath = storage_path('app/public/' . $this->image_path);
            if (file_exists($fullPath)) {
                return asset('storage/' . $this->image_path);
            }
        }

        return asset('assets/images/placeholder.svg');
    }

    /**
     * Get the category label.
     */
    public function getCategoryLabelAttribute()
    {
        $categories = self::getCategories();
        return $categories[$this->category] ?? $this->category;
    }

    /**
     * Get the alt text or fallback to title.
     */
    public function getImageAltAttribute()
    {
        return $this->alt_text ?: $this->title;
    }

    /**
     * Get the next available sort order for a category.
     * 
     * @param string $category
     * @param int|null $excludeId Exclude this ID when checking (for updates)
     * @return int
     */
    public static function getNextAvailableSortOrder(string $category, ?int $excludeId = null): int
    {
        $query = static::where('category', $category)
                      ->where('is_active', true);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        $maxSortOrder = $query->max('sort_order');
        
        return ($maxSortOrder ?? 0) + 1;
    }

    /**
     * Check if a sort order is available in a category.
     * 
     * @param string $category
     * @param int $sortOrder
     * @param int|null $excludeId Exclude this ID when checking (for updates)
     * @return bool
     */
    public static function isSortOrderAvailable(string $category, int $sortOrder, ?int $excludeId = null): bool
    {
        $query = static::where('category', $category)
                      ->where('sort_order', $sortOrder)
                      ->where('is_active', true);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return !$query->exists();
    }
}
