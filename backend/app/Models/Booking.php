<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'lead_id',
        'user_id',
        'call_date',
        'call_time',
        'call_timezone',
        'call_scheduled_at',
        'status',
        'notes',
        'plan',
    ];

    protected $casts = [
        'call_date' => 'date',
        'call_scheduled_at' => 'datetime',
    ];

    public function lead()    { return $this->belongsTo(Lead::class); }
    public function user()    { return $this->belongsTo(User::class); }
}
