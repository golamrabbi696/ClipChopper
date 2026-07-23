<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|max:255',
            'company'        => 'nullable|string|max:255',
            'plan'           => 'nullable|string|max:50',
            'message'        => 'nullable|string|max:5000',
            'newsletter'     => 'nullable',
        ]);

        $lead = Lead::create([
            'name'             => $validated['name'],
            'email'            => $validated['email'],
            'company'          => $validated['company'] ?? null,
            'plan'             => $validated['plan'] ?? null,
            'message'          => $validated['message'] ?? null,
            'newsletter_opt_in'=> !empty($validated['newsletter']),
            'source'           => 'website',
        ]);

        // Auto-subscribe to newsletter if opted in
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

        return response()->json([
            'message' => 'Message received. We\'ll be in touch within 2 business hours.',
            'lead_id' => $lead->id,
        ], 201);
    }
}
