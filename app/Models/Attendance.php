<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'year', 'event', 'event_id', 'att', 'coop', 'client_id',
        'firstname', 'lastname', 'title', 'qtr'
    ];

    protected $casts = [
        'year' => 'integer',
        'event_id' => 'integer',
        'qtr' => 'integer',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
