<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'position',
        'avatar_path',
        'is_active',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => 'string',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Get the articles published by this user.
     */
    public function publishedArticles()
    {
        return $this->hasMany(Article::class, 'published_by');
    }

    /**
     * Get the borrow requests reviewed by this user.
     */
    public function reviewedBorrowRequests()
    {
        return $this->hasMany(BorrowRequest::class, 'reviewed_by');
    }

    /**
     * Get the visit requests reviewed by this user.
     */
    public function reviewedVisitRequests()
    {
        return $this->hasMany(VisitRequest::class, 'reviewed_by');
    }

    /**
     * Get the testing requests reviewed by this user.
     */
    public function reviewedTestingRequests()
    {
        return $this->hasMany(TestingRequest::class, 'reviewed_by');
    }

    /**
     * Get the testing requests assigned to this user.
     */
    public function assignedTestingRequests()
    {
        return $this->hasMany(TestingRequest::class, 'assigned_to');
    }

    /**
     * Scope a query to only include active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include admin users.
     */
    public function scopeAdmins($query)
    {
        return $query->whereIn('role', ['admin', 'super_admin']);
    }

    /**
     * Check if user is super admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'super_admin']);
    }
}
