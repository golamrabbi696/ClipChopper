<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $bookings = Booking::with(['lead', 'user'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->orderByDesc('created_at')
            ->paginate(25);

        return response()->json($bookings);
    }

    public function update(Request $request, Booking $booking)
    {
        $booking->update($request->validate([
            'status'           => 'sometimes|string|in:pending,confirmed,completed,cancelled',
            'notes'            => 'sometimes|nullable|string|max:2000',
            'call_date'        => 'sometimes|nullable|date_format:Y-m-d',
            'call_time'        => 'sometimes|nullable|date_format:H:i',
            'call_timezone'    => 'sometimes|nullable|timezone',
            'call_scheduled_at'=> 'sometimes|nullable|date',
        ]));

        if ($booking->status === 'confirmed' && $booking->lead) {
            $booking->lead->update(['status' => 'booked']);
        }

        return response()->json($booking);
    }

    public function store(Request $request)
    {
        $booking = Booking::create($request->validate([
            'lead_id'          => 'nullable|exists:leads,id',
            'user_id'          => 'nullable|exists:users,id',
            'call_date'        => 'nullable|date_format:Y-m-d',
            'call_time'        => 'nullable|date_format:H:i',
            'call_timezone'    => 'nullable|timezone',
            'call_scheduled_at'=> 'nullable|date',
            'plan'             => 'nullable|string|max:50',
            'notes'            => 'nullable|string|max:2000',
            'status'           => 'sometimes|string|in:pending,confirmed,completed,cancelled',
        ]));

        if ($booking->status === 'confirmed' && $booking->lead) {
            $booking->lead->update(['status' => 'booked']);
        }

        return response()->json($booking, 201);
    }
}
