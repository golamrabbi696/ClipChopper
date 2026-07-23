<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{
    public function index(Request $request)
    {
        $subscribers = NewsletterSubscriber::query()
            ->when($request->active !== null, fn($q) => $q->whereNull('unsubscribed_at'))
            ->orderByDesc('subscribed_at')
            ->paginate(50);

        return response()->json($subscribers);
    }
}
