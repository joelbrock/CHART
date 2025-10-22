<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Journal extends Model
{
    protected $fillable = [
        'client_id', 'staff_id', 'flags', 'hours', 'billable',
        'team_note', 'client_note', 'retreat_note', 'retreat_date1',
        'retreat_date2', 'qtr_inc', 'quarterly', 'intro', 'retain',
        'date', 'category'
    ];

    protected $casts = [
        'billable' => 'boolean',
        'qtr_inc' => 'boolean',
        'retain' => 'boolean',
        'hours' => 'decimal:2',
        'date' => 'date',
        'retreat_date1' => 'date',
        'retreat_date2' => 'date',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }
}
