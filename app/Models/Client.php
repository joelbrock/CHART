<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Client extends Model
{
    protected $fillable = [
        'code', 'name', 'active', 'program', 'total_hours', 'q_hours',
        'address', 'city', 'state', 'zip', 'url', 'contact_details',
        'BalancedHrsUse', 'UsingPG', 'CBLDSince', 'ExpireDate',
        'Expansion', 'NewGM', 'Retain', 'RetreatDate', 'RetreatDesc',
        'gm_name', 'gm_contact', 'gm_email', 'chair_name', 'chair_contact',
        'chair_email', 'board_name', 'board_contact', 'board_email'
    ];

    protected $casts = [
        'active' => 'boolean',
        'BalancedHrsUse' => 'integer',
        'UsingPG' => 'integer',
        'Expansion' => 'integer',
        'NewGM' => 'integer',
        'CBLDSince' => 'date',
        'ExpireDate' => 'date',
        'RetreatDate' => 'date',
        'total_hours' => 'decimal:2',
        'q_hours' => 'decimal:1',
    ];

    public function journal(): HasMany
    {
        return $this->hasMany(Journal::class);
    }

    public function staff(): BelongsToMany
    {
        return $this->belongsToMany(Staff::class, 'staff_clients');
    }

    public function attendance(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
