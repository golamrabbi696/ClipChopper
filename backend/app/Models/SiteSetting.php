<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'logo_type',
        'logo_text_mark',
        'logo_text_type',
        'logo_image_path',
    ];

    public static function singleton(): self
    {
        return static::query()->first() ?? static::query()->create([
            'logo_type' => 'text',
            'logo_text_mark' => 'CC',
            'logo_text_type' => 'CLIPCHOPPER',
        ]);
    }
}
