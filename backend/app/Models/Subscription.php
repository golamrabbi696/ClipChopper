<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'user_id', 'stripe_subscription_id', 'stripe_customer_id', 'plan', 'status', 'started_at', 'cancelled_at',
    ];

    protected $casts = [
        'started_at'   => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function user() { return $this->belongsTo(User::class); }
}
