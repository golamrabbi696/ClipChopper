<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'name'  => 'nullable|string|max:255',
        ]);

        $subscriber = NewsletterSubscriber::firstOrCreate(
            ['email' => $validated['email']],
            [
                'name'              => $validated['name'] ?? null,
                'unsubscribe_token' => Str::random(32),
                'subscribed_at'     => now(),
                'unsubscribed_at'   => null,
            ]
        );

        // Re-subscribe if previously unsubscribed
        if ($subscriber->unsubscribed_at) {
            $subscriber->update(['unsubscribed_at' => null, 'subscribed_at' => now()]);
        }

        return response()->json(['message' => 'Subscribed successfully.']);
    }

    public function unsubscribe(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string',
        ]);

        $subscriber = NewsletterSubscriber::where('unsubscribe_token', $validated['token'])->first();

        if (!$subscriber) {
            return response()->json(['message' => 'Invalid unsubscribe token.'], 404);
        }

        $subscriber->update(['unsubscribed_at' => now()]);

        return response()->json(['message' => 'Unsubscribed successfully.']);
    }
}
