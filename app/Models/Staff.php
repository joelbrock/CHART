<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Staff extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'firstname', 'lastname', 'email', 'password', 'active', 'admin'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'active' => 'boolean',
        'admin' => 'boolean',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the user's full name
     */
    public function getNameAttribute(): string
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->admin;
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    public function journal(): HasMany
    {
        return $this->hasMany(Journal::class);
    }

    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(Client::class, 'staff_clients');
    }
}
