<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebhookSetting;
use Illuminate\Http\Request;

class WebhookSettingController extends Controller
{
    public function show()
    {
        return response()->json(WebhookSetting::singleton());
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'discord_webhook_url' => 'nullable|url|max:2000',
            'discord_enabled'     => 'sometimes|boolean',
            'telegram_bot_token'  => 'nullable|string|max:255',
            'telegram_chat_id'    => 'nullable|string|max:255',
            'telegram_enabled'    => 'sometimes|boolean',
        ]);

        $settings = WebhookSetting::singleton();
        $settings->update($validated);

        return response()->json($settings);
    }
}

