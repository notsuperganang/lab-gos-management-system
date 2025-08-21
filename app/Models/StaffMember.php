<?php

namespace App\Models;

use App\Enums\StaffType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffMember extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'position',
        'staff_type',
        'specialization',
        'education',
        'email',
        'phone',
        'photo_path',
        'bio',
        'research_interests',
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
            'staff_type' => StaffType::class,
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Scope a query to only include active staff members.
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
     * Scope a query to filter by position.
     */
    public function scopeByPosition($query, $position)
    {
        return $query->where('position', 'like', "%{$position}%");
    }

    /**
     * Scope a query to filter by staff type.
     */
    public function scopeType($query, StaffType $type)
    {
        return $query->where('staff_type', $type);
    }

    /**
     * Get the staff member's full contact information.
     */
    public function getFullContactAttribute()
    {
        $contact = [];

        if ($this->email) {
            $contact[] = $this->email;
        }

        if ($this->phone) {
            $contact[] = $this->phone;
        }

        return implode(' | ', $contact);
    }

    /**
     * Get the staff member's photo URL.
     */
    public function getPhotoUrlAttribute()
    {
        // Treat 'bodong' (invalid placeholder from seed) or empty as no photo
        $path = $this->photo_path;
        if ($path && strtolower($path) !== 'bodong') {
            // Only return storage asset if file actually exists to avoid 403
            $full = storage_path('app/public/' . $path);
            if (is_file($full)) {
                return asset('storage/' . $path);
            }
        }
        // Fallback placeholder requested
        return asset('assets/images/placeholder.svg');
    }

    // NOTE: If you see 403 responses for storage/staff/*.jpg ensure:
    // 1. File actually exists under storage/app/public/staff
    // 2. Symlink public/storage -> storage/app/public is present (php artisan storage:link)
    // 3. Web server / filesystem permissions allow read (chmod o+r on files)
    // Laravel's local file serving aborts(403) when the path resolves outside the configured directory or missing.

    /**
     * Check if staff member has research interests.
     */
    public function hasResearchInterests(): bool
    {
        return !empty($this->research_interests);
    }

    /**
     * Determine if the staff member has a valid photo file (not 'bodong' and exists).
     */
    public function hasValidPhoto(): bool
    {
        $path = $this->photo_path;
        if (!$path || strtolower($path) === 'bodong') {
            return false;
        }
        return is_file(storage_path('app/public/' . $path));
    }

    /**
     * Get the staff type label.
     */
    public function getStaffTypeLabelAttribute(): string
    {
        return $this->staff_type?->label() ?? 'Unknown';
    }
}
