<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\WebhookSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookNotifier
{
    public function notifyBookingCreated(Booking $booking): void
    {
        $settings = WebhookSetting::singleton();

        $lead = $booking->lead;
        $who = $lead
            ? trim($lead->name . ' (' . $lead->email . ')')
            : ('Lead #' . ($booking->lead_id ?? 'unknown'));

        $when = $booking->call_date || $booking->call_time
            ? trim(($booking->call_date?->format('Y-m-d') ?? '') . ' ' . ($booking->call_time ?? ''))
            : ($booking->call_scheduled_at ? $booking->call_scheduled_at->toDateTimeString() : 'not provided');

        if (!empty($booking->call_timezone)) {
            $when .= " ({$booking->call_timezone})";
        }

        $text = "New booking request\n"
            . "Who: {$who}\n"
            . "When: {$when}\n"
            . "Plan: " . ($booking->plan ?: '—') . "\n"
            . "Booking ID: {$booking->id}";

        if ($settings->discord_enabled && $settings->discord_webhook_url) {
            try {
                Http::timeout(5)->post($settings->discord_webhook_url, [
                    'content' => $text,
                ]);
            } catch (\Throwable $e) {
                Log::warning('Discord webhook failed', ['error' => $e->getMessage()]);
            }
        }

        if ($settings->telegram_enabled && $settings->telegram_bot_token && $settings->telegram_chat_id) {
            try {
                $url = 'https://api.telegram.org/bot' . $settings->telegram_bot_token . '/sendMessage';
                Http::timeout(5)->post($url, [
                    'chat_id' => $settings->telegram_chat_id,
                    'text' => $text,
                ]);
            } catch (\Throwable $e) {
                Log::warning('Telegram webhook failed', ['error' => $e->getMessage()]);
            }
        }
    }
}
