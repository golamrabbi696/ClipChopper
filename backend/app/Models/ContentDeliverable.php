<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentDeliverable extends Model
{
    protected $fillable = ['user_id', 'month', 'type', 'title', 'file_url', 'status', 'notes'];

    public function user() { return $this->belongsTo(User::class); }
}
