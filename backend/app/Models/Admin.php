<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $table = 'admin';

    protected $fillable = [
        'user_id',
        'is_superadmin',
    ];

    protected $casts = [
        'is_superadmin' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

