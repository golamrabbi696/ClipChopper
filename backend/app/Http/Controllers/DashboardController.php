<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function deliverables(Request $request)
    {
        $user  = auth()->user();
        $month = $request->query('month'); // optional filter e.g. "2026-07"

        $query = $user->deliverables()->orderByDesc('created_at');
        if ($month) {
            $query->where('month', $month);
        }

        $items = $query->get(['id', 'type', 'title', 'month', 'file_url', 'status']);

        return response()->json(['data' => $items]);
    }
}
