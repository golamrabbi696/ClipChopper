<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookSetting extends Model
{
    protected $fillable = [
        'discord_webhook_url',
        'discord_enabled',
        'telegram_bot_token',
        'telegram_chat_id',
        'telegram_enabled',
    ];

    protected $casts = [
        'discord_enabled' => 'boolean',
        'telegram_enabled' => 'boolean',
    ];

    public static function singleton(): self
    {
        return static::query()->first() ?? static::query()->create([
            'discord_enabled' => false,
            'telegram_enabled' => false,
        ]);
    }
}

