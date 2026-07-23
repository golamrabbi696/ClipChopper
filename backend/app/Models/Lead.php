<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'name', 'email', 'company', 'plan', 'message', 'source', 'newsletter_opt_in', 'status',
    ];

    protected $casts = [
        'newsletter_opt_in' => 'boolean',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
