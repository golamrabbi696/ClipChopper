<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $leads = Lead::query()
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->search, fn($q) => $q->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            }))
            ->orderByDesc('created_at')
            ->paginate(25);

        return response()->json($leads);
    }

    public function show(Lead $lead)
    {
        return response()->json($lead->load('bookings'));
    }

    public function update(Request $request, Lead $lead)
    {
        $lead->update($request->validate([
            'status' => 'sometimes|string|in:new,contacted,booked,lost',
        ]));

        return response()->json($lead);
    }
}
