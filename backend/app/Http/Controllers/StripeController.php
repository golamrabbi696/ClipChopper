<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;

class StripeController extends Controller
{
    /**
     * Create a Stripe Checkout session and return the URL.
     * Frontend redirects the user to Stripe's hosted checkout.
     */
    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'plan' => 'required|in:one-time,monthly',
        ]);

        $stripeKey = config('services.stripe.secret');
        if (!$stripeKey) {
            return response()->json(['message' => 'Stripe is not configured yet.'], 503);
        }

        \Stripe\Stripe::setApiKey($stripeKey);

        $priceId = $validated['plan'] === 'monthly'
            ? config('services.stripe.price_monthly')
            : config('services.stripe.price_one_time');

        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items'           => [['price' => $priceId, 'quantity' => 1]],
            'mode'                 => $validated['plan'] === 'monthly' ? 'subscription' : 'payment',
            'success_url'          => config('app.frontend_url') . '/dashboard?checkout=success',
            'cancel_url'           => config('app.frontend_url') . '/contact?checkout=cancelled',
            'customer_email'       => auth()->user()?->email,
        ]);

        return response()->json(['url' => $session->url]);
    }

    /**
     * Handle Stripe webhook events (subscription lifecycle).
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sig     = $request->header('Stripe-Signature');
        $secret  = config('services.stripe.webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sig, $secret);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Webhook error: ' . $e->getMessage()], 400);
        }

        match ($event->type) {
            'customer.subscription.created',
            'customer.subscription.updated' => $this->handleSubscriptionUpsert($event->data->object),
            'customer.subscription.deleted' => $this->handleSubscriptionDeleted($event->data->object),
            default                          => null,
        };

        return response()->json(['status' => 'ok']);
    }

    private function handleSubscriptionUpsert(object $sub): void
    {
        Subscription::updateOrCreate(
            ['stripe_subscription_id' => $sub->id],
            [
                'stripe_customer_id' => $sub->customer,
                'plan'               => 'monthly',
                'status'             => $sub->status,
            ]
        );
    }

    private function handleSubscriptionDeleted(object $sub): void
    {
        Subscription::where('stripe_subscription_id', $sub->id)
            ->update(['status' => 'cancelled', 'cancelled_at' => now()]);
    }
}
