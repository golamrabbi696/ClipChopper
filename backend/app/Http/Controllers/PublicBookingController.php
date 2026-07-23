<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Lead;
use App\Models\NewsletterSubscriber;
use App\Services\WebhookNotifier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicBookingController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|max:255',
            'company'    => 'nullable|string|max:255',
            'plan'       => 'nullable|string|max:50',
            'message'    => 'nullable|string|max:5000',
            'newsletter' => 'nullable',

            'call_date'  => 'required|date_format:Y-m-d',
            'call_time'  => 'required|date_format:H:i',
            'timezone'   => 'nullable|timezone',
        ]);

        $timezone = $validated['timezone'] ?? config('app.timezone');
        $callScheduledAt = Carbon::createFromFormat(
            'Y-m-d H:i',
            $validated['call_date'] . ' ' . $validated['call_time'],
            $timezone
        );

        $lead = Lead::updateOrCreate(
            ['email' => $validated['email']],
            [
                'name'              => $validated['name'],
                'company'           => $validated['company'] ?? null,
                'plan'              => $validated['plan'] ?? null,
                'message'           => $validated['message'] ?? null,
                'newsletter_opt_in' => !empty($validated['newsletter']),
                'source'            => 'website',
            ]
        );

        if ($lead->newsletter_opt_in) {
            NewsletterSubscriber::firstOrCreate(
                ['email' => $lead->email],
                [
                    'name'              => $lead->name,
                    'unsubscribe_token' => Str::random(32),
                    'subscribed_at'     => now(),
                ]
            );
        }

        $booking = Booking::create([
            'lead_id'           => $lead->id,
            'call_date'         => $validated['call_date'],
            'call_time'         => $validated['call_time'],
            'call_timezone'     => $timezone,
            'call_scheduled_at' => $callScheduledAt,
            'status'            => 'pending',
            'plan'              => $validated['plan'] ?? null,
        ]);

        try {
            app(WebhookNotifier::class)->notifyBookingCreated($booking->load('lead'));
        } catch (\Throwable $e) {
            // Never fail the booking flow because of webhooks.
        }

        return response()->json([
            'message'    => 'Booking request received. We\'ll confirm your call shortly.',
            'lead_id'    => $lead->id,
            'booking_id' => $booking->id,
        ], 201);
    }
}
